<?php
session_start();

// 檢查用戶是否已登入
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.php?error=5");
    exit;
}

// 獲取 POST 請求中的 JSON 資料
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!isset($data['restaurantId']) || empty($data['restaurantId'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
    exit;
}

$restaurantId = $data['restaurantId'];
$userEmail = $_SESSION['user_email'];
$usersDataFile = "../database/users.json";
$restsDataFile = "../database/restaurants.json";

// 讀取 users.json 和 restaurants.json 資料
if (file_exists($usersDataFile) && file_exists($restsDataFile)) {
    // 讀取 users.json 資料
    $jsonContent = file_get_contents($usersDataFile);
    $usersData = json_decode($jsonContent, true);

    // 讀取 restaurants.json 資料
    $jsonContent = file_get_contents($restsDataFile);
    $restsData = json_decode($jsonContent, true);

    $userFound = false;
    $restaurantFound = false;
    
    // 從使用者資料中刪除餐廳 ID
    foreach ($usersData as $userKey => $user) {
        if (isset($user['email']) && $user['email'] === $userEmail) {
            $userFound = true;
            if (isset($user['restaurants']) && is_array($user['restaurants'])) {
                foreach ($user['restaurants'] as $restIndex => $restId) {
                    if ($restId == $restaurantId) {
                        $restaurantFound = true;
                        // 刪除使用者的餐廳列表中的餐廳 ID
                        array_splice($usersData[$userKey]['restaurants'], $restIndex, 1);
                        
                        // 更新 users.json 檔案
                        file_put_contents($usersDataFile, json_encode($usersData, JSON_PRETTY_PRINT));
                        break;
                    }
                }
            }
            break;
        }
    }

    // 從餐廳資料中刪除餐廳
    if ($restaurantFound) {
        foreach ($restsData as $restKey => $restaurant) {
            if (isset($restaurant['id']) && $restaurant['id'] === $restaurantId) {
                // 刪除餐廳資料
                array_splice($restsData, $restKey, 1);
                
                // 更新 restaurants.json 檔案
                file_put_contents($restsDataFile, json_encode($restsData, JSON_PRETTY_PRINT));
                break;
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Restaurant successfully deleted.']);
        exit;
    }
    
    if (!$userFound) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'User profile not found.']);
        exit;
    }
    
    if (!$restaurantFound) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'The specified restaurant could not be found.']);
        exit;
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'The data file does not exist.']);
    exit;
}
?>