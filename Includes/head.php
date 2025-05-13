<meta charset="UTF-8">
<title><?= htmlspecialchars($boxName) ?> - Sensor Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
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
