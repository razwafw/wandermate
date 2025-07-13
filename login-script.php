<?php
session_start();
$error = FALSE;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $conn = new mysqli('localhost', 'projec15_root', '@kaesquare123', 'projec15_wandermate');
    if ($conn->connect_error) {
        die('Database connection failed: ' . $conn->connect_error);
    }
    $stmt = $conn->prepare('SELECT id, role_id, password FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $role_id, $db_password);
        $stmt->fetch();
        if ($password === $db_password) { // For demo only; use password_hash in production
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role_id'] = $role_id;
            header('Location: index.php');
            exit();
        } else {
            $error = TRUE;
        }
    } else {
        $error = TRUE;
    }
    $stmt->close();
    $conn->close();
}
// If not redirected, show error or redirect back to login.php with error
if ($error) {
    header('Location: login.php?error=1');
    exit();
}
