<?php
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    http_response_code(403);
    exit('Direct access not allowed.');
}

global $loggedIn, $role_id;

if (!$loggedIn) {
    $modalId = 'loginModal';
    $modalTitle = 'Login';
    $redirect = $_SERVER["REQUEST_URI"];
    $modalContent = "
        <form
            action='login.php'
            method='post'
        >
            <input type='hidden' name='redirect' value='$redirect'>
        
            <div class='form-group'>
                <label for='email'>Email Address</label>
                <input
                    type='email'
                    id='email'
                    name='email'
                    class='form-control'
                    required
                    placeholder='Enter your email'
                >
            </div>
    
            <div class='form-group'>
                <label for='password'>Password</label>
                <input
                    type='password'
                    id='password'
                    name='password'
                    class='form-control'
                    required
                    placeholder='Enter your password'
                >
            </div>
    
            <button
                type='submit'
                class='btn btn-full'
            >
                Login
            </button>
        </form>
    ";
    $modalFooter = "
        <div class='register-prompt'>
            <p>Don't have an account?
                <a href='#'>Register now</a>
            </p>
        </div>
    ";

    include 'modal.php';

    echo "
        <script type='module'>
            import { openModal } from './modal.js';
            
            document.getElementById('loginBtn').addEventListener('click', function () {
                openModal('loginModal');
            });
        </script>
    ";
}
?>

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
                        <button
                            id="loginBtn"
                            class="btn btn-sm"
                        >
                            Login
                        </button>
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