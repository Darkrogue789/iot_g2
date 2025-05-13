<?php
// dashboard.php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the logged-in user's username
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sensor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Welcome, <?= htmlspecialchars($username) ?>!</h2>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Sensor Dashboard</h4>
                <p class="card-text">You are now logged in. Here you can view and manage sensor data.</p>
                <a href="index.php" class="btn btn-primary">Go to Sensor View</a>
            </div>
        </div>
    </div>
</body>
</html>
