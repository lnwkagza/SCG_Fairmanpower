<?php

require_once('..\config\connection.php');

session_start(); // เริ่ม session

if (isset($_POST['reason_salary'])) {
    if (isset($_SESSION['splitId'])) {
        $splitId = $_SESSION['splitId']; // เก็บค่า session ในตัวแปร
        // นำค่าตัวแปร $splitId ไปใช้งานตามต้องการ
    }
    // รับค่าจาก input ในฟอร์ม
    $reason_salary = $_POST['reason_salary'];
    $admin_id = $_SESSION['card_id'];

    // ใช้ SQL เพื่อ insert ข้อมูลลงในฐานข้อมูล
    $sql_insert = "INSERT INTO log_saw_salary (reason,card_id_admin,datetime) VALUES (?,?,GETDATE())"; // แก้เป็น log_saw_salary
    $params_insert = array($reason_salary, $admin_id); // ระบุค่าที่ต้องการ insert ลงในคอลัมน์ของฐานข้อมูล

    // ทำการ execute คำสั่ง SQL พร้อมกับส่งพารามิเตอร์ไปให้ฐานข้อมูล
    $stmt_insert = sqlsrv_query($conn, $sql_insert, $params_insert);

    if ($stmt_insert === false) {
        // กรณีเกิดข้อผิดพลาดในการ execute SQL
        die(print_r(sqlsrv_errors(), true));
    } else {
        if ($splitId == "1") {
            // บันทึกข้อมูลสำเร็จ ส่งผ่านไปยังหน้าเว็บหลัก
            header('Location: resultsSummary1.php');
            exit;
        } else if ($splitId == "2") {
            header('Location: resultsSummary2.php');
            exit;
        }
    }
}
