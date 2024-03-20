<?php
session_start();
include("../database/connectdb.php");

date_default_timezone_set('Asia/Bangkok');
$time_stamp = date("Y-m-d H:i:s");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $cardId = $_SESSION["card_id"];
    $work = $_POST['work'];
    $dateStart = $_POST['startDate'];
    $dateEnd = $_POST['endDate'];
    $detail = $_POST['detail']; 
    $employeeid = $_POST['employeeid'];

    // Retrieve work format code for the specified employee
    $sql_work_format_code  = "SELECT work_format_code FROM employee WHERE card_id = ?";
    $params_work_format_code  = array($employeeid);
    $query_work_format_code  = sqlsrv_query($conn, $sql_work_format_code, $params_work_format_code);
    $row_work_format_code  = sqlsrv_fetch_array($query_work_format_code, SQLSRV_FETCH_ASSOC);
    $work_format_code = $row_work_format_code["work_format_code"];

    // Prepare and execute SQL query to insert day-off request
    $insertSql = "INSERT INTO day_off_request (input_timestamp, card_id, edit_time, request_card_id, old_work_format_code, edit_work_format_code, edit_detail, approve_status, approver, date_start, date_end) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insertParams = array($time_stamp, $employeeid, $time_stamp, $cardId, $work_format_code, $work, $detail, 'confirm', $cardId, $dateStart, $dateEnd);
    $insertStmt = sqlsrv_query($conn, $insertSql, $insertParams);


    // Retrieve employee details for notification
    $querynotify = "SELECT * FROM employee 
                    INNER JOIN login ON employee.card_id = login.card_id
                    WHERE login.card_id = ?";
    $params = array($employeeid);
    $stmtnotify = sqlsrv_query($conn, $querynotify, $params);

    $row = sqlsrv_fetch_array($stmtnotify, SQLSRV_FETCH_ASSOC);

    // Prepare LINE Notify message
    $url = 'https://notify-api.line.me/api/notify';
    
    $headers = array(
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Bearer ' . $row['line_token']
    );

    $message = "เรียนคุณ " . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . ' หัวหน้าของท่านได้ทำเปลี่ยนรูปแบบวันหยุดและการทำงานของท่านเมื่อเวลา ' . $time_stamp . " ดูเพิ่มเติม: https://liff.line.me/2003823996-Q0DXqyOZ";

    // Use cURL to send the notification
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'message=' . urlencode($message));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Redirect after successful insertion and notification
    echo '<script>
            window.location.href = "../dayoff/day-off-change-history-head.php";
          </script>';
}
?>
