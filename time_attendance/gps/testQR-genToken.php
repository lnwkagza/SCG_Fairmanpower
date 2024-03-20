<?php
date_default_timezone_set('Asia/Bangkok');
// include('../includes/header.php');
$time_now = date("H:i");
$date_now = date("Y-m-d");
require('../check-in/check-inout-query.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Encoding</title>
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="../assets/script/crypto-js.js"></script>


    <style>
        #progress-bar-container {
            width: 100%;
            height: 20px;
            background-color: #eee;
            margin-top: 10px;
            overflow: hidden;
        }

        #progress-bar {
            height: 100%;
            width: 100%;
            background-color: #4caf50;
        }

        #countdown {
            font-size: 18px;
            text-align: center;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div id="qrcode"></div>
    <div id="progress-bar-container">
        <div id="progress-bar"></div>
    </div>
    <div id="countdown"></div>

    <script>
        $(document).ready(function() {

            const progressBar = $("#progress-bar");
            const countdown = $("#countdown");
            const progressBarContainer = $("#progress-bar-container");

            function encryptGPS(coords, key) {
                const encryptedMessageGPS = CryptoJS.AES.encrypt(coords, key).toString();
                return encryptedMessageGPS;
            }

            function decryptGPS(token, key) {
                const decryptedMessageGPS = CryptoJS.AES.decrypt(token, key).toString(CryptoJS.enc.Utf8);
                return decryptedMessageGPS;
            }

            function generateUniqueToken(length) {
                const array = new Uint8Array(length / 2);
                crypto.getRandomValues(array);
                return Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('');
            }

            function updateQRCode() {
                let dataToEncode = generateUniqueToken(32);


                // ลบ QR Code เดิม
                $("#qrcode").empty();

                // สร้าง QR Code ใหม่
                let qrcode = new QRCode(document.getElementById("qrcode"), {
                    text: dataToEncode,
                    width: 256,
                    height: 256
                });

                return dataToEncode;
            }

            // ตั้งค่าให้รีโหลดหน้าทุก ๆ 10 วินาที
            setInterval(function() {

                let progress = 100;
                progressBar.width(progress + "%");

                // ตั้งค่าให้ progress bar เริ่มนับถอยหลัง
                const interval = setInterval(function() {
                    progress -= 10;
                    progressBar.width(progress + "%");

                    // แสดงเวลาถอยหลัง
                    countdown.text(`เหลือเวลาอีก : ${progress / 10} วินาที`);

                    // เมื่อ progress bar ลดลงเหลือ 0%
                    if (progress <= 0) {

                        clearInterval(interval);
                        let token = updateQRCode();

                        console.info(`Token : ${token}`);
                        progress = 100;
                    }
                }, 1000); // ระยะเวลาในการลด progress bar ทุก 1000 มิลลิวินาที (1 วินาที)
            }, 10000); // รีโหลดทุก 10 วินาที

        });
    </script>
</body>

</html>