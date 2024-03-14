<?php
session_start();
require_once '../connect/connect.php';
if (isset($_GET['quiz_id'])) {
    $quiz_id = $_GET['quiz_id'];

    $sql = "DELETE FROM Tablequiz WHERE quiz_id = ?";
    $params = array($quiz_id);

    $stmt = sqlsrv_prepare($conn, $sql, $params);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    $result = sqlsrv_execute($stmt);
    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "เสร็จ";
        header("location:../admin/web/editquiz.php");
        exit();
    }
} else {
    echo "ไม่มีคำขอการลบที่ถูกส่งมา";
}
?>
