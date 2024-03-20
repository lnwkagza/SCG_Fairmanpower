<?php
session_start();
include("../database/connectdb.php");

// Assuming you have a database connection object named $conn

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize inputs
    $Id = $_POST["id"]; // Assuming id is sanitized elsewhere
    $dateStart = htmlspecialchars($_POST['startDate']);
    $dateEnd = htmlspecialchars($_POST['endDate']);
    $timeStart = htmlspecialchars($_POST['startTime']);
    $timeEnd = htmlspecialchars($_POST['endTime']);
    $requestDetail = htmlspecialchars($_POST['request_detail']);

    // Construct the SQL query for UPDATE
    $sql = "UPDATE absence_record 
            SET date_start = ?, date_end = ?, time_start = ?, time_end = ?, request_detail = ?, approve_status = ?
            WHERE absence_record_id = ?";

    // Prepare and execute the SQL statement
    $params = array($dateStart, $dateEnd, $timeStart, $timeEnd, $requestDetail, 'waiting', $Id);
    $stmt = sqlsrv_query($conn, $sql, $params);  

    // Check for SQL errors
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Fetch user data for notification
    $querynotify = "SELECT * FROM absence_record 
                    INNER JOIN login ON absence_record.card_id = login.card_id
                    RIGHT JOIN employee ON login.card_id = employee.card_id
                    WHERE absence_record_id = ?";
    $params = array($Id);
    $stmtnotify = sqlsrv_query($conn, $querynotify, $params);

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
    $message = "รายการขอลาของคุณ " . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . ' ได้รับการแก้ไข เมื่อเวลา ' . $time_stamp . " ดูเพิ่มเติม: https://liff.line.me/2003823996-Q0DXqyOZ";

    // Use curl to send the notification
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'message=' . urlencode($message));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Redirect to leave history page after processing
    header("Location: ../leave/leave-history-employee.php");
    exit(); // Ensure script execution stops after redirection
}

// Include your HTML or redirect to another page after processing the form
?>
