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
    <title>Chewverse - Chat With AI</title>
    <!-- CSS -->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .container {
            max-width: 480px;
            padding: 0;
            padding-bottom: 200px;
            /* 為固定在底部的控制區域留出空間 */
            height: 100vh;
            /* 讓容器填滿整個視窗高度 */
            position: relative;
            /* 使其成為定位上下文 */
            overflow: hidden;
            /* 防止內容溢出 */
        }

        .avatar {
            border: #333 solid 2px;
            border-radius: 999px;
            text-align: center;
        }

        .video-container {
            position: relative;
            width: 100%;
            height: calc(100vh - 150px);
            /* 視窗高度減去控制區域高度 */
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #000;
            overflow: hidden;
            /* 確保內容溢出時被裁切 */
        }

        .ai-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* 保持比例填滿容器 */
            position: absolute;
            /* 絕對定位以填滿容器 */
            top: 0;
            left: 0;
        }

        .self-video {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 120px;
            height: 120px;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid #fff;
        }

        .self-video img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .controls {
            position: fixed;
            /* 固定定位 */
            bottom: 0;
            /* 固定在底部 */
            left: 0;
            /* 從左邊界開始 */
            right: 0;
            /* 延伸到右邊界 */
            background-color: #ddd;
            padding: 1rem;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            text-align: center;
            z-index: 1000;
            /* 確保顯示在其他元素上方 */
            max-width: 480px;
            /* 與容器寬度一致 */
            margin: 0 auto;
            /* 水平居中 */
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }

        .topic-text {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .control-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            padding: 2.5rem;
        }

        .control-btn {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: none;
            background-color: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #6c757d;
            transition: background-color 0.3s;
        }

        .control-btn:hover {
            background-color: #d1d4d7;
        }

        .end-btn {
            background-color: #ff4d4f;
            color: white;
        }

        .end-btn:hover {
            background-color: #e6393b;
        }

        /* 模態框樣式調整 */
        .modal-content {
            border-radius: 1rem;
            padding: 1rem;
            background-color: #FBF5EC;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.85rem;
            text-align: center;
            margin-top: 0.5rem;
            display: none;
        }
    </style>
</head>

<body>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 從 sessionStorage 中獲取選擇的主題
            const selectedTopic = sessionStorage.getItem('aiChatTopic') || 'General Topic';

            // 更新頁面上顯示的主題文字
            document.querySelector('.topic-text').textContent = 'Table Topic: ' + selectedTopic;
        });
    </script>

    <div class="container">
        <div class="video-container">
            <!-- AI 視訊畫面（使用GIF代替） -->
            <img src="https://media.giphy.com/media/3o7btPCcdNniyf0ArS/giphy.gif" class="ai-video" alt="AI Video">

            <!-- 自己的視訊畫面（使用GIF代替） -->
            <div class="self-video">
                <img src="https://media.giphy.com/media/l0MYt5jPR6QX5pnqM/giphy.gif" alt="Self Video">
            </div>
        </div>

        <div class="controls">
            <div class="topic-text">Table Topic: Healthy Diet</div>
            <div class="control-buttons">
                <button class="control-btn" title="Camera"><img src="assets/images/camera.png" alt="Camera"></button>
                <button class="control-btn" title="Microphone"><img src="assets/images/microphone.png" alt="Microphone"></button>
                <button class="control-btn" title="Switch Camera"><img src="assets/images/switch_camera.png" alt="Switch Camera"></button>
                <div class="confirmLeaveContainer">
                    <button class="control-btn end-btn" title="End Chat"><img src="assets/images/close.png" alt="End Chat"></button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'element/end_chat.php'; ?>

    <!-- JS -->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        // 模擬按鈕功能（實際應用中需添加視訊控制邏輯）
        document.querySelector('.control-btn[title="Camera"]').addEventListener('click', function() {
            alert('Camera toggle clicked (functionality not implemented).');
        });

        document.querySelector('.control-btn[title="Microphone"]').addEventListener('click', function() {
            alert('Microphone toggle clicked (functionality not implemented).');
        });

        document.querySelector('.control-btn[title="Switch Camera"]').addEventListener('click', function() {
            alert('Switch Camera toggle clicked (functionality not implemented).');
        });
    </script>
</body>

</html>