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
SET approve_status = ?, approve_time = ? 
WHERE day_off_req_id = ?";
$addvalues = array('confirm', $time_stamp, $id);
$stmt = sqlsrv_prepare($conn, $query, $addvalues);

// Check if the SQL statement is prepared successfully
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Execute the prepared statement
if (sqlsrv_execute($stmt) === false) {
    die(print_r(sqlsrv_errors(), true));
}

$querynotify = "SELECT * FROM day_off_request 
INNER JOIN login ON day_off_request.card_id = login.card_id
RIGHT JOIN employee ON login.card_id = employee.card_id
WHERE day_off_req_id = ?";
$params = array($id);
$stmtnotify = sqlsrv_prepare($conn, $querynotify, $params);

// Execute the prepared statement for notification query
if (sqlsrv_execute($stmtnotify) === false) {
    die(print_r(sqlsrv_errors(), true));
}

$row = sqlsrv_fetch_array($stmtnotify, SQLSRV_FETCH_ASSOC);

// Check if a valid record is found
if ($row) {
    $work = $row['day_off_request.edit_work_format_code']; // replace 'your_work_format_code' with the actual value
    $query2 = "UPDATE employee 
        SET work_format_code = ? 
        WHERE card_id = ?";
    $addvalues2 = array($work, $row['card_id']);
    $stmt2 = sqlsrv_prepare($conn, $query2, $addvalues2);

    // Execute the prepared statement for updating employee work format code
    $result2 = sqlsrv_execute($stmt2);

    $url = 'https://notify-api.line.me/api/notify';
    
    $headers = array(
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Bearer ' . $row['line_token']
    );
    $message = "รายการขอสลับวันหยุดและการทำงานของคุณ " . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . ' ได้รับการอนุมัติ เมื่อเวลา ' . $time_stamp . " ดูเพิ่มเติม: https://liff.line.me/2003823996-Q0DXqyOZ";

    // Use cURL to send the notification
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'message=' . urlencode($message));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
} else {
    // Handle the case where no record is found
    echo "No record found for the given day_off_req_id.";
}

// Redirect after processing
echo '<script>
    window.location.href = "../dayoff/day-off-head.php";
</script>';
?>
