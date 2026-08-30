<?php
/**
 * Activity.php — barangay activities / events (list + add + edit + delete).
 * Add  -> backend/actions/activity_insert.php   (multipart, photo)
 * Edit -> backend/actions/activity_update.php   (hidden activity_id, existing_photo)
 * Delete (AJAX GET) -> backend/actions/activity_delete.php?id=
 */
require __DIR__ . '/../partials/bootstrap.php';
require_admin();

$pdo = db();

$search  = trim($_GET['search'] ?? '');
$perPage = 9;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;
$like    = '%' . $search . '%';

$st = $pdo->prepare('SELECT COUNT(*) FROM activity WHERE activity LIKE ? OR description LIKE ?');
$st->execute([$like, $like]);
$total = (int) $st->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));

$st = $pdo->prepare(
    'SELECT * FROM activity WHERE activity LIKE ? OR description LIKE ?
      ORDER BY date DESC, id DESC LIMIT ? OFFSET ?'
);
$st->bindValue(1, $like); $st->bindValue(2, $like);
$st->bindValue(3, $perPage, PDO::PARAM_INT);
$st->bindValue(4, $offset, PDO::PARAM_INT);
$st->execute();
$rows = $st->fetchAll();

$page_title    = 'Activities';
$page_heading  = 'Barangay Activities';
$page_subtitle = $total . ' record' . ($total === 1 ? '' : 's');
$active_nav    = 'activity';
$page_actions  = '<button class="btn btn-primary" onclick="openActivity()"><i class="bi bi-plus-lg me-1"></i>Add Activity</button>';

require __DIR__ . '/../partials/admin_top.php';
?>

<div class="card">
    <form class="table-toolbar" method="get">
        <div class="field-search">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" name="search" value="<?= e($search) ?>" placeholder="Search activities…">
        </div>
        <?php if ($search): ?><a class="btn btn-outline-secondary" href="?">Clear</a><?php endif; ?>
    </form>
    <div class="card-bd">
        <?php if (!$rows): ?>
            <div class="empty"><i class="bi bi-calendar-x"></i><h3>No activities recorded</h3>
                <p><?= $search ? 'Try a different search.' : 'Add the first activity to get started.' ?></p></div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($rows as $r): ?>
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 hover-lift" data-row='<?= e(json_encode($r, JSON_HEX_APOS | JSON_HEX_QUOT)) ?>'>
                    <img src="<?= upload_url('activity_photos/' . ($r['photos'] ?? '')) ?>" alt=""
                         style="height:170px;object-fit:cover;border-radius:var(--radius) var(--radius) 0 0;background:var(--brand-50)"
                         onerror="this.src='<?= asset('images/logo1.png') ?>';this.style.objectFit='contain';this.style.padding='2rem'">
                    <div class="card-bd">
                        <div class="text-caption mb-1"><i class="bi bi-calendar-event me-1"></i><?= e($r['date'] ? date('F j, Y', strtotime((string) $r['date'])) : 'No date') ?></div>
                        <h3 class="h6 mb-1"><?= e($r['activity']) ?></h3>
                        <p class="small text-muted-2 mb-3" style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden">
                            <?= e($r['description']) ?>
                        </p>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary" onclick="editActivity(this)"><i class="bi bi-pencil me-1"></i>Edit</button>
                            <button class="btn btn-sm btn-outline-secondary text-danger"
                                    onclick="deleteActivity(<?= (int) $r['id'] ?>, '<?= e($r['activity']) ?>')"><i class="bi bi-trash me-1"></i>Delete</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <?= render_pager($page, $pages, $total, $qs ?? ['search' => $search], $perPage) ?>
</div>

<div class="modal fade" id="activityModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="activityForm" method="POST" enctype="multipart/form-data" action="<?= action_url('activity_insert.php') ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="activityModalTitle">Add Activity</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="activity_id" id="af_id">
          <input type="hidden" name="existing_photo" id="af_existing_photo">
          <div class="mb-3">
            <label class="form-label">Photo</label>
            <input type="file" class="form-control" name="photo" accept="image/*">
            <div class="form-text" id="af_photoNote"></div>
          </div>
          <div class="row g-3">
            <div class="col-md-5"><label class="form-label">Date</label><input type="date" class="form-control" name="date" id="af_date"></div>
            <div class="col-md-7"><label class="form-label">Activity name</label><input class="form-control" name="activity" id="af_activity" required></div>
            <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" rows="4" name="description" id="af_description"></textarea></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="activitySubmit">Save activity</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
$insertUrl = action_url('activity_insert.php');
$updateUrl = action_url('activity_update.php');
$deleteUrl = action_url('activity_delete.php');
$foot_extra = <<<HTML
<script>
const activityModal = new bootstrap.Modal('#activityModal');
const aForm = document.getElementById('activityForm');
function openActivity() {
    aForm.reset(); aForm.action = '{$insertUrl}';
    document.getElementById('af_id').value = '';
    document.getElementById('af_existing_photo').value = '';
    document.getElementById('af_photoNote').textContent = '';
    document.getElementById('activityModalTitle').textContent = 'Add Activity';
    document.getElementById('activitySubmit').textContent = 'Save activity';
    activityModal.show();
}
function editActivity(btn) {
    const d = JSON.parse(btn.closest('.card').dataset.row);
    aForm.reset(); aForm.action = '{$updateUrl}';
    document.getElementById('af_id').value = d.id;
    document.getElementById('af_existing_photo').value = d.photos ?? '';
    document.getElementById('af_date').value = d.date ?? '';
    document.getElementById('af_activity').value = d.activity ?? '';
    document.getElementById('af_description').value = d.description ?? '';
    document.getElementById('af_photoNote').textContent = d.photos ? 'Leave empty to keep the current photo.' : '';
    document.getElementById('activityModalTitle').textContent = 'Edit Activity';
    document.getElementById('activitySubmit').textContent = 'Update activity';
    activityModal.show();
}
function deleteActivity(id, name) {
    Swal.fire({ icon:'warning', title:'Delete activity?',
        html:'Remove <b>' + name + '</b>?',
        showCancelButton:true, confirmButtonText:'Delete', confirmButtonColor:'#c0392b', reverseButtons:true
    }).then(r => { if (r.isConfirmed) location.href = '{$deleteUrl}?id=' + id; });
}
</script>
HTML;
require __DIR__ . '/../partials/admin_bottom.php';
