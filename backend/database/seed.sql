-- ============================================================================
--  Barangay Management System — Seed / sample data
-- ----------------------------------------------------------------------------
--  Run AFTER schema.sql. Safe to re-run: every INSERT uses a fixed id together
--  with "ON DUPLICATE KEY UPDATE", so importing twice will not create copies.
--
--  Default administrator login
--  ---------------------------------------------------------------------------
--    Username : admin@barangay.gov.ph
--    Password : Admin@123
--  ---------------------------------------------------------------------------
--  CHANGE THIS PASSWORD immediately after the first login.
-- ============================================================================

SET NAMES utf8mb4;
USE `barangay_management_system`;


-- ----------------------------------------------------------------------------
--  Admin account. The password below is a bcrypt hash of "Admin@123"
--  produced with PHP password_hash(). The app verifies it with password_verify().
-- ----------------------------------------------------------------------------
INSERT INTO `users` (`id`, `fullName`, `userName`, `password`, `userType`) VALUES
    (1, 'System Administrator', 'admin@barangay.gov.ph',
     '$2y$10$8dwxAIpKnF.fwGXPb1EcM.kjkq82rPY4jBbWVayrU3Jp.Q8L4GSbS', 'admin')
ON DUPLICATE KEY UPDATE `fullName` = VALUES(`fullName`), `userType` = VALUES(`userType`);


-- ----------------------------------------------------------------------------
--  Profile rows for the admin account (the UserProfile page joins on e-mail)
-- ----------------------------------------------------------------------------
INSERT INTO `profiledata`
    (`id`, `firstname`, `middlename`, `lastname`, `gender`, `birthdate`, `email`, `contact`, `religion`, `status`, `emergency_person`, `emergency_contact`)
VALUES
    (1, 'System', '', 'Administrator', 'Male', '1990-01-01', 'admin@barangay.gov.ph', '09000000000', 'N/A', 'Single', 'N/A', '09000000000')
ON DUPLICATE KEY UPDATE `firstname` = VALUES(`firstname`);

INSERT INTO `importantinfo`
    (`id`, `address`, `barangay`, `city`, `province`, `occupation`, `monthly_income`, `number_of_years`, `number_household`, `allergies_conditions`, `education`, `emergency_person`, `emergency_contact`)
VALUES
    (1, 'Barangay Hall', 'Paule 1', 'Santo Tomas', 'Batangas', 'Administrator', 'N/A', '1', '1', 'None', 'College', 'N/A', '09000000000')
ON DUPLICATE KEY UPDATE `address` = VALUES(`address`);

INSERT INTO `proof_of_identity` (`id`, `picture`, `valid_id`) VALUES
    (1, 'logo1.png', 'logo1.png')
ON DUPLICATE KEY UPDATE `picture` = VALUES(`picture`);


-- ----------------------------------------------------------------------------
--  Public "General Information" content — one row per block
-- ----------------------------------------------------------------------------
INSERT INTO `introduction` (`id`, `paragraph`) VALUES
    (1, 'Welcome to Barangay Paule 1. This portal keeps the community connected with barangay services and information.')
ON DUPLICATE KEY UPDATE `paragraph` = VALUES(`paragraph`);

INSERT INTO `mission` (`id`, `paragraph`) VALUES
    (1, 'To deliver responsive, transparent and inclusive public service to every resident of Barangay Paule 1.')
ON DUPLICATE KEY UPDATE `paragraph` = VALUES(`paragraph`);

INSERT INTO `vision` (`id`, `paragraph`) VALUES
    (1, 'A progressive, safe and united barangay where every family enjoys a good quality of life.')
ON DUPLICATE KEY UPDATE `paragraph` = VALUES(`paragraph`);

INSERT INTO `history` (`id`, `context`) VALUES
    (1, 'Barangay Paule 1 was established to serve the growing community and has since grown into an active local government unit.')
ON DUPLICATE KEY UPDATE `context` = VALUES(`context`);

INSERT INTO `map_statics` (`id`, `total_land_area`, `land_used`) VALUES
    (1, '250 ha', '180 ha')
ON DUPLICATE KEY UPDATE `total_land_area` = VALUES(`total_land_area`);

INSERT INTO `statistics` (`id`, `founding_years`, `environmental_health_status`, `partnerships_organization`, `projects_made`) VALUES
    (1, '1975', 'Good', '5', '12')
ON DUPLICATE KEY UPDATE `founding_years` = VALUES(`founding_years`);

INSERT INTO `population` (`id`, `number_of_population`, `average_household_size`) VALUES
    (1, '5000', '4')
ON DUPLICATE KEY UPDATE `number_of_population` = VALUES(`number_of_population`);

INSERT INTO `economics` (`id`, `message`) VALUES
    (1, 'Agriculture and small trade drive the local economy.'),
    (2, 'A growing number of residents are employed in nearby industrial estates.')
ON DUPLICATE KEY UPDATE `message` = VALUES(`message`);

INSERT INTO `major_business` (`id`, `business_text`) VALUES
    (1, 'Sari-sari stores'),
    (2, 'Poultry and livestock farms')
ON DUPLICATE KEY UPDATE `business_text` = VALUES(`business_text`);

INSERT INTO `major_income` (`id`, `income_text`) VALUES
    (1, 'Farming'),
    (2, 'Employment in nearby cities')
ON DUPLICATE KEY UPDATE `income_text` = VALUES(`income_text`);


-- ----------------------------------------------------------------------------
--  Contact block shown on the public site (pages read ids 1..3 explicitly)
-- ----------------------------------------------------------------------------
INSERT INTO `contacts` (`id`, `label`, `description`, `contacts`) VALUES
    (1, 'Barangay Hall',   'Main barangay office',        '(043) 000-0000'),
    (2, 'Emergency / BPSO', 'Peace and order response',   '0917-000-0000'),
    (3, 'Health Center',   'Barangay health station',     '0918-000-0000')
ON DUPLICATE KEY UPDATE `label` = VALUES(`label`), `description` = VALUES(`description`), `contacts` = VALUES(`contacts`);


-- ----------------------------------------------------------------------------
--  A few FAQs
-- ----------------------------------------------------------------------------
INSERT INTO `faq` (`id`, `question`, `answer`, `date`) VALUES
    (1, 'How do I request a Barangay Clearance?', 'Log in to the resident portal, open Document Requests, choose "Barangay Clearance" and fill out the form.', CURDATE()),
    (2, 'What are the office hours?', 'Monday to Friday, 8:00 AM to 5:00 PM.', CURDATE())
ON DUPLICATE KEY UPDATE `question` = VALUES(`question`), `answer` = VALUES(`answer`);


-- ----------------------------------------------------------------------------
--  Certificate templates that can be requested. The `file` column must point
--  at a real PDF in /upload/uploads for PDF generation to work; the sample
--  rows below reuse the clearance PDFs already shipped in /request_pdf.
-- ----------------------------------------------------------------------------
INSERT INTO `certificates` (`id`, `certificate_name`, `requirements`, `file`) VALUES
    (1, 'Barangay Clearance',        'Valid ID, Cedula',                'Barangay_Clearance.pdf'),
    (2, 'Business Clearance',         'DTI registration, Valid ID',      'Business_Clearance.pdf'),
    (3, 'Certificate of Residency',   'Valid ID, proof of address',      'Certificate_of_Residency.pdf'),
    (4, 'Certificate of Indigency',   'Valid ID',                        'Certificate_of_Indigency.pdf')
ON DUPLICATE KEY UPDATE `certificate_name` = VALUES(`certificate_name`), `requirements` = VALUES(`requirements`);


-- ----------------------------------------------------------------------------
--  Sample operational data so the dashboard counters are not all zero
-- ----------------------------------------------------------------------------
INSERT INTO `barangay_officials` (`id`, `position`, `photo`, `fullName`, `contact`, `address`, `startOfTerm`, `endOfTerm`) VALUES
    (1, 'Punong Barangay', 'logo1.png', 'Juan Dela Cruz',  '0917-111-1111', 'Purok 1, Paule 1', '2023', '2026'),
    (2, 'Barangay Kagawad', 'logo1.png', 'Maria Santos',    '0917-222-2222', 'Purok 2, Paule 1', '2023', '2026')
ON DUPLICATE KEY UPDATE `fullName` = VALUES(`fullName`);

INSERT INTO `residents`
    (`id`, `photo`, `full_name`, `birth_date`, `birth_place`, `age`, `total_households`, `contact`, `blood_type`, `civil_status`, `occupation`, `monthly_income`, `household`, `length_of_stay`, `religion`, `nationality`, `gender`, `education`)
VALUES
    (1, 'logo1.png', 'Pedro Reyes', '1985-05-12', 'Batangas', 39, 1, '0917-333-3333', 'O+', 'Married', 'Farmer', '10000', 'Owner', '39', 'Catholic', 'Filipino', 'Male', 'High School'),
    (2, 'logo1.png', 'Ana Lopez',   '1995-09-30', 'Batangas', 29, 1, '0917-444-4444', 'A+', 'Single',  'Teacher', '20000', 'Renter', '10', 'Catholic', 'Filipino', 'Female', 'College')
ON DUPLICATE KEY UPDATE `full_name` = VALUES(`full_name`);

INSERT INTO `activity` (`id`, `photos`, `date`, `activity`, `description`) VALUES
    (1, 'assembly.jpeg', CURDATE(), 'Barangay Assembly', 'Quarterly general assembly of residents.')
ON DUPLICATE KEY UPDATE `activity` = VALUES(`activity`);
