<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $inputCode = trim($_POST['code'] ?? '');
    $nickname = $_POST['nickname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    $code = $_POST['code'];

    // 驗證碼過期或不存在
    if (
        !isset($_SESSION['verification_code']) ||
        !isset($_SESSION['verification_expiry']) ||
        time() > $_SESSION['verification_expiry']
    ) {
        echo "<script>alert('Verification code expired. Please request a new one.'); window.location.href='../register.php';</script>";
        exit;
    }

    if ($inputCode !== $_SESSION['verification_code']) {
        echo "<script>alert('Incorrect verification code.'); window.history.back();</script>";
        exit;
    }

    // 驗證密碼
    if ($password !== $confirm) {
        echo "Passwords do not match.";
        exit;
    }

    // 設定資料檔案路徑
    $dataFile = "../database/users.json";

    // 檢查檔案是否可寫入
    if (!is_writable($dataFile) && file_exists($dataFile)) {
        $_SESSION['error_message'] = '系統錯誤：無法寫入用戶資料，請聯繫管理員';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // 讀取現有資料
    if (file_exists($dataFile)) {
        $jsonContent = file_get_contents($dataFile);
        $usersData = json_decode($jsonContent, true);

        // 檢查 JSON 解析是否成功
        if ($usersData === null && json_last_error() !== JSON_ERROR_NONE) {
            $usersData = [];
        }
    } else {
        $usersData = [];
    }

    // 準備要存儲的使用者資料
    $userData = [
        'nickname' => $nickname,
        'email' => $email,
        'phone' => $phone,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'avatar' => "",
        'created_at' => date('Y-m-d H:i:s')
    ];

    // 檢查電子郵件是否已存在
    foreach ($usersData as $user) {
        if (isset($user['email']) && $user['email'] === $email) {
            echo "<script>alert('This email address has already been registered.'); window.history.back();</script>";
            exit;
        }
    }

    // 處理圖片上傳
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $uploadDir = "uploads/images/";
        if (!is_dir("../" . $uploadDir)) {
            mkdir("../" . $uploadDir, 0755, true);
        }

        $filename = uniqid() . "_" . basename($_FILES["avatar"]["name"]);
        $targetPath = $uploadDir . $filename;
        $userData['avatar'] = $targetPath;
        move_uploaded_file($_FILES["avatar"]["tmp_name"], "../" . $targetPath);
    }

    // 新增使用者資料
    $usersData[] = $userData;

    // 寫回 data.json 檔案
    file_put_contents($dataFile, json_encode($usersData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // 清除驗證碼 session
    unset($_SESSION['verification_code']);
    unset($_SESSION['verification_expiry']);

    // 註冊完成後導向 login 頁面
    $_SESSION['success_message'] = 'Registration successful! Please log in.';
    header('Location: ../login.php');
    exit;
}
