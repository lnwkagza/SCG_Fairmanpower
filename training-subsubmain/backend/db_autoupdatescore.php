<?php
session_start();
require_once '../connect/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userAnswers = json_decode(file_get_contents('php://input'), true);

    // ดึงคำตอบที่ถูกต้องจากฐานข้อมูล
    $sql = "SELECT quiz_id, answer FROM Tablequiz WHERE chapter_id = ?";
    $params = array($_SESSION['chapterId']);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $correctAnswers = array();
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $correctAnswers[$row['quiz_id']] = $row['answer'];
    }

    // เปรียบเทียบคำตอบของผู้ใช้กับคำตอบที่ถูกต้อง
    $result = array();
    foreach ($userAnswers as $quizId => $userAnswer) {
        $result[$quizId] = ($userAnswer === $correctAnswers[$quizId]);
    }

    // คำนวณคะแนน
    $totalScore = array_sum($result);
    $maxScore = count($result);

    // อัปเดตคะแนนในฐานข้อมูล
    if(isset($_SESSION['user_login'])) {
        $userId = $_SESSION['user_login'];
    } elseif(isset($_SESSION['admin_login'])) {
        $userId = $_SESSION['admin_login'];
    } else {
        // ทำการกำหนดค่าเริ่มต้นสำหรับ $userId ในกรณีที่ไม่มีค่าใน session ที่ต้องการ
        $userId = null; // หรือค่าอื่น ๆ ตามที่ต้องการ
    }
    $status_total = 2;
     // คำนวณว่าจะใช้ User ID หรือไม่ขึ้นอยู่กับโครงสร้างของฐานข้อมูล
    $chapterId = $_SESSION['chapterId'];
    $updateSql = "UPDATE Tabletrainningdata SET status_total = ?, score = ? WHERE chapter_id = ? AND person_id = ?";
    $updateParams = array($status_total, $totalScore, $chapterId, $userId);
    $updateStmt = sqlsrv_query($conn, $updateSql, $updateParams);

    if ($updateStmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // ส่งผลลัพธ์พร้อมคะแนนกลับเป็น JSON
    header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'ตรวจสอบคำตอบเรียบร้อย', 'result' => $result, 'score' => array('total' => $totalScore, 'max' => $maxScore)));
    exit();
} else {
    // วิธีการร้องขอไม่ถูกต้อง
    header('Content-Type: application/json');
    echo json_encode(array('success' => false, 'message' => 'วิธีการร้องขอไม่ถูกต้อง'));
    exit();
}

?>
