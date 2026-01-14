<?php
session_start();
require_once '../vendor/autoload.php';
require_once '../config/db_connect.php';
require_once '../config/google_setup.php';

if (isset($_GET['code'])) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        if(!isset($token['error'])){
            $client->setAccessToken($token['access_token']);
            
            $google_oauth = new Google_Service_Oauth2($client);
            $google_account_info = $google_oauth->userinfo->get();
        
            $email = $google_account_info->email;
            $name = $google_account_info->name;
            $google_id = $google_account_info->id;
            $avatar = $google_account_info->picture; 

            // Kiểm tra người dùng
            $stmt = $db->prepare("SELECT * FROM NguoiDung WHERE TenDangNhap = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $update = $db->prepare("UPDATE NguoiDung SET GoogleID = :gid, Avatar = :ava WHERE id = :id");
                $update->execute([
                    ':gid' => $google_id,
                    ':ava' => $avatar, 
                    ':id' => $user['id']
                ]);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['TenDangNhap'];
                $_SESSION['fullname'] = $user['TenDayDu'];
                $_SESSION['role'] = $user['VaiTro'];
                $_SESSION['avatar'] = $avatar; 

            } else {
                $sql = "INSERT INTO NguoiDung (TenDangNhap, MatKhau, TenDayDu, Avatar, VaiTro, TrangThai, GoogleID) 
                        VALUES (:email, '', :name, :avatar, 'user', 1, :gid)";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    ':email' => $email,
                    ':name' => $name,
                    ':avatar' => $avatar,
                    ':gid' => $google_id
                ]);
                
                $new_id = $db->lastInsertId();
                $_SESSION['user_id'] = $new_id;
                $_SESSION['username'] = $email;
                $_SESSION['fullname'] = $name;
                $_SESSION['role'] = 'user';
                $_SESSION['avatar'] = $avatar;
            }
            
            header("Location: ../index.php");
            exit;
        }
    } catch (Exception $e) {
        echo "Lỗi: " . $e->getMessage();
    }
}
?>