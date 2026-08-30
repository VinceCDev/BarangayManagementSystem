-- ============================================================================
--  Barangay Management System — Database Schema
-- ----------------------------------------------------------------------------
--  Reconstructed from the application source code (the original .sql dump was
--  missing). Two databases are used by the application:
--
--    1. barangay_management_system  -> all business data (residents, users, ...)
--    2. file_management_system      -> the Document Management System (DMS) that
--                                      tracks every uploaded file / document
--
--  Character set : utf8mb4 (full Unicode, required for names / Filipino text)
--  Engine        : InnoDB     (transactions + foreign keys are used in code)
--
--  Import with:
--    mysql -u root -P 3307 < backend/database/schema.sql
--  or run backend/database/install.php
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;


-- ############################################################################
--  DATABASE 1 : barangay_management_system
-- ############################################################################
CREATE DATABASE IF NOT EXISTS `barangay_management_system`
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `barangay_management_system`;


-- ----------------------------------------------------------------------------
--  users — system accounts that can log into the admin dashboard
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `fullName`  VARCHAR(150)  NOT NULL,
    `userName`  VARCHAR(150)  NOT NULL COMMENT 'Login name; the app also treats this as the account e-mail',
    `password`  VARCHAR(255)  NOT NULL COMMENT 'Stored as a password_hash() bcrypt string',
    `userType`  VARCHAR(50)   NOT NULL DEFAULT 'staff' COMMENT 'e.g. admin / staff',
    `created_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_userName` (`userName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
--  residents — master list of barangay residents
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `residents` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `photo`            VARCHAR(255)  DEFAULT NULL COMMENT 'File name inside /upload/resident_photo',
    `full_name`        VARCHAR(200)  NOT NULL,
    `birth_date`       DATE          DEFAULT NULL,
    `birth_place`      VARCHAR(200)  DEFAULT NULL,
    `age`              INT           DEFAULT NULL,
    `total_households` INT           DEFAULT NULL,
    `contact`          VARCHAR(30)   DEFAULT NULL,
    `blood_type`       VARCHAR(10)   DEFAULT NULL,
    `civil_status`     VARCHAR(30)   DEFAULT NULL,
    `occupation`       VARCHAR(120)  DEFAULT NULL,
    `monthly_income`   VARCHAR(60)   DEFAULT NULL,
    `household`        VARCHAR(120)  DEFAULT NULL,
    `length_of_stay`   VARCHAR(60)   DEFAULT NULL,
    `religion`         VARCHAR(80)   DEFAULT NULL,
    `nationality`      VARCHAR(80)   DEFAULT NULL,
    `gender`           VARCHAR(20)   DEFAULT NULL,
    `education`        VARCHAR(120)  DEFAULT NULL,
    `created_at`       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_residents_full_name` (`full_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
--  barangay_officials — elected / appointed officials
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `barangay_officials` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `position`    VARCHAR(120)  NOT NULL,
    `photo`       VARCHAR(255)  DEFAULT NULL COMMENT 'File name inside /upload/photos',
    `fullName`    VARCHAR(200)  NOT NULL,
    `contact`     VARCHAR(30)   DEFAULT NULL,
    `address`     VARCHAR(255)  DEFAULT NULL,
    `startOfTerm` VARCHAR(20)   DEFAULT NULL COMMENT 'Stored as a date/year string by the UI',
    `endOfTerm`   VARCHAR(20)   DEFAULT NULL,
    `created_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_officials_fullName` (`fullName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
--  blotterrecords — incident / complaint blotter
--  (referenced in code as both `blotterrecords` and `BlotterRecords`;
--   MySQL on Windows is case-insensitive for table names so one table serves)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `blotterrecords` (
    `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `status`            VARCHAR(40)   NOT NULL DEFAULT 'Pending',
    `complainant`       VARCHAR(200)  NOT NULL,
    `age1`              VARCHAR(10)   DEFAULT NULL,
    `address1`          VARCHAR(255)  DEFAULT NULL,
    `contact1`          VARCHAR(30)   DEFAULT NULL,
    `personToComplaint` VARCHAR(200)  DEFAULT NULL,
    `age2`              VARCHAR(10)   DEFAULT NULL,
    `address2`          VARCHAR(255)  DEFAULT NULL,
    `contact2`          VARCHAR(30)   DEFAULT NULL,
    `actionTaken`       TEXT          DEFAULT NULL,
    `created_at`        TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
--  activity — barangay activities / events with a photo
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `activity` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `photos`      VARCHAR(255)  DEFAULT NULL COMMENT 'File name inside /upload/activity_photos',
    `date`        DATE          DEFAULT NULL,
    `activity`    VARCHAR(200)  NOT NULL,
    `description` TEXT          DEFAULT NULL,
    `created_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
--  faq — frequently asked questions shown on the public site
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `faq` (
    `id`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `question` VARCHAR(255) NOT NULL,
    `answer`   TEXT         NOT NULL,
    `date`     DATE         DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
--  certificates — certificate/clearance templates that can be requested
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `certificates` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `certificate_name` VARCHAR(150) NOT NULL,
    `requirements`     TEXT         DEFAULT NULL,
    `file`             VARCHAR(255) DEFAULT NULL COMMENT 'PDF template file name inside /upload/uploads',
    `created_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_certificates_name` (`certificate_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
--  contacts — barangay hotline / contact numbers block on the site
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contacts` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `label`       VARCHAR(120) NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `contacts`    VARCHAR(120) DEFAULT NULL COMMENT 'Phone / e-mail string',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
--  receivemessages — messages sent through the public "Contact" form
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `receivemessages` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(150) NOT NULL,
    `age`        INT          DEFAULT NULL,
    `email`      VARCHAR(190) NOT NULL,
    `contact`    VARCHAR(30)  DEFAULT NULL,
    `message`    TEXT         NOT NULL,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_messages_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
--  document_requests — certificate requests submitted by residents
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `document_requests` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `certificate_id` INT UNSIGNED NOT NULL,
    `fullName`       VARCHAR(200)  NOT NULL,
    `age`            INT           DEFAULT NULL,
    `purpose`        VARCHAR(255)  DEFAULT NULL,
    `address`        VARCHAR(255)  DEFAULT NULL,
    `dob`            DATE          DEFAULT NULL,
    `civilStatus`    VARCHAR(30)   DEFAULT NULL,
    `placeOfBirth`   VARCHAR(200)  DEFAULT NULL,
    `sex`            VARCHAR(20)   DEFAULT NULL,
    `email`          VARCHAR(190)  DEFAULT NULL,
    `business`       VARCHAR(200)  DEFAULT NULL,
    `request_date`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_docreq_certificate` (`certificate_id`),
    CONSTRAINT `fk_docreq_certificate` FOREIGN KEY (`certificate_id`)
        REFERENCES `certificates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
--  profiledata — the logged-in user's editable personal profile
--  (referenced as ProfileData / profiledata — same table)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `profiledata` (
    `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `firstname`         VARCHAR(100) NOT NULL,
    `middlename`        VARCHAR(100) DEFAULT NULL,
    `lastname`          VARCHAR(100) NOT NULL,
    `gender`            VARCHAR(20)  DEFAULT NULL,
    `birthdate`         DATE         DEFAULT NULL,
    `email`             VARCHAR(190) NOT NULL COMMENT 'Matches users.userName of the owner',
    `contact`           VARCHAR(30)  DEFAULT NULL,
    `religion`          VARCHAR(80)  DEFAULT NULL,
    `status`            VARCHAR(30)  DEFAULT NULL,
    `emergency_person`  VARCHAR(150) DEFAULT NULL,
    `emergency_contact` VARCHAR(30)  DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_profiledata_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
--  importantinfo — the "other info" half of the user's profile
--  (referenced as ImportantInfo / importantinfo — same table)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `importantinfo` (
    `id`                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `address`              VARCHAR(255) DEFAULT NULL,
    `barangay`             VARCHAR(120) DEFAULT NULL,
    `city`                 VARCHAR(120) DEFAULT NULL,
    `province`             VARCHAR(120) DEFAULT NULL,
    `occupation`           VARCHAR(120) DEFAULT NULL,
    `monthly_income`       VARCHAR(60)  DEFAULT NULL,
    `number_of_years`      VARCHAR(20)  DEFAULT NULL,
    `number_household`     VARCHAR(20)  DEFAULT NULL,
    `allergies_conditions` TEXT         DEFAULT NULL,
    `education`            VARCHAR(120) DEFAULT NULL,
    `emergency_person`     VARCHAR(150) DEFAULT NULL,
    `emergency_contact`    VARCHAR(30)  DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
--  proof_of_identity — the 2x2 photo + valid ID uploaded by a user
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `proof_of_identity` (
    `id`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `picture`  VARCHAR(255) DEFAULT NULL COMMENT 'File inside /upload/profile_pic',
    `valid_id` VARCHAR(255) DEFAULT NULL COMMENT 'File inside /upload/valid_id',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
--  tasks — work items assigned to officials / SK / treasurer
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tasks` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`          VARCHAR(200) NOT NULL,
    `description`    TEXT         DEFAULT NULL,
    `assignee_email` VARCHAR(190) DEFAULT NULL COMMENT 'users.userName of the person responsible',
    `assignee_name`  VARCHAR(190) DEFAULT NULL,
    `assignee_role`  VARCHAR(40)  DEFAULT NULL,
    `status`         VARCHAR(20)  NOT NULL DEFAULT 'Pending' COMMENT 'Pending / In Progress / Done',
    `priority`       VARCHAR(20)  NOT NULL DEFAULT 'Normal'  COMMENT 'Low / Normal / High',
    `due_date`       DATE         DEFAULT NULL,
    `created_by`     VARCHAR(190) DEFAULT NULL,
    `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_tasks_assignee` (`assignee_email`),
    KEY `idx_tasks_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------------------------------------------------------
--  Public "General Information" content blocks (one editable row each)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `introduction` (
    `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `paragraph` TEXT DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mission` (
    `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `paragraph` TEXT DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `vision` (
    `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `paragraph` TEXT DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `history` (
    `id`      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `context` TEXT DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `map_statics` (
    `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `total_land_area` VARCHAR(60) DEFAULT NULL,
    `land_used`       VARCHAR(60) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `statistics` (
    `id`                          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `founding_years`              VARCHAR(60)  DEFAULT NULL,
    `environmental_health_status` VARCHAR(255) DEFAULT NULL,
    `partnerships_organization`   VARCHAR(255) DEFAULT NULL,
    `projects_made`               VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `population` (
    `id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `number_of_population`    VARCHAR(60) DEFAULT NULL,
    `average_household_size`  VARCHAR(60) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `economics` (
    `id`      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `message` TEXT DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `major_business` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `business_text` TEXT DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `major_income` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `income_text` TEXT DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ############################################################################
--  DATABASE 2 : file_management_system  (the DMS)
--  Every table links a stored file to the record it belongs to and to the
--  user account that uploaded it.
-- ############################################################################
CREATE DATABASE IF NOT EXISTS `file_management_system`
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `file_management_system`;


-- resident_file — photos attached to a residents row
CREATE TABLE IF NOT EXISTS `resident_file` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `resident_name` VARCHAR(200)  NOT NULL,
    `photos`        VARCHAR(255)  DEFAULT NULL,
    `resident_id`   INT UNSIGNED  NOT NULL,
    `user_id`       INT UNSIGNED  DEFAULT NULL,
    `user_email`    VARCHAR(190)  DEFAULT NULL,
    `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_resident_file_resident` (`resident_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- document_folder — photos attached to an activity row
CREATE TABLE IF NOT EXISTS `document_folder` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `activity_id`   INT UNSIGNED  NOT NULL,
    `activity_name` VARCHAR(200)  NOT NULL,
    `photos`        VARCHAR(255)  DEFAULT NULL,
    `user_id`       INT UNSIGNED  DEFAULT NULL,
    `user_email`    VARCHAR(190)  DEFAULT NULL,
    `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_document_folder_activity` (`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- official_file — photos attached to a barangay_officials row
--  (column name `offcial_name` kept intentionally — the code depends on the typo)
CREATE TABLE IF NOT EXISTS `official_file` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `offcial_name` VARCHAR(200)  NOT NULL,
    `photos`       VARCHAR(255)  DEFAULT NULL,
    `official_id`  INT UNSIGNED  NOT NULL,
    `user_id`      INT UNSIGNED  DEFAULT NULL,
    `user_email`   VARCHAR(190)  DEFAULT NULL,
    `created_at`   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_official_file_official` (`official_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- form_file — certificate template files attached to a certificates row
CREATE TABLE IF NOT EXISTS `form_file` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `form_name`  VARCHAR(200)  NOT NULL,
    `form`       VARCHAR(255)  DEFAULT NULL,
    `form_id`    INT UNSIGNED  NOT NULL,
    `user_id`    INT UNSIGNED  DEFAULT NULL,
    `user_email` VARCHAR(190)  DEFAULT NULL,
    `created_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_form_file_form` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- profile_file — the 2x2 picture attached to a profiledata row
CREATE TABLE IF NOT EXISTS `profile_file` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `photos`     VARCHAR(255)  DEFAULT NULL,
    `profile_id` INT UNSIGNED  NOT NULL,
    `user_id`    INT UNSIGNED  DEFAULT NULL,
    `user_email` VARCHAR(190)  DEFAULT NULL,
    `created_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_profile_file_profile` (`profile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- request_file — generated certificate PDFs attached to a document_requests row
CREATE TABLE IF NOT EXISTS `request_file` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `request_id`       INT UNSIGNED  NOT NULL,
    `request_from`     VARCHAR(255)  DEFAULT NULL COMMENT 'Generated PDF file name',
    `person_requested` VARCHAR(200)  DEFAULT NULL,
    `person_email`     VARCHAR(190)  DEFAULT NULL,
    `created_date`     DATE          DEFAULT NULL,
    `created_at`       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_request_file_request` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


SET FOREIGN_KEY_CHECKS = 1;
-- ============================================================================
--  End of schema
-- ============================================================================
