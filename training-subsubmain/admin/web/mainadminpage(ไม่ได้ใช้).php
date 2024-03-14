<?php 
    session_start();
    require_once 'connect\connect.php';
    if(!isset($_SESSION['admin_login'])){
        $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
        header('location: login.php');
        exit();
    }

    // ถ้าผู้ใช้กด Logout
    if(isset($_GET['logout']) && isset($_SESSION['admin_login'])) {
        // ลบ session และส่งผู้ใช้กลับไปยังหน้าล็อกอิน
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
    <link rel="stylesheet" href="mainadminpage.css">
    <link rel="stylesheet" href="components\sidebaradminmainpage.css">
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
?>

    <div class="contianer">
        
    <?php include('components\sidebaradmin.php');?>
    <?php include('components\navbarprofileadmin.php');?>

        <div class="title"><div class="titlename">หน้าเเรก</div></div>

        <div class="mainmanu">
            <a href="#"><button type="button"><div class="icon1"><i class="bi bi-calendar-fill"></i></div></button></a>
            <a href="#"><button type="button"><div class="icon2"><i class="bi bi-briefcase-fill"></i></div></button></a>
            <a href="#"><button type="button"><div class="icon3"><i class="bi bi-person-fill"></i></div></button></a>
            <a href="#"><button type="button"><div class="icon4"><i class="bi bi-envelope-fill"></i></div></button></a>
        </div>


    </div>
</body>
</html>