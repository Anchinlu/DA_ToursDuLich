<?php
// Nhúng autoload và file cấu hình (đã lùi 1 cấp)
require_once '../vendor/autoload.php';
require_once '../config/google_setup.php';

// Tạo đường dẫn đăng nhập
$login_url = $client->createAuthUrl();

// Chuyển hướng người dùng
header("Location: " . $login_url);
exit;
?>