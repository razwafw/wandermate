// Function to set scroll padding based on header height
function setScrollPadding() {
    const header = document.querySelector("header");
    const headerHeight = header.offsetHeight;
    document.documentElement.style.scrollPaddingTop = headerHeight + "px";
}

// Set initial scroll padding
window.addEventListener("DOMContentLoaded", setScrollPadding);

// Update scroll padding on window resize
window.addEventListener("resize", setScrollPadding);