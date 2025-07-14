<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: package-management.php?error=1');
    exit();
}

session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] ?? 1) != 2) {
    header('Location: package-management.php?error=1');
    exit();
}

require_once 'DatabaseConnection.php';
$conn = new DatabaseConnection();
if ($conn->connect_error) {
    header('Location: package-management.php?error=1');
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
$package_id = intval(trim($_POST['package_id'] ?? ''));

if (!$name || !$subtitle || !$price || !$duration || !$group_size || !$start_location || !$end_location || !$description) {
    header('Location: package-management.php?error=1');
    exit();
}

if (!$package_id) {
    header('Location: package-management.php?error=1');
    exit();
}

$newImages = '';

// Handle removal of existing images if requested
if (!empty($_POST['existing_images'])) {
    $existingImages = json_decode($_POST['existing_images'], TRUE);
    $imgSql = "SELECT images FROM packages WHERE id = ?";
    $imgStmt = $conn->prepare($imgSql);
    $imgStmt->bind_param('i', $package_id);
    $imgStmt->execute();
    $result = $imgStmt->get_result()->fetch_assoc();
    $allImages = [];
    foreach (explode("\n", $result['images']) as $image) {
        $image = trim($image);
        if (!empty($image)) {
            $allImages[] = $image;
        }
    }
    $imgStmt->close();
    $toDelete = array_diff($allImages, $existingImages);

    foreach ($toDelete as $image) {
        if (file_exists($image)) {
            unlink($image);
        }
    }

    $newImages .= implode("\n", array_intersect($allImages, $existingImages)) . "\n";
}

// Handle images (if any)
if (!empty($_FILES['package_images']['name'][0])) {
    foreach ($_FILES['package_images']['tmp_name'] as $idx => $tmpName) {
        if ($_FILES['package_images']['error'][$idx] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['package_images']['name'][$idx], PATHINFO_EXTENSION);
            $filename = uniqid(strtolower(str_replace(' ', '-', trim($name))) . '-') . '.' . $ext;
            $dest = $filename;
            if (move_uploaded_file($tmpName, $dest)) {
                $newImages .= $dest . "\n";
            }
        }
    }
}

$stmt = $conn->prepare("UPDATE packages SET name=?, subtitle=?, price=?, duration=?, group_size=?, start_location=?, end_location=?, description=?, highlights=?, includes=?, excludes=?, itinerary=?, images=? WHERE id=?");
if (!$stmt) {
    header('Location: package-management.php?error=1');
    exit();
}
$stmt->bind_param(
    'ssissssssssssi',
    $name,
    $subtitle,
    $price,
    $duration,
    $group_size,
    $start_location,
    $end_location,
    $description,
    $highlights,
    $includes,
    $excludes,
    $itinerary,
    $newImages,
    $package_id
);

if ($stmt->execute()) {
    header('Location: package-management.php?success=1');
    exit();
}

$stmt->close();
$conn->close();

header('Location: package-management.php?error=1');
exit();
