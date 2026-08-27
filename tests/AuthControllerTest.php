<?php
function test_auth_password_verification(): void {
    $userModel = new User();
    $email = 'auth_test_' . uniqid() . '@test.com';
    $password = 'CorrectPassword123!';

    $userModel->create('Auth Test', $email, '555', $password, 'tenant');
    $user = $userModel->findByEmail($email);

    assert_true($user !== null);
    assert_true(password_verify($password, $user['password_hash']), 'Correct password must verify');
    assert_true(!password_verify('WrongPassword', $user['password_hash']), 'Wrong password must fail');
}
