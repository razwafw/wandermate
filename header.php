<?php
session_start();
$loggedIn = isset($_SESSION['user_id']);
$role_id = $_SESSION['role_id'] ?? 1;
?>

<link
    rel="stylesheet"
    href="header.css"
>

<header>
    <div class="container nav-container">
        <a
            href="index.php"
            class="logo"
        >
            Wander<span>Mate</span>
        </a>
        <nav>
            <ul>
                <li>
                    <a href="index.php">Home</a>
                </li>
                <li>
                    <a href="package-list.php">Packages</a>
                </li>

                <?php if (!$loggedIn): ?>
                    <li>
                        <a
                            href="login.php"
                            class="btn btn-sm"
                        >
                            Login
                        </a>
                    </li>
                <?php else: ?>
                    <?php if ($role_id === 1): ?>
                        <li>
                            <a href="order-history.php">My Orders</a>
                        </li>
                    <?php elseif ($role_id === 2): ?>
                        <li>
                            <a href="dashboard.php">Dashboard</a>
                        </li>
                    <?php endif; ?>

                    <li>
                        <a
                            href="logout.php"
                            class="btn btn-sm"
                        >
                            Logout
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<script src="header.js"></script>