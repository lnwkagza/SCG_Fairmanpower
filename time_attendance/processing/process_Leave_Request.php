<?php
session_start();
include("../database/connectdb.php");

date_default_timezone_set('Asia/Bangkok');
$time_stamp = date("Y-m-d H:i:s");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cardId = $_SESSION["card_id"];
    $absenceTypeId = $_POST['leaveType'];
    $dateStart = $_POST['startDate'];
    $dateEnd = $_POST['endDate']; 
    $timeStart = $_POST['startTime']; 
    $timeEnd = $_POST['endTime'];
    $requestDetail = $_POST['leaveDetails'];

    $sql = "INSERT INTO absence_record (card_id, input_timestamp, absence_type_id, date_start, date_end, time_start, time_end, request_detail,approve_status) 
            VALUES (?,?,?,?,?,?,?,?,?)";
    $params = array($cardId, $time_stamp,$absenceTypeId, $dateStart, $dateEnd, $timeStart, $timeEnd, $requestDetail,'waiting');
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo '<script>
                window.location.href = "../leave/leave-history-employee.php";
              </script>';
    }
}
?>
