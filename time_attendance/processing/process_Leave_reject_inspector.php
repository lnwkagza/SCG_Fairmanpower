<?php
session_start();
include("../database/connectdb.php");

// Validate and sanitize input ID
$id = isset($_GET['id']) ? $_GET['id'] : '';
$id = trim($id);
$id = preg_replace('/[^a-zA-Z0-9]/', '', $id);
$id = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');

date_default_timezone_set('Asia/Bangkok');
$time_stamp = date("Y-m-d H:i:s");

// Check if card_id is set in session and sanitize it
$card_id = isset($_SESSION["card_id"]) ? $_SESSION["card_id"] : '';
$card_id = htmlspecialchars($card_id, ENT_QUOTES, 'UTF-8');

// Prepare and execute update query
$query = "UPDATE absence_record 
SET inspector_status = ?, inspector = ?, inspector_time = ? 
WHERE absence_record_id = ?";
$addvalues = array('reject', $card_id, $time_stamp, $id);
$stmt = sqlsrv_prepare($conn, $query, $addvalues);
$result = sqlsrv_execute($stmt);

// Check for query execution errors
if ($result === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Fetch absence record details for notification
$querynotify = "SELECT * FROM absence_record 
                INNER JOIN login ON absence_record.card_id = login.card_id
                RIGHT JOIN employee ON login.card_id = employee.card_id
                WHERE absence_record_id = ?";
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
$message = "รายการขอลาของคุณ " . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . ' ได้รับการปฎิเสธจากผู้ตรวจสอบ เมื่อเวลา ' . $time_stamp . " ดูเพิ่มเติม: https://liff.line.me/2003823996-Q0DXqyOZ";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'message=' . urlencode($message));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo '<script>
        alert("คุณได้ทำการปฏิเสธรายการลา");
        window.location.href = "../leave/leave-approve-head.php";
      </script>';
?>
