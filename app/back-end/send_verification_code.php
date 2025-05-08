<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    // 產生 6 位數驗證碼
    $code = strval(rand(100000, 999999));

    // 儲存驗證資訊
    $_SESSION['reset_email'] = $email;
    $_SESSION['verification_code'] = $code;
    $_SESSION['verification_expiry'] = time() + 300; // 5 分鐘有效

    // 模擬寄送（可改用 mail() 寄出）
    // mail($email, "Your Verification Code", "Code: $code");

    echo json_encode([
        'status' => 'success',
        'message' => "Verification code sent to $email (code: $code)" // 僅用於測試，正式應去除 code
    ]);
    exit;
}

echo json_encode([
    'status' => 'error',
    'message' => 'Invalid request'
]);
exit;
