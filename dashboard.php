<?php
require_once 'config.php';

session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
    header('Location: index.php');
    exit();
}

require_once 'DatabaseConnection.php';
$conn = new DatabaseConnection();
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Fetch total packages
$packageCount = 0;
$orderCount = 0;
$packageResult = $conn->query('SELECT COUNT(*) AS total FROM packages');
if ($packageResult && $row = $packageResult->fetch_assoc()) {
    $packageCount = $row['total'];
}
// Fetch total orders
$orderResult = $conn->query('SELECT COUNT(*) AS total FROM orders');
if ($orderResult && $row = $orderResult->fetch_assoc()) {
    $orderCount = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>WanderMate - Admin Dashboard</title>

    <link
        rel="stylesheet"
        href="global.css"
    >
    <link
        rel="stylesheet"
        href="global-dashboard.css"
    >
    <link
        rel="stylesheet"
        href="sidebar.css"
    >
    <link
        rel="stylesheet"
        href="dashboard.css"
    >

    <!-- Font Awesome (for icons) -->
    <script
        src="https://kit.fontawesome.com/c880a1b0f6.js"
        crossorigin="anonymous"
    ></script>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main>
            <div class="dashboard-header">
                <h1 class="dashboard-title">Admin Dashboard</h1>

                <a
                    href="index.php"
                    class="btn btn-outline btn-with-icon"
                    target="_blank"
                >
                    <i class="fa-solid fa-eye"></i>
                    View Site
                </a>
            </div>

            <!-- Main Navigation Cards -->
            <div class="dashboard-cards">
                <a
                    href="package-management.php"
                    class="dashboard-card"
                >
                    <div class="card-icon">
                        <i class="fa-solid fa-suitcase-rolling"></i>
                    </div>
                    <h3 class="card-title">Package Management</h3>
                    <p class="card-description">Add, edit or remove travel packages</p>
                    <button class="btn">Manage Packages</button>
                </a>

                <a
                    href="order-management.php"
                    class="dashboard-card"
                >
                    <div class="card-icon">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <h3 class="card-title">Order Management</h3>
                    <p class="card-description">View and process customer orders</p>
                    <button class="btn">Manage Orders</button>
                </a>
            </div>

            <!-- Quick Stats -->
            <div class="admin-stats">
                <h2 class="stats-title">Quick Stats</h2>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $packageCount; ?></div>
                        <div class="stat-label">Total Packages</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $orderCount; ?></div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
