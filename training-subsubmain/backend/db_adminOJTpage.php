<?php
    session_start();
    require_once '../connect/connect.php';

    if (isset($_POST['save'])) {
        $userid = $_SESSION['user_login'];
        $ojt_name = $_POST['ojt_name'];
        $ojt_type = $_POST['ojt_type'];
        $ojt_chapter = $_POST['ojt_chapter'];
        $ojt_totallearn = $_POST['ojt_totallearn'];
        $ojt_manday = $ojt_totallearn/8;
        $ojt_timestart = $_POST['ojt_timestart'];
        $ojt_timeend = $_POST['ojt_timeend'];
        $ojt_description = $_POST['ojt_description'];
        $userimage = $_FILES['ojt_img']['name'];
        
        echo $ojt_name.' ';
        echo $ojt_type.' ';
        echo $ojt_chapter.' ';
        echo $ojt_totallearn.' ';
        echo $ojt_timestart.' ';
        echo $ojt_manday.' ';
        echo $ojt_timeend.' ';
        echo $ojt_description.' ';
        echo $userimage.' ';
        echo $userid;
            $file_name = $_FILES['ojt_img']['name'];
    
            // เก็บไฟล์ภาพลงในโฟลเดอร์ที่ต้องการ
            $target_directory = "../data/imageOJT/";
            $target_file = $target_directory . $file_name;
    
            // ย้ายไฟล์ภาพไปยังโฟลเดอร์ที่กำหนด
            move_uploaded_file($_FILES['ojt_img']['tmp_name'], $target_file);
            $sql = "INSERT TableOJT (person_id, ojt_name, ojt_type, ojt_chapter, ojt_totallearn, ojt_manday, ojt_timestart, ojt_timeend, ojt_description, ojt_img ) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = array($userid, $ojt_name, $ojt_type, $ojt_chapter, $ojt_totallearn, $ojt_manday, $ojt_timestart, $ojt_timeend, $ojt_description, $userimage);
            $stmt = sqlsrv_query($conn, $sql, $params);
                if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            header("location: ../user/web/adminOJTpage.php");
        }
?>