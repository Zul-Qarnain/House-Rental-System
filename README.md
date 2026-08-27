# 🏡 House Rental Management System (PropTech OS)

A high-trust, production-ready **Online Rental Property Management Platform** built with raw **PHP 8.x**, **MVC Architecture**, **MySQL 8.0**, and **Tailwind CSS**.

---

### 👥 Team Members & Contribution Breakdown

This project was built collaboratively by our team with dedicated module responsibilities:

| Contributor | GitHub Username | Assigned Module & Deliverables |
| :--- | :--- | :--- |
| **Zulqarnain** (Project Lead) | [@Zul-Qarnain](https://github.com/Zul-Qarnain) | Core MVC Architecture, Database Schema, Auth System, Tenant Portal & Public Marketplace |
| **Naimul (Tashin)** | [@Tashin90](https://github.com/Tashin90) | Admin Control Desk, Property Approvals, Complaint Resolution Desk & Audit Trail (`admin_actions`) |
| **Rahul** | [@Rahul53662](https://github.com/Rahul53662) | Property Owner Portfolio, Listing Form, Rental Request Inbox & Review Reply System |
| **Labib** | [@Md-Mahir-Labib](https://github.com/Md-Mahir-Labib) | Broker Suite, Property Visit Walkthrough Scheduler, Commission Ledger & Direct Messaging |

---

### 🚀 Key Features by Module

#### 1. Public Marketplace & Tenant Portal
- Filterable property catalog (city, price range, bedrooms, availability status).
- Property detail page with verified badges, specs, image galleries, and reviews.
- Rental request submission & application tracking desk.
- Verified tenant review submission.

#### 2. Property Owner Dashboard
- Add/edit listings with cover image management.
- Availability status toggling (`available`, `pending`, `rented`).
- Incoming rental request inbox with 1-click Approve/Reject actions.
- Review reply system linked directly to owner property reviews.

#### 3. Real Estate Broker Suite
- Active client property assignments ledger.
- Walkthrough visit scheduling with status updates (`scheduled`, `completed`).
- Automated commission tracking ledger (calculated on deal approvals).

#### 4. Admin Control Desk & Audit Trail
- Platform user activation/deactivation and role controls.
- Property verification and approval workflow.
- Broker property assignment modal.
- Complaint resolution desk (enforcing XOR constraint on user vs property complaints).
- Persistent audit log (`admin_actions`).

#### 5. Real-Time Security & System Design
- Prepared statements via PDO on all database queries.
- CSRF token validation on all POST form submissions.
- Role-based route authorization guards (`Auth::requireRole()`).
- Responsive Slate/Emerald modern UI matching design system specs.

---

### ⚙️ CI/CD & Automated InfinityFree Deployment

Automated CI/CD pipeline configured in `.github/workflows/ci-cd.yml`:
1. Runs automated unit tests on an ephemeral MySQL 8.0 container.
2. Injects production database credentials from GitHub Secrets.
3. Automatically deploys clean production artifacts to InfinityFree hosting via FTP on every push to `main`.
