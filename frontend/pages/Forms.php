<?php
/**
 * Forms.php — certificate / clearance templates (list + add + edit + delete).
 * Add  -> backend/actions/forms_insert.php   (multipart: certificate, requirements, file)
 * Edit -> backend/actions/forms_update.php   (hidden forms_id)
 * Delete -> backend/actions/forms_delete.php (POST delete_id)
 */
require __DIR__ . '/../partials/bootstrap.php';
require_admin();

$pdo = db();

$search  = trim($_GET['search'] ?? '');
$perPage = 10;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;
$like    = '%' . $search . '%';

$st = $pdo->prepare('SELECT COUNT(*) FROM certificates WHERE certificate_name LIKE ?');
$st->execute([$like]);
$total = (int) $st->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));

$st = $pdo->prepare('SELECT * FROM certificates WHERE certificate_name LIKE ? ORDER BY certificate_name LIMIT ? OFFSET ?');
$st->bindValue(1, $like);
$st->bindValue(2, $perPage, PDO::PARAM_INT);
$st->bindValue(3, $offset, PDO::PARAM_INT);
$st->execute();
$rows = $st->fetchAll();

$page_title    = 'Certificates';
$page_heading  = 'Certificate Templates';
$page_subtitle = $total . ' template' . ($total === 1 ? '' : 's');
$active_nav    = 'forms';
$page_actions  = '<button class="btn btn-primary" onclick="openForm()"><i class="bi bi-plus-lg me-1"></i>Add Template</button>';

require __DIR__ . '/../partials/admin_top.php';
?>

<div class="card">
    <form class="table-toolbar" method="get">
        <div class="field-search">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" name="search" value="<?= e($search) ?>" placeholder="Search templates…">
        </div>
        <?php if ($search): ?><a class="btn btn-outline-secondary" href="?">Clear</a><?php endif; ?>
        <span class="spacer"></span>
        <span class="text-caption"><?= $total ?> result<?= $total === 1 ? '' : 's' ?></span>
    </form>

    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>Certificate</th><th>Requirements</th><th>Template file</th><th class="col-actions">Actions</th></tr></thead>
            <tbody>
            <?php if (!$rows): ?>
                <tr><td colspan="4"><div class="empty"><i class="bi bi-award"></i><h3>No templates yet</h3></div></td></tr>
            <?php else: foreach ($rows as $r): ?>
                <tr data-row='<?= e(json_encode($r, JSON_HEX_APOS | JSON_HEX_QUOT)) ?>'>
                    <td class="fw-semibold"><?= e($r['certificate_name']) ?></td>
                    <td>
                        <?php $reqs = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $r['requirements']))); ?>
                        <?php if ($reqs): ?>
                            <ul class="mb-0 ps-3 small text-muted-2"><?php foreach ($reqs as $q): ?><li><?= e($q) ?></li><?php endforeach; ?></ul>
                        <?php else: ?><span class="text-caption">—</span><?php endif; ?>
                    </td>
                    <td>
                        <?php if ($r['file']): ?>
                            <a class="pill pill--info" target="_blank" href="<?= upload_url('uploads/' . $r['file']) ?>">
                                <i class="bi bi-file-earmark-pdf"></i><?= e($r['file']) ?>
                            </a>
                        <?php else: ?><span class="text-caption">No file</span><?php endif; ?>
                    </td>
                    <td class="col-actions">
                        <button class="btn btn-sm btn-light btn-icon" title="Edit" onclick="editForm(this)"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-light btn-icon text-danger" title="Delete"
                                onclick="deleteForm(<?= (int) $r['id'] ?>, '<?= e($r['certificate_name']) ?>')"><i class="bi bi-trash"></i></button>
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

<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formForm" method="POST" enctype="multipart/form-data" action="<?= action_url('forms_insert.php') ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="formModalTitle">Add Certificate Template</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="forms_id" id="cf_id">
          <div class="mb-3">
            <label class="form-label">Certificate name</label>
            <input class="form-control" name="certificate" id="cf_name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Requirements <span class="text-caption">(one per line)</span></label>
            <textarea class="form-control" rows="4" name="requirements" id="cf_requirements"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Template file (PDF)</label>
            <input type="file" class="form-control" name="file" accept="application/pdf">
            <div class="form-text" id="cf_fileNote"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="formSubmit">Save template</button>
        </div>
      </form>
    </div>
  </div>
</div>

<form id="formDeleteForm" method="POST" action="<?= action_url('forms_delete.php') ?>" class="d-none">
    <input type="hidden" name="delete_id" id="formDeleteId">
</form>

<?php
$insertUrl = action_url('forms_insert.php');
$updateUrl = action_url('forms_update.php');
$foot_extra = <<<HTML
<script>
const formModal = new bootstrap.Modal('#formModal');
const cForm = document.getElementById('formForm');
function openForm() {
    cForm.reset(); cForm.action = '{$insertUrl}';
    document.getElementById('cf_id').value = '';
    document.getElementById('cf_fileNote').textContent = '';
    document.getElementById('formModalTitle').textContent = 'Add Certificate Template';
    document.getElementById('formSubmit').textContent = 'Save template';
    formModal.show();
}
function editForm(btn) {
    const d = JSON.parse(btn.closest('tr').dataset.row);
    cForm.reset(); cForm.action = '{$updateUrl}';
    document.getElementById('cf_id').value = d.id;
    document.getElementById('cf_name').value = d.certificate_name ?? '';
    document.getElementById('cf_requirements').value = d.requirements ?? '';
    document.getElementById('cf_fileNote').textContent = d.file ? 'Current: ' + d.file + ' (leave empty to keep)' : '';
    document.getElementById('formModalTitle').textContent = 'Edit Certificate Template';
    document.getElementById('formSubmit').textContent = 'Update template';
    formModal.show();
}
function deleteForm(id, name) {
    Swal.fire({ icon:'warning', title:'Delete template?',
        html:'Remove <b>' + name + '</b>? Existing requests keep their generated PDFs.',
        showCancelButton:true, confirmButtonText:'Delete', confirmButtonColor:'#c0392b', reverseButtons:true
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('formDeleteId').value = id;
            document.getElementById('formDeleteForm').submit();
        }
    });
}
</script>
HTML;
require __DIR__ . '/../partials/admin_bottom.php';
