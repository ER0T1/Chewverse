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
    <title>Chewverse - Profile</title>
    <!-- CSS -->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
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

        .avatar {
            border: #333 solid 2px;
            border-radius: 999px;
            text-align: center;
        }

        .album-section {
            margin-top: 20px;
            padding-top: 15px;
        }

        .photo-card {
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .photo-card:hover {
            transform: translateY(-5px);
        }

        .photo-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border: 1px solid #eee;
        }

        .photo-info {
            padding: 5px 0;
        }
    </style>
</head>

<body>
    <div class="mask"></div>
    <div class="container my-5">
        <div class="header">
            <div class="row d-flex justify-content-flex-start align-items-center mb-4">
                <a class="col-1 btn-left-arrow" href="dashboard.php">
                    <img src="assets/images/left_arrow.png" alt="Left Arrow">
                </a>
                <h2 class="col text-left fw-bold mb-0">Profile</h2>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col text-center">
                <a href="settings.php">
                    <img src="<?php echo htmlspecialchars($avatar); ?>" alt="<?php echo htmlspecialchars($nickname); ?>" class="avatar" width="100" height="100" style="margin-bottom: 10px;">
                </a>
                <h3><?php echo htmlspecialchars($nickname); ?></h3>
                <p><?php echo htmlspecialchars($email); ?></p>
            </div>
        </div>

        <div class="album-section mt-4">
            <h4 class="mb-3">My Photo Album</h4>

            <div class="row g-3">
                <?php
                // 這裡可以從資料庫或其他來源讀取相片資料
                // 以下是示範用的假資料
                $photos = [
                    ['id' => 1, 'title' => 'Food photos', 'image' => 'assets/images/sample1.jpg', 'date' => '2025-04-02'],
                    ['id' => 2, 'title' => 'Landscape photos', 'image' => 'assets/images/sample2.jpg', 'date' => '2025-04-15'],
                    ['id' => 3, 'title' => 'Friends gathering', 'image' => 'assets/images/sample3.jpg', 'date' => '2025-04-28'],
                    ['id' => 4, 'title' => 'travel', 'image' => 'assets/images/sample4.jpg', 'date' => '2025-05-01'],
                    ['id' => 5, 'title' => 'pet', 'image' => 'assets/images/sample5.jpg', 'date' => '2025-05-06'],
                    ['id' => 6, 'title' => 'Family gathering', 'image' => 'assets/images/sample6.jpg', 'date' => '2025-05-07'],
                ];

                // 顯示相片網格
                foreach ($photos as $photo): ?>
                    <div class="col-6 col-md-4">
                        <div class="photo-card">
                            <img src="<?php echo htmlspecialchars($photo['image']); ?>" alt="<?php echo htmlspecialchars($photo['title']); ?>" class="img-fluid rounded">
                            <div class="photo-info mt-2">
                                <h6 class="mb-0"><?php echo htmlspecialchars($photo['title']); ?></h6>
                                <small class="text-muted"><?php echo htmlspecialchars($photo['date']); ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-4">
                <a href="upload-photo.php" class="btn btn-gradient"><span>Upload New Photo</span></a>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>