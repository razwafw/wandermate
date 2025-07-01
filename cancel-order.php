<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => FALSE,
        'message' => 'Unauthorized',
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => FALSE,
        'message' => 'Method not allowed',
    ]);
    exit();
}

$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$user_id = $_SESSION['user_id'];

if ($order_id <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => FALSE,
        'message' => 'Invalid order ID',
    ]);
    exit();
}

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'wandermate';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        'success' => FALSE,
        'message' => 'Database connection failed',
    ]);
    exit();
}

// Only allow the user to cancel their own order
$sql = "UPDATE orders SET status_id = 1 WHERE id = ? AND customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $order_id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => TRUE]);
} else {
    http_response_code(400);
    echo json_encode([
        'success' => FALSE,
        'message' => 'Order not found or already cancelled',
    ]);
}

$stmt->close();
$conn->close();

