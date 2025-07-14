<?php
require_once 'config.php';

session_start();

$error = TRUE;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    require_once 'DatabaseConnection.php';

    $conn = new DatabaseConnection();

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

        if ($password === $db_password) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role_id'] = $role_id;

            header('Location: index.php');
            exit();
        }
    }

    $stmt->close();
    $conn->close();
}

if ($error) {
    header('Location: login.php?error=1');
    exit();
}
