<?php
class RentalController extends Controller {
    private RentalRequest $requestModel;
    private RentalAgreement $agreementModel;
    private Property $propertyModel;
    private Notification $notificationModel;
    private PropertyVisit $visitModel;

    public function __construct() {
        $this->requestModel = new RentalRequest();
        $this->agreementModel = new RentalAgreement();
        $this->propertyModel = new Property();
        $this->notificationModel = new Notification();
        $this->visitModel = new PropertyVisit();
    }

    public function showApplicationForm(string $propertyId): void {
        Auth::requireRole('tenant');
        $id = (int)$propertyId;
        $property = $this->propertyModel->findById($id);

        if (!$property) {
            http_response_code(404);
            echo "Property not found.";
            return;
        }

        $this->render('tenant/apply', [
            'property' => $property,
            'csrf_token' => Auth::csrfToken()
        ]);
    }

    public function processRequest(): void {
        Auth::requireRole('tenant');
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            echo "Invalid CSRF token.";
            return;
        }

        $propertyId = (int)($_POST['property_id'] ?? 0);
        $moveInDate = $_POST['requested_move_in'] ?? null;
        $message = trim($_POST['message'] ?? '');
        $tenant = Auth::user();

        $requestId = $this->requestModel->create($propertyId, $tenant['user_id'], $moveInDate, $message);
        
        $property = $this->propertyModel->findById($propertyId);
        if ($property) {
            $this->notificationModel->create(
                $property['owner_id'],
                'rental_request',
                "New rental request received for {$property['title']} from {$tenant['name']}.",
                'rental_request',
                $requestId
            );
        }

        $this->redirect('/tenant/dashboard');
    }

    public function requestVisit(): void {
        Auth::requireRole('tenant');
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            echo "Invalid CSRF token.";
            return;
        }

        $propertyId = (int)($_POST['property_id'] ?? 0);
        $scheduledAt = $_POST['scheduled_at'] ?? '';
        $notes = trim($_POST['notes'] ?? '');
        $tenant = Auth::user();

        $property = $this->propertyModel->findById($propertyId);
        if (!$property) {
            http_response_code(404);
            echo "Property not found.";
            return;
        }

        $visitId = $this->visitModel->schedule($propertyId, $tenant['user_id'], null, $scheduledAt, $notes);

        // Notify property owner about new visit request
        $this->notificationModel->create(
            $property['owner_id'],
            'visit_request',
            "New walkthrough visit requested for '{$property['title']}' by {$tenant['name']}.",
            'visit',
            $visitId
        );

        $_SESSION['success'] = "Walkthrough visit requested successfully for '{$property['title']}'.";
        $this->redirect('/tenant/dashboard');
    }

    public function assignVisitBroker(): void {
        Auth::requireRole('owner');
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            echo "Invalid CSRF token.";
            return;
        }

        $visitId = (int)($_POST['visit_id'] ?? 0);
        $brokerId = (int)($_POST['broker_id'] ?? 0);
        $owner = Auth::user();

        $visit = $this->visitModel->findById($visitId);
        if (!$visit || $visit['owner_id'] !== $owner['user_id']) {
            $_SESSION['error'] = "Unauthorized or visit request not found.";
            $this->redirect('/owner/dashboard');
            return;
        }

        $this->visitModel->assignBroker($visitId, $brokerId);

        // Notify Broker
        $userModel = new User();
        $broker = $userModel->findById($brokerId);
        $this->notificationModel->create(
            $brokerId,
            'visit_assignment',
            "You were assigned to handle a property visit for '{$visit['property_title']}'.",
            'visit',
            $visitId
        );

        // Notify Tenant
        $this->notificationModel->create(
            $visit['tenant_id'],
            'visit_broker_assigned',
            "Broker " . ($broker ? $broker['name'] : '') . " was assigned to your visit for '{$visit['property_title']}'.",
            'visit',
            $visitId
        );

        $_SESSION['success'] = "Broker assigned to visit #{$visitId} successfully.";
        $this->redirect('/owner/dashboard');
    }

    public function tenantDashboard(): void {
        Auth::requireRole('tenant');
        $tenant = Auth::user();

        $requests = $this->requestModel->findByTenant($tenant['user_id']);
        $agreements = $this->agreementModel->findByTenant($tenant['user_id']);
        $visits = $this->visitModel->findByTenant($tenant['user_id']);
        $notifications = $this->notificationModel->findByUser($tenant['user_id']);

        $this->render('tenant/dashboard', [
            'user' => $tenant,
            'requests' => $requests,
            'agreements' => $agreements,
            'visits' => $visits,
            'notifications' => $notifications,
            'csrf_token' => Auth::csrfToken()
        ]);
    }

    public function ownerDashboard(): void {
        Auth::requireRole('owner');
        $owner = Auth::user();

        $properties = $this->propertyModel->findByOwner($owner['user_id']);
        $requests = $this->requestModel->findByOwner($owner['user_id']);
        $agreements = $this->agreementModel->findByOwner($owner['user_id']);
        $visits = $this->visitModel->findByOwner($owner['user_id']);
        $reviewModel = new Review();
        $reviews = $reviewModel->findByOwner($owner['user_id']);
        $notifications = $this->notificationModel->findByUser($owner['user_id']);

        $userModel = new User();
        $brokers = $userModel->getByRole('broker');
        $assignmentModel = new BrokerAssignment();

        foreach ($properties as &$p) {
            $p['active_broker'] = $assignmentModel->findActiveByProperty($p['property_id']);
        }

        $this->render('owner/dashboard', [
            'user' => $owner,
            'properties' => $properties,
            'requests' => $requests,
            'agreements' => $agreements,
            'visits' => $visits,
            'reviews' => $reviews,
            'notifications' => $notifications,
            'brokers' => $brokers,
            'csrf_token' => Auth::csrfToken()
        ]);
    }

    public function processDecision(): void {
        Auth::requireRole('owner', 'admin');
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            echo "Invalid CSRF token.";
            return;
        }

        $requestId = (int)($_POST['request_id'] ?? 0);
        $decision = $_POST['decision'] ?? ''; // 'approved' or 'rejected'
        $user = Auth::user();

        $request = $this->requestModel->findById($requestId);
        if (!$request) {
            http_response_code(404);
            echo "Rental request not found.";
            return;
        }

        if ($decision === 'approved') {
            $this->requestModel->updateStatus($requestId, 'approved', $user['user_id']);
            
            // Create rental agreement
            $property = $this->propertyModel->findById($request['property_id']);
            $brokerAssignModel = new BrokerAssignment();
            $activeAssign = $brokerAssignModel->findActiveByProperty($request['property_id']);
            $brokerId = $activeAssign ? $activeAssign['broker_id'] : null;

            $startDate = $request['requested_move_in'] ?: date('Y-m-d');
            $endDate = date('Y-m-d', strtotime('+1 year', strtotime($startDate)));
            $monthlyRent = $property['price_per_month'];

            $agreementId = $this->agreementModel->create($requestId, $brokerId, $startDate, $endDate, $monthlyRent);
            
            // Update property status to rented
            $this->propertyModel->updateStatus($request['property_id'], 'rented');

            // If broker assigned, calculate commission (50% of 1st month rent)
            if ($brokerId) {
                $commissionModel = new Commission();
                $commissionModel->create($brokerId, $agreementId, $monthlyRent * 0.5);
            }

            $this->notificationModel->create(
                $request['tenant_id'],
                'request_approved',
                "Your rental request for {$property['title']} was approved!",
                'rental_agreement',
                $agreementId
            );
            $_SESSION['success'] = "Rental request approved!";
        } else {
            $this->requestModel->updateStatus($requestId, 'rejected', $user['user_id']);
            $this->notificationModel->create(
                $request['tenant_id'],
                'request_rejected',
                "Your rental request for property ID {$request['property_id']} was rejected.",
                'rental_request',
                $requestId
            );
            $_SESSION['success'] = "Rental request rejected.";
        }

        $this->redirect($user['role'] === 'owner' ? '/owner/dashboard' : '/admin/users');
    }
}
