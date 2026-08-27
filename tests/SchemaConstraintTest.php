<?php
function test_single_active_agreement_per_property(): void {
    $userModel = new User();
    $propertyModel = new Property();
    $requestModel = new RentalRequest();
    $agreementModel = new RentalAgreement();

    $ownerId = $userModel->create('Uniq Owner', 'owner_uniq_' . uniqid() . '@test.com', '555-0000', 'pass', 'owner');
    $tenant1Id = $userModel->create('Tenant 1', 'tenant1_' . uniqid() . '@test.com', '555-0001', 'pass', 'tenant');
    $tenant2Id = $userModel->create('Tenant 2', 'tenant2_' . uniqid() . '@test.com', '555-0002', 'pass', 'tenant');

    $propId = $propertyModel->create([
        'owner_id' => $ownerId,
        'title' => 'Single Active Property Test',
        'address_line' => '789 Unique Way',
        'city' => 'UniqCity',
        'price_per_month' => 3000.00
    ]);

    $req1Id = $requestModel->create($propId, $tenant1Id, '2026-09-01', 'Req 1');
    $req2Id = $requestModel->create($propId, $tenant2Id, '2026-09-01', 'Req 2');

    // First active agreement should succeed
    $agr1Id = $agreementModel->create($req1Id, null, '2026-09-01', '2027-08-31', 3000.00);
    assert_true($agr1Id > 0, 'First active agreement created successfully');

    // Second active agreement on SAME property MUST fail due to uq_active_rental_property constraint
    assert_throws(function() use ($agreementModel, $req2Id) {
        $agreementModel->create($req2Id, null, '2026-09-01', '2027-08-31', 3000.00);
    }, 'Second active agreement on same property must throw DB Exception');
}
