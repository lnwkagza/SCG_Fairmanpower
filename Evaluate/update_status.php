<?php
// เชื่อมต่อกับฐานข้อมูล
require_once('..\config\connection.php');


// รับค่าที่ส่งมาจาก JavaScript
$status = isset($_POST['status']) ? $_POST['status'] : null;
$trId = isset($_POST['tr_id']) ? $_POST['tr_id'] : null;

// ตรวจสอบและอัปเดตข้อมูลในฐานข้อมูล
if ($status !== null && $trId !== null) {
    // ทำการอัปเดตค่า tr.status ในฐานข้อมูลตาม $status ที่ได้รับ
    $sql = "UPDATE transaction_review SET status = ? WHERE tr_id = ?";
    $params = array($status, $trId);

    // Execute query และอัปเดตข้อมูล
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "Status updated successfully";
    }
} else {
    echo "Missing data"; // หรือส่งข้อความกลับหา JavaScript ว่ามีข้อมูลส่งมาไม่ครบถ้วน
}
?>
