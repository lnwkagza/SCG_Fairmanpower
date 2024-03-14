<?php
    session_start();
    require_once 'connect\connect.php';

    if (isset($_POST['record'])) {
        $userid = $_SESSION['user_login'];
        // $userid = $_SESSION['admin_login'];
        $jobid = $_POST['jobid'];
        $prefix = $_POST['prefix'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $jobtime = $_POST['jobtime'];
        $jobskill = $_POST['jobskill'];
        $jobrole = $_POST['jobrole'];
        $agency = $_POST['agency'];
        $affiliation = $_POST['affiliation'];
        $commender = $_POST['commender'];
    
        $userimage = $_FILES['userimage']['name'];

            $file_name = $_FILES['userimage']['name'];
    
            // เก็บไฟล์ภาพลงในโฟลเดอร์ที่ต้องการ
            $target_directory = "imageprofile/";
            $target_file = $target_directory . $file_name;
    
            // ย้ายไฟล์ภาพไปยังโฟลเดอร์ที่กำหนด
            move_uploaded_file($_FILES['userimage']['tmp_name'], $target_file);
    
            // ให้ทำการอัปเดตข้อมูลในฐานข้อมูล
            $sql = "UPDATE employee 
                    SET employee_image = ?, scg_employee_id = ?, prefix_th = ?, firstname_th = ?, lastname_th = ?, scg_hiring_date = ?, skill = ?, position_name = ?, section_text_thai = ?, department_text_thai = ?, manager_name = ?
                    WHERE person_id = ?";
            $params = array($userimage, $jobid, $prefix, $firstname, $lastname, $jobtime,  $jobskill, $jobrole, $agency, $affiliation, $commender, $userid);
            $stmt = sqlsrv_query($conn, $sql, $params);
    
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
    
            $_SESSION['success'] = "อัปเดตข้อมูลส่วนตัวเรียบร้อยแล้ว";
            // header("location: mainuserpage.php");
            unset($_SESSION['success']);
        }
?>