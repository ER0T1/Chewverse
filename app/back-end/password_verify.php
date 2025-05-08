<?php
session_start();
header('Content-Type: application/json');

// 檢查用戶是否已登入
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.php?error=5");
    exit;
}

// 獲取 POST 資料 (JSON 格式)
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// 檢查是否有 oldPassword 欄位
if (!isset($data['oldPassword']) || empty($data['oldPassword'])) {
    echo json_encode(['success' => false, 'message' => 'Please enter your old password.']);
    exit;
}

$oldPassword = $data['oldPassword'];

// 設定資料檔案路徑
$dataFile = "../database/users.json";

// 檢查資料檔案是否存在
if (!file_exists($dataFile)) {
    echo json_encode(['success' => false, 'message' => 'The user profile does not exist.']);
    exit;
}

// 讀取 data.json 資料
$jsonContent = file_get_contents($dataFile);
$usersData = json_decode($jsonContent, true);

if ($usersData === null) {
    echo json_encode(['success' => false, 'message' => 'Unable to parse user profile.']);
    exit;
}

// 尋找當前用戶資料
$userFound = false;
$passwordCorrect = false;

foreach ($usersData as $user) {
    if (isset($user['email']) && $user['email'] === $_SESSION['user_email']) {
        $userFound = true;
        
        // 驗證密碼
        if (isset($user['password']) && password_verify($oldPassword, $user['password'])) {
            $passwordCorrect = true;
        }
        
        break;
    }
}

// 如果找不到用戶
if (!$userFound) {
    echo json_encode(['success' => false, 'message' => 'User profile not found.']);
    exit;
}

// 如果密碼不正確
if (!$passwordCorrect) {
    echo json_encode(['success' => false, 'message' => 'The old password is incorrect.']);
    exit;
}

// 密碼驗證成功
echo json_encode(['success' => true, 'message' => 'Password verification successful.']);
?>