<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] ?? 1) != 2) {
    header('Location: package-management.php?error=1');
    exit();
}

// DB connection
$conn = new mysqli('localhost', 'projec15_root', '@kaesquare123', 'projec15_wandermate');
if ($conn->connect_error) {
    header('Location: package-management.php?error=1');
    exit();
}

// Handle POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: package-management.php?error=1');
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
$package_id = intval(post('package_id'));

// Validate required fields
if (!$name || !$subtitle || !$price || !$duration || !$group_size || !$start_location || !$end_location || !$description) {
    header('Location: package-management.php?error=1');
    exit();
}

if (!$package_id) {
    header('Location: package-management.php?error=1');
    exit();
}

$stmt = $conn->prepare("UPDATE packages SET name=?, subtitle=?, price=?, duration=?, group_size=?, start_location=?, end_location=?, description=?, highlights=?, includes=?, excludes=?, itinerary=? WHERE id=?");
if (!$stmt) {
    header('Location: package-management.php?error=1');
    exit();
}
$stmt->bind_param(
    'ssisssssssssi',
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
    $package_id
);
if ($stmt->execute()) {
    // Handle removal of existing images if requested
    if (!empty($_POST['existing_images'])) {
        $existingImages = json_decode($_POST['existing_images'], TRUE);
        $imgSql = "SELECT url FROM images WHERE package_id = ?";
        $imgStmt = $conn->prepare($imgSql);
        $imgStmt->bind_param('i', $package_id);
        $imgStmt->execute();
        $result = $imgStmt->get_result();
        $allImages = [];
        while ($row = $result->fetch_assoc()) {
            $allImages[] = $row['url'];
        }
        $imgStmt->close();
        $toDelete = array_diff($allImages, $existingImages);
        foreach ($toDelete as $imgUrl) {
            $delStmt = $conn->prepare('DELETE FROM images WHERE package_id = ? AND url = ?');
            $delStmt->bind_param('is', $package_id, $imgUrl);
            $delStmt->execute();
            $delStmt->close();
            if (file_exists($imgUrl)) {
                unlink($imgUrl);
            }
        }
    }
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
    header('Location: package-management.php?success=1');
    exit();
} else {
    header('Location: package-management.php?error=1');
    exit();
}
$stmt->close();
$conn->close();
