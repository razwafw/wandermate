<?php
require_once 'config.php';

session_start();
$loggedIn = isset($_SESSION['user_id']) && isset($_SESSION['role_id']);
$role_id = $_SESSION['role_id'];

require_once 'DatabaseConnection.php';

$conn = new DatabaseConnection();

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$popularPackages = [];

$sql = "
    SELECT p.id, p.name, p.description, p.price / p.group_size as price_per_person, p.images, SUM(o.amount)  AS total_orders 
    FROM packages p
    LEFT JOIN orders o ON o.package_id = p.id
    GROUP BY p.id
    ORDER BY total_orders DESC, price_per_person
    LIMIT 3
";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['image'] = trim(explode("\n", $row['images'])[0]);

        $popularPackages[] = $row;
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
    <title>WanderMate - Your Travel Partner</title>

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
        href="index.css"
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
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Page Content -->
    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1>Discover the World with WanderMate</h1>
                    <p>Your trusted travel partner for unforgettable adventures and seamless journeys across the globe.</p>
                    <a
                        href="package-list.php"
                        class="btn"
                    >
                        Explore Packages
                    </a>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section
            id="about"
            class="about"
        >
            <div class="container">
                <div class="section-title">
                    <h2>About WanderMate</h2>
                    <p>Learn about our mission to make travel accessible, enjoyable, and unforgettable for everyone.</p>
                </div>
                <div class="about-content">
                    <div class="about-text">
                        <h3>Your Perfect Travel Companion</h3>
                        <p>Founded in 2018, WanderMate has quickly become one of the leading travel agencies in the industry. Our mission is to provide exceptional travel experiences that create lifelong memories for our clients.</p>
                        <p>We believe that travel should be accessible to everyone, which is why we offer a wide range of packages to suit different budgets and preferences. Our team of experienced travel consultants work tirelessly to craft itineraries that showcase the best each destination has to offer.</p>
                        <p>What sets us apart is our attention to detail and personalized service. We take the time to understand your travel dreams and turn them into reality.</p>
                    </div>
                    <div class="about-image">
                        <img
                            src="business-conversation.jpg"
                            alt="WanderMate Office"
                        >
                    </div>
                </div>
            </div>
        </section>

        <!-- Packages Section -->
        <?php if (count($popularPackages) > 0): ?>
            <section class="packages">
                <div class="container">
                    <div class="section-title">
                        <h2>Popular Packages</h2>
                        <p>Explore our most sought-after travel experiences and find your next dream destination.</p>
                    </div>
                    <div class="package-cards">
                        <?php foreach ($popularPackages as $package): ?>
                            <?php include 'package-card.php'; ?>
                        <?php endforeach; ?>
                    </div>
                    <div style="text-align: center; margin: 0 auto;">
                        <a
                            href="package-list.php"
                            class="btn"
                        >
                            See More
                        </a>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- CTA Section -->
        <section class="cta">
            <div class="container">
                <h2>Ready for Your Next Adventure?</h2>
                <p>Contact our travel experts today to start planning your dream vacation. We offer personalized itineraries to suit your preferences and budget.</p>
                <a
                    href="mailto:info@wandermate.com"
                    class="btn"
                >
                    Contact Us Now
                </a>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="header.js"></script>
</body>
</html>
