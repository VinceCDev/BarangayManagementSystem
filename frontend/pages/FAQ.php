<?php
/** FAQ.php — public frequently-asked-questions page. */
require __DIR__ . '/../partials/bootstrap.php';

$faqs = db()->query('SELECT question, answer FROM faq ORDER BY id')->fetchAll();

$page_title = 'FAQ';
$active     = 'faq';
require __DIR__ . '/../partials/public_top.php';
?>

<section class="section">
    <div class="container" style="max-width:820px">
        <div class="text-center mb-5">
            <p class="section__eyebrow">Need help?</p>
            <h2 class="section__title">Frequently asked questions</h2>
        </div>

        <?php if (!$faqs): ?>
            <p class="text-center text-muted-2">No FAQs have been published yet.</p>
        <?php else: ?>
        <div class="accordion" id="faqAcc">
            <?php foreach ($faqs as $i => $f): ?>
            <div class="accordion-item border rounded-xl mb-2 overflow-hidden">
                <h3 class="accordion-header">
                    <button class="accordion-button <?= $i ? 'collapsed' : '' ?>" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>">
                        <?= e($f['question']) ?>
                    </button>
                </h3>
                <div id="faq<?= $i ?>" class="accordion-collapse collapse <?= $i ? '' : 'show' ?>" data-bs-parent="#faqAcc">
                    <div class="accordion-body text-muted-2"><?= nl2br(e($f['answer'])) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="text-center mt-5">
            <p class="text-muted-2">Can’t find your answer?</p>
            <a href="<?= page_url('Contact.php') ?>" class="btn btn-primary">Contact the barangay</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../partials/public_bottom.php'; ?>
