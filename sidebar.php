<?php
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    http_response_code(403);
    exit('Direct access not allowed.');
}
?>

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
                    <i
                        class="fa-solid fa-gauge icon"
                        title="Dashboard"
                    ></i>
                    <span>Dashboard</span>
                </a>
            </li>
        </ul>
        <ul>
            <li>
                <a
                    href="index.php"
                    class="nav-link"
                    target="_blank"
                >
                    <i
                        class="fa-solid fa-eye icon"
                        title="View Site"
                    ></i>
                    <span>View Site</span>
                </a>
            </li>
        </ul>
        <ul>
            <li>
                <a
                    href="logout.php"
                    class="nav-link"
                >
                    <i
                        class="fa-solid fa-right-from-bracket icon"
                        title="Logout"
                    ></i>
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
                    <i
                        class="fa-solid fa-suitcase-rolling icon"
                        title="Manage Packages"
                    ></i>
                    <span>Packages</span>
                </a>
            </li>
            <li>
                <a
                    href="order-management.php"
                    class="nav-link"
                >
                    <i
                        class="fa-solid fa-cart-shopping icon"
                        title="Manage Orders"
                    ></i>
                    <span>Orders</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <p>&copy; 2025 WanderMate</p>
    </div>
</aside>
