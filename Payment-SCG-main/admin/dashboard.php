<?php include('../admin/include/header.php') ?>


<body>
    <?php
    echo "<script>";
    echo "Swal.fire({title: 'เข้าสู่ระบบสำเร็จ!',text: 'ยินดีต้อนรับสู่ Fair Manpower', icon: 'success', timer: 2500});";
    echo "</script>";
    ?>

    </div>
    </div>

    <!-- js -->
    <?php include('../admin/include/scripts.php') ?>
    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" type="image/ico" href="../favicon.ico">

        <link href="../asset/css/signin.css" rel="stylesheet">

        <script src="../asset/plugins/sweetalert2-11.10.1/jquery-3.7.1.min.js"></script>
        <script src="../asset/plugins/sweetalert2-11.10.1/sweetalert2.all.min.js"></script>

        <title> SCG | Fair Manpower</title>
        <style>
            body,
            html {
                height: 100%;
                margin: 0;
            }

            .bg {
                background-image: url('../src/images/homepage.png');
                height: 100%;
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;


            }

            .transbox {
                background: transparent;
                backdrop-filter: blur(3px);
                height: 100%;


            }

            hr.custom {
                border: 2px solid #d2d4d4;
                border-radius: 5px;
            }

            .card {
                border-radius: 67px;
            }

            .card-body {
                border-radius: 67px;

            }

            div.BoxLogo {
                display: flex;
                justify-content: center;
                align-items: center;
                text-align: center;
                background: transparent;
            }

            img.Logo {
                width: 23%;
                margin-top: 5%;
            }

            h1 {
                src: url('../asset/fonts/Inter/Inter-VariableFont_slnt\,wght.ttf');
                font-size: 38px;
                font-weight: bold;
                color: #fff;
                /* box-shadow: 0px 2px 15px -6px #000000; */

            }

            h2 {
                font-size: 38px;
                font-weight: bold;
                color: #fff;
            }

            h4 {
                color: #fff;
            }

            input[type=submit] {
                width: 80%;
                padding: 14px 20px;
                margin: 8px 0;
                border: none;
                border-radius: 15px;
                border-style: solid;
                border-color: white;
                border-width: 3px;
            }

            .login-button {
                font-size: 25px;
                font-weight: bold;
                transition: 0.5s ease-in-out;
                color: #f0f8ff;
                padding: 4px 50px;
                box-shadow: 0px 2px 15px -6px #000000;
                transition: 0.1s ease-in-out;
                background-image: linear-gradient(#1FBABF, #60D3AA);
            }

            .login-button:hover {
                font-weight: bold;
                color: white;
                transform: scale(1.1);
                transition: 0.25s ease-in-out;
                box-shadow: 0px 2px 15px -8px #000000;
                background-image: linear-gradient(#1FBABF, #60D3AA);

            }

            .center {
                margin: auto;
                width: 50%;
                padding: 10px;
            }

            input:focus {
                border: 2px solid #90d4ce !important;
                box-shadow: 0px 0px 5px rgba(56, 169, 240, 0.75) !important;
            }

            .formcustom {
                display: inline-block;
                border: 2px solid #d2d4d4;
                border-radius: 4px;
                box-sizing: border-box;
            }
        </style>

        <div class="bg">
            <div class="transbox">
                <div class="BoxLogo">
                    <div className="BoxLogoinfo">
                        <img src="https://salmon-charming-stingray-66.mypinata.cloud/ipfs/QmaEmRci8GWNpFGXSXhNVqmNGQwyZ4vRaa4c3fgXAtkufe" class="Logo " />
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-xl-12">
                        <h1 class="text-center">PAYMENT </h1>
                        <h2 class="text-center">คำนวณเงินเดือน </h2>
                    </div>
                </div>
                <div class="row justify-content-center h-50">
                    <div class="col-lg-2 col-md-2 col-sm-2">
                        <div class="form-input-content">
                            <div class="BoxLogo h-100">
                                <div class="dashboard-setting user-notification ">
                                    <div class="dropdown">
                                        <button class="dashboard-payment-employee-btn justify-content-center" onclick="location.href='employee_payment.php'">
                                            <i class="fa-solid fa-users"></i>
                                            <h3 class="text-center ">เงินเดือนพนักงาน</h3>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2">
                        <div class="form-input-content">
                            <div class="BoxLogo h-100">
                                <div class="dashboard-setting user-notification ">
                                    <div class="dropdown">
                                        <button class="dashboard-payment-income-deduct-btn justify-content-center" onclick="location.href='income_deduct_payment.php'">
                                        <i class="fa-solid fa-sack-dollar"></i>
                                            <h3 class="text-center">รายรับ/รายจ่าย</h3>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2">
                        <div class="form-input-content">
                            <div class="BoxLogo h-100">
                                <div class="dashboard-setting user-notification ">
                                    <div class="dropdown">
                                        <button class="dashboard-payment-calculator-btn justify-content-center" onclick="location.href='calculator_payment.php'">
                                        <i class="fa fa-calculator" aria-hidden="true"></i>
                                            <h3 class="text-center">คำนวณเงินเดือน</h3>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2">
                        <div class="form-input-content">
                            <div class="BoxLogo h-100">
                                <div class="dashboard-setting user-notification ">
                                    <div class="dropdown">
                                        <button class="dashboard-payment-status-btn justify-content-center" onclick="location.href='status_payment.php'">
                                        <i class="fa-solid fa-list-check"></i>
                                            <h3 class="text-center ">สถานะเงินเดือน</h3>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2">
                        <div class="form-input-content">
                            <div class="BoxLogo h-100">
                                <div class="dashboard-setting user-notification ">
                                    <div class="dropdown">
                                        <button class="dashboard-payment-setting-btn justify-content-center" onclick="location.href='setting_payment.php'">
                                        <i class="fa-solid fa-gear"></i>
                                            <h3 class="text-center ">การตั้งค่า</h3>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</body>

</html>