
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>📦 POS Packing Scanner</title>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: 100%;
            min-height: 100vh;
            font-family: 'Prompt', sans-serif;
            background-image: linear-gradient(to left bottom, #474bff, #5255fc, #5d5ef9, #6666f5, #6f6ff1);
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: cover;
            color: white;

            /* ทำให้ body เป็น flex container เพื่อจัดกึ่งกลาง */
            display: flex;
            justify-content: center;
            /* แนวนอนกึ่งกลาง */
            align-items: center;
            /* แนวตั้งกึ่งกลาง */
            flex-direction: column;
        }

        h1 {
            font-weight: 600;
            font-size: 3rem;
        }

        /* ซ่อน container และ bottom-nav เพื่อไม่รบกวนตำแหน่ง */
        .container,
        .bottom-nav {
            display: none;
        }

        #welcome-text {
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="category-bar">
            <div class="category-item">
                <img src="https://img.icons8.com/color/96/barcode.png" />
                <div>Scan</div>
            </div>
            <div class="category-item">
                <img src="https://img.icons8.com/color/96/open-box.png" />
                <div>Packed</div>
            </div>
            <div class="category-item">
                <img src="https://img.icons8.com/color/96/delivery.png" />
                <div>Shipping</div>
            </div>
            <div class="category-item">
                <img src="https://img.icons8.com/color/96/settings--v1.png" />
                <div>Setting</div>
            </div>
        </div>
    </div>


    <a href="importdb.php" id="welcome-link" style="color: white; text-decoration: none;">
        <h1 id="welcome-text">Welcome</h1>
        <h4 id="welcome-text">Scan Picking Card</h4>
    </a>
    <div class="bottom-nav">
        <button class="nav-btn" data-link="test.php">📚</button>
        <button class="nav-btn" data-link="test.php">🏠</button>
        <button class="nav-btn" data-link="test.php">📦</button>
        <button class="nav-btn" data-link="test.php">📤</button>
    </div>
    <script>
        const link = document.getElementById('welcome-link');
        const text = document.getElementById('welcome-text');

        link.addEventListener('click', function (event) {
            event.preventDefault(); // ป้องกันการลิงก์ไปทันที

            // ใส่คลาสอนิเมชัน
            text.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
            text.style.transform = 'scale(1.2)';
            text.style.opacity = '0';

            // รอ 500ms แล้วไปหน้าใหม่
            setTimeout(() => {
                window.location.href = link.href;
            }, 500);
        });
    </script>
</body>

</html>