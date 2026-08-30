<?php
/** Photos.php — public photo album of barangay activities. */
require __DIR__ . '/../partials/bootstrap.php';

$photos = db()->query('SELECT photos, activity, date, description FROM activity ORDER BY date DESC, id DESC')->fetchAll();

$page_title = 'Photo Album';
$active     = 'photos';
require __DIR__ . '/../partials/public_top.php';
?>

<header class="hero" style="min-height:34vh;--hero-img:linear-gradient(#0b2f6e,#123f92)">
    <div class="container">
        <p class="section__eyebrow text-white-50">Our Barangay</p>
        <h1>Photo Album</h1>
        <p>Moments from barangay activities and community events.</p>
    </div>
</header>

<section class="section">
    <div class="container">
        <?php if (!$photos): ?>
            <p class="text-center text-muted-2">No photos have been added yet.</p>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($photos as $p): ?>
            <div class="col-sm-6 col-lg-4">
                <div class="card h-100 hover-lift">
                    <img src="<?= upload_url('activity_photos/' . ($p['photos'] ?? '')) ?>" alt="<?= e($p['activity']) ?>"
                         style="height:220px;object-fit:cover;border-radius:var(--radius) var(--radius) 0 0;background:var(--brand-50)"
                         onerror="this.src='<?= asset('images/logo1.png') ?>';this.style.objectFit='contain';this.style.padding='2rem'">
                    <div class="card-bd">
                        <div class="text-caption mb-1"><i class="bi bi-calendar-event me-1"></i><?= e($p['date'] ? date('F j, Y', strtotime((string) $p['date'])) : '') ?></div>
                        <h3 class="h6 mb-1"><?= e($p['activity']) ?></h3>
                        <p class="small text-muted-2 mb-0"><?= e($p['description']) ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../partials/public_bottom.php'; ?>
