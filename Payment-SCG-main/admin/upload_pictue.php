<?php
session_start();
require_once('..\config\connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $targetDir = "flie/";
    $targetFile = $targetDir . $_FILES["file"]["name"];
    $file_type = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $income_target_id = 504;

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
        $filename = $_FILES["file"]["name"];
        $sql = "UPDATE income_target SET evidence_name = ?, evidence_data = ? WHERE income_target_id = ?";
        
        // ใช้ sqlsrv_prepare เพื่อเตรียมคำสั่ง SQL
        $stmt = sqlsrv_prepare($conn, $sql, array(&$filename, &$targetFile, &$income_target_id));
        
        // ใช้ sqlsrv_execute เพื่อ execute คำสั่ง SQL
        if (sqlsrv_execute($stmt)) {
            echo "success";
        } else {
            echo "error";
        }
    }
}
?>
