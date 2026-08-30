<?php
/**
 * BarangayOfficial.php — elected / appointed officials (list + add + edit + delete).
 * Add  -> backend/actions/barangay_official_insert.php   (multipart, photo)
 * Edit -> backend/actions/barangay_official_update.php   (hidden official_id)
 * Delete (AJAX GET) -> backend/actions/barangay_official_delete.php?id=
 */
require __DIR__ . '/../partials/bootstrap.php';
require_admin();

$pdo = db();

$search  = trim($_GET['search'] ?? '');
$perPage = 12;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;
$like    = '%' . $search . '%';

$st = $pdo->prepare('SELECT COUNT(*) FROM barangay_officials WHERE fullName LIKE ? OR position LIKE ?');
$st->execute([$like, $like]);
$total = (int) $st->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));

$st = $pdo->prepare(
    'SELECT * FROM barangay_officials WHERE fullName LIKE ? OR position LIKE ?
      ORDER BY id LIMIT ? OFFSET ?'
);
$st->bindValue(1, $like); $st->bindValue(2, $like);
$st->bindValue(3, $perPage, PDO::PARAM_INT);
$st->bindValue(4, $offset, PDO::PARAM_INT);
$st->execute();
$rows = $st->fetchAll();

$positions = [
    'Punong Barangay', 'Barangay Kagawad', 'SK Chairperson', 'SK Kagawad',
    'Barangay Secretary', 'Barangay Treasurer', 'Barangay Health Worker',
    'Barangay Tanod', 'Lupong Tagapamayapa',
];

$page_title    = 'Barangay Officials';
$page_heading  = 'Barangay Officials';
$page_subtitle = $total . ' official' . ($total === 1 ? '' : 's');
$active_nav    = 'officials';
$page_actions  = '<button class="btn btn-primary" onclick="openOfficial()"><i class="bi bi-plus-lg me-1"></i>Add Official</button>';

require __DIR__ . '/../partials/admin_top.php';
?>

<div class="card">
    <form class="table-toolbar" method="get">
        <div class="field-search">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" name="search" value="<?= e($search) ?>" placeholder="Search name or position…">
        </div>
        <?php if ($search): ?><a class="btn btn-outline-secondary" href="?">Clear</a><?php endif; ?>
        <span class="spacer"></span>
        <span class="text-caption"><?= $total ?> result<?= $total === 1 ? '' : 's' ?></span>
    </form>

    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>Official</th><th>Position</th><th>Contact</th><th>Term</th><th class="col-actions">Actions</th></tr></thead>
            <tbody>
            <?php if (!$rows): ?>
                <tr><td colspan="5"><div class="empty"><i class="bi bi-people"></i><h3>No officials yet</h3></div></td></tr>
            <?php else: foreach ($rows as $r): ?>
                <tr data-row='<?= e(json_encode($r, JSON_HEX_APOS | JSON_HEX_QUOT)) ?>'>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img class="thumb" src="<?= upload_url('photos/' . ($r['photo'] ?? '')) ?>" alt=""
                                 onerror="this.src='<?= asset('images/logo1.png') ?>'">
                            <div>
                                <div class="fw-semibold"><?= e($r['fullName']) ?></div>
                                <div class="text-caption"><?= e($r['address'] ?: '—') ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?= e($r['position']) ?></td>
                    <td><?= e($r['contact'] ?: '—') ?></td>
                    <td><?= e(($r['startOfTerm'] ?: '—') . ' – ' . ($r['endOfTerm'] ?: '—')) ?></td>
                    <td class="col-actions">
                        <button class="btn btn-sm btn-light btn-icon" title="Edit" onclick="editOfficial(this)"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-light btn-icon text-danger" title="Delete"
                                onclick="deleteOfficial(<?= (int) $r['id'] ?>, '<?= e($r['fullName']) ?>')"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pages > 1): ?>
    <div class="card-ft pager">
        <span class="pager__info">Page <?= $page ?> of <?= $pages ?> · <?= $total ?> total</span>
        <a class="btn btn-sm btn-outline-secondary <?= $page <= 1 ? 'disabled' : '' ?>" href="?<?= http_build_query(['search' => $search, 'page' => $page - 1]) ?>">Previous</a>
        <a class="btn btn-sm btn-outline-secondary <?= $page >= $pages ? 'disabled' : '' ?>" href="?<?= http_build_query(['search' => $search, 'page' => $page + 1]) ?>">Next</a>
    </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="officialModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="officialForm" method="POST" enctype="multipart/form-data" action="<?= action_url('barangay_official_insert.php') ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="officialModalTitle">Add Official</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="official_id" id="of_id">
          <div class="mb-3">
            <label class="form-label">Photo</label>
            <input type="file" class="form-control" name="barangayPhoto" accept="image/*">
          </div>
          <div class="mb-3">
            <label class="form-label">Position</label>
            <select class="form-select" name="position" id="of_position">
              <?php foreach ($positions as $p): ?><option><?= $p ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Full name</label>
            <input class="form-control" name="barangayFullName" id="of_fullName" required>
          </div>
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Contact</label><input class="form-control" name="barangayContact" id="of_contact"></div>
            <div class="col-md-6"><label class="form-label">Address</label><input class="form-control" name="barangayResident" id="of_address"></div>
            <div class="col-md-6"><label class="form-label">Start of term</label><input type="date" class="form-control" name="StartofTerm" id="of_start"></div>
            <div class="col-md-6"><label class="form-label">End of term</label><input type="date" class="form-control" name="EndofTerm" id="of_end"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="officialSubmit">Save official</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
$insertUrl = action_url('barangay_official_insert.php');
$updateUrl = action_url('barangay_official_update.php');
$deleteUrl = action_url('barangay_official_delete.php');
$foot_extra = <<<HTML
<script>
const officialModal = new bootstrap.Modal('#officialModal');
const oForm = document.getElementById('officialForm');
function openOfficial() {
    oForm.reset(); oForm.action = '{$insertUrl}';
    document.getElementById('of_id').value = '';
    document.getElementById('officialModalTitle').textContent = 'Add Official';
    document.getElementById('officialSubmit').textContent = 'Save official';
    officialModal.show();
}
function editOfficial(btn) {
    const d = JSON.parse(btn.closest('tr').dataset.row);
    oForm.reset(); oForm.action = '{$updateUrl}';
    document.getElementById('of_id').value = d.id;
    document.getElementById('of_position').value = d.position ?? '';
    document.getElementById('of_fullName').value = d.fullName ?? '';
    document.getElementById('of_contact').value  = d.contact ?? '';
    document.getElementById('of_address').value  = d.address ?? '';
    document.getElementById('of_start').value    = d.startOfTerm ?? '';
    document.getElementById('of_end').value      = d.endOfTerm ?? '';
    document.getElementById('officialModalTitle').textContent = 'Edit Official';
    document.getElementById('officialSubmit').textContent = 'Update official';
    officialModal.show();
}
function deleteOfficial(id, name) {
    Swal.fire({ icon:'warning', title:'Delete official?',
        html:'Remove <b>' + name + '</b> from the officials list?',
        showCancelButton:true, confirmButtonText:'Delete', confirmButtonColor:'#c0392b', reverseButtons:true
    }).then(r => { if (r.isConfirmed) location.href = '{$deleteUrl}?id=' + id; });
}
</script>
HTML;
require __DIR__ . '/../partials/admin_bottom.php';
