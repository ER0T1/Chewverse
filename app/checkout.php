<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Chewverse - Checkout</title>
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

        .checkout-content {
            padding: 1rem;
            flex: 1;
            overflow-y: auto;
        }

        .section {
            background-color: #fff;
            border-radius: 1rem;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .section h5 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item span {
            font-size: 0.9rem;
            color: #333;
        }

        .order-total {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            font-weight: 600;
            color: #333;
        }

        .delivery-info,
        .payment-method {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            font-size: 0.9rem;
            color: #333;
        }

        .delivery-info i,
        .payment-method i {
            color: #6c757d;
        }

        .fixed-bottom-container {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #FBF5EC;
            padding: 1rem;
            z-index: 1000;
            display: flex;
            justify-content: center;
            margin: 50px;
        }

        /* 模態框樣式調整 */
        .modal-content {
            border-radius: 1rem;
            padding: 1rem;
            background-color: #FBF5EC;
        }
    </style>
</head>

<body>
    <div class="mask"></div>
    <div class="container my-5">
        <div class="header">
            <div class="row d-flex justify-content-center align-items-center">
                <a class="col-1 btn-left-arrow" href="cart.php">
                    <img src="assets/images/left_arrow.png" alt="Left Arrow">
                </a>
                <h2 class="col text-left fw-bold mb-0">Checkout</h2>
            </div>
        </div>

        <div class="checkout-content">
            <h1 class="text-center fw-bold mb-4">Payment</h1>
            <!-- Order Details -->
            <div class="section">
                <h5>Order Details</h5>
                <div class="order-item">
                    <span>Capricciosa x1</span>
                    <span>$200</span>
                </div>
                <div class="order-item">
                    <span>Marinara x1</span>
                    <span>$99</span>
                </div>
                <div class="order-total">
                    <span>Total</span>
                    <span>$299</span>
                </div>
            </div>

            <!-- Delivery Details -->
            <!-- <div class="section">
                <h5>Delivery Details</h5>
                <div class="delivery-info">
                    <span>Address: 123 Main St</span>
                    <i class="bi bi-chevron-right"></i>
                </div>
                <div class="delivery-info">
                    <span>Delivery Time: 20 min</span>
                    <i class="bi bi-chevron-right"></i>
                </div>
            </div> -->

            <!-- Payment Method -->
            <div class="section">
                <h5>Payment Method</h5>
                <div class="payment-method">
                    <span>Cash on Delivery</span>
                    <i class="bi bi-chevron-right"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="fixed-bottom-container optionsContainer">
        <button class="btn btn-gradient"><span>Proceed to Payment</span></button>
    </div>

    <!-- 選項 模態框 -->
    <div class="modal fade" id="optionsModal" tabindex="-1" aria-labelledby="optionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="optionsModalLabel">Start a chat when the meal gets here?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p>We will send you a message when the meal arrives. You can chat with the delivery person if you have any questions.</p>
                    <div class="d-flex justify-content-center">
                        <img src="assets/images/chewverse.png" alt="Chewverse Logo" style="width: 100px; height: 100px;">
                    </div>
                    <p class="text-center">Chewverse</p>
                    <p class="text-center">We are here to help you!</p>
                </div>

                <div class="modal-footer">
                    <a type="button" class="btn btn-outline-gradient" href="dashboard.php"><span>No, thanks</span></a>
                    <button type="button" class="btn btn-gradient" id="chatOptionsBtn"><span>Yes</span></button>
                </div>
            </div>
        </div>
    </div>

    <!-- 聊天選項 模態框 -->
    <div class="modal fade" id="chatOptionsModal" tabindex="-1" aria-labelledby="chatOptionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="chatOptionsModalLabel">How do you feel like chatting today?</h5>
                </div>

                <div class="modal-body">
                    <a href="chat_with_ai.php" class="btn btn-outline-general w-100 mb-2"><span>Chat With AI</span></a>
                    <a href="table_for_two.php" class="btn btn-outline-general w-100 mb-2"><span>Table For Two</span></a>
                    <a href="join_table.php" class="btn btn-outline-general w-100 mb-2"><span>Join Shared Table</span></a>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelector('.btn-gradient').addEventListener('click', function() {
            var optionsModal = new bootstrap.Modal(document.getElementById('optionsModal'));
            optionsModal.show();
        });
        document.getElementById('chatOptionsBtn').addEventListener('click', function() {
            var optionsModal = bootstrap.Modal.getInstance(document.getElementById('optionsModal'));
            optionsModal.hide();
            var chatOptionsModal = new bootstrap.Modal(document.getElementById('chatOptionsModal'));
            chatOptionsModal.show();
        });
    </script>

</body>

</html>