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
    <title>Chewverse - Order</title>
    <!-- CSS -->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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

        .avatar {
            border: #333 solid 2px;
            border-radius: 999px;
            text-align: center;
        }

        .header {
            background-color: #FBF5EC;
            width: 100%;
            max-width: 400px;
            position: fixed;
            top: 80px;
            padding: 0 1rem 1.5rem;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header .bi {
            font-size: 1.5rem;
            color: #6c757d;
        }

        .search-bar {
            width: 100%;
            max-width: 370px;
            padding: 0.5rem 1rem;
            border: 1px solid #ccc;
            border-radius: 1rem;
            background-color: #fff;
            margin: 1rem;
        }

        .categories-section {
            padding: 1rem;
            /* background-color: #fff; */
            margin-bottom: 1rem;
        }

        .category-container {
            display: flex;
            overflow-x: auto;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            gap: 1rem;
            padding-bottom: 0.5rem;
        }

        .category-item {
            min-width: 100px;
            text-align: center;
            padding: 0.5rem;
            background-color: #f8f9fa;
            border-radius: 1rem;
            flex: 0 0 auto;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .category-item:hover {
            background-color: #e9ecef;
        }

        .category-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 1rem;
            margin-bottom: 0.5rem;
        }

        .category-item span {
            display: block;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .restaurants-section {
            padding: 1rem;
            /* background-color: #fff; */
            margin-bottom: 1rem;
            border-radius: 1rem;
        }

        .restaurant-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }

        .restaurant-item:last-child {
            border-bottom: none;
        }

        .restaurant-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 1rem;
            margin-right: 1rem;
        }

        .restaurant-info {
            flex-grow: 1;
        }

        .restaurant-info h5 {
            margin: 0;
            font-size: 1rem;
            color: #333;
        }

        .restaurant-info p {
            margin: 0.25rem 0;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .rating {
            color: #ff7043;
            font-weight: 600;
        }

        .delivery {
            color: #28a745;
        }

        .time {
            color: #6c757d;
        }

        .see-all {
            color: #333;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .see-all:hover {
            text-decoration: underline;
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
                <h2 class="col text-left fw-bold mb-0">Order</h2>
            </div>
            <div>
                <a href="cart.php" class="text-decoration-none">
                    <img src="assets/images/cart.png" alt="Cart" class="cart-icon"></img>
                </a>
                <a href="profile.php" class="text-decoration-none">
                    <?php if (!empty($avatar) && file_exists($avatar)): ?>
                        <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Profile" width="50" height="50" class="rounded-circle avatar">
                    <?php else: ?>
                        <img src="assets/images/avatar-1.png" alt="Profile" width="50" height="50">
                    <?php endif; ?>
                </a>
            </div>
        </div>
        <div class="search-bar">
            <i class="bi bi-search"></i>
            <span>Search dishes, restaurants</span>
        </div>

        <div class="categories-section">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5>All Categories</h5>
                <a href="#" class="see-all">See All &gt;</a>
            </div>
            <div class="category-container">
                <div class="category-item">
                    <img src="assets/images/drinks.png" alt="Drinks">
                    <span>Drinks</span>
                    <span>Starting $70</span>
                </div>
                <div class="category-item">
                    <img src="assets/images/burger.png" alt="Burger">
                    <span>Burger</span>
                    <span>Starting $50</span>
                </div>
                <div class="category-item">
                    <img src="assets/images/donburi.png" alt="Donburi">
                    <span>Donburi</span>
                    <span>Starting $60</span>
                </div>
                <div class="category-item">
                    <img src="assets/images/salad.png" alt="Salad">
                    <span>Salad</span>
                    <span>Starting $40</span>
                </div>
                <div class="category-item">
                    <img src="assets/images/sushi.png" alt="Sushi">
                    <span>Sushi</span>
                    <span>Starting $80</span>
                </div>
                <div class="category-item">
                    <img src="assets/images/pasta.png" alt="Pasta">
                    <span>Pasta</span>
                    <span>Starting $90</span>
                </div>
            </div>
        </div>

        <div class="restaurants-section">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5>Open Restaurants</h5>
                <a href="#" class="see-all">See All &gt;</a>
            </div>
            <div class="restaurant-item">
                <img src="assets/images/skewers.png" alt="Skewers">
                <div class="restaurant-info">
                    <h5>RapidGrill</h5>
                    <p>Burger - Chicken - Riches - Wings <i class="bi bi-check-circle-fill text-success"></i></p>
                    <div>
                        <span class="rating">★4.7</span>
                        <span class="delivery">🚚 Free</span>
                        <span class="time">⏱ 20 min</span>
                    </div>
                </div>
            </div>
            <div class="restaurant-item">
                <img src="assets/images/salad.png" alt="Salad">
                <div class="restaurant-info">
                    <h5>Rose Garden Restaurant</h5>
                    <p>Salad - Vegan - Healthy - Fresh <i class="bi bi-check-circle-fill text-success"></i></p>
                    <div>
                        <span class="rating">★4.2</span>
                        <span class="delivery">🚚 Free</span>
                        <span class="time">⏱ 25 min</span>
                    </div>
                </div>
            </div>
            <div class="restaurant-item">
                <img src="assets/images/sushi.png" alt="Sushi">
                <div class="restaurant-info">
                    <h5>Tokyo Sushi</h5>
                    <p>Sushi - Japanese - Seafood <i class="bi bi-check-circle-fill text-success"></i></p>
                    <div>
                        <span class="rating">★4.8</span>
                        <span class="delivery">🚚 Free</span>
                        <span class="time">⏱ 15 min</span>
                    </div>
                </div>
            </div>
            <div class="restaurant-item">
                <img src="assets/images/pasta.png" alt="Pasta">
                <div class="restaurant-info">
                    <h5>Italian Pasta House</h5>
                    <p>Pasta - Italian - Gourmet <i class="bi bi-check-circle-fill text-success"></i></p>
                    <div>
                        <span class="rating">★4.5</span>
                        <span class="delivery">🚚 Free</span>
                        <span class="time">⏱ 30 min</span>
                    </div>
                </div>
            </div>
            <div class="restaurant-item">
                <img src="assets/images/donburi.png" alt="Donburi">
                <div class="restaurant-info">
                    <h5>Donburi Delight</h5>
                    <p>Donburi - Rice Bowl - Japanese <i class="bi bi-check-circle-fill text-success"></i></p>
                    <div>
                        <span class="rating">★4.6</span>
                        <span class="delivery">🚚 Free</span>
                        <span class="time">⏱ 20 min</span>
                    </div>
                </div>
            </div>
            <div class="restaurant-item">
                <img src="assets/images/burger.png" alt="Burger">
                <div class="restaurant-info">
                    <h5>Burger Palace</h5>
                    <p>Burger - Fast Food - American <i class="bi bi-check-circle-fill text-success"></i></p>
                    <div>
                        <span class="rating">★4.3</span>
                        <span class="delivery">🚚 Free</span>
                        <span class="time">⏱ 35 min</span>
                    </div>
                </div>
            </div>
            <div class="restaurant-item">
                <img src="assets/images/drinks.png" alt="Drinks">
                <div class="restaurant-info">
                    <h5>Drink Station</h5>
                    <p>Drinks - Smoothies - Refreshing <i class="bi bi-check-circle-fill text-success"></i></p>
                    <div>
                        <span class="rating">★4.4</span>
                        <span class="delivery">🚚 Free</span>
                        <span class="time">⏱ 10 min</span>
                    </div>
                </div>
            </div>
            <div class="restaurant-item">
                <img src="assets/images/steak.png" alt="Steak">
                <div class="restaurant-info">
                    <h5>Steak House</h5>
                    <p>Steak - Grilled - Meat Lover <i class="bi bi-check-circle-fill text-success"></i></p>
                    <div>
                        <span class="rating">★4.9</span>
                        <span class="delivery">🚚 Free</span>
                        <span class="time">⏱ 40 min</span>
                    </div>
                </div>
            </div>
            <div class="restaurant-item">
                <img src="assets/images/pizza.png" alt="Pizza">
                <div class="restaurant-info">
                    <h5>Pizza World</h5>
                    <p>Pizza - Italian - Fast Food <i class="bi bi-check-circle-fill text-success"></i></p>
                    <div>
                        <span class="rating">★4.1</span>
                        <span class="delivery">🚚 Free</span>
                        <span class="time">⏱ 50 min</span>
                    </div>
                </div>
            </div>
            <div class="restaurant-item">
                <img src="assets/images/dessert.png" alt="Dessert">
                <div class="restaurant-info">
                    <h5>Dessert Paradise</h5>
                    <p>Desserts - Sweet Treats - Cakes <i class="bi bi-check-circle-fill text-success"></i></p>
                    <div>
                        <span class="rating">★4.0</span>
                        <span class="delivery">🚚 Free</span>
                        <span class="time">⏱ 45 min</span>
                    </div>
                </div>
            </div>
            <div class="restaurant-item">
                <img src="assets/images/seafood.png" alt="Seafood">
                <div class="restaurant-info">
                    <h5>Seafood Shack</h5>
                    <p>Seafood - Fresh Catch - Ocean Flavors <i class="bi bi-check-circle-fill text-success"></i></p>
                    <div>
                        <span class="rating">★4.7</span>
                        <span class="delivery">🚚 Free</span>
                        <span class="time">⏱ 30 min</span>
                    </div>
                </div>
            </div>
            <div class="restaurant-item">
                <img src="assets/images/vegan.png" alt="Vegan">
                <div class="restaurant-info">
                    <h5>Vegan Bistro</h5>
                    <p>Vegan - Plant-Based - Healthy <i class="bi bi-check-circle-fill text-success"></i></p>
                    <div>
                        <span class="rating">★4.8</span>
                        <span class="delivery">🚚 Free</span>
                        <span class="time">⏱ 20 min</span>
                    </div>
                </div>
            </div>
            <div class="restaurant-item">
                <img src="assets/images/icecream.png" alt="Ice Cream">
                <div class="restaurant-info">
                    <h5>Ice Cream Heaven</h5>
                    <p>Ice Cream - Desserts - Sweet Treats <i class="bi bi-check-circle-fill text-success"></i></p>
                    <div>
                        <span class="rating">★4.6</span>
                        <span class="delivery">🚚 Free</span>
                        <span class="time">⏱ 15 min</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // 啟用平滑滾動
        const categoryContainer = document.querySelector('.category-container');
        let isDown = false;
        let startX;
        let scrollLeft;

        categoryContainer.addEventListener('mousedown', (e) => {
            isDown = true;
            startX = e.pageX - categoryContainer.offsetLeft;
            scrollLeft = categoryContainer.scrollLeft;
        });

        categoryContainer.addEventListener('mouseleave', () => {
            isDown = false;
        });

        categoryContainer.addEventListener('mouseup', () => {
            isDown = false;
        });

        categoryContainer.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - categoryContainer.offsetLeft;
            const walk = (x - startX) * 1.5; // 滾動速度
            categoryContainer.scrollLeft = scrollLeft - walk;
        });

        // 防止觸摸設備滾動頁面
        categoryContainer.addEventListener('touchstart', (e) => {
            isDown = true;
            startX = e.touches[0].pageX - categoryContainer.offsetLeft;
            scrollLeft = categoryContainer.scrollLeft;
        });

        categoryContainer.addEventListener('touchmove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.touches[0].pageX - categoryContainer.offsetLeft;
            const walk = (x - startX) * 1.5;
            categoryContainer.scrollLeft = scrollLeft - walk;
        });

        categoryContainer.addEventListener('touchend', () => {
            isDown = false;
        });
    </script>
</body>

</html>