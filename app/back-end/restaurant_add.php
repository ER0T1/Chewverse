<?php
session_start();

// 檢查用戶是否已登入
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php?error=5");
    exit;
}

// 檢查是否為 POST 請求
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../settings.php");
    exit;
}

// 獲取表單數據
$restaurantName = trim($_POST['restaurantName'] ?? '');
$restaurantAddress = trim($_POST['restaurantAddress'] ?? '');
$restaurantPhone = trim($_POST['restaurantPhone'] ?? '');
$restaurantDescription = trim($_POST['restaurantDescription'] ?? '');

// 驗證表單數據
if (empty($restaurantName) || empty($restaurantAddress) || empty($restaurantPhone)) {
    echo "<script>alert('Please fill in all required fields.'); window.location.href='../settings.php';</script>";
    exit;
}

// 設定資料檔案路徑
$usersDataFile = "../database/users.json";
$restaurantsDataFile = "../database/restaurants.json";

// 檢查資料檔案是否存在
if (!file_exists($usersDataFile)) {
    echo "<script>alert('The user profile does not exist.'); window.location.href='../settings.php';</script>";
    exit;
}
if (!file_exists($restaurantsDataFile)) {
    echo "<script>alert('The restaurant data file does not exist.'); window.location.href='../settings.php';</script>";
    exit;
}

// 讀取現有資料
$jsonContent = file_get_contents($usersDataFile);
$usersData = json_decode($jsonContent, true);
$jsonContent = file_get_contents($restaurantsDataFile);
$restaurantsData = json_decode($jsonContent, true);

if ($usersData === null) {
    echo "<script>alert('Unable to parse user profile.'); window.location.href='../settings.php';</script>";
    exit;
}
if ($restaurantsData === null) {
    echo "<script>alert('Unable to parse restaurant data.'); window.location.href='../settings.php';</script>";
    exit;
}

// 創建新餐廳資料
$newRestaurant = [
    'id' => uniqid('rest_'),  // 產生唯一ID
    'name' => $restaurantName,
    'address' => $restaurantAddress,
    'phone' => $restaurantPhone,
    'description' => $restaurantDescription,
    'owner_email' => $_SESSION['user_email'],
    'created_at' => date('Y-m-d H:i:s')
];

// 尋找並更新當前用戶的餐廳資料
$userFound = false;
foreach ($usersData as $key => $user) {
    if (isset($user['email']) && $user['email'] === $_SESSION['user_email']) {
        // 如果用戶沒有 restaurants 陣列，創建一個
        if (!isset($usersData[$key]['restaurants']) || !is_array($usersData[$key]['restaurants'])) {
            $usersData[$key]['restaurants'] = [];
        }
        
        // 添加新餐廳到用戶的 restaurants 陣列
        $usersData[$key]['restaurants'][] = $newRestaurant['id'];
        // 將新餐廳添加到全局餐廳資料
        $restaurantsData[] = $newRestaurant;
        $userFound = true;
        break;
    }
}

// 如果找不到用戶
if (!$userFound) {
    echo "<script>alert('User profile not found.'); window.location.href='../settings.php';</script>";
    exit;
}

// 將更新後的用戶資料與寫回檔案
if (!file_put_contents($usersDataFile, json_encode($usersData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    // 餐廳添加失敗
    echo "<script>alert('Unable to save user data, please check file permissions.'); window.location.href='../settings.php';</script>";
    exit;
}

// 將更新後的餐廳資料寫回檔案
if (!file_put_contents($restaurantsDataFile, json_encode($restaurantsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    // 餐廳添加失敗
    echo "<script>alert('Unable to save restaurant data, please check file permissions.'); window.location.href='../settings.php';</script>";
    exit;
}

// 如果所有操作都成功，重定向到設定頁面
header("Location: ../settings.php");
exit;
?>