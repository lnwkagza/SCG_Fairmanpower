<?php
session_start();
require_once '../connect/connect.php';
if (isset($_POST['deletecoure'])) {
    
    $course_id = $_POST['course_name'];
    echo $course_id;
    $sql = "DELETE FROM Tablecourse WHERE course_name = ?";
    $params = array($course_id);

    $stmt = sqlsrv_prepare($conn, $sql, $params);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    $result = sqlsrv_execute($stmt);
    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        header("location: ../admin/web/uploadadminmain.php");
        exit();
    }
} else {
    echo "ไม่มีคำขอการลบที่ถูกส่งมา";
    echo $course_id;
}
?>
