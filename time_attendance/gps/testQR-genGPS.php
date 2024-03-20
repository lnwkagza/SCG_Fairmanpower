<?php
date_default_timezone_set('Asia/Bangkok');
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

</head>

<body>
    <div id="qrcode">เข้ารหัส</div>
    <br>
    <div id="decode">ถอดรหัส</div>


    <script>
        function encryptGPS(coords, key) {
            const encryptedMessageGPS = CryptoJS.AES.encrypt(coords, key).toString();
            return encryptedMessageGPS;
        }

        function decryptGPS(token, key) {
            const decryptedMessageGPS = CryptoJS.AES.decrypt(token, key).toString(CryptoJS.enc.Utf8);
            return decryptedMessageGPS;
        }

        var messes = '8.102126,99.675998,1000';
        var key = 'SCG_Thungsong_thai';

        let payload = encryptGPS(messes, secretKey)
        let decodePayload = decryptGPS(payload, secretKey)

        $(document).ready(function() {

            let dataToEncode = 'U2FsdGVkX1+4W6Lo6KMPeUhiUYLLy35fjqYCAK7sbYHGTEky58Xp0ldR4OqasSfd';

            $('#decode').html(decodePayload);

            let qrcode = new QRCode(document.getElementById("qrcode"), {
                text: payload,
                width: 256,
                height: 256
            });

        });
    </script>



    <!-- 8.102126,99.675998,1000 -->
</body>

</html>