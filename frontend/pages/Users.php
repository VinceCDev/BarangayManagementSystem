<?php
/**
 * Users.php — system accounts (list + add + edit + delete).
 * Add  -> backend/actions/users_insert.php
 * Edit -> backend/actions/user_update.php   (hidden users_id; blank password = keep)
 * Delete (AJAX GET) -> backend/actions/user_delete.php?id=
 *
 * The password hash is never displayed.
 */
require __DIR__ . '/../partials/bootstrap.php';
require_admin();

$pdo = db();

$types = ['admin' => 'Administrator', 'staff' => 'Staff'];

$search  = trim($_GET['search'] ?? '');
$fRole   = $_GET['role'] ?? '';
$perPage = 10;
$page    = max(1, (int) ($_GET['page'] ?? 1));

$where  = ['(fullName LIKE ? OR userName LIKE ?)'];
$params = ['%' . $search . '%', '%' . $search . '%'];
if (isset($types[$fRole])) { $where[] = 'userType = ?'; $params[] = $fRole; }
$whereSql = implode(' AND ', $where);

$st = $pdo->prepare("SELECT COUNT(*) FROM users WHERE $whereSql");
$st->execute($params);
$total = (int) $st->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));
$page  = min($page, $pages);
$offset = ($page - 1) * $perPage;

$st = $pdo->prepare("SELECT id, fullName, userName, userType FROM users WHERE $whereSql ORDER BY fullName LIMIT $perPage OFFSET $offset");
$st->execute($params);
$rows = $st->fetchAll();

$qs = ['search' => $search, 'role' => $fRole];

$page_title    = 'System Users';
$page_heading  = 'System Users';
$page_subtitle = $total . ' account' . ($total === 1 ? '' : 's');
$active_nav    = 'users';
$page_actions  = '<button class="btn btn-primary" onclick="openUser()"><i class="bi bi-plus-lg me-1"></i>Add User</button>';

require __DIR__ . '/../partials/admin_top.php';
?>

<div class="card">
    <form class="table-toolbar" method="get">
        <div class="field-search">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" name="search" value="<?= e($search) ?>" placeholder="Search name or username…">
        </div>
        <?= filter_select('role', $fRole, ['' => 'All roles'] + $types) ?>
        <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Apply</button>
        <?php if ($search || $fRole): ?><a class="btn btn-sm btn-outline-secondary" href="?">Reset</a><?php endif; ?>
        <span class="spacer"></span>
        <span class="text-caption"><?= $total ?> result<?= $total === 1 ? '' : 's' ?></span>
    </form>

    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>Name</th><th>Username</th><th>Role</th><th class="col-actions">Actions</th></tr></thead>
            <tbody>
            <?php if (!$rows): ?>
                <tr><td colspan="4"><div class="empty"><i class="bi bi-person-x"></i><h3>No users found</h3></div></td></tr>
            <?php else: foreach ($rows as $r): $roleKey = strtolower((string) $r['userType']); ?>
                <tr data-row='<?= e(json_encode($r, JSON_HEX_APOS | JSON_HEX_QUOT)) ?>'>
                    <td class="fw-semibold"><?= e($r['fullName']) ?></td>
                    <td><?= e($r['userName']) ?></td>
                    <td>
                        <span class="pill <?= $roleKey === 'admin' ? 'pill--info' : 'pill--muted' ?>">
                            <?= e($types[$roleKey] ?? ucfirst($roleKey ?: 'Staff')) ?>
                        </span>
                    </td>
                    <td class="col-actions">
                        <button class="btn btn-sm btn-light btn-icon" title="Edit" onclick="editUser(this)"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-light btn-icon text-danger" title="Delete"
                                onclick="deleteUser(<?= (int) $r['id'] ?>, '<?= e($r['fullName']) ?>')"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <?= render_pager($page, $pages, $total, $qs ?? ['search' => $search]) ?>
</div>

<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="userForm" method="POST" action="<?= action_url('users_insert.php') ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="userModalTitle">Add User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="users_id" id="uf_id">
          <div class="mb-3">
            <label class="form-label">Full name</label>
            <input class="form-control" name="userFullName" id="uf_fullName" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input class="form-control" name="userName" id="uf_userName" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select class="form-select" name="usertype" id="uf_userType">
              <?php foreach ($types as $k => $label): ?><option value="<?= $k ?>"><?= $label ?></option><?php endforeach; ?>
            </select>
          </div>
          <div id="uf_addPw" class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" id="uf_password">
          </div>
          <div id="uf_editPw" class="d-none">
            <div class="mb-3">
              <label class="form-label">New password <span class="text-caption">(leave blank to keep current)</span></label>
              <input type="password" class="form-control" name="newpassword" id="uf_newpassword">
            </div>
            <div class="mb-3">
              <label class="form-label">Confirm new password</label>
              <input type="password" class="form-control" name="confirmpassword" id="uf_confirmpassword">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="userSubmit">Save user</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
$insertUrl = action_url('users_insert.php');
$updateUrl = action_url('user_update.php');
$deleteUrl = action_url('user_delete.php');
$foot_extra = <<<HTML
<script>
const userModal = new bootstrap.Modal('#userModal');
const uForm = document.getElementById('userForm');

function openUser() {
    uForm.reset(); uForm.action = '{$insertUrl}';
    document.getElementById('uf_id').value = '';
    document.getElementById('uf_addPw').classList.remove('d-none');
    document.getElementById('uf_password').required = true;
    document.getElementById('uf_editPw').classList.add('d-none');
    document.getElementById('userModalTitle').textContent = 'Add User';
    document.getElementById('userSubmit').textContent = 'Save user';
    userModal.show();
}
function editUser(btn) {
    const d = JSON.parse(btn.closest('tr').dataset.row);
    uForm.reset(); uForm.action = '{$updateUrl}';
    document.getElementById('uf_id').value = d.id;
    document.getElementById('uf_fullName').value = d.fullName ?? '';
    document.getElementById('uf_userName').value = d.userName ?? '';
    document.getElementById('uf_userType').value = (d.userType || 'staff').toLowerCase();
    document.getElementById('uf_addPw').classList.add('d-none');
    document.getElementById('uf_password').required = false;
    document.getElementById('uf_editPw').classList.remove('d-none');
    document.getElementById('userModalTitle').textContent = 'Edit User';
    document.getElementById('userSubmit').textContent = 'Update user';
    userModal.show();
}
uForm.addEventListener('submit', function (e) {
    const a = document.getElementById('uf_newpassword').value;
    const b = document.getElementById('uf_confirmpassword').value;
    if (!document.getElementById('uf_editPw').classList.contains('d-none') && a !== b) {
        e.preventDefault();
        Swal.fire({ icon: 'error', title: 'Passwords do not match' });
    }
});
function deleteUser(id, name) {
    Swal.fire({ icon:'warning', title:'Delete user?',
        html:'Remove the account for <b>' + name + '</b>?',
        showCancelButton:true, confirmButtonText:'Delete', confirmButtonColor:'#c0392b', reverseButtons:true
    }).then(r => { if (r.isConfirmed) location.href = '{$deleteUrl}?id=' + id; });
}
</script>
HTML;
require __DIR__ . '/../partials/admin_bottom.php';
