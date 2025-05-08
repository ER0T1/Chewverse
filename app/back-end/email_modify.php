<?php
session_start();

// 檢查用戶是否已登入
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.php?error=5");
    exit;
}

// 檢查是否為 POST 請求
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../settings.php");
    exit;
}

// 獲取表單提交的資料
$new_email = trim($_POST['email'] ?? '');
$old_email = $_SESSION['user_email'];

// 驗證電子郵件
if (empty($new_email)) {
    echo "<script>alert('Email cannot be empty.'); window.location.href='../settings.php';</script>";
    exit;
}

if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Please enter a valid email address.'); window.location.href='../settings.php';</script>";
    exit;
}

// 如果新舊電子郵件相同，無需更新
if ($new_email === $old_email) {
    header("Location: ../settings.php");
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

// 檢查新電子郵件是否已被其他用戶使用
foreach ($usersData as $user) {
    if (isset($user['email']) && $user['email'] === $new_email) {
        echo "<script>alert('This email is already in use, please use a different one.'); window.location.href='../settings.php';</script>";
        exit;
    }
}

// 尋找並更新當前用戶的電子郵件
$userFound = false;
foreach ($usersData as $key => $user) {
    if (isset($user['email']) && $user['email'] === $old_email) {
        // 更新用戶電子郵件
        $usersData[$key]['email'] = $new_email;
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
    // 更新 session 中的電子郵件
    $_SESSION['user_email'] = $new_email;
    
    // 資料更新成功，返回個人資料頁面
    header("Location: ../settings.php");
    exit;
} else {
    echo "<script>alert('Unable to save user data, please check file permissions.'); window.location.href='../settings.php';</script>";
    exit;
}
?>