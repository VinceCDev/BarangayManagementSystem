<?php
/**
 * MyMessages.php — a resident's messages to the barangay (sent via the
 * public contact form, matched by e-mail) plus a compose box.
 */
require __DIR__ . '/../partials/bootstrap.php';
require_role(['resident']);

$pdo   = db();
$email = current_username();

$st = $pdo->prepare('SELECT firstname, lastname, contact FROM profiledata WHERE email = ? LIMIT 1');
$st->execute([$email]);
$me = $st->fetch() ?: [];
$myName = trim(($me['firstname'] ?? '') . ' ' . ($me['lastname'] ?? '')) ?: $email;

$msgs = $pdo->prepare('SELECT * FROM receivemessages WHERE email = ? ORDER BY id DESC');
$msgs->execute([$email]);
$msgs = $msgs->fetchAll();

$sent = $_GET['sent'] ?? '';

$page_title   = 'My Messages';
$page_heading = 'My Messages';
$page_subtitle = count($msgs) . ' message' . (count($msgs) === 1 ? '' : 's') . ' sent';
$active_nav   = 'mymessages';
require __DIR__ . '/../partials/admin_top.php';
?>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-hd"><span class="card-hd__title"><i class="bi bi-send"></i> New message</span></div>
            <div class="card-bd">
                <?php if ($sent === 'ok'): ?>
                    <div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>Message sent.</div>
                <?php elseif ($sent): ?>
                    <div class="alert alert-warning">Please write a message before sending.</div>
                <?php endif; ?>
                <form method="post" action="<?= action_url('contact_insert.php') ?>">
                    <input type="hidden" name="name" value="<?= e($myName) ?>">
                    <input type="hidden" name="email" value="<?= e($email) ?>">
                    <input type="hidden" name="contact" value="<?= e($me['contact'] ?? '') ?>">
                    <input type="hidden" name="_return" value="MyMessages.php">
                    <label class="form-label">Message</label>
                    <textarea class="form-control" name="message" rows="5" required
                              placeholder="Type your message to the barangay…"></textarea>
                    <button class="btn btn-primary mt-3"><i class="bi bi-send me-1"></i>Send</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-hd"><span class="card-hd__title"><i class="bi bi-clock-history"></i> Sent messages</span></div>
            <div class="card-bd stack">
                <?php if (!$msgs): ?>
                    <div class="empty py-4"><i class="bi bi-inbox"></i>You haven’t sent any messages yet.</div>
                <?php else: foreach ($msgs as $m): ?>
                    <div class="p-3 rounded" style="background:var(--surface-2);border:1px solid var(--line)">
                        <div class="text-caption mb-1"><i class="bi bi-calendar me-1"></i>
                            <?= e($m['created_at'] ? date('M j, Y g:i A', strtotime((string) $m['created_at'])) : '') ?></div>
                        <div><?= nl2br(e($m['message'])) ?></div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/admin_bottom.php'; ?>
