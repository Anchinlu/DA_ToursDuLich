
<?php
$host = 'localhost';
$db_name = 'db_tourdulich';
$username = 'root';
$password = ''; 
define('BASE_URL', 'http://localhost:8080/DoAn_TourDuLich/');
try {
    $dsn = "mysql:host=$host;dbname=$db_name;charset=utf8mb4";
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Lỗi kết nối: " . $e->getMessage();
    die();
}
?>