<?php
session_start();
require_once '../connect/connect.php';


if (isset($_POST['course_code'])) {
    // ทำความสะอาดข้อมูลที่รับเข้ามาเพื่อป้องกันการโจมตีแบบ XSS
    
    $course_id = $_POST['course_code'];
    $cleaned_chapter_type = htmlspecialchars($_POST['course_name'], ENT_QUOTES, 'UTF-8');
            echo $course_id;
            echo" ";
            echo $cleaned_chapter_type;
    // สร้างคำสั่ง SQL สำหรับการอัพเดตข้อมูลใน SQL Server
    $update_query = "UPDATE Tablecourse SET course_name = ? WHERE course_id = ?";
    $params = array($cleaned_chapter_type, $course_id);
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
