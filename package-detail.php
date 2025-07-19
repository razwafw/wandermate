<?php
require_once 'config.php';

session_start();
$loggedIn = isset($_SESSION['user_id']) && isset($_SESSION['role_id']);
$role_id = $_SESSION['role_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: package-list.php');
    exit();
}

$packageId = intval($_GET['id']);

require_once 'DatabaseConnection.php';

$conn = new DatabaseConnection();

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$sql = 'SELECT p.*, p.price / p.group_size AS price_per_person FROM packages p WHERE  p.id = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $packageId);
$stmt->execute();
$result = $stmt->get_result();
$package = $result->fetch_assoc();

$stmt->close();
$conn->close();

if (!$package) {
    header('Location: package-list.php');
    exit();
}

$images = [];
foreach (explode("\n", $package['images']) as $image) {
    $image = trim($image);
    if (!empty($image)) {
        $images[] = $image;
    }
}

require_once 'utilities.php';

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
        href="package-detail.css"
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
        <!-- Page Header -->
        <?php
        $title = htmlspecialchars($package['name']);
        $subtitle = htmlspecialchars($package['subtitle']);
        include 'page-header.php';
        ?>

        <div class="container">
            <!-- Image Gallery -->
            <div class="gallery-container">
                <img
                    src="<?php echo htmlspecialchars($images[0]); ?>"
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

                        <?php foreach ($itinerary as $day => $desc): ?>
                            <div class="itinerary-day">
                                <h3 class="day-title"><?php echo htmlspecialchars($day); ?></h3>
                                <p><?php echo htmlspecialchars($desc); ?></p>
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
                    <div class="package-price-large">$<?php echo number_format($package['price_per_person']); ?></div>
                    <span class="price-per-person">per person</span>

                    <div class="package-actions">
                        <?php
                        session_start();
                        $loggedIn = isset($_SESSION['user_id']) && isset($_SESSION['role_id']);
                        $role_id = $_SESSION['role_id'];
                        ?>

                        <button
                            id="bookNowBtn"
                            class="btn btn-full"
                            <?php echo $loggedIn && $role_id != 2 ? '' : 'disabled'; ?>
                        >
                            Book Now
                        </button>
                        <?php if (!$loggedIn): ?>
                            <p class="login-prompt">
                                Please log in to book this package
                            </p>
                        <?php endif; ?>
                        <div class="return-to-list">
                            <hr style="margin: 20px 0; border: none; border-top: 1px solid #eee;">
                            <a
                                href="package-list.php"
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
        <?php
        $modalId = 'bookingModal';
        $modalTitle = 'Book Your Trip';
        $packageId = $package['id'];
        $modalContent = "
            <form id='bookingForm'>
                <input
                    type='hidden'
                    name='package_id'
                    value='$packageId'
                >


                <div class='form-group'>
                    <label for='departureDate'>Departure Date</label>
                    <input
                        type='date'
                        id='departureDate'
                        name='departureDate'
                        class='form-control'
                        required
                    >
                </div>

                <div class='form-group'>
                    <label for='groupAmount'>Number of Groups</label>
                    <select
                        id='groupAmount'
                        name='groupAmount'
                        class='form-control'
                        required
                    >
        ";

        for ($i = 1; $i <= 10; $i++) {
            $modalContent .= "<option value='$i'>$i</option>";
        }

        $modalContent .= "
                    </select>
                </div>
        
                <div class='form-group'>
                    <label for='specialRequests'>Special Requests (Optional)</label>
                    <textarea
                        id='specialRequests'
                        name='specialRequests'
                        class='form-control'
                        rows='3'
                    ></textarea>
                </div>
            </form>
        ";

        $modalFooter = "
            <button
                type='button'
                class='btn btn-outline'
                id='cancelBooking'
            >
                Cancel
            </button>
            <button
                type='submit'
                class='btn'
                id='submitBooking'
            >
                Complete Booking
            </button>
        ";

        include 'modal.php';
        ?>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="header.js"></script>
    <script
        src="package-detail.js"
        type="module"
    ></script>
</body>
</html>
