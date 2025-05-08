<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Chewverse - Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

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

        .avatar-upload {
            border: #ccc solid 2px;
            border-radius: 0.5rem;
            padding: 1rem;
            text-align: center;
            color: #999;
            background-color: #fff;
        }

        .container {
            max-width: 480px;
            padding: 2rem;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <h2 class="text-left text-gradient fw-bold">Register a New Account</h2>

        <form action="back-end/register_submit.php" method="post" enctype="multipart/form-data">
            <div class="mb-1">
                <label class="form-label" for="nickname">Nick Name</label>
                <input type="text" id="nickname" name="nickname" class="form-control" placeholder="Your nick name" title="Please enter your Nick Name." required>
            </div>

            <div class="mb-1">
                <label class="form-label" for="email">E-mail</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="example@mail.com" title="Please enter your E-mail." required>
            </div>

            <div class="mb-1">
                <label class="form-label" for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control" placeholder="09xx-xxxxxx" pattern="[0-9]{2}[0-9]{8}" title="Please enter your Phone Number." required>
            </div>

            <div class="mb-1">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="6-12 characters" title="Please enter your Password." required>
            </div>

            <div class="mb-1">
                <label class="form-label" for="confirm">Confirm Password</label>
                <input type="password" id="confirm" name="confirm" class="form-control" placeholder="6-12 characters" title="Please enter your Password." required>
            </div>

            <div class="mb-1">
                <label class="form-label" for="avatar">Upload an Avatar</label>
                <input type="file" id="avatar" name="avatar" accept="image/*" style="display: none;" onchange="previewImage(event)">
                
                <div class="row">
                    <label for="avatar">
                        <div class="avatar-upload" id="avatar-preview">
                            <img src="assets/images/image.png" alt="Upload" width="40" title="Please upload your Avatar.">
                        </div>
                    </label>
                </div>
            </div>

            <div class="mb-1">
                <label class="form-label" for="code">Verification Code</label>
                <div class="d-flex gap-2">
                    <input type="text" id="code" name="code" class="form-control w-50" placeholder="Enter code" title="Please enter the verification code." required>
                    <button type="button" class="btn btn-outline-gradient w-50" onclick="sendCode()" title="Click the button to send a verification code to your e-mail."><span>Send Code</span></button>
                </div>
            </div>

            <div id="countdown" class="text-muted small mt-1"></div>

            <div class="d-flex justify-content-between mt-5">
                <button type="reset" class="btn btn-outline-gradient" onclick="window.history.back();"><span>Cancel</span></button>
                <button type="submit" class="btn btn-gradient"><span>Confirm</span></button>
            </div>
        </form>
    </div>

    <!-- JS -->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        let countdownInterval;

        function sendCode() {
            const email = document.getElementById('email').value.trim();
            if (!email) {
                alert("Please enter your email first.");
                return;
            }

            fetch('back-end/send_verification_code.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        email: email
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message); // 測試階段保留
                        startCountdown(300); // 啟動 5 分鐘倒數
                    } else {
                        alert("Failed to send code: " + data.message);
                    }
                })
                .catch(error => {
                    alert("Error sending code.");
                    console.error(error);
                });
        }

        function startCountdown(seconds) {
            clearInterval(countdownInterval);
            const countdownEl = document.getElementById('countdown');
            let timeLeft = seconds;

            countdownInterval = setInterval(() => {
                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    countdownEl.textContent = "Verification code has expired. Please request a new one.";
                    return;
                }

                const minutes = Math.floor(timeLeft / 60);
                const secs = timeLeft % 60;
                countdownEl.textContent = `Code expires in ${minutes}:${secs.toString().padStart(2, '0')}`;
                timeLeft--;
            }, 1000);
        }
    </script>
</body>

</html>