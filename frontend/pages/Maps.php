<?php
/** Maps.php — public location + land-area page. */
require __DIR__ . '/../partials/bootstrap.php';

$map = db()->query('SELECT total_land_area, land_used FROM map_statics ORDER BY id LIMIT 1')->fetch() ?: [];

$page_title = 'Maps';
$active     = 'maps';
require __DIR__ . '/../partials/public_top.php';

$embed = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7738.967087619569!2d121.40030895000001!3d14.107637050000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd5a4d36636399%3A0x4f52190306e4b869!2sPaule%201%2C%20Rizal%2C%20Laguna!5e0!3m2!1sen!2sph!4v1713785201525!5m2!1sen!2sph';
?>

<section class="section">
    <div class="container">
        <div class="text-center mb-4">
            <p class="section__eyebrow">Our Barangay</p>
            <h2 class="section__title">Where to find us</h2>
        </div>
        <div class="card overflow-hidden mb-4">
            <iframe src="<?= e($embed) ?>" width="100%" height="440" style="border:0" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade" title="Map of Barangay Paule 1"></iframe>
        </div>
        <div class="row g-4">
            <div class="col-md-6"><div class="info-card">
                <div class="icon-circle"><i class="bi bi-geo-alt"></i></div>
                <div class="h3 mb-0"><?= e($map['total_land_area'] ?? '—') ?></div>
                <div class="text-muted-2">Total land area</div>
            </div></div>
            <div class="col-md-6"><div class="info-card">
                <div class="icon-circle"><i class="bi bi-rulers"></i></div>
                <div class="h3 mb-0"><?= e($map['land_used'] ?? '—') ?></div>
                <div class="text-muted-2">Land used</div>
            </div></div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../partials/public_bottom.php'; ?>
