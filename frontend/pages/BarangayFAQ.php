<?php
/**
 * BarangayFAQ.php — manage the public FAQ (list + add + edit + delete).
 * Add  -> backend/actions/faq1_insert.php   (question, answer, date)
 * Edit -> backend/actions/faq1_update.php   (faq_id, question, answer, date)
 * Delete -> backend/actions/faq1_delete.php (POST delete_id)
 */
require __DIR__ . '/../partials/bootstrap.php';
require_admin();

$pdo = db();

$search  = trim($_GET['search'] ?? '');
$perPage = 10;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;
$like    = '%' . $search . '%';

$st = $pdo->prepare('SELECT COUNT(*) FROM faq WHERE question LIKE ? OR answer LIKE ?');
$st->execute([$like, $like]);
$total = (int) $st->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));

$st = $pdo->prepare('SELECT * FROM faq WHERE question LIKE ? OR answer LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?');
$st->bindValue(1, $like); $st->bindValue(2, $like);
$st->bindValue(3, $perPage, PDO::PARAM_INT);
$st->bindValue(4, $offset, PDO::PARAM_INT);
$st->execute();
$rows = $st->fetchAll();

$page_title    = 'FAQ';
$page_heading  = 'Frequently Asked Questions';
$page_subtitle = $total . ' published question' . ($total === 1 ? '' : 's');
$active_nav    = 'faq';
$page_actions  = '<button class="btn btn-primary" onclick="openFaq()"><i class="bi bi-plus-lg me-1"></i>Add FAQ</button>';

require __DIR__ . '/../partials/admin_top.php';
?>

<div class="card">
    <form class="table-toolbar" method="get">
        <div class="field-search">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" name="search" value="<?= e($search) ?>" placeholder="Search questions…">
        </div>
        <?php if ($search): ?><a class="btn btn-outline-secondary" href="?">Clear</a><?php endif; ?>
        <span class="spacer"></span>
        <span class="text-caption"><?= $total ?> result<?= $total === 1 ? '' : 's' ?></span>
    </form>

    <div class="card-bd">
        <?php if (!$rows): ?>
            <div class="empty"><i class="bi bi-question-circle"></i><h3>No FAQs yet</h3></div>
        <?php else: ?>
        <div class="divide-y">
            <?php foreach ($rows as $r): ?>
            <div class="py-3" data-row='<?= e(json_encode($r, JSON_HEX_APOS | JSON_HEX_QUOT)) ?>'>
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <div class="fw-semibold mb-1"><i class="bi bi-patch-question me-1 text-primary"></i><?= e($r['question']) ?></div>
                        <div class="text-muted-2 small"><?= nl2br(e($r['answer'])) ?></div>
                        <div class="text-caption mt-1"><?= e($r['date'] ? date('M j, Y', strtotime((string) $r['date'])) : '') ?></div>
                    </div>
                    <div class="d-flex gap-2 flex-shrink-0">
                        <button class="btn btn-sm btn-light btn-icon" title="Edit" onclick="editFaq(this)"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-light btn-icon text-danger" title="Delete"
                                onclick="deleteFaq(<?= (int) $r['id'] ?>)"><i class="bi bi-trash"></i></button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <?= render_pager($page, $pages, $total, $qs ?? ['search' => $search]) ?>
</div>

<div class="modal fade" id="faqModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="faqForm" method="POST" action="<?= action_url('faq1_insert.php') ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="faqModalTitle">Add FAQ</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="faq_id" id="ff_id">
          <div class="mb-3">
            <label class="form-label">Question</label>
            <input class="form-control" name="question" id="ff_question" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Answer</label>
            <textarea class="form-control" rows="4" name="answer" id="ff_answer" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" class="form-control" name="date" id="ff_date" value="<?= date('Y-m-d') ?>">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="faqSubmit">Save FAQ</button>
        </div>
      </form>
    </div>
  </div>
</div>

<form id="faqDeleteForm" method="POST" action="<?= action_url('faq1_delete.php') ?>" class="d-none">
    <input type="hidden" name="delete_id" id="faqDeleteId">
</form>

<?php
$insertUrl = action_url('faq1_insert.php');
$updateUrl = action_url('faq1_update.php');
$foot_extra = <<<HTML
<script>
const faqModal = new bootstrap.Modal('#faqModal');
const fForm = document.getElementById('faqForm');
function openFaq() {
    fForm.reset(); fForm.action = '{$insertUrl}';
    document.getElementById('ff_id').value = '';
    document.getElementById('ff_date').value = new Date().toISOString().slice(0, 10);
    document.getElementById('faqModalTitle').textContent = 'Add FAQ';
    document.getElementById('faqSubmit').textContent = 'Save FAQ';
    faqModal.show();
}
function editFaq(btn) {
    const d = JSON.parse(btn.closest('[data-row]').dataset.row);
    fForm.reset(); fForm.action = '{$updateUrl}';
    document.getElementById('ff_id').value = d.id;
    document.getElementById('ff_question').value = d.question ?? '';
    document.getElementById('ff_answer').value = d.answer ?? '';
    document.getElementById('ff_date').value = d.date ?? '';
    document.getElementById('faqModalTitle').textContent = 'Edit FAQ';
    document.getElementById('faqSubmit').textContent = 'Update FAQ';
    faqModal.show();
}
function deleteFaq(id) {
    Swal.fire({ icon:'warning', title:'Delete this FAQ?',
        showCancelButton:true, confirmButtonText:'Delete', confirmButtonColor:'#c0392b', reverseButtons:true
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('faqDeleteId').value = id;
            document.getElementById('faqDeleteForm').submit();
        }
    });
}
</script>
HTML;
require __DIR__ . '/../partials/admin_bottom.php';
