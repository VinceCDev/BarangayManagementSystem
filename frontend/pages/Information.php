<?php
/**
 * Information.php — edit the "Barangay Information" content shown on the
 * public site. One small form per content block; each posts a `section`
 * to backend/actions/information_update.php.
 */
require __DIR__ . '/../partials/bootstrap.php';
require_role(['official']);

$pdo = db();
$one = static fn (string $sql) => $pdo->query($sql)->fetch() ?: [];

$intro   = $one('SELECT paragraph FROM introduction ORDER BY id LIMIT 1');
$mission = $one('SELECT paragraph FROM mission ORDER BY id LIMIT 1');
$vision  = $one('SELECT paragraph FROM vision ORDER BY id LIMIT 1');
$history = $one('SELECT context FROM history ORDER BY id LIMIT 1');
$map     = $one('SELECT total_land_area, land_used FROM map_statics ORDER BY id LIMIT 1');
$stat    = $one('SELECT founding_years, environmental_health_status, partnerships_organization, projects_made FROM statistics ORDER BY id LIMIT 1');
$pop     = $one('SELECT number_of_population, average_household_size FROM population ORDER BY id LIMIT 1');

$listText = static fn (string $sql, string $col) => implode(
    "\n",
    array_column($pdo->query($sql)->fetchAll(), $col)
);
$economics = $listText('SELECT message FROM economics ORDER BY id', 'message');
$business  = $listText('SELECT business_text FROM major_business ORDER BY id', 'business_text');
$income    = $listText('SELECT income_text FROM major_income ORDER BY id', 'income_text');

$action = action_url('information_update.php');
$saved  = $_GET['saved'] ?? '';

$page_title    = 'Barangay Information';
$page_heading  = 'Barangay Information';
$page_subtitle = 'Content shown on the public “General Information” pages.';
$active_nav    = 'information';

require __DIR__ . '/../partials/admin_top.php';
?>

<?php if ($saved): ?>
<div class="alert alert-success d-flex align-items-center gap-2" role="alert">
    <i class="bi bi-check-circle-fill"></i> Saved “<?= e(ucfirst($saved)) ?>”.
</div>
<?php endif; ?>

<div class="row g-4">

  <?php
  /* Render a card with one <textarea> block. */
  $textCard = function (string $title, string $icon, string $section, string $field, string $value, string $hint = '') use ($action) {
      ?>
      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-hd"><span class="card-hd__title"><i class="bi <?= e($icon) ?>"></i> <?= e($title) ?></span></div>
          <form class="card-bd" method="post" action="<?= $action ?>">
            <input type="hidden" name="section" value="<?= e($section) ?>">
            <textarea class="form-control" name="<?= e($field) ?>" rows="5"><?= e($value) ?></textarea>
            <?php if ($hint): ?><div class="form-text mt-1"><?= e($hint) ?></div><?php endif; ?>
            <div class="text-end mt-3"><button class="btn btn-primary btn-sm">Save</button></div>
          </form>
        </div>
      </div>
      <?php
  };

  $textCard('Introduction', 'bi-card-text', 'introduction', 'paragraph', $intro['paragraph'] ?? '');
  $textCard('Mission', 'bi-flag', 'mission', 'paragraph', $mission['paragraph'] ?? '');
  $textCard('Vision', 'bi-eye', 'vision', 'paragraph', $vision['paragraph'] ?? '');
  $textCard('History', 'bi-clock-history', 'history', 'context', $history['context'] ?? '');
  $textCard('Predominant Economic Activities', 'bi-graph-up', 'economics', 'economics', $economics, 'One item per line.');
  $textCard('Major Businesses', 'bi-shop', 'business', 'business_text', $business, 'One item per line.');
  $textCard('Major Sources of Income', 'bi-cash-coin', 'income', 'income_text', $income, 'One item per line.');
  ?>

  <!-- Land area -->
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-hd"><span class="card-hd__title"><i class="bi bi-map"></i> Land Area</span></div>
      <form class="card-bd row g-3" method="post" action="<?= $action ?>">
        <input type="hidden" name="section" value="map">
        <div class="col-sm-6"><label class="form-label">Total land area</label>
          <input class="form-control" name="total_land_area" value="<?= e($map['total_land_area'] ?? '') ?>"></div>
        <div class="col-sm-6"><label class="form-label">Land used</label>
          <input class="form-control" name="land_used" value="<?= e($map['land_used'] ?? '') ?>"></div>
        <div class="col-12 text-end"><button class="btn btn-primary btn-sm">Save</button></div>
      </form>
    </div>
  </div>

  <!-- Population -->
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-hd"><span class="card-hd__title"><i class="bi bi-people"></i> Population</span></div>
      <form class="card-bd row g-3" method="post" action="<?= $action ?>">
        <input type="hidden" name="section" value="population">
        <div class="col-sm-6"><label class="form-label">Number of population</label>
          <input class="form-control" name="number_of_population" value="<?= e($pop['number_of_population'] ?? '') ?>"></div>
        <div class="col-sm-6"><label class="form-label">Average household size</label>
          <input class="form-control" name="average_household_size" value="<?= e($pop['average_household_size'] ?? '') ?>"></div>
        <div class="col-12 text-end"><button class="btn btn-primary btn-sm">Save</button></div>
      </form>
    </div>
  </div>

  <!-- Statistics -->
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-hd"><span class="card-hd__title"><i class="bi bi-bar-chart"></i> Statistics</span></div>
      <form class="card-bd row g-3" method="post" action="<?= $action ?>">
        <input type="hidden" name="section" value="statistics">
        <div class="col-sm-6"><label class="form-label">Founding year(s)</label>
          <input class="form-control" name="founding_years" value="<?= e($stat['founding_years'] ?? '') ?>"></div>
        <div class="col-sm-6"><label class="form-label">Environmental health status</label>
          <input class="form-control" name="environmental_health_status" value="<?= e($stat['environmental_health_status'] ?? '') ?>"></div>
        <div class="col-sm-6"><label class="form-label">Partnerships / organizations</label>
          <input class="form-control" name="partnerships_organization" value="<?= e($stat['partnerships_organization'] ?? '') ?>"></div>
        <div class="col-sm-6"><label class="form-label">Projects made</label>
          <input class="form-control" name="projects_made" value="<?= e($stat['projects_made'] ?? '') ?>"></div>
        <div class="col-12 text-end"><button class="btn btn-primary btn-sm">Save</button></div>
      </form>
    </div>
  </div>

</div>

<?php require __DIR__ . '/../partials/admin_bottom.php'; ?>
