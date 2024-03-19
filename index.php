<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/ico" href="favicon.ico">

    <link href="vendors/styles/signin.css" rel="stylesheet" />


    <script src="/asset/plugins/sweetalert2-11.10.1/jquery-3.7.1.min.js"></script>
    <script src="/asset/plugins/sweetalert2-11.10.1/sweetalert2.all.min.js"></script>


    <!-- LINE SCRIPT -->
    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>


    	<!-- Chagan Font -->
	<link href="https://fonts.googleapis.com/css2?family=Chakra+Petch&family=Inter:wght@600&family=Noto+Sans+Thai:wght@500&display=swap" rel="stylesheet">

    <title> SCG | Fair Manpower</title>
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
        }

        .flex {
            display: flex;
        }

        .bg {
            background-image: url("asset/img/bg.png");
            height: 100%;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;

        }

        .transbox {
            background: transparent;
            backdrop-filter: blur(5px);
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
            width: 48%;
        }

        h1 {
            src: url('/asset/fonts/Inter/Inter-VariableFont_slnt\,wght.ttf');
            font-size: 38px;
            font-weight: bold;
            color: #ed2626;
            text-shadow: 2px 2px #c9c7c7;

        }

        h2 {
            font-size: 28px;
            font-weight: bold;
            color: #293d52;

        }

        .admin-color {
            color: #ED2626;
        }

        .employee-color {
            color: #9C9D9D;
        }

        h4 {
            color: #b8b2b2;
        }

        input[type=submit] {
            width: 100%;
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
            color: #C6EBD5;
            box-shadow: 0px 2px 15px -6px #000000;
            transition: 0.1s ease-in-out;
            background: linear-gradient(#06C755, #12B153);

        }

        .login-button:hover {
            font-weight: bold;
            color: white;
            transform: scale(1.05);
            transition: 0.25s ease-in-out;
            box-shadow: 0px 2px 15px -5px #000000;

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

        .user-icon {
            width: 80px;
            height: 80px;
            background-color: #d9d9d9;
            border-radius: 100%;
            box-shadow: 0 0 10px rgba(0, 0, 0, .18)
        }

        .border-radius-100 {
            border: 3px solid #ffffff !important;
            box-shadow: 0px 0px 5px rgba(48, 50, 51, 0.382) !important;
            border-radius: 100%
        }
    </style>

</head>

<body>
    <div class="bg" style="height: 100%">
        <div class="transbox">
            <div class="login-form-bg h-100">
                <div class="container h-100">
                    <div class="row justify-content-center h-100">
                        <div class="col-xl-6">

                            <div class="form-input-content">
                                <div class="card login-form mb-0">
                                    <div class="card-body pt-10 shadow">
                                        <div class="row pl-2">
                                            <div class="user-icon ">
                                                <img src="asset\img\workers.png" class="border-radius-100" width="80" height="80" alt="" style="box-shadow: 0 0 10px rgba(0, 0, 0, .18);">
                                            </div>
                                        </div>
                                        <div class="BoxLogo">
                                            <div className="BoxLogoinfo">
                                                <img src="asset\img\SCGlogo.png" class="Logo " />
                                            </div>
                                        </div>
                                        <div>
                                            <h1 class="text-center">Fair Manpower</h1>
                                            <h2 class="text-center">ระบบโครงสร้างองค์กรและข้อมูลประวัติ</h2>

                                        </div>

                                        <hr class="custom">

                                        <div class="form-group">
                                            <!-- <input  type="submit" value="เข้าสู่ระบบด้วย LINE" class="login-button " name="signin" onclick="location.href='dashboard.php'"> -->
                                            <input id="btnLogIn" type="submit" value="เข้าสู่ระบบด้วย LINE" class="login-button">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var userid = "";
        const btnLogIn = document.getElementById('btnLogIn');
        const btnLogOut = document.getElementById('btnLogOut');
        const userId = document.getElementById('userId');

        async function main() {
            // Initialize LIFF app)
            await liff.init({
                liffId: '2002037246-LQ8EE7E7'
            });

            getUserProfile();
            if (!liff.isInClient()) {
                if (liff.isLoggedIn()) {
                    btnLogIn.style.display = 'none';
                    getUserProfile();
                } else {
                    btnLogIn.style.display = 'block';
                }
            }
        }

        main();

        async function getUserProfile() {
            const profile = await liff.getProfile();
            window.location.href = "Auth_Line.php?w1=" + profile.userId

        }

        btnLogIn.onclick = () => {
            liff.login();
        };
    </script>

</body>

</html>