<?php
session_start();
include("../database/connectdb.php");

date_default_timezone_set('Asia/Bangkok');
$time_stamp = date("Y-m-d H:i:s");

$shift_change_id = isset($_GET['shift_change_id']) ? $_GET['shift_change_id'] : '';
$shift_add_id = isset($_GET['shift_add_id']) ? $_GET['shift_add_id'] : '';
$shift_lock_id = isset($_GET['shift_lock_id']) ? $_GET['shift_lock_id'] : '';
$shift_switch_id = isset($_GET['shift_switch_id']) ? $_GET['shift_switch_id'] : '';
$sub_team_id = isset($_GET['sub_team_id']) ? $_GET['sub_team_id'] : '';

if (!empty($shift_change_id)) {
    $query = "UPDATE shift_change 
              SET approve_status = ?, approver = ?, approve_time = ? 
              WHERE shift_change_id = ?";
    $addvalues = array('confirm', $_SESSION["card_id"], $time_stamp, $shift_change_id);
    $stmt = sqlsrv_prepare($conn, $query, $addvalues);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true)); // Handle errors
    }

    $result = sqlsrv_execute($stmt);

    if ($result === false) {
        die(print_r(sqlsrv_errors(), true)); // Handle errors
    }
}

if (!empty($shift_add_id)) {
    $query = "UPDATE shift_add 
              SET approve_status = ?, approver = ?, approve_time = ? 
              WHERE shift_add_id = ?";
    $addvalues = array('confirm', $_SESSION["card_id"], $time_stamp, $shift_add_id);
    $stmt = sqlsrv_prepare($conn, $query, $addvalues);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true)); // Handle errors
    }

    $result = sqlsrv_execute($stmt);

    if ($result === false) {
        die(print_r(sqlsrv_errors(), true)); // Handle errors
    }
}

if (!empty($shift_lock_id)) {
    $query = "UPDATE shift_lock 
              SET approve_status = ?, approver = ?, approve_time = ? 
              WHERE shift_lock_id = ?";
    $addvalues = array('confirm', $_SESSION["card_id"], $time_stamp, $shift_lock_id);
    $stmt = sqlsrv_prepare($conn, $query, $addvalues);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true)); // Handle errors
    }

    $result = sqlsrv_execute($stmt);

    if ($result === false) {
        die(print_r(sqlsrv_errors(), true)); // Handle errors
    }
}

if (!empty($shift_switch_id)) {
    $query = "UPDATE shift_switch 
              SET approve_status = ?, approver = ?, approve_time = ? 
              WHERE shift_switch_id = ?";
    $addvalues = array('confirm', $_SESSION["card_id"], $time_stamp, $shift_switch_id);
    $stmt = sqlsrv_prepare($conn, $query, $addvalues);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true)); // Handle errors
    }

    $result = sqlsrv_execute($stmt);

    if ($result === false) {
        die(print_r(sqlsrv_errors(), true)); // Handle errors
    }
}

if (!empty($sub_team_id)) {
    $query = "UPDATE sub_team 
              SET approve_status = ?, approver = ?, approve_time = ? 
              WHERE sub_team_id = ?";
    $addvalues = array('confirm', $_SESSION["card_id"], $time_stamp, $sub_team_id);
    $stmt = sqlsrv_prepare($conn, $query, $addvalues);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true)); // Handle errors
    }

    $result = sqlsrv_execute($stmt);

    if ($result === false) {
        die(print_r(sqlsrv_errors(), true)); // Handle errors
    }
}

echo '<script>
                alert("คุณได้ทำการอนุมัติ");
                window.location.href = "../shift/shift-request-head.php";
              </script>';

// if ($result === false) {
//     die(print_r(sqlsrv_errors(), true));
// }

// $querynotify = "SELECT * FROM absence_record 
// INNER JOIN login ON absence_record.card_id = login.card_id
// RIGHT JOIN employee ON login.card_id = employee.card_id
// WHERE absence_record_id = ?";
// $params = array($id);
// $stmtnotify = sqlsrv_query($conn, $querynotify, $params);

// if ($stmtnotify === false) {
//     die(print_r(sqlsrv_errors(), true));
// }

// $row = sqlsrv_fetch_array($stmtnotify, SQLSRV_FETCH_ASSOC);

// $url = 'https://notify-api.line.me/api/notify';
// $headers = array(
//     'Content-Type: application/x-www-form-urlencoded',
//     'Authorization: Bearer ' . $row['line_token']
// );
// $message = "รายการขอลาของคุณ " . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . 'ได้รับการอนุมัติ เมื่อเวลา ' . $time_stamp;


// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, $url);
// curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
// curl_setopt($ch, CURLOPT_POSTFIELDS, 'message=' . urlencode($message));
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// $response = curl_exec($ch);
// curl_close($ch);

?>
