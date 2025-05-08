<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // 設定資料檔案路徑
    $dataFile = "../database/users.json";

    // 檢查資料檔案是否存在
    if (!file_exists($dataFile)) {
        header("Location: ../login.php?error=2"); // 資料檔案不存在的錯誤
        exit;
    }

    // 讀取使用者資料
    $jsonContent = file_get_contents($dataFile);
    $usersData = json_decode($jsonContent, true);

    // 檢查 JSON 解析是否成功
    if ($usersData === null && json_last_error() !== JSON_ERROR_NONE) {
        header("Location: ../login.php?error=3"); // JSON 解析錯誤
        exit;
    }

    // 尋找匹配的使用者
    $userFound = false;

    foreach ($usersData as $user) {
        if (isset($user['email']) && $user['email'] === $email) {
            // 檢查密碼是否匹配
            if (password_verify($password, $user['password'])) {
                // 設定登入 session
                $_SESSION['user_email'] = $email;
                $_SESSION['user_nickname'] = $user['nickname'];
                $_SESSION['user_avatar'] = $user['avatar'];
                
                // 登入成功後導向首頁或 dashboard
                header("Location: ../dashboard.php");
                exit;
            } else {
                $userFound = true;
                break; // 找到使用者但密碼錯誤
            }
        }
    }

    // 登入失敗 (電子郵件不存在或密碼錯誤)
    if ($userFound) {
        header("Location: ../login.php?error=1"); // 密碼錯誤
    } else {
        header("Location: ../login.php?error=4"); // 帳號不存在
    }
    header("Location: ../login.php?error=99"); // 其他錯誤
    exit;
}
