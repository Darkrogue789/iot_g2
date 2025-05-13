<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
  const map = L.map('map').setView([<?= $latitude ?>, <?= $longitude ?>], 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);
  L.marker([<?= $latitude ?>, <?= $longitude ?>]).addTo(map).bindPopup('Sensor Location').openPopup();

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

          const sensorCard = document.getElementById(`sensor-${sensor._id}`);
          sensorCard.classList.add('fade-in');
          setTimeout(() => {
            sensorCard.classList.remove('fade-in');
          }, 1000);
        });
      });
  }

  function updateChart() {
    axios.get(`https://api.opensensemap.org/boxes/5f2b56f4263635001c1dd1fd/data/${sensorId}?format=json&limit=10`)
      .then(res => {
        const values = res.data.reverse();
        chart.data.labels = values.map(v => new Date(v.createdAt).toLocaleTimeString());
        chart.data.datasets[0].data = values.map(v => parseFloat(v.value));
        chart.update();

        const chartElement = document.getElementById('sensorChart');
        chartElement.classList.add('fade-in');
        setTimeout(() => {
          chartElement.classList.remove('fade-in');
        }, 1000);
      });
  }

  function refreshData() {
    updateSensorCards();
    updateChart();
  }

  refreshData();
  setInterval(refreshData, 60000);
</script>
