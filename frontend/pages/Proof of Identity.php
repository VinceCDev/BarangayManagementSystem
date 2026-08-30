<?php
/** Proof of Identity.php — step 3 of profile setup. Posts to proof_insert.php. */
require __DIR__ . '/../partials/bootstrap.php';

$page_title = 'Proof of identity';
$step       = 3;
$step_title = 'Proof of identity';
$step_hint  = 'Upload a 2×2 photo and a valid ID. Accepted formats: JPG or PNG.';
require __DIR__ . '/../partials/onboarding_top.php';
?>

<form method="post" action="<?= action_url('proof_insert.php') ?>" enctype="multipart/form-data" class="row g-4">
    <div class="col-md-6">
        <label class="form-label">2×2 photo</label>
        <input type="file" class="form-control" name="file1" accept="image/*" required>
        <div class="form-text">Recent, plain background.</div>
    </div>
    <div class="col-md-6">
        <label class="form-label">Valid ID</label>
        <input type="file" class="form-control" name="file2" accept="image/*" required>
        <div class="form-text">Government-issued Philippine ID.</div>
    </div>

    <div class="col-12 d-flex justify-content-between">
        <a class="btn btn-outline-secondary" href="<?= page_url('Other Info.php') ?>"><i class="bi bi-arrow-left me-1"></i>Back</a>
        <button class="btn btn-primary">Finish setup <i class="bi bi-check-lg ms-1"></i></button>
    </div>
</form>

<?php require __DIR__ . '/../partials/onboarding_bottom.php'; ?>
