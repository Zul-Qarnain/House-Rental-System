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

        $properties = $propertyModel->findByOwner($owner['user_id']);
        assert_true(!empty($properties), "Owner properties must exist");

        $propertyId = $properties[0]['property_id'];

        // 1. Homeowner assigns broker to property
        $assignmentId = $assignmentModel->assign($broker['user_id'], $propertyId);
        assert_true($assignmentId > 0, "Broker assignment ID should be positive");

        // 2. Verify active assignment
        $activeAssign = $assignmentModel->findActiveByProperty($propertyId);
        assert_true(!empty($activeAssign), "Active assignment should exist");
        assert_equal((int)$broker['user_id'], (int)$activeAssign['broker_id'], "Broker ID should match");
    }
}
