<?php
require_once 'config.php';

session_start();
$loggedIn = isset($_SESSION['user_id']) && isset($_SESSION['role_id']);
$role_id = $_SESSION['role_id'];

require_once 'DatabaseConnection.php';

$conn = new DatabaseConnection();

$packages = [];

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$sql = 'SELECT p.id, p.name, p.description, p.price / p.group_size as price_per_person, p.images FROM packages p';

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['image'] = trim(explode("\n", $row['images'])[0]);

        $packages[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>WanderMate - Travel Packages</title>

    <link
        rel="stylesheet"
        href="global.css"
    >
    <link
        rel="stylesheet"
        href="header.css"
    >
    <link
        rel="stylesheet"
        href="page-header.css"
    >
    <link
        rel="stylesheet"
        href="package-list.css"
    >
    <link
        rel="stylesheet"
        href="package-card.css"
    >
    <link
        rel="stylesheet"
        href="modal.css"
    >
    <link
        rel="stylesheet"
        href="footer.css"
    >

    <!-- Font Awesome (for icons) -->
    <script
        src="https://kit.fontawesome.com/c880a1b0f6.js"
        crossorigin="anonymous"
    ></script>
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Page Content -->
    <main>
        <?php
        $title = 'Explore Our Travel Packages';
        $subtitle = 'Discover carefully curated travel experiences that combine adventure, comfort, and unforgettable memories. From tropical getaways to cultural explorations, we have the perfect package for you.';
        include 'page-header.php';
        ?>

        <div class="container">
            <?php if (count($packages) > 0): ?>
                <div class="search-container">
                    <input
                        type="text"
                        class="search-input"
                        placeholder="Search for packages, destinations..."
                        id="package-search"
                    />
                    <button
                        class="search-btn"
                        onclick="document.querySelector('.search-input').focus();"
                    >
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>

                <div class="packages-grid">
                    <?php foreach ($packages as $package): ?>
                        <?php include 'package-card.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <p
                id="noPackagesFound"
                class="no-packages-found"
                <?php if (count($packages) > 0) echo 'style="display: none;"'; ?>
            >
                No packages found.
            </p>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="header.js"></script>
    <script src="package-list.js"></script>
</body>
</html>
