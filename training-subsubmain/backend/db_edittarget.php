<?php 
session_start();
require_once '../connect/connect.php';

if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: login.php');
    exit();
}
$chapter = $_SESSION['last_viewed_lesson'];
$person_id = $_POST['myCheckbox'];

echo $chapter;
var_dump($person_id);
$sql = "SELECT * FROM Tablepositiontarget WHERE chapter_id = ?";
$params = array($chapter);
$stmt = sqlsrv_query($conn, $sql, $params);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
if (sqlsrv_has_rows($stmt)) {
    echo "พบค่าที่ตรงกับ $chapter ในฐานข้อมูล";
    $deleteSql = "DELETE FROM Tablepositiontarget WHERE chapter_id = ?";
    $deleteParams = array($chapter);
    $deleteStmt = sqlsrv_query($conn, $deleteSql, $deleteParams);

    if ($deleteStmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "ลบข้อมูลที่ตรงกับ $chapter แล้ว";
        // ทำสิ่งอื่นๆ หลังจากการลบข้อมูล
    }
} else {
    echo "ไม่พบค่าที่ตรงกับ $chapter ในฐานข้อมูล";
}
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
    header("location: ../admin/web/addtarget.php");
} else {
    echo "ไม่มีข้อมูลที่จะบันทึก";
    header("location: ../admin/web/addtarget.php");
}

?>
