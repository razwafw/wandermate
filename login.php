<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
$error = isset($_GET['error']) && $_GET['error'] == 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>WanderMate - Login</title>

    <!-- Global Styles -->
    <style>
        html {
            scroll-behavior: smooth;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #fff;
            color: #000;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .btn {
            display: inline-block;
            background-color: midnightblue;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #1a1a5a;
            color: #fff;
        }

        button[disabled] {
            background-color: #cccccc !important;
            color: #666666 !important;
            cursor: not-allowed;
            opacity: 0.7;
        }

        button[disabled]:hover {
            background-color: #cccccc !important;
            color: #666666 !important;
        }

        .btn-full {
            width: 100%;
            text-align: center;
            padding: 15px;
            font-size: 1.1rem;
        }

        .btn-outline {
            background-color: transparent;
            color: midnightblue;
            border: 1px solid midnightblue;
        }

        .btn-outline:hover {
            background-color: rgba(25, 25, 112, 0.1);
            color: midnightblue;
        }
    </style>

    <!-- Page Styles-->
    <style>
        /* Login Form Styles */
        .login-container {
            max-width: 450px;
            margin: 80px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-title {
            font-size: 2rem;
            color: midnightblue;
            margin-bottom: 10px;
        }

        .login-subtitle {
            color: #666;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #444;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: midnightblue;
            box-shadow: 0 0 0 2px rgba(25, 25, 112, 0.1);
        }

        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .form-footer a {
            color: midnightblue;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #e74c3c;
            background-color: #fdf3f2;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }

        .register-prompt {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #eee;
            color: #666;
        }

        .register-prompt a {
            color: midnightblue;
            text-decoration: none;
            font-weight: 500;
        }

        .register-prompt a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h1 class="login-title">Welcome Back</h1>
                <p class="login-subtitle">Log in to access your account</p>
            </div>
            <?php if ($error): ?>
                <div
                    id="errorMessage"
                    class="error-message"
                    style="display:block;"
                >
                    Invalid email or password. Please try again.
                </div>
            <?php endif; ?>
            <form
                action="login-script.php"
                method="post"
            >
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        required
                        placeholder="Enter your email"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        required
                        placeholder="Enter your password"
                    >
                </div>

                <button
                    type="submit"
                    class="btn btn-full"
                >
                    Login
                </button>

                <div class="register-prompt">
                    <p>Don't have an account?
                        <a href="#">Register now</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
