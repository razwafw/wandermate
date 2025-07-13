<?php
include 'env.php';

// Fetch most popular packages from the database
$host = 'localhost';
$user = 'projec15_root';
$pass = '@kaesquare123';
$db = 'projec15_wandermate';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
$popularPackages = [];
$sql = "SELECT p.id, p.name, p.description, p.price / p.group_size as price_per_person, (SELECT url FROM images WHERE package_id = p.id LIMIT 1) AS image_url, SUM(o.amount)  AS total_orders
        FROM packages p
        LEFT JOIN orders o ON o.package_id = p.id
        GROUP BY p.id
        ORDER BY total_orders DESC, price_per_person
        LIMIT 3";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Get first image for the package
        $imgSql = "SELECT url FROM images WHERE package_id = " . $row['id'] . " LIMIT 1";
        $imgResult = $conn->query($imgSql);
        $row['image_url'] = ($imgResult && $imgResult->num_rows > 0)
            ? $imgResult->fetch_assoc()['url']
            : 'https://picsum.photos/600/400?random=' . $row['id'];
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
    </style>

    <!-- Page Styles-->
    <style>
        /* Inter-section */
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: #000;
            margin-bottom: 15px;
        }

        .section-title p {
            color: #555;
            max-width: 700px;
            margin: 0 auto;
        }

        /* Hero Section */
        .hero {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('person-standing-near-cliff.jpg');
            background-size: cover;
            background-position: center;
            height: 80vh;
            display: flex;
            align-items: center;
            text-align: center;
            color: #fff;
            padding-top: 80px;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        /* About Section */
        .about {
            padding: 80px 0;
            background-color: #f8f8f8;
        }

        .about-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
        }

        .about-text {
            flex: 1;
        }

        .about-text h3 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: midnightblue;
        }

        .about-image {
            flex: 1;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .about-image img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Packages Section */
        .packages {
            padding: 80px 0;
        }

        .package-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 50px;
            margin-bottom: 50px;
        }

        .package-card {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .package-card:hover {
            transform: translateY(-10px);
        }

        .package-img {
            height: 200px;
            overflow: hidden;
        }

        .package-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .package-card:hover .package-img img {
            transform: scale(1.1);
        }

        .package-info {
            padding: 20px;
        }

        .package-info h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: midnightblue;
        }

        .price {
            font-size: 1.2rem;
            font-weight: 700;
            margin: 15px 0;
            color: #000;
        }

        .package-info .btn {
            display: block;
            text-align: center;
            margin-top: 15px;
        }

        /* CTA Section */
        .cta {
            background-color: midnightblue;
            padding: 80px 0;
            text-align: center;
            color: #fff;
        }

        .cta h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .cta p {
            max-width: 700px;
            margin: 0 auto 30px;
            font-size: 1.1rem;
        }

        .cta .btn {
            background-color: #fff;
            color: midnightblue;
            font-weight: 600;
        }

        .cta .btn:hover {
            background-color: #f0f0f0;
            color: midnightblue;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            /* Inter-section */
            .section-title h2 {
                font-size: 2rem;
            }

            /* Hero Section */
            .hero h1 {
                font-size: 2.2rem;
            }

            /* About Section */
            .about-content {
                flex-direction: column;
            }
        }
    </style>
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
        <section class="packages">
            <div class="container">
                <div class="section-title">
                    <h2>Popular Packages</h2>
                    <p>Explore our most sought-after travel experiences and find your next dream destination.</p>
                </div>
                <div class="package-cards">
                    <?php foreach ($popularPackages as $pkg): ?>
                        <div class="package-card">
                            <div class="package-img">
                                <img
                                    src="<?php echo htmlspecialchars($pkg['image_url']); ?>"
                                    alt="<?php echo htmlspecialchars($pkg['name']); ?>"
                                >
                            </div>
                            <div class="package-info">
                                <h3><?php echo htmlspecialchars($pkg['name']); ?></h3>
                                <p><?php echo htmlspecialchars($pkg['subtitle'] ?? (strlen($pkg['description']) > 100 ? substr($pkg['description'], 0, 100) . '...' : $pkg['description'])); ?></p>
                                <div class="price">From $<?php echo number_format($pkg['price_per_person']); ?> per person</div>
                                <a
                                    href="package-detail.php?id=<?php echo $pkg['id']; ?>"
                                    class="btn"
                                >View Details
                                </a>
                            </div>
                        </div>
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
</body>
</html>
