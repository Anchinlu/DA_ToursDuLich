<?php
require_once __DIR__ . '/../vendor/autoload.php'; 

use Cloudinary\Configuration\Configuration;
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