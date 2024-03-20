<?php
session_start();
include("../database/connectdb.php");

date_default_timezone_set('Asia/Bangkok');
$time_stamp = date("Y-m-d H:i:s");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cardId = $_SESSION["card_id"];
    $employeeid = $_POST["employeeid"];
    $work = $_POST['work'];
    $detail = $_POST['detail'];
    $dateStart = $_POST['startDate'];
    $dateEnd = $_POST['endDate'];


    $insertSql = "INSERT INTO day_off_request (input_timestamp, card_id, edit_time, request_card_id, edit_work_format_code, edit_detail, approve_time, approve_status,approver,date_start,date_end) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?,?,?,?)";

    $insertParams = array($time_stamp, $employeeid, $time_stamp, $cardId, $work, $detail, $time_stamp, 'confirm', $cardId, $dateStart, $dateEnd);

    $insertStmt = sqlsrv_query($conn, $insertSql, $insertParams);

    if ($insertStmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo '<script>
                    window.location.href = "../shift/shift-progress-step2-head.php";
                  </script>';
    }
}