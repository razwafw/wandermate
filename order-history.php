<?php
require_once 'config.php';

session_start();
$loggedIn = isset($_SESSION['user_id']) && isset($_SESSION['role_id']);

if (!$loggedIn) {
    header("Location: index.php");
    exit();
}

$role_id = $_SESSION['role_id'];

if ($role_id !== 1) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

require_once 'DatabaseConnection.php';

$conn = new DatabaseConnection();

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$sql = "
    SELECT o.id, o.booking_date, o.departure_date, o.amount, o.request, o.package_id, o.status_id, o.itinerary_url, p.name AS package_name, p.price AS package_price, p.group_size, s.name AS status_name
    FROM orders o
    JOIN packages p ON o.package_id = p.id
    LEFT JOIN statuses s ON o.status_id = s.id
    WHERE o.customer_id = ?
    ORDER BY o.departure_date
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

$stmt->close();
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
        href="page-header.css"
    >
    <link
        rel="stylesheet"
        href="modal.css"
    >
    <link
        rel="stylesheet"
        href="table.css"
    >
    <link
        rel="stylesheet"
        href="footer.css"
    >
    <link
        rel="stylesheet"
        href="order-history.css"
    >
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Page Content -->
    <main>
        <!-- Page Header -->
        <?php
        $title = "My Travel Bookings";
        $subtitle = "View and manage all your current and past travel orders";
        include 'page-header.php';
        ?>

        <div
            class="container"
            style="margin-bottom: 30px"
        >
            <?php if (count($orders) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Package</th>
                                <th>Amount</th>
                                <th>Departure Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr
                                    data-order-id="<?php echo $order['id']; ?>"
                                    onclick='showOrderDetails(<?php echo json_encode([
                                        "id" => $order["id"],
                                        "package_name" => $order["package_name"],
                                        "travelers" => $order["amount"],
                                        "departure_date" => $order["departure_date"],
                                        "status" => $order["status_name"] ?? "pending",
                                        "booking_date" => $order["booking_date"],
                                        "request" => $order["request"],
                                        "total_price" => $order["package_price"],
                                    ]); ?>)'
                                >
                                    <td><?php echo htmlspecialchars($order['package_name']); ?></td>
                                    <td><?php echo $order['amount']; ?></td>
                                    <td><?php echo date('F j, Y', strtotime($order['departure_date'])); ?></td>
                                    <td>
                                    <span class="status-badge status-<?php echo strtolower($order['status_name'] ?? 'Pending'); ?>">
                                        <?php echo ucfirst($order['status_name'] ?? 'Pending'); ?>
                                    </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if (strtolower($order['status_name']) === 'confirmed'): ?>
                                                <button
                                                    class="action-btn btn-warning"
                                                    onclick="viewItinerary(event, '<?php echo htmlspecialchars($order['itinerary_url']); ?>')"
                                                    aria-label="View itinerary"
                                                >
                                                    View Itinerary
                                                </button>
                                            <?php elseif (strtolower($order['status_name']) !== 'cancelled'): ?>
                                                <button
                                                    class="action-btn btn-danger"
                                                    onclick="confirmCancellation(event, <?php echo $order['id']; ?>)"
                                                    aria-label="Cancel booking"
                                                >
                                                    Cancel
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="table-container empty-state">
                    <div class="icon">✈️</div>
                    <h3>No bookings found</h3>
                    <p>You haven't made any travel bookings yet. Start exploring our packages and plan your next adventure!</p>
                    <a
                        href="package-list.php"
                        class="btn"
                    >
                        Browse Packages
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Order Details Modal -->
        <?php
        $modalId = 'orderDetailsModal';
        $modalTitle = 'Booking Details';
        $modalContent = "
            <div id='orderDetailsContent'>
                <!-- Content will be populated by JavaScript -->
            </div>
        ";
        $modalFooter = "
            <button
                type='button'
                id='closeOrderDetailsBtn'
                class='btn'
            >
                Close
            </button>
        ";

        include 'modal.php';
        ?>

        <!-- Cancellation Confirmation Modal -->
        <?php
        $modalId = 'cancellationModal';
        $modalTitle = 'Confirm Cancellation';
        $modalContent = "
            <p class='confirm-text'>Are you sure you want to cancel this booking? This action cannot be undone.</p>
        ";
        $modalFooter = "
            <button
                type='button'
                id='cancelCancelBtn'
                class='btn btn-outline'
            >
                No, Keep Booking
            </button>
            <button
                type='button'
                class='btn'
                id='confirmCancelBtn'
            >
                Yes, Cancel Booking
            </button>
        ";

        include 'modal.php';
        ?>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="header.js"></script>
    <script
        src="order-history.js"
        type="module"
    ></script>
</body>
</html>
