<?php

session_start(); // เรียกใช้ session_start() ก่อนที่จะใช้ session

require_once('..\config\connection.php');

    // นำค่า firstname_thai ที่ถูกส่งมาจาก URL parameters ไปใช้งาน
    $tr_id = $_SESSION['id'];
    $review_to  =  $_SESSION['rt'];
    $role1 = $_SESSION['role'] ;

    // แสดงค่า firstname_thai ที่ถูกส่งมา

    if (isset($_POST['submit'])) {
        // รับค่าที่เลือกมาจาก dropdown
        $selectedValue1 = $_POST['dropdown1'];
        $selectedValue2 = $_POST['dropdown2'];
        $selectedValue3 = $_POST['dropdown3'];
        $status = NULL;
        $detail = NULL;
    
        switch ($role1) {
            case 'Peer':
                $sqlUpdate1 = "UPDATE transaction_review SET reviewer = ?,status = ? , detail = ? WHERE tr_id = ?";
                $params1 = array($selectedValue1,$status,$detail, $tr_id);
                $result1 = sqlsrv_query($conn, $sqlUpdate1, $params1);
                break;
    
            case 'Subordinate':
                $sqlUpdate2 = "UPDATE transaction_review SET reviewer = ?,status = ? , detail = ? WHERE tr_id = ?";
                $params2 = array($selectedValue2,$status,$detail, $tr_id);
                $result2 = sqlsrv_query($conn, $sqlUpdate2, $params2);
                break;
    
            case 'Customer':
                $sqlUpdate3 = "UPDATE transaction_review SET reviewer = ?,status = ? , detail = ? WHERE tr_id = ?";
                $params3 = array($selectedValue3,$status,$detail, $tr_id);
                $result3 = sqlsrv_query($conn, $sqlUpdate3, $params3);
                break;
    
            default:
                // เงื่อนไขที่ไม่ระบุ
                break;
        }
    
        // เช็คความสำเร็จของการอัพเดท
         if(isset($result1) && $result1) {
            header('Location: checkrole.php');
        } elseif (isset($result2) && $result2) {
            header('Location: checkrole.php');
        } elseif (isset($result3) && $result3) {
            header('Location: checkrole.php');
        } 
    }
?>