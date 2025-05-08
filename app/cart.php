<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Chewverse - Cart</title>
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

        .cart-items {
            padding: 1rem;
            flex: 1;
        }

        .cart-item {
            display: flex;
            align-items: center;
            background-color: #fff;
            border-radius: 1rem;
            padding: 0.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .cart-item img {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: 1rem;
            margin-right: 1rem;
        }

        .cart-info {
            flex-grow: 1;
        }

        .cart-info h5 {
            margin: 0;
            font-size: 1rem;
            color: #333;
        }

        .cart-info p {
            margin: 0.25rem 0;
            font-size: 1rem;
            font-weight: 600;
            color: #333;
        }

        .remove-btn {
            background: none;
            border: none;
            cursor: pointer;
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
    </style>
</head>

<body>
    <div class="mask"></div>
    <div class="container my-5">
        <div class="header">
            <div class="row d-flex justify-content-center align-items-center">
                <a class="col-1 btn-left-arrow" href="order.php">
                    <img src="assets/images/left_arrow.png" alt="Left Arrow">
                </a>
                <h2 class="col text-left fw-bold mb-0">Cart</h2>
            </div>
        </div>

        <div class="cart-items">
            <div class="cart-item" data-id="1">
                <img src="assets/images/capricciosa.png" alt="Capricciosa">
                <div class="cart-info">
                    <h5>Capricciosa</h5>
                    <p>$200</p>
                </div>
                <button class="remove-btn" title="Remove"><img src="assets/images/delete.png" style="width: 20px; height: 20px;"></button>
            </div>

            <div class="cart-item" data-id="2">
                <img src="assets/images/marinara.png" alt="Marinara">
                <div class="cart-info">
                    <h5>Marinara</h5>
                    <p>$99</p>
                </div>
                <button class="remove-btn" title="Remove"><img src="assets/images/delete.png" style="width: 20px; height: 20px;"></button>
            </div>
        </div>
    </div>

    <div class="fixed-bottom-container">
        <a class="btn btn-gradient" href="checkout.php"><span>Checkout</span></a>
    </div>

    <!-- JS -->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>