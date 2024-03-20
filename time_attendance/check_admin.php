<?php
// include("../database/connectdb.php");

include("database/connectdb.php");

session_start();

$line_id = isset($_GET['w1']) ? $_GET['w1'] : '';
$line_id = preg_replace('/[^a-zA-Z0-9]/', '', $line_id);
$line_id = htmlspecialchars($line_id, ENT_QUOTES, 'UTF-8');
$_SESSION["line_id"] = $line_id;

checkLogin($line_id);

// Prepare and execute SQL query
$query = "SELECT * FROM login INNER JOIN employee ON login.card_id = employee.card_id WHERE line_id = ?";
$params = array($line_id);
$sql = sqlsrv_prepare($conn, $query, $params);

if (!$sql || !sqlsrv_execute($sql)) {
    die(print_r(sqlsrv_errors(), true));
}

// Fetch user data
$userData = sqlsrv_fetch_object($sql);

// Set session variables and redirect based on permission
if ($userData) {
    $_SESSION["line_id"] = $line_id;
    $_SESSION["card_id"] = $userData->card_id;
    $_SESSION["permission_id"] = $userData->permission_id;

    // Redirect based on permission directly
    switch ($_SESSION["permission_id"]) {
        case '1':
            header("Location: report/report-admin.php");
            exit();
        default:
            header("Location: permission.html");
            break;
    }
}

function checkLogin($line_id)
{
    global $conn;

    // Check if the provided line_id exists in the login table
    $query = "SELECT * FROM login WHERE line_id = ?";
    $params = array($line_id);

    $sql = sqlsrv_query($conn, $query, $params);

    if ($sql === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $userData = sqlsrv_fetch_object($sql);

    if (!$userData) {
        $_SESSION["login_error"] = "Invalid login. Please register.";
        header("Location: register.php?w1={$line_id}");
        exit();
    }
}