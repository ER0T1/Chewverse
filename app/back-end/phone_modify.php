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
$phone = trim($_POST['phone'] ?? '');

// 驗證電話號碼不能為空
if (empty($phone)) {
    echo "<script>alert('Phone number cannot be empty.'); window.location.href='../settings.php';</script>";
    exit;
}

// 驗證電話號碼格式
if (!preg_match('/^\d{10}$/', $phone)) {
    echo "<script>alert('Invalid phone number format.'); window.location.href='../settings.php';</script>";
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
        // 更新用戶電話號碼
        $usersData[$key]['phone'] = $phone;
        $userFound = true;
        break;
    }
}

// 如果用戶不存在，顯示錯誤訊息
if (!$userFound) {
    echo "<script>alert('User not found.'); window.location.href='../settings.php';</script>";
    exit;
}

// 將更新後的用戶資料寫回檔案
if (file_put_contents($dataFile, json_encode($usersData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    // 資料更新成功，返回個人資料頁面
    header("Location: ../settings.php");
    exit;
} else {
    // 資料更新失敗，顯示錯誤訊息
    echo "<script>alert('Unable to save user data, please check file permissions.'); window.location.href='../settings.php';</script>";
    exit;
}