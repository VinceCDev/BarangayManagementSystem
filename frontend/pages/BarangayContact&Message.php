<?php
/**
 * BarangayContact&Message.php — inbox of public contact-form messages, plus
 * the editable barangay contact directory shown on the public site.
 * Contact edit -> backend/actions/contact_update.php (contact_id, label, description, contact)
 */
require __DIR__ . '/../partials/bootstrap.php';
require_role(['official', 'sk_chairman', 'treasurer']);

$pdo = db();

/* ---- Messages (paginated + search) ---------------------------------- */
$search  = trim($_GET['search'] ?? '');
$perPage = 10;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;
$like    = '%' . $search . '%';

$st = $pdo->prepare('SELECT COUNT(*) FROM receivemessages WHERE name LIKE ? OR email LIKE ?');
$st->execute([$like, $like]);
$total = (int) $st->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));

$st = $pdo->prepare(
    'SELECT * FROM receivemessages WHERE name LIKE ? OR email LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?'
);
$st->bindValue(1, $like); $st->bindValue(2, $like);
$st->bindValue(3, $perPage, PDO::PARAM_INT);
$st->bindValue(4, $offset, PDO::PARAM_INT);
$st->execute();
$messages = $st->fetchAll();

/* ---- Contact directory --------------------------------------------- */
$contacts = $pdo->query('SELECT * FROM contacts ORDER BY id')->fetchAll();

$page_title    = 'Messages';
$page_heading  = 'Messages & Contacts';
$page_subtitle = $total . ' message' . ($total === 1 ? '' : 's') . ' received';
$active_nav    = 'messages';

require __DIR__ . '/../partials/admin_top.php';
?>

<div class="row g-4">
  <!-- Messages inbox -->
  <div class="col-12">
    <div class="card">
        <form class="table-toolbar" method="get">
            <div class="field-search">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" name="search" value="<?= e($search) ?>" placeholder="Search sender or email…">
            </div>
            <?php if ($search): ?><a class="btn btn-sm btn-outline-secondary" href="?">Clear</a><?php endif; ?>
        </form>
        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>Sender</th><th>Contact</th><th>Message</th><th>Received</th><th class="col-actions">Reply</th></tr></thead>
                <tbody>
                <?php if (!$messages): ?>
                    <tr><td colspan="5"><div class="empty"><i class="bi bi-inbox"></i><h3>No messages</h3></div></td></tr>
                <?php else: foreach ($messages as $m): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= e($m['name']) ?></div>
                            <div class="text-caption"><?= e($m['email']) ?><?= $m['age'] ? ' · ' . e((string) $m['age']) : '' ?></div>
                        </td>
                        <td><?= e($m['contact'] ?: '—') ?></td>
                        <td class="text-truncate" style="max-width:26rem"><?= e($m['message']) ?></td>
                        <td><?= e($m['created_at'] ? date('M j, Y g:i A', strtotime((string) $m['created_at'])) : '—') ?></td>
                        <td class="col-actions">
                            <a class="btn btn-sm btn-outline-secondary" target="_blank"
                               href="https://mail.google.com/mail/?view=cm&fs=1&to=<?= urlencode((string) $m['email']) ?>">
                                <i class="bi bi-reply me-1"></i>Email
                            </a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
        <?= render_pager($page, $pages, $total, $qs ?? ['search' => $search], $perPage) ?>
    </div>
  </div>

  <!-- Contact directory -->
  <div class="col-12">
    <div class="card">
        <div class="card-hd"><span class="card-hd__title"><i class="bi bi-telephone"></i> Barangay Contact Directory</span></div>
        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>Label</th><th>Description</th><th>Contact</th><th class="col-actions">Actions</th></tr></thead>
                <tbody>
                <?php foreach ($contacts as $c): ?>
                    <tr data-row='<?= e(json_encode($c, JSON_HEX_APOS | JSON_HEX_QUOT)) ?>'>
                        <td class="fw-semibold"><?= e($c['label']) ?></td>
                        <td><?= e($c['description'] ?: '—') ?></td>
                        <td><?= e($c['contacts'] ?: '—') ?></td>
                        <td class="col-actions">
                            <?php if (is_admin()): ?>
                            <button class="btn btn-sm btn-light btn-icon" title="Edit" onclick="editContact(this)"><i class="bi bi-pencil"></i></button>
                            <?php else: ?><span class="text-caption">—</span><?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
  </div>
</div>

<div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="<?= action_url('contact_update.php') ?>">
        <div class="modal-header">
          <h5 class="modal-title">Edit Contact</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="contact_id" id="ct_id">
          <div class="mb-3"><label class="form-label">Label</label><input class="form-control" name="label" id="ct_label" required></div>
          <div class="mb-3"><label class="form-label">Description</label><input class="form-control" name="description" id="ct_description"></div>
          <div class="mb-3"><label class="form-label">Contact (phone / email)</label><input class="form-control" name="contact" id="ct_contact"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
$foot_extra = <<<'HTML'
<script>
const contactModal = new bootstrap.Modal('#contactModal');
function editContact(btn) {
    const d = JSON.parse(btn.closest('tr').dataset.row);
    document.getElementById('ct_id').value = d.id;
    document.getElementById('ct_label').value = d.label ?? '';
    document.getElementById('ct_description').value = d.description ?? '';
    document.getElementById('ct_contact').value = d.contacts ?? '';
    contactModal.show();
}
</script>
HTML;
require __DIR__ . '/../partials/admin_bottom.php';
