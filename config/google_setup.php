<?php
require_once __DIR__ . '/../vendor/autoload.php';
$google_client_id = 'YOUR_GOOGLE_CLIENT_ID'; 
$google_client_secret = 'YOUR_GOOGLE_CLIENT_SECRET';

$google_redirect_url = 'http://localhost/DoAn_TourDuLich/auth/google_callback.php';

$client = new Google_Client();
$client->setClientId($google_client_id);
$client->setClientSecret($google_client_secret);
$client->setRedirectUri($google_redirect_url);
$client->addScope('email');
$client->addScope('profile');
?>