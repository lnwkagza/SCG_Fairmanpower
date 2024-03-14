<?php
// เชื่อมต่อกับฐานข้อมูล
require_once('..\config\connection.php');

// ...
// รับค่าที่ส่งมาจาก JavaScript
$status = isset($_POST['status']) ? $_POST['status'] : null;
$trIds = isset($_POST['tr_ids']) ? $_POST['tr_ids'] : null;

// ตรวจสอบและอัปเดตข้อมูลในฐานข้อมูล
if ($status !== null && $trIds !== null) {
    // แยกค่า tr_ids เป็นอาร์เรย์
    $trIdArray = explode(',', $trIds);

    // ทำการอัปเดตค่า tr.status ในฐานข้อมูลตาม $status ที่ได้รับ
    $sql = "UPDATE transaction_review SET status = ? WHERE tr_id IN (" . implode(',', array_fill(0, count($trIdArray), '?')) . ")";
    $params = array_merge([$status], $trIdArray);

    // Execute query และอัปเดตข้อมูล
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "All statuses updated successfully";
    }
} else {
    echo "Missing data";
}

?>