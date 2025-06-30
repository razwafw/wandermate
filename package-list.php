<?php
session_start();
$loggedIn = isset($_SESSION['user_id']);
$role_id = $_SESSION['role_id'] ?? 1;
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
    </style>

    <!-- Header Styles -->
    <style>
        header {
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: midnightblue;
            cursor: pointer;
            text-decoration: none;
        }

        .logo span {
            color: #000;
        }

        nav ul {
            display: flex;
            align-items: center;
            list-style: none;
        }

        nav ul li {
            margin-left: 30px;
        }

        nav ul li a {
            text-decoration: none;
            color: #000;
            font-weight: 500;
            transition: color 0.3s;
        }

        nav ul li a:hover {
            color: midnightblue;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
            }

            nav ul {
                margin-top: 20px;
                flex-wrap: wrap;
                justify-content: center;
            }

            nav ul li {
                margin: 5px 10px;
            }
        }
    </style>

    <!-- Footer Styles -->
    <style>
        footer {
            background-color: #111;
            color: #fff;
            padding: 60px 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .footer-column {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .footer-column h3 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            color: #fff;
        }

        .footer-column p {
            color: #bbb;
            margin-bottom: 10px;
            width: 100%;
        }

        .footer-column a {
            color: #bbb;
            margin-bottom: 10px;
            text-decoration: none;
            transition: color 0.3s;
            display: inline-block;
            padding: 3px 8px;
        }

        .footer-column a:hover {
            color: #fff;
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #333;
            color: #999;
            font-size: 0.9rem;
        }
    </style>

    <!-- Page Styles-->
    <style>
        .page-header {
            background-color: #f8f9fa;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 20px;
        }

        .page-header h1 {
            font-size: 2.5rem;
            color: midnightblue;
            margin-bottom: 10px;
        }

        .page-header p {
            color: #666;
            max-width: 700px;
            margin: 0 auto;
        }

        /* Search Bar Styles */
        .search-container {
            max-width: 600px;
            margin: 0 auto 40px;
            position: relative;
            display: flex;
            align-items: center;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 30px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .search-input {
            padding: 12px 20px;
            border: 1px solid #ddd;
            border-radius: 30px;
            outline: none;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
        }

        .search-input:focus {
            border-color: midnightblue;
            box-shadow: 0 0 8px rgba(25, 25, 112, 0.2);
        }

        .search-btn {
            background: none;
            border: none;
            cursor: pointer;
            position: absolute;
            right: 15px;
            color: #666;
        }

        .search-btn:hover {
            color: midnightblue;
        }

        .packages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .package-card {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .package-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .package-image {
            height: 200px;
            background-size: cover;
            background-position: center;
        }

        .package-content {
            padding: 20px;
        }

        .package-name {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: midnightblue;
        }

        .package-description {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .package-price {
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .price-highlight {
            color: midnightblue;
            font-size: 1.2rem;
        }

        .package-action {
            text-align: center;
        }

        @media (max-width: 768px) {
            .packages-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container nav-container">
            <a
                href="#"
                class="logo"
            >
                Wander<span>Mate</span>
            </a>

            <nav>
                <ul>
                    <li>
                        <a href="home.php">Home</a>
                    </li>
                    <li>
                        <a href="package-list.php">Packages</a>
                    </li>

                    <?php if (!$loggedIn): ?>
                        <li>
                            <a
                                href="login.php"
                                class="btn btn-sm"
                            >
                                Login
                            </a>
                        </li>
                    <?php else: ?>
                        <?php if ($role_id === 1): ?>
                            <li>
                                <a href="order-history.php">My Orders</a>
                            </li>
                        <?php elseif ($role_id === 2): ?>
                            <li>
                                <a href="dashboard.php">Dashboard</a>
                            </li>
                        <?php endif; ?>

                        <li>
                            <a
                                href="logout.php"
                                class="btn btn-sm"
                            >
                                Logout
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Page Content -->
    <main>
        <div class="page-header">
            <div class="container">
                <h1>Explore Our Travel Packages</h1>
                <p>Discover carefully curated travel experiences that combine adventure, comfort, and unforgettable memories. From tropical getaways to cultural explorations, we have the perfect package for you.</p>
            </div>
        </div>

        <div class="container">
            <div class="search-container">
                <input
                    type="text"
                    class="search-input"
                    placeholder="Search for packages, destinations..."
                />
                <button class="search-btn">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        fill="currentColor"
                        class="bi bi-search"
                        viewBox="0 0 16 16"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.397l3.646 3.646a1 1 0 0 0 1.415-1.415l-3.646-3.646zm-5.53-.53a5.5 5.5 0 1 1 7.778 0 5.5 5.5 0 0 1-7.778 0z"
                        />
                    </svg>
                </button>
            </div>

            <div class="packages-grid">
                <!-- Package 1 -->
                <div class="package-card">
                    <div
                        class="package-image"
                        style="background-image: url('https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80');"
                    ></div>
                    <div class="package-content">
                        <h3 class="package-name">Bali Paradise Retreat</h3>
                        <p class="package-description">Experience the magic of Bali with our 7-day retreat package. Visit sacred temples, relax on pristine beaches, and immerse yourself in local culture.</p>
                        <p class="package-price">From <span class="price-highlight">$1,299</span> per person</p>
                        <div class="package-action">
                            <a
                                href="#"
                                class="btn"
                            >
                                View Details
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Package 2 -->
                <div class="package-card">
                    <div
                        class="package-image"
                        style="background-image: url('https://images.unsplash.com/photo-1467269204594-9661b134dd2b?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80');"
                    ></div>
                    <div class="package-content">
                        <h3 class="package-name">Japanese Cultural Journey</h3>
                        <p class="package-description">Explore the land of the rising sun in this 10-day journey through Tokyo, Kyoto, and Osaka. Experience traditional tea ceremonies and modern city life.</p>
                        <p class="package-price">From <span class="price-highlight">$2,499</span> per person</p>
                        <div class="package-action">
                            <a
                                href="#"
                                class="btn"
                            >
                                View Details
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Package 3 -->
                <div class="package-card">
                    <div
                        class="package-image"
                        style="background-image: url('https://images.unsplash.com/photo-1528702748617-c64d49f918af?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80');"
                    ></div>
                    <div class="package-content">
                        <h3 class="package-name">Greek Islands Cruise</h3>
                        <p class="package-description">Set sail through the crystal-clear waters of the Aegean Sea. Visit Santorini, Mykonos, and hidden gems on this 8-day Mediterranean cruise adventure.</p>
                        <p class="package-price">From <span class="price-highlight">$1,899</span> per person</p>
                        <div class="package-action">
                            <a
                                href="#"
                                class="btn"
                            >
                                View Details
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Package 4 -->
                <div class="package-card">
                    <div
                        class="package-image"
                        style="background-image: url('https://images.unsplash.com/photo-1505761671935-60b3a7427bad?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80');"
                    ></div>
                    <div class="package-content">
                        <h3 class="package-name">New York City Break</h3>
                        <p class="package-description">Experience the energy of the Big Apple with our 5-day city break. Broadway shows, iconic landmarks, and world-class dining await you.</p>
                        <p class="package-price">From <span class="price-highlight">$1,099</span> per person</p>
                        <div class="package-action">
                            <a
                                href="#"
                                class="btn"
                            >
                                View Details
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Package 5 -->
                <div class="package-card">
                    <div
                        class="package-image"
                        style="background-image: url('https://images.unsplash.com/photo-1531572753322-ad063cecc140?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80');"
                    ></div>
                    <div class="package-content">
                        <h3 class="package-name">Safari Adventure Kenya</h3>
                        <p class="package-description">Witness the magnificent wildlife of Kenya on this 6-day safari. See the Big Five in their natural habitat and experience authentic African culture.</p>
                        <p class="package-price">From <span class="price-highlight">$2,799</span> per person</p>
                        <div class="package-action">
                            <a
                                href="#"
                                class="btn"
                            >
                                View Details
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Package 6 -->
                <div class="package-card">
                    <div
                        class="package-image"
                        style="background-image: url('https://images.unsplash.com/photo-1527631746610-bca00a040d60?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80');"
                    ></div>
                    <div class="package-content">
                        <h3 class="package-name">Thailand Beach Escape</h3>
                        <p class="package-description">Relax on the stunning beaches of Phuket and Koh Samui. This 9-day package includes island hopping, spa treatments, and authentic Thai cuisine.</p>
                        <p class="package-price">From <span class="price-highlight">$1,499</span> per person</p>
                        <div class="package-action">
                            <a
                                href="#"
                                class="btn"
                            >
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>WanderMate</h3>
                    <p>Your trusted travel partner since 2018. We specialize in creating unforgettable travel experiences across the globe.</p>
                </div>
                <div class="footer-column">
                    <h3>Contact Us</h3>
                    <p>123 Travel Street, Suite 100</p>
                    <p>New York, NY 10001</p>
                    <p>Phone: (123) 456-7890</p>
                    <p>Email: info@wandermate.com</p>
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <a href="#">Home</a>
                    <a href="#">About Us</a>
                    <a href="#">Packages</a>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; <?php echo date("Y"); ?> WanderMate Travel Agency. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
