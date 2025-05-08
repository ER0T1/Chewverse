<?php
session_start();

// 檢查用戶是否已登入
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.php?error=5");
    exit;
}

// 檢查是否有文件上傳
if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] != 0) {
    echo "<script>alert('File upload failed, please try again.'); window.location.href='../settings.php';</script>";
    exit;
}

// 驗證文件類型
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
$file_type = $_FILES['avatar']['type'];

if (!in_array($file_type, $allowed_types)) {
    echo "<script>alert('Only JPG, PNG or GIF image formats are supported.'); window.location.href='../settings.php';</script>";
    exit;
}

// 設定上傳目錄
$upload_dir = "uploads/images/";
if (!is_dir("../" . $upload_dir)) {
    mkdir("../" . $upload_dir, 0755, true);
}

// 生成唯一文件名
$filename = uniqid() . "_" . basename($_FILES["avatar"]["name"]);
$target_path = $upload_dir . $filename;

// 移動上傳的文件
if (!move_uploaded_file($_FILES["avatar"]["tmp_name"], "../" . $target_path)) {
    echo "<script>alert('File upload failed, please check directory permissions.'); window.location.href='../settings.php';</script>";
    exit;
}

// 設定資料檔案路徑
$dataFile = "../database/users.json";

// 檢查資料檔案是否存在
if (!file_exists($dataFile)) {
    echo "<script>alert('The user profile does not exist.'); window.location.href='../settings.php';</script>";
    exit;
}

// 讀取現有資料
$jsonContent = file_get_contents($dataFile);
$usersData = json_decode($jsonContent, true);

if ($usersData === null) {
    echo "<script>alert('Unable to parse user profile.'); window.location.href='../settings.php';</script>";
    exit;
}

// 尋找並更新當前用戶資料
$userFound = false;
foreach ($usersData as $key => $user) {
    if (isset($user['email']) && $user['email'] === $_SESSION['user_email']) {
        // 刪除舊頭像文件 (如果存在)
        if (!empty($user['avatar']) && file_exists("../" . $user['avatar']) && $user['avatar'] != $target_path) {
            @unlink("../" . $user['avatar']);
        }
        
        // 更新用戶頭像路徑
        $usersData[$key]['avatar'] = $target_path;
        $userFound = true;
        break;
    }
}

// 如果找不到用戶
if (!$userFound) {
    echo "<script>alert('User profile not found.'); window.location.href='../settings.php';</script>";
    exit;
}

// 將更新後的用戶資料寫回檔案
if (file_put_contents($dataFile, json_encode($usersData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    // 同時更新 session 中的頭像路徑（如果有的話）
    $_SESSION['user_avatar'] = $target_path;
    
    // 上傳成功，返回個人資料頁面
    header("Location: ../settings.php");
    exit;
} else {
    echo "<script>alert('Unable to save user data, please check file permissions.'); window.location.href='../settings.php';</script>";
    exit;
}
?>