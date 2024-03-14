<?php

session_start(); // เรียกใช้ session_start() ก่อนที่จะใช้ session

require_once('..\config\connection.php');

// นำค่า firstname_thai ที่ถูกส่งมาจาก URL parameters ไปใช้งาน
$tr_id = $_SESSION['id'];
$assessment_id = $_SESSION['assessment_id'];

// ตรวจสอบว่ามีการส่งข้อมูลผ่าน POST หรือไม่
// ตรวจสอบคะแนนทั้งหมดและทำการ Insert เข้าฐานข้อมูล
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $totalScore = 0; // สร้างตัวแปรสำหรับเก็บคะแนนรวม
    $totalQuestions = 0; // ตั้งค่าตัวแปรเพื่อเก็บจำนวนคำถามทั้งหมด

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'answer') !== false) {
            $questionID = substr($key, 6);

            $sql = "INSERT INTO question_log (tr_id,question_id, answer, date) VALUES (?,?, ?, GETDATE())";
            $params = array($tr_id, $questionID, $value);

            $stmt = sqlsrv_query($conn, $sql, $params);
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            // เพิ่มคะแนนเข้าไปในคะแนนรวม เฉพาะคะแนนที่เป็น 25, 50, 75, 100
            if ($value == 25 || $value == 50 || $value == 75 || $value == 100) {
                $totalScore += $value;
            }
            $totalQuestions++;

        }
    }
    $percentage = ($totalScore / ($totalQuestions * 100)) * 100;
    // ทำการ Insert คะแนนรวมเข้าฐานข้อมูลเมื่อทุกคำตอบถูก Insert และรวมคะแนนได้แล้ว
    $status = 'success'; // ประกาศตัวแปร status เพื่อเก็บข้อมูลสถานะ
    $sql_score = "UPDATE review_score SET score = ? , assessment_id = ?, date = GETDATE(), status = ? WHERE tr_id = ?";
    $params_score = array($percentage, $assessment_id, $status,$tr_id);

    $stmt_score = sqlsrv_query($conn, $sql_score, $params_score);
    if ($stmt_score === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // ทำการ Redirect ไปหน้า checkrole.php เมื่อเสร็จสิ้นการทำงาน
    header('Location: checkrole.php');
}

