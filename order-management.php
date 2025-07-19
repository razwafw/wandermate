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
                    ORDER BY o.departure_date";
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

    <link
        rel="stylesheet"
        href="global.css"
    >
    <link
        rel="stylesheet"
        href="global-dashboard.css"
    >
    <link
        rel="stylesheet"
        href="sidebar.css"
    >
    <link
        rel="stylesheet"
        href="table.css"
    >
    <link
        rel="stylesheet"
        href="modal.css"
    >
    <link
        rel="stylesheet"
        href="order-management.css"
    >

    <!-- Font Awesome (for icons) -->
    <script
        src="https://kit.fontawesome.com/c880a1b0f6.js"
        crossorigin="anonymous"
    ></script>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main>
            <!-- Page Header -->
            <h1 class="page-title">Order Management</h1>
            <p class="page-subtitle">View and manage all customer bookings</p>

            <!-- Filters and Search -->
            <div class="filter-container">
                <button
                    class="filter-btn active"
                    data-filter="all"
                >
                    All Orders
                </button>
                <button
                    class="filter-btn"
                    data-filter="pending"
                >
                    Pending
                </button>
                <button
                    class="filter-btn"
                    data-filter="confirmed"
                >
                    Confirmed
                </button>
                <button
                    class="filter-btn"
                    data-filter="cancelled"
                >
                    Cancelled
                </button>
            </div>

            <!-- Orders Table -->
            <div class="table-container">
                <table>
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
                                                class="action-btn btn-success"
                                                onclick="openConfirmOrderModal(event, <?php echo htmlspecialchars(json_encode($order)); ?>)"
                                            >
                                                <span class="fa-solid fa-circle-check"></span>
                                                Confirm
                                            </button>
                                        <?php elseif ($order['status'] === 'confirmed'): ?>
                                            <button
                                                class="action-btn btn-warning"
                                                onclick="viewItinerary(event, '<?php echo $order['itinerary_file']; ?>', <?php echo $order['id']; ?>)"
                                            >
                                                <span class="fa-solid fa-file-lines"></span>
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
                            class="fa-solid fa-magnifying-glass"
                            style="font-size: 48px;"
                        ></span>
                    </div>
                    <h3>No orders found</h3>
                    <p>No orders match your search criteria. Try adjusting your filters.</p>
                </div>
            </div>
        </main>
    </div>

    <!-- Order Details Modal -->
    <?php
    $modalId = 'orderDetailsModal';
    $modalTitle = 'Order Details';
    $modalContent = "
        <div id='orderDetailsContent'>
            <!-- Content will be populated by JavaScript -->
        </div>
    ";
    $modalFooter = "
        <button
            type='button'
            class='btn btn-outline'
            id='closeOrderDetailsBtn'
        >
            Close
        </button>
    ";

    include 'modal.php';
    ?>

    <!-- Confirm Order Modal -->
    <?php
    $modalId = 'confirmOrderModal';
    $modalTitle = 'Confirm Order';
    $modalContent = "
        <p>You are about to confirm this booking. Please attach the itinerary file before confirming.</p>

        <div class='modal-divider'></div>

        <div id='confirmOrderSummary'>
            <!-- Order summary will be populated here -->
        </div>
        
        <div class='file-upload'>
            <label for='itineraryFile'>Upload Itinerary File (PDF, DOC, or TXT):</label>
            <input
                type='file'
                id='itineraryFile'
                name='itineraryFile'
                accept='.pdf,.doc,.docx,.txt'
            >
        </div>
    ";
    $modalFooter = "
        <button
            type='button'
            class='btn btn-outline'
            id='closeConfirmOrderModalBtn'
        >
            Cancel
        </button>
        <button
            type='button'
            class='btn btn-success'
            id='confirmOrderBtn'
        >
            Confirm Order
        </button>
    ";

    include 'modal.php';
    ?>

    <script
        src="order-management.js"
        type="module"
    ></script>
</body>
</html>
