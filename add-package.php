<?php
require_once 'config.php';

// Handle POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: package-management.php');
    exit();
}

session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] ?? 1) != 2) {
    header('Location: package-management.php');
    exit();
}

require_once 'DatabaseConnection.php';
$conn = new DatabaseConnection();
if ($conn->connect_error) {
    header('Location: package-management.php');
    exit();
}

$name = trim($_POST['package_name'] ?? '');
$subtitle = trim($_POST['subtitle'] ?? '');
$price = floatval(trim($_POST['price'] ?? ''));
$duration = trim($_POST['duration'] ?? '');
$group_size = trim($_POST['group_size'] ?? '');
$start_location = trim($_POST['start_location'] ?? '');
$end_location = trim($_POST['end_location'] ?? '');
$description = trim($_POST['description'] ?? '');
$highlights = trim($_POST['highlights'] ?? '');
$includes = trim($_POST['includes'] ?? '');
$excludes = trim($_POST['excludes'] ?? '');
$itinerary = trim($_POST['itinerary'] ?? '');

// Validate required fields
if (!$name || !$subtitle || !$price || !$duration || !$group_size || !$start_location || !$end_location || !$description) {
    header('Location: package-management.php');
    exit();
}

$images = '';

if (!empty($_FILES['package_images']['name'][0])) {
    foreach ($_FILES['package_images']['tmp_name'] as $idx => $tmpName) {
        if ($_FILES['package_images']['error'][$idx] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['package_images']['name'][$idx], PATHINFO_EXTENSION);
            $filename = uniqid(strtolower(str_replace(' ', '-', trim($name))) . '-') . '.' . $ext;
            $dest = $filename;
            if (move_uploaded_file($tmpName, $dest)) {
                $images .= $dest . "\n";
            }
        }
    }

    $images = rtrim($images, "\n");
}

// Insert package
$stmt = $conn->prepare("INSERT INTO packages (name, subtitle, price, duration, group_size, start_location, end_location, description, highlights, includes, excludes, itinerary, images) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param('ssdssssssssss', $name, $subtitle, $price, $duration, $group_size, $start_location, $end_location, $description, $highlights, $includes, $excludes, $itinerary, $images);
if (!$stmt->execute()) {
    header('Location: package-management.php');
    exit();
}
$stmt->close();
$conn->close();
die();

header('Location: package-management.php');
exit();
