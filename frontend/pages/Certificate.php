<?php
/**
 * Certificate.php — public certificate / clearance request page.
 * Choosing a certificate opens a request form that posts to
 * backend/actions/services_submit.php (which generates the PDF).
 */
require __DIR__ . '/../partials/bootstrap.php';

$certs = db()->query('SELECT id, certificate_name, requirements FROM certificates ORDER BY certificate_name')->fetchAll();

$page_title = 'Services';
$active     = 'services';
require __DIR__ . '/../partials/public_top.php';
?>

<header class="hero" style="min-height:34vh;--hero-img:linear-gradient(#0b2f6e,#123f92)">
    <div class="container">
        <p class="section__eyebrow text-white-50">Barangay Services</p>
        <h1>Request a certificate</h1>
        <p>Choose a document below and fill in your details. Your certificate is generated as a PDF.</p>
    </div>
</header>

<section class="section">
    <div class="container">
        <?php if (!$certs): ?>
            <p class="text-center text-muted-2">No certificate templates are available right now.</p>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($certs as $c):
                $reqs = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $c['requirements']))); ?>
            <div class="col-md-6 col-lg-4">
                <div class="info-card h-100 d-flex flex-column">
                    <div class="icon-circle"><i class="bi bi-award"></i></div>
                    <h3 class="h5"><?= e($c['certificate_name']) ?></h3>
                    <?php if ($reqs): ?>
                        <p class="text-caption mb-1">Requirements</p>
                        <ul class="text-muted-2 small ps-3">
                            <?php foreach ($reqs as $q): ?><li><?= e($q) ?></li><?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <button class="btn btn-primary mt-auto"
                            onclick='openRequest(<?= (int) $c["id"] ?>, <?= json_encode($c["certificate_name"]) ?>)'>
                        Request this document
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<div class="modal fade" id="reqModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <form method="post" action="<?= action_url('services_submit.php') ?>">
        <div class="modal-header">
          <h5 class="modal-title">Request: <span id="reqCertName"></span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body row g-3">
          <input type="hidden" name="certificate_id" id="reqCertId">
          <div class="col-md-8"><label class="form-label">Full name</label><input class="form-control" name="fullName" required></div>
          <div class="col-md-4"><label class="form-label">Age</label><input type="number" min="0" class="form-control" name="age"></div>
          <div class="col-md-6"><label class="form-label">Date of birth</label><input type="date" class="form-control" name="dob"></div>
          <div class="col-md-6"><label class="form-label">Place of birth</label><input class="form-control" name="placeOfBirth"></div>
          <div class="col-md-6"><label class="form-label">Civil status</label><input class="form-control" name="civilStatus"></div>
          <div class="col-md-6"><label class="form-label">Sex</label>
            <select class="form-select" name="sex"><option value="">—</option><option>Male</option><option>Female</option></select></div>
          <div class="col-12"><label class="form-label">Address</label><input class="form-control" name="address"></div>
          <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" name="email"></div>
          <div class="col-md-6"><label class="form-label">Business name <span class="text-caption">(for business clearance)</span></label>
            <input class="form-control" name="business"></div>
          <div class="col-12"><label class="form-label">Purpose</label><input class="form-control" name="purpose"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Submit request</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
$foot_extra = <<<'HTML'
<script>
const reqModal = new bootstrap.Modal('#reqModal');
function openRequest(id, name) {
    document.getElementById('reqCertId').value = id;
    document.getElementById('reqCertName').textContent = name;
    reqModal.show();
}
</script>
HTML;
require __DIR__ . '/../partials/public_bottom.php';
