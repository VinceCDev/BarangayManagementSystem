<?php
/** Other Info.php — step 2 of profile setup. Posts to important_info_insert.php. */
require __DIR__ . '/../partials/bootstrap.php';

$page_title = 'Other information';
$step       = 2;
$step_title = 'Other information';
$step_hint  = 'Address, work and household details.';
require __DIR__ . '/../partials/onboarding_top.php';
?>

<form method="POST" action="<?= action_url('important_info_insert.php') ?>" class="row g-3">
    <div class="col-md-6"><label class="form-label">House / unit no. and street</label><input class="form-control" name="address" required></div>
    <div class="col-md-6"><label class="form-label">Barangay</label><input class="form-control" name="barangay" value="Paule 1" required></div>
    <div class="col-md-6"><label class="form-label">City / municipality</label><input class="form-control" name="city" value="Rizal" required></div>
    <div class="col-md-6"><label class="form-label">Province</label><input class="form-control" name="province" value="Laguna" required></div>

    <div class="col-12"><hr></div>
    <div class="col-md-6"><label class="form-label">Occupation</label><input class="form-control" name="occupation" required></div>
    <div class="col-md-6"><label class="form-label">Monthly income</label><input type="number" min="0" class="form-control" name="monthly_income" required></div>
    <div class="col-md-6"><label class="form-label">Years of residency</label><input type="number" min="0" class="form-control" name="number_of_years" required></div>
    <div class="col-md-6"><label class="form-label">Members per household</label><input type="number" min="0" class="form-control" name="number_household" required></div>
    <div class="col-md-6"><label class="form-label">Educational attainment</label><input class="form-control" name="education" required></div>
    <div class="col-md-6"><label class="form-label">Allergies / medical conditions</label><input class="form-control" name="allergies_conditions" placeholder="None"></div>

    <div class="col-12 d-flex justify-content-between">
        <a class="btn btn-outline-secondary" href="<?= page_url('Personal Data.php') ?>"><i class="bi bi-arrow-left me-1"></i>Back</a>
        <button class="btn btn-primary">Continue <i class="bi bi-arrow-right ms-1"></i></button>
    </div>
</form>

<?php require __DIR__ . '/../partials/onboarding_bottom.php'; ?>
