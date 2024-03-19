<?php
session_start(); // เรียกใช้ session_start() ก่อนที่จะใช้ session
require_once('..\config\connection.php');
// ตรวจสอบว่ามี Session 'line_id' หรือไม่ และค่าของ 'line_id' ไม่เป็นค่าว่าง

if (isset($_GET['scg_employee_id'])&isset($_GET['formattedDateStart'])) {
    $scg_employee_id = $_GET['scg_employee_id'];
    $formattedDateStart = $_GET['formattedDateStart'];
    $emp_split = $_GET['split'];
    $_SESSION['scg_employee_id'] = $scg_employee_id;
    $_SESSION['formattedDateStart'] = $formattedDateStart;
    $_SESSION['split'] = $emp_split;


    
    if ($emp_split == 1) {
        header('Location: slip.php');
    } else {
        header('Location: slip1.php');
    }
}
?>
