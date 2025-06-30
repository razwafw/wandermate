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
            const header = document.querySelector('header');
            const headerHeight = header.offsetHeight;
            document.documentElement.style.scrollPaddingTop = headerHeight + 'px';
        }

        // Set initial scroll padding
        window.addEventListener('DOMContentLoaded', setScrollPadding);

        // Update scroll padding on window resize
        window.addEventListener('resize', setScrollPadding);
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
            // In a real application, this data would come from a database
            // Here we're using sample data
            $orders = [
                [
                    'id' => 1001,
                    'package_name' => 'Bali Paradise Retreat',
                    'package_id' => 1,
                    'travelers' => 2,
                    'departure_date' => '2025-07-15',
                    'status' => 'confirmed',
                    'booking_date' => '2025-06-01',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'email' => 'john.doe@example.com',
                    'phone' => '(555) 123-4567',
                    'special_requests' => 'Vegetarian meals preferred',
                    'total_price' => 2598.00,
                ],
                [
                    'id' => 1002,
                    'package_name' => 'Japanese Cultural Journey',
                    'package_id' => 2,
                    'travelers' => 1,
                    'departure_date' => '2025-08-10',
                    'status' => 'pending',
                    'booking_date' => '2025-06-05',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'email' => 'john.doe@example.com',
                    'phone' => '(555) 123-4567',
                    'special_requests' => 'Early check-in if possible',
                    'total_price' => 2499.00,
                ],
                [
                    'id' => 1003,
                    'package_name' => 'Greek Islands Cruise',
                    'package_id' => 3,
                    'travelers' => 4,
                    'departure_date' => '2025-09-22',
                    'status' => 'cancelled',
                    'booking_date' => '2025-05-20',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'email' => 'john.doe@example.com',
                    'phone' => '(555) 123-4567',
                    'special_requests' => 'Cabin with ocean view',
                    'total_price' => 7596.00,
                ],
            ];

            // Sort orders by departure date (most recent first)
            usort($orders, function ($a, $b) {
                return strtotime($b['departure_date']) - strtotime($a['departure_date']);
            });

            if (count($orders) > 0):
                ?>
                <div class="orders-container">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Package</th>
                                <th>Travelers</th>
                                <th>Departure Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr
                                    data-order-id="<?php echo $order['id']; ?>"
                                    onclick="showOrderDetails(<?php echo htmlspecialchars(json_encode($order)); ?>)"
                                >
                                    <td><?php echo htmlspecialchars($order['package_name']); ?></td>
                                    <td><?php echo $order['travelers']; ?></td>
                                    <td><?php echo date('F j, Y', strtotime($order['departure_date'])); ?></td>
                                    <td>
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if ($order['status'] === 'confirmed'): ?>
                                                <button
                                                    class="action-btn btn-download"
                                                    onclick="downloadItinerary(event, <?php echo $order['id']; ?>)"
                                                    aria-label="Download itinerary"
                                                >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="16"
                                                        height="16"
                                                        fill="currentColor"
                                                        viewBox="0 0 16 16"
                                                    >
                                                        <path d="M8 0a1 1 0 0 1 1 1v6h1.5a.5.5 0 0 1 .4.8l-3 4a.5.5 0 0 1-.8 0l-3-4a.5.5 0 0 1 .4-.8H6V1a1 1 0 0 1 1-1z" />
                                                        <path d="M1.5 14.5A1.5 1.5 0 0 0 3 16h10a1.5 1.5 0 0 0 1.5-1.5V9.05a2.5 2.5 0 0 1-.5.05H13v5.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V9.1c-.18.01-.36.01-.5 0v5.4z" />
                                                    </svg>
                                                    Download
                                                </button>
                                            <?php elseif ($order['status'] !== 'cancelled'): ?>
                                                <button
                                                    class="action-btn btn-cancel"
                                                    onclick="confirmCancellation(event, <?php echo $order['id']; ?>)"
                                                    aria-label="Cancel booking"
                                                >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="16"
                                                        height="16"
                                                        fill="currentColor"
                                                        viewBox="0 0 16 16"
                                                    >
                                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                                                    </svg>
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
            const formattedDate = new Date(order.departure_date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            const bookingDate = new Date(order.booking_date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
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
                    <div class="detail-label">Traveler Name:</div>
                    <div class="detail-value">${order.first_name} ${order.last_name}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Email:</div>
                    <div class="detail-value">${order.email}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Phone:</div>
                    <div class="detail-value">${order.phone}</div>
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
                    <div class="detail-value">$${order.total_price.toFixed(2)}</div>
                </div>`;

            if (order.special_requests) {
                content += `
                <div class="detail-row">
                    <div class="detail-label">Special Requests:</div>
                    <div class="detail-value">${order.special_requests}</div>
                </div>`;
            }

            document.getElementById('orderDetailsContent').innerHTML = content;
            openModal('orderDetailsModal');
        }

        // Function to open a modal
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }

        // Function to close a modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            document.body.style.overflow = 'auto'; // Enable scrolling
        }

        // Function to download itinerary
        function downloadItinerary(event, orderId) {
            // Stop event propagation (to prevent showing details modal when clicking the download button)
            event.stopPropagation();

            // Create a temporary link to download the file
            const link = document.createElement('a');
            link.href = `itinerary-${orderId}.txt`;
            link.download = `WanderMate-Itinerary-${orderId}.txt`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Alert user about the download (in a real app, we'd handle this more gracefully)
            setTimeout(() => {
                alert('Your itinerary is being downloaded. If the download doesn\'t start automatically, please check your browser settings.');
            }, 500);
        }

        // Function to show cancellation confirmation modal
        function confirmCancellation(event, orderId) {
            // Stop event propagation
            event.stopPropagation();

            // Store the order ID
            currentOrderId = orderId;

            // Update the confirm button to use the current order ID
            document.getElementById('confirmCancelBtn').onclick = function () {
                cancelOrder(currentOrderId);
            };

            // Show the modal
            openModal('cancellationModal');
        }

        // Function to cancel the order
        function cancelOrder(orderId) {
            // In a real application, this would send an AJAX request to the server
            // For this demo, we'll just update the UI

            // Find the row with the order
            const row = document.querySelector(`tr[data-order-id="${orderId}"]`);

            if (row) {
                // Update the status badge
                const statusCell = row.querySelector('td:nth-child(4)');
                statusCell.innerHTML = '<span class="status-badge status-cancelled">Cancelled</span>';

                // Remove the cancel button
                const cancelBtn = row.querySelector('.btn-cancel');
                if (cancelBtn) {
                    cancelBtn.parentNode.removeChild(cancelBtn);
                }
            }

            // Close the confirmation modal
            closeModal('cancellationModal');

            // Show confirmation message
            alert('Your booking has been cancelled successfully.');
        }

        // Close modals when clicking outside
        window.addEventListener('click', function (event) {
            const orderDetailsModal = document.getElementById('orderDetailsModal');
            const cancellationModal = document.getElementById('cancellationModal');

            if (event.target === orderDetailsModal) {
                closeModal('orderDetailsModal');
            }

            if (event.target === cancellationModal) {
                closeModal('cancellationModal');
            }
        });
    </script>
</body>
</html>