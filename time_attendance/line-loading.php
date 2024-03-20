<?php
include("database/connectdb.php");
session_start();

// Sanitize and validate input
$line_id = isset($_GET['w1']) ? $_GET['w1'] : '';
$line_id = preg_replace('/[^a-zA-Z0-9]/', '', $line_id);
$line_id = htmlspecialchars($line_id, ENT_QUOTES, 'UTF-8');
$_SESSION["line_id"] = $line_id;

// Use the correct variable in the SQL query
$sql = "SELECT card_id FROM login WHERE line_id = ?";
$params = array($line_id); // Correct variable name
$stmt = sqlsrv_prepare($conn, $sql, $params);

// Execute the prepared statement and fetch the result
if (sqlsrv_execute($stmt)) {
    if (sqlsrv_fetch($stmt)) {
        $card_id = sqlsrv_get_field($stmt, 0);
    } else {
        // No user data found, redirect to register page
        $_SESSION["login_error"] = "Invalid login. Please register.";
        header("Location: register.php?w1={$line_id}");  
}
}

?>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
// Wait for the document to be ready
$(document).ready(function() {
    // Make an AJAX request after the page is loaded
    $.ajax({
        type: 'GET',
        url: 'processing/update_shift_day.php',
        data: {
            'id': '<?php echo $card_id; ?>'
        },
        success: function(response) {
            // Redirect after the update_shift process is complete
            setTimeout(function() {
                window.location.href = 'check_login.php';
            }, 1000); // 3 seconds delay (adjust as needed)
        },
        error: function(error) {
            console.error('Error during update_shift:', error);
        }
    });
});
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