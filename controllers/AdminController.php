<?php
class AdminController extends Controller {
    private User $userModel;
    private Property $propertyModel;
    private BrokerAssignment $assignmentModel;
    private Complaint $complaintModel;
    private AdminAction $auditModel;

    public function __construct() {
        $this->userModel = new User();
        $this->propertyModel = new Property();
        $this->assignmentModel = new BrokerAssignment();
        $this->complaintModel = new Complaint();
        $this->auditModel = new AdminAction();
    }

    public function users(): void {
        Auth::requireRole('admin');
        $users = $this->userModel->getAll();
        $properties = $this->propertyModel->getAllForAdmin();
        $complaints = $this->complaintModel->findAll();
        $auditLogs = $this->auditModel->findAll();

        $this->render('admin/dashboard', [
            'admin' => Auth::user(),
            'users' => $users,
            'properties' => $properties,
            'complaints' => $complaints,
            'audit_logs' => $auditLogs,
            'csrf_token' => Auth::csrfToken()
        ]);
    }

    public function toggleUserStatus(): void {
        Auth::requireRole('admin');
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            echo "Invalid CSRF token.";
            return;
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $isActive = (bool)($_POST['is_active'] ?? 0);

        $this->userModel->updateStatus($userId, $isActive);
        $this->auditModel->log(Auth::user()['user_id'], $isActive ? 'ACTIVATE_USER' : 'DEACTIVATE_USER', 'user', $userId);

        $this->redirect('/admin/users');
    }

    public function approveProperty(): void {
        Auth::requireRole('admin');
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            echo "Invalid CSRF token.";
            return;
        }

        $propertyId = (int)($_POST['property_id'] ?? 0);
        $isApproved = (bool)($_POST['is_approved'] ?? 1);
        $isVerified = (bool)($_POST['is_verified'] ?? 1);

        $this->propertyModel->updateApproval($propertyId, $isApproved, $isVerified);
        $this->auditModel->log(Auth::user()['user_id'], 'APPROVE_PROPERTY', 'property', $propertyId, 'Property approved and verified.');

        $this->redirect('/admin/users');
    }

    public function assignBroker(): void {
        Auth::requireRole('admin');
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            echo "Invalid CSRF token.";
            return;
        }

        $brokerId = (int)($_POST['broker_id'] ?? 0);
        $propertyId = (int)($_POST['property_id'] ?? 0);

        try {
            $this->assignmentModel->assign($brokerId, $propertyId);
            $this->auditModel->log(Auth::user()['user_id'], 'ASSIGN_BROKER', 'property', $propertyId, "Assigned broker ID {$brokerId}");
            $_SESSION['success'] = 'Broker assigned successfully.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to assign broker: ' . $e->getMessage();
        }

        $this->redirect('/admin/users');
    }

    public function resolveComplaint(): void {
        Auth::requireRole('admin');
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            echo "Invalid CSRF token.";
            return;
        }

        $complaintId = (int)($_POST['complaint_id'] ?? 0);
        $status = $_POST['status'] ?? 'resolved';

        $this->complaintModel->resolve($complaintId, Auth::user()['user_id'], $status);
        $this->auditModel->log(Auth::user()['user_id'], 'RESOLVE_COMPLAINT', 'complaint', $complaintId, "Status updated to {$status}");

        $this->redirect('/admin/users');
    }
}
