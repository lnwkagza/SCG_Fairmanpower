<?php
    session_start();
    require_once 'connect\connect.php';

    if (isset($_POST['save'])) {
        if(isset($_SESSION['user_login'])) {
            $user_id = $_SESSION['user_login'];
        } elseif(isset($_SESSION['admin_login'])) {
            $user_id = $_SESSION['admin_login'];
        } else {
            // ทำการกำหนดค่าเริ่มต้นสำหรับ $user_id ในกรณีที่ไม่มีค่าใน session ที่ต้องการ
            $user_id = null; // หรือค่าอื่น ๆ ตามที่ต้องการ
        }
        $training_name = htmlspecialchars($_POST['training_name']);
        $training_start = htmlspecialchars($_POST['training_start']);
        $training_end = htmlspecialchars($_POST['training_end']);
        $training_totaltime = htmlspecialchars($_POST['training_totaltime']);
        $training_from = htmlspecialchars($_POST['training_from']);
        $training_type = htmlspecialchars($_POST['training_type']);
        $training_notifydevelopment = htmlspecialchars($_POST['training_notifydevelopment']);
        $training_objective = htmlspecialchars($_POST['training_objective']);
        $training_topic = htmlspecialchars($_POST['training_topic']);
        $training_lecturer1 = htmlspecialchars($_POST['training_lecturer1']);
        $training_lecturer2 = htmlspecialchars($_POST['training_lecturer2']);
        $training_lecturer3 = htmlspecialchars($_POST['training_lecturer3']);
        $training_lecturer4 = htmlspecialchars($_POST['training_lecturer4']);
        $training_lecturer5 = htmlspecialchars($_POST['training_lecturer5']);
        $training_lecturer6 = htmlspecialchars($_POST['training_lecturer6']);
        $training_costcenter = htmlspecialchars($_POST['training_costcenter']);
        $training_costelement = htmlspecialchars($_POST['training_costelement']);
        $training_internalorder = htmlspecialchars($_POST['training_internalorder']);
        $training_courseperperson = htmlspecialchars($_POST['training_courseperperson']);
        $training_personcourse = htmlspecialchars($_POST['training_personcourse']);
        $training_totalcourse = htmlspecialchars($_POST['training_totalcourse']);
        $training_ticketperperson = htmlspecialchars($_POST['training_ticketperperson']);
        $training_personticket = htmlspecialchars($_POST['training_personticket']);
        $training_totalticket = htmlspecialchars($_POST['training_totalticket']);
        $training_carperperson = htmlspecialchars($_POST['training_carperperson']);
        $training_personcar = htmlspecialchars($_POST['training_personcar']);
        $training_totalcar = htmlspecialchars($_POST['training_totalcar']);
        $training_hotalperperson = htmlspecialchars($_POST['training_hotalperperson']);
        $training_personhotal = htmlspecialchars($_POST['training_personhotal']);
        $training_totalhotal = htmlspecialchars($_POST['training_totalhotal']);
        $training_otherperperson = htmlspecialchars($_POST['training_otherperperson']);
        $training_personother = htmlspecialchars($_POST['training_personother']);
        $training_totalother = htmlspecialchars($_POST['training_totalother']);
        $training_costtotal = htmlspecialchars($_POST['training_costtotal']);
        $training_persontotal = htmlspecialchars($_POST['training_persontotal']);
        $training_costperperson = htmlspecialchars($_POST['training_costperperson']);
        
        echo $user_id;
        echo "Training Name: " . $training_name . "<br>";
        echo "Training Start Date: " . $training_start . "<br>";
        echo "Training End Date: " . $training_end . "<br>";
        echo "Training Total Time: " . $training_totaltime . "<br>";
        echo "Training Location: " . $training_from . "<br>";
        echo "Training Type: " . $training_type . "<br>";
        echo "Notify Development: " . $training_notifydevelopment . "<br>";
        echo "Training Objective: " . $training_objective . "<br>";
        echo "Training Topic: " . $training_topic . "<br>";
        echo "Lecturer 1: " . $training_lecturer1 . "<br>";
        echo "Lecturer 2: " . $training_lecturer2 . "<br>";
        echo "Lecturer 3: " . $training_lecturer3 . "<br>";
        echo "Lecturer 4: " . $training_lecturer4 . "<br>";
        echo "Lecturer 5: " . $training_lecturer5 . "<br>";
        echo "Lecturer 6: " . $training_lecturer6 . "<br>";
        echo "Cost Center: " . $training_costcenter . "<br>";
        echo "Cost Element: " . $training_costelement . "<br>";
        echo "Internal Order: " . $training_internalorder . "<br>";
        echo "Course Cost Per Person: " . $training_courseperperson . "<br>";
        echo "Number of Persons for Course: " . $training_personcourse . "<br>";
        echo "Training Total Course: " . $training_totalcourse . "<br>";
        echo "Training Ticket Per Person: " . $training_ticketperperson . "<br>";
        echo "Training Person Ticket: " . $training_personticket . "<br>";
        echo "Training Total Ticket: " . $training_totalticket . "<br>";
        echo "Training Car Per Person: " . $training_carperperson . "<br>";
        echo "Training Person Car: " . $training_personcar . "<br>";
        echo "Training Total Car: " . $training_totalcar . "<br>";
        echo "Training Hotel Per Person: " . $training_hotalperperson . "<br>";
        echo "Training Person Hotel: " . $training_personhotal . "<br>";
        echo "Training Total Hotel: " . $training_totalhotal . "<br>";
        echo "Training Other Per Person: " . $training_otherperperson . "<br>";
        echo "Training Person Other: " . $training_personother . "<br>";
        echo "Training Total Other: " . $training_totalother . "<br>";
        echo "Training Cost Total: " . $training_costtotal . "<br>";
        echo "Training Person Total: " . $training_persontotal . "<br>";
        echo "Training Cost Per Person: " . $training_costperperson . "<br>";

            $sql = "INSERT INTO Tabletraining (
                person_id, 
                training_name,  
                training_from, 
                training_type, 
                training_start, 
                training_end, 
                training_objective, 
                training_topic, 
                training_lecturer1, 
                training_lecturer2, 
                training_lecturer3, 
                training_lecturer4, 
                training_lecturer5, 
                training_lecturer6, 
                training_notifydevelopment, 
                training_totaltime, 
                training_costcenter, 
                training_costelement, 
                training_internalorder, 
                training_courseperperson, 
                training_personcourse, 
                training_totalcourse, 
                training_ticketperperson, 
                training_personticket, 
                training_totalticket, 
                training_carperperson, 
                training_personcar, 
                training_totalcar, 
                training_hotalperperson, 
                training_personhotal, 
                training_totalhotal, 
                training_otherperperson, 
                training_personother, 
                training_totalother,
                training_persontotal, 
                training_costtotal, 
                training_costperperson) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = array(
                $user_id, 
                $training_name, 
                $training_from, 
                $training_type, 
                $training_start, 
                $training_end, 
                $training_objective, 
                $training_topic, 
                $training_lecturer1, 
                $training_lecturer2, 
                $training_lecturer3, 
                $training_lecturer4, 
                $training_lecturer5, 
                $training_lecturer6, 
                $training_notifydevelopment, 
                $training_totaltime, 
                $training_costcenter, 
                $training_costelement, 
                $training_internalorder, 
                $training_courseperperson, 
                $training_personcourse, 
                $training_totalcourse, 
                $training_ticketperperson, 
                $training_personticket, 
                $training_totalticket, 
                $training_carperperson, 
                $training_personcar, 
                $training_totalcar, 
                $training_hotalperperson, 
                $training_personhotal, 
                $training_totalhotal, 
                $training_otherperperson, 
                $training_personother, 
                $training_totalother,
                $training_persontotal,  
                $training_costtotal, 
                $training_costperperson
            );

            $conn = sqlsrv_connect($serverName, $connectionOptions);
            if (!$conn) {
                die("Connection failed: " . sqlsrv_errors());
            }
        
            $stmt = sqlsrv_query($conn, $sql, $params);
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            } else {
                echo "Training data inserted successfully.";
            }

            }

    
    