<?php
// เชื่อมต่อกับฐานข้อมูล
require_once('..\config\connection.php');

// รับค่าที่ส่งมาจาก JavaScript
$datail = isset($_POST['text']) ? $_POST['text'] : null;
$trId = isset($_POST['tr_id']) ? $_POST['tr_id'] : null;

// ตรวจสอบและอัปเดตข้อมูลในฐานข้อมูล
if ($datail !== null && $trId !== null) {
    $status = 'reject';
    // ทำการอัปเดตค่า tr.status ในฐานข้อมูลตาม $status ที่ได้รับ
    $sql = "UPDATE transaction_review SET status = ? , detail = ? WHERE tr_id = ?";
    $params = array($status,$datail, $trId);

    // Execute query และอัปเดตข้อมูล
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } 

    else {
        // ส่ง JSON response กลับไปยัง JavaScript
        echo json_encode(array('success' => true));
    }
} else {
    // ส่ง JSON response กลับไปยัง JavaScript โดยระบุข้อความแจ้งเตือน
    echo json_encode(array('success' => false, 'message' => 'Missing data'));
}
?>