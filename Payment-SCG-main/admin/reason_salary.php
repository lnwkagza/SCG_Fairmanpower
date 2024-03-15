<?php
require_once('..\..\config\connection.php');

session_start(); // เริ่ม session

if (isset($_SESSION['splitId'])) {
    $splitId = $_SESSION['splitId']; // เก็บค่า session ในตัวแปร
    // นำค่าตัวแปร $splitId ไปใช้งานตามต้องการ
} else {
    echo "ไม่พบค่า splitId ที่ถูกตั้งไว้ใน session";
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8">
    <title>SCG | Fair Manpower</title>
    <!-- Site favicon -->
    <link rel="icon" type="image/ico" href="../favicon.ico">

    <style>
        .container {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* ความสูงของ container เท่ากับความสูงของหน้าจอ */
            background-image: url('../src/images/homepage.png');
            background-size: cover; /* ภาพพื้นหลังขยายเต็มขนาดของ container */
            background-position: center; /* จัดตำแหน่งภาพพื้นหลังตรงกลาง */
            background-repeat: no-repeat; /* ไม่ให้ภาพพื้นหลังทำซ้ำ */
        }

        .signup-box {
            width: 400px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 15px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.125);
            background: white;
        }

        .signup-box label {
            margin-top: 0;
            margin-bottom: 40px;
            text-align: center;
            font-size: 30px;
        }

        .signup-box form {
            display: flex;
            flex-direction: column;
        }

        .signup-box input {
            margin-top: 10px;
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 15px;
        }

        .signup-box input[type="submit"] {
            background-color: #000;
            color: #fff;
            cursor: pointer;
        }

        .signup-box input[type="submit"]:hover {
            background-color: #333;
        }

        .button-37 {
            background-color: #13aa52;
            border: 1px solid #13aa52;
            border-radius: 4px;
            box-shadow: rgba(0, 0, 0, .1) 0 2px 4px 0;
            box-sizing: border-box;
            color: #fff;
            cursor: pointer;
            font-family: "Akzidenz Grotesk BQ Medium", -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 16px;
            font-weight: bold;
            outline: none;
            outline: 0;
            padding: 10px 25px;
            text-align: center;
            transform: translateY(0);
            transition: transform 150ms, box-shadow 150ms;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
        }

        .button-37:hover {
            box-shadow: rgba(0, 0, 0, .15) 0 3px 9px 0;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center main">
        <div class="signup-box">
            <label>เหตุผลการคำนวณ</label>
            <form action="insert_reason_salary.php" method="POST">
                <input type="text" placeholder="กรุณากรอกเหตุผล" name="reason_salary" autocomplete="off" required>
                <button class="button-37" type="submit">บันทึก</button>
            </form>
        </div>
    </div>
</body>
</html>
