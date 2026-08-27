-- =====================================================================
-- Online Rental Property Management System — MySQL 8.0 Schema
-- Revision 2: fixes role-integrity, agreement duplication, active-record
-- uniqueness, and review–agreement linkage flagged in schema review.
-- MySQL has no CREATE TYPE / partial unique index / multi-table CHECK,
-- so those are implemented as inline ENUMs, generated-column + unique
-- index tricks, and BEFORE INSERT/UPDATE triggers respectively.
-- =====================================================================

SET NAMES utf8mb4;
SET default_storage_engine = InnoDB;

-- =====================================================================
-- users
-- =====================================================================
CREATE TABLE users (
    user_id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(120)  NOT NULL,
    email           VARCHAR(160)  NOT NULL UNIQUE,
    phone           VARCHAR(30),
    password_hash   VARCHAR(255)  NOT NULL,
    role            ENUM('tenant','owner','broker','admin') NOT NULL,
    is_verified     BOOLEAN       NOT NULL DEFAULT FALSE,
    is_active       BOOLEAN       NOT NULL DEFAULT TRUE,
    created_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                   ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE password_resets (
    reset_id        BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED NOT NULL,
    token_hash      VARCHAR(255) NOT NULL,
    expires_at      TIMESTAMP    NOT NULL,
    used_at         TIMESTAMP    NULL,
    created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pwreset_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_password_resets_user (user_id)
) ENGINE=InnoDB;

-- =====================================================================
-- properties  (owner_id role enforced by trigger below — FK alone can't)
-- =====================================================================
CREATE TABLE properties (
    property_id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_id            BIGINT UNSIGNED NOT NULL,
    title               VARCHAR(160) NOT NULL,
    description         TEXT,
    address_line        VARCHAR(255) NOT NULL,
    city                VARCHAR(100) NOT NULL,
    price_per_month     DECIMAL(12,2) NOT NULL CHECK (price_per_month >= 0),
    bedrooms            SMALLINT,
    bathrooms           SMALLINT,
    area_sqft           DECIMAL(10,2),
    availability_status ENUM('available','pending','rented') NOT NULL DEFAULT 'available',
    is_approved         BOOLEAN NOT NULL DEFAULT FALSE,
    is_verified         BOOLEAN NOT NULL DEFAULT FALSE,
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                                    ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_properties_owner FOREIGN KEY (owner_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_properties_owner (owner_id),
    INDEX idx_properties_status (availability_status),
    INDEX idx_properties_city (city)
) ENGINE=InnoDB;

-- fix #1: enforce owner_id -> users.role = 'owner'
DELIMITER $$
CREATE TRIGGER trg_properties_owner_role_ins
BEFORE INSERT ON properties
FOR EACH ROW
BEGIN
    IF (SELECT role FROM users WHERE user_id = NEW.owner_id) <> 'owner' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'properties.owner_id must reference a user with role = owner';
    END IF;
END$$
CREATE TRIGGER trg_properties_owner_role_upd
BEFORE UPDATE ON properties
FOR EACH ROW
BEGIN
    IF (SELECT role FROM users WHERE user_id = NEW.owner_id) <> 'owner' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'properties.owner_id must reference a user with role = owner';
    END IF;
END$$
DELIMITER ;

-- =====================================================================
-- property_images  (fix #7: at most one cover image per property)
-- =====================================================================
CREATE TABLE property_images (
    image_id        BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    property_id     BIGINT UNSIGNED NOT NULL,
    image_url       VARCHAR(500) NOT NULL,
    is_cover        BOOLEAN NOT NULL DEFAULT FALSE,
    -- generated column: non-NULL only when is_cover=TRUE; unique index
    -- then allows at most one TRUE row per property (MySQL unique index
    -- permits multiple NULLs, so non-cover rows never collide)
    cover_marker    BIGINT UNSIGNED
                     GENERATED ALWAYS AS (IF(is_cover, property_id, NULL)) VIRTUAL,
    uploaded_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_images_property FOREIGN KEY (property_id) REFERENCES properties(property_id) ON DELETE CASCADE,
    INDEX idx_property_images_property (property_id),
    UNIQUE INDEX uq_property_cover_image (cover_marker)
) ENGINE=InnoDB;

-- =====================================================================
-- broker_assignments  (fix #1 role check, fix #8 one active per property)
-- =====================================================================
CREATE TABLE broker_assignments (
    assignment_id   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    broker_id       BIGINT UNSIGNED NOT NULL,
    property_id     BIGINT UNSIGNED NOT NULL,
    assigned_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    unassigned_at   TIMESTAMP NULL,
    active_marker   BIGINT UNSIGNED
                     GENERATED ALWAYS AS (IF(unassigned_at IS NULL, property_id, NULL)) VIRTUAL,
    CONSTRAINT fk_assign_broker FOREIGN KEY (broker_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_assign_property FOREIGN KEY (property_id) REFERENCES properties(property_id) ON DELETE CASCADE,
    INDEX idx_broker_assignments_property (property_id),
    UNIQUE INDEX uq_active_broker_assignment (active_marker)
) ENGINE=InnoDB;

DELIMITER $$
CREATE TRIGGER trg_assign_broker_role_ins
BEFORE INSERT ON broker_assignments
FOR EACH ROW
BEGIN
    IF (SELECT role FROM users WHERE user_id = NEW.broker_id) <> 'broker' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'broker_assignments.broker_id must reference a user with role = broker';
    END IF;
END$$
CREATE TRIGGER trg_assign_broker_role_upd
BEFORE UPDATE ON broker_assignments
FOR EACH ROW
BEGIN
    IF (SELECT role FROM users WHERE user_id = NEW.broker_id) <> 'broker' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'broker_assignments.broker_id must reference a user with role = broker';
    END IF;
END$$
DELIMITER ;

-- =====================================================================
-- rental_requests  (fix #1 role checks, fix #4 richer status enum)
-- =====================================================================
CREATE TABLE rental_requests (
    request_id        BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    property_id        BIGINT UNSIGNED NOT NULL,
    tenant_id           BIGINT UNSIGNED NOT NULL,
    status              ENUM('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
    requested_move_in   DATE,
    message             TEXT,
    requested_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    responded_at        TIMESTAMP NULL,
    responded_by        BIGINT UNSIGNED NULL,   -- owner (or admin override) who approved/rejected
    CONSTRAINT fk_requests_property FOREIGN KEY (property_id) REFERENCES properties(property_id) ON DELETE CASCADE,
    CONSTRAINT fk_requests_tenant FOREIGN KEY (tenant_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_requests_responder FOREIGN KEY (responded_by) REFERENCES users(user_id),
    INDEX idx_rental_requests_property (property_id),
    INDEX idx_rental_requests_tenant (tenant_id),
    INDEX idx_rental_requests_status (status)
) ENGINE=InnoDB;

DELIMITER $$
CREATE TRIGGER trg_requests_tenant_role_ins
BEFORE INSERT ON rental_requests
FOR EACH ROW
BEGIN
    IF (SELECT role FROM users WHERE user_id = NEW.tenant_id) <> 'tenant' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'rental_requests.tenant_id must reference a user with role = tenant';
    END IF;
    IF NEW.responded_by IS NOT NULL
       AND (SELECT role FROM users WHERE user_id = NEW.responded_by) NOT IN ('owner','admin') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'rental_requests.responded_by must be an owner or admin';
    END IF;
END$$
CREATE TRIGGER trg_requests_tenant_role_upd
BEFORE UPDATE ON rental_requests
FOR EACH ROW
BEGIN
    IF (SELECT role FROM users WHERE user_id = NEW.tenant_id) <> 'tenant' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'rental_requests.tenant_id must reference a user with role = tenant';
    END IF;
    IF NEW.responded_by IS NOT NULL
       AND (SELECT role FROM users WHERE user_id = NEW.responded_by) NOT IN ('owner','admin') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'rental_requests.responded_by must be an owner or admin';
    END IF;
END$$
DELIMITER ;

-- =====================================================================
-- rental_agreements  (fix #2: Option A — no duplicated property/tenant/
-- owner columns; derive them from request_id via v_rental_agreements_full.
-- fix #3: only one ACTIVE agreement per property, via generated column.)
-- =====================================================================
-- property_id is a plain column, NOT app/user-writable in practice: it is
-- populated only by the BEFORE INSERT/UPDATE triggers below from
-- request_id, so it cannot independently drift from the request (this
-- replaces a generated-column approach, since MySQL generated columns
-- cannot contain subqueries — only same-row column expressions).
CREATE TABLE rental_agreements (
    agreement_id    BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_id      BIGINT UNSIGNED NOT NULL UNIQUE,
    property_id     BIGINT UNSIGNED NULL,   -- trigger-maintained, derived from request_id
    broker_id       BIGINT UNSIGNED NULL,   -- nullable: direct owner-tenant deals allowed
    start_date      DATE NOT NULL,
    end_date        DATE,
    monthly_rent    DECIMAL(12,2) NOT NULL,
    status          ENUM('active','completed','terminated') NOT NULL DEFAULT 'active',
    confirmed_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    -- generated column CAN reference property_id/status since those are
    -- real same-row columns; this enforces "one active agreement per property"
    active_marker   BIGINT UNSIGNED
                     GENERATED ALWAYS AS (IF(status = 'active', property_id, NULL)) VIRTUAL,
    CONSTRAINT fk_agreements_request FOREIGN KEY (request_id) REFERENCES rental_requests(request_id) ON DELETE RESTRICT,
    CONSTRAINT fk_agreements_property FOREIGN KEY (property_id) REFERENCES properties(property_id),
    CONSTRAINT fk_agreements_broker FOREIGN KEY (broker_id) REFERENCES users(user_id),
    INDEX idx_rental_agreements_broker (broker_id),
    UNIQUE INDEX uq_active_rental_property (active_marker)
) ENGINE=InnoDB;

DELIMITER $$
CREATE TRIGGER trg_agreements_set_property_ins
BEFORE INSERT ON rental_agreements
FOR EACH ROW
BEGIN
    SET NEW.property_id = (SELECT property_id FROM rental_requests WHERE request_id = NEW.request_id);
    IF NEW.broker_id IS NOT NULL
       AND (SELECT role FROM users WHERE user_id = NEW.broker_id) <> 'broker' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'rental_agreements.broker_id must reference a user with role = broker';
    END IF;
END$$
CREATE TRIGGER trg_agreements_set_property_upd
BEFORE UPDATE ON rental_agreements
FOR EACH ROW
BEGIN
    -- request_id is effectively immutable post-creation; re-derive anyway
    -- in case it's ever changed, to keep property_id from drifting.
    SET NEW.property_id = (SELECT property_id FROM rental_requests WHERE request_id = NEW.request_id);
    IF NEW.broker_id IS NOT NULL
       AND (SELECT role FROM users WHERE user_id = NEW.broker_id) <> 'broker' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'rental_agreements.broker_id must reference a user with role = broker';
    END IF;
END$$
DELIMITER ;

-- =====================================================================
-- property_visits
-- =====================================================================
CREATE TABLE property_visits (
    visit_id        BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    property_id     BIGINT UNSIGNED NOT NULL,
    tenant_id       BIGINT UNSIGNED NOT NULL,
    broker_id       BIGINT UNSIGNED NULL,
    scheduled_at    TIMESTAMP NOT NULL,
    status          ENUM('scheduled','completed','cancelled') NOT NULL DEFAULT 'scheduled',
    notes           TEXT,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_visits_property FOREIGN KEY (property_id) REFERENCES properties(property_id) ON DELETE CASCADE,
    CONSTRAINT fk_visits_tenant FOREIGN KEY (tenant_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_visits_broker FOREIGN KEY (broker_id) REFERENCES users(user_id),
    INDEX idx_property_visits_property (property_id),
    INDEX idx_property_visits_broker (broker_id)
) ENGINE=InnoDB;

DELIMITER $$
CREATE TRIGGER trg_visits_broker_role_ins
BEFORE INSERT ON property_visits
FOR EACH ROW
BEGIN
    IF NEW.broker_id IS NOT NULL
       AND (SELECT role FROM users WHERE user_id = NEW.broker_id) <> 'broker' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'property_visits.broker_id must reference a user with role = broker';
    END IF;
END$$
CREATE TRIGGER trg_visits_broker_role_upd
BEFORE UPDATE ON property_visits
FOR EACH ROW
BEGIN
    IF NEW.broker_id IS NOT NULL
       AND (SELECT role FROM users WHERE user_id = NEW.broker_id) <> 'broker' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'property_visits.broker_id must reference a user with role = broker';
    END IF;
END$$
DELIMITER ;

-- =====================================================================
-- commissions
-- =====================================================================
CREATE TABLE commissions (
    commission_id   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    broker_id       BIGINT UNSIGNED NOT NULL,
    agreement_id    BIGINT UNSIGNED NOT NULL,
    amount          DECIMAL(12,2) NOT NULL CHECK (amount >= 0),
    status          ENUM('pending','paid') NOT NULL DEFAULT 'pending',
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    paid_at         TIMESTAMP NULL,
    CONSTRAINT fk_commissions_broker FOREIGN KEY (broker_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_commissions_agreement FOREIGN KEY (agreement_id) REFERENCES rental_agreements(agreement_id) ON DELETE CASCADE,
    INDEX idx_commissions_broker (broker_id)
) ENGINE=InnoDB;

DELIMITER $$
CREATE TRIGGER trg_commissions_broker_role_ins
BEFORE INSERT ON commissions
FOR EACH ROW
BEGIN
    IF (SELECT role FROM users WHERE user_id = NEW.broker_id) <> 'broker' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'commissions.broker_id must reference a user with role = broker';
    END IF;
END$$
CREATE TRIGGER trg_commissions_broker_role_upd
BEFORE UPDATE ON commissions
FOR EACH ROW
BEGIN
    IF (SELECT role FROM users WHERE user_id = NEW.broker_id) <> 'broker' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'commissions.broker_id must reference a user with role = broker';
    END IF;
END$$
DELIMITER ;

-- =====================================================================
-- reviews  (fix #5: tied to a rental_agreement, not raw tenant/property.
-- One review per agreement => tenant must have actually rented it.
-- tenant/property/owner are derived — see v_reviews_full.)
-- =====================================================================
CREATE TABLE reviews (
    review_id       BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    agreement_id    BIGINT UNSIGNED NOT NULL UNIQUE,
    rating          TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    feedback        TEXT,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_agreement FOREIGN KEY (agreement_id) REFERENCES rental_agreements(agreement_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- review_replies  (fix #6: owner_id must match the actual property owner)
-- =====================================================================
CREATE TABLE review_replies (
    reply_id        BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    review_id       BIGINT UNSIGNED NOT NULL UNIQUE,
    owner_id        BIGINT UNSIGNED NOT NULL,
    reply_text      TEXT NOT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_replies_review FOREIGN KEY (review_id) REFERENCES reviews(review_id) ON DELETE CASCADE,
    CONSTRAINT fk_replies_owner FOREIGN KEY (owner_id) REFERENCES users(user_id)
) ENGINE=InnoDB;

DELIMITER $$
CREATE TRIGGER trg_replies_owner_match_ins
BEFORE INSERT ON review_replies
FOR EACH ROW
BEGIN
    DECLARE actual_owner BIGINT UNSIGNED;
    SELECT p.owner_id INTO actual_owner
    FROM reviews r
    JOIN rental_agreements ra ON ra.agreement_id = r.agreement_id
    JOIN properties p ON p.property_id = ra.property_id
    WHERE r.review_id = NEW.review_id;

    IF actual_owner IS NULL OR actual_owner <> NEW.owner_id THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'review_replies.owner_id must match the owner of the reviewed property';
    END IF;
END$$
CREATE TRIGGER trg_replies_owner_match_upd
BEFORE UPDATE ON review_replies
FOR EACH ROW
BEGIN
    DECLARE actual_owner BIGINT UNSIGNED;
    SELECT p.owner_id INTO actual_owner
    FROM reviews r
    JOIN rental_agreements ra ON ra.agreement_id = r.agreement_id
    JOIN properties p ON p.property_id = ra.property_id
    WHERE r.review_id = NEW.review_id;

    IF actual_owner IS NULL OR actual_owner <> NEW.owner_id THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'review_replies.owner_id must match the owner of the reviewed property';
    END IF;
END$$
DELIMITER ;

-- =====================================================================
-- messages
-- =====================================================================
CREATE TABLE messages (
    message_id      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sender_id       BIGINT UNSIGNED NOT NULL,
    receiver_id     BIGINT UNSIGNED NOT NULL,
    property_id     BIGINT UNSIGNED NULL,
    content         TEXT NOT NULL,
    is_read         BOOLEAN NOT NULL DEFAULT FALSE,
    sent_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_messages_sender FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_messages_receiver FOREIGN KEY (receiver_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_messages_property FOREIGN KEY (property_id) REFERENCES properties(property_id),
    CONSTRAINT chk_messages_distinct CHECK (sender_id <> receiver_id),
    INDEX idx_messages_sender (sender_id),
    INDEX idx_messages_receiver (receiver_id),
    INDEX idx_messages_thread (sender_id, receiver_id, sent_at)
) ENGINE=InnoDB;

-- =====================================================================
-- notifications  (polymorphic FK kept as-is — accepted trade-off, see review pt.9)
-- =====================================================================
CREATE TABLE notifications (
    notification_id     BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id             BIGINT UNSIGNED NOT NULL,
    type                ENUM('rental_request','request_approved','request_rejected',
                              'new_message','property_approved','new_review','visit_scheduled') NOT NULL,
    content             VARCHAR(500) NOT NULL,
    related_entity_type VARCHAR(50),
    related_entity_id   BIGINT UNSIGNED,
    is_read             BOOLEAN NOT NULL DEFAULT FALSE,
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_notifications_user (user_id, is_read)
) ENGINE=InnoDB;

-- =====================================================================
-- complaints  (fix #10: XOR — against a user OR a property, not both)
-- =====================================================================
CREATE TABLE complaints (
    complaint_id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    filed_by             BIGINT UNSIGNED NOT NULL,
    against_user_id      BIGINT UNSIGNED NULL,
    against_property_id  BIGINT UNSIGNED NULL,
    description          TEXT NOT NULL,
    status               ENUM('open','under_review','resolved','dismissed') NOT NULL DEFAULT 'open',
    resolved_by          BIGINT UNSIGNED NULL,
    created_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    resolved_at          TIMESTAMP NULL,
    CONSTRAINT fk_complaints_filer FOREIGN KEY (filed_by) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_complaints_user FOREIGN KEY (against_user_id) REFERENCES users(user_id),
    CONSTRAINT fk_complaints_property FOREIGN KEY (against_property_id) REFERENCES properties(property_id),
    CONSTRAINT fk_complaints_resolver FOREIGN KEY (resolved_by) REFERENCES users(user_id),
    CONSTRAINT chk_complaints_xor CHECK (
        (against_user_id IS NOT NULL AND against_property_id IS NULL)
        OR
        (against_user_id IS NULL AND against_property_id IS NOT NULL)
    ),
    INDEX idx_complaints_status (status)
) ENGINE=InnoDB;

-- =====================================================================
-- admin_actions
-- =====================================================================
CREATE TABLE admin_actions (
    action_id       BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id        BIGINT UNSIGNED NOT NULL,
    action_type     VARCHAR(50) NOT NULL,
    target_type     VARCHAR(50) NOT NULL,
    target_id       BIGINT UNSIGNED NOT NULL,
    notes           TEXT,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_admin_actions_admin FOREIGN KEY (admin_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_admin_actions_admin (admin_id),
    INDEX idx_admin_actions_target (target_type, target_id)
) ENGINE=InnoDB;

DELIMITER $$
CREATE TRIGGER trg_admin_actions_role_ins
BEFORE INSERT ON admin_actions
FOR EACH ROW
BEGIN
    IF (SELECT role FROM users WHERE user_id = NEW.admin_id) <> 'admin' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'admin_actions.admin_id must reference a user with role = admin';
    END IF;
END$$
DELIMITER ;

-- =====================================================================
-- Convenience views (replace the columns removed from rental_agreements
-- and reviews for normalization — join cost is trivial at project scale)
-- =====================================================================
CREATE VIEW v_rental_agreements_full AS
SELECT
    ra.agreement_id,
    ra.request_id,
    rq.property_id,
    rq.tenant_id,
    p.owner_id,
    ra.broker_id,
    ra.start_date,
    ra.end_date,
    ra.monthly_rent,
    ra.status,
    ra.confirmed_at
FROM rental_agreements ra
JOIN rental_requests rq ON rq.request_id = ra.request_id
JOIN properties p       ON p.property_id = rq.property_id;

CREATE VIEW v_reviews_full AS
SELECT
    rv.review_id,
    rv.agreement_id,
    af.property_id,
    af.tenant_id,
    af.owner_id,
    rv.rating,
    rv.feedback,
    rv.created_at
FROM reviews rv
JOIN v_rental_agreements_full af ON af.agreement_id = rv.agreement_id;
