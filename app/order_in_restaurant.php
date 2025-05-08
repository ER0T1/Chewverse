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
    <title>Chewverse - Order In Restaurant</title>
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

        .search-bar {
            width: 100%;
            padding: 0.5rem 1rem;
            border: 1px solid #ccc;
            border-radius: 1rem;
            background-color: #fff;
            margin: 1rem 0;
            display: flex;
            align-items: center;
        }

        .search-bar i {
            margin-right: 0.5rem;
            color: #6c757d;
        }

        .category-container {
            display: flex;
            overflow-x: auto;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            gap: 0.5rem;
            padding: 0 1rem 0.5rem 1rem;
        }

        .category-item {
            min-width: 80px;
            height: 80px;
            text-align: center;
            padding: 0.5rem;
            border-radius: 1rem;
            flex: 0 0 auto;
            cursor: pointer;
            transition: background-color 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 2px solid transparent;
            background:
                linear-gradient(to bottom, #FBF5EC, #FBF5EC) padding-box,
                linear-gradient(to bottom, #FA4A0C, #942C07) border-box;
        }

        .category-item:hover,
        .category-item.active {
            background:
                linear-gradient(to bottom, #FA4A0C, #942C07) padding-box,
                linear-gradient(to bottom, #FA4A0C, #942C07) border-box;
            color: white;
        }

        .category-item i {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .category-item span {
            font-size: 0.9rem;
            background: linear-gradient(to bottom, #FA4A0C, #942C07);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .category-item:hover span,
        .category-item.active span {
            background: none;
            color: #ECECEC;
        }

        .category-item[data-category="pizza"] img {
            content: url('assets/images/pizza_icon.png');
        }

        .category-item[data-category="pizza"]:hover img,
        .category-item[data-category="pizza"].active img {
            content: url('assets/images/pizza_icon_click.png');
        }

        .category-item[data-category="burger"] img {
            content: url('assets/images/burger_icon.png');
        }

        .category-item[data-category="burger"]:hover img,
        .category-item[data-category="burger"].active img {
            content: url('assets/images/burger_icon_click.png');
        }

        .category-item[data-category="drink"] img {
            content: url('assets/images/drink_icon.png');
        }

        .category-item[data-category="drink"]:hover img,
        .category-item[data-category="drink"].active img {
            content: url('assets/images/drink_icon_click.png');
        }

        .category-item[data-category="fries"] img {
            content: url('assets/images/french_fries_icon.png');
        }

        .category-item[data-category="fries"]:hover img,
        .category-item[data-category="fries"].active img {
            content: url('assets/images/french_fries_icon_click.png');
        }

        .category-item[data-category="veggies"] img {
            content: url('assets/images/veggies_icon.png');
        }

        .category-item[data-category="veggies"]:hover img,
        .category-item[data-category="veggies"].active img {
            content: url('assets/images/veggies_icon_click.png');
        }

        .category-item img {
            max-width: 30px;
            max-width: 30px;
            margin: 0.5rem;
        }

        .menu-section {
            padding: 1rem;
            flex: 1;
            overflow-y: auto;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .menu-item {
            background-color: #fff;
            border-radius: 1rem;
            padding: 0.5rem;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.2s;
        }

        .menu-item:hover {
            transform: scale(1.02);
        }

        .menu-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 1rem;
            margin-bottom: 0.25rem;
        }

        .menu-item h5 {
            margin: 0 0 0 0.5rem;
            font-size: 1rem;
            color: #333;
            text-align: left;
        }

        .menu-item p {
            margin: 0.5rem 0 0 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: #333;
            text-align: left;
        }

        .menu-item-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.5rem;
        }

        .item-info {
            text-align: left;
        }

        .menu-item .bi {
            font-size: 1.2rem;
            color: #ff7043;
            margin: 0.5rem;
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
                <h2 class="col text-left fw-bold mb-0">Order In Restaurant</h2>
            </div>
            <div style="display: flex; align-items: center; gap: 5px;">
                <a href="order_with_ai.php" class="text-decoration-none" style="color: #FA4A0C;">
                    <i class="bi bi-chat-text" style="height: 50px; width: 50px; font-size: 40px; display: flex; align-items: center; justify-content: center;"></i>
                </a>
                <a href="cart.php" class="text-decoration-none">
                    <img src="assets/images/cart.png" alt="Cart" class="cart-icon"></img>
                </a>
            </div>
        </div>

        <div class="search-bar">
            <i class="bi bi-search"></i>
            <span>Search</span>
        </div>

        <div class="category-container">
            <div class="category-item active" data-category="pizza">
                <img src="assets/images/pizza_icon.png" alt="pizza">
                <span>Pizza</span>
            </div>
            <div class="category-item" data-category="burger">
                <img src="assets/images/burger_icon.png" alt="burger">
                <span>Burger</span>
            </div>
            <div class="category-item" data-category="drink">
                <img src="assets/images/drink_icon.png" alt="drink">
                <span>Drink</span>
            </div>
            <div class="category-item" data-category="fries">
                <img src="assets/images/french_fries_icon.png" alt="fries">
                <span>French Fries</span>
            </div>
            <div class="category-item" data-category="veggies">
                <img src="assets/images/veggies_icon.png" alt="veggies">
                <span>Veggies</span>
            </div>
        </div>

        <div class="menu-section">
            <h3>Pizza</h3>
            <div class="menu-grid">
                <div class="menu-item">
                    <img src="assets/images/Capricciosa.png" alt="Capricciosa">
                    <div class="menu-item-footer">
                        <div class="item-info">
                            <h5>Capricciosa</h5>
                            <p>$200</p>
                        </div>
                        <i class="bi bi-heart"></i>
                    </div>
                </div>
                <div class="menu-item">
                    <img src="assets/images/Sicilian.png" alt="Sicilian">
                    <div class="menu-item-footer">
                        <div class="item-info">
                            <h5>Sicilian</h5>
                            <p>$150</p>
                        </div>
                        <i class="bi bi-heart"></i>
                    </div>
                </div>
                <div class="menu-item">
                    <img src="assets/images/Marinara.png" alt="Marinara">
                    <div class="menu-item-footer">
                        <div class="item-info">
                            <h5>Marinara</h5>
                            <p>$99</p>
                        </div>
                        <i class="bi bi-heart"></i>
                    </div>
                </div>
                <div class="menu-item">
                    <img src="assets/images/Pepperoni.png" alt="Pepperoni">
                    <div class="menu-item-footer">
                        <div class="item-info">
                            <h5>Pepperoni</h5>
                            <p>$250</p>
                        </div>
                        <i class="bi bi-heart"></i>
                    </div>
                </div>
                <div class="menu-item">
                    <img src="assets/images/Sicilian2.png" alt="Sicilian">
                    <div class="menu-item-footer">
                        <div class="item-info">
                            <h5>Sicilian</h5>
                            <p>$150</p>
                        </div>
                        <i class="bi bi-heart"></i>
                    </div>
                </div>
                <div class="menu-item">
                    <img src="assets/images/Capricciosa2.png" alt="Capricciosa">
                    <div class="menu-item-footer">
                        <div class="item-info">
                            <h5>Capricciosa</h5>
                            <p>$200</p>
                        </div>
                        <i class="bi bi-heart"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- JS -->
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script>
            // 類別切換功能
            const categoryItems = document.querySelectorAll('.category-item');
            const menuSection = document.querySelector('.menu-section');
            let currentCategory = 'pizza';

            categoryItems.forEach(item => {
                item.addEventListener('click', function() {
                    categoryItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    currentCategory = this.getAttribute('data-category');

                    // 根據類別更新菜單（這裡簡單模擬Pizza菜單，實際應用可從後端獲取）
                    const menuTitle = document.querySelector('.menu-section h3');
                    menuTitle.textContent = currentCategory.charAt(0).toUpperCase() + currentCategory.slice(1);
                    // 在實際應用中，可以使用AJAX從後端獲取對應類別的菜單數據
                    // fetch(`get_menu.php?category=${currentCategory}`)
                    //     .then(response => response.json())
                    //     .then(data => {
                    //         const menuGrid = document.querySelector('.menu-grid');
                    //         menuGrid.innerHTML = data.map(item => `
                    //             <div class="menu-item">
                    //                 <img src="${item.image}" alt="${item.name}">
                    //                 <h5>${item.name}</h5>
                    //                 <p>$${item.price}</p>
                    //                 <i class="bi bi-heart"></i>
                    //             </div>
                    //         `).join('');
                    //     });
                });
            });

            // 加入購物車或喜歡功能（模擬）
            document.querySelectorAll('.menu-item .bi-heart').forEach(heart => {
                heart.addEventListener('click', function(e) {
                    e.preventDefault();
                    const item = this.closest('.menu-item');
                    const name = item.querySelector('h5').textContent;
                    const price = item.querySelector('p').textContent.replace('$', '');
                    this.classList.toggle('text-danger');
                    alert(`${name} (${price}) added to cart!`);
                    // 實際應用中，應發送AJAX請求到後端添加購物車
                    // fetch('add_to_cart.php', {
                    //     method: 'POST',
                    //     headers: { 'Content-Type': 'application/json' },
                    //     body: JSON.stringify({ name, price })
                    // })
                    // .then(response => response.json())
                    // .then(data => {
                    //     if (data.success) alert(`${name} added to cart!`);
                    // });
                });
            });
        </script>
</body>

</html>