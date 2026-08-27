<?php
function test_rental_request_lifecycle(): void {
    $userModel = new User();
    $propertyModel = new Property();
    $requestModel = new RentalRequest();

    $ownerId = $userModel->create('Request Owner', 'owner_req_' . uniqid() . '@test.com', '555-1111', 'pass', 'owner');
    $tenantId = $userModel->create('Request Tenant', 'tenant_req_' . uniqid() . '@test.com', '555-2222', 'pass', 'tenant');

    $propId = $propertyModel->create([
        'owner_id' => $ownerId,
        'title' => 'Request Property',
        'address_line' => '456 Sample Rd',
        'city' => 'SampleCity',
        'price_per_month' => 1500.00
    ]);

    $reqId = $requestModel->create($propId, $tenantId, '2026-09-01', 'Please accept my application.');
    assert_true($reqId > 0, 'Request ID must be valid');

    $req = $requestModel->findById($reqId);
    assert_equal('pending', $req['status'], 'Initial request status must be pending');

    $requestModel->updateStatus($reqId, 'approved', $ownerId);
    $approved = $requestModel->findById($reqId);
    assert_equal('approved', $approved['status'], 'Request status must update to approved');
    assert_equal($ownerId, (int)$approved['responded_by'], 'Responded by owner ID must match');
}
