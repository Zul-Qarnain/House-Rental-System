# House Rental Management System

An Online Rental Property Management Platform built with PHP (MVC Architecture), MySQL, and Tailwind CSS.

---

## Team Members & Contribution Breakdown

| Contributor | GitHub Profile | Assigned Module & Deliverables |
| :--- | :--- | :--- |
| **Zulqarnain** (Lead) | [@Zul-Qarnain](https://github.com/Zul-Qarnain) | Core MVC Architecture, Database Schema, Auth System, Tenant Portal & Public Marketplace |
| **Naimul (Tashin)** | [@Tashin90](https://github.com/Tashin90) | Admin Control Desk, Account Status Management, Complaint Resolution Desk & Audit Logs (`admin_actions`) |
| **Rahul** | [@Rahul53662](https://github.com/Rahul53662) | Homeowner Portfolio, Listing Wizard, Rental Request Inbox & Review Reply System |
| **Labib** | [@Md-Mahir-Labib](https://github.com/Md-Mahir-Labib) | Broker Portal, Assigned Property Ledger, Walkthrough Visit Execution & Commission Payout Ledger |

---

## Local Setup (XAMPP / Localhost)

Follow these steps to run the application locally using XAMPP:

### 1. Copy Project Files
Copy the project folder into your XAMPP `htdocs` directory:
- **Windows:** `C:\xampp\htdocs\House-Rental-System`
- **Linux:** `/opt/lampp/htdocs/House-Rental-System`
- **macOS:** `/Applications/XAMPP/htdocs/House-Rental-System`

### 2. Start Services
Open the XAMPP Control Panel and start **Apache** and **MySQL**.

### 3. Setup Database
1. Open phpMyAdmin at `http://localhost/phpmyadmin`
2. Create a new database named `defaultdb`.
3. Select `defaultdb` and go to the **Import** tab.
4. Import `database/schema.sql` first, followed by `database/seed.sql` to load initial seed data.

### 4. Configuration
Create `config/config.local.php` in the root project folder:
```php
<?php
return [
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => 3306,
    'DB_NAME' => 'defaultdb',
    'DB_USER' => 'root',  // Default XAMPP username
    'DB_PASS' => '',      // Default XAMPP password is empty
    'DB_SSL_CA' => null,
];
```

### 5. Access Application
Run the built-in PHP development server inside the project root:
```bash
php -S 127.0.0.1:8000 -t public public/index.php
```
Open `http://127.0.0.1:8000` in your browser. Alternatively, access via `http://localhost/House-Rental-System/`.

---

## User Roles & System Features

### 1. System Admin (`admin@proptech.com`)
- **User Management:** Activate or deactivate user accounts.
- **Resolution Desk:** Manage and resolve filed complaints.
- **Audit Logs:** Track system administrator actions (`admin_actions`).

### 2. Homeowner (`owner@proptech.com`)
- **Property Portfolio:** Create, edit, and publish property listings (instant live listing activation).
- **Broker Management:** Assign and change brokers assigned to property listings or walkthrough visits.
- **Listing Status:** Change property status (`available`, `pending`, `rented`).
- **Rental Applications:** Approve or reject tenant rental requests.
- **Reviews:** View tenant reviews and post replies.

### 3. Tenant / General User (`tenant@proptech.com`)
- **Marketplace Search:** Search and filter listings by city, rent price, and bedrooms.
- **Walkthrough Visits:** Schedule requested visit dates and times for properties.
- **Rental Applications:** Submit rental requests with proposed move-in dates.
- **Tenant Dashboard:** View active lease agreements, requested visits, and request history.
- **Reviews:** Submit ratings and reviews for rented properties.

### 4. Real Estate Broker (`broker@proptech.com`)
- **Assigned Portfolio:** View property listings assigned by homeowners.
- **Walkthrough Visits:** View assigned walkthrough visit requests and update status (`completed`).
- **Commission Ledger:** Track earned commission payouts calculated on approved deals.

---

## Deployment & CI/CD Workflow

Automated testing and deployment pipeline configured in `.github/workflows/ci-cd.yml`:

```
Git Push -> MySQL 8.0 Test Runner -> Inject Config -> FTP Deploy (htdocs/)
```

1. **Automated Testing:** Spins up a MySQL 8.0 container on GitHub Actions and executes test suites (`php tests/run_tests.php`).
2. **Configuration Injection:** Injects production database credentials from GitHub Repository Secrets into `config/config.local.php`.
3. **Deployment:** Deploys code to the web hosting server (`htdocs/`) via FTP.

---

## Running Unit Tests

To run the custom test suite locally:
```bash
php tests/run_tests.php
```
