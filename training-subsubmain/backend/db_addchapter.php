<?php
session_start();
require_once '../connect/connect.php';

if (isset($_POST['save'])) {
    // รับค่าจากฟอร์ม
    $course_id = $_POST['course_id']; // ใช้เพื่อเชื่อมกับตาราง Course
    $chapter_name = $_POST['chapter_name'];
    $chapter_type = $_POST['chapter_type']; // ตรวจสอบชื่อที่ถูกส่งมาจากฟอร์มว่าเป็นตัวเลือกใด
    $chapter_time = $_POST['chapter_time'];
    $general_target = isset($_POST['general_target']) ? 1 : 0; // ถ้า checkbox ถูกติ๊กให้เป็น 1 ไม่งั้นเป็น 0

    // ข้อมูลของไฟล์วิดีโอ
    $uploadedFile = $_FILES['VDO']['tmp_name']; // ที่อยู่ของไฟล์ที่อัพโหลด
    $VDOname = $_FILES['VDO']['name']; 
    $uploadDirectory = '../data/VDO/';
        $locationFile = $uploadDirectory . basename($_FILES['VDO']['name']);

        if (move_uploaded_file($_FILES['VDO']['tmp_name'], $locationFile)) {
            echo "ไฟล์ถูกอัพโหลดสำเร็จ";
        } else {
            echo "มีปัญหาเกิดขึ้นในการอัพโหลดไฟล์";
        }


    // คำสั่ง SQL เพื่อเพิ่มข้อมูลลงในฐานข้อมูล Tablechapter
    $sqlChapter = "INSERT INTO Tablechapter (course_id, chapter_name, chapter_type, chapter_time, VDO, general_target) 
            VALUES (?, ?, ?, ?, ?, ?)";

    // สร้าง prepared statement สำหรับ Tablechapter
    $paramsChapter = array($course_id, $chapter_name, $chapter_type, $chapter_time, $VDOname, $general_target);
    $stmtChapter = sqlsrv_prepare($conn, $sqlChapter, $paramsChapter);

    // ทำการ execute คำสั่ง SQL สำหรับ Tablechapter
    $resultChapter = sqlsrv_execute($stmtChapter);
    // ตรวจสอบผลลัพธ์
    if ($resultChapter === false ) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        // หากบันทึกสำเร็จ ทำการ redirect หรือแจ้งผู้ใช้
        if ($resultChapter === false) {
            die(print_r(sqlsrv_errors(), true));
        } else {
            // หา chapter_id ที่เพิ่งถูกเพิ่มลงในฐานข้อมูล
            $sqlGetChapterID = "SELECT IDENT_CURRENT('Tablechapter') AS chapter_id"; // อาจจะต้องแก้ไขตามชื่อตารางจริงของคุณ
            $stmtGetChapterID = sqlsrv_query($conn, $sqlGetChapterID);
        
            if ($stmtGetChapterID === false) {
                die(print_r(sqlsrv_errors(), true));
            } else {
                // อ่านค่า chapter_id จากการ query ที่เพิ่งทำ
                if ($row = sqlsrv_fetch_array($stmtGetChapterID, SQLSRV_FETCH_ASSOC)) {
                    // เก็บค่า chapter_id ใน $_SESSION
                    $_SESSION['last_viewed_lesson'] = $row['chapter_id'];
                }
        
                // หากเก็บค่า $_SESSION เรียบร้อยแล้ว ทำการ redirect หรือจัดการต่อตามต้องการ
                header("location: ../admin/web/addchapter_to_addtarget.php");
                exit();
            }
        }
    }
} else {
    echo "ไม่มีคำขอการบันทึกที่ถูกส่งมา";
}
?>
