<?php
/**
 * RequestDocument.php — resident-portal page to request a barangay document.
 * The form is filled in and submitted right here (no public page), pre-filled
 * from the resident's profile. Posts to backend/actions/services_submit.php,
 * which generates the PDF and returns to MyRequests.php.
 */
require __DIR__ . '/../partials/bootstrap.php';
require_role(['resident']);          // guests are sent to Login by the guard

$pdo   = db();
$email = current_username();

$certs = $pdo->query('SELECT id, certificate_name, requirements FROM certificates ORDER BY certificate_name')->fetchAll();

$st = $pdo->prepare('SELECT * FROM profiledata WHERE email = ? LIMIT 1');
$st->execute([$email]);
$p = $st->fetch() ?: [];

$st = $pdo->prepare('SELECT address, barangay, city, province FROM importantinfo WHERE id = (SELECT id FROM profiledata WHERE email = ?) LIMIT 1');
$st->execute([$email]);
$info = $st->fetch() ?: [];
$fullAddress = trim(implode(' ', array_filter([
    $info['address'] ?? '', $info['barangay'] ?? '', $info['city'] ?? '', $info['province'] ?? '',
])));
$fullName = trim(($p['firstname'] ?? '') . ' ' . ($p['middlename'] ?? '') . ' ' . ($p['lastname'] ?? ''));
$age = !empty($p['birthdate']) ? (int) ((time() - strtotime((string) $p['birthdate'])) / 31556952) : '';

$preSelect = (int) ($_GET['cert'] ?? 0);

$page_title   = 'Request a Document';
$page_heading = 'Request a Document';
$page_subtitle = 'Fill in the details below. Your certificate is generated as a PDF.';
$active_nav   = 'request_new';
require __DIR__ . '/../partials/admin_top.php';
?>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-hd"><span class="card-hd__title"><i class="bi bi-file-earmark-plus"></i> New request</span></div>
      <form class="card-bd row g-3" method="post" action="<?= action_url('services_submit.php') ?>">
        <input type="hidden" name="_return" value="MyRequests.php">

        <div class="col-md-6">
          <label class="form-label">Document type</label>
          <select class="form-select" name="certificate_id" id="certSelect" required>
            <option value="">Select a certificate…</option>
            <?php foreach ($certs as $c): ?>
              <option value="<?= (int) $c['id'] ?>" <?= $preSelect === (int) $c['id'] ? 'selected' : '' ?>>
                <?= e($c['certificate_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Purpose</label>
          <input class="form-control" name="purpose" placeholder="e.g. employment, scholarship" required>
        </div>

        <div class="col-md-8"><label class="form-label">Full name</label>
          <input class="form-control" name="fullName" value="<?= e($fullName) ?>" required></div>
        <div class="col-md-4"><label class="form-label">Age</label>
          <input type="number" min="0" class="form-control" name="age" value="<?= e((string) $age) ?>"></div>

        <div class="col-md-6"><label class="form-label">Date of birth</label>
          <input type="date" class="form-control" name="dob" value="<?= e($p['birthdate'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Place of birth</label>
          <input class="form-control" name="placeOfBirth"></div>

        <div class="col-md-6"><label class="form-label">Civil status</label>
          <input class="form-control" name="civilStatus" value="<?= e($p['status'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Sex</label>
          <select class="form-select" name="sex">
            <option value="">—</option>
            <option <?= ($p['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
            <option <?= ($p['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
          </select></div>

        <div class="col-12"><label class="form-label">Address</label>
          <input class="form-control" name="address" value="<?= e($fullAddress) ?>"></div>

        <div class="col-12" id="bizRow" style="display:none">
          <label class="form-label">Business name <span class="text-caption">(for Business Clearance)</span></label>
          <input class="form-control" name="business">
        </div>

        <div class="col-12 d-flex justify-content-end gap-2">
          <a class="btn btn-outline-secondary" href="<?= page_url('MyRequests.php') ?>">Cancel</a>
          <button class="btn btn-primary"><i class="bi bi-send me-1"></i>Submit request</button>
        </div>
      </form>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card">
      <div class="card-hd"><span class="card-hd__title"><i class="bi bi-list-check"></i> Requirements</span></div>
      <div class="card-bd">
        <div id="reqBox" class="text-muted-2 small">Select a document type to see its requirements.</div>
      </div>
    </div>
  </div>
</div>

<?php
$reqMap = [];
foreach ($certs as $c) {
    $reqMap[(int) $c['id']] = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $c['requirements']))));
}
$foot_extra = '<script>const REQS = ' . json_encode($reqMap) . ';'
    . 'const sel=document.getElementById("certSelect"), box=document.getElementById("reqBox"), biz=document.getElementById("bizRow");'
    . 'function sync(){const id=sel.value, list=REQS[id]||[];'
    . 'box.innerHTML=list.length?("<ul class=\'ps-3 mb-0\'>"+list.map(x=>"<li>"+x.replace(/[<>&]/g,"")+"</li>").join("")+"</ul>"):"No specific requirements listed.";'
    . 'biz.style.display=(sel.options[sel.selectedIndex]||{}).text.toLowerCase().includes("business")?"block":"none";}'
    . 'sel.addEventListener("change",sync); sync();</script>';
require __DIR__ . '/../partials/admin_bottom.php';
