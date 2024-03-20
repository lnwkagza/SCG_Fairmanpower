<?php
session_start();
include("../database/connectdb.php");

// Assuming you have a database connection object named $conn

$id = isset($_GET['id']) ? intval($_GET['id']) : 0; // Ensure $id is an integer
if ($id <= 0) {
    // Handle invalid or missing ID, perhaps redirect to an error page
    exit("Invalid ID");
}

// SQL query to delete the record
$sql = "DELETE FROM absence_record WHERE absence_record_id = ?";
// Prepare and execute the query
$params = array($id);
$stmt = sqlsrv_query($conn, $sql, $params);

// Check for errors in the SQL query
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Fetch user data for notification
$querynotify = "SELECT * FROM employee 
                INNER JOIN login ON employee.card_id = login.card_id
                WHERE employee.card_id = ?";
$params = array($_SESSION['card_id']);
$stmtnotify = sqlsrv_query($conn, $querynotify, $params);

// Check for errors in the notification query
if ($stmtnotify === false) {
    die(print_r(sqlsrv_errors(), true));
}

$row = sqlsrv_fetch_array($stmtnotify, SQLSRV_FETCH_ASSOC);

// Prepare LINE Notify message
$url = 'https://notify-api.line.me/api/notify';
$headers = array(
    'Content-Type: application/x-www-form-urlencoded',
    'Authorization: Bearer ' . $row['line_token']
);
$time_stamp = date('Y-m-d H:i:s'); // Assuming you want the current timestamp
$message = "รายการขอลาของคุณ " . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . ' ได้รับการยกเลิก เมื่อเวลา ' . $time_stamp . " ดูเพิ่มเติม: https://liff.line.me/2003823996-Q0DXqyOZ";

// Use curl to send the notification
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'message=' . urlencode($message));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Display success message and redirect
header("Location: ../leave/leave-history-employee.php");
?>
