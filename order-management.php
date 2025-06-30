<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] ?? 1) != 2) {
    header('Location: home.php');
    exit();
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
            cursor: pointer;
            border: none;
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

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-success {
            background-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
        }
    </style>

    <!-- Dashboard Styles -->
    <style>
        .dashboard-container {
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: 280px;
            background-color: midnightblue;
            color: white;
            padding: 30px 0;
            height: 100vh;
            position: fixed;
            z-index: 1000;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar-brand {
            padding: 0 25px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .sidebar-brand h2 {
            color: white;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
        }

        .sidebar-brand span {
            color: #adb5bd;
            font-weight: 300;
        }

        .sidebar-menu {
            padding: 0 25px;
        }

        .sidebar-menu h3 {
            color: #adb5bd;
            font-size: 0.95rem;
            text-transform: uppercase;
            margin-bottom: 15px;
            font-weight: 400;
        }

        .sidebar-menu ul {
            list-style-type: none;
        }

        .sidebar-menu li {
            margin-bottom: 10px;
        }

        .sidebar-menu .nav-link {
            color: white;
            text-decoration: none;
            font-size: 1.1rem;
            padding: 12px 15px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }

        .sidebar-menu .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 500;
        }

        .sidebar-menu .icon {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 20px;
            width: 100%;
            padding: 0 25px;
            color: #adb5bd;
            font-size: 0.9rem;
        }

        main {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
        }

        /* For icons */
        .material-icons {
            font-family: 'Material Icons';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            display: inline-block;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
            direction: ltr;
            vertical-align: middle;
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
                padding: 20px 0;
            }

            .sidebar-brand {
                padding: 0 15px 15px;
                text-align: center;
            }

            .sidebar-brand h2 {
                font-size: 1.2rem;
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }

            .sidebar-menu {
                padding: 0 10px;
            }

            .sidebar-menu h3 {
                text-align: center;
                font-size: 0.7rem;
            }

            .sidebar-menu .nav-link {
                justify-content: center;
                padding: 12px;
            }

            .sidebar-menu .nav-link span {
                display: none;
            }

            .sidebar-menu .nav-link span.icon {
                display: block;
            }

            .sidebar-menu .icon {
                margin-right: 0;
                font-size: 1.5rem;
            }

            .sidebar-footer {
                display: none;
            }

            main {
                margin-left: 80px;
            }
        }
    </style>

    <!-- Page Styles -->
    <style>
        /* Page Header */
        .page-header {
            margin-bottom: 25px;
        }

        .page-title {
            font-size: 2.2rem;
            color: midnightblue;
            margin-bottom: 10px;
        }

        .page-subtitle {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        /* Orders Table */
        .orders-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-bottom: 40px;
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

        .btn-confirm {
            background-color: #e6f7ff;
            color: #0066cc;
        }

        .btn-confirm:hover {
            background-color: #cceeff;
        }

        .filter-container {
            display: flex;
            margin-bottom: 20px;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 15px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-btn:hover {
            background-color: #eaeaea;
        }

        .filter-btn.active {
            background-color: midnightblue;
            color: white;
            border-color: midnightblue;
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
            max-width: 700px;
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

        .file-upload {
            margin-top: 20px;
        }

        .file-upload label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .file-upload input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .search-container {
            margin-bottom: 20px;
        }

        .search-input {
            padding: 12px 15px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
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
            margin: 0 auto;
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
        <aside class="sidebar">
            <div class="sidebar-brand">
                <h2>Wander<span>Mate</span></h2>
            </div>

            <div class="sidebar-menu">
                <ul>
                    <li>
                        <a
                            href="dashboard.php"
                            class="nav-link active"
                        >
                            <span class="material-icons icon">dashboard</span>
                            <span>Dashboard</span>
                        </a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a
                            href="logout.php"
                            class="nav-link"
                        >
                            <span class="material-icons icon">logout</span>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
                <h3>Manage</h3>
                <ul>
                    <li>
                        <a
                            href="package-management.php"
                            class="nav-link"
                        >
                            <span class="material-icons icon">luggage</span>
                            <span>Packages</span>
                        </a>
                    </li>
                    <li>
                        <a
                            href="order-management.php"
                            class="nav-link"
                        >
                            <span class="material-icons icon">shopping_cart</span>
                            <span>Orders</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-footer">
                <p>&copy; 2025 WanderMate</p>
            </div>
        </aside>

        <!-- Main Content -->
        <main>
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">Order Management</h1>
                <p class="page-subtitle">View and manage all customer bookings</p>
            </div>

            <!-- Filters and Search -->
            <div class="filter-container">
                <button
                    class="filter-btn active"
                    data-filter="all"
                >All Orders
                </button>
                <button
                    class="filter-btn"
                    data-filter="pending"
                >Pending
                </button>
                <button
                    class="filter-btn"
                    data-filter="confirmed"
                >Confirmed
                </button>
                <button
                    class="filter-btn"
                    data-filter="cancelled"
                >Cancelled
                </button>
            </div>

            <div class="search-container">
                <input
                    type="text"
                    class="search-input"
                    placeholder="Search orders by ID, customer name, or package..."
                >
            </div>

            <!-- Orders Table -->
            <?php
            // In a real application, this data would come from a database
            // Here we're using sample data
            $orders = [
                [
                    'id' => 1001,
                    'customer_id' => 501,
                    'customer_name' => 'John Doe',
                    'package_id' => 1,
                    'package_name' => 'Bali Paradise Retreat',
                    'travelers' => 2,
                    'departure_date' => '2025-07-15',
                    'status' => 'confirmed',
                    'booking_date' => '2025-06-01',
                    'email' => 'john.doe@example.com',
                    'phone' => '(555) 123-4567',
                    'special_requests' => 'Vegetarian meals preferred',
                    'total_price' => 2598.00,
                    'itinerary_file' => 'itinerary-1001.txt',
                ],
                [
                    'id' => 1002,
                    'customer_id' => 502,
                    'customer_name' => 'Jane Smith',
                    'package_id' => 2,
                    'package_name' => 'Japanese Cultural Journey',
                    'travelers' => 1,
                    'departure_date' => '2025-08-10',
                    'status' => 'pending',
                    'booking_date' => '2025-06-05',
                    'email' => 'jane.smith@example.com',
                    'phone' => '(555) 987-6543',
                    'special_requests' => 'Early check-in if possible',
                    'total_price' => 2499.00,
                    'itinerary_file' => NULL,
                ],
                [
                    'id' => 1003,
                    'customer_id' => 503,
                    'customer_name' => 'Robert Johnson',
                    'package_id' => 3,
                    'package_name' => 'Greek Islands Cruise',
                    'travelers' => 4,
                    'departure_date' => '2025-09-22',
                    'status' => 'cancelled',
                    'booking_date' => '2025-05-20',
                    'email' => 'robert.johnson@example.com',
                    'phone' => '(555) 456-7890',
                    'special_requests' => 'Cabin with ocean view',
                    'total_price' => 7596.00,
                    'itinerary_file' => NULL,
                ],
                [
                    'id' => 1004,
                    'customer_id' => 504,
                    'customer_name' => 'Sarah Williams',
                    'package_id' => 4,
                    'package_name' => 'African Safari Adventure',
                    'travelers' => 2,
                    'departure_date' => '2025-07-30',
                    'status' => 'pending',
                    'booking_date' => '2025-06-15',
                    'email' => 'sarah.williams@example.com',
                    'phone' => '(555) 234-5678',
                    'special_requests' => 'Prefer window seats for flights',
                    'total_price' => 5998.00,
                    'itinerary_file' => NULL,
                ],
                [
                    'id' => 1005,
                    'customer_id' => 505,
                    'customer_name' => 'Michael Brown',
                    'package_id' => 5,
                    'package_name' => 'New York City Explorer',
                    'travelers' => 3,
                    'departure_date' => '2025-08-05',
                    'status' => 'confirmed',
                    'booking_date' => '2025-05-25',
                    'email' => 'michael.brown@example.com',
                    'phone' => '(555) 876-5432',
                    'special_requests' => 'Need airport transfer',
                    'total_price' => 3747.00,
                    'itinerary_file' => 'itinerary-1005.txt',
                ],
            ];

            // Sort orders by departure date (most recent first)
            usort($orders, function ($a, $b) {
                return strtotime($b['departure_date']) - strtotime($a['departure_date']);
            });
            ?>

            <div class="orders-container">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Package ID</th>
                            <th>Package Name</th>
                            <th>Travelers</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="orders-table-body">
                        <?php foreach ($orders as $order): ?>
                            <tr
                                class="order-row"
                                data-order-id="<?php echo $order['id']; ?>"
                                data-status="<?php echo $order['status']; ?>"
                                data-order="<?php echo htmlspecialchars(json_encode($order)); ?>"
                            >
                                <td><?php echo $order['id']; ?></td>
                                <td><?php echo $order['customer_id']; ?> - <?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo $order['package_id']; ?></td>
                                <td><?php echo htmlspecialchars($order['package_name']); ?></td>
                                <td><?php echo $order['travelers']; ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($order['status'] === 'pending'): ?>
                                            <button
                                                class="action-btn btn-confirm"
                                                onclick="openConfirmOrderModal(event, <?php echo htmlspecialchars(json_encode($order)); ?>)"
                                            >
                                                <span class="material-icons">check_circle</span>
                                                Confirm
                                            </button>
                                        <?php elseif ($order['status'] === 'confirmed'): ?>
                                            <button
                                                class="action-btn btn-confirm"
                                                onclick="viewItinerary(event, '<?php echo $order['itinerary_file']; ?>', <?php echo $order['id']; ?>)"
                                            >
                                                <span class="material-icons">description</span>
                                                View Itinerary
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <!-- Empty state message (initially hidden) -->
                <div
                    id="empty-state"
                    class="empty-state"
                    style="display: none;"
                >
                    <div class="icon">
                        <span
                            class="material-icons"
                            style="font-size: 48px;"
                        >search_off</span>
                    </div>
                    <h3>No orders found</h3>
                    <p>No orders match your search criteria. Try adjusting your filters.</p>
                </div>
            </div>
        </main>
    </div>

    <!-- Order Details Modal -->
    <div
        id="orderDetailsModal"
        class="modal"
    >
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Order Details</h2>
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
                    class="btn btn-outline"
                    onclick="closeModal('orderDetailsModal')"
                >Close
                </button>
            </div>
        </div>
    </div>

    <!-- Confirm Order Modal -->
    <div
        id="confirmOrderModal"
        class="modal"
    >
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Confirm Order</h2>
                <span
                    class="close"
                    onclick="closeModal('confirmOrderModal')"
                >&times;</span>
            </div>

            <p>You are about to confirm this booking. Please attach the itinerary file before confirming.</p>

            <div class="modal-divider"></div>

            <div id="confirmOrderSummary">
                <!-- Order summary will be populated here -->
            </div>

            <div class="file-upload">
                <label for="itineraryFile">Upload Itinerary File (PDF, DOC, or TXT):</label>
                <input
                    type="file"
                    id="itineraryFile"
                    name="itineraryFile"
                    accept=".pdf,.doc,.docx,.txt"
                >
            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-outline"
                    onclick="closeModal('confirmOrderModal')"
                >Cancel
                </button>
                <button
                    type="button"
                    class="btn btn-success"
                    id="confirmOrderBtn"
                >Confirm Order
                </button>
            </div>
        </div>
    </div>

    <script>
        // Store current order ID for operations
        let currentOrderId = null;

        // Initialize event listeners once the DOM is loaded
        document.addEventListener('DOMContentLoaded', function () {
            // Add click event to all order rows
            const orderRows = document.querySelectorAll('.order-row');
            orderRows.forEach(row => {
                row.addEventListener('click', function (e) {
                    // Ignore clicks on action buttons
                    if (!e.target.closest('.action-buttons')) {
                        const orderData = JSON.parse(this.getAttribute('data-order'));
                        showOrderDetails(orderData);
                    }
                });
            });

            // Set up filter buttons
            const filterButtons = document.querySelectorAll('.filter-btn');
            filterButtons.forEach(button => {
                button.addEventListener('click', function () {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => btn.classList.remove('active'));

                    // Add active class to clicked button
                    this.classList.add('active');

                    // Apply filter
                    const filter = this.getAttribute('data-filter');
                    filterOrders(filter);
                });
            });

            // Set up search functionality
            const searchInput = document.querySelector('.search-input');
            searchInput.addEventListener('keyup', function () {
                searchOrders(this.value.toLowerCase());
            });

            // Set up confirm order button
            document.getElementById('confirmOrderBtn').addEventListener('click', function () {
                confirmOrder();
            });
        });

        // Function to show order details in the modal
        function showOrderDetails(order) {
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
                    <div class="detail-label">Booking ID:</div>
                    <div class="detail-value">#${order.id}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value"><span class="${statusClass}">${statusLabel}</span></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Booking Date:</div>
                    <div class="detail-value">${bookingDate}</div>
                </div>

                <div class="modal-divider"></div>

                <div class="detail-row">
                    <div class="detail-label">Customer ID:</div>
                    <div class="detail-value">${order.customer_id}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Customer Name:</div>
                    <div class="detail-value">${order.customer_name}</div>
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
                    <div class="detail-label">Package ID:</div>
                    <div class="detail-value">${order.package_id}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Package Name:</div>
                    <div class="detail-value">${order.package_name}</div>
                </div>
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

            if (order.status === 'confirmed' && order.itinerary_file) {
                content += `
                <div class="modal-divider"></div>

                <div class="detail-row">
                    <div class="detail-label">Itinerary:</div>
                    <div class="detail-value">
                        <a href="${order.itinerary_file}" target="_blank" class="btn btn-sm btn-outline">
                            <span class="material-icons" style="font-size: 16px; margin-right: 5px;">description</span>
                            View Itinerary
                        </a>
                    </div>
                </div>`;
            }

            document.getElementById('orderDetailsContent').innerHTML = content;
            openModal('orderDetailsModal');
        }

        // Function to open the confirm order modal
        function openConfirmOrderModal(event, order) {
            event.stopPropagation();
            currentOrderId = order.id;

            // Populate order summary
            const summary = `
                <div class="detail-row">
                    <div class="detail-label">Order ID:</div>
                    <div class="detail-value">#${order.id}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Customer:</div>
                    <div class="detail-value">${order.customer_name}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Package:</div>
                    <div class="detail-value">${order.package_name}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Departure:</div>
                    <div class="detail-value">${new Date(order.departure_date).toLocaleDateString('en-US', {
                year: 'numeric', month: 'long', day: 'numeric'
            })}</div>
                </div>
            `;

            document.getElementById('confirmOrderSummary').innerHTML = summary;
            openModal('confirmOrderModal');
        }

        // Function to confirm an order
        function confirmOrder() {
            // Get the file input
            const fileInput = document.getElementById('itineraryFile');

            // Check if a file was selected
            if (!fileInput.files || fileInput.files.length === 0) {
                alert('Please select an itinerary file to upload');
                return;
            }

            // In a real application, you would upload the file to the server here
            // For this demo, we'll just simulate a successful upload

            // Find the order row
            const row = document.querySelector(`tr[data-order-id="${currentOrderId}"]`);
            if (row) {
                // Update the status in the table
                const statusCell = row.querySelector('td:nth-child(6)');
                statusCell.innerHTML = '<span class="status-badge status-confirmed">Confirmed</span>';

                // Update the actions cell
                const actionsCell = row.querySelector('td:nth-child(7)');
                const fileName = `itinerary-${currentOrderId}.txt`; // In a real app, this would be the actual uploaded file name
                actionsCell.innerHTML = `
                    <div class="action-buttons">
                        <button class="action-btn btn-confirm" onclick="viewItinerary(event, '${fileName}', ${currentOrderId})">
                            <span class="material-icons">description</span>
                            View Itinerary
                        </button>
                    </div>
                `;

                // Update the data attribute
                const orderData = JSON.parse(row.getAttribute('data-order'));
                orderData.status = 'confirmed';
                orderData.itinerary_file = fileName;
                row.setAttribute('data-order', JSON.stringify(orderData));
                row.setAttribute('data-status', 'confirmed');

                // Close the modal
                closeModal('confirmOrderModal');

                // Show success message
                alert('Order has been confirmed successfully');
            }
        }

        // Function to view itinerary
        function viewItinerary(event, fileName, orderId) {
            event.stopPropagation();

            // In a real application, this would open the file or download it
            // For this demo, we'll just show a message
            if (fileName) {
                window.open(fileName, '_blank');
            } else {
                alert('Itinerary file not found');
            }
        }

        // Function to filter orders by status
        function filterOrders(filter) {
            const rows = document.querySelectorAll('.order-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const status = row.getAttribute('data-status');

                if (filter === 'all' || status === filter) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide empty state message
            document.getElementById('empty-state').style.display = visibleCount === 0 ? 'block' : 'none';
        }

        // Function to search orders
        function searchOrders(query) {
            const rows = document.querySelectorAll('.order-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const orderData = JSON.parse(row.getAttribute('data-order'));

                // Check if the query matches any field we want to search by
                const matchesSearch =
                    orderData.id.toString().includes(query) ||
                    orderData.customer_id.toString().includes(query) ||
                    orderData.customer_name.toLowerCase().includes(query) ||
                    orderData.package_id.toString().includes(query) ||
                    orderData.package_name.toLowerCase().includes(query);

                // Only show the row if it matches the search and current filter
                const activeFilter = document.querySelector('.filter-btn.active').getAttribute('data-filter');
                const matchesFilter = activeFilter === 'all' || orderData.status === activeFilter;

                if (matchesSearch && matchesFilter) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide empty state message
            document.getElementById('empty-state').style.display = visibleCount === 0 ? 'block' : 'none';
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

            // Clear file input when closing confirm order modal
            if (modalId === 'confirmOrderModal') {
                document.getElementById('itineraryFile').value = '';
            }
        }

        // Close modals when clicking outside
        window.addEventListener('click', function (event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    closeModal(modal.id);
                }
            });
        });
    </script>
</body>
</html>
