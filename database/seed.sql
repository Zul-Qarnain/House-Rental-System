-- =====================================================================
-- Online Rental Property Management System — Seed Data
-- =====================================================================

-- 1. Users (password for all is 'password123')
INSERT INTO users (user_id, name, email, phone, password_hash, role, is_verified, is_active) VALUES
(1, 'System Admin', 'admin@proptech.com', '+1-555-0101', '$2y$12$uFjCSToi7BOS8mVK9Blr5.nXcpjU0Bd9Ii5sWDjZM52.L/UQwa4ry', 'admin', 1, 1),
(2, 'Eleanor Vance (Owner)', 'owner@proptech.com', '+1-555-0102', '$2y$12$uFjCSToi7BOS8mVK9Blr5.nXcpjU0Bd9Ii5sWDjZM52.L/UQwa4ry', 'owner', 1, 1),
(3, 'Marcus Sterling (Owner)', 'owner2@proptech.com', '+1-555-0103', '$2y$12$uFjCSToi7BOS8mVK9Blr5.nXcpjU0Bd9Ii5sWDjZM52.L/UQwa4ry', 'owner', 1, 1),
(4, 'Sarah Jenkins (Broker)', 'broker@proptech.com', '+1-555-0104', '$2y$12$uFjCSToi7BOS8mVK9Blr5.nXcpjU0Bd9Ii5sWDjZM52.L/UQwa4ry', 'broker', 1, 1),
(5, 'John Doe (Tenant)', 'tenant@proptech.com', '+1-555-0105', '$2y$12$uFjCSToi7BOS8mVK9Blr5.nXcpjU0Bd9Ii5sWDjZM52.L/UQwa4ry', 'tenant', 1, 1),
(6, 'Alice Smith (Tenant)', 'tenant2@proptech.com', '+1-555-0106', '$2y$12$uFjCSToi7BOS8mVK9Blr5.nXcpjU0Bd9Ii5sWDjZM52.L/UQwa4ry', 'tenant', 1, 1);

-- 2. Properties
INSERT INTO properties (property_id, owner_id, title, description, address_line, city, price_per_month, bedrooms, bathrooms, area_sqft, availability_status, is_approved, is_verified) VALUES
(1, 2, 'The Aurora Residences', 'Luxury high-rise residential apartment with floor-to-ceiling windows and panoramic skyline views.', '100 Skyline Blvd, Suite 1402', 'New York', 3500.00, 2, 2, 1200.00, 'rented', 1, 1),
(2, 2, 'Grand Horizon Suite', 'Modern open-concept luxury condo featuring premium modern finishes and private balcony.', '250 Ocean Drive', 'New York', 4200.00, 3, 2, 1600.00, 'available', 1, 1),
(3, 3, 'Skyline Penthouse', 'Exclusive top-floor penthouse with private rooftop terrace and concierge service.', '500 Park Avenue', 'Chicago', 5500.00, 4, 3, 2200.00, 'available', 1, 1),
(4, 3, 'Bayside Luxury Apartment', 'Cozy waterfront studio close to business district and public transportation.', '88 Bay Street', 'Miami', 2800.00, 1, 1, 850.00, 'pending', 0, 0);

-- 3. Property Images
INSERT INTO property_images (image_id, property_id, image_url, is_cover) VALUES
(1, 1, 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80', 1),
(2, 1, 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80', 0),
(3, 2, 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80', 1),
(4, 3, 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80', 1),
(5, 4, 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=800&q=80', 1);

-- 4. Broker Assignments
INSERT INTO broker_assignments (assignment_id, broker_id, property_id, assigned_at) VALUES
(1, 4, 1, CURRENT_TIMESTAMP),
(2, 4, 2, CURRENT_TIMESTAMP);

-- 5. Rental Requests
INSERT INTO rental_requests (request_id, property_id, tenant_id, status, requested_move_in, message, responded_at, responded_by) VALUES
(1, 1, 5, 'approved', '2026-09-01', 'Interested in securing a 12-month lease starting September.', CURRENT_TIMESTAMP, 2),
(2, 2, 6, 'pending', '2026-09-15', 'I would love to schedule a viewing and submit my application.', NULL, NULL),
(3, 4, 5, 'rejected', '2026-10-01', 'Can I move in earlier next month?', CURRENT_TIMESTAMP, 3);

-- 6. Rental Agreements
INSERT INTO rental_agreements (agreement_id, request_id, broker_id, start_date, end_date, monthly_rent, status) VALUES
(1, 1, 4, '2026-09-01', '2027-08-31', 3500.00, 'active');

-- 7. Property Visits
INSERT INTO property_visits (visit_id, property_id, tenant_id, broker_id, scheduled_at, status, notes) VALUES
(1, 2, 6, 4, '2026-08-28 14:00:00', 'scheduled', 'Guided walkthrough scheduled with Sarah Jenkins.');

-- 8. Commissions
INSERT INTO commissions (commission_id, broker_id, agreement_id, amount, status, paid_at) VALUES
(1, 4, 1, 1750.00, 'paid', CURRENT_TIMESTAMP);

-- 9. Reviews
INSERT INTO reviews (review_id, agreement_id, rating, feedback) VALUES
(1, 1, 5, 'Outstanding property with world-class amenities, excellent natural lighting, and prompt management response.');

-- 10. Review Replies
INSERT INTO review_replies (reply_id, review_id, owner_id, reply_text) VALUES
(1, 1, 2, 'Thank you for the wonderful review, John! We are glad you enjoy living at The Aurora.');

-- 11. Messages
INSERT INTO messages (message_id, sender_id, receiver_id, property_id, content, is_read) VALUES
(1, 5, 2, 1, 'Hi Eleanor, where can I find the building gate code?', 1),
(2, 2, 5, 1, 'Hello John! The gate code is #4921. Let me know if you need anything else.', 1);

-- 12. Notifications
INSERT INTO notifications (notification_id, user_id, type, content, related_entity_type, related_entity_id, is_read) VALUES
(1, 2, 'rental_request', 'New rental request received from John Doe for The Aurora Residences.', 'rental_request', 1, 1),
(2, 5, 'request_approved', 'Your rental request for The Aurora Residences has been approved!', 'rental_agreement', 1, 0);

-- 13. Complaints
INSERT INTO complaints (complaint_id, filed_by, against_user_id, against_property_id, description, status) VALUES
(1, 5, NULL, 4, 'Listing photos do not accurately reflect the actual unit layout.', 'open');

-- 14. Admin Actions
INSERT INTO admin_actions (action_id, admin_id, action_type, target_type, target_id, notes) VALUES
(1, 1, 'APPROVE_PROPERTY', 'property', 1, 'Verified ownership deed and safety inspection certificates.');
