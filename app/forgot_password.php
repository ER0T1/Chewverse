<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Chewverse - Forgot Password</title>
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

        .form-group label {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <h2 class="text-left text-gradient fw-bold">Forgot Password</h2>

        <form action="back-end/verify_code.php" method="POST">
            <div class="mb-1">
                <label class="form-label" for="email">E-mail</label>
                <input class="form-control" type="email" class="form-control" id="email" name="email" placeholder="example@mail.com" required>
            </div>

            <div class="mb-1">
                <label class="form-label" for="code">Verification Code</label>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control w-50" id="code" name="code" placeholder="Enter code" required>
                    <button type="button" class="btn btn-outline-gradient w-50" onclick="sendCode()"><span>Send Code</span></button>
                </div>
            </div>

            <div id="countdown" class="text-muted small mt-1"></div>

            <div class="d-flex justify-content-between mt-5">
                <button type="button" class="btn btn-outline-gradient" onclick="window.history.back();"><span>Cancel</span></button>
                <button type="submit" class="btn btn-gradient"><span>Next</span></button>
            </div>
        </form>
    </div>

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