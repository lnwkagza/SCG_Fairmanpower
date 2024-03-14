<?php 
session_start();
require_once '../connect/connect.php';

if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: login.php');
    exit();
}

if (isset($_POST['save'])) {
    if(isset($_SESSION['last_viewed_lesson']) && isset($_POST['myCheckbox'])) {
        $lesson_id = $_SESSION['last_viewed_lesson'];
        $selectedEmployeeID = $_POST['myCheckbox'];

        foreach($selectedEmployeeID as $employeeID) {
            // เตรียมคำสั่ง SQL เพื่อ insert ข้อมูลลงในฐานข้อมูล
            $sqlInsert = "INSERT INTO Tablepositiontarget (chapter_id, person_id) VALUES (?, ?)";
            $stmtInsert = sqlsrv_query($conn, $sqlInsert, array($lesson_id, $employeeID));

            if ($stmtInsert === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        }

        echo "บันทึกข้อมูลเรียบร้อยแล้ว";
        header("location: ../admin/web/addquiz.php");
    } else {
        echo "ไม่มีข้อมูลที่จะบันทึก";
        header("location: ../admin/web/addquiz.php");
    }
}
?>
