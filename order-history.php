<?php
session_start();
$loggedIn = isset($_SESSION['user_id']);

if (!$loggedIn) {
    header("Location: login.php");
    exit();
}

$role_id = $_SESSION['role_id'] ?? 1;

if ($role_id !== 1) {
    header("Location: home.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Database connection
$host = 'localhost';
$user = 'projec15_root';
$pass = '@kaesquare123';
$db = 'projec15_wandermate';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Fetch orders for the logged-in user
$sql = "SELECT o.id, o.booking_date, o.departure_date, o.amount, o.request, o.package_id, o.status_id, o.itinerary_url, 
               p.name AS package_name, p.price AS package_price, p.group_size, s.name AS status_name
        FROM orders o
        JOIN packages p ON o.package_id = p.id
        LEFT JOIN statuses s ON o.status_id = s.id
        WHERE o.customer_id = ?
        ORDER BY o.departure_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
$stmt->close();

// Optionally, close the DB connection at the end of the script
// $conn->close();
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
        /* Page Header */
        .page-header {
            background-color: #f8f9fa;
            padding: 40px 0;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 2.5rem;
            color: midnightblue;
            margin-bottom: 10px;
        }

        .page-subtitle {
            color: #666;
            font-size: 1.2rem;
            margin-bottom: 0;
        }

        /* Orders Table */
        .orders-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 60px;
            overflow-x: auto;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }

        .orders-table th {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: left;
            color: #444;
            font-weight: 600;
            border-bottom: 2px solid #eee;
        }

        .orders-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            color: #333;
        }

        .orders-table tr:hover {
            background-color: #f9f9f9;
            cursor: pointer;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-confirmed {
            background-color: #e3f7e8;
            color: #0a8534;
        }

        .status-pending {
            background-color: #fff8e6;
            color: #cc8800;
        }

        .status-cancelled {
            background-color: #ffeaea;
            color: #cc0000;
        }

        .no-orders {
            text-align: center;
            padding: 40px;
            color: #777;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            padding: 8px 12px;
            background-color: #f0f0f0;
            color: #333;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .action-btn:hover {
            background-color: #e5e5e5;
        }

        .btn-download {
            background-color: #e6f7ff;
            color: #0066cc;
        }

        .btn-download:hover {
            background-color: #cceeff;
        }

        .btn-cancel {
            background-color: #fff0f0;
            color: #cc0000;
        }

        .btn-cancel:hover {
            background-color: #ffe0e0;
        }

        /* Order Details Modal */
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
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
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

        .detail-row {
            display: flex;
            margin-bottom: 15px;
        }

        .detail-label {
            flex: 1;
            font-weight: 500;
            color: #555;
        }

        .detail-value {
            flex: 2;
            color: #333;
        }

        .modal-divider {
            height: 1px;
            background-color: #eee;
            margin: 20px 0;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state .icon {
            font-size: 3rem;
            color: #ccc;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 1.3rem;
            color: #555;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #777;
            max-width: 400px;
            margin: 0 auto 20px;
        }

        /* Confirm Modal */
        .confirm-modal .modal-content {
            max-width: 400px;
        }

        .confirm-text {
            margin-bottom: 20px;
            color: #333;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .orders-table {
                font-size: 0.9rem;
            }

            .orders-table th,
            .orders-table td {
                padding: 10px;
            }

            .action-buttons {
                flex-direction: column;
                gap: 5px;
            }

            .detail-row {
                flex-direction: column;
            }

            .detail-label {
                margin-bottom: 5px;
            }

            .modal-content {
                margin: 10% auto;
                padding: 20px;
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

    <script>
        // Function to set scroll padding based on header height
        function setScrollPadding() {
            const header = document.querySelector("header");
            const headerHeight = header.offsetHeight;
            document.documentElement.style.scrollPaddingTop = headerHeight + "px";
        }

        // Set initial scroll padding
        window.addEventListener("DOMContentLoaded", setScrollPadding);

        // Update scroll padding on window resize
        window.addEventListener("resize", setScrollPadding);
    </script>

    <!-- Page Content -->
    <main>
        <!-- Page Header -->
        <div class="page-header">
            <div class="container">
                <h1 class="page-title">My Travel Bookings</h1>
                <p class="page-subtitle">View and manage all your current and past travel orders</p>
            </div>
        </div>

        <div class="container">
            <?php
            // Remove sample data and use fetched $orders from database
            if (count($orders) > 0):
                ?>
                <div class="orders-container">
                    <table class="orders-table">
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
                                                    class="action-btn btn-download"
                                                    onclick="viewItinerary(event, '<?php echo htmlspecialchars($order['itinerary_url']); ?>')"
                                                    aria-label="View itinerary"
                                                >
                                                    View Itinerary
                                                </button>
                                            <?php elseif (strtolower($order['status_name']) !== 'cancelled'): ?>
                                                <button
                                                    class="action-btn btn-cancel"
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
                <!-- Empty state when no orders are available -->
                <div class="orders-container empty-state">
                    <div class="icon">✈️</div>
                    <h3>No bookings found</h3>
                    <p>You haven't made any travel bookings yet. Start exploring our packages and plan your next adventure!</p>
                    <a
                        href="package-list.php"
                        class="btn"
                    >Browse Packages
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Order Details Modal -->
        <div
            id="orderDetailsModal"
            class="modal"
        >
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Booking Details</h2>
                    <span
                        class="close"
                        onclick="closeModal('orderDetailsModal')"
                    >&times;</span>
                </div>

                <div id="orderDetailsContent">
                    <!-- Content will be populated by JavaScript -->
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn"
                        onclick="closeModal('orderDetailsModal')"
                    >Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Cancellation Confirmation Modal -->
        <div
            id="cancellationModal"
            class="modal confirm-modal"
        >
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Confirm Cancellation</h2>
                    <span
                        class="close"
                        onclick="closeModal('cancellationModal')"
                    >&times;</span>
                </div>

                <p class="confirm-text">Are you sure you want to cancel this booking? This action cannot be undone.</p>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-outline"
                        onclick="closeModal('cancellationModal')"
                    >No, Keep Booking
                    </button>
                    <button
                        type="button"
                        class="btn"
                        id="confirmCancelBtn"
                    >Yes, Cancel Booking
                    </button>
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

    <script>
        // Store current order ID for cancellation
        let currentOrderId = null;

        // Function to show order details in the modal
        function showOrderDetails(order) {
            // Set modal content
            const formattedDate = new Date(order.departure_date).toLocaleDateString("en-US", {
                year: "numeric",
                month: "long",
                day: "numeric",
            });

            const bookingDate = new Date(order.booking_date).toLocaleDateString("en-US", {
                year: "numeric",
                month: "long",
                day: "numeric",
            });

            const statusClass = `status-badge status-${order.status}`;
            const statusLabel = order.status.charAt(0).toUpperCase() + order.status.slice(1);

            let content = `
                <div class="detail-row">
                    <div class="detail-label">Package:</div>
                    <div class="detail-value">${order.package_name}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value"><span class="${statusClass}">${statusLabel}</span></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Booking ID:</div>
                    <div class="detail-value">#${order.id}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Booking Date:</div>
                    <div class="detail-value">${bookingDate}</div>
                </div>

                <div class="modal-divider"></div>

                <div class="detail-row">
                    <div class="detail-label">Departure Date:</div>
                    <div class="detail-value">${formattedDate}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Number of Travelers:</div>
                    <div class="detail-value">${order.travelers}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Total Price:</div>
                    <div class="detail-value">$${parseInt(order.total_price).toFixed(2)}</div>
                </div>`;

            if (order.special_requests) {
                content += `
                <div class="detail-row">
                    <div class="detail-label">Special Requests:</div>
                    <div class="detail-value">${order.special_requests}</div>
                </div>`;
            }

            document.getElementById("orderDetailsContent").innerHTML = content;
            openModal("orderDetailsModal");
        }

        // Function to open a modal
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "block";
            document.body.style.overflow = "hidden"; // Prevent scrolling
        }

        // Function to close a modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
            document.body.style.overflow = "auto"; // Enable scrolling
        }

        // Function to view itinerary
        function viewItinerary(event, fileUrl) {
            event.stopPropagation();
            if (fileUrl) {
                window.open(fileUrl, "_blank");
            } else {
                alert("Itinerary file not found");
            }
        }

        // Function to show cancellation confirmation modal
        function confirmCancellation(event, orderId) {
            // Stop event propagation
            event.stopPropagation();

            // Store the order ID
            currentOrderId = orderId;

            // Update the confirm button to use the current order ID
            document.getElementById("confirmCancelBtn").onclick = function () {
                cancelOrder(currentOrderId);
            };

            // Show the modal
            openModal("cancellationModal");
        }

        // Function to cancel the order
        function cancelOrder(orderId) {
            if (!orderId) {
                alert("Order ID is missing.");
                return;
            }
            // Send AJAX request to cancel-order.php
            fetch("cancel-order.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: "order_id=" + encodeURIComponent(orderId),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Find the row with the order
                        const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                        if (row) {
                            // Update the status badge
                            const statusCell = row.querySelector("td:nth-child(4)");
                            statusCell.innerHTML = "<span class=\"status-badge status-cancelled\">Cancelled</span>";
                            // Remove the cancel button
                            const cancelBtn = row.querySelector(".btn-cancel");
                            if (cancelBtn) {
                                cancelBtn.parentNode.removeChild(cancelBtn);
                            }
                        }
                        closeModal("cancellationModal");
                        alert("Your booking has been cancelled successfully.");
                    } else {
                        alert(data.message || "Failed to cancel booking.");
                    }
                })
                .catch(() => {
                    alert("An error occurred while cancelling the booking.");
                });
        }

        // Close modals when clicking outside
        window.addEventListener("click", function (event) {
            const orderDetailsModal = document.getElementById("orderDetailsModal");
            const cancellationModal = document.getElementById("cancellationModal");

            if (event.target === orderDetailsModal) {
                closeModal("orderDetailsModal");
            }

            if (event.target === cancellationModal) {
                closeModal("cancellationModal");
            }
        });
    </script>
</body>
</html>
