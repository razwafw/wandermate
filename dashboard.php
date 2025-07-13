<?php
require_once 'config.php';

session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] ?? 1) != 2) {
    header('Location: index.php');
    exit();
}

// Database connection
$host = 'localhost';
$user = 'projec15_root';
$pass = '@kaesquare123';
$db = 'projec15_wandermate';
$conn = new mysqli($host, $user, $pass, $db);
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

    <!-- Global Styles -->
    <style>
        html {
            scroll-behavior: smooth;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #fff;
            color: #000;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .btn {
            display: inline-block;
            background-color: midnightblue;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #1a1a5a;
            color: #fff;
        }

        button[disabled] {
            background-color: #cccccc !important;
            color: #666666 !important;
            cursor: not-allowed;
            opacity: 0.7;
        }

        button[disabled]:hover {
            background-color: #cccccc !important;
            color: #666666 !important;
        }

        .btn-full {
            width: 100%;
            text-align: center;
            padding: 15px;
            font-size: 1.1rem;
        }

        .btn-outline {
            background-color: transparent;
            color: midnightblue;
            border: 1px solid midnightblue;
        }

        .btn-outline:hover {
            background-color: rgba(25, 25, 112, 0.1);
            color: midnightblue;
        }

        body {
            background-color: #f8f9fa;
        }

        .dashboard-container {
            min-height: 100vh;
            display: flex;
        }

        main {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .dashboard-title {
            font-size: 1.8rem;
            color: #333;
        }

        .dashboard-actions {
            display: flex;
            gap: 10px;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .dashboard-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 25px;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            text-align: center;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .card-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: midnightblue;
        }

        .card-title {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #333;
        }

        .card-description {
            color: #6c757d;
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .admin-stats {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-bottom: 30px;
        }

        .stats-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #333;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .stat-item {
            border-left: 4px solid midnightblue;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 0 5px 5px 0;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: midnightblue;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .btn-dashboard {
            padding: 12px 20px;
            font-size: 1rem;
            cursor: pointer;
        }

        .btn-green {
            background-color: #28a745;
        }

        .btn-green:hover {
            background-color: #218838;
        }

        .btn-orange {
            background-color: #fd7e14;
        }

        .btn-orange:hover {
            background-color: #e96b02;
        }

        .btn-red {
            background-color: #dc3545;
        }

        .btn-red:hover {
            background-color: #c82333;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: midnightblue;
            margin-right: 10px;
        }

        .preview-link {
            margin-top: 30px;
            text-align: right;
        }

        .preview-link a {
            color: midnightblue;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-weight: 500;
        }

        .preview-link a:hover {
            text-decoration: underline;
        }

        .preview-icon {
            margin-right: 5px;
            font-size: 1.1rem;
        }

        @media (max-width: 992px) {
            main {
                margin-left: 80px;
            }
        }

        @media (max-width: 768px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
            }

            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .dashboard-actions {
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>

    <!-- Material Icons -->
    <link
        href="https://fonts.googleapis.com/icon?family=Material+Icons"
        rel="stylesheet"
    >
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main>
            <div class="dashboard-header">
                <h1 class="dashboard-title">Admin Dashboard</h1>

                <div class="dashboard-actions">
                    <a
                        href="index.php"
                        class="btn btn-outline"
                        target="_blank"
                    >
                        <span
                            class="material-icons"
                            style="vertical-align: middle"
                        >
                            visibility
                        </span>
                        View Site
                    </a>
                </div>
            </div>

            <!-- Main Navigation Cards -->
            <div class="dashboard-cards">
                <a
                    href="package-management.php"
                    class="dashboard-card"
                >
                    <div class="card-icon">
                        <span class="material-icons">luggage</span>
                    </div>
                    <h3 class="card-title">Package Management</h3>
                    <p class="card-description">Add, edit or remove travel packages</p>
                    <button class="btn btn-green btn-dashboard">Manage Packages</button>
                </a>

                <a
                    href="order-management.php"
                    class="dashboard-card"
                >
                    <div class="card-icon">
                        <span class="material-icons">shopping_cart</span>
                    </div>
                    <h3 class="card-title">Order Management</h3>
                    <p class="card-description">View and process customer orders</p>
                    <button class="btn btn-orange btn-dashboard">Manage Orders</button>
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
