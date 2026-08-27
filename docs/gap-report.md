# Gap Analysis Report — Rental Property Management Platform

## 1. Overview
This report inventories the existing HTML/CSS/JS export in `stitch_proptech_management_suite/`, cross-references it against the MySQL 8.0 schema (`db.schema`), and maps every database table and feature requirement to specific controllers, models, and views.

---

## 2. Reusable Existing Pages (HTML Templates to PHP Views)

The following pages exist in the Stitch export and will be reused as PHP views with MVC wiring:

1. **Auth Pages**
   - `login_proptech_os/code.html` -> `views/auth/login.php` (Sign in form, handles POST `/login`).
   - `sign_up_proptech_os/code.html` -> `views/auth/register.php` (Registration form with role selection: tenant, owner, broker).
   - `reset_password_proptech_os/code.html` -> `views/auth/forgot_password.php` & `views/auth/reset_password.php` (Password recovery & token reset).

2. **Public Property Discovery & Details**
   - `property_search_discovery/code.html` & `property_marketplace_public_discovery/code.html` -> `views/public/index.php` & `views/public/search.php` (Filterable marketplace: city, price range, bedrooms).
   - `property_details_the_aurora_residences/code.html` -> `views/public/property_detail.php` (Property gallery, specs, availability status, review listing, rental request trigger).

3. **Tenant Portal**
   - `tenant_dashboard/code.html` & `tenant_dashboard_my_home/code.html` -> `views/tenant/dashboard.php` & `views/tenant/agreements.php` (Active rental agreements, request status/history, notification list).
   - `rental_application_checkout/code.html` -> `views/tenant/apply.php` (Rental request submission with move-in date & message).

4. **Owner Portal**
   - `owner_dashboard_portfolio_overview/code.html` & `owner_portfolio_management/code.html` -> `views/owner/dashboard.php` (Portfolio list, availability toggles, review display).
   - `manage_property_add_new_listing/code.html` -> `views/owner/property_form.php` (Add/edit property, upload/manage images, mark cover image).
   - `rental_requests_inbox/code.html` -> `views/owner/requests.php` (Incoming rental requests inbox with Approve/Reject actions).

5. **Broker Portal**
   - `broker_management_suite/code.html` -> `views/broker/dashboard.php` (Assigned properties, rental deal management, commission tracking ledger).
   - `broker_portal_visit_schedule_ledger/code.html` -> `views/broker/visits.php` (Property visit schedule and visit status management).

6. **Admin Portal**
   - `system_administration/code.html` -> `views/admin/users.php` & `views/admin/properties.php` (User activation/deactivation, role management, property approval & verification).
   - `admin_control_center_resolution_desk/code.html` -> `views/admin/complaints.php` & `views/admin/audit_log.php` (Complaint resolution desk & `admin_actions` audit trail).

---

## 3. Missing Views / UI Gaps & Enhancements

To achieve 100% feature and schema coverage without breaking the design language, the following views and modal components will be constructed using the established Tailwind/Inter/Material Symbols design tokens:

1. **Direct Messaging / Chat Center (`views/messages/index.php`)**
   - *Gap:* `messages` table allows communication between users (tenant, owner, broker).
   - *Solution:* Dedicated chat thread UI matching the slate/emerald theme for viewing threads and sending messages.

2. **Complaint Submission Modal / View (`views/complaints/create.php`)**
   - *Gap:* Tenants/owners/brokers need a way to file complaints against a user OR a property (enforcing XOR constraint).
   - *Solution:* Form modal reachable from property details and dashboard cards.

3. **Owner Review Reply Component (`views/owner/reviews.php`)**
   - *Gap:* `review_replies` table allows owners to reply to property reviews.
   - *Solution:* Inline reply box attached to each review card in the owner dashboard.

4. **Admin Broker Assignment Modal (`views/admin/broker_assignment.php`)**
   - *Gap:* Admin needs an explicit workflow to assign an active broker to a property (`broker_assignments`).
   - *Solution:* Quick assignment dropdown/modal in the admin property catalog.

5. **Tenant Review Submission (`views/tenant/submit_review.php`)**
   - *Gap:* Tenants with active/completed agreements need to submit 1-5 star rating and feedback (`reviews`).
   - *Solution:* Review submission card on completed agreement cards in tenant dashboard.

---

## 4. Schema Reachability Confirmation

All 15 tables, 2 views, and DB triggers in `db.schema` are fully reachable and exercisable through the planned MVC routing and UI views:
- `users`, `password_resets` -> AuthController
- `properties`, `property_images` -> PropertyController
- `broker_assignments` -> BrokerController / AdminController
- `rental_requests` -> RentalController
- `rental_agreements` -> RentalController
- `property_visits` -> BrokerController
- `commissions` -> BrokerController
- `reviews`, `review_replies` -> ReviewController
- `messages` -> MessageController
- `notifications` -> NotificationController
- `complaints` -> AdminController
- `admin_actions` -> AdminController

---

## 5. Required CI/CD Secrets

The GitHub Actions workflow (`.github/workflows/ci-cd.yml`) expects the following secrets:
- `INFINITYFREE_DB_HOST`
- `INFINITYFREE_DB_NAME`
- `INFINITYFREE_DB_USER`
- `INFINITYFREE_DB_PASS`
- `INFINITYFREE_FTP_HOST`
- `INFINITYFREE_FTP_USER`
- `INFINITYFREE_FTP_PASS`

---

*Report generated and validated for implementation.*
