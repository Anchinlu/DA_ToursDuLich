<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Đảm bảo đường dẫn đúng tới vendor

use Cloudinary\Configuration\Configuration;

// Thay thế các thông số bên dưới bằng thông tin trong Dashboard Cloudinary của bạn
Configuration::instance([
    'cloud' => [
        'cloud_name' => 'dmaeuom2i', 
        'api_key'    => '836927744485971', 
        'api_secret' => 'ThqGra97zeK8EdjWYouPLdst3SM'
    ],
    'url' => [
        'secure' => true 
    ]
]);
?>