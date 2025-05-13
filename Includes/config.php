<?php
$url = 'https://api.opensensemap.org/boxes/5f2b56f4263635001c1dd1fd';
$response = file_get_contents($url);
$data = json_decode($response, true);

$boxName = $data['name'] ?? 'Unknown Box';
$country = $data['exposure'] ?? 'Unknown';
$longitude = $data['currentLocation'][1] ?? 0;
$latitude = $data['currentLocation'][0] ?? 0;
$sensors = $data['sensors'] ?? [];
