<?php
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    http_response_code(403);
    exit('Direct access not allowed.');
}
?>

<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-column">
                <h3>WanderMate</h3>
                <p>Your trusted travel partner since 2018. We specialize in creating unforgettable travel experiences across the globe.</p>
            </div>
            <div class="footer-column">
                <h3>Contact Us</h3>
                <p>123 Travel Street, Suite 100</p>
                <p>New York, NY 10001</p>
                <p>Phone: (123) 456-7890</p>
                <p>Email: info@wandermate.com</p>
            </div>
            <div class="footer-column">
                <h3>Quick Links</h3>
                <a href="index.php">Home</a>
                <a href="index.php#about">About Us</a>
                <a href="package-list.php">Packages</a>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; <?php echo date("Y"); ?> WanderMate Travel Agency. All Rights Reserved.</p>
        </div>
    </div>
</footer>