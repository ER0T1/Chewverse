<?php
session_start();

// 檢查用戶是否已登入
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php?error=5");
    exit;
}

// 設定資料檔案路徑
$dataFile = "database/users.json";
$userData = null;

// 讀取 data.json 資料
if (file_exists($dataFile)) {
    $jsonContent = file_get_contents($dataFile);
    $usersData = json_decode($jsonContent, true);

    // 檢查 JSON 解析是否成功
    if ($usersData === null && json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON 解析錯誤: " . json_last_error_msg());
    } else {
        // 確保 $usersData 是陣列
        if (is_array($usersData)) {
            // 尋找當前用戶資料
            foreach ($usersData as $user) {
                if (isset($user['email']) && $user['email'] === $_SESSION['user_email']) {
                    $userData = $user;
                    break;
                }
            }
        } else {
            error_log("users.json 格式錯誤: 預期為陣列，實際為 " . gettype($usersData));
        }
    }
} else {
    // 如果找不到 data.json 檔案，嘗試使用 session 中的資料
    $userData = [
        'nickname' => $_SESSION['user_nickname'] ?? 'User',
        'email' => $_SESSION['user_email'],
        'avatar' => $_SESSION['user_avatar'] ?? ''
    ];
}

// 設定用戶資料變數
$nickname = $userData['nickname'] ?? 'User';
$email = $userData['email'] ?? $_SESSION['user_email'];
$avatar = $userData['avatar'] ?? '';

// 除錯用：檢查讀取到的資料
// echo "<pre>"; print_r($usersData); echo "</pre>";
// echo "<pre>"; print_r($userData); echo "</pre>";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Chewverse - Join Shared Table</title>
    <!-- CSS -->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .container {
            max-width: 480px;
            padding: 2rem;
        }

        .avatar {
            border: #333 solid 2px;
            border-radius: 999px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row d-flex justify-content-flex-start align-items-center mb-4">
            <a class="col-1 btn-left-arrow" href="javascript:history.back()">
                <img src="assets/images/left_arrow.png" alt="Left Arrow">
            </a>
            <h2 class="col text-left fw-bold mb-0">Join Shared Table</h2>
        </div>

        <!-- Join Shared Table content goes here -->
        <div class="content">
            <?php include 'element/choose_a_topic.php'; ?>
        </div>
    </div>

    <!-- JS -->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <!-- 在 body 結束標籤之前添加此腳本 -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 監聽 topicSelected 事件
            document.addEventListener('topicSelected', function(event) {
                // 取得選中的主題
                const selectedTopic = event.detail.topic;

                // 儲存主題到 sessionStorage
                sessionStorage.setItem('aiChatTopic', selectedTopic);
                
                // 實際執行 AI 聊天功能
                window.location.href = 'join_table_chating.php';
            });
        });
    </script>
</body>

</html>