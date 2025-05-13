<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<?php include 'header.php'; ?>
<?php include 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include 'includes/head.php'; ?>
</head>
<body class="bg-light">
  <div class="container py-5">
    <?php include 'includes/header.php'; ?>

    <!-- Back to Dashboard Button -->
    <div class="mb-4 text-end">
      <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>

    <?php include 'includes/map.php'; ?>
    <?php include 'includes/sensor_cards.php'; ?>
    <?php include 'includes/chart.php'; ?>
  </div>
  <?php include 'includes/scripts.php'; ?>
</body>
</html>
