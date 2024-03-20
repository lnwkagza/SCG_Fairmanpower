<?php
include("../database/connectdb.php");  // Make sure this file includes the necessary database connection logic
session_start();

date_default_timezone_set('Asia/Bangkok');
$time_stamp = date("Y-m-d H:i:s");

$cardId = $_SESSION["card_id"];
$employeeId = isset($_POST['team_member']) ? $_POST['team_member'] : '';
$date = isset($_POST['request_date']) ? $_POST['request_date'] : '';
$originalShiftId = isset($_POST['original_shift']) ? $_POST['original_shift'] : '';
$addedShiftId = isset($_POST['added_shift']) ? $_POST['added_shift'] : '';
$detail = isset($_POST['reason']) ? $_POST['reason'] : '';
$inspectorId = isset($_POST['inspector']) ? $_POST['inspector'] : '';
$approveStatus = 'confirm';
$approverId = isset($_POST['approver_id']) ? $_POST['approver_id'] : ''; // Assuming this is correct, adjust accordingly

// SQL query with prepared statement
$sql = "INSERT INTO shift_add
            (input_timestamp, card_id, date, request_time, request_card_id, before_shift_type_id, add_shift_type_id, request_detail, approve_time, approve_status, approver, inspector) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

// Prepare the statement
$stmt = sqlsrv_prepare($conn, $sql, array(&$time_stamp, &$employeeId, &$date, &$time_stamp, &$cardId, &$originalShiftId, &$addedShiftId, &$detail, &$approveStatus, &$cardId, &$inspectorId));

// Execute the query
if (sqlsrv_execute($stmt)) {
    // Provide a JSON response indicating success
    echo '<script>
    window.location.href = "../shift/shift-progress-step5-head.php";
  </script>';
} else {
    // Provide a JSON response indicating failure along with error details
    echo json_encode(array('success' => false, 'message' => 'Error inserting data.', 'errors' => sqlsrv_errors()));
}

// Close the statement
sqlsrv_free_stmt($stmt);