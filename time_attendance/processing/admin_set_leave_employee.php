<?php
session_start();
include('../database/dbconnect.php');

// Get form data
$card_id = $_POST['empid']; // ถ้า empid เป็นชื่อของ input สำหรับรหัสพนักงาน
$annual_leave = $_POST['annual_leave'];
$maternity_leave = $_POST['maternity_leave'];
$ordination_leave = $_POST['ordination_leave'];
$training_leave_nopaid = $_POST['training_leave_nopaid'];
$other_leave = $_POST['other_leave'];
$sick_leave = $_POST['sick_leave'];

// ตรวจสอบว่ามีข้อมูลของพนักงานอยู่แล้วหรือไม่
$select_query = "SELECT * FROM absence_quota WHERE card_id = ?";
$select_stmt = sqlsrv_prepare($conn, $select_query, array(&$card_id));
sqlsrv_execute($select_stmt);
$row_count = sqlsrv_num_rows($select_stmt);

if ($row_count > 0) {
    // มีข้อมูลของพนักงานอยู่แล้ว จะทำการอัปเดตข้อมูล
    $update_query = "UPDATE absence_quota SET 
                    annual_leave = ?,
                    maternity_leave = ?,
                    ordination_leave = ?,
                    training_leave_nopaid = ?,
                    other_leave = ?,
                    sick_leave = ?
                    WHERE card_id = ?";
    $update_stmt = sqlsrv_prepare($conn, $update_query, array(
        &$annual_leave, &$maternity_leave, &$ordination_leave, &$training_leave_nopaid,
        &$other_leave, &$sick_leave, &$card_id
    ));
    if (sqlsrv_execute($update_stmt) === false) {
        echo "อัปเดตข้อมูลไม่สำเร็จ";
    } else {
        echo "อัปเดตข้อมูลสำเร็จ";
    }
} else {
    // ไม่มีข้อมูลของพนักงานอยู่ จะทำการเพิ่มข้อมูลใหม่
    $insert_query = "INSERT INTO absence_quota (card_id, annual_leave, maternity_leave, ordination_leave,
                    training_leave_nopaid, other_leave, sick_leave) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = sqlsrv_prepare($conn, $insert_query, array(
        &$card_id, &$annual_leave, &$maternity_leave, &$ordination_leave, &$training_leave_nopaid,
        &$other_leave, &$sick_leave
    ));
    if (sqlsrv_execute($insert_stmt) === false) {
        echo "เพิ่มข้อมูลไม่สำเร็จ";
    } else {
        echo "เพิ่มข้อมูลสำเร็จ";
    }
}
?>
