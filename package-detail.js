import { closeModal, openModal } from "./modal.js";

// Image Gallery Functionality
function changeImage(src, thumbnail) {
    // Update main image source
    document.getElementById("mainImage").src = src;

    // Remove active class from all thumbnails
    document.querySelectorAll(".thumbnail").forEach(thumb => {
        thumb.classList.remove("active");
    });

    // Add active class to clicked thumbnail
    thumbnail.classList.add("active");
}

// Modal Functionality
document.addEventListener("DOMContentLoaded", function () {
    const modalId = "bookingModal";
    const bookNowBtn = document.getElementById("bookNowBtn");
    const cancelBtn = document.getElementById("cancelBooking");
    const bookingForm = document.getElementById("bookingForm");
    const submitBooking = document.getElementById("submitBooking");

    // Open modal when Book Now is clicked
    bookNowBtn.addEventListener("click", function () {
        openModal(modalId);
    });

    // Close modal when Cancel is clicked
    cancelBtn.addEventListener("click", function () {
        closeModal(modalId);
    });

    // Handle form submission
    submitBooking.addEventListener("click", async function (event) {
        event.preventDefault();

        const formData = new FormData(bookingForm);
        try {
            const response = await fetch("add-order.php", {
                method: "POST", body: formData,
            });
            const result = await response.json();

            if (result.success) {
                alert(result.message);
            } else {
                alert(result.message || "Failed to create order");
            }
        } catch (e) {
            alert(e);
        }

        closeModal(modalId);

        // Reset form
        bookingForm.reset();

        window.location.href = "order-history.php";
    });

    // Set min date for departure date input to today
    const today = new Date().toISOString().split("T")[0];
    document.getElementById("departureDate").setAttribute("min", today);
});

window.changeImage = changeImage;