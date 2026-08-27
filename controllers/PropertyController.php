<?php
class PropertyController extends Controller {
    private Property $propertyModel;
    private PropertyImage $imageModel;

    public function __construct() {
        $this->propertyModel = new Property();
        $this->imageModel = new PropertyImage();
    }

    public function index(): void {
        $city = $_GET['city'] ?? null;
        $minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float)$_GET['min_price'] : null;
        $maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : null;
        $bedrooms = isset($_GET['bedrooms']) && $_GET['bedrooms'] !== '' ? (int)$_GET['bedrooms'] : null;

        $properties = $this->propertyModel->search($city, $minPrice, $maxPrice, $bedrooms);

        $this->render('public/marketplace', [
            'properties' => $properties,
            'city' => $city,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'bedrooms' => $bedrooms
        ]);
    }

    public function detail(string $id): void {
        $propertyId = (int)$id;
        $property = $this->propertyModel->findById($propertyId);

        if (!$property) {
            http_response_code(404);
            echo "Property not found.";
            return;
        }

        $images = $this->imageModel->findByProperty($propertyId);
        $reviewModel = new Review();
        $reviews = $reviewModel->findByProperty($propertyId);

        $this->render('public/property_detail', [
            'property' => $property,
            'images' => $images,
            'reviews' => $reviews,
            'csrf_token' => Auth::csrfToken()
        ]);
    }

    public function showCreateForm(): void {
        Auth::requireRole('owner', 'broker');
        $this->render('owner/property_form', [
            'csrf_token' => Auth::csrfToken(),
            'property' => null
        ]);
    }

    public function processCreate(): void {
        Auth::requireRole('owner', 'broker');
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            echo "Invalid CSRF token.";
            return;
        }

        $user = Auth::user();
        $ownerId = $user['role'] === 'owner' ? $user['user_id'] : (int)($_POST['owner_id'] ?? 0);

        if ($user['role'] === 'broker' && !$ownerId) {
            http_response_code(400);
            echo "Broker must specify property owner_id.";
            return;
        }

        $data = [
            'owner_id' => $ownerId,
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'address_line' => trim($_POST['address_line'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'price_per_month' => (float)($_POST['price_per_month'] ?? 0),
            'bedrooms' => (int)($_POST['bedrooms'] ?? 0),
            'bathrooms' => (int)($_POST['bathrooms'] ?? 0),
            'area_sqft' => (float)($_POST['area_sqft'] ?? 0),
            'availability_status' => $_POST['availability_status'] ?? 'available'
        ];

        $propertyId = $this->propertyModel->create($data);

        if (!empty($_POST['cover_image_url'])) {
            $this->imageModel->addImage($propertyId, trim($_POST['cover_image_url']), true);
        }

        $this->redirect($user['role'] === 'owner' ? '/owner/dashboard' : '/broker/dashboard');
    }

    public function toggleAvailability(): void {
        Auth::requireRole('owner');
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            echo "Invalid CSRF token.";
            return;
        }

        $propertyId = (int)($_POST['property_id'] ?? 0);
        $status = $_POST['availability_status'] ?? 'available';

        $property = $this->propertyModel->findById($propertyId);
        if ($property && $property['owner_id'] === Auth::user()['user_id']) {
            $this->propertyModel->updateStatus($propertyId, $status);
        }

        $this->redirect('/owner/dashboard');
    }
}
