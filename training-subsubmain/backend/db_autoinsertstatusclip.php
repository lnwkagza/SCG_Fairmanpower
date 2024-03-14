<?php

session_start();
require_once '../connect/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chapterId = $_POST['chapterId'];
    if(isset($_SESSION['user_login'])) {
        $personId = $_SESSION['user_login'];
    } elseif(isset($_SESSION['admin_login'])) {
        $personId = $_SESSION['admin_login'];
    } else {
        // ทำการกำหนดค่าเริ่มต้นสำหรับ $personId ในกรณีที่ไม่มีค่าใน session ที่ต้องการ
        $personId = null; // หรือค่าอื่น ๆ ตามที่ต้องการ
    }
    $courseName = $_POST['courseName'];
    $statusVDO = 1;
    $statustotle = 1;
    $score = 0;

    // Check if data already exists for the given personId and chapterId
    $checkSql = "SELECT * FROM Tabletrainningdata WHERE person_id = ? AND chapter_id = ?";
    $checkParams = array($personId, $chapterId);
    $checkStmt = sqlsrv_query($conn, $checkSql, $checkParams);

    if ($checkStmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $existingData = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);

    if ($existingData) {
        // Data already exists, update the existing record
        $updateSql = "UPDATE Tabletrainningdata SET status_VDO = ?, status_total = ?, score = ? WHERE person_id = ? AND chapter_id = ?";
        $updateParams = array($statusVDO, $statustotle, $score, $personId, $chapterId);
        $updateStmt = sqlsrv_query($conn, $updateSql, $updateParams);

        if ($updateStmt === false) {
            die(print_r(sqlsrv_errors(), true));
        } else {
            $_SESSION['chapterId'] = $chapterId;
            $_SESSION['courseName'] = $courseName;
            exit();
        }
    } else {
        // Data doesn't exist, insert new record
        $insertSql = "INSERT INTO Tabletrainningdata (person_id, chapter_id, status_VDO, status_total, score) VALUES (?, ?, ?, ?, ?)";
        $insertParams = array($personId, $chapterId, $statusVDO, $statustotle, $score);
        $insertStmt = sqlsrv_query($conn, $insertSql, $insertParams);

        if ($insertStmt === false) {
            die(print_r(sqlsrv_errors(), true));
        } else {
            $_SESSION['chapterId'] = $chapterId;
            $_SESSION['courseName'] = $courseName;
            exit();
        }
    }
}

?>
