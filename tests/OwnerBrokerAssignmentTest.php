<?php
class OwnerBrokerAssignmentTest {
    public function run(): void {
        $userModel = new User();
        $propertyModel = new Property();
        $assignmentModel = new BrokerAssignment();

        $owner = $userModel->findByEmail('owner@proptech.com');
        $broker = $userModel->findByEmail('broker@proptech.com');

        assert_true(!empty($owner), "Owner user must exist");
        assert_true(!empty($broker), "Broker user must exist");

        // Create a dedicated test property for clean assignment testing
        $propertyId = $propertyModel->create([
            'owner_id' => $owner['user_id'],
            'title' => 'Broker Test Apartment',
            'description' => 'Dedicated for broker assignment unit test',
            'address_line' => '789 Broker Way',
            'city' => 'Metropolis',
            'price_per_month' => 1850.00,
            'bedrooms' => 2,
            'bathrooms' => 2,
            'area_sqft' => 950.0,
            'availability_status' => 'available'
        ]);

        // 1. Homeowner assigns broker to property
        $assignmentId = $assignmentModel->assign($broker['user_id'], $propertyId);
        assert_true($assignmentId > 0, "Broker assignment ID should be positive");

        // 2. Verify active assignment
        $activeAssign = $assignmentModel->findActiveByProperty($propertyId);
        assert_true(!empty($activeAssign), "Active assignment should exist");
        assert_equal((int)$broker['user_id'], (int)$activeAssign['broker_id'], "Broker ID should match");
    }
}
