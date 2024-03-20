<?php
include("database/connectdb.php"); // Assuming this file contains the database connection code
include('includes/header.php');
// Ensure that the connection is established successfully before proceeding

$line_id = $_GET['w1'];
$card_id = $_POST['card_id'];

if (isset($_POST["login"])) {
    // Use prepared statements to prevent SQL injection
    $query = "UPDATE login SET line_id = ? WHERE card_id = ?";

    $params = array(&$line_id, &$card_id);
    $stmt = sqlsrv_prepare($conn, $query, $params);

    if ($stmt === false) {
        // Handle the SQL preparation error
        die(print_r(sqlsrv_errors(), true));
    }

    // Execute the prepared statement
    $result = sqlsrv_execute($stmt);

    if ($result === false) {
        // Handle the SQL execution error
        die(print_r(sqlsrv_errors(), true));
    }

    // Check the number of rows affected by the update
    $rowsAffected = sqlsrv_rows_affected($stmt);

    if ($rowsAffected > 0) {
        // Successful update, redirect to index.html
        header("Location: index.html");
        exit;
    } else {
        // No rows affected, redirect to line/lineToken.php
        header("Location: line/lineToken.php");
        exit;
    }
}

// Handle other cases or provide a default behavior if needed

// Start the session
session_start();
// Set session variables
// $_SESSION["card_id"] = $card_id;


?>

<!DOCTYPE html>

<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <link rel="stylesheet" href="assets/css/register.css">
    <title>สร้างบัญชี</title>
</head>

<body>
    <div class="container">
        <img src="IMG/scg.png" alt="" style="width: 25%; margin-left: -70%;">
        <hr style="width: 100%; border: 1px solid #B0B2B3;">
        <div class="imgLogo">
            <img src="IMG/logoFM.png" alt="">
        </div>

        <div class="box-confirm">
            <span>ยืนยันตัวตนอีกครั้ง</span>
            <form method="POST" class="register">
                <input type="text" name="card_id" placeholder="รหัสพนักงาน 0150-xxxxxx" />
                <a href="index.php"><button type="submit" name="login">ยืนยันการใช้งาน</button></a>
            </form>
        </div>

    </div>

</body>

</html>
<?php
sqlsrv_close($conn);
?>