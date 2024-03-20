<?php
session_start();
include("../database/connectdb.php");

date_default_timezone_set('Asia/Bangkok');
$time_stamp = date("Y-m-d H:i:s");

echo $cardId = $_SESSION["card_id"];
echo $employeeIds = isset($_POST['employeeid']) ? $_POST['employeeid'] : '';
echo $date = isset($_POST['date']) ? $_POST['date'] : '';
echo $shiftType = isset($_POST['shiftType']) ? $_POST['shiftType'] : '';
echo $detail = isset($_POST['detail']) ? $_POST['detail'] : '';
echo $inspector = isset($_POST['inspector']) ? $_POST['inspector'] : '';
echo $approve = isset($_POST['approve']) ? $_POST['approve'] : '';

// SQL query to retrieve day-off settings for the employee
$select_dayoff_set = "SELECT work_format.day_off1 AS day_off1, work_format.day_off2 AS day_off2
FROM employee 
INNER JOIN work_format ON employee.work_format_code = work_format.work_format_code
WHERE employee.card_id = ?";
$dayoff_set_stmt = sqlsrv_prepare($conn, $select_dayoff_set, array($employeeIds));

// SQL query to retrieve day-off requests for the employee within the date range
$select_dayoff_syn = "SELECT work_format.day_off1 AS day_off1, work_format.day_off2 AS day_off2,
date_start, date_end
FROM day_off_request 
INNER JOIN work_format ON day_off_request.edit_work_format_code = work_format.work_format_code 
WHERE card_id = ? AND date_start <= ? AND date_end >= ?";

$select_shift_format = "SELECT card_id, day, shift_format.shift_type_id
FROM employee
INNER JOIN shift_format ON employee.work_format_code = shift_format.work_format_code
INNER JOIN shift_type ON shift_format.shift_type_id = shift_type.shift_type_id
WHERE card_id = ? AND day = ?
ORDER BY card_id ASC";

$select_absence_record = "SELECT * FROM absence_record WHERE card_id = ? AND date_start <= ? AND date_end >= ?";
$select_shift_add = "SELECT * FROM shift_add WHERE card_id = ? AND date = ?";
$select_shift_change = "SELECT * FROM shift_change WHERE card_id = ? AND date = ? ";
$select_shift_lock = "SELECT * FROM shift_lock WHERE card_id = ? AND date = ? ";

$select_shift_switch = "SELECT date, new_shift_1 AS new_shift FROM shift_switch WHERE employee_1 = ? AND date = ?
UNION
SELECT date, new_shift_2 AS new_shift FROM shift_switch WHERE employee_2 = ? AND date = ?
UNION
SELECT date, new_shift_3 AS new_shift FROM shift_switch WHERE employee_3 = ? AND date = ? ";

$select_holiday = "SELECT * FROM holiday WHERE date = ?";


// Loop through the date range
foreach (array($date) as $formatted_date) {

$formatted_date = date('Y-m-d', strtotime($date));
$day_of_week = date('D', strtotime($date));
$absence_record_stmt = sqlsrv_prepare($conn, $select_absence_record, array($employeeIds, $formatted_date, $formatted_date));

$dayoff_syn_stmt = sqlsrv_prepare($conn, $select_dayoff_syn, array($employeeIds, $formatted_date, $formatted_date));
$holiday_stmt = sqlsrv_prepare($conn, $select_holiday, array($formatted_date));

$shift_format_stmt = sqlsrv_prepare($conn, $select_shift_format, array($employeeIds,$day_of_week));

$shift_add_stmt = sqlsrv_prepare($conn, $select_shift_add, array($employeeIds, $formatted_date));
$shift_change_stmt = sqlsrv_prepare($conn, $select_shift_change, array($employeeIds, $formatted_date));
$shift_lock_stmt = sqlsrv_prepare($conn, $select_shift_lock, array($employeeIds, $formatted_date));
$shift_switch_stmt = sqlsrv_prepare($conn, $select_shift_switch, array(&$date, &$formatted_date, &$card_id, &$formatted_date, &$card_id, &$formatted_date));

// Execute the SQL statements
if (sqlsrv_execute($dayoff_set_stmt) && 
sqlsrv_execute($dayoff_syn_stmt) && 
sqlsrv_execute($shift_format_stmt) && 
sqlsrv_execute($absence_record_stmt) && 
sqlsrv_execute($shift_add_stmt) &&
sqlsrv_execute($shift_change_stmt) &&
sqlsrv_execute($shift_lock_stmt) && 
sqlsrv_execute($shift_switch_stmt) && 
sqlsrv_execute($holiday_stmt)) {

$dayoff_set_result = sqlsrv_fetch_array($dayoff_set_stmt, SQLSRV_FETCH_ASSOC);
$dayoff_syn_result = sqlsrv_fetch_array($dayoff_syn_stmt, SQLSRV_FETCH_ASSOC);

$holiday_result = sqlsrv_fetch_array($holiday_stmt, SQLSRV_FETCH_ASSOC);

$absence_record_result = sqlsrv_fetch_array($absence_record_stmt, SQLSRV_FETCH_ASSOC);
$shift_add_result = sqlsrv_fetch_array($shift_add_stmt, SQLSRV_FETCH_ASSOC);
$shift_change_result = sqlsrv_fetch_array($shift_change_stmt, SQLSRV_FETCH_ASSOC);
$shift_lock_result = sqlsrv_fetch_array($shift_lock_stmt, SQLSRV_FETCH_ASSOC);
$shift_switch_result = sqlsrv_fetch_array($shift_switch_stmt, SQLSRV_FETCH_ASSOC);

$shift_format_result = sqlsrv_fetch_array($shift_format_stmt, SQLSRV_FETCH_ASSOC);

// Initialize variables
$day1 = NULL;
$day2 = NULL;

$shift_main = $shift_format_result['shift_type_id'];

$shift_add = NULL;
$shift_lock = NULL;

// Check if there is a day-off request for the current date
if ($dayoff_syn_result) {
$day1 = $dayoff_syn_result['day_off1'];
$day2 = $dayoff_syn_result['day_off2'];
} elseif ($dayoff_set_result) {
$day1 = $dayoff_set_result['day_off1'];
$day2 = $dayoff_set_result['day_off2'];
}

// Map day names to numbers
$dayMap = [
'Sun' => 7,
'Mon' => 1,
'Tue' => 2,
'Wed' => 3,
'Thu' => 4,
'Fri' => 5,
'Sat' => 6,
];
$day_off1 = $dayMap[$day1];
$day_off2 = $dayMap[$day2];

// Check if the current date is a holiday
if ($day_off1 == $dayMap[$day_of_week] || $day_off2 == $dayMap[$day_of_week]) {
$is_holiday = "Yes";
} else {
$is_holiday = "No";
}

if ($absence_record_result) {
$shift_main = 'LEAVE';
} elseif ($holiday_result) {
$shift_main = 'HOLIDAY';
$is_holiday = "Yes";
} elseif ($shift_add_result) {
$shift_add = $shift_add_result['add_shift_type_id'];
} elseif ($shift_change_result) {
$shift_main = $shift_change_result['new_shift_id'];
} elseif ($shift_lock_result) {
// $shift_main = $shift_lock_result['shift_type_id'];
$shift_lock = $shift_lock_result['shift_type_id'];
}
elseif ($shift_switch_result) {
$shift_main = $shift_switch_result['new_shift'];
// $shift_switch = $shift_switch_result['new_shift'];
}

}
}


// SQL query with prepared statement
$sql = "INSERT INTO shift_change
            (input_timestamp, card_id, date,  request_card_id,old_shift_id, new_shift_id, request_detail, approve_status, approver, inspector) 
        VALUES (?, ?, ?, ?, ?, ?,?, ?, ?, ?)";
       
// Prepare the statement
$stmt = sqlsrv_prepare($conn, $sql, array(&$time_stamp, &$employeeIds, &$date, &$cardId,$shift_main, &$shiftType, &$detail, 'waiting', &$approve, &$inspector));

// Execute the query
if (sqlsrv_execute($stmt)) {
    echo "Data inserted successfully.";
} else {
    die(print_r(sqlsrv_errors(), true)); // Error handling
}

// Close the statement
sqlsrv_free_stmt($stmt);
?>
