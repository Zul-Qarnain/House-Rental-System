<?php
class BrokerController extends Controller {
    private BrokerAssignment $assignmentModel;
    private PropertyVisit $visitModel;
    private Commission $commissionModel;

    public function __construct() {
        $this->assignmentModel = new BrokerAssignment();
        $this->visitModel = new PropertyVisit();
        $this->commissionModel = new Commission();
    }

    public function dashboard(): void {
        Auth::requireRole('broker');
        $broker = Auth::user();

        $assignments = $this->assignmentModel->findActiveByBroker($broker['user_id']);
        $visits = $this->visitModel->findByBroker($broker['user_id']);
        $commissions = $this->commissionModel->findByBroker($broker['user_id']);

        $this->render('broker/dashboard', [
            'user' => $broker,
            'assignments' => $assignments,
            'visits' => $visits,
            'commissions' => $commissions,
            'csrf_token' => Auth::csrfToken()
        ]);
    }

    public function scheduleVisit(): void {
        Auth::requireRole('broker', 'tenant');
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            echo "Invalid CSRF token.";
            return;
        }

        $user = Auth::user();
        $propertyId = (int)($_POST['property_id'] ?? 0);
        $tenantId = $user['role'] === 'tenant' ? $user['user_id'] : (int)($_POST['tenant_id'] ?? 0);
        $brokerId = $user['role'] === 'broker' ? $user['user_id'] : (int)($_POST['broker_id'] ?? 0);
        $scheduledAt = $_POST['scheduled_at'] ?? date('Y-m-d H:i:s');
        $notes = trim($_POST['notes'] ?? '');

        $this->visitModel->schedule($propertyId, $tenantId, $brokerId ?: null, $scheduledAt, $notes);
        $this->redirect($user['role'] === 'broker' ? '/broker/dashboard' : '/tenant/dashboard');
    }

    public function updateVisitStatus(): void {
        Auth::requireRole('broker');
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            echo "Invalid CSRF token.";
            return;
        }

        $visitId = (int)($_POST['visit_id'] ?? 0);
        $status = $_POST['status'] ?? 'completed';

        $this->visitModel->updateStatus($visitId, $status);
        $this->redirect('/broker/dashboard');
    }
}
