<?php
session_start();
include("../database/connectdb.php");

// Sanitize and validate input ID
$id = isset($_GET['id']) ? $_GET['id'] : '';
$id = preg_replace('/[^a-zA-Z0-9]/', '', $id);
$id = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');

date_default_timezone_set('Asia/Bangkok');
$time_stamp = date("Y-m-d H:i:s");

// Prepare and execute SQL query to update day-off request status to 'reject'
$query = "UPDATE day_off_request 
          SET approve_status = ?, approver = ?, approve_time = ? 
          WHERE day_off_req_id = ?";
$addvalues = array('reject', $_SESSION["card_id"], $time_stamp, $id);
$stmt = sqlsrv_prepare($conn, $query, $addvalues);
$result = sqlsrv_execute($stmt);

// Check for SQL query execution errors
if ($result === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Retrieve employee details for notification
$querynotify = "SELECT * FROM day_off_request 
                INNER JOIN login ON day_off_request.card_id = login.card_id
                RIGHT JOIN employee ON login.card_id = employee.card_id
                WHERE day_off_req_id = ?";
$params = array($id);
$stmtnotify = sqlsrv_query($conn, $querynotify, $params);

// Check for notification query errors
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
$message = 'ถูกปฏิเสธ'."\n".'การขออนุมัติขอสลับวันหยุดและการทำงาน'."\n".'ของคุณ ' . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . "\n".' เมื่อเวลา ' . $time_stamp . ' ดูเพิ่มเติม: https://liff.line.me/2003823996-Q0DXqyOZ';

// Use cURL to send the notification
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'message=' . urlencode($message));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Redirect after successful rejection and notification
echo '<script>
        window.location.href = "../dayoff/day-off-head.php";
      </script>';
?>
