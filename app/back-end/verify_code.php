<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $inputCode = trim($_POST['code'] ?? '');

    if (
        !isset($_SESSION['verification_code']) ||
        !isset($_SESSION['verification_expiry']) ||
        time() > $_SESSION['verification_expiry']
    ) {
        echo "<script>alert('Verification code expired. Please request a new one.'); window.location.href='forgot_password.php';</script>";
        exit;
    }

    if ($inputCode !== $_SESSION['verification_code']) {
        echo "<script>alert('Incorrect verification code.'); window.history.back();</script>";
        exit;
    }

    // 驗證成功，只保留 email 並前往重設密碼頁
    $_SESSION['reset_email'] = $email;
    unset($_SESSION['verification_code'], $_SESSION['verification_expiry']);
    echo "<script>window.location.href='../reset_password.php';</script>";
    exit;
}

header("Location: ../forgot_password.php");
exit;
