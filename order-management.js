import { closeModal, openModal } from "./modal.js";

// Store current order ID for operations
let currentOrderId = null;

// Initialize event listeners once the DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("closeOrderDetailsBtn").addEventListener("click", function () {
        closeModal("orderDetailsModal");
    });

    document.getElementById("closeConfirmOrderModalBtn").addEventListener("click", function () {
        closeModal("confirmOrderModal");
        document.getElementById("itineraryFile").value = "";
    });

    // Add click event to all order rows
    const orderRows = document.querySelectorAll(".order-row");
    orderRows.forEach(row => {
        row.addEventListener("click", function (e) {
            // Ignore clicks on action buttons
            if (!e.target.closest(".action-buttons")) {
                const orderData = JSON.parse(this.getAttribute("data-order"));
                showOrderDetails(orderData);
            }
        });
    });

    // Set up filter buttons
    const filterButtons = document.querySelectorAll(".filter-btn");
    filterButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove("active"));

            // Add active class to clicked button
            this.classList.add("active");

            // Apply filter
            const filter = this.getAttribute("data-filter");
            filterOrders(filter);
        });
    });

    // Set up confirm order button
    document.getElementById("confirmOrderBtn").addEventListener("click", function () {
        confirmOrder();
    });
});

// Function to show order details in the modal
function showOrderDetails(order) {
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
                    <div class="detail-label">Booking ID:</div>
                    <div class="detail-value">#${order.id}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value"><span class="${statusClass}">${statusLabel}</span></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Booking Date:</div>
                    <div class="detail-value">${bookingDate}</div>
                </div>

                <div class="modal-divider"></div>

                <div class="detail-row">
                    <div class="detail-label">Customer ID:</div>
                    <div class="detail-value">${order.customer_id}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Customer Name:</div>
                    <div class="detail-value">${order.customer_name}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Email:</div>
                    <div class="detail-value">${order.email}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Phone:</div>
                    <div class="detail-value">${order.phone}</div>
                </div>

                <div class="modal-divider"></div>

                <div class="detail-row">
                    <div class="detail-label">Package ID:</div>
                    <div class="detail-value">${order.package_id}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Package Name:</div>
                    <div class="detail-value">${order.package_name}</div>
                </div>
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
                </div>`;

    if (order.special_requests) {
        content += `
                <div class="detail-row">
                    <div class="detail-label">Special Requests:</div>
                    <div class="detail-value">${order.special_requests}</div>
                </div>`;
    }

    if (order.status === "confirmed" && order.itinerary_file) {
        content += `
                <div class="modal-divider"></div>

                <div class="detail-row">
                    <div class="detail-label">Itinerary:</div>
                    <div class="detail-value">
                        <a href="${order.itinerary_file}" target="_blank" class="btn btn-sm btn-outline">
                            <span class="fa-solid fa-file-lines" style="font-size: 16px; margin-right: 5px;"></span>
                            View Itinerary
                        </a>
                    </div>
                </div>`;
    }

    document.getElementById("orderDetailsContent").innerHTML = content;
    openModal("orderDetailsModal");
}

// Function to open the confirmation order modal
function openConfirmOrderModal(event, order) {
    event.stopPropagation();
    currentOrderId = order.id;

    document.getElementById("confirmOrderSummary").innerHTML = `
                <div class="detail-row">
                    <div class="detail-label">Order ID:</div>
                    <div class="detail-value">#${order.id}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Customer:</div>
                    <div class="detail-value">${order.customer_name}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Package:</div>
                    <div class="detail-value">${order.package_name}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Departure:</div>
                    <div class="detail-value">
                        ${new Date(order.departure_date).toLocaleDateString("en-US", {
        year: "numeric", month: "long", day: "numeric",
    })}
                    </div>
                </div>
            `;
    openModal("confirmOrderModal");
}

// Function to confirm order
function confirmOrder() {
    if (!currentOrderId) {
        alert("Order ID is missing.");
        return;
    }
    // Read the file content from the file input
    const fileInput = document.getElementById("itineraryFile");
    if (!fileInput.files || fileInput.files.length === 0) {
        alert("Please select an itinerary file.");
        return;
    }
    const file = fileInput.files[0];
    const reader = new FileReader();
    reader.onload = function (e) {
        const itineraryContent = e.target.result;
        // Send AJAX request to confirm-order.php
        fetch("confirm-order.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: "order_id=" + encodeURIComponent(currentOrderId) + "&itinerary_content=" + encodeURIComponent(itineraryContent),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the row UI
                    const row = document.querySelector(`tr[data-order-id="${currentOrderId}"]`);
                    if (row) {
                        const orderStatus = row.querySelector(".order-status");
                        orderStatus.textContent = "Confirmed";
                        orderStatus.classList.remove("status-pending");
                        orderStatus.classList.add("status-confirmed");

                        row.setAttribute("data-status", "confirmed");
                        // Update action button to View Itinerary
                        const actionCell = row.querySelector(".action-buttons");
                        if (actionCell) {
                            actionCell.innerHTML = `<button class=\"action-btn btn-success\" onclick=\"viewItinerary(event, '${data.itinerary_url}', ${currentOrderId})\"><span class=\"fa-solid fa-file-lines\"></span>View Itinerary</button>`;
                        }
                        // Update data-order attribute
                        const orderData = JSON.parse(row.getAttribute("data-order"));
                        orderData.status = "confirmed";
                        orderData.itinerary_file = data.itinerary_url;
                        row.setAttribute("data-order", JSON.stringify(orderData));
                    }
                    closeModal("confirmOrderModal");
                    document.getElementById("itineraryFile").value = "";
                    alert("Order has been confirmed successfully");
                } else {
                    alert(data.message || "Failed to confirm order.");
                }
            })
            .catch(() => {
                alert("An error occurred while confirming the order.");
            });
    };
    reader.onerror = function () {
        alert("Failed to read the itinerary file.");
    };
    reader.readAsText(file);
}

// Function to view itinerary
function viewItinerary(event, fileName) {
    event.stopPropagation();

    // In a real application, this would open the file or download it
    // For this demo, we'll just show a message
    if (fileName) {
        window.open(fileName, "_blank");
    } else {
        alert("Itinerary file not found");
    }
}

// Function to filter orders by status
function filterOrders(filter) {
    const rows = document.querySelectorAll(".order-row");
    let visibleCount = 0;

    rows.forEach(row => {
        const status = row.getAttribute("data-status");

        if (filter === "all" || status === filter) {
            row.style.display = "";
            visibleCount++;
        } else {
            row.style.display = "none";
        }
    });

    // Show/hide empty state message
    document.getElementById("empty-state").style.display = visibleCount === 0 ? "block" : "none";
}

window.openConfirmOrderModal = openConfirmOrderModal;
window.viewItinerary = viewItinerary;
window.filterOrders = filterOrders;