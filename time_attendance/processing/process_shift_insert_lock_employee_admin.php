<?php
include("../database/connectdb.php");
session_start();

date_default_timezone_set('Asia/Bangkok');
$time_stamp = date("Y-m-d H:i:s");

$cardId = $_SESSION["card_id"];
$employeeIds = isset($_POST['employeeid']) ? $_POST['employeeid'] : '';
$date = isset($_POST['date']) ? $_POST['date'] : '';
$shiftType = isset($_POST['shiftType']) ? $_POST['shiftType'] : '';
$detail = isset($_POST['detail']) ? $_POST['detail'] : '';
$inspector = isset($_POST['inspector']) ? $_POST['inspector'] : '';
$approve = isset($_POST['approve']) ? $_POST['approve'] : '';

// SQL query with prepared statement
$sql = "INSERT INTO shift_lock
            (input_timestamp, card_id, date, request_time, request_card_id, shift_type_id, request_detail, approve_status, approver, inspector) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
       
// Prepare the statement
$stmt = sqlsrv_prepare($conn, $sql, array(&$time_stamp, &$employeeIds, &$date, &$time_stamp, &$cardId, &$shiftType, &$detail, 'waiting', &$approve, &$inspector));

// Execute the query
if (sqlsrv_execute($stmt)) {
    echo "Data inserted successfully.";
} else {
    die(print_r(sqlsrv_errors(), true)); // Error handling
}

// Close the statement
sqlsrv_free_stmt($stmt);
?>
