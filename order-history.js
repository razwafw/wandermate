import { closeModal, openModal } from "./modal.js";

document.getElementById("closeOrderDetailsBtn").addEventListener("click", function () {
    closeModal("orderDetailsModal");
});

document.getElementById("cancelCancelBtn").addEventListener("click", function () {
    closeModal("cancellationModal");
});

// Store current order ID for cancellation
let currentOrderId = null;

// Function to show order details in the modal
function showOrderDetails(order) {
    // Set modal content
    const formattedDate = new Date(order.departure_date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });

    const bookingDate = new Date(order.booking_date).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });

    const statusClass = `status-badge status-${order.status}`;
    const statusLabel = order.status.charAt(0).toUpperCase() + order.status.slice(1);

    let content = `
        <div class="detail-row">
            <div class="detail-label">Package:</div>
            <div class="detail-value">${order.package_name}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Status:</div>
            <div class="detail-value"><span class="${statusClass}">${statusLabel}</span></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Booking ID:</div>
            <div class="detail-value">#${order.id}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Booking Date:</div>
            <div class="detail-value">${bookingDate}</div>
        </div>
    
        <div class="modal-divider"></div>
    
        <div class="detail-row">
            <div class="detail-label">Departure Date:</div>
            <div class="detail-value">${formattedDate}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Number of Travelers:</div>
            <div class="detail-value">${order.travelers}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Total Price:</div>
            <div class="detail-value">$${parseInt(order.total_price).toFixed(2)}</div>
        </div>
    `;

    if (order.special_requests) {
        content += `
            <div class="detail-row">
                <div class="detail-label">Special Requests:</div>
                <div class="detail-value">${order.special_requests}</div>
            </div>
        `;
    }

    document.getElementById("orderDetailsContent").innerHTML = content;
    openModal("orderDetailsModal");
}

// Function to view itinerary
function viewItinerary(event, fileUrl) {
    event.stopPropagation();
    if (fileUrl) {
        window.open(fileUrl, "_blank");
    } else {
        alert("Itinerary file not found");
    }
}

// Function to show cancellation confirmation modal
function confirmCancellation(event, orderId) {
    // Stop event propagation
    event.stopPropagation();

    // Store the order ID
    currentOrderId = orderId;

    // Update the confirm button to use the current order ID
    document.getElementById("confirmCancelBtn").onclick = function () {
        cancelOrder(currentOrderId);
    };

    // Show the modal
    openModal("cancellationModal");
}

// Function to cancel the order
function cancelOrder(orderId) {
    if (!orderId) {
        alert("Order ID is missing.");
        return;
    }
    // Send AJAX request to cancel-order.php
    fetch("cancel-order.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: "order_id=" + encodeURIComponent(orderId),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Find the row with the order
                const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                if (row) {
                    // Update the status badge
                    const statusCell = row.querySelector("td:nth-child(4)");
                    statusCell.innerHTML = "<span class=\"status-badge status-cancelled\">Cancelled</span>";
                    // Remove the cancel button
                    const cancelBtn = row.querySelector(".btn-danger");
                    if (cancelBtn) {
                        cancelBtn.parentNode.removeChild(cancelBtn);
                    }
                }
                closeModal("cancellationModal");
                alert("Your booking has been cancelled successfully.");
            } else {
                alert(data.message || "Failed to cancel booking.");
            }
        })
        .catch(() => {
            alert("An error occurred while cancelling the booking.");
        });
}

window.showOrderDetails = showOrderDetails;
window.viewItinerary = viewItinerary;
window.confirmCancellation = confirmCancellation;
window.cancelOrder = cancelOrder;
