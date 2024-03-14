<?php
session_start();
require_once '../connect/connect.php';

if (isset($_POST['chapter_id']) && isset($_FILES['VDO'])) {
    $chapter_id = $_POST['chapter_id'];
    $VDOname = $_FILES['VDO']['name'];
    $uploadDirectory = '../data/VDO/';
    $locationFile = $uploadDirectory . basename($_FILES['VDO']['name']);
    
    echo $locationFile;
    // Check file type and size here before moving it to the directory
    
    if (move_uploaded_file($_FILES['VDO']['tmp_name'], $locationFile)) {
        $sql = "UPDATE Tablechapter SET VDO = ? WHERE chapter_id = ?";
        $params = array($VDOname, $chapter_id);
        $stmt = sqlsrv_query($conn, $sql, $params);
        
        if ($stmt) {
            echo "อัปเดตชื่อคลิปสำเร็จ";
            echo "$VDOname";
            echo "$chapter_id";
            header("location: ../admin/web/uploadadminmain.php");
        } else {
            echo "การอัปเดตผิดพลาด: " . print_r(sqlsrv_errors(), true);
        }
    } else {
        echo "มีปัญหาเกิดขึ้นในการอัพโหลดไฟล์";
     }
} else {
    echo "ข้อมูลไม่ครบถ้วนหรือไม่ถูกต้อง";
}

// Close the SQL connection
sqlsrv_close($conn);
?>
