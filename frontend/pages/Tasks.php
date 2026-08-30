<?php
/**
 * Tasks.php — work items assigned to officials / SK / treasurer.
 *   admin          -> sees & manages every task (create / edit / delete)
 *   other roles    -> see only their own tasks, may change the status
 *
 * Save   -> backend/actions/task_save.php
 * Delete -> backend/actions/task_delete.php?id=
 */
require __DIR__ . '/../partials/bootstrap.php';
require_role(['official', 'sk_chairman', 'treasurer']);

$pdo    = db();
$admin  = is_admin();
$me     = current_username();

$statuses   = ['Pending', 'In Progress', 'Done'];
$priorities = ['Low', 'Normal', 'High'];

/* ---- filters + pagination ------------------------------------------- */
$search  = trim($_GET['search'] ?? '');
$fStatus = $_GET['status'] ?? '';
$perPage = 10;
$page    = max(1, (int) ($_GET['page'] ?? 1));

$where  = ['(title LIKE ? OR description LIKE ? OR assignee_name LIKE ?)'];
$params = ['%' . $search . '%', '%' . $search . '%', '%' . $search . '%'];
if (!$admin) { $where[] = 'assignee_email = ?'; $params[] = $me; }
if (in_array($fStatus, $statuses, true)) { $where[] = 'status = ?'; $params[] = $fStatus; }
$whereSql = implode(' AND ', $where);

$st = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE $whereSql");
$st->execute($params);
$total = (int) $st->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));
$page  = min($page, $pages);
$offset = ($page - 1) * $perPage;

$st = $pdo->prepare(
    "SELECT * FROM tasks WHERE $whereSql
      ORDER BY FIELD(status,'In Progress','Pending','Done'), COALESCE(due_date,'9999-12-31'), id DESC
      LIMIT $perPage OFFSET $offset"
);
$st->execute($params);
$rows = $st->fetchAll();

$qs = ['search' => $search, 'status' => $fStatus];

/* ---- assignee options (admin only) -------------------------------- */
$assignees = [];
if ($admin) {
    $assignees = $pdo->query(
        "SELECT userName, fullName, userType FROM users WHERE LOWER(userType) <> 'resident' ORDER BY fullName"
    )->fetchAll();
}

/* ---- summary counters -------------------------------------------- */
$mineCount = (function () use ($pdo, $admin, $me) {
    $sql = "SELECT status, COUNT(*) c FROM tasks " . ($admin ? '' : 'WHERE assignee_email = ? ') . 'GROUP BY status';
    $st  = $pdo->prepare($sql);
    $st->execute($admin ? [] : [$me]);
    $out = ['Pending' => 0, 'In Progress' => 0, 'Done' => 0];
    foreach ($st as $r) { $out[$r['status']] = (int) $r['c']; }
    return $out;
})();

function task_pill(string $s): string
{
    $c = match ($s) { 'Done' => 'pill--success', 'In Progress' => 'pill--info', default => 'pill--warning' };
    return '<span class="pill ' . $c . '">' . e($s) . '</span>';
}
function prio_pill(string $p): string
{
    $c = match ($p) { 'High' => 'pill--danger', 'Low' => 'pill--muted', default => 'pill--info' };
    return '<span class="pill ' . $c . '">' . e($p) . '</span>';
}

$page_title    = 'Tasks & Activities';
$page_heading  = $admin ? 'Tasks & Activities' : 'My Tasks';
$page_subtitle = $total . ' task' . ($total === 1 ? '' : 's');
$active_nav    = 'tasks';
if ($admin) {
    $page_actions = '<button class="btn btn-primary" onclick="openTask()"><i class="bi bi-plus-lg me-1"></i>New Task</button>';
}

$saveUrl   = action_url('task_save.php');
$deleteUrl = action_url('task_delete.php');

require __DIR__ . '/../partials/admin_top.php';
?>

<div class="stat-grid" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr))">
    <div class="stat stat--gold"><span class="stat__icon"><i class="bi bi-hourglass-split"></i></span>
        <span><span class="stat__value"><?= $mineCount['Pending'] ?></span><span class="stat__label">Pending</span></span></div>
    <div class="stat"><span class="stat__icon"><i class="bi bi-arrow-repeat"></i></span>
        <span><span class="stat__value"><?= $mineCount['In Progress'] ?></span><span class="stat__label">In progress</span></span></div>
    <div class="stat stat--green"><span class="stat__icon"><i class="bi bi-check2-circle"></i></span>
        <span><span class="stat__value"><?= $mineCount['Done'] ?></span><span class="stat__label">Done</span></span></div>
</div>

<div class="card">
    <form class="table-toolbar" method="get">
        <div class="field-search">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" name="search" value="<?= e($search) ?>" placeholder="Search tasks…">
        </div>
        <span class="spacer"></span>
        <?= filter_select('status', $fStatus, ['' => 'All statuses'] + array_combine($statuses, $statuses)) ?>
        <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Apply</button>
        <?php if ($search || $fStatus): ?><a class="btn btn-sm btn-outline-secondary" href="?">Reset</a><?php endif; ?>
    </form>

    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Task</th><?php if ($admin): ?><th>Assigned to</th><?php endif; ?>
                    <th>Priority</th><th>Due</th><th>Status</th><th class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$rows): ?>
                <tr><td colspan="<?= $admin ? 6 : 5 ?>"><div class="empty">
                    <i class="bi bi-check2-square"></i><h3>No tasks</h3>
                    <p><?= $admin ? 'Create the first task with “New Task”.' : 'You have no tasks assigned right now.' ?></p>
                </div></td></tr>
            <?php else: foreach ($rows as $t):
                $view = ['Title' => $t['title'], 'Description' => $t['description'],
                         'Assigned to' => $t['assignee_name'] . ' (' . role_label($t['assignee_role']) . ')',
                         'Priority' => $t['priority'], 'Due date' => $t['due_date'] ?: '—',
                         'Status' => $t['status'], 'Created by' => $t['created_by'],
                         'Created' => $t['created_at']]; ?>
                <tr data-row='<?= e(json_encode($t, JSON_HEX_APOS | JSON_HEX_QUOT)) ?>'>
                    <td>
                        <div class="fw-semibold"><?= e($t['title']) ?></div>
                        <div class="text-caption text-truncate" style="max-width:24rem"><?= e($t['description']) ?></div>
                    </td>
                    <?php if ($admin): ?><td>
                        <div class="fw-semibold"><?= e($t['assignee_name'] ?: '—') ?></div>
                        <div class="text-caption"><?= e(role_label($t['assignee_role'])) ?></div>
                    </td><?php endif; ?>
                    <td><?= prio_pill((string) $t['priority']) ?></td>
                    <td><?= e($t['due_date'] ? date('M j, Y', strtotime((string) $t['due_date'])) : '—') ?></td>
                    <td><?= task_pill((string) $t['status']) ?></td>
                    <td class="col-actions">
                        <?= view_button($view, 'Task details') ?>
                        <?php if (!$admin && $t['status'] !== 'Done'): ?>
                            <button class="btn btn-sm btn-light btn-icon text-success" title="Advance status"
                                    onclick="advance(<?= (int) $t['id'] ?>, '<?= $t['status'] === 'Pending' ? 'In Progress' : 'Done' ?>')">
                                <i class="bi bi-arrow-right-circle"></i></button>
                        <?php endif; ?>
                        <?php if ($admin): ?>
                            <button class="btn btn-sm btn-light btn-icon" title="Edit" onclick="editTask(this)"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-light btn-icon text-danger" title="Delete"
                                    onclick="delTask(<?= (int) $t['id'] ?>, '<?= e($t['title']) ?>')"><i class="bi bi-trash"></i></button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?= render_pager($page, $pages, $total, $qs, $perPage) ?>
</div>

<?php if ($admin): ?>
<div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="taskForm" method="POST" action="<?= $saveUrl ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="taskModalTitle">New Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body row g-3">
          <input type="hidden" name="id" id="tf_id">
          <div class="col-12"><label class="form-label">Title</label>
            <input class="form-control" name="title" id="tf_title" required></div>
          <div class="col-12"><label class="form-label">Description</label>
            <textarea class="form-control" rows="3" name="description" id="tf_description"></textarea></div>
          <div class="col-12"><label class="form-label">Assign to</label>
            <select class="form-select" name="assignee_email" id="tf_assignee" required>
              <option value="">Select…</option>
              <?php foreach ($assignees as $a): ?>
                <option value="<?= e($a['userName']) ?>" data-name="<?= e($a['fullName']) ?>" data-role="<?= e($a['userType']) ?>">
                  <?= e($a['fullName']) ?> — <?= e(role_label(strtolower((string) $a['userType']))) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4"><label class="form-label">Priority</label>
            <select class="form-select" name="priority" id="tf_priority">
              <?php foreach ($priorities as $p): ?><option<?= $p === 'Normal' ? ' selected' : '' ?>><?= $p ?></option><?php endforeach; ?>
            </select></div>
          <div class="col-md-4"><label class="form-label">Status</label>
            <select class="form-select" name="status" id="tf_status">
              <?php foreach ($statuses as $s): ?><option><?= $s ?></option><?php endforeach; ?>
            </select></div>
          <div class="col-md-4"><label class="form-label">Due date</label>
            <input type="date" class="form-control" name="due_date" id="tf_due"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="taskSubmit">Save task</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<form id="statusForm" method="POST" action="<?= $saveUrl ?>" class="d-none">
    <input type="hidden" name="id" id="sf_id">
    <input type="hidden" name="status" id="sf_status">
    <input type="hidden" name="status_only" value="1">
</form>

<?php
$foot_extra = <<<HTML
<script>
function advance(id, next) {
    Swal.fire({ icon:'question', title:'Update status?', text:'Mark this task as "' + next + '"?',
        showCancelButton:true, confirmButtonText:'Update', reverseButtons:true })
      .then(r => { if (r.isConfirmed) {
          document.getElementById('sf_id').value = id;
          document.getElementById('sf_status').value = next;
          document.getElementById('statusForm').submit();
      }});
}
HTML;

if ($admin) {
    $foot_extra .= <<<HTML
const taskModal = new bootstrap.Modal('#taskModal');
const tForm = document.getElementById('taskForm');
function openTask() {
    tForm.reset(); document.getElementById('tf_id').value = '';
    document.getElementById('taskModalTitle').textContent = 'New Task';
    document.getElementById('taskSubmit').textContent = 'Save task';
    taskModal.show();
}
function editTask(btn) {
    const d = JSON.parse(btn.closest('tr').dataset.row);
    tForm.reset();
    document.getElementById('tf_id').value = d.id;
    document.getElementById('tf_title').value = d.title || '';
    document.getElementById('tf_description').value = d.description || '';
    document.getElementById('tf_assignee').value = d.assignee_email || '';
    document.getElementById('tf_priority').value = d.priority || 'Normal';
    document.getElementById('tf_status').value = d.status || 'Pending';
    document.getElementById('tf_due').value = d.due_date || '';
    document.getElementById('taskModalTitle').textContent = 'Edit Task';
    document.getElementById('taskSubmit').textContent = 'Update task';
    taskModal.show();
}
function delTask(id, title) {
    Swal.fire({ icon:'warning', title:'Delete task?', html:'Remove <b>' + title + '</b>?',
        showCancelButton:true, confirmButtonText:'Delete', confirmButtonColor:'#c0392b', reverseButtons:true })
      .then(r => { if (r.isConfirmed) location.href = '{$deleteUrl}?id=' + id; });
}
HTML;
}
$foot_extra .= "\n</script>";

require __DIR__ . '/../partials/admin_bottom.php';
