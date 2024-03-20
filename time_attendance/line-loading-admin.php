<?php
include("database/connectdb.php");
session_start();

// Sanitize and validate input
$line_id = isset($_GET['w1']) ? $_GET['w1'] : '';
$line_id = preg_replace('/[^a-zA-Z0-9]/', '', $line_id);
$line_id = htmlspecialchars($line_id, ENT_QUOTES, 'UTF-8');
$_SESSION["line_id"] = $line_id;

?>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    var myVar;
    function myFunction() {
        myVar = setTimeout(showPage, 3000);
    }
    function showPage() {
        window.location.href = 'check_admin.php';
    }
    // Make sure to call myFunction to initiate the timeout.
    myFunction();
</script>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="assets/css/loader-login.css">
    <!-- font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300&family=IBM+Plex+Sans+Thai&family=Inter:wght@200&family=Itim&family=Noto+Sans+Thai&family=Prompt:wght@200;300;400&display=swap"
        rel="stylesheet">
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0,viewport-fit=cover" />
    <title>กำลังโหลด</title>
</head>

<body id="body">

    <div class="container">
        <img src="IMG/scg.png" alt="" style="width: 25%; margin-left: -70%;">
        <hr>
        <div class="imgLogo">
            <img src="IMG/logoFM.png" alt="">
        </div>
        <div class="text-detail">
            <span style="font-size: 18px; color: #6b6b6b;">ระบบจัดการเวลา</span>
        </div>
        <div class="btn-login">
            <section id="button">
                <div id="loader"></div>
            </section>
        </div>

    </div>
</body>

</html>