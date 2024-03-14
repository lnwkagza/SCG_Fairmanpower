<?php
session_start();
require_once '../connect/connect.php';


if (isset($_POST['chapter_type'])) {
    // ทำความสะอาดข้อมูลที่รับเข้ามาเพื่อป้องกันการโจมตีแบบ XSS
    $chapter_id = $_POST['chapter_id'];
    $cleaned_chapter_type = htmlspecialchars($_POST['chapter_type'], ENT_QUOTES, 'UTF-8');

    // สร้างคำสั่ง SQL สำหรับการอัพเดตข้อมูลใน SQL Server
    $update_query = "UPDATE Tablechapter SET chapter_type = ? WHERE chapter_id = ?";

    // สร้างพรีแพร์ด์สเตตเมนต์สำหรับคำสั่ง SQL
    $params = array($cleaned_chapter_type, $chapter_id);
    $stmt = sqlsrv_query($conn, $update_query, $params);

    if ($stmt === false) {
        echo "เกิดข้อผิดพลาดในการอัพเดตข้อมูล: " . print_r(sqlsrv_errors(), true);
    } else {
        header("location: ../admin/web/uploadadminmain.php");
    }

    // ปิดการเชื่อมต่อ
    sqlsrv_close($conn);
}
?>
