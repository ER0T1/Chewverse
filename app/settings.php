<?php
session_start();

// 檢查用戶是否已登入
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php?error=5");
    exit;
}

// 設定 users.json 資料檔案路徑
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
$signature = !empty($userData['signature']) ? $userData['signature'] : 'signature'; // 若資料中沒有 signature 欄位或為空字串
$userRests = $userData['restaurants'] ?? []; // 若資料中沒有 restaurants 欄位

// 除錯用：檢查讀取到的資料
// echo "<pre>"; print_r($usersData); echo "</pre>";
// echo "<pre>"; print_r($userData); echo "</pre>";

// 設定 restaurants.json 資料檔案路徑
$restaurantsFile = "database/restaurants.json";
$restaurantsData = null;

// 讀取 restaurants.json 資料
if (file_exists($restaurantsFile)) {
    $jsonContent = file_get_contents($restaurantsFile);
    $restaurantsData = json_decode($jsonContent, true);

    // 檢查 JSON 解析是否成功
    if ($restaurantsData === null && json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON 解析錯誤: " . json_last_error_msg());
    } else {
        // 確保 $restaurantsData 是陣列
        if (!is_array($restaurantsData)) {
            error_log("restaurants.json 格式錯誤: 預期為陣列，實際為 " . gettype($restaurantsData));
        }
    }
} else {
    error_log("找不到 restaurants.json 檔案");
}

// 檢查用戶是否擁有餐廳資料，若有存入 $rests 中
$rests = [];
if ($restaurantsData !== null && is_array($restaurantsData)) {
    foreach ($userRests as $restId) {
        if (isset($restId)) {
            // 在餐廳資料中尋找匹配的餐廳 ID
            foreach ($restaurantsData as $restaurant) {
                if (isset($restaurant['id']) && $restaurant['id'] === $restId) {
                    $rests[] = $restaurant; // 正確的語法
                }
            }
        }
    }
} else {
    error_log("restaurants.json 格式錯誤或檔案不存在");
}

// 除錯用：檢查讀取到的資料
// echo "<pre>"; print_r($restaurantsData); echo "</pre>";
// echo "<pre>"; print_r($rests); echo "</pre>";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Chewverse - Settings</title>
    <!-- CSS -->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .container {
            max-width: 480px;
            padding: 2rem;
        }

        .form-label {
            font-weight: 600;
            margin-top: 1rem;
        }

        .form-control {
            border-radius: 0.5rem;
            border: #ccc solid 2px;
            background-color: #fff;
        }

        .btn-left-arrow,
        .btn-edit {
            border: none;
            background: none;
        }

        .profile-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }

        .avatar-default {
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .avatar {
            border: #ccc solid 2px;
            border-radius: 0.5rem;
            text-align: center;
            color: #999;
            background-color: #fff;
            width: 84px;
            height: 84px;
            overflow: hidden;

            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        /* 模態框樣式調整 */
        .modal-content {
            border-radius: 1rem;
            padding: 1rem;
            background-color: #FBF5EC;
        }

        .upload-area {
            width: 150px;
            height: 150px;
            border: 2px dashed #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 1rem auto;
        }

        .avatar-upload {
            border: #ccc solid 2px;
            border-radius: 0.5rem;
            padding: 1rem;
            text-align: center;
            color: #999;
            background-color: #fff;
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
    <div class="container my-5">
        <div class="row d-flex justify-content-flex-start align-items-center mb-4">
            <a class="col-1 btn-left-arrow" href="profile.php">
                <img src="assets/images/left_arrow.png" alt="Left Arrow">
            </a>
            <h2 class="col text-left fw-bold mb-0">Settings</h2>
        </div>

        <div class="profile-item">
            <div class="d-flex align-items-flex-start">
                <div id="avatarContainer">
                    <div class="avatar me-3">
                        <?php if (!empty($avatar) && file_exists($avatar)): ?>
                            <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" style="width: 100%; height: auto; min-height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <img src="assets/images/avatar-2.png" alt="Avatar" style="width: 50px; height: 50px;" class="avatar-default">
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <h4><?php echo htmlspecialchars($nickname); ?></h4>
                    <small class="text-muted"><?php echo htmlspecialchars($signature); ?></small>
                </div>
            </div>
            <div id="nickNameContainer">
                <img src="assets/images/edit.png" alt="Edit" style="width: 20px; height: 20px;">
            </div>
        </div>

        <div class="profile-item">
            <div style="font-weight: 600;">E-mail</div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span><?php echo htmlspecialchars($email); ?>&nbsp;&nbsp;</span>
                <div id="emailContainer">
                    <img src="assets/images/edit.png" alt="Edit" style="width: 20px; height: 20px;">
                </div>
            </div>
        </div>

        <div class="profile-item">
            <div style="font-weight: 600;">Phone</div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span><?php echo htmlspecialchars($userData['phone'] ?? ''); ?>&nbsp;&nbsp;</span>
                <div id="phoneContainer">
                    <img src="assets/images/edit.png" alt="Edit" style="width: 20px; height: 20px;">
                </div>
            </div>
        </div>

        <div class="profile-item">
            <div style="font-weight: 600;">Password</div>
            <div id="passwordContainer">
                <button class="btn btn-outline-gradient"><span>Modify Password</span></button>
            </div>
        </div>

        <div class="profile-item">
            <div style="display: flex; flex-direction: column; width: 100%;">
                <div style="font-weight: 600;">Owned Restaurants</div>
                <?php if (!empty($rests)): ?>
                    <?php foreach ($rests as $index => $rest): ?>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span><?php echo htmlspecialchars($rest['name']); ?></span>
                            <div class="d-flex align-items-center">
                                <div class="edit-restaurant me-2"
                                    data-restaurant-id="<?php echo htmlspecialchars($rest['id']); ?>"
                                    data-restaurant-name="<?php echo htmlspecialchars($rest['name']); ?>"
                                    data-restaurant-address="<?php echo htmlspecialchars($rest['address']); ?>"
                                    data-restaurant-phone="<?php echo htmlspecialchars($rest['phone']); ?>"
                                    data-restaurant-description="<?php echo htmlspecialchars($rest['description']); ?>"
                                    data-restaurant-index="<?php echo $index; ?>">
                                    <img src="assets/images/edit.png" alt="Edit" style="width: 20px; height: 20px; cursor: pointer;">
                                </div>
                                <div class="delete-restaurant" data-restaurant-id="<?php echo htmlspecialchars($rest['id']); ?>">
                                    <img src="assets/images/delete.png" alt="Delete" style="width: 20px; height: 20px; cursor: pointer;">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="mt-2 text-muted">You don't own any restaurants yet.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="profile-item">
            <div id="restaurantContainer" style="width: 100%;">
                <button class="btn btn-outline-gradient w-100"><span>Add Restaurant</span></button>
            </div>
        </div>

        <div class="profile-item">
            <a href="index.php" class="btn btn-gradient w-100"><span>Sign Out</span></a>
        </div>
    </div>

    <!-- 編輯大頭貼 模態框 -->
    <div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="back-end/avatar_modify.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="avatarModalLabel">Modify Avatar</h5>
                    </div>

                    <div class="modal-body">
                        <label class="form-label" for="avatar">Upload an Avatar</label>
                        <input type="file" id="avatar" name="avatar" accept="image/*" style="display: none;" onchange="previewImage(event)">

                        <div class="row">
                            <label for="avatar">
                                <div class="avatar-upload" id="avatar-preview">
                                    <?php if (!empty($avatar) && file_exists($avatar)): ?>
                                        <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Current Avatar" style="max-height:100%; max-width:100%; border-radius: 1rem;">
                                    <?php else: ?>
                                        <img src="assets/images/image.png" alt="Upload" width="40">
                                    <?php endif; ?>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer d-flex justify-content-between mt-5">
                        <button type="reset" class="btn btn-outline-gradient" data-bs-dismiss="modal"><span>Cancel</span></button>
                        <?php if (!empty($avatar) && file_exists($avatar)): ?>
                            <button type="button" id="deleteAvatarBtn" class="btn btn-outline-gradient"><span>Delete</span></button>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-gradient"><span>Modify</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 編輯暱稱&簽名 模態框 -->
    <div class="modal fade" id="nickNameModal" tabindex="-1" aria-labelledby="nickNameModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nickNameModalLabel">Modify Profile</h5>
                </div>

                <div class="modal-body">
                    <form id="nickNameForm" action="back-end/nick_name_modify.php" method="POST">
                        <div class="mb-1">
                            <label class="form-label" for="nickname">Nick Name</label>
                            <input type="text" id="nickname" name="nickname" class="form-control" value="<?php echo htmlspecialchars($nickname); ?>" required>
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="signature">Signature</label>
                            <textarea id="signature" name="signature" class="form-control" rows="3"><?php echo htmlspecialchars($signature); ?></textarea>
                        </div>
                    </form>
                </div>

                <div class="modal-footer d-flex justify-content-between mt-5">
                    <button type="reset" class="btn btn-outline-gradient" data-bs-dismiss="modal"><span>Cancel</span></button>
                    <button type="submit" form="nickNameForm" class="btn btn-gradient"><span>Modify</span></button>
                </div>
            </div>
        </div>
    </div>

    <!-- 編輯Email 模態框 -->
    <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="emailModalLabel">Modify Email</h5>
                </div>

                <div class="modal-body">
                    <form id="emailForm" action="back-end/email_modify.php" method="POST">
                        <div class="mb-1">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                    </form>
                </div>

                <div class="modal-footer d-flex justify-content-between mt-5">
                    <button type="reset" class="btn btn-outline-gradient" data-bs-dismiss="modal"><span>Cancel</span></button>
                    <button type="submit" form="emailForm" class="btn btn-gradient"><span>Modify</span></button>
                </div>
            </div>
        </div>
    </div>

    <!-- 編輯電話 模態框 -->
    <div class="modal fade" id="phoneModal" tabindex="-1" aria-labelledby="phoneModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="phoneModalLabel">Modify Phone</h5>
                </div>

                <div class="modal-body">
                    <form id="phoneForm" action="back-end/phone_modify.php" method="POST">
                        <div class="mb-1">
                            <label class="form-label" for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between mt-5">
                    <button type="reset" class="btn btn-outline-gradient" data-bs-dismiss="modal"><span>Cancel</span></button>
                    <button type="submit" form="phoneForm" class="btn btn-gradient"><span>Modify</span></button>
                </div>
            </div>
        </div>
    </div>

    <!-- 編輯密碼 驗證舊密碼 模態框 -->
    <div class="modal fade" id="verifyPasswordModal" tabindex="-1" aria-labelledby="verifyPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verifyPasswordModalLabel">Modify Password</h5>
                </div>

                <div class="modal-body">
                    <div class="mb-1">
                        <label class="form-label" for="oldPassword">Enter Old Password</label>
                        <input type="password" id="oldPassword" name="oldPassword" class="form-control" required>
                    </div>
                </div>
                <div class="error-message" id="verifyErrorMessage"></div>

                <div class="modal-footer d-flex justify-content-between mt-5">
                    <button type="reset" class="btn btn-outline-gradient" data-bs-dismiss="modal"><span>Cancel</span></button>
                    <button type="button" id="verifyPasswordBtn" class="btn btn-gradient"><span>Next</span></button>
                </div>
            </div>
        </div>
    </div>

    <!-- 編輯密碼 修改新密碼 模態框 -->
    <div class="modal fade" id="modifyPasswordModal" tabindex="-1" aria-labelledby="modifyPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modifyPasswordModalLabel">Modify Password</h5>
                </div>

                <div class="modal-body">
                    <form id="modifyPasswordForm" action="back-end/password_modify.php" method="POST">
                        <div class="mb-1">
                            <label class="form-label" for="newPassword">New Password</label>
                            <input type="password" id="newPassword" name="newPassword" class="form-control" required>
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="confirmPassword">Confirm Password</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required>
                        </div>
                    </form>
                </div>

                <div class="modal-footer d-flex justify-content-between mt-5">
                    <button type="reset" class="btn btn-outline-gradient" data-bs-dismiss="modal"><span>Cancel</span></button>
                    <button type="submit" form="modifyPasswordForm" class="btn btn-gradient"><span>Modify</span></button>
                </div>
            </div>
        </div>
    </div>

    <!-- 增加餐廳 模態框 -->
    <div class="modal fade" id="addRestaurantModal" tabindex="-1" aria-labelledby="addRestaurantModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRestaurantModalLabel">Add Restaurant</h5>
                </div>

                <div class="modal-body">
                    <form id="addRestaurantForm" action="back-end/restaurant_add.php" method="POST">
                        <div class="mb-1">
                            <label class="form-label" for="restaurantName">Restaurant Name</label>
                            <input type="text" id="restaurantName" name="restaurantName" class="form-control" required>
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="restaurantAddress">Restaurant Address</label>
                            <input type="text" id="restaurantAddress" name="restaurantAddress" class="form-control" required>
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="restaurantPhone">Restaurant Phone</label>
                            <input type="text" id="restaurantPhone" name="restaurantPhone" class="form-control" required>
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="restaurantDescription">Restaurant Description</label>
                            <textarea id="restaurantDescription" name="restaurantDescription" class="form-control" rows="3"></textarea>
                        </div>
                    </form>
                </div>

                <div class="modal-footer d-flex justify-content-between mt-5">
                    <button type="reset" class="btn btn-outline-gradient" data-bs-dismiss="modal"><span>Cancel</span></button>
                    <button type="submit" form="addRestaurantForm" class="btn btn-gradient"><span>Add</span></button>
                </div>
            </div>
        </div>
    </div>

    <!-- 編輯餐廳 模態框 -->
    <div class="modal fade" id="modifyRestaurantModal" tabindex="-1" aria-labelledby="modifyRestaurantModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modifyRestaurantModalLabel">Modify Restaurant</h5>
                </div>

                <div class="modal-body">
                    <form id="modifyRestaurantForm" action="back-end/restaurant_modify.php" method="POST">
                        <input type="hidden" id="restaurantId" name="restaurantId" value="">
                        <input type="hidden" id="restaurantIndex" name="restaurantIndex" value="">
                        <div class="mb-1">
                            <label class="form-label" for="modifyRestaurantName">Restaurant Name</label>
                            <input type="text" id="modifyRestaurantName" name="modifyRestaurantName" class="form-control" required>
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="modifyRestaurantAddress">Restaurant Address</label>
                            <input type="text" id="modifyRestaurantAddress" name="modifyRestaurantAddress" class="form-control" required>
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="modifyRestaurantPhone">Restaurant Phone</label>
                            <input type="text" id="modifyRestaurantPhone" name="modifyRestaurantPhone" class="form-control" required>
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="modifyRestaurantDescription">Restaurant Description</label>
                            <textarea id="modifyRestaurantDescription" name="modifyRestaurantDescription" class="form-control" rows="3"></textarea>
                        </div>
                    </form>
                </div>

                <div class="modal-footer d-flex justify-content-between mt-5">
                    <button type="reset" class="btn btn-outline-gradient" data-bs-dismiss="modal"><span>Cancel</span></button>
                    <button type="button" id="deleteRestaurantBtn" class="btn btn-outline-gradient"><span>Delete</span></button>
                    <button type="submit" form="modifyRestaurantForm" class="btn btn-gradient"><span>Save</span></button>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        document.getElementById('avatarContainer').addEventListener('click', function() {
            var myModal = new bootstrap.Modal(document.getElementById('avatarModal'));
            myModal.show();
        });
        document.getElementById('deleteAvatarBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to delete your profile picture?')) {
                window.location.href = 'back-end/avatar_delete.php';
            }
        });
        document.getElementById('nickNameContainer').addEventListener('click', function() {
            var myModal = new bootstrap.Modal(document.getElementById('nickNameModal'));
            myModal.show();
        });
        document.getElementById('emailContainer').addEventListener('click', function() {
            var myModal = new bootstrap.Modal(document.getElementById('emailModal'));
            myModal.show();
        });
        document.getElementById('phoneContainer').addEventListener('click', function() {
            var myModal = new bootstrap.Modal(document.getElementById('phoneModal'));
            myModal.show();
        });
        document.getElementById('passwordContainer').addEventListener('click', function() {
            var myModal = new bootstrap.Modal(document.getElementById('verifyPasswordModal'));
            myModal.show();
        });
        document.getElementById('verifyPasswordBtn').addEventListener('click', function() {
            var oldPassword = document.getElementById('oldPassword').value;
            var verifyErrorMessage = document.getElementById('verifyErrorMessage');

            if (oldPassword.length < 6 || oldPassword.length > 12) {
                verifyErrorMessage.textContent = 'Old password must be 6-12 characters.';
                verifyErrorMessage.style.display = 'block';
                return;
            }

            // 發送AJAX請求驗證舊密碼
            fetch('back-end/password_verify.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        oldPassword: oldPassword
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // 關閉第一個模態框
                        var verifyModal = bootstrap.Modal.getInstance(document.getElementById('verifyPasswordModal'));
                        verifyModal.hide();

                        // 顯示第二個模態框（修改新密碼）
                        var modifyModal = new bootstrap.Modal(document.getElementById('modifyPasswordModal'));
                        modifyModal.show();
                    } else {
                        verifyErrorMessage.textContent = data.message;
                        verifyErrorMessage.style.display = 'block';
                    }
                })
                .catch(error => {
                    verifyErrorMessage.textContent = 'An error occurred. Please try again.';
                    verifyErrorMessage.style.display = 'block';
                });
        });
        document.getElementById('restaurantContainer').addEventListener('click', function() {
            var myModal = new bootstrap.Modal(document.getElementById('addRestaurantModal'));
            myModal.show();
        });
        document.addEventListener('DOMContentLoaded', function() {
            // 使用事件代理處理所有編輯餐廳按鈕
            document.body.addEventListener('click', function(event) {
                const editButton = event.target.closest('.edit-restaurant');
                if (editButton) {
                    // 獲取餐廳數據
                    const restaurantId = editButton.dataset.restaurantId;
                    const restaurantName = editButton.dataset.restaurantName;
                    const restaurantAddress = editButton.dataset.restaurantAddress;
                    const restaurantPhone = editButton.dataset.restaurantPhone;
                    const restaurantDescription = editButton.dataset.restaurantDescription;
                    const restaurantIndex = editButton.dataset.restaurantIndex;

                    // 填充模態框表單
                    document.getElementById('restaurantId').value = restaurantId;
                    document.getElementById('restaurantIndex').value = restaurantIndex;
                    document.getElementById('modifyRestaurantName').value = restaurantName;
                    document.getElementById('modifyRestaurantAddress').value = restaurantAddress;
                    document.getElementById('modifyRestaurantPhone').value = restaurantPhone;
                    document.getElementById('modifyRestaurantDescription').value = restaurantDescription;

                    // 顯示模態框
                    const myModal = new bootstrap.Modal(document.getElementById('modifyRestaurantModal'));
                    myModal.show();
                }
            });

            // 處理刪除餐廳按鈕
            document.body.addEventListener('click', function(event) {
                const deleteButton = event.target.closest('.delete-restaurant');
                if (deleteButton) {
                    const restaurantId = deleteButton.dataset.restaurantId;
                    const restaurantItem = deleteButton.closest('.d-flex.justify-content-between.align-items-center.mt-2');

                    if (confirm('Are you sure you want to delete this restaurant? This action cannot be undone.')) {
                        // 發送AJAX請求刪除餐廳
                        fetch('back-end/restaurant_delete.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    restaurantId: restaurantId
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // 從DOM中移除餐廳項目
                                    restaurantItem.remove();

                                    // 可選：顯示成功消息
                                    // alert('Restaurant deleted successfully!');
                                } else {
                                    alert(data.message || 'Failed to delete restaurant, please try again later.');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('An error occurred. Please try again later.');
                            });
                    }
                }
            });

            // 處理模態框中的刪除餐廳按鈕
            document.getElementById('deleteRestaurantBtn').addEventListener('click', function() {
                const restaurantId = document.getElementById('restaurantId').value;
                const restaurantIndex = document.getElementById('restaurantIndex').value;

                if (confirm('Are you sure you want to delete this restaurant? This action cannot be undone.')) {
                    // 發送AJAX請求刪除餐廳
                    fetch('back-end/restaurant_delete.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                restaurantId: restaurantId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // 關閉模態框
                                var modifyModal = bootstrap.Modal.getInstance(document.getElementById('modifyRestaurantModal'));
                                modifyModal.hide();

                                // 從DOM中移除餐廳項目
                                const restaurantItems = document.querySelectorAll('.d-flex.justify-content-between.align-items-center.mt-2');
                                if (restaurantItems && restaurantItems[restaurantIndex]) {
                                    restaurantItems[restaurantIndex].remove();
                                }
                            } else {
                                alert(data.message || 'Failed to delete restaurant, please try again later.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred. Please try again later.');
                        });
                }
            });
        });
    </script>
</body>