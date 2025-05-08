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

// 讀取 users.json 資料
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
    // 如果找不到 users.json 檔案，嘗試使用 session 中的資料
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
    <title>Chewverse - Dashboard</title>
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

        .btn-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            /* 2列 */
            gap: 1rem;
            /* 按鈕間距 */
        }

        .dashboard-btn {
            position: relative;
            padding: 2rem;
            border-radius: 1rem;
            background: transparent;
            text-align: center;
            font-weight: 600;
            color: #333;
            overflow: hidden;
            transition: background-color 0.3s;
        }

        .dashboard-btn p {
            margin: 0;
            font-size: 1.2rem;
        }

        .dashboard-btn:hover {
            border-color: rgba(236, 131, 136, .7);
        }

        .dashboard-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 1rem;
            background-size: cover;
            z-index: -1;
        }

        .btn-chat::before {
            background: rgba(236, 131, 136, .1);
            border: 2px solid rgba(236, 131, 136, .7);
        }

        .btn-chat:hover {
            background: rgba(236, 131, 136, .5);
        }

        .btn-chat:active {
            background: rgba(236, 131, 136, .4);
        }

        .btn-table::before {
            background: rgb(255, 164, 65, .1);
            border: 2px solid rgba(255, 164, 65, .7);
        }

        .btn-table:hover {
            background: rgba(255, 164, 65, .5);
        }

        .btn-table:active {
            background: rgba(255, 164, 65, .4);
        }

        .btn-join::before {
            background: rgb(255, 206, 0, .1);
            border: 2px solid rgba(255, 206, 0, .7);
        }

        .btn-join:hover {
            background: rgba(255, 206, 0, .5);
        }

        .btn-join:active {
            background: rgba(255, 206, 0, .4);
        }

        .btn-order::before {
            background: rgb(113, 203, 134, .1);
            border: 2px solid rgba(113, 203, 134, .7);
        }

        .btn-order:hover {
            background: rgba(113, 203, 134, .5);
        }

        .btn-order:active {
            background: rgba(113, 203, 134, .4);
        }

        .btn-order-restaurant::before {
            background: rgb(131, 161, 236, .1);
            border: 2px solid rgba(131, 161, 236, .7);
        }

        .btn-order-restaurant:hover {
            background: rgba(131, 161, 236, .5);
        }

        .btn-order-restaurant:active {
            background: rgba(131, 161, 236, .4);
        }

        .btn-management::before {
            background: rgb(219, 131, 236, .1);
            border: 2px solid rgba(219, 131, 236, .7);
        }

        .btn-management:hover {
            background: rgba(219, 131, 236, .5);
        }

        .btn-management:active {
            background: rgba(219, 131, 236, .4);
        }
    </style>
</head>

<body>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="text-left fw-bold">Hello, welcome to <br /><span class="text-gradient">Chewverse</span>!</h1>
            <a href="profile.php" class="text-decoration-none">
                <?php if (!empty($avatar) && file_exists($avatar)): ?>
                    <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Profile" width="50" height="50" class="rounded-circle avatar">
                <?php else: ?>
                    <img src="assets/images/avatar-1.png" alt="Profile" width="50" height="50">
                <?php endif; ?>
            </a>
        </div>

        <div class="btn-grid mt-3">
            <!-- 按鈕1 -->
            <a href="chat_with_ai.php" class="text-decoration-none">
                <div class="dashboard-btn btn-chat">
                    <img src="assets/images/dashboard-1.png" alt="Chat With AI" class="mb-3">
                    <p>Chat With AI</p>
                </div>
            </a>
            <!-- 按鈕2 -->
            <a href="table_for_two.php" class="text-decoration-none">
                <div class="dashboard-btn btn-table">
                    <img src="assets/images/dashboard-2.png" alt="Table For Two" class="mb-3">
                    <p>Table For Two</p>
                </div>
            </a>
            <!-- 按鈕3 -->
            <a href="join_table.php" class="text-decoration-none">
                <div class="dashboard-btn btn-join">
                    <img src="assets/images/dashboard-3.png" alt="Join Table" class="mb-3">
                    <p>Join Table</p>
                </div>
            </a>
            <!-- 按鈕4 -->
            <a href="order.php" class="text-decoration-none">
                <div class="dashboard-btn btn-order">
                    <img src="assets/images/dashboard-4.png" alt="Order" class="mb-3">
                    <p>Order</p>
                </div>
            </a>
            <!-- 按鈕5 -->
            <a href="scan_qr_code.php" class="text-decoration-none">
                <div class="dashboard-btn btn-order-restaurant">
                    <img src="assets/images/dashboard-5.png" alt="Order In Restaurant" class="mb-3">
                    <p>Order In Rest</p>
                </div>
            </a>
            <!-- 按鈕6 -->
            <a href="management.php" class="text-decoration-none">
                <div class="dashboard-btn btn-management">
                    <img src="assets/images/dashboard-6.png" alt="Management" class="mb-3">
                    <p>Management</p>
                </div>
            </a>
        </div>
    </div>

    <!-- JS -->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>