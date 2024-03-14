<?php
session_start();
require_once '../connect/connect.php';

if (isset($_POST['chapter_name'])) {
    $chapter_id = $_POST['chapter_id'];
    var_dump ($chapter_id);
    $cleaned_chapter_name = htmlspecialchars($_POST['chapter_name'], ENT_QUOTES, 'UTF-8');
    echo $cleaned_chapter_name;
    $update_query = "UPDATE Tablechapter SET chapter_name = ? WHERE chapter_id = ?";

    $params = array($cleaned_chapter_name, $chapter_id);
    $stmt = sqlsrv_query($conn, $update_query, $params);

    if ($stmt === false) {
        echo "เกิดข้อผิดพลาดในการอัพเดตข้อมูล: " . print_r(sqlsrv_errors(), true);
    } else {
        header("location: ../admin/web/uploadadminmain.php");
        echo "อัพเดตข้อมูลเรียบร้อย";
    }

    sqlsrv_close($conn);
}

?>
