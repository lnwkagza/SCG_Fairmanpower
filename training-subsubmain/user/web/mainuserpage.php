<?php 
    session_start();
    require_once '../../connect/connect.php';
    if(!isset($_SESSION['user_login'])){
        $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
        header('location: ../../../../linelogin/index.html');
        exit();
    }

    // ถ้าผู้ใช้กด Logout
    if(isset($_GET['logout']) && isset($_SESSION['user_login'])) {
        // ลบ session และส่งผู้ใช้กลับไปยังหน้าล็อกอิน
        unset($_SESSION['user_login']);
        $_SESSION['error'] = 'คุณได้ออกจากระบบแล้ว';
        header('location: ../../../../linelogin/index.html');
        exit();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../plugin/bootstrap-icons-1.11.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../plugin/boxicons-2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="../css/mainuserpage.css">
    <link rel="stylesheet" href="../../components/sidebaruser.css">
    <link rel="stylesheet" href="../../components/footer.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300&display=swap" rel="stylesheet">
    <title>User Page</title>
</head>
<body>


<?php


        if(isset($_SESSION['user_login'])) {
            $user_id = $_SESSION['user_login'];
        } elseif(isset($_SESSION['admin_login'])) {
            $user_id = $_SESSION['admin_login'];
        } else {
            // ทำการกำหนดค่าเริ่มต้นสำหรับ $user_id ในกรณีที่ไม่มีค่าใน session ที่ต้องการ
            $user_id = null; // หรือค่าอื่น ๆ ตามที่ต้องการ
        }
        $role = $user_id;
        $sql = "SELECT 
        employee.employee_image,
        employee.card_id,
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
        department.name_eng AS department_name_eng,
        permission.name AS permission
        FROM employee 
        LEFT JOIN position_info ON employee.card_id = position_info.card_id
        LEFT JOIN position ON position_info.position_id = position.position_id
        LEFT JOIN cost_center ON employee.cost_center_organization_id = cost_center.cost_center_id
        LEFT JOIN section ON cost_center.section_id = section.section_id
        LEFT JOIN department ON section.department_id = department.department_id
        LEFT JOIN permission ON employee .permission_id = employee.permission_id 
         
                WHERE employee.person_id = ?";
        $params = array($user_id);
        $stmt = sqlsrv_query($conn, $sql, $params);
        if( $stmt === false ) {
            die( print_r( sqlsrv_errors(), true));
        }
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $image_path = '../../../admin/uploads_img/'.$row['employee_image'];
        $years = $row['service_year'];
        $months = $row['service_month'];
        $card_id = $row['card_id'];
    

    $sql1 = "SELECT employee.firstname_thai,employee.lastname_thai FROM manager
    JOIN employee ON employee.card_id = manager.manager_card_id
    WHERE manager.card_id = ?";
    $params1 = array($card_id);
    $stmt1 = sqlsrv_query($conn, $sql1, $params1);
    if( $stmt1 === false ) {
        die( print_r( sqlsrv_errors(), true));
    }
    $row1 = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC);

?>

<?php include('../../components/sidebaruser.php');?>

<div class="contianer">

    <div class="mainhead">
        <div class="mainname">ข้อมูลส่วนตัว</div>
        <div class="dot1"></div>
        <div class="dot2"></div>
        <div class="dot3"></div>
    </div>

    <div class="imageuser" ><?php echo "<img src='$image_path' alt='รูปภาพ' class='profile'> ";?></div>

        <div class="conresu">
            <div class="resuma">
                <div class="form">
                    <div class="data" style="color: #555555; text-decoration: none !important;"><?php echo "รหัสพนักงาน : " . $row['scg_employee_id']; ?></div>
                    <div class="data"><?php echo "ชื่อ-นามสกุล : " . $row['firstname_thai'].' '. $row['lastname_thai']; ?></div>
                    <div class="data"><?php echo "อายุงาน : $years ปี $months เดือน"; ?></div>
                    <div class="data"><?php echo "ความเชี่ยวชาญ: " . $row['skill']; ?></div>
                    <div class="data"><?php echo "ตำเเหน่ง: " . $row['position_name_eng']; ?></div>
                    <div class="data"><?php echo "หน่วยงาน: " . $row['section_name_eng']; ?></div>
                    <div class="data"><?php echo "สังกัด: " . $row['department_name_eng']; ?></div>
                    <div class="data"><?php echo "ผู้บังคับบัญชา: " . $row1['firstname_thai'].' '. $row1['lastname_thai']; ?></div>
                </div>
            </div>

            <div class="button">
                <a href="usercoursetargetpage.php"><button type="button" class="manu"><i class='bx bx-book'></i> หลักสูตรที่ต้องเรียนรู้</button></a>
                <a href="usersubcourseotherpage.php"><button type="button" class="manu"><i class="bi bi-journal-text"></i> หลักสูตรอื่นๆ</button></a>
                <a href="userOJTpage.php"><button type="button" class="manu"><i class="bi bi-book"></i> OJT/Self Learning</button></a>
                <a href="userlearnsummary.php"><button type="button" class="manu"><i class="bi bi-bar-chart-line-fill"></i> สรุปผลการเรียนรู้</button></a>
                <a href="usertraining-record.php"><button type="button" class="manu"><i class="bi bi-bar-chart-line-fill"></i> ฝึกอบรม</button></a>
            </div>

        </div>

   
<?php include('../../components/footer.php');?>
    
</div>
</body>
</html>