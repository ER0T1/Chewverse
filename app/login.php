<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Chewverse - Sign In</title>
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

        .container {
            max-width: 480px;
            padding: 2rem;
        }

        .form-footer a {
            font-size: 0.9rem;
            margin: 2rem;
            color: #942C07;
            text-decoration: none;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container my-5">
        <h2 class="text-left text-gradient fw-bold">Sign In</h2>

        <?php
        // 檢查是否有錯誤參數並顯示對應的錯誤訊息
        if (isset($_GET['error'])) {
            $errorMessage = '';
            switch ($_GET['error']) {
                case 1:
                    $errorMessage = 'Incorrect password. Please try again.';
                    break;
                case 2:
                    $errorMessage = 'User data file not found. Please contact support.';
                    break;
                case 3:
                    $errorMessage = 'Error reading user data. Please contact support.';
                    break;
                case 4:
                    $errorMessage = 'Account does not exist. Please register.';
                    break;
                case 5:
                    $errorMessage = 'Not logged in. Please log in to continue.';
                    break;
                default:
                    $errorMessage = 'An unexpected error occurred. Please try again.';
                    break;
            }
            echo '<div class="alert alert-danger text-center" role="alert">' . htmlspecialchars($errorMessage) . '</div>';
        }
        ?>

        <form action="back-end/login_submit.php" method="POST">
            <div class="mb-1">
                <label class="form-label" for="email" class="form-label">E-mail</label>
                <input class="form-control" type="email" id="email" name="email" placeholder="example@mail.com" required>
            </div>

            <div class="mb-1">
                <label class="form-label" for="password" class="form-label">Password</label>
                <input class="form-control" type="password" id="password" name="password" placeholder="6-12 characters" required>
            </div>

            <div class="d-flex justify-content-between my-5">
                <button class="btn btn-outline-gradient" onclick="window.history.back();"><span>Cancel</span></button>
                <button type="submit" class="btn btn-gradient"><span>Sign In</span></button>
            </div>

            <div class="form-footer text-center mt-5">
                <a href="forgot_password.php">Forget Password</a>
                <a href="register.php">No Account</a>
            </div>
        </form>
    </div>

    <!-- JS -->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        setTimeout(function() {
            document.querySelector('.alert')?.remove();
        }, 5000); // 5秒後消失
    </script>
</body>

</html>