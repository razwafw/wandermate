import { closeModal, openModal } from "./modal.js";

document.addEventListener("DOMContentLoaded", function () {
    // Create Package Button Event Listener
    document.getElementById("createPackageBtn").addEventListener("click", function () {
        openCreatePackageModal();
    });

    // Initialize image preview for file uploads
    document.getElementById("package_images").addEventListener("change", handleImageUpload);

    document.getElementById("closePackageModalBtn").addEventListener("click", function () {
        closeModal("packageModal");
    });

    document.getElementById("closeDeleteModalBtn").addEventListener("click", function () {
        closeModal("deleteModal");
    });
});

// Function to open modal for creating a new package
function openCreatePackageModal() {
    // Reset form
    document.getElementById("packageForm").reset();
    document.getElementById("package_id").value = "";
    document.querySelector("#packageModal .modal-title").textContent = "Create New Package";
    document.getElementById("imagePreviewContainer").innerHTML = "";
    document.getElementById("existingImagesContainer").innerHTML = "";
    document.getElementById("existing_images").value = "";

    // Open modal
    openModal("packageModal");
}

// Function to edit an existing package
function editPackage(pkg) {
    // Set form title
    document.querySelector("#packageModal .modal-title").textContent = "Edit Package";

    // Populate form fields with package data
    document.getElementById("package_id").value = pkg.id;
    document.getElementById("package_name").value = pkg.name;
    document.getElementById("subtitle").value = pkg.subtitle;
    document.getElementById("price").value = pkg.price;
    document.getElementById("duration").value = pkg.duration;
    document.getElementById("group_size").value = pkg.group_size;
    document.getElementById("start_location").value = pkg.start_location;
    document.getElementById("end_location").value = pkg.end_location;
    document.getElementById("description").value = pkg.description;

    // Handle array fields by joining with newlines
    document.getElementById("highlights").value = pkg.highlights.join("\n");
    document.getElementById("includes").value = pkg.includes.join("\n");
    document.getElementById("excludes").value = pkg.excludes.join("\n");

    // Handle the itinerary (key-value pairs)
    let itineraryLines = [];
    for (const [day, description] of Object.entries(pkg.itinerary)) {
        itineraryLines.push(`${day} | ${description}`);
    }
    document.getElementById("itinerary").value = itineraryLines.join("\n");

    // Handle existing images
    displayExistingImages(pkg.images);

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
        .filter((_, i) => i !== Number(index))
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
        let form = document.createElement("form");
        form.method = "POST";
        form.action = "delete-package.php";
        let input = document.createElement("input");
        input.type = "hidden";
        input.name = "package_id";
        input.value = id;
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    };
    openModal("deleteModal");
}

window.openCreatePackageModal = openCreatePackageModal;
window.editPackage = editPackage;
window.confirmDelete = confirmDelete;