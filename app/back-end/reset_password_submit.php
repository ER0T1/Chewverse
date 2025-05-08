<?php
session_start();

// 檢查是否為 POST 請求
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../settings.php");
    exit;
}

// 獲取表單提交的資料
$email = trim($_SESSION['reset_email'] ?? '');
$newPassword = trim($_POST['new_password'] ?? '');
$confirmPassword = trim($_POST['confirm_password'] ?? '');

// 驗證電子郵件地址
if (!$email) {
    echo "<script>alert('Session expired or no email.'); window.location.href='../forgot_password.php';</script>";
    exit;
}

// 驗證新密碼和確認密碼
if ($newPassword !== $confirmPassword) {
    echo "<script>alert('Passwords do not match.'); window.location.href='../forgot_password.php';</script>";
    exit;
}

// 驗證密碼長度
if (strlen($newPassword) < 6 || strlen($newPassword) > 12) {
    echo "<script>alert('Password must be 6-12 characters.'); window.location.href='../forgot_password.php';</script>";
    exit;
}

// 設定資料檔案路徑
$dataFile = "../database/users.json";

// 檢查資料檔案是否存在
if (!file_exists($dataFile)) {
    echo "<script>alert('The user profile does not exist.'); window.location.href='../forgot_password.php';</script>";
    exit;
}

// 讀取現有資料
$jsonContent = file_get_contents($dataFile);
$usersData = json_decode($jsonContent, true);

if ($usersData === null) {
    echo "<script>alert('Unable to parse user profile.'); window.location.href='../forgot_password.php';</script>";
    exit;
}

// 尋找用戶並更新密碼
$userFound = false;
foreach ($usersData as $key => $user) {
    if (isset($user['email']) && $user['email'] === $email) {
        // 產生新的密碼 hash
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // 更新用戶密碼
        $usersData[$key]['password'] = $hashedPassword;
        $userFound = true;
        break;
    }
}

// 如果找不到用戶
if (!$userFound) {
    echo "<script>alert('User profile not found.'); window.location.href='../forgot_password.php';</script>";
    exit;
}

// 將更新後的用戶資料寫回檔案
if (file_put_contents($dataFile, json_encode($usersData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    // 清除重設密碼相關的 session 資料
    unset($_SESSION['reset_email'], $_SESSION['verification_code'], $_SESSION['verification_expiry']);
    
    // 密碼更新成功，返回登入頁面
    header("Location: ../login.php");
    exit;
} else {
    echo "<script>alert('Failed to update password. Please try again.'); window.location.href='../forgot_password.php';</script>";
    exit;
}
?>