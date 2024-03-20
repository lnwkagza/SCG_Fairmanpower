<?php
session_start();
include("../database/connectdb.php");

date_default_timezone_set('Asia/Bangkok');
$time_stamp = date("Y-m-d H:i:s");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cardId = $_POST["employeeid"];
    $absenceTypeId = $_POST['leaveType'];
    $dateStart = $_POST['startDate'];
    $dateEnd = $_POST['endDate']; 
    $timeStart = $_POST['startTime']; 
    $timeEnd = $_POST['endTime'];
    $requestDetail = $_POST['leaveDetails'];
    $approver = $_SESSION['card_id'];

    $sql = "INSERT INTO absence_record (card_id, input_timestamp, absence_type_id, date_start, date_end, time_start, time_end, request_detail, approve_time, approve_status, approver) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?)";
    $params = array($cardId, $time_stamp,$absenceTypeId, $dateStart, $dateEnd, $timeStart, $timeEnd, $requestDetail,$time_stamp,'confirm',$approver);
    $stmt = sqlsrv_query($conn, $sql, $params);


    $querynotify = "SELECT * FROM employee 
                    INNER JOIN login ON employee.card_id = login.card_id
                    WHERE login.card_id = ?";
    $params = array($cardId);
    $stmtnotify = sqlsrv_query($conn, $querynotify, $params);

    $row = sqlsrv_fetch_array($stmtnotify, SQLSRV_FETCH_ASSOC);

    // Prepare LINE Notify message
    $url = 'https://notify-api.line.me/api/notify';
    
    $headers = array(
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Bearer ' . $row['line_token']
    );

    $message = "เรียนคุณ " . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . ' หัวหน้าของท่านได้กำหนดวันลาของท่านเมื่อเวลา ' . $time_stamp . " ดูเพิ่มเติม: https://liff.line.me/2003823996-Q0DXqyOZ";

    // Use cURL to send the notification
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'message=' . urlencode($message));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo '<script>
                window.location.href = "../leave/leave-approve-head.php";
              </script>';
    }
}
?>