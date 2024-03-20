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

    $sql_manager  = "SELECT manager_card_id FROM manager WHERE card_id = ?";
    $params_manager  = array($_SESSION["card_id"]);
    $query_manager  = sqlsrv_query($conn, $sql_manager, $params_manager);
    $row_manager  = sqlsrv_fetch_array($query_manager, SQLSRV_FETCH_ASSOC);
    $manager_id = $row_manager["manager_card_id"];

        $insertSql = "INSERT INTO day_off_request (input_timestamp, card_id, edit_time, request_card_id, edit_work_format_code, edit_detail, approve_status,approver,date_start,date_end) 
        VALUES (?, ?, ?, ?, ?, ?, ?,?,?,?)";
        
        $insertParams = array($time_stamp, $employeeid, $time_stamp, $cardId, $work, $detail, 'waiting',$manager_id,$dateStart,$dateEnd);
        
        $insertStmt = sqlsrv_query($conn, $insertSql, $insertParams);
        
        if ($insertStmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else {
            echo '<script>
                    window.location.href = "../shift/shift-progress-step2-employee.php";
                  </script>';
        }

}
?>