<?php
session_start();
$loggedIn = isset($_SESSION['user_id']);
$role_id = $_SESSION['role_id'] ?? 1;

// Fetch package ID from query param
$packageId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Database connection
$conn = new mysqli('localhost', 'root', '', 'wandermate');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Fetch package data
$sql = 'SELECT p.*, (SELECT url FROM images WHERE package_id = p.id LIMIT 1) AS image_url FROM packages p WHERE  p.id = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $packageId);
$stmt->execute();
$result = $stmt->get_result();
$package = $result->fetch_assoc();

// Fetch all images for this package
$images = [];
if ($package) {
    $imgStmt = $conn->prepare('SELECT url FROM images WHERE package_id = ?');
    $imgStmt->bind_param('i', $packageId);
    $imgStmt->execute();
    $imgResult = $imgStmt->get_result();
    while ($imgRow = $imgResult->fetch_assoc()) {
        $images[] = $imgRow['url'];
    }
    $imgStmt->close();
}
$stmt->close();
$conn->close();

if (!$package) {
    header('Location: package-list.php');
    exit();
}

// Parse highlights, includes, excludes, itinerary
function parseNewlineList($str)
{
    return array_filter(array_map('trim', explode("\n", $str)));
}

function parseItinerary($str)
{
    $result = [];
    foreach (parseNewlineList($str) as $line) {
        $parts = explode('|', $line, 2);
        $day = isset($parts[0]) ? trim($parts[0]) : '';
        $desc = isset($parts[1]) ? trim($parts[1]) : '';
        if ($day !== '' && $desc !== '') {
            $result[] = [
                'day' => $day,
                'desc' => $desc,
            ];
        }
    }
    return $result;
}

$highlights = parseNewlineList($package['highlights']);
$includes = parseNewlineList($package['includes']);
$excludes = parseNewlineList($package['excludes']);
$itinerary = parseItinerary($package['itinerary']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>WanderMate - Package Details</title>

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
        /* Package Detail Layout */
        .package-header {
            background-color: #f8f9fa;
            padding: 40px 0;
            margin-bottom: 30px;
        }

        .breadcrumb {
            display: flex;
            margin-bottom: 20px;
            align-items: center;
        }

        .breadcrumb a {
            color: #666;
            text-decoration: none;
            transition: color 0.3s;
        }

        .breadcrumb a:hover {
            color: midnightblue;
        }

        .breadcrumb .separator {
            margin: 0 10px;
            color: #ccc;
        }

        .breadcrumb .current {
            color: midnightblue;
            font-weight: 500;
        }

        .package-title {
            font-size: 2.5rem;
            color: midnightblue;
            margin-bottom: 10px;
        }

        .package-subtitle {
            color: #666;
            font-size: 1.2rem;
            margin-bottom: 20px;
        }

        /* Image Gallery */
        .gallery-container {
            margin-bottom: 40px;
            position: relative;
        }

        .main-image {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .thumbnail-container {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 15px;
        }

        .thumbnail {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
            transition: opacity 0.3s;
        }

        .thumbnail:hover, .thumbnail.active {
            opacity: 0.8;
            border: 2px solid midnightblue;
        }

        /* Package Info Layout */
        .package-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 60px;
        }

        .package-details {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }

        .package-info-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
            align-self: flex-start;
            position: sticky;
            top: 100px;
        }

        .package-price-large {
            font-size: 2rem;
            color: midnightblue;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .price-per-person {
            display: block;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .package-actions {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        /* Login prompt styles */
        .login-prompt {
            display: none;
            margin-top: 10px;
            text-align: center;
            color: #666;
            font-size: 0.9rem;
        }

        button[disabled] + .login-prompt {
            display: block;
        }

        /* Detail Sections */
        .section-title {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .detail-section {
            margin-bottom: 30px;
        }

        .detail-section p {
            margin-bottom: 15px;
            color: #444;
        }

        .highlights-list {
            list-style-type: none;
            padding-left: 5px;
            margin-bottom: 20px;
        }

        .highlights-list li {
            padding: 10px 0;
            position: relative;
            padding-left: 30px;
            color: #444;
        }

        .highlights-list li:before {
            content: "✓";
            color: midnightblue;
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-item {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .info-value {
            color: #333;
            font-size: 1.1rem;
        }

        .itinerary-day {
            margin-bottom: 25px;
        }

        .day-title {
            font-weight: 600;
            color: midnightblue;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            max-width: 600px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.3s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 1.5rem;
            color: midnightblue;
        }

        .close {
            color: #aaa;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #444;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: midnightblue;
            box-shadow: 0 0 0 2px rgba(25, 25, 112, 0.1);
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .modal-footer {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .main-image {
                height: 300px;
            }

            .package-content {
                grid-template-columns: 1fr;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .package-info-card {
                position: sticky;
                bottom: 0;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }

        @media (max-height: 768px) {
            .package-price-large, .price-per-person {
                display: inline-block;
            }

            .return-to-list {
                display: none;
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
        <!-- Package Header -->
        <div class="package-header">
            <div class="container">
                <!-- Breadcrumb navigation -->
                <div class="breadcrumb">
                    <a href="#">Travel Packages</a>
                    <span class="separator">›</span>
                    <span class="current"><?php echo htmlspecialchars($package['name']); ?></span>
                </div>

                <h1 class="package-title"><?php echo htmlspecialchars($package['name']); ?></h1>
                <p class="package-subtitle"><?php echo htmlspecialchars($package['subtitle']); ?></p>
            </div>
        </div>

        <div class="container">
            <!-- Image Gallery -->
            <div class="gallery-container">
                <img
                    src="<?php echo htmlspecialchars($package['image_url']); ?>"
                    alt="<?php echo htmlspecialchars($package['name']); ?>"
                    class="main-image"
                    id="mainImage"
                >
                <div class="thumbnail-container">
                    <?php foreach ($images as $index => $image): ?>
                        <img
                            src="<?php echo htmlspecialchars($image); ?>"
                            alt="<?php echo htmlspecialchars($package['name']) . ' image ' . ($index + 1); ?>"
                            class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>"
                            onclick="changeImage('<?php echo htmlspecialchars($image); ?>', this)"
                        >
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Package Content -->
            <div class="package-content">
                <!-- Left Column - Package Details -->
                <div class="package-details">
                    <!-- Overview Section -->
                    <div class="detail-section">
                        <h2 class="section-title">Overview</h2>
                        <p><?php echo htmlspecialchars($package['description']); ?></p>

                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Duration</div>
                                <div class="info-value"><?php echo htmlspecialchars($package['duration']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Group Size</div>
                                <div class="info-value"><?php echo htmlspecialchars($package['group_size']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Start Location</div>
                                <div class="info-value"><?php echo htmlspecialchars($package['start_location']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">End Location</div>
                                <div class="info-value"><?php echo htmlspecialchars($package['end_location']); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Highlights Section -->
                    <div class="detail-section">
                        <h2 class="section-title">Trip Highlights</h2>
                        <ul class="highlights-list">
                            <?php foreach ($highlights as $highlight): ?>
                                <li><?php echo htmlspecialchars($highlight); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Detailed Itinerary -->
                    <div class="detail-section">
                        <h2 class="section-title">Detailed Itinerary</h2>

                        <?php foreach ($itinerary as $item): ?>
                            <div class="itinerary-day">
                                <h3 class="day-title"><?php echo htmlspecialchars($item['day']); ?></h3>
                                <p><?php echo htmlspecialchars($item['desc']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- What's Included -->
                    <div class="detail-section">
                        <h2 class="section-title">What's Included</h2>
                        <ul class="highlights-list">
                            <?php foreach ($includes as $item): ?>
                                <li><?php echo htmlspecialchars($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- What's Not Included -->
                    <div class="detail-section">
                        <h2 class="section-title">What's Not Included</h2>
                        <ul class="highlights-list">
                            <?php foreach ($excludes as $item): ?>
                                <li><?php echo htmlspecialchars($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Right Column - Booking Section -->
                <div class="package-info-card">
                    <div class="package-price-large">$<?php echo number_format($package['price']); ?></div>
                    <span class="price-per-person">per person</span>

                    <div class="package-actions">
                        <button
                            id="bookNowBtn"
                            class="btn btn-full"
                            <?php echo $loggedIn ? '' : 'disabled'; ?>
                        >
                            Book Now
                        </button>
                        <?php if (!$loggedIn): ?>
                            <p class="login-prompt">Please
                                <a href="#">log in</a>
                                to book this package
                            </p>
                        <?php endif; ?>
                        <div class="return-to-list">
                            <hr style="margin: 20px 0; border: none; border-top: 1px solid #eee;">
                            <a
                                href="#"
                                class="btn btn-outline btn-full"
                            >
                                Back to Packages
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Modal -->
        <div
            id="bookingModal"
            class="modal"
        >
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Book Your Trip</h2>
                    <span class="close">&times;</span>
                </div>

                <form id="bookingForm">
                    <input
                        type="hidden"
                        name="package_id"
                        value="<?php echo $package['id']; ?>"
                    >

                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input
                                type="text"
                                id="firstName"
                                name="firstName"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input
                                type="text"
                                id="lastName"
                                name="lastName"
                                class="form-control"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            class="form-control"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="departureDate">Departure Date</label>
                        <input
                            type="date"
                            id="departureDate"
                            name="departureDate"
                            class="form-control"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="travelers">Number of Travelers</label>
                        <select
                            id="travelers"
                            name="travelers"
                            class="form-control"
                            required
                        >
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="specialRequests">Special Requests (Optional)</label>
                        <textarea
                            id="specialRequests"
                            name="specialRequests"
                            class="form-control"
                            rows="3"
                        ></textarea>
                    </div>

                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-outline"
                            id="cancelBooking"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="btn"
                        >
                            Complete Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Image Gallery Functionality
        function changeImage(src, thumbnail) {
            // Update main image source
            document.getElementById('mainImage').src = src;

            // Remove active class from all thumbnails
            document.querySelectorAll('.thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });

            // Add active class to clicked thumbnail
            thumbnail.classList.add('active');
        }

        // Modal Functionality
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('bookingModal');
            const bookNowBtn = document.getElementById('bookNowBtn');
            const closeBtn = document.querySelector('.close');
            const cancelBtn = document.getElementById('cancelBooking');
            const bookingForm = document.getElementById('bookingForm');

            // Open modal when Book Now is clicked
            bookNowBtn.addEventListener('click', function () {
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden'; // Prevent scrolling
            });

            // Close modal when X is clicked
            closeBtn.addEventListener('click', function () {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto'; // Enable scrolling
            });

            // Close modal when Cancel is clicked
            cancelBtn.addEventListener('click', function () {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto'; // Enable scrolling
            });

            // Close modal when clicking outside content area
            window.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto'; // Enable scrolling
                }
            });

            // Handle form submission
            bookingForm.addEventListener('submit', function (event) {
                event.preventDefault();

                // Here you would typically send the form data to a server
                // For demonstration, we'll just show an alert
                alert('Thank you for your booking! Your request has been received.');

                // Close the modal
                modal.style.display = 'none';
                document.body.style.overflow = 'auto'; // Enable scrolling

                // Reset form
                bookingForm.reset();
            });

            // Set min date for departure date input to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('departureDate').setAttribute('min', today);
        });
    </script>

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
