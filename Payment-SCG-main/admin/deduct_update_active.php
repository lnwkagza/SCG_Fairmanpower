<?php

require_once('..\..\config\connection.php');

// ตรวจสอบว่ามีข้อมูลถูกส่งมาจาก AJAX request หรือไม่
if (isset($_POST['isChecked']) && isset($_POST['deductTargetId'])) {
    $isChecked = $_POST['isChecked'];
    $deductTargetId = $_POST['deductTargetId'];

    $sql = "UPDATE deduct_target SET active = ? WHERE deduct_target_id = ?";
    $params = array($isChecked,$deductTargetId);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    // ตอบกลับถึง client ว่าการอัพเดตสถานะเสร็จสมบูรณ์
    echo 'Success';
}
?>