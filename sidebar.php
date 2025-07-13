<?php
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    http_response_code(403);
    exit('Direct access not allowed.');
}
?>

<link
    rel="stylesheet"
    href="sidebar.css"
>

<aside class="sidebar">
    <div class="sidebar-brand">
        <h2>Wander<span>Mate</span></h2>
    </div>

    <div class="sidebar-menu">
        <ul>
            <li>
                <a
                    href="dashboard.php"
                    class="nav-link"
                >
                    <span class="material-icons icon">dashboard</span>
                    <span>Dashboard</span>
                </a>
            </li>
        </ul>
        <ul>
            <li>
                <a
                    href="logout.php"
                    class="nav-link"
                >
                    <span class="material-icons icon">logout</span>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
        <h3>Manage</h3>
        <ul>
            <li>
                <a
                    href="package-management.php"
                    class="nav-link"
                >
                    <span class="material-icons icon">luggage</span>
                    <span>Packages</span>
                </a>
            </li>
            <li>
                <a
                    href="order-management.php"
                    class="nav-link"
                >
                    <span class="material-icons icon">shopping_cart</span>
                    <span>Orders</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <p>&copy; 2025 WanderMate</p>
    </div>
</aside>
