<?php
// Load sensor box data
$url = 'https://api.opensensemap.org/boxes/5f2b56f4263635001c1dd1fd';
$response = file_get_contents($url);
$data = json_decode($response, true);

// Box metadata
$boxName = $data['name'] ?? 'Unknown Box';
$country = $data['exposure'] ?? 'Unknown';
$longitude = $data['currentLocation'][1] ?? 0;
$latitude = $data['currentLocation'][0] ?? 0;
$sensors = $data['sensors'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($boxName) ?> - Sensor Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap, Chart.js, Leaflet -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    #map { height: 300px; }
    .sensor-card { margin-bottom: 1rem; }
    .sensor-value { font-size: 1.5rem; font-weight: bold; }
    .sensor-card, #sensorChart {
      transition: transform 0.5s ease-in-out;
    }
    .fade-in {
      animation: fadeIn 1s ease-in-out;
    }
    @keyframes fadeIn {
      0% { opacity: 0; }
      100% { opacity: 1; }
    }
  </style>
</head>
<body class="bg-light">
<div class="container py-5">
  <h2 class="mb-4 text-center"><?= htmlspecialchars($boxName) ?> – Live Dashboard</h2>

  <!-- Location Info -->
  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Location Info</h5>
          <p><strong>Country:</strong> <?= htmlspecialchars($country) ?></p>
          <p><strong>Latitude:</strong> <?= $latitude ?></p>
          <p><strong>Longitude:</strong> <?= $longitude ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Map</h5>
          <div id="map"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Sensor Cards -->
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

  <!-- Live Graph for First Sensor -->
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Live Graph (<?= htmlspecialchars($sensors[0]['title'] ?? 'Sensor') ?>)</h5>
      <canvas id="sensorChart" height="100"></canvas>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
  // Initialize Leaflet map
  const map = L.map('map').setView([<?= $latitude ?>, <?= $longitude ?>], 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);
  L.marker([<?= $latitude ?>, <?= $longitude ?>]).addTo(map).bindPopup('Sensor Location').openPopup();

  // Chart.js setup for the first sensor
  const sensorId = <?= json_encode($sensors[0]['_id'] ?? '') ?>;
  const sensorUnit = <?= json_encode($sensors[0]['unit'] ?? '') ?>;
  const chartCtx = document.getElementById('sensorChart').getContext('2d');
  const chart = new Chart(chartCtx, {
    type: 'line',
    data: {
      labels: [],
      datasets: [{
        label: 'Live Sensor (' + sensorUnit + ')',
        data: [],
        borderColor: '#0d6efd',
        backgroundColor: 'rgba(13, 110, 253, 0.2)',
        tension: 0.3,
        pointRadius: 3
      }]
    },
    options: {
      responsive: true,
      scales: {
        x: { title: { display: true, text: 'Time' }},
        y: { title: { display: true, text: sensorUnit }}
      }
    }
  });

  // Counting animation for sensor data
  function animateCount(el, start, end, duration) {
    let startTime = null;
    function step(time) {
      if (!startTime) startTime = time;
      const progress = Math.min((time - startTime) / duration, 1);
      el.textContent = (start + (end - start) * progress).toFixed(2);
      if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  // Update sensor values with counting animation
  function updateSensorCards() {
    axios.get('https://api.opensensemap.org/boxes/5f2b56f4263635001c1dd1fd')
      .then(res => {
        res.data.sensors.forEach(sensor => {
          const el = document.getElementById(`value-${sensor._id}`);
          const timeEl = document.getElementById(`time-${sensor._id}`);
          const last = parseFloat(sensor.lastMeasurement.value || 0);
          const current = parseFloat(el.textContent) || 0;
          animateCount(el, current, last, 1000);
          el.textContent = last.toFixed(2) + ' ' + sensor.unit;
          timeEl.textContent = 'Updated: ' + new Date(sensor.lastMeasurement.createdAt).toLocaleTimeString();

          // Apply the fade-in animation on sensor card update
          const sensorCard = document.getElementById(`sensor-${sensor._id}`);
          sensorCard.classList.add('fade-in');
          setTimeout(() => {
            sensorCard.classList.remove('fade-in');
          }, 1000); // Fade-in duration
        });
      });
  }

  // Update chart for first sensor
  function updateChart() {
    axios.get(`https://api.opensensemap.org/boxes/5f2b56f4263635001c1dd1fd/data/${sensorId}?format=json&limit=10`)
      .then(res => {
        const values = res.data.reverse();
        chart.data.labels = values.map(v => new Date(v.createdAt).toLocaleTimeString());
        chart.data.datasets[0].data = values.map(v => parseFloat(v.value));
        chart.update();

        // Apply the animation to the chart
        const chartElement = document.getElementById('sensorChart');
        chartElement.classList.add('fade-in');
        setTimeout(() => {
          chartElement.classList.remove('fade-in');
        }, 1000); // Fade-in duration for the chart
      });
  }

  // Initialize data and refresh every 60 seconds
  function refreshData() {
    updateSensorCards();
    updateChart();
  }

  // Initial load
  refreshData();

  // Repeat every 60 seconds with animation
  setInterval(refreshData, 60000);
</script>
</body>
</html>
