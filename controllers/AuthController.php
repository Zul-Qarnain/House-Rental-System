<?php
class AuthController extends Controller {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function showLogin(): void {
        if (Auth::check()) {
            $this->redirectDashboard();
        }
        $this->render('auth/login', [
            'csrf_token' => Auth::csrfToken(),
            'error' => $_SESSION['error'] ?? null
        ]);
        unset($_SESSION['error']);
    }

    public function processLogin(): void {
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid session security token.';
            $this->redirect('/login');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['error'] = 'Invalid email or password.';
            $this->redirect('/login');
        }

        if (!$user['is_active']) {
            $_SESSION['error'] = 'Account is deactivated. Please contact support.';
            $this->redirect('/login');
        }

        Auth::login($user);
        $this->redirectDashboard();
    }

    public function showRegister(): void {
        if (Auth::check()) {
            $this->redirectDashboard();
        }
        $this->render('auth/register', [
            'csrf_token' => Auth::csrfToken(),
            'error' => $_SESSION['error'] ?? null
        ]);
        unset($_SESSION['error']);
    }

    public function processRegister(): void {
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid session security token.';
            $this->redirect('/register');
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'tenant';

        if (empty($name) || empty($email) || empty($password) || !in_array($role, ['tenant', 'owner', 'broker'])) {
            $_SESSION['error'] = 'Please fill out all required fields properly.';
            $this->redirect('/register');
        }

        if ($this->userModel->findByEmail($email)) {
            $_SESSION['error'] = 'Email address is already registered.';
            $this->redirect('/register');
        }

        try {
            $userId = $this->userModel->create($name, $email, $phone, $password, $role);
            $user = $this->userModel->findById($userId);
            Auth::login($user);
            $this->redirectDashboard();
        } catch (Exception $e) {
            $_SESSION['error'] = 'Registration failed: ' . $e->getMessage();
            $this->redirect('/register');
        }
    }

    public function logout(): void {
        Auth::logout();
        $this->redirect('/login');
    }

    public function showForgotPassword(): void {
        $this->render('auth/forgot_password', [
            'csrf_token' => Auth::csrfToken(),
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null
        ]);
        unset($_SESSION['error'], $_SESSION['success']);
    }

    public function processForgotPassword(): void {
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid token.';
            $this->redirect('/forgot-password');
        }

        $email = trim($_POST['email'] ?? '');
        $user = $this->userModel->findByEmail($email);
        if ($user) {
            $token = bin2hex(random_bytes(16));
            $tokenHash = hash('sha256', $token);
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $this->userModel->createPasswordReset($user['user_id'], $tokenHash, $expiresAt);
            $_SESSION['success'] = "Password reset link generated: /reset-password?token={$token}";
        } else {
            $_SESSION['error'] = "No user found with that email address.";
        }
        $this->redirect('/forgot-password');
    }

    public function showResetPassword(): void {
        $token = $_GET['token'] ?? '';
        $this->render('auth/reset_password', [
            'csrf_token' => Auth::csrfToken(),
            'token' => $token,
            'error' => $_SESSION['error'] ?? null
        ]);
        unset($_SESSION['error']);
    }

    public function processResetPassword(): void {
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid token.';
            $this->redirect('/reset-password');
        }

        $token = $_POST['token'] ?? '';
        $newPassword = $_POST['password'] ?? '';

        $tokenHash = hash('sha256', $token);
        $reset = $this->userModel->findPasswordReset($tokenHash);

        if (!$reset) {
            $_SESSION['error'] = 'Invalid or expired password reset token.';
            $this->redirect('/forgot-password');
        }

        $this->userModel->updatePassword($reset['user_id'], $newPassword);
        $this->userModel->markResetUsed($reset['reset_id']);

        $_SESSION['success'] = 'Password reset successfully! Please sign in.';
        $this->redirect('/login');
    }

    private function redirectDashboard(): void {
        $user = Auth::user();
        switch ($user['role']) {
            case 'admin': $this->redirect('/admin/users'); break;
            case 'owner': $this->redirect('/owner/dashboard'); break;
            case 'broker': $this->redirect('/broker/dashboard'); break;
            case 'tenant': default: $this->redirect('/tenant/dashboard'); break;
        }
    }
}
