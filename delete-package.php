<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] ?? 1) != 2) {
    http_response_code(403);
    echo 'Unauthorized';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit();
}

$package_id = intval($_POST['package_id'] ?? 0);
if (!$package_id) {
    http_response_code(400);
    echo 'Invalid package ID';
    exit();
}

$conn = new mysqli('localhost', 'projec15_root', '@kaesquare123', 'projec15_wandermate');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Check if any order exists for this package
$orderCheck = $conn->prepare('SELECT id FROM orders WHERE package_id = ? LIMIT 1');
$orderCheck->bind_param('i', $package_id);
$orderCheck->execute();
$orderCheck->store_result();
if ($orderCheck->num_rows > 0) {
    $orderCheck->close();
    $conn->close();
    header('Location: package-management.php?delete_failed=1');
    exit();
}
$orderCheck->close();

// Delete images from server and DB
$imgSql = "SELECT url FROM images WHERE package_id = ?";
$imgStmt = $conn->prepare($imgSql);
$imgStmt->bind_param('i', $package_id);
$imgStmt->execute();
$result = $imgStmt->get_result();
while ($row = $result->fetch_assoc()) {
    $imgUrl = $row['url'];
    if (file_exists($imgUrl)) {
        unlink($imgUrl);
    }
}
$imgStmt->close();

$delImgStmt = $conn->prepare('DELETE FROM images WHERE package_id = ?');
$delImgStmt->bind_param('i', $package_id);
$delImgStmt->execute();
$delImgStmt->close();

// Delete package
$delPkgStmt = $conn->prepare('DELETE FROM packages WHERE id = ?');
$delPkgStmt->bind_param('i', $package_id);
$delPkgStmt->execute();
$delPkgStmt->close();

$conn->close();
header('Location: package-management.php?deleted=1');
exit();

