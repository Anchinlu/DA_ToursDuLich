<?php
session_start();

// Xóa tất cả các biến session
session_unset();

// Hủy session
session_destroy();

// Chuyển hướng về trang chủ ở thư mục gốc
header("Location: ../index.php");
exit;
?>