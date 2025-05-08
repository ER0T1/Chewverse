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

// 獲取表單數據
$restaurantId = $_POST['restaurantId'] ?? '';
$restaurantIndex = $_POST['restaurantIndex'] ?? '';
$restaurantName = trim($_POST['modifyRestaurantName'] ?? '');
$restaurantAddress = trim($_POST['modifyRestaurantAddress'] ?? '');
$restaurantPhone = trim($_POST['modifyRestaurantPhone'] ?? '');
$restaurantDescription = trim($_POST['modifyRestaurantDescription'] ?? '');

// 驗證表單數據
if (empty($restaurantId) || empty($restaurantName) || empty($restaurantAddress) || 
    empty($restaurantPhone)) {
    echo "<script>alert('Please fill in all required fields.'); window.location.href='profile.php';</script>";
    exit;
}

// 設定資料檔案路徑
$dataFile = "../database/restaurants.json";

// 檢查資料檔案是否存在
if (!file_exists($dataFile)) {
    echo "<script>alert('The restaurants data does not exist.'); window.location.href='../settings.php';</script>";
    exit;
}

// 讀取資料
$jsonContent = file_get_contents($dataFile);
$restsData = json_decode($jsonContent, true);

if ($restsData === null) {
    echo "<script>alert('Unable to parse restaurants data.'); window.location.href='../settings.php';</script>";
    exit;
}

// 檢查餐廳資料是否存在
$restaurantFound = false;
foreach ($restsData as $key => $restaurant) {
    if (isset($restaurant['id']) && $restaurant['id'] === $restaurantId) {
        $restaurantFound = true;

        // 更新餐廳資料
        $restsData[$key]['name'] = $restaurantName;
        $restsData[$key]['address'] = $restaurantAddress;
        $restsData[$key]['phone'] = $restaurantPhone;
        $restsData[$key]['description'] = $restaurantDescription;
        $restsData[$key]['updated_at'] = date('Y-m-d H:i:s');
        
        // 儲存資料
        if (file_put_contents($dataFile, json_encode($restsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
            header("Location: ../settings.php");
            exit;
        } else {
            echo "<script>alert('Unable to save restaurant data, please check file permissions.'); window.location.href='../settings.php';</script>";
            exit;
        }
    }
}

// 如果找不到餐廳資料
if (!$restaurantFound) {
    echo "<script>alert('Restaurant not found.'); window.location.href='../settings.php';</script>";
    exit;
}
?>