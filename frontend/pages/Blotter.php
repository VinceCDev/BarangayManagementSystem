<?php
/**
 * Blotter.php — incident / complaint blotter (list + add + edit + delete).
 * Add  -> backend/actions/blotter_insert.php
 * Edit -> backend/actions/blotter_update.php   (hidden blotter_id)
 * Delete (AJAX GET) -> backend/actions/blotter_delete.php?id=
 */
require __DIR__ . '/../partials/bootstrap.php';
require_admin();

$pdo = db();

$statuses = ['Pending', 'Ongoing', 'Resolved', 'Settled', 'Dismissed'];

$search  = trim($_GET['search'] ?? '');
$fStatus = $_GET['status'] ?? '';
$perPage = 10;
$page    = max(1, (int) ($_GET['page'] ?? 1));

$where  = ['(complainant LIKE ? OR personToComplaint LIKE ? OR status LIKE ?)'];
$params = ['%' . $search . '%', '%' . $search . '%', '%' . $search . '%'];
if (in_array($fStatus, $statuses, true)) { $where[] = 'status = ?'; $params[] = $fStatus; }
$whereSql = implode(' AND ', $where);

$st = $pdo->prepare("SELECT COUNT(*) FROM blotterrecords WHERE $whereSql");
$st->execute($params);
$total = (int) $st->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));
$page  = min($page, $pages);
$offset = ($page - 1) * $perPage;

$st = $pdo->prepare("SELECT * FROM blotterrecords WHERE $whereSql ORDER BY id DESC LIMIT $perPage OFFSET $offset");
$st->execute($params);
$rows = $st->fetchAll();

$qs = ['search' => $search, 'status' => $fStatus];

function blotter_pill(string $s): string
{
    $k = strtolower($s);
    $cls = match (true) {
        str_contains($k, 'resolve'), str_contains($k, 'settle') => 'pill--success',
        str_contains($k, 'ongoing'), str_contains($k, 'progress') => 'pill--info',
        str_contains($k, 'dismiss') => 'pill--muted',
        default => 'pill--warning',
    };
    return '<span class="pill ' . $cls . '">' . e($s ?: 'Pending') . '</span>';
}

$page_title    = 'Blotter';
$page_heading  = 'Blotter Records';
$page_subtitle = $total . ' record' . ($total === 1 ? '' : 's');
$active_nav    = 'blotter';
$page_actions  = '<button class="btn btn-primary" onclick="openBlotter()"><i class="bi bi-plus-lg me-1"></i>New Record</button>';

require __DIR__ . '/../partials/admin_top.php';
?>

<div class="card">
    <form class="table-toolbar" method="get">
        <div class="field-search">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" name="search" value="<?= e($search) ?>"
                   placeholder="Search complainant or respondent…">
        </div>
        <?= filter_select('status', $fStatus, ['' => 'All statuses'] + array_combine($statuses, $statuses)) ?>
        <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Apply</button>
        <?php if ($search || $fStatus): ?><a class="btn btn-sm btn-outline-secondary" href="?">Reset</a><?php endif; ?>
    </form>

    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr><th>Status</th><th>Complainant</th><th>Respondent</th><th>Action taken</th><th class="col-actions">Actions</th></tr>
            </thead>
            <tbody>
            <?php if (!$rows): ?>
                <tr><td colspan="5"><div class="empty">
                    <i class="bi bi-journal-x"></i><h3>No blotter records</h3>
                    <p><?= $search ? 'Try a different search.' : 'File the first record with “New Record”.' ?></p>
                </div></td></tr>
            <?php else: foreach ($rows as $r): ?>
                <tr data-row='<?= e(json_encode($r, JSON_HEX_APOS | JSON_HEX_QUOT)) ?>'>
                    <td><?= blotter_pill((string) $r['status']) ?></td>
                    <td>
                        <div class="fw-semibold"><?= e($r['complainant']) ?></div>
                        <div class="text-caption"><?= e($r['address1'] ?: '—') ?></div>
                    </td>
                    <td>
                        <div class="fw-semibold"><?= e($r['personToComplaint'] ?: '—') ?></div>
                        <div class="text-caption"><?= e($r['address2'] ?: '') ?></div>
                    </td>
                    <td class="text-truncate" style="max-width:22rem"><?= e($r['actionTaken'] ?: '—') ?></td>
                    <td class="col-actions">
                        <button class="btn btn-sm btn-light btn-icon" title="Edit" onclick="editBlotter(this)"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-light btn-icon text-danger" title="Delete"
                                onclick="deleteBlotter(<?= (int) $r['id'] ?>, '<?= e($r['complainant']) ?>')"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <?= render_pager($page, $pages, $total, $qs ?? ['search' => $search], $perPage) ?>
</div>

<div class="modal fade" id="blotterModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <form id="blotterForm" method="POST" action="<?= action_url('blotter_insert.php') ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="blotterModalTitle">New Blotter Record</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="blotter_id" id="bf_id">
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status" id="bf_status">
              <?php foreach ($statuses as $s): ?><option><?= $s ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="row g-3">
            <div class="col-12"><h6 class="text-muted-2 mb-0 mt-2">Complainant</h6></div>
            <div class="col-md-6"><label class="form-label">Full name</label><input class="form-control" name="complainant" id="bf_complainant" required></div>
            <div class="col-md-2"><label class="form-label">Age</label><input type="number" min="0" class="form-control" name="age1" id="bf_age1"></div>
            <div class="col-md-4"><label class="form-label">Contact</label><input class="form-control" name="contact1" id="bf_contact1"></div>
            <div class="col-12"><label class="form-label">Address</label><input class="form-control" name="address1" id="bf_address1"></div>

            <div class="col-12"><h6 class="text-muted-2 mb-0 mt-2">Respondent</h6></div>
            <div class="col-md-6"><label class="form-label">Full name</label><input class="form-control" name="personToComplaint" id="bf_personToComplaint"></div>
            <div class="col-md-2"><label class="form-label">Age</label><input type="number" min="0" class="form-control" name="age2" id="bf_age2"></div>
            <div class="col-md-4"><label class="form-label">Contact</label><input class="form-control" name="contact2" id="bf_contact2"></div>
            <div class="col-12"><label class="form-label">Address</label><input class="form-control" name="address2" id="bf_address2"></div>

            <div class="col-12">
              <label class="form-label">Action taken</label>
              <textarea class="form-control" rows="3" name="actionTaken" id="bf_actionTaken"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="blotterSubmit">Save record</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
$insertUrl = action_url('blotter_insert.php');
$updateUrl = action_url('blotter_update.php');
$deleteUrl = action_url('blotter_delete.php');
$foot_extra = <<<HTML
<script>
const blotterModal = new bootstrap.Modal('#blotterModal');
const bForm = document.getElementById('blotterForm');
const bFields = ['status','complainant','age1','contact1','address1',
                 'personToComplaint','age2','contact2','address2','actionTaken'];

function openBlotter() {
    bForm.reset(); bForm.action = '{$insertUrl}';
    document.getElementById('bf_id').value = '';
    document.getElementById('blotterModalTitle').textContent = 'New Blotter Record';
    document.getElementById('blotterSubmit').textContent = 'Save record';
    blotterModal.show();
}
function editBlotter(btn) {
    const d = JSON.parse(btn.closest('tr').dataset.row);
    bForm.reset(); bForm.action = '{$updateUrl}';
    document.getElementById('bf_id').value = d.id;
    bFields.forEach(f => { const el = document.getElementById('bf_' + f); if (el) el.value = d[f] ?? ''; });
    document.getElementById('blotterModalTitle').textContent = 'Edit Blotter Record';
    document.getElementById('blotterSubmit').textContent = 'Update record';
    blotterModal.show();
}
function deleteBlotter(id, name) {
    Swal.fire({ icon:'warning', title:'Delete record?',
        html:'Remove the blotter record for <b>' + name + '</b>?',
        showCancelButton:true, confirmButtonText:'Delete', confirmButtonColor:'#c0392b', reverseButtons:true
    }).then(r => { if (r.isConfirmed) location.href = '{$deleteUrl}?id=' + id; });
}
</script>
HTML;
require __DIR__ . '/../partials/admin_bottom.php';
