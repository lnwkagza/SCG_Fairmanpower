<?php
session_start();
require_once '../connect/connect.php';

if (isset($_POST['save'])) {
    $chapter_id = $_POST['chapter_id'];
    // ตรวจสอบถ้า Checkbox ถูกติกให้กำหนดค่าเป็น 1 ไม่ใช้กำหนดเป็น 0
    $checkboxValue = isset($_POST['myCheckbox']) ? 1 : 0;
    echo $chapter_id;
    echo " ";
    echo $checkboxValue;

    $update_query = "UPDATE Tablechapter SET general_target = ? WHERE chapter_id = ?";

    $params = array($checkboxValue, $chapter_id);
    $stmt = sqlsrv_query($conn, $update_query, $params);

    if ($stmt === false) {
        echo "เกิดข้อผิดพลาดในการอัพเดตข้อมูล: " . print_r(sqlsrv_errors(), true);
    } else {
        header("location: ../admin/web/uploadadminmain.php");
    }

    sqlsrv_close($conn);
}
?>