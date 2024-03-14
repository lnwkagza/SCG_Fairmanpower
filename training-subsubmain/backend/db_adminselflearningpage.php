<?php
    session_start();
    require_once '../connect/connect.php';

    if (isset($_POST['save'])) {
        $userid = $_SESSION['user_login'];
        $self_name = $_POST['self_name'];
        $self_type = $_POST['self_type'];
        $self_from = $_POST['self_from'];
        $self_totallearn = $_POST['self_totallearn'];
        $self_timestart = $_POST['self_timestart'];
        $self_timeend = $_POST['self_timeend'];
        $self_link = $_POST['self_link'];
        $self_description = $_POST['self_description'];

        $userimage = $_FILES['self_img']['name'];
        $file_name = $_FILES['self_img']['name'];
        // เก็บไฟล์ภาพลงในโฟลเดอร์ที่ต้องการ
        $target_directory = "../data/imageself/";
        $target_file = $target_directory . $file_name;
        move_uploaded_file($_FILES['self_img']['tmp_name'], $target_file);

        $certificate = $_FILES['self_certificate']['name'];
        $file_name1 = $_FILES['self_certificate']['name'];
        // เก็บไฟล์ภาพลงในโฟลเดอร์ที่ต้องการ
        $target_directory1 = "../data/selfcertificate/";
        $target_file1 = $target_directory1 . $file_name;
        move_uploaded_file($_FILES['self_certificate']['tmp_name'], $target_file1);

            
            $sql = "INSERT Tableselflearning (person_id, self_name, self_type, self_from, self_totallearn, self_timestart, self_timeend, self_link, self_description, self_img, self_certificate ) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = array($userid, $self_name, $self_type, $self_from, $self_totallearn, $self_timestart, $self_timeend, $self_link, $self_description, $userimage, $certificate);
            $stmt = sqlsrv_query($conn, $sql, $params);
                if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
            header("location: ../user/web/adminselflearningpage.php");
        }
?>