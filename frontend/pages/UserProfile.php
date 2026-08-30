<?php
/**
 * UserProfile.php — the signed-in user's own profile (view + edit).
 * Both forms post to backend/actions/update_profile.php (matched by email).
 */
require __DIR__ . '/../partials/bootstrap.php';
require_admin();

$pdo   = db();
$email = current_username();

$st = $pdo->prepare('SELECT * FROM profiledata WHERE email = ? LIMIT 1');
$st->execute([$email]);
$p = $st->fetch() ?: [];

$st = $pdo->prepare('SELECT * FROM importantinfo WHERE id = (SELECT id FROM profiledata WHERE email = ?) LIMIT 1');
$st->execute([$email]);
$info = $st->fetch() ?: [];

$st = $pdo->prepare('SELECT picture FROM proof_of_identity WHERE id = (SELECT id FROM profiledata WHERE email = ?) LIMIT 1');
$st->execute([$email]);
$pic = $st->fetchColumn() ?: '';
$avatar = $pic ? (str_contains((string) $pic, '/') ? UPLOAD_URL . '/' . ltrim((string) $pic, '/') : upload_url('profile_pic/' . $pic))
              : asset('images/logo1.png');

$fullName = trim(($p['firstname'] ?? '') . ' ' . ($p['middlename'] ?? '') . ' ' . ($p['lastname'] ?? '')) ?: 'Administrator';
$action   = action_url('update_profile.php');
$saved    = isset($_GET['saved']);

$page_title   = 'My Profile';
$page_heading = 'My Profile';
$active_nav   = '';

require __DIR__ . '/../partials/admin_top.php';
?>

<?php if ($saved): ?>
<div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill"></i> Profile updated.</div>
<?php endif; ?>

<div class="row g-4">
  <!-- Identity -->
  <div class="col-lg-4">
    <div class="card">
      <div class="card-bd text-center">
        <img src="<?= e($avatar) ?>" alt="" class="rounded-circle mb-3"
             style="width:104px;height:104px;object-fit:cover;border:3px solid var(--brand-100)"
             onerror="this.src='<?= asset('images/logo1.png') ?>'">
        <h3 class="h5 mb-1"><?= e($fullName) ?></h3>
        <div class="text-muted-2"><?= e($p['email'] ?? $email) ?></div>
        <div class="mt-3 d-flex flex-column gap-2 text-start">
          <div><i class="bi bi-telephone me-2 text-primary"></i><?= e($p['contact'] ?? '—') ?></div>
          <div><i class="bi bi-geo-alt me-2 text-primary"></i>
            <?= e(trim(($info['address'] ?? '') . ' ' . ($info['barangay'] ?? '') . ' ' . ($info['city'] ?? '') . ' ' . ($info['province'] ?? '')) ?: '—') ?>
          </div>
          <div><i class="bi bi-calendar me-2 text-primary"></i><?= e($p['birthdate'] ?? '—') ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <!-- Personal details -->
    <div class="card mb-4">
      <div class="card-hd"><span class="card-hd__title"><i class="bi bi-person-badge"></i> Personal Details</span></div>
      <form class="card-bd row g-3" method="post" action="<?= $action ?>">
        <div class="col-md-4"><label class="form-label">First name</label>
          <input class="form-control" name="firstname" value="<?= e($p['firstname'] ?? '') ?>" required></div>
        <div class="col-md-4"><label class="form-label">Middle name</label>
          <input class="form-control" name="middlename" value="<?= e($p['middlename'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Last name</label>
          <input class="form-control" name="lastname" value="<?= e($p['lastname'] ?? '') ?>" required></div>
        <div class="col-md-4"><label class="form-label">Birth date</label>
          <input type="date" class="form-control" name="birthdate" value="<?= e($p['birthdate'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Gender</label>
          <input class="form-control" name="gender" value="<?= e($p['gender'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Civil status</label>
          <input class="form-control" name="status" value="<?= e($p['status'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" value="<?= e($p['email'] ?? $email) ?>" required></div>
        <div class="col-md-3"><label class="form-label">Contact</label>
          <input class="form-control" name="contact" value="<?= e($p['contact'] ?? '') ?>"></div>
        <div class="col-md-3"><label class="form-label">Religion</label>
          <input class="form-control" name="religion" value="<?= e($p['religion'] ?? '') ?>"></div>
        <!-- carry the "other info" fields so the handler updates both tables -->
        <input type="hidden" name="occupation" value="<?= e($info['occupation'] ?? '') ?>">
        <input type="hidden" name="monthly_income" value="<?= e($info['monthly_income'] ?? '') ?>">
        <input type="hidden" name="allergies_conditions" value="<?= e($info['allergies_conditions'] ?? '') ?>">
        <input type="hidden" name="education" value="<?= e($info['education'] ?? '') ?>">
        <input type="hidden" name="emergency_person" value="<?= e($info['emergency_person'] ?? '') ?>">
        <input type="hidden" name="emergency_contact" value="<?= e($info['emergency_contact'] ?? '') ?>">
        <div class="col-12 text-end"><button class="btn btn-primary">Save details</button></div>
      </form>
    </div>

    <!-- Other information -->
    <div class="card">
      <div class="card-hd"><span class="card-hd__title"><i class="bi bi-clipboard-data"></i> Other Information</span></div>
      <form class="card-bd row g-3" method="post" action="<?= $action ?>">
        <div class="col-md-6"><label class="form-label">Occupation</label>
          <input class="form-control" name="occupation" value="<?= e($info['occupation'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Monthly income</label>
          <input class="form-control" name="monthly_income" value="<?= e($info['monthly_income'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Educational attainment</label>
          <input class="form-control" name="education" value="<?= e($info['education'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Allergies / medical conditions</label>
          <input class="form-control" name="allergies_conditions" value="<?= e($info['allergies_conditions'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Emergency contact person</label>
          <input class="form-control" name="emergency_person" value="<?= e($info['emergency_person'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Emergency contact number</label>
          <input class="form-control" name="emergency_contact" value="<?= e($info['emergency_contact'] ?? '') ?>"></div>
        <!-- carry personal fields so the handler still matches by email -->
        <input type="hidden" name="firstname" value="<?= e($p['firstname'] ?? '') ?>">
        <input type="hidden" name="middlename" value="<?= e($p['middlename'] ?? '') ?>">
        <input type="hidden" name="lastname" value="<?= e($p['lastname'] ?? '') ?>">
        <input type="hidden" name="birthdate" value="<?= e($p['birthdate'] ?? '') ?>">
        <input type="hidden" name="email" value="<?= e($p['email'] ?? $email) ?>">
        <input type="hidden" name="contact" value="<?= e($p['contact'] ?? '') ?>">
        <input type="hidden" name="gender" value="<?= e($p['gender'] ?? '') ?>">
        <input type="hidden" name="religion" value="<?= e($p['religion'] ?? '') ?>">
        <input type="hidden" name="status" value="<?= e($p['status'] ?? '') ?>">
        <div class="col-12 text-end"><button class="btn btn-primary">Save information</button></div>
      </form>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../partials/admin_bottom.php'; ?>
