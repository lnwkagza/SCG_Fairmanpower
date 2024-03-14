<?php
session_start();
require_once '../connect/connect.php';


if (isset($_POST['course_code']) && isset($_POST['course_id'])) {
    $course_id = $_POST['course_id'];
    $cleaned_course_code = htmlspecialchars($_POST['course_code'], ENT_QUOTES, 'UTF-8');

    $update_query = "UPDATE Tablecourse SET course_id = ? WHERE course_id = ?";
    $params = array($cleaned_course_code, $course_id);
    $stmt = sqlsrv_query($conn, $update_query, $params);

    if ($stmt === false) {
        echo "เกิดข้อผิดพลาดในการอัพเดตข้อมูล: " . print_r(sqlsrv_errors(), true);
    } else {
        header("location: ../admin/web/uploadadminmain.php");
    }

    sqlsrv_close($conn);
}
?>
