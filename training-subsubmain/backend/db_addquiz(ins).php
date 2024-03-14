<?php
session_start();
require_once '../connect/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lesson_id = $_SESSION['last_viewed_lesson'];

    // เก็บค่าจากฟอร์มที่ส่งมาในตัวแปร
    $questions = $_POST['question'];
    $choicesA = $_POST['choicea'];
    $choicesB = $_POST['choiceb'];
    $choicesC = $_POST['choicec'];
    $choicesD = $_POST['choiced'];
    $answers = $_POST['answer']; 


    // วนลูปเพื่อดึงข้อมูลจากอาร์เรย์และทำการประมวลผล
    foreach ($questions as $index => $question) {
        // ดึงข้อมูลที่ต้องการจากอาร์เรย์ในแต่ละ index
        $currentQuestion = $questions[$index];
        $currentChoiceA = !empty($choicesA[$index]) ? $choicesA[$index] : null;
        $currentChoiceB = !empty($choicesB[$index]) ? $choicesB[$index] : null;
        $currentChoiceC = !empty($choicesC[$index]) ? $choicesC[$index] : null;
        $currentChoiceD = !empty($choicesD[$index]) ? $choicesD[$index] : null;
        $currentAnswer = $answers[$index];
        if($currentAnswer=='choicea'){
            $currentAnswer = $currentChoiceA;
        }elseif($currentAnswer=='choiceb'){
            $currentAnswer = $currentChoiceB;
        }elseif($currentAnswer=='choicec'){
            $currentAnswer = $currentChoiceC;
        }elseif($currentAnswer=='choiced'){
            $currentAnswer = $currentChoiceD;
        }

        // ทำสิ่งที่ต้องการกับข้อมูลที่ได้ เช่น บันทึกลงฐานข้อมูล หรือประมวลผลต่อไป
        // เช่น ใช้ข้อมูลดังกล่าวในคำสั่ง SQL เพื่อบันทึกลงฐานข้อมูล
        $sqlInsertQuiz = "INSERT INTO Tablequiz (chapter_id, question, choice_a, choice_b, choice_c, choice_d, answer) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtInsertQuiz = sqlsrv_prepare($conn, $sqlInsertQuiz, array(&$lesson_id, &$currentQuestion, &$currentChoiceA, &$currentChoiceB, &$currentChoiceC, &$currentChoiceD, &$currentAnswer));

        if ($stmtInsertQuiz === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_execute($stmtInsertQuiz) === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }

    echo "บันทึกข้อมูลเรียบร้อยแล้ว";
    header("location: ../admin/web/uploadadminmain.php");
}