<?php
/** Personal Data.php — step 1 of profile setup. Posts to personal_data_insert.php. */
require __DIR__ . '/../partials/bootstrap.php';

$page_title = 'Personal data';
$step       = 1;
$step_title = 'Personal data';
$step_hint  = 'Tell us about yourself. All fields are required.';
require __DIR__ . '/../partials/onboarding_top.php';
?>

<form method="POST" action="<?= action_url('personal_data_insert.php') ?>" class="row g-3">
    <div class="col-md-4"><label class="form-label">First name</label><input class="form-control" name="firstname" required></div>
    <div class="col-md-4"><label class="form-label">Middle name</label><input class="form-control" name="middlename"></div>
    <div class="col-md-4"><label class="form-label">Last name</label><input class="form-control" name="lastname" required></div>

    <div class="col-md-4"><label class="form-label">Gender</label>
        <select class="form-select" name="gender" required>
            <option value="">Select…</option><option>Male</option><option>Female</option><option>Other</option>
        </select>
    </div>
    <div class="col-md-4"><label class="form-label">Birth date</label><input type="date" class="form-control" name="birthdate" required></div>
    <div class="col-md-4"><label class="form-label">Civil status</label>
        <select class="form-select" name="status" required>
            <option value="">Select…</option><option>Single</option><option>Married</option><option>Separated</option><option>Widowed</option>
        </select>
    </div>

    <div class="col-md-6"><label class="form-label">Email address</label><input type="email" class="form-control" name="email" required></div>
    <div class="col-md-6"><label class="form-label">Contact number</label><input type="tel" class="form-control" name="contact" required></div>
    <div class="col-md-6"><label class="form-label">Religion</label><input class="form-control" name="religion" required></div>

    <div class="col-12"><hr></div>
    <div class="col-md-6"><label class="form-label">Emergency contact person</label><input class="form-control" name="emergency_name" required></div>
    <div class="col-md-6"><label class="form-label">Emergency contact number</label><input type="tel" class="form-control" name="emergency_contact" required></div>

    <div class="col-12 d-flex justify-content-end">
        <button class="btn btn-primary">Continue <i class="bi bi-arrow-right ms-1"></i></button>
    </div>
</form>

<?php require __DIR__ . '/../partials/onboarding_bottom.php'; ?>
