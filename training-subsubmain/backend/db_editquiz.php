<?php 
session_start();
require_once '../connect/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quizIds = $_POST['quiz_id'];
    $questions = $_POST['question'];
    $choicesA = $_POST['choicea'];
    $choicesB = $_POST['choiceb'];
    $choicesC = $_POST['choicec'];
    $choicesD = $_POST['choiced'];
    $answers = $_POST['answer'];
    $chapter_id = $_SESSION['last_viewed_lesson'];

    foreach ($questions as $index => $currentQuestion) {
        // ดึงข้อมูลจาก form
        $currentQuizId = $quizIds[$index];
        $currentChoiceA = $choicesA[$index];
        $currentChoiceB = $choicesB[$index];
        $currentChoiceC = $choicesC[$index];
        $currentChoiceD = $choicesD[$index];
        $currentAnswer = $answers[$index];

        // Check if choices are empty, set them to NULL
        $currentChoiceA = empty($currentChoiceA) ? null : $currentChoiceA;
        $currentChoiceB = empty($currentChoiceB) ? null : $currentChoiceB;
        $currentChoiceC = empty($currentChoiceC) ? null : $currentChoiceC;
        $currentChoiceD = empty($currentChoiceD) ? null : $currentChoiceD;

        if ($currentAnswer == 'choicea') {
            $currentAnswer = $currentChoiceA;
        } elseif ($currentAnswer == 'choiceb') {
            $currentAnswer = $currentChoiceB;
        } elseif ($currentAnswer == 'choicec') {
            $currentAnswer = $currentChoiceC;
        } elseif ($currentAnswer == 'choiced') {
            $currentAnswer = $currentChoiceD;
        }

        var_dump($currentQuestion);
        var_dump($currentQuizId);
        var_dump($currentChoiceA);
        var_dump($currentChoiceB);
        var_dump($currentChoiceC);
        var_dump($currentChoiceD);
        var_dump($currentAnswer);

        if ($currentQuizId <> 0) {
            // Existing quiz, update
            $sqlUpdateQuiz = "UPDATE Tablequiz 
                              SET question = ?, choice_a = ?, choice_b = ?, choice_c = ?, choice_d = ?, answer = ? 
                              WHERE quiz_id = ?";
            $stmtUpdateQuiz = sqlsrv_prepare($conn, $sqlUpdateQuiz, array(
                &$currentQuestion,
                &$currentChoiceA,
                &$currentChoiceB,
                &$currentChoiceC,
                &$currentChoiceD,
                &$currentAnswer,
                &$currentQuizId
            ));
            echo "เเก้ไขสำเร็จ";
            header("location: ../admin/web/editquiz.php");
            if ($stmtUpdateQuiz === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            // กรณีที่มีการ execute ไม่สำเร็จ
            if (sqlsrv_execute($stmtUpdateQuiz) === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        } elseif ($currentQuizId == 0) {
            // New quiz, insert
            $sqlInsert = "INSERT INTO Tablequiz (chapter_id, question, choice_a, choice_b, choice_c, choice_d, answer) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtInsert = sqlsrv_prepare($conn, $sqlInsert, array(
                &$chapter_id,
                &$currentQuestion,
                &$currentChoiceA,
                &$currentChoiceB,
                &$currentChoiceC,
                &$currentChoiceD,
                &$currentAnswer
            ));
            echo "เพิ่มสำเร็จ";
            header("location: ../admin/web/editquiz.php");
            if ($stmtInsert === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            // กรณีที่มีการ execute ไม่สำเร็จ
            if (sqlsrv_execute($stmtInsert) === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        }
    }
} 
?>
