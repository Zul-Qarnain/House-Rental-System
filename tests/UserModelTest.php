<?php
function test_user_creation_and_lookup(): void {
    $userModel = new User();
    $email = 'unittest_' . uniqid() . '@test.com';
    
    $userId = $userModel->create('Unit Test User', $email, '1234567890', 'secret123', 'tenant');
    assert_true($userId > 0, 'User ID should be greater than 0');

    $found = $userModel->findByEmail($email);
    assert_true($found !== null, 'User should be found by email');
    assert_equal('Unit Test User', $found['name'], 'Name must match');
    assert_true(password_verify('secret123', $found['password_hash']), 'Password hash must verify');
}
