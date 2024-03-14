<?php
session_start();
require_once '../connect/connect.php';
if (isset($_POST['deletechapter'])) {
    var_dump($_POST['chapter_id']);
    $chapter_id = $_POST['chapter_id'];

    $sql = "DELETE FROM Tablechapter WHERE chapter_id = ?";
    $params = array($chapter_id);

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
}
?>
