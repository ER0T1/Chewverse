<?php
session_start();

// 檢查用戶是否已登入
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.php?error=5");
    exit;
}

// 檢查是否為 POST 請求
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "<script>window.location.href='../settings.php';</script>";
    exit;
}

// 獲取表單數據
$newPassword = $_POST['newPassword'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';

// 驗證密碼
if (empty($newPassword) || empty($confirmPassword)) {
    echo "<script>alert('Please fill in all fields.'); window.location.href='../settings.php';</script>";
    exit;
}

// 檢查密碼長度
if (strlen($newPassword) < 6 || strlen($newPassword) > 12) {
    echo "<script>alert('Password length must be between 6 and 12 characters.'); window.location.href='../settings.php';</script>";
    exit;
}

// 檢查密碼一致性
if ($newPassword !== $confirmPassword) {
    echo "<script>alert('The new password and the confirmed password do not match.'); window.location.href='../settings.php';</script>";
    exit;
}

// 設定資料檔案路徑
$dataFile = "../database/users.json";

// 檢查資料檔案是否存在
if (!file_exists($dataFile)) {
    echo "<script>alert('The user profile does not exist.'); window.location.href='../settings.php';</script>";
    exit;
}

// 讀取 data.json 資料
$jsonContent = file_get_contents($dataFile);
$usersData = json_decode($jsonContent, true);

if ($usersData === null) {
    echo "<script>alert('Unable to parse user profile.'); window.location.href='../settings.php';</script>";
    exit;
}

// 尋找並更新當前用戶密碼
$userFound = false;
foreach ($usersData as $key => $user) {
    if (isset($user['email']) && $user['email'] === $_SESSION['user_email']) {
        // 使用 password_hash 加密新密碼
        $usersData[$key]['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
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
    // 密碼更新成功
    header("Location: ../settings.php");
    exit;
} else {
    echo "<script>alert('Unable to save user data, please check file permissions.'); window.location.href='../settings.php';</script>";
    exit;
}
?>