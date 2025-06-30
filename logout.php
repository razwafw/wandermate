<?php
session_start();
if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION)) {
    session_unset();
    session_destroy();
}
header('Location: home.php');
exit();
