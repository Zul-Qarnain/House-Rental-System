<?php
function test_property_creation_and_status(): void {
    $userModel = new User();
    $propertyModel = new Property();

    $ownerEmail = 'propowner_' . uniqid() . '@test.com';
    $ownerId = $userModel->create('Test Owner', $ownerEmail, '555-9999', 'pass123', 'owner');

    $propId = $propertyModel->create([
        'owner_id' => $ownerId,
        'title' => 'Test Apartment',
        'address_line' => '123 Test St',
        'city' => 'TestCity',
        'price_per_month' => 2000.00,
        'bedrooms' => 2,
        'bathrooms' => 1,
        'area_sqft' => 900.00,
        'availability_status' => 'available'
    ]);

    assert_true($propId > 0, 'Property ID should be valid');

    $prop = $propertyModel->findById($propId);
    assert_equal('Test Apartment', $prop['title']);
    assert_equal('available', $prop['availability_status']);

    $propertyModel->updateStatus($propId, 'rented');
    $updated = $propertyModel->findById($propId);
    assert_equal('rented', $updated['availability_status'], 'Status should transition to rented');
}
