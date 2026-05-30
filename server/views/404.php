<!DOCTYPE html>
<html lang="vi" style="height: 100%;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT QUIZ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #000;
        height: 100vh;
        width: 100vw;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        overflow: hidden;
    }

    .error-wrapper {
        text-align: center;
        padding: 40px;
        width: 100%;
        max-width: 1000px;
    }

    .big-text {
        font-size: 15rem;
        font-weight: 900;
        color: #3498db;
        text-shadow: 0 0 50px rgba(52, 152, 219, 0.4);
        margin-bottom: 20px;
        animation: pulse 3s ease-in-out infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            text-shadow: 0 0 50px rgba(52, 152, 219, 0.4);
        }

        50% {
            transform: scale(1.05);
            text-shadow: 0 0 80px rgba(52, 152, 219, 0.6);
        }

        100% {
            transform: scale(1);
            text-shadow: 0 0 50px rgba(52, 152, 219, 0.4);
        }
    }

    h1 {
        font-size: 4rem;
        margin-bottom: 20px;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: 3px;
    }

    p {
        color: #bdc3c7;
        margin-bottom: 50px;
        font-size: 1.6rem;
        font-weight: 300;
    }

    .btn-home {
        background: #3498db;
        color: white;
        padding: 18px 45px;
        text-decoration: none;
        border-radius: 50px;
        font-weight: bold;
        font-size: 1.2rem;
        transition: 0.4s;
        display: inline-flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
    }

    .btn-home:hover {
        background: #2980b9;
        transform: scale(1.1);
        box-shadow: 0 15px 30px rgba(52, 152, 219, 0.4);
    }

    @media (max-width: 768px) {
        .big-text {
            font-size: 8rem;
        }

        h1 {
            font-size: 2.2rem;
        }

        p {
            font-size: 1.1rem;
        }
    }
    </style>
</head>

<body>
    <div class="error-wrapper">
        <div class="big-text">404</div>
        <h1>Úi! Trang không tồn tại</h1>
        <p>Có vẻ như bạn đã đi lạc vào vùng không gian chưa xác định của PT QUIZ.</p>
        <a href="index.php?act=dashboard" class="btn-home">
            <i class="fas fa-home"></i> Quay về trang chủ ngay
        </a>
    </div>
</body>

</html>
