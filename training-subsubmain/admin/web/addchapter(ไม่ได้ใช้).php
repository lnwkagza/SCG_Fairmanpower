<?php
    session_start();
    require_once 'connect\connect.php';

    if (!isset($_SESSION['admin_login'])) {
        $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
        header('location: login.php');
        exit();
    }

    // ถ้าผู้ใช้กด Logout
    if (isset($_GET['logout']) && isset($_SESSION['admin_login'])) {
        unset($_SESSION['admin_login']);
        $_SESSION['error'] = 'คุณได้ออกจากระบบแล้ว';
        header('location: login.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap-icons-1.11.2\font\bootstrap-icons.css">
    <link rel="stylesheet" href="boxicons-2.1.4\css\boxicons.min.css">
    <link rel="stylesheet" href="components\sidebaradminupload.css">
    <link rel="stylesheet" href="addchapter.css">
    <link rel="stylesheet" href="components\navbarprofile.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300&display=swap" rel="stylesheet">
    <title>AdminPage</title>
</head>
<body>
    <?php
    if(isset($_SESSION['user_login'])){
        $user_id = $_SESSION['user_login'];
        $role = $user_id;
        $sql = "SELECT 
        employee.employee_image,
        employee.person_id,
        employee.scg_employee_id,
        employee.prefix_thai,
        employee.firstname_thai,
        employee.lastname_thai,
        employee.service_year,
        employee.service_month,
        employee.skill,
        position.name_eng AS position_name_eng,
        section.name_eng AS section_name_eng,
        department.name_eng AS department_name_eng
        FROM employee 
        JOIN position_info ON employee.card_id = position_info.card_id
        JOIN position ON position_info.position_id = position.position_id
        JOIN cost_center ON employee.cost_center_organization_id = cost_center.cost_center_code
        JOIN section ON cost_center.section_id = section.section_id
        JOIN department ON section.department_id = department.department_id
         
                WHERE employee.person_id = ?";
        $params = array($user_id);
        $stmt = sqlsrv_query($conn, $sql, $params);
        if( $stmt === false ) {
            die( print_r( sqlsrv_errors(), true));
        }
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $image_path = 'imageprofile/'.$row['employee_image'];
        $years = $row['service_year'];
        $months = $row['service_month'];
    }
        $sql1 = "SELECT course_id, course_name FROM Tablecourse";
        $stmt1 = sqlsrv_query($conn, $sql1);
        if ($stmt1 === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        $displayedCourses = [];
    ?>

    <div class="container">
        <?php include('components\sidebaradmin.php'); ?>
        <?php include('components\navbarprofileadmin.php'); ?>

        <div class="title">
            <div class="titlename">เพิ่มบทเรียน</div>
        </div>

        <div class="mainclass">
            <div class="buttoncon">
                <a href="addcourse.php"><button try="button" class="uploadmanu2">เพิ่มหลักสูตร</button></a>
                <a href="addchapter.php"><button try="button" class="uploadmanu1">เพิ่มบทเรียน</button></a>
                
            </div>
            <div class="tablecourse">
                <div class="addform">
                    <form action="db_addchapter.php" method="post" enctype="multipart/form-data">

                        <div class="manuaddchapter">
                        <label class="form-label">เลือกหลักสูตร</label>
                        <select name="course_id" id="course_id">
                            <?php
                            while ($row1 = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
                                echo '<option value="' . $row1['course_id'] . '">' . $row1['course_name'] . '</option>';
                            }
                            ?>
                        </select>
                        

                        <label class="form-label">กรอกชื่อบทเรียน</label>
                        <input type="text" name="chapter_name"class="chapter_name">
                        </div>


                        <div class="manuaddchapter">
                        <label class="form-label">เลือกประเภทของบทเรียน</label>
                        <select name="chapter_type" id="chapter_type">
                            <option value="ตามตำแหน่ง" selected="selected">ตามตำแหน่ง</option>
                            <option value="กฎหมายบังคับ" >กฎหมายบังคับ</option>
                            <option value="พื้นฐาน" >พื้นฐาน</option>
                            <option value="อื่นๆ" >อื่นๆ</option>
                        </select>

                        <label class="form-label">เลือกคลิปวิดีโอการสอน</label>
                        <input type="file" name="VDO" id="VDO" class="VDO">
                        <label for="VDO" class="VDOtext">เลือกไฟล์วิดีโอ</label>
                        </div>

                        <div class="manuaddchapter">
                        <label class="form-label">เวลาmandayที่ได้</label>
                        <input type="text" name="chapter_time"class="chapter_time">


                        <td><input type="checkbox" name="general_target" class="general_target"></td>
                        <label class="target">พนักงานทุกคนสามารถเรียนได้หรือไม่</label>
                        </div>

                        
                        
                        <div class="buttonsubmit"> <a href="addtarget.php?chapter_id=<?= $row1['chapter_id']; ?>"></a><button type="submit" name="save" class="addbutton">ถัดไป</button></div>
                            
                    </form>
                </div>
                <button onclick="goBack()" class = "backbutton"><i class='bx bx-left-arrow-alt' ></i></button>
            </div>
        </div>
    </div>
    
    
</body>
</html>
<script>
function goBack() {
  window.history.back();
}
</script>