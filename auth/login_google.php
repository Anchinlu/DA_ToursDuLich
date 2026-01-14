<?php
require_once '../vendor/autoload.php';
require_once '../config/google_setup.php';

// Tạo đường dẫn đăng nhập
$login_url = $client->createAuthUrl();

// Chuyển hướng người dùng
header("Location: " . $login_url);
exit;
?>