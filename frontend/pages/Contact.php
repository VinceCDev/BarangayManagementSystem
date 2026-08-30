<?php
/** Contact.php — public contact page: message form + barangay directory. */
require __DIR__ . '/../partials/bootstrap.php';

$contacts = db()->query('SELECT label, description, contacts FROM contacts ORDER BY id')->fetchAll();
$sent = $_GET['sent'] ?? '';

$page_title = 'Contact';
$active     = 'contact';
require __DIR__ . '/../partials/public_top.php';
?>

<section class="section">
    <div class="container">
        <div class="text-center mb-5">
            <p class="section__eyebrow">Get in touch</p>
            <h2 class="section__title">Contact Barangay Paule 1</h2>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-hd"><span class="card-hd__title"><i class="bi bi-envelope"></i> Send a message</span></div>
                    <div class="card-bd">
                        <?php if ($sent === 'ok'): ?>
                            <div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>Thank you — your message has been received.</div>
                        <?php elseif ($sent === 'invalid'): ?>
                            <div class="alert alert-warning">Please fill in your name, a valid email and a message.</div>
                        <?php elseif ($sent === 'error'): ?>
                            <div class="alert alert-danger">Sorry, something went wrong. Please try again later.</div>
                        <?php endif; ?>

                        <form method="post" action="<?= action_url('contact_insert.php') ?>" class="row g-3">
                            <div class="col-md-8"><label class="form-label">Full name</label>
                                <input class="form-control" name="name" required></div>
                            <div class="col-md-4"><label class="form-label">Age</label>
                                <input type="number" min="0" class="form-control" name="age"></div>
                            <div class="col-md-6"><label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required></div>
                            <div class="col-md-6"><label class="form-label">Contact number</label>
                                <input class="form-control" name="contact"></div>
                            <div class="col-12"><label class="form-label">Message</label>
                                <textarea class="form-control" name="message" rows="5" required></textarea></div>
                            <div class="col-12">
                                <button class="btn btn-primary"><i class="bi bi-send me-1"></i>Send message</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-hd"><span class="card-hd__title"><i class="bi bi-telephone"></i> Directory</span></div>
                    <div class="card-bd divide-y">
                        <div class="pb-3">
                            <div class="fw-semibold"><i class="bi bi-geo-alt me-2 text-primary"></i>Barangay Hall</div>
                            <div class="text-muted-2 small">Paule 1, Rizal, Laguna</div>
                        </div>
                        <?php foreach ($contacts as $c): ?>
                        <div class="py-3">
                            <div class="fw-semibold"><?= e($c['label']) ?></div>
                            <div class="text-muted-2 small"><?= e($c['description'] ?: '') ?></div>
                            <div class="mt-1"><i class="bi bi-telephone me-2 text-primary"></i><?= e($c['contacts'] ?: '—') ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../partials/public_bottom.php'; ?>
