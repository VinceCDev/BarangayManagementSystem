<?php
/** History.php — public barangay history page. */
require __DIR__ . '/../partials/bootstrap.php';

$context = db()->query('SELECT context FROM history ORDER BY id LIMIT 1')->fetchColumn() ?: 'History content is not available yet.';

$page_title = 'History';
$active     = 'history';
require __DIR__ . '/../partials/public_top.php';
?>

<header class="hero" style="min-height:36vh;--hero-img:url(<?= asset('images/history.jpg') ?>)">
    <div class="container">
        <p class="section__eyebrow text-white-50">Our Barangay</p>
        <h1>History</h1>
    </div>
</header>

<section class="section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5">
                <img src="<?= asset('images/history.jpg') ?>" alt="Barangay Paule 1 history"
                     class="img-fluid rounded-xl shadow-card" style="width:100%;max-height:380px;object-fit:cover">
            </div>
            <div class="col-lg-7">
                <p class="section__eyebrow">How it began</p>
                <h2 class="section__title mb-3">The story of Barangay Paule 1</h2>
                <p class="text-muted-2" style="white-space:pre-line"><?= e($context) ?></p>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../partials/public_bottom.php'; ?>
