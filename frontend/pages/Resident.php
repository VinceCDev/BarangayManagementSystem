<?php
/**
 * Resident.php — barangay residents master list (list + add + edit + delete).
 * Add  -> backend/actions/resident_insert.php
 * Edit -> backend/actions/resident_update.php   (hidden official_id = resident id)
 * Delete (AJAX GET) -> backend/actions/resident_delete.php?id=
 */
require __DIR__ . '/../partials/bootstrap.php';
require_admin();

$pdo = db();

/* ---- List query: paginated + optional name search (prepared) ---------- */
$search   = trim($_GET['search'] ?? '');
$perPage  = 8;
$page     = max(1, (int) ($_GET['page'] ?? 1));
$offset   = ($page - 1) * $perPage;
$like     = '%' . $search . '%';

$total = (function () use ($pdo, $like) {
    $st = $pdo->prepare('SELECT COUNT(*) FROM residents WHERE full_name LIKE ?');
    $st->execute([$like]);
    return (int) $st->fetchColumn();
})();
$pages = max(1, (int) ceil($total / $perPage));

$st = $pdo->prepare(
    'SELECT * FROM residents WHERE full_name LIKE ? ORDER BY full_name LIMIT ? OFFSET ?'
);
$st->bindValue(1, $like);
$st->bindValue(2, $perPage, PDO::PARAM_INT);
$st->bindValue(3, $offset, PDO::PARAM_INT);
$st->execute();
$residents = $st->fetchAll();

/* ---- Reference option lists ------------------------------------------- */
$bloodTypes  = ['O', 'A', 'B', 'AB'];
$civilStatus = ['Single', 'Married', 'Separated', 'Widowed'];
$genders     = ['Male', 'Female', 'Others'];
$education   = ['Elementary', 'High School', 'College', 'Vocational', 'Post Graduate'];

$page_title   = 'Residents';
$page_heading = 'Barangay Residents';
$page_subtitle = $total . ' registered resident' . ($total === 1 ? '' : 's');
$active_nav   = 'residents';
$page_actions = '<button class="btn btn-primary" onclick="openResident()"><i class="bi bi-plus-lg me-1"></i>Add Resident</button>';

require __DIR__ . '/../partials/admin_top.php';
?>

<div class="card">
    <form class="table-toolbar" method="get">
        <div class="field-search">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" name="search" value="<?= e($search) ?>"
                   placeholder="Search by name…">
        </div>
        <?php if ($search): ?>
            <a class="btn btn-outline-secondary" href="?">Clear</a>
        <?php endif; ?>
        <span class="spacer"></span>
        <span class="text-caption"><?= $total ?> result<?= $total === 1 ? '' : 's' ?></span>
    </form>

    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Resident</th><th>Age</th><th>Civil status</th>
                    <th>Occupation</th><th>Contact</th><th class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$residents): ?>
                <tr><td colspan="6">
                    <div class="empty">
                        <i class="bi bi-person-x"></i>
                        <h3>No residents found</h3>
                        <p><?= $search ? 'Try a different search term.' : 'Add the first resident to get started.' ?></p>
                    </div>
                </td></tr>
            <?php else: foreach ($residents as $r): ?>
                <tr data-resident='<?= e(json_encode($r, JSON_HEX_APOS | JSON_HEX_QUOT)) ?>'>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img class="thumb" src="<?= upload_url('resident_photo/' . ($r['photo'] ?? '')) ?>"
                                 alt="" onerror="this.src='<?= asset('images/logo1.png') ?>'">
                            <div>
                                <div class="fw-semibold"><?= e($r['full_name']) ?></div>
                                <div class="text-caption"><?= e($r['birth_place'] ?: '—') ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?= e((string) $r['age']) ?></td>
                    <td><?= e($r['civil_status'] ?: '—') ?></td>
                    <td><?= e($r['occupation'] ?: '—') ?></td>
                    <td><?= e($r['contact'] ?: '—') ?></td>
                    <td class="col-actions">
                        <button class="btn btn-sm btn-light btn-icon" title="Edit"
                                onclick="editResident(this)"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-light btn-icon text-danger" title="Delete"
                                onclick="deleteResident(<?= (int) $r['id'] ?>, '<?= e($r['full_name']) ?>')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pages > 1): ?>
    <div class="card-ft pager">
        <span class="pager__info">Page <?= $page ?> of <?= $pages ?> · <?= $total ?> total</span>
        <a class="btn btn-sm btn-outline-secondary <?= $page <= 1 ? 'disabled' : '' ?>"
           href="?<?= http_build_query(['search' => $search, 'page' => $page - 1]) ?>">Previous</a>
        <a class="btn btn-sm btn-outline-secondary <?= $page >= $pages ? 'disabled' : '' ?>"
           href="?<?= http_build_query(['search' => $search, 'page' => $page + 1]) ?>">Next</a>
    </div>
    <?php endif; ?>
</div>

<!-- Add / Edit modal ---------------------------------------------------- -->
<div class="modal fade" id="residentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <form id="residentForm" method="POST" enctype="multipart/form-data"
            action="<?= action_url('resident_insert.php') ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="residentModalTitle">Add Resident</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="official_id" id="rf_id">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Photo</label>
              <input type="file" class="form-control" name="photo" accept="image/*">
            </div>
            <div class="col-md-6">
              <label class="form-label">Full name</label>
              <input type="text" class="form-control" name="residentFullName" id="rf_full_name" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Birth date</label>
              <input type="date" class="form-control" name="residentBirthDate" id="rf_birth_date">
            </div>
            <div class="col-md-4">
              <label class="form-label">Birth place</label>
              <input type="text" class="form-control" name="residentBirthPlace" id="rf_birth_place">
            </div>
            <div class="col-md-4">
              <label class="form-label">Age</label>
              <input type="number" min="0" class="form-control" name="residentAge" id="rf_age">
            </div>
            <div class="col-md-4">
              <label class="form-label">Contact</label>
              <input type="tel" class="form-control" name="residentContact" id="rf_contact">
            </div>
            <div class="col-md-4">
              <label class="form-label">Blood type</label>
              <select class="form-select" name="residentBloodType" id="rf_blood_type">
                <option value="">—</option>
                <?php foreach ($bloodTypes as $o): ?><option><?= $o ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Civil status</label>
              <select class="form-select" name="residentCivilStatus" id="rf_civil_status">
                <option value="">—</option>
                <?php foreach ($civilStatus as $o): ?><option><?= $o ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Occupation</label>
              <input type="text" class="form-control" name="residentOccupation" id="rf_occupation">
            </div>
            <div class="col-md-6">
              <label class="form-label">Monthly income</label>
              <input type="number" min="0" class="form-control" name="residentMonthlyIncome" id="rf_monthly_income">
            </div>
            <div class="col-md-4">
              <label class="form-label">Total households</label>
              <input type="number" min="0" class="form-control" name="residentTotalHouseholds" id="rf_total_households">
            </div>
            <div class="col-md-4">
              <label class="form-label">Household per head</label>
              <input type="number" min="0" class="form-control" name="residentHousehold" id="rf_household">
            </div>
            <div class="col-md-4">
              <label class="form-label">Length of stay (years)</label>
              <input type="number" min="0" class="form-control" name="residentLengthOfStay" id="rf_length_of_stay">
            </div>
            <div class="col-md-4">
              <label class="form-label">Religion</label>
              <input type="text" class="form-control" name="residentReligion" id="rf_religion">
            </div>
            <div class="col-md-4">
              <label class="form-label">Nationality</label>
              <input type="text" class="form-control" name="residentNationality" id="rf_nationality" value="Filipino">
            </div>
            <div class="col-md-4">
              <label class="form-label">Gender</label>
              <select class="form-select" name="residentGender" id="rf_gender">
                <option value="">—</option>
                <?php foreach ($genders as $o): ?><option><?= $o ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Educational attainment</label>
              <select class="form-select" name="residentEducation" id="rf_education">
                <option value="">—</option>
                <?php foreach ($education as $o): ?><option><?= $o ?></option><?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="residentSubmit">Save resident</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
$insertUrl = action_url('resident_insert.php');
$updateUrl = action_url('resident_update.php');
$deleteUrl = action_url('resident_delete.php');
$foot_extra = <<<HTML
<script>
const residentModal = new bootstrap.Modal('#residentModal');
const rForm = document.getElementById('residentForm');
const rFields = ['full_name','birth_date','birth_place','age','contact','blood_type',
                 'civil_status','occupation','monthly_income','total_households','household',
                 'length_of_stay','religion','nationality','gender','education'];

function openResident() {
    rForm.reset();
    rForm.action = '{$insertUrl}';
    document.getElementById('rf_id').value = '';
    document.getElementById('residentModalTitle').textContent = 'Add Resident';
    document.getElementById('residentSubmit').textContent = 'Save resident';
    residentModal.show();
}

function editResident(btn) {
    const data = JSON.parse(btn.closest('tr').dataset.resident);
    rForm.reset();
    rForm.action = '{$updateUrl}';
    document.getElementById('rf_id').value = data.id;
    rFields.forEach(f => {
        const el = document.getElementById('rf_' + f);
        if (el) el.value = data[f] ?? '';
    });
    document.getElementById('residentModalTitle').textContent = 'Edit Resident';
    document.getElementById('residentSubmit').textContent = 'Update resident';
    residentModal.show();
}

function deleteResident(id, name) {
    Swal.fire({
        icon: 'warning', title: 'Delete resident?',
        html: 'This will permanently remove <b>' + name + '</b>.',
        showCancelButton: true, confirmButtonText: 'Delete',
        confirmButtonColor: '#c0392b', reverseButtons: true
    }).then(r => { if (r.isConfirmed) location.href = '{$deleteUrl}?id=' + id; });
}
</script>
HTML;
require __DIR__ . '/../partials/admin_bottom.php';
