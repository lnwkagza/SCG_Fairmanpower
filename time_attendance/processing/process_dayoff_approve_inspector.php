<?php
session_start();
include("../database/connectdb.php");

$id = isset($_GET['id']) ? $_GET['id'] : '';
$id = trim($id); // Add trim to remove leading and trailing whitespaces
$id = preg_replace('/[^a-zA-Z0-9]/', '', $id);
$id = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');

date_default_timezone_set('Asia/Bangkok');
$time_stamp = date("Y-m-d H:i:s");

$query = "UPDATE day_off_request 
SET inspector_status = ?, inspector_time = ? 
WHERE day_off_req_id = ?";
$addvalues = array('confirm', $time_stamp, $id);
$stmt = sqlsrv_prepare($conn, $query, $addvalues);
// $result = sqlsrv_execute($stmt);
if (sqlsrv_execute($stmt) === false) {
    die(print_r(sqlsrv_errors(), true));
}

$querynotify = "SELECT * FROM day_off_request 
INNER JOIN login ON day_off_request.approver = login.card_id
RIGHT JOIN employee ON login.card_id = employee.card_id
WHERE day_off_req_id = ?";
$params = array($id);
$stmtnotify = sqlsrv_prepare($conn, $querynotify, $params);

if (sqlsrv_execute($stmtnotify) === false) {
    die(print_r(sqlsrv_errors(), true));
}

$row = sqlsrv_fetch_array($stmtnotify, SQLSRV_FETCH_ASSOC);

// Check if the query returned a valid result
    
    $url = 'https://notify-api.line.me/api/notify';
    
    $headers = array(
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Bearer ' . $row['line_token']
    );
    $message = 'โปรดอนุมัติ' . "\n" . 'การขอสลับวันหยุด' . "\n" . 'ของคุณ ' . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . "\n" . 'เมื่อเวลา ' . $time_stamp . "\n" . 'ดูเพิ่มเติม: https://liff.line.me/2003823996-Q0DXqyOZ';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'message=' . urlencode($message));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);


echo '<script>
                    window.location.href = "../dayoff/day-off-head.php";
                  </script>';
?>
