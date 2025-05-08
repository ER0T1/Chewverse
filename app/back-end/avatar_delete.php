<?php
session_start();

// 檢查用戶是否已登入
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.php?error=5");
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

// 尋找當前用戶資料
$userFound = false;
$avatarPath = '';

foreach ($usersData as $key => $user) {
    if (isset($user['email']) && $user['email'] === $_SESSION['user_email']) {
        $userFound = true;
        
        // 儲存當前頭像路徑，以便之後刪除檔案
        if (isset($user['avatar']) && !empty($user['avatar'])) {
            $avatarPath = "../" . $user['avatar'];
            
            // 清除用戶頭像資料
            $usersData[$key]['avatar'] = '';
        }
        
        break;
    }
}

// 如果找不到用戶
if (!$userFound) {
    echo "<script>alert('User profile not found.'); window.location.href='../settings.php';</script>";
    exit;
}

// 如果頭像路徑不為空且文件存在，則刪除檔案
if (!empty($avatarPath) && file_exists($avatarPath)) {
    if (!unlink($avatarPath)) {
        echo "<script>alert('Unable to delete avatar file, but avatar data has been cleared.'); window.location.href='../settings.php';</script>";
        exit;
    }
}

// 更新 SESSION 中的頭像資料
$_SESSION['user_avatar'] = '';

// 將更新後的用戶資料寫回檔案
if (file_put_contents($dataFile, json_encode($usersData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    header("Location: ../settings.php");
    exit;
} else {
    echo "<script>alert('Unable to save user data, please check file permissions.'); window.location.href='../settings.php';</script>";
    exit;
}
?>