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
