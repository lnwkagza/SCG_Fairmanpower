<?php
session_start();
require_once '../connect/connect.php';

if (isset($_POST['save'])) {
    $course_id = $_POST['course_id'];
    $course_name = $_POST['course_name'];

    // ตรวจสอบความถูกต้องของข้อมูลก่อนบันทึกลงฐานข้อมูล
    // เตรียมคำสั่ง SQL เพื่อบันทึกข้อมูล
    $sql = "INSERT INTO Tablecourse (course_id, course_name) VALUES (?, ?)";
    $params = array($course_id, $course_name);

    // ทำการ execute คำสั่ง SQL
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        // หลังจากบันทึกเรียบร้อยแล้ว ทำสิ่งที่ต้องการ เช่น redirect หรือแสดงข้อความว่าบันทึกสำเร็จ
        header("location: ../admin/web/uploadadminmain.php");
        exit();
    }
}
?>
