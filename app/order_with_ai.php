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
    <title>Chewverse - Order Chat</title>
    <!-- CSS -->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            max-width: 480px;
            padding: 2rem;
        }

        .mask {
            background-color: #FBF5EC;
            width: 100%;
            height: 80px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header {
            background-color: #FBF5EC;
            width: 100%;
            max-width: 400px;
            height: 74px;
            position: fixed;
            top: 80px;
            padding: 0 1rem 1.5rem;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-container {
            flex: 1;
            padding: 1rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 100px;
        }

        .message {
            max-width: 70%;
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            position: relative;
        }

        .message.ai {
            background-color: #fff;
            align-self: flex-start;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            left: 40px;
        }

        .message.user {
            background-color: #ff7043;
            color: white;
            align-self: flex-end;
            right: 40px;
        }

        .message-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            overflow: hidden;
            position: absolute;
            top: -10px;
        }

        .message.ai .message-avatar {
            left: -40px;
        }

        .message.user .message-avatar {
            right: -40px;
        }

        .message-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .input-container {
            background-color: #FBF5EC;
            position: sticky;
            bottom: 0;
            padding: 2.5rem 1rem;
            display: flex;
            align-items: center;
            border-top: 1px solid #eee;
        }

        .input-field {
            flex-grow: 1;
            border: none;
            outline: none;
            padding: 0.5rem;
            border-radius: 1rem;
            background-color: #f8f9fa;
            margin: 0 0.5rem;
        }

        .input-icons .bi {
            font-size: 1.5rem;
            color: #6c757d;
            cursor: pointer;
            margin: 0 0.25rem;
        }

        .input-icons .bi-send {
            color: #ff7043;
        }
    </style>
</head>

<body>
    <div class="mask"></div>
    <div class="container my-5">
        <div class="header">
            <div class="row d-flex justify-content-center align-items-center">
                <a class="col-1 btn-left-arrow" href="dashboard.php">
                    <img src="assets/images/left_arrow.png" alt="Left Arrow">
                </a>
                <h2 class="col text-left fw-bold mb-0">Order With AI</h2>
            </div>
            <div style="display: flex; align-items: center; gap: 5px;">
                <a href="cart.php" class="text-decoration-none">
                    <img src="assets/images/cart.png" alt="Cart" class="cart-icon"></img>
                </a>
            </div>
        </div>

        <div class="chat-container">
            <div class="message user">
                <div class="message-avatar">
                    <img src="<?php echo htmlspecialchars($avatar); ?>" alt="User Avatar" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                </div>
                <p>我想吃一個吃了會有幸福感的餐點。</p>
            </div>
            <div class="message ai">
                <div class="message-avatar">
                    <img src="assets/images/ai_avatar.png" alt="AI Avatar" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                </div>
                <p>了解！推薦您：「Capricciosa」，配料豐富多樣，很適合追求幸福感的您。還有其他需求嗎？</p>
            </div>
            <div class="message user">
                <div class="message-avatar">
                    <img src="<?php echo htmlspecialchars($avatar); ?>" alt="User Avatar" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                </div>
                <p>好，幫我加入購物車！還有其他推薦嗎？</p>
            </div>
            <div class="message ai">
                <div class="message-avatar">
                    <img src="assets/images/ai_avatar.png" alt="AI Avatar" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                </div>
                <p>已經幫您加入購物車！還有什麼想吃的嗎？</p>
            </div>
        </div>

        <div class="input-container">
            <div class="input-icons">
                <i class="bi bi-paperclip"></i>
                <i class="bi bi-emoji-smile"></i>
            </div>
            <input type="text" class="input-field" placeholder="Type a new message here">
            <i class="bi bi-send"></i>
        </div>
    </div>



    <!-- JS -->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // 發送消息功能
        const inputField = document.querySelector('.input-field');
        const sendButton = document.querySelector('.bi-send');
        const chatContainer = document.querySelector('.chat-container');

        sendButton.addEventListener('click', function() {
            const messageText = inputField.value.trim();
            if (messageText) {
                const message = document.createElement('div');
                message.classList.add('message', 'user');
                message.innerHTML = `
                    <div class="message-avatar">
                        <img src="<?php echo htmlspecialchars($avatar); ?>" alt="User Avatar" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                    </div>
                    <p>${messageText}</p>
                `;
                chatContainer.appendChild(message);
                inputField.value = '';

                // 模擬AI回應（延遲1秒）
                setTimeout(() => {
                    const aiResponse = document.createElement('div');
                    aiResponse.classList.add('message', 'ai');
                    aiResponse.innerHTML = `
                        <div class="message-avatar">
                            <img src="assets/images/ai_avatar.png" alt="AI Avatar" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                        </div>
                        <p>感謝您的消息！請問還有什麼我可以幫您的？</p>
                    `;
                    chatContainer.appendChild(aiResponse);
                    chatContainer.scrollTop = chatContainer.scrollHeight; // 自動滾動到底部
                }, 1000);

                chatContainer.scrollTop = chatContainer.scrollHeight; // 自動滾動到底部
            }
        });

        // 按Enter鍵發送
        inputField.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendButton.click();
            }
        });
    </script>
</body>

</html>