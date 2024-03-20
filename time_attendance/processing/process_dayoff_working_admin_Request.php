<?php
session_start();
include("../database/connectdb.php");

date_default_timezone_set('Asia/Bangkok');
$time_stamp = date("Y-m-d H:i:s");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cardId = $_SESSION["card_id"];
    $work = $_POST['work'];
    $dateStart = $_POST['startDate'];
    $dateEnd = $_POST['endDate'];
    $detail = $_POST['detail'];
    $inspector = !empty($_POST['inspector']) ? $_POST['inspector'] : null;

    $sql_manager  = "SELECT manager_card_id FROM manager WHERE card_id = ?";
    $params_manager  = array($cardId);
    $query_manager  = sqlsrv_query($conn, $sql_manager, $params_manager);
    $row_manager  = sqlsrv_fetch_array($query_manager, SQLSRV_FETCH_ASSOC);
    $manager_id = $row_manager["manager_card_id"];

    $sql_work_format_code  = "SELECT work_format_code FROM employee WHERE card_id = ?";
    $params_work_format_code  = array($cardId);
    $query_work_format_code  = sqlsrv_query($conn, $sql_work_format_code, $params_work_format_code);
    $row_work_format_code  = sqlsrv_fetch_array($query_work_format_code, SQLSRV_FETCH_ASSOC);
    $work_format_code = $row_work_format_code["work_format_code"];

    $approve_status = 'waiting';
    $approver = $manager_id;
    $inspector_status = 'waiting';
    $inspector_time = null;

    if (!is_null($inspector)) {
        $inspector_status = 'waiting';
        $inspector_time = $time_stamp;
    }

    $insertSql = "INSERT INTO day_off_request (input_timestamp, card_id, edit_time, request_card_id, old_work_format_code, edit_work_format_code, edit_detail, approve_status, approver, inspector_status, inspector, inspector_time, date_start, date_end) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insertParams = array(
        $time_stamp, $cardId, $time_stamp, $cardId, $work_format_code, $work, $detail, 
        $approve_status, $approver, $inspector_status, $inspector, $inspector_time, $dateStart, $dateEnd
    );

    $insertStmt = sqlsrv_query($conn, $insertSql, $insertParams);

    if ($insertStmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        $querynotify = "SELECT * FROM employee 
                        INNER JOIN login ON employee.card_id = login.card_id
                        WHERE employee.card_id = ?";
        $params = array($inspector ?: $manager_id);
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
        $message = "เรียนคุณ " . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . ' มีรายการขอตรวจสอบเมื่อเวลา ' . $time_stamp . " ดูเพิ่มเติม: https://liff.line.me/2003823996-Q0DXqyOZ";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'message=' . urlencode($message));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        echo '<script>
                window.location.href = "../dayoff/day-off-change-history-admin.php";
              </script>';
    }
}
?>