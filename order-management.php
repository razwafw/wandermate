<?php
require_once 'config.php';

session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] ?? 1) != 2) {
    header('Location: index.php');
    exit();
}

require_once 'DatabaseConnection.php';
$conn = new DatabaseConnection();
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
$sql = "SELECT o.id, o.customer_id, u.name AS customer_name, u.email, u.phone, o.package_id, p.name AS package_name, o.amount AS travelers, o.departure_date, o.booking_date, s.name AS status, o.request AS special_requests, o.amount * p.price AS total_price, o.itinerary_url AS itinerary_file
                    FROM orders o
                    LEFT JOIN users u ON o.customer_id = u.id
                    LEFT JOIN packages p ON o.package_id = p.id
                    LEFT JOIN statuses s ON o.status_id = s.id
                    ORDER BY o.booking_date";
$result = $conn->query($sql);
$orders = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
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

        main {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
        }

        @media (max-width: 992px) {
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

        .orders-table tbody tr:hover {
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
        <?php include 'sidebar.php'; ?>

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

            <!-- Orders Table -->
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
                        <?php foreach ($orders as &$order): ?>
                            <?php
                            if ($order['status'] === NULL) {
                                $order['status'] = 'pending';
                            } else {
                                $order['status'] = strtolower($order['status']);
                            }
                            ?>
                            <tr
                                class="order-row"
                                data-order-id="<?php echo $order['id']; ?>"
                                data-status="<?php echo $order['status']; ?>"
                                data-order="<?php echo htmlspecialchars(json_encode($order)); ?>"
                            >
                                <td><?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo $order['package_id']; ?></td>
                                <td><?php echo htmlspecialchars($order['package_name']); ?></td>
                                <td><?php echo $order['travelers']; ?></td>
                                <td>
                                    <span class="order-status status-badge status-<?php echo $order['status']; ?>">
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
        document.addEventListener("DOMContentLoaded", function () {
            // Add click event to all order rows
            const orderRows = document.querySelectorAll(".order-row");
            orderRows.forEach(row => {
                row.addEventListener("click", function (e) {
                    // Ignore clicks on action buttons
                    if (!e.target.closest(".action-buttons")) {
                        const orderData = JSON.parse(this.getAttribute("data-order"));
                        showOrderDetails(orderData);
                    }
                });
            });

            // Set up filter buttons
            const filterButtons = document.querySelectorAll(".filter-btn");
            filterButtons.forEach(button => {
                button.addEventListener("click", function () {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => btn.classList.remove("active"));

                    // Add active class to clicked button
                    this.classList.add("active");

                    // Apply filter
                    const filter = this.getAttribute("data-filter");
                    filterOrders(filter);
                });
            });

            // Set up confirm order button
            document.getElementById("confirmOrderBtn").addEventListener("click", function () {
                confirmOrder();
            });
        });

        // Function to show order details in the modal
        function showOrderDetails(order) {
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
                    <div class="detail-value">$${parseInt(order.total_price).toFixed(2)}</div>
                </div>`;

            if (order.special_requests) {
                content += `
                <div class="detail-row">
                    <div class="detail-label">Special Requests:</div>
                    <div class="detail-value">${order.special_requests}</div>
                </div>`;
            }

            if (order.status === "confirmed" && order.itinerary_file) {
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

            document.getElementById("orderDetailsContent").innerHTML = content;
            openModal("orderDetailsModal");
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
                    <div class="detail-value">${new Date(order.departure_date).toLocaleDateString("en-US", {
                year: "numeric", month: "long", day: "numeric",
            })}</div>
                </div>
            `;

            document.getElementById("confirmOrderSummary").innerHTML = summary;
            openModal("confirmOrderModal");
        }

        // Function to confirm order
        function confirmOrder() {
            if (!currentOrderId) {
                alert("Order ID is missing.");
                return;
            }
            // Read the file content from the file input
            const fileInput = document.getElementById("itineraryFile");
            if (!fileInput.files || fileInput.files.length === 0) {
                alert("Please select an itinerary file.");
                return;
            }
            const file = fileInput.files[0];
            const reader = new FileReader();
            reader.onload = function (e) {
                const itineraryContent = e.target.result;
                // Send AJAX request to confirm-order.php
                fetch("confirm-order.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: "order_id=" + encodeURIComponent(currentOrderId) + "&itinerary_content=" + encodeURIComponent(itineraryContent),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update the row UI
                            const row = document.querySelector(`tr[data-order-id="${currentOrderId}"]`);
                            console.log(row);
                            if (row) {
                                const orderStatus = row.querySelector(".order-status");
                                orderStatus.textContent = "Confirmed";
                                orderStatus.classList.remove("status-pending");
                                orderStatus.classList.add("status-confirmed");

                                row.setAttribute("data-status", "confirmed");
                                // Update action button to View Itinerary
                                const actionCell = row.querySelector(".action-buttons");
                                if (actionCell) {
                                    actionCell.innerHTML = `<button class=\"action-btn btn-confirm\" onclick=\"viewItinerary(event, '${data.itinerary_url}', ${currentOrderId})\"><span class=\"material-icons\">description</span>View Itinerary</button>`;
                                }
                                // Update data-order attribute
                                const orderData = JSON.parse(row.getAttribute("data-order"));
                                orderData.status = "confirmed";
                                orderData.itinerary_file = data.itinerary_url;
                                row.setAttribute("data-order", JSON.stringify(orderData));
                            }
                            closeModal("confirmOrderModal");
                            alert("Order has been confirmed successfully");
                        } else {
                            alert(data.message || "Failed to confirm order.");
                        }
                    })
                    .catch(() => {
                        alert("An error occurred while confirming the order.");
                    });
            };
            reader.onerror = function () {
                alert("Failed to read the itinerary file.");
            };
            reader.readAsText(file);
        }

        // Function to view itinerary
        function viewItinerary(event, fileName, orderId) {
            event.stopPropagation();

            // In a real application, this would open the file or download it
            // For this demo, we'll just show a message
            if (fileName) {
                window.open(fileName, "_blank");
            } else {
                alert("Itinerary file not found");
            }
        }

        // Function to filter orders by status
        function filterOrders(filter) {
            const rows = document.querySelectorAll(".order-row");
            let visibleCount = 0;

            rows.forEach(row => {
                const status = row.getAttribute("data-status");

                if (filter === "all" || status === filter) {
                    row.style.display = "";
                    visibleCount++;
                } else {
                    row.style.display = "none";
                }
            });

            // Show/hide empty state message
            document.getElementById("empty-state").style.display = visibleCount === 0 ? "block" : "none";
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

            // Clear file input when closing confirm order modal
            if (modalId === "confirmOrderModal") {
                document.getElementById("itineraryFile").value = "";
            }
        }

        // Close modals when clicking outside
        window.addEventListener("click", function (event) {
            const modals = document.querySelectorAll(".modal");
            modals.forEach(modal => {
                if (event.target === modal) {
                    closeModal(modal.id);
                }
            });
        });
    </script>
</body>
</html>
