<?php

require_once('..\..\config\connection.php');


// ตรวจสอบว่ามีข้อมูลถูกส่งมาจาก AJAX request หรือไม่
if (isset($_POST['isChecked']) && isset($_POST['incomeTargetId'])) {
    $isChecked = $_POST['isChecked'];
    $incomeTargetId = $_POST['incomeTargetId'];

    $sql = "UPDATE income_target SET active = ? WHERE income_target_id = ?";
    $params = array($isChecked,$incomeTargetId);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    // ตอบกลับถึง client ว่าการอัพเดตสถานะเสร็จสมบูรณ์
    echo 'Success';
}
?>
