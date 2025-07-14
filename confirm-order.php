<?php
require_once 'config.php';

session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] ?? 1) != 2) {
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
$itinerary_content = $_POST['itinerary_content'] ?? '';

if ($order_id <= 0 || !$itinerary_content) {
    http_response_code(400);
    echo json_encode([
        'success' => FALSE,
        'message' => 'Order ID and itinerary content are required.',
    ]);
    exit();
}

require_once 'DatabaseConnection.php';
$conn = new DatabaseConnection();
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        'success' => FALSE,
        'message' => 'Database connection failed',
    ]);
    exit();
}

$filename = 'order-' . $order_id . '-itinerary.txt';
$file_path = __DIR__ . DIRECTORY_SEPARATOR . $filename;
if (file_put_contents($file_path, $itinerary_content) === FALSE) {
    http_response_code(500);
    echo json_encode([
        'success' => FALSE,
        'message' => 'Failed to save itinerary file.',
    ]);
    exit();
}

$sql = "UPDATE orders SET status_id = 2, itinerary_url = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $filename, $order_id);
$stmt->execute();
if ($stmt->affected_rows > 0) {
    echo json_encode([
        'success' => TRUE,
        'itinerary_url' => $filename,
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        'success' => FALSE,
        'message' => 'Order not found or not updated.',
    ]);
}
$stmt->close();
$conn->close();

