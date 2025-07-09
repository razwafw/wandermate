<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] ?? 1) != 2) {
    http_response_code(403);
    echo 'Unauthorized';
    exit();
}

// DB connection
$conn = new mysqli('localhost', 'root', '', 'wandermate');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Handle POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit();
}

// Helper: sanitize and get POST
function post($key)
{
    return trim($_POST[$key] ?? '');
}

$name = post('package_name');
$subtitle = post('subtitle');
$price = floatval(post('price'));
$duration = post('duration');
$group_size = post('group_size');
$start_location = post('start_location');
$end_location = post('end_location');
$description = post('description');
$highlights = post('highlights');
$includes = post('includes');
$excludes = post('excludes');
$itinerary = post('itinerary');

// Validate required fields
if (!$name || !$subtitle || !$price || !$duration || !$group_size || !$start_location || !$end_location || !$description) {
    http_response_code(400);
    echo 'Missing required fields';
    exit();
}

// Insert package
$stmt = $conn->prepare("INSERT INTO packages (name, subtitle, price, duration, group_size, start_location, end_location, description, highlights, includes, excludes, itinerary) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param('ssdsssssssss', $name, $subtitle, $price, $duration, $group_size, $start_location, $end_location, $description, $highlights, $includes, $excludes, $itinerary);
if (!$stmt->execute()) {
    http_response_code(500);
    echo 'Failed to add package: ' . $stmt->error;
    exit();
}
$package_id = $stmt->insert_id;
$stmt->close();

// Handle images (if any)
if (!empty($_FILES['package_images']['name'][0])) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, TRUE);
    }
    foreach ($_FILES['package_images']['tmp_name'] as $idx => $tmpName) {
        if ($_FILES['package_images']['error'][$idx] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['package_images']['name'][$idx], PATHINFO_EXTENSION);
            $filename = uniqid('pkgimg_') . '.' . $ext;
            $dest = $uploadDir . $filename;
            if (move_uploaded_file($tmpName, $dest)) {
                $url = $dest;
                $imgStmt = $conn->prepare('INSERT INTO images (package_id, url) VALUES (?, ?)');
                $imgStmt->bind_param('is', $package_id, $url);
                $imgStmt->execute();
                $imgStmt->close();
            }
        }
    }
}

$conn->close();
header('Location: package-management.php?success=1');
exit();
