<?php

require_once('..\config\connection.php');

session_start(); // เริ่ม session

if (isset($_POST['reason_payment'])) {
    // รับค่าจาก input ในฟอร์ม
    $reason_payment = $_POST['reason_payment'];
    $employee_payment_id = $_SESSION['employee_payment_id'];
    $e_card_id = $_SESSION['e_card_id'] ;
    $admin_id = $_SESSION['card_id'] ;

    // ทำสิ่งที่ต้องการกับข้อมูล เช่น บันทึกข้อมูลลงในฐานข้อมูล
    // ตัวอย่างนี้คือการใช้ SQL เพื่อ insert ข้อมูลลงในฐานข้อมูล
    $sql_insert = "INSERT INTO log_saw_payment (reason,card_id,card_id_admin,datetime) VALUES (?,?,?,GETDATE())"; // แทน column_name ด้วยชื่อคอลัมน์ที่ต้องการบันทึกข้อมูล
    $params_insert = array($reason_payment,$e_card_id, $admin_id); // ระบุค่าที่ต้องการ insert ลงในคอลัมน์ของฐานข้อมูล

    // ทำการ execute คำสั่ง SQL พร้อมกับส่งพารามิเตอร์ไปให้ฐานข้อมูล
    $stmt_insert = sqlsrv_query($conn, $sql_insert, $params_insert);

    if ($stmt_insert === false) {
        // กรณีเกิดข้อผิดพลาดในการ execute SQL
        die(print_r(sqlsrv_errors(), true));
    } else {
        // บันทึกข้อมูลสำเร็จ ส่งผ่านไปยังหน้าเว็บหลัก
        header('Location: employee_payment_edit.php');
        exit;
    }
}
?>