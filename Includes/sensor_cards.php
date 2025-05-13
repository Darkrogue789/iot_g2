<div class="row" id="sensorCards">
  <?php foreach ($sensors as $sensor): 
    $sid = $sensor['_id'];
    $title = htmlspecialchars($sensor['title']);
    $unit = htmlspecialchars($sensor['unit']);
    $value = htmlspecialchars($sensor['lastMeasurement']['value'] ?? 'N/A');
    $time = htmlspecialchars($sensor['lastMeasurement']['createdAt'] ?? 'Unknown');
  ?>
  <div class="col-md-6 sensor-card" id="sensor-<?= $sid ?>">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"><?= $title ?></h5>
        <p class="sensor-value" id="value-<?= $sid ?>"><?= $value ?> <?= $unit ?></p>
        <small class="text-muted" id="time-<?= $sid ?>">Updated: <?= date('g:i:s A', strtotime($time)) ?></small>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
