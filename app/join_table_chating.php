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

        .video-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            /* 2 列佈局 */
            grid-template-rows: repeat(3, 1fr);
            /* 3 行佈局 */
            gap: 5px;
            padding: 10px;
            height: calc(100vh - 200px);
            /* 視窗高度減去控制區域和頂部文字區域 */
            overflow-y: auto;
            /* 內容過多時可滾動 */
        }

        .video-item {
            width: 100%;
            height: 100%;
            aspect-ratio: 16/9;
            /* 維持視訊比例 */
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: relative;
            /* 為絕對定位的子元素設置定位上下文 */
            cursor: pointer;
            /* 滑鼠指針改為手形 */
        }

        .video-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* 確保圖片填滿容器 */
        }

        .video-options {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 8px;
            padding: 10px;
            display: none;
            /* 默認隱藏 */
            flex-direction: column;
            gap: 10px;
            z-index: 10;
            width: 80%;
        }

        .option-btn {
            background-color: #fff;
            color: #333;
            border: none;
            border-radius: 999px;
            padding: 8px 10px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.2s;
        }

        .option-btn:hover {
            background-color: #f0f0f0;
        }

        .option-btn.danger {
            background-color: #ff4d4f;
            color: white;
        }

        .option-btn.danger:hover {
            background-color: #e6393b;
        }

        /* 添加關閉相機圖示樣式 */
        .camera-off-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            aspect-ratio: 1/1;
            z-index: 5;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 25% 35%;
        }

        .blurred-video {
            filter: blur(20px);
            /* 高斯模糊效果 */
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
            padding: 2rem;
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

            // 生成10至20之間的隨機整數
            function generateRandomInteger(min, max) {
                return Math.floor(Math.random() * (max - min + 1)) + min;
            }

            // 使用生成器生成數字
            const randomInteger = generateRandomInteger(10, 20);
            // console.log('Generated Random Integer:', randomInteger);

            // 更新頁面上顯示的主題文字
            document.querySelector('.topic-text').textContent = 'Table Topic: ' + selectedTopic + ' (' + randomInteger + ' joiners)';
        });
    </script>

    <div class="container">

        <div class="topic-text text-center mt-3 mb-0">Table Topic: Healthy Diet</div>

        <div class="video-grid">
            <div class="video-item"><img src="https://media.giphy.com/media/l0MYt5jPR6QX5pnqM/giphy.gif" alt="Video 1"></div>
            <div class="video-item"><img src="https://media.giphy.com/media/3o7btPCcdNniyf0ArS/giphy.gif" alt="Video 2"></div>
            <div class="video-item"><img src="https://media.giphy.com/media/l0MYt5jPR6QX5pnqM/giphy.gif" alt="Video 3"></div>
            <div class="video-item"><img src="https://media.giphy.com/media/3o7btPCcdNniyf0ArS/giphy.gif" alt="Video 4"></div>
            <div class="video-item"><img src="https://media.giphy.com/media/l0MYt5jPR6QX5pnqM/giphy.gif" alt="Video 5"></div>
            <div class="video-item"><img src="https://media.giphy.com/media/3o7btPCcdNniyf0ArS/giphy.gif" alt="Video 6"></div>
        </div>

        <div class="controls">
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

        document.addEventListener('DOMContentLoaded', function() {
            // 從 sessionStorage 中獲取選擇的主題
            const selectedTopic = sessionStorage.getItem('aiChatTopic') || 'General Topic';

            // 更新主題文字
            document.querySelector('.topic-text').textContent = 'Table Topic: ' + selectedTopic;

            // 為每個視訊項目添加點擊事件
            const videoItems = document.querySelectorAll('.video-item');

            videoItems.forEach((item, index) => {
                // 設置狀態標記
                item.dataset.blurred = 'false';

                // 創建選項菜單
                const optionsMenu = document.createElement('div');
                optionsMenu.className = 'video-options';
                item.appendChild(optionsMenu);

                // 更新選項菜單內容
                updateOptionsMenu(item);

                // 為視訊項目添加點擊事件
                item.addEventListener('click', function(e) {
                    // 阻止事件冒泡
                    e.stopPropagation();

                    // 隱藏所有選項菜單
                    document.querySelectorAll('.video-options').forEach(menu => {
                        menu.style.display = 'none';
                    });

                    // 顯示當前項目的選項菜單
                    optionsMenu.style.display = 'flex';
                });
            });

            // 點擊頁面其他地方隱藏選項菜單
            document.addEventListener('click', function() {
                document.querySelectorAll('.video-options').forEach(menu => {
                    menu.style.display = 'none';
                });
            });

            // 更新選項菜單函數
            function updateOptionsMenu(item) {
                const optionsMenu = item.querySelector('.video-options');
                const isBlurred = item.dataset.blurred === 'true';

                if (isBlurred) {
                    optionsMenu.innerHTML = `
                <button class="option-btn" data-action="unblur">Open User Camera</button>
                <button class="option-btn danger" data-action="report">Report This User</button>
            `;
                } else {
                    optionsMenu.innerHTML = `
                <button class="option-btn" data-action="blur">Close User Camera</button>
                <button class="option-btn danger" data-action="report">Report This User</button>
            `;
                }

                // 為選項按鈕添加點擊事件
                optionsMenu.querySelectorAll('.option-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();

                        const action = this.getAttribute('data-action');

                        if (action === 'blur') {
                            // 關閉鏡頭：添加模糊效果和圖示
                            blurVideo(item, true);
                        } else if (action === 'unblur') {
                            // 開啟鏡頭：移除模糊效果和圖示
                            blurVideo(item, false);
                        } else if (action === 'report') {
                            // 檢舉功能暫無實現
                            alert('檢舉功能暫未實現');
                        }

                        // 隱藏選項菜單
                        optionsMenu.style.display = 'none';
                    });
                });
            }

            // 模糊/取消模糊視訊函數
            function blurVideo(item, shouldBlur) {
                const videoImg = item.querySelector('img');

                if (shouldBlur) {
                    // 添加模糊效果
                    videoImg.classList.add('blurred-video');

                    // 添加相機關閉圖示
                    const cameraOffIcon = document.createElement('img');
                    cameraOffIcon.src = 'assets/images/camera_off.png';
                    cameraOffIcon.className = 'camera-off-icon';
                    cameraOffIcon.alt = 'Camera Off';
                    item.appendChild(cameraOffIcon);

                    // 更新狀態
                    item.dataset.blurred = 'true';
                } else {
                    // 移除模糊效果
                    videoImg.classList.remove('blurred-video');

                    // 移除相機關閉圖示
                    const cameraOffIcon = item.querySelector('.camera-off-icon');
                    if (cameraOffIcon) {
                        cameraOffIcon.remove();
                    }

                    // 更新狀態
                    item.dataset.blurred = 'false';
                }

                // 更新選項菜單
                updateOptionsMenu(item);
            }
        });
    </script>
</body>

</html>