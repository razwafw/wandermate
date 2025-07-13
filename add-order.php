<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => FALSE,
        'message' => 'Invalid request method.',
    ]);
    exit();
}

// Get user id and role id from session
$customer_id = $_SESSION['user_id'] ?? NULL;
$role_id = $_SESSION['role_id'] ?? 1;

if (!$customer_id) {
    echo json_encode([
        'success' => FALSE,
        'message' => 'User not logged in.',
    ]);
    exit();
}

if ($role_id == 2) {
    echo json_encode([
        'success' => FALSE,
        'message' => 'Action not allowed.',
    ]);
    exit();
}

// Validate required fields
$required = [
    'package_id',
    'departureDate',
    'travelers',
];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        echo json_encode([
            'success' => FALSE,
            'message' => 'Missing required fields.',
        ]);
        exit();
    }
}

$package_id = intval($_POST['package_id']);
$departureDate = trim($_POST['departureDate']);
$travelers = intval($_POST['travelers']);
$specialRequests = isset($_POST['specialRequests']) ? trim($_POST['specialRequests']) : NULL;

// Connect to DB
$conn = new mysqli('localhost', 'projec15_root', '@kaesquare123', 'projec15_wandermate');
if ($conn->connect_error) {
    echo json_encode([
        'success' => FALSE,
        'message' => 'Database connection failed.',
    ]);
    exit();
}

// Insert order
$stmt = $conn->prepare('INSERT INTO orders (departure_date, amount, request, customer_id, package_id) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('sisii', $departureDate, $travelers, $specialRequests, $customer_id, $package_id);
$success = $stmt->execute();
$stmt->close();
$conn->close();

if ($success) {
    echo json_encode([
        'success' => TRUE,
        'message' => 'Order created successfully.',
    ]);
} else {
    echo json_encode([
        'success' => FALSE,
        'message' => 'Failed to create order',
    ]);
}
