<?php
session_start();
include("../database/connectdb.php");

$id = isset($_GET['id']) ? $_GET['id'] : '';
$id = preg_replace('/[^a-zA-Z0-9]/', '', $id);
$id = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');

date_default_timezone_set('Asia/Bangkok');
$time_stamp = date("Y-m-d H:i:s");

// echo "SCG Employee ID: " . $_SESSION["card_id"] . "<br>";

$query = "UPDATE check_inout 
SET approve_status = ?, approver = ?, approve_time = ? 
WHERE check_inout_id = ?";
$addvalues = array('reject', $_SESSION["card_id"], $time_stamp, $id);
$stmt = sqlsrv_prepare($conn, $query, $addvalues);
$result = sqlsrv_execute($stmt);

if ($result === false) {
    die(print_r(sqlsrv_errors(), true));
}

$querynotify = "SELECT * FROM check_inout 
INNER JOIN login ON check_inout.card_id = login.card_id
RIGHT JOIN employee ON login.card_id = employee.card_id
WHERE check_inout_id = ?";
$params = array($id);
$stmtnotify = sqlsrv_query($conn, $querynotify, $params);

if ($stmtnotify === false) {
    die(print_r(sqlsrv_errors(), true));
}

$row = sqlsrv_fetch_array($stmtnotify, SQLSRV_FETCH_ASSOC);

$url = 'https://notify-api.line.me/api/notify';
$headers = array(
    'Content-Type: application/x-www-form-urlencoded',
    'Authorization: Bearer ' . $row['line_token']
);
$message = "รายการขอแก้เวลา check in ของคุณ " . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . ' ได้รับการปฎิเสธ เมื่อเวลา ' . $time_stamp . " ดูเพิ่มเติม: https://liff.line.me/2003823996-Q0DXqyOZ";

// Use the curl library to send the notification
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'message=' . urlencode($message));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo '<script>
                window.location.href = "../views/approval-head.php";
              </script>';

?>
