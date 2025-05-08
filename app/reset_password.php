<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Chewverse - Reset Password</title>
    <!-- CSS -->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .form-label {
            font-weight: 600;
            margin-top: 1rem;
        }

        .form-control {
            border-radius: 0.5rem;
            border: #ccc solid 2px;
            background-color: #fff;
        }

        .form-control-plaintext {
            border: #ccc solid 2px;
            background-color: #eee;
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
        }

        .container {
            max-width: 480px;
            padding: 2rem;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-left text-gradient fw-bold">Reset Password</h2>

        <form action="back-end/reset_password_submit.php" method="POST">
            <div class="mb-1">
                <label class="form-label" for="email">E-mail</label>
                <input type="email" readonly class="form-control-plaintext" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['reset_email'] ?? ''); ?>">
            </div>

            <div class="mb-1">
                <label class="form-label" for="new_password">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" placeholder="6-12 characters" required>
            </div>

            <div class="mb-1">
                <label class="form-label" for="confirm_password">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="6-12 characters" required>
            </div>

            <div class="d-flex justify-content-between mt-5">
                <button type="reset" class="btn btn-outline-gradient" onclick="window.history.back();"><span>Cancel</span></button>
                <button type="submit" class="btn btn-gradient"><span>Reset</span></button>
            </div>
        </form>
    </div>

    <!-- JS -->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>