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

    <!-- Global Styles -->
    <style>
        html {
            scroll-behavior: smooth;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #fff;
            color: #000;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .btn {
            display: inline-block;
            background-color: midnightblue;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
            cursor: pointer;
            border: none;
        }

        .btn:hover {
            background-color: #1a1a5a;
            color: #fff;
        }

        button[disabled] {
            background-color: #cccccc !important;
            color: #666666 !important;
            cursor: not-allowed;
            opacity: 0.7;
        }

        button[disabled]:hover {
            background-color: #cccccc !important;
            color: #666666 !important;
        }

        .btn-full {
            width: 100%;
            text-align: center;
            padding: 15px;
            font-size: 1.1rem;
        }

        .btn-outline {
            background-color: transparent;
            color: midnightblue;
            border: 1px solid midnightblue;
        }

        .btn-outline:hover {
            background-color: rgba(25, 25, 112, 0.1);
            color: midnightblue;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }
    </style>

    <!-- Dashboard Styles -->
    <style>
        .dashboard-container {
            min-height: 100vh;
            display: flex;
        }

        main {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
        }

        @media (max-width: 992px) {
            main {
                margin-left: 80px;
            }
        }
    </style>

    <!-- Page Styles -->
    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 2rem;
            color: midnightblue;
        }

        /* Table Styles */
        .package-table-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 30px;
            overflow-x: auto;
        }

        .package-table {
            width: 100%;
            border-collapse: collapse;
        }

        .package-table th {
            background-color: #f8f9fa;
            padding: 12px 15px;
            text-align: left;
            color: #444;
            font-weight: 600;
            border-bottom: 2px solid #eee;
        }

        .package-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            color: #333;
        }

        .package-table tr:hover {
            background-color: #f9f9f9;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
            border: none;
        }

        .btn-edit {
            background-color: #ffc107;
            color: #212529;
        }

        .btn-edit:hover {
            background-color: #e0a800;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            max-width: 700px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.3s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 1.5rem;
            color: midnightblue;
        }

        .close {
            color: #aaa;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #444;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: midnightblue;
            box-shadow: 0 0 0 2px rgba(25, 25, 112, 0.1);
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .modal-footer {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        /* Image upload styles */
        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .image-preview {
            position: relative;
            width: 120px;
            height: 80px;
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid #ddd;
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-preview .remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
        }

        .image-preview .remove-image:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }

        .file-upload-container {
            margin-top: 10px;
        }

        .file-upload-btn {
            display: inline-block;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px 15px;
            cursor: pointer;
            font-size: 0.9rem;
            color: #444;
            transition: all 0.3s;
        }

        .file-upload-btn:hover {
            background-color: #e9ecef;
            border-color: #ced4da;
        }

        .file-upload-btn .material-icons {
            font-size: 1rem;
            margin-right: 5px;
            vertical-align: middle;
        }

        .hidden-file-input {
            display: none;
        }

        .existing-image-item {
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px;
            border: 1px solid #eee;
            border-radius: 4px;
        }

        .existing-image-item .remove-existing {
            color: #dc3545;
            cursor: pointer;
        }

        /* Confirm modal styles */
        .confirm-modal .modal-content {
            max-width: 400px;
        }

        .confirm-text {
            margin-bottom: 20px;
            color: #333;
        }

        /* Table empty state */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #777;
        }

        .empty-state .icon {
            font-size: 3rem;
            margin-bottom: 10px;
            color: #ddd;
        }
    </style>

    <!-- Material Icons -->
    <link
        href="https://fonts.googleapis.com/icon?family=Material+Icons"
        rel="stylesheet"
    >
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">Package Management</h1>
                <button
                    id="createPackageBtn"
                    class="btn"
                >
                    <span
                        class="material-icons"
                        style="vertical-align: middle"
                    >
                        add
                    </span>
                    Create Package
                </button>
            </div>

            <?php
            // Banner for operation result
            if (isset($_GET['success'])) {
                echo '<div style="background:#d4edda;color:#155724;padding:15px;text-align:center;margin-bottom:20px;border-radius:5px;">Operation successful.</div>';
            } else if (isset($_GET['deleted'])) {
                echo '<div style="background:#d4edda;color:#155724;padding:15px;text-align:center;margin-bottom:20px;border-radius:5px;">Package deleted successfully.</div>';
            } else if (isset($_GET['delete_failed'])) {
                echo '<div style="background:#f8d7da;color:#721c24;padding:15px;text-align:center;margin-bottom:20px;border-radius:5px;">Delete failed: There are existing orders for this package.</div>';
            } else if (isset($_GET['error'])) {
                echo '<div style="background:#f8d7da;color:#721c24;padding:15px;text-align:center;margin-bottom:20px;border-radius:5px;">Operation failed. Please try again.</div>';
            }
            ?>

            <!-- Package Table -->
            <div class="package-table-container">
                <?php if (count($packages) > 0): ?>
                    <table class="package-table">
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
                                                class="action-btn btn-edit"
                                                onclick="editPackage(<?php echo htmlspecialchars(json_encode($package)); ?>)"
                                            >
                                                <span class="material-icons">edit</span> Edit
                                            </button>
                                            <button
                                                class="action-btn btn-delete"
                                                onclick="confirmDelete(<?php echo $package['id']; ?>, '<?php echo htmlspecialchars($package['name']); ?>')"
                                            >
                                                <span class="material-icons">delete</span> Delete
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
                            <span class="material-icons">luggage</span>
                        </div>
                        <p>No packages available. Create your first package to get started.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Package Modal (Create/Edit) -->
            <div
                id="packageModal"
                class="modal"
            >
                <div class="modal-content">
                    <div class="modal-header">
                        <h2
                            class="modal-title"
                            id="modalTitle"
                        >Create New Package</h2>
                        <span
                            class="close"
                            onclick="closeModal('packageModal')"
                        >&times;</span>
                    </div>

                    <form
                        id="packageForm"
                        enctype="multipart/form-data"
                        method="post"
                        action="add-package.php"
                    >
                        <input
                            type="hidden"
                            id="package_id"
                            name="package_id"
                        >

                        <!-- Basic Information -->
                        <div class="form-group">
                            <label for="package_name">Package Name</label>
                            <input
                                type="text"
                                id="package_name"
                                name="package_name"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="subtitle">Subtitle</label>
                            <input
                                type="text"
                                id="subtitle"
                                name="subtitle"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="price">Price ($)</label>
                                <input
                                    type="number"
                                    id="price"
                                    name="price"
                                    class="form-control"
                                    min="0"
                                    required
                                >
                            </div>
                            <div class="form-group">
                                <label for="duration">Duration</label>
                                <input
                                    type="text"
                                    id="duration"
                                    name="duration"
                                    placeholder="e.g. 7 days / 6 nights"
                                    class="form-control"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="group_size">Group Size</label>
                                <input
                                    type="number"
                                    id="group_size"
                                    name="group_size"
                                    class="form-control"
                                    min="1"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="start_location">Start Location</label>
                                <input
                                    type="text"
                                    id="start_location"
                                    name="start_location"
                                    class="form-control"
                                    required
                                >
                            </div>
                            <div class="form-group">
                                <label for="end_location">End Location</label>
                                <input
                                    type="text"
                                    id="end_location"
                                    name="end_location"
                                    class="form-control"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea
                                id="description"
                                name="description"
                                class="form-control"
                                rows="4"
                                required
                            ></textarea>
                        </div>

                        <!-- Trip Details -->
                        <div class="form-group">
                            <label for="highlights">Highlights (one per line)</label>
                            <textarea
                                id="highlights"
                                name="highlights"
                                class="form-control"
                                rows="5"
                                required
                            ></textarea>
                        </div>

                        <div class="form-group">
                            <label for="includes">Includes (one per line)</label>
                            <textarea
                                id="includes"
                                name="includes"
                                class="form-control"
                                rows="5"
                                required
                            ></textarea>
                        </div>

                        <div class="form-group">
                            <label for="excludes">Excludes (one per line)</label>
                            <textarea
                                id="excludes"
                                name="excludes"
                                class="form-control"
                                rows="5"
                                required
                            ></textarea>
                        </div>

                        <!-- Itinerary -->
                        <div class="form-group">
                            <label for="itinerary">Itinerary (format: Day X: Title | Description)</label>
                            <textarea
                                id="itinerary"
                                name="itinerary"
                                class="form-control"
                                rows="5"
                                placeholder="Day 1: Arrival in Bali | Arrive at airport where you'll be greeted by your guide.&#10;Day 2: Sacred Temples | Visit the iconic sea temple of Tanah Lot."
                                required
                            ></textarea>
                        </div>

                        <!-- Images -->
                        <div class="form-group">
                            <label for="package_images">Images</label>
                            <input
                                type="hidden"
                                name="existing_images"
                                id="existing_images"
                            >

                            <!-- Image Preview Container -->
                            <div
                                id="imagePreviewContainer"
                                class="image-preview-container"
                            ></div>

                            <!-- Existing Images List (when editing) -->
                            <div id="existingImagesContainer"></div>

                            <div class="file-upload-container">
                                <label class="file-upload-btn">
                                    <span class="material-icons">upload</span> Upload Images
                                    <input
                                        type="file"
                                        id="package_images"
                                        name="package_images[]"
                                        class="hidden-file-input"
                                        accept="image/*"
                                        multiple
                                    >
                                </label>
                                <small class="form-text text-muted">You can select multiple images at once.</small>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-outline"
                                onclick="closeModal('packageModal')"
                            >Cancel
                            </button>
                            <button
                                type="submit"
                                class="btn"
                            >Save Package
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div
                id="deleteModal"
                class="modal confirm-modal"
            >
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title">Confirm Deletion</h2>
                        <span
                            class="close"
                            onclick="closeModal('deleteModal')"
                        >&times;</span>
                    </div>

                    <p
                        class="confirm-text"
                        id="deleteConfirmText"
                    >Are you sure you want to delete this package? This action cannot be undone.</p>

                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-outline"
                            onclick="closeModal('deleteModal')"
                        >Cancel
                        </button>
                        <button
                            type="button"
                            class="btn btn-danger"
                            id="confirmDeleteBtn"
                        >Delete Package
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Create Package Button Event Listener
            document.getElementById("createPackageBtn").addEventListener("click", function () {
                openCreatePackageModal();
            });

            // Initialize image preview for file uploads
            document.getElementById("package_images").addEventListener("change", handleImageUpload);
        });

        // Function to open modal for creating a new package
        function openCreatePackageModal() {
            // Reset form
            document.getElementById("packageForm").reset();
            document.getElementById("package_id").value = "";
            document.getElementById("modalTitle").textContent = "Create New Package";
            document.getElementById("imagePreviewContainer").innerHTML = "";
            document.getElementById("existingImagesContainer").innerHTML = "";
            document.getElementById("existing_images").value = "";

            // Open modal
            openModal("packageModal");
        }

        // Function to edit an existing package
        function editPackage(package) {
            // Set form title
            document.getElementById("modalTitle").textContent = "Edit Package";

            // Populate form fields with package data
            document.getElementById("package_id").value = package.id;
            document.getElementById("package_name").value = package.name;
            document.getElementById("subtitle").value = package.subtitle;
            document.getElementById("price").value = package.price;
            document.getElementById("duration").value = package.duration;
            document.getElementById("group_size").value = package.group_size;
            document.getElementById("start_location").value = package.start_location;
            document.getElementById("end_location").value = package.end_location;
            document.getElementById("description").value = package.description;

            // Handle array fields by joining with newlines
            document.getElementById("highlights").value = package.highlights.join("\n");
            document.getElementById("includes").value = package.includes.join("\n");
            document.getElementById("excludes").value = package.excludes.join("\n");

            // Handle the itinerary (key-value pairs)
            let itineraryLines = [];
            for (const [day, description] of Object.entries(package.itinerary)) {
                itineraryLines.push(`${day} | ${description}`);
            }
            document.getElementById("itinerary").value = itineraryLines.join("\n");

            // Handle existing images
            displayExistingImages(package.images);

            // Set form action to edit
            document.getElementById("packageForm").action = "edit-package.php";

            // Open modal
            openModal("packageModal");
        }

        // Function to display existing images when editing a package
        function displayExistingImages(images) {
            const existingImagesContainer = document.getElementById("existingImagesContainer");
            existingImagesContainer.innerHTML = "";

            if (images && images.length > 0) {
                // Store image URLs in hidden input for form submission
                document.getElementById("existing_images").value = JSON.stringify(images);

                // Create heading for existing images section
                const heading = document.createElement("h4");
                heading.style.marginTop = "15px";
                heading.style.marginBottom = "10px";
                heading.textContent = "Current Images:";
                existingImagesContainer.appendChild(heading);

                // Create image previews
                const previewContainer = document.createElement("div");
                previewContainer.className = "image-preview-container";

                images.forEach((imageUrl, index) => {
                    const previewDiv = document.createElement("div");
                    previewDiv.className = "image-preview";

                    const img = document.createElement("img");
                    img.src = imageUrl;
                    img.alt = "Package Image";

                    const removeButton = document.createElement("span");
                    removeButton.className = "remove-image";
                    removeButton.textContent = "×";
                    removeButton.onclick = function () {
                        removeExistingImage(index);
                    };

                    previewDiv.appendChild(img);
                    previewDiv.appendChild(removeButton);
                    previewContainer.appendChild(previewDiv);
                });

                existingImagesContainer.appendChild(previewContainer);
            }
        }

        // Function to remove an existing image
        function removeExistingImage(index) {
            const existingImagesInput = document.getElementById("existing_images");
            const images = JSON.parse(existingImagesInput.value);

            images.splice(index, 1);
            existingImagesInput.value = JSON.stringify(images);

            // Update the display
            displayExistingImages(images);
        }

        // Function to handle image file uploads
        function handleImageUpload(e) {
            const files = e.target.files;
            const imagePreviewContainer = document.getElementById("imagePreviewContainer");

            // Clear previous uploads
            imagePreviewContainer.innerHTML = "";

            if (files.length > 0) {
                const heading = document.createElement("h4");
                heading.style.marginTop = "15px";
                heading.style.marginBottom = "10px";
                heading.textContent = "New Uploads:";
                imagePreviewContainer.appendChild(heading);

                // Process each file
                Array.from(files).forEach((file, index) => {
                    const reader = new FileReader();

                    reader.onload = function (event) {
                        const previewDiv = document.createElement("div");
                        previewDiv.className = "image-preview";

                        const img = document.createElement("img");
                        img.src = event.target.result;
                        img.alt = "Preview";

                        const removeButton = document.createElement("span");
                        removeButton.className = "remove-image";
                        removeButton.textContent = "×";
                        removeButton.dataset.index = index;
                        removeButton.onclick = function () {
                            removeUploadedImage(this.dataset.index);
                        };

                        previewDiv.appendChild(img);
                        previewDiv.appendChild(removeButton);
                        imagePreviewContainer.appendChild(previewDiv);
                    };

                    reader.readAsDataURL(file);
                });
            }
        }

        // Function to remove an uploaded image
        function removeUploadedImage(index) {
            const fileInput = document.getElementById("package_images");
            const dt = new DataTransfer();

            // Keep all files except the one to be removed
            Array.from(fileInput.files)
                .filter((_, i) => i != index)
                .forEach(file => dt.items.add(file));

            // Update the file input with the new file list
            fileInput.files = dt.files;

            // Update preview
            handleImageUpload({target: fileInput});
        }

        // Function to show delete confirmation dialog
        function confirmDelete(id, name) {
            document.getElementById("deleteConfirmText").textContent =
                `Are you sure you want to delete "${name}"? This action cannot be undone.`;
            document.getElementById("confirmDeleteBtn").onclick = function () {
                // Submit form to delete-package.php
                var form = document.createElement("form");
                form.method = "POST";
                form.action = "delete-package.php";
                var input = document.createElement("input");
                input.type = "hidden";
                input.name = "package_id";
                input.value = id;
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            };
            openModal("deleteModal");
        }

        // Function to delete a package
        function deletePackage(id) {
            // In a real application, this would send a request to delete the package
            // For this demo, we'll just show a success message
            alert(`Package #${id} deleted successfully!`);
            closeModal("deleteModal");

            // In a real application, you would refresh the data or update the table
        }

        // Function to open a modal
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "block";
            document.body.style.overflow = "hidden"; // Prevent scrolling
        }

        // Function to close a modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
            document.body.style.overflow = "auto"; // Enable scrolling
        }

        // Close modals when clicking outside
        window.addEventListener("click", function (event) {
            const packageModal = document.getElementById("packageModal");
            const deleteModal = document.getElementById("deleteModal");

            if (event.target === packageModal) {
                closeModal("packageModal");
            }

            if (event.target === deleteModal) {
                closeModal("deleteModal");
            }
        });
    </script>
</body>
</html>
