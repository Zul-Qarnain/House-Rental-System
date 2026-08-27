<?php
class PropertyVisitTest {
    public function run(): void {
        $visitModel = new PropertyVisit();
        $userModel = new User();
        $propertyModel = new Property();

        // 1. Get demo tenant, owner, broker, and property
        $tenant = $userModel->findByEmail('tenant@proptech.com');
        $owner = $userModel->findByEmail('owner@proptech.com');
        $broker = $userModel->findByEmail('broker@proptech.com');
        $properties = $propertyModel->findByOwner($owner['user_id']);

        assert_true(!empty($tenant), "Tenant user must exist for visit test");
        assert_true(!empty($owner), "Owner user must exist for visit test");
        assert_true(!empty($broker), "Broker user must exist for visit test");
        assert_true(!empty($properties), "Owner properties must exist for visit test");

        $propertyId = $properties[0]['property_id'];

        // 2. Tenant schedules a walkthrough visit request
        $visitId = $visitModel->schedule($propertyId, $tenant['user_id'], null, date('Y-m-d H:i:s', strtotime('+2 days')), 'Test morning walkthrough');
        assert_true($visitId > 0, "Visit request ID should be positive integer");

        // 3. Homeowner assigns broker to the requested visit
        $assigned = $visitModel->assignBroker($visitId, $broker['user_id']);
        assert_true($assigned, "Homeowner should successfully assign broker to visit");

        // 4. Verify visit details
        $visit = $visitModel->findById($visitId);
        assert_equal((int)$broker['user_id'], (int)$visit['broker_id'], "Assigned broker ID should match");

        // 5. Broker completes the visit
        $completed = $visitModel->updateStatus($visitId, 'completed');
        assert_true($completed, "Broker should successfully mark visit completed");

        $updatedVisit = $visitModel->findById($visitId);
        assert_equal('completed', $updatedVisit['status'], "Visit status should be updated to completed");
    }
}
