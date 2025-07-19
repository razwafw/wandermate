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
    die('Database connection failed: ' . $conn->connect_error);
}

$packages = [];
$sql = "SELECT * FROM packages";
$result = $conn->query($sql);

require_once 'utilities.php';
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $images = [];
        foreach (explode("\n", $row['images']) as $image) {
            $image = trim($image);
            if (!empty($image)) {
                $images[] = $image;
            }
        }

        $row['images'] = $images;

        $row['highlights'] = parseNewlineList($row['highlights']);
        $row['includes'] = parseNewlineList($row['includes']);
        $row['excludes'] = parseNewlineList($row['excludes']);
        $row['itinerary'] = parseItinerary($row['itinerary']);
        $packages[] = $row;
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
        href="modal.css"
    >
    <link
        rel="stylesheet"
        href="table.css"
    >
    <link
        rel="stylesheet"
        href="package-management.css"
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
            <div class="page-header">
                <h1 class="page-title">Package Management</h1>
                <button
                    id="createPackageBtn"
                    class="btn btn-with-icon"
                >
                    <i class="fa-solid fa-plus"></i>
                    Create Package
                </button>
            </div>

            <!-- Package Table -->
            <div class="table-container">
                <?php if (count($packages) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Package Name</th>
                                <th>Duration</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($packages as $package): ?>
                                <tr>
                                    <td><?php echo $package['id']; ?></td>
                                    <td><?php echo htmlspecialchars($package['name']); ?></td>
                                    <td><?php echo htmlspecialchars($package['duration']); ?></td>
                                    <td>$<?php echo number_format($package['price']); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button
                                                class="action-btn btn-warning"
                                                onclick="editPackage(<?php echo htmlspecialchars(json_encode($package)); ?>)"
                                            >
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </button>
                                            <button
                                                class="action-btn btn-danger"
                                                onclick="confirmDelete(<?php echo $package['id']; ?>, '<?php echo htmlspecialchars($package['name']); ?>')"
                                            >
                                                <i class="fa-solid fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="icon">
                            <i class="fa-solid fa-suitcase-rolling"></i>
                        </div>
                        <p>No packages available. Create your first package to get started.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Package Modal (Create/Edit) -->
            <?php
            $modalId = 'packageModal';
            $modalTitle = 'Create New Package';
            $modalContent = "
                <form
                    id='packageForm'
                    enctype='multipart/form-data'
                    method='post'
                    action='add-package.php'
                >
                    <input
                        type='hidden'
                        id='package_id'
                        name='package_id'
                    >

                    <!-- Basic Information -->
                    <div class='form-group'>
                        <label for='package_name'>Package Name</label>
                        <input
                            type='text'
                            id='package_name'
                            name='package_name'
                            class='form-control'
                            required
                        >
                    </div>

                    <div class='form-group'>
                        <label for='subtitle'>Subtitle</label>
                        <input
                            type='text'
                            id='subtitle'
                            name='subtitle'
                            class='form-control'
                            required
                        >
                    </div>

                    <div class='form-row'>
                        <div class='form-group'>
                            <label for='price'>Price ($)</label>
                            <input
                                type='number'
                                id='price'
                                name='price'
                                class='form-control'
                                min='0'
                                required
                            >
                        </div>
                        <div class='form-group'>
                            <label for='duration'>Duration</label>
                            <input
                                type='text'
                                id='duration'
                                name='duration'
                                placeholder='e.g. 7 days / 6 nights'
                                class='form-control'
                                required
                            >
                        </div>
                    </div>

                    <div class='form-row'>
                        <div class='form-group'>
                            <label for='group_size'>Group Size</label>
                            <input
                                type='number'
                                id='group_size'
                                name='group_size'
                                class='form-control'
                                min='1'
                                required
                            >
                        </div>
                    </div>

                    <div class='form-row'>
                        <div class='form-group'>
                            <label for='start_location'>Start Location</label>
                            <input
                                type='text'
                                id='start_location'
                                name='start_location'
                                class='form-control'
                                required
                            >
                        </div>
                        <div class='form-group'>
                            <label for='end_location'>End Location</label>
                            <input
                                type='text'
                                id='end_location'
                                name='end_location'
                                class='form-control'
                                required
                            >
                        </div>
                    </div>

                    <div class='form-group'>
                        <label for='description'>Description</label>
                        <textarea
                            id='description'
                            name='description'
                            class='form-control'
                            rows='4'
                            required
                        ></textarea>
                    </div>

                    <!-- Trip Details -->
                    <div class='form-group'>
                        <label for='highlights'>Highlights (one per line)</label>
                        <textarea
                            id='highlights'
                            name='highlights'
                            class='form-control'
                            rows='5'
                            required
                        ></textarea>
                    </div>

                    <div class='form-group'>
                        <label for='includes'>Includes (one per line)</label>
                        <textarea
                            id='includes'
                            name='includes'
                            class='form-control'
                            rows='5'
                            required
                        ></textarea>
                    </div>

                    <div class='form-group'>
                        <label for='excludes'>Excludes (one per line)</label>
                        <textarea
                            id='excludes'
                            name='excludes'
                            class='form-control'
                            rows='5'
                            required
                        ></textarea>
                    </div>

                    <!-- Itinerary -->
                    <div class='form-group'>
                        <label for='itinerary'>Itinerary (format: Day X: Title | Description)</label>
                        <textarea
                            id='itinerary'
                            name='itinerary'
                            class='form-control'
                            rows='5'
                            placeholder='Day 1: Arrival in Bali | Arrive at airport where you will be greeted by your guide.&#10;Day 2: Sacred Temples | Visit the iconic sea temple of Tanah Lot.'
                            required
                        ></textarea>
                    </div>

                    <!-- Images -->
                    <div class='form-group'>
                        <label for='package_images'>Images</label>
                        <input
                            type='hidden'
                            name='existing_images'
                            id='existing_images'
                        >

                        <!-- Image Preview Container -->
                        <div
                            id='imagePreviewContainer'
                            class='image-preview-container'
                        ></div>

                        <!-- Existing Images List (when editing) -->
                        <div id='existingImagesContainer'></div>

                        <div class='file-upload-container'>
                            <label class='file-upload-btn'>
                                <i class='fa-solid fa-upload'></i> Upload Images
                                <input
                                    type='file'
                                    id='package_images'
                                    name='package_images[]'
                                    class='hidden-file-input'
                                    accept='image/*'
                                    multiple
                                >
                            </label>
                            <small class='form-text text-muted'>You can select multiple images at once.</small>
                        </div>
                    </div>
                </form>
            ";
            $modalFooter = "
                <button
                    type='button'
                    class='btn btn-outline'
                    id='closePackageModalBtn'
                >
                    Cancel
                </button>
                <button
                    type='submit'
                    class='btn'
                    onclick='document.getElementById(\"packageForm\").submit();'
                >
                    Save Package
                </button>
            ";

            include 'modal.php';
            ?>

            <!-- Delete Confirmation Modal -->
            <?php
            $modalId = 'deleteModal';
            $modalTitle = 'Confirm Deletion';
            $modalContent = "
                <p
                    class='confirm-text'
                    id='deleteConfirmText'
                >
                    Are you sure you want to delete this package? This action cannot be undone.
                </p>
            ";
            $modalFooter = "
                <button
                    type='button'
                    class='btn btn-outline'
                    id='closeDeleteModalBtn'
                >
                    Cancel
                </button>
                <button
                    type='button'
                    class='btn btn-danger'
                    id='confirmDeleteBtn'
                >
                    Delete Package
                </button>
            ";

            include 'modal.php';
            ?>
        </main>
    </div>

    <script
        src="package-management.js"
        type="module"
    ></script>
</body>
</html>
