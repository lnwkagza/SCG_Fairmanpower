<?php
session_start();
require_once '../../connect/connect.php';
if(!isset($_SESSION['user_login']) ){
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location:  ../../../../linelogin/index.html');
    exit();
}

// ถ้าผู้ใช้กด Logout
if (isset($_GET['logout']) && isset($_SESSION['user_login'])) {
    // ลบ session และส่งผู้ใช้กลับไปยังหน้าล็อกอิน
    unset($_SESSION['user_login']);
    $_SESSION['error'] = 'คุณได้ออกจากระบบแล้ว';
    header('location:  ../../../../linelogin/index.html');
    exit();
}

?>

<?php include('../../components/head.php') ?>
<link rel="stylesheet" href="../../plugin/bootstrap-icons-1.11.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../plugin/boxicons-2.1.4/css/boxicons.min.css">
<link rel="stylesheet" href="../css/usertraining-record.css">
<title>AdminPage</title>

<body>
<?php 
        if(isset($_SESSION['user_login'])) {
            $user_id = $_SESSION['user_login'];
            echo $user_id;
        } elseif(isset($_SESSION['admin_login'])) {
            $user_id = $_SESSION['admin_login'];
            echo $user_id;
        } else {
            // ทำการกำหนดค่าเริ่มต้นสำหรับ $user_id ในกรณีที่ไม่มีค่าใน session ที่ต้องการ
            $user_id = null; // หรือค่าอื่น ๆ ตามที่ต้องการ
            echo $user_id;
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
        LEFT JOIN permission ON employee .permission_id = permission.permission_id  
         
                WHERE employee.person_id = ?";
        $params = array($user_id);
        $stmt = sqlsrv_query($conn, $sql, $params);
        if( $stmt === false ) {
            die( print_r( sqlsrv_errors(), true));
        }
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $image_path = '../../data/imageprofile/'.$row['employee_image'];
        $years = $row['service_year'];
        $months = $row['service_month'];
    ?>
    <?php include('../../components/navbaruserall.php') ?>
    <?php include('../../components/sidebaruserall.php') ?>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pl-10 pt-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <span id="head">การฝึกอบรม</span>
                                <span id="recommend">คำแนะนำ : โปรดค้นหาหลักสูตรการฝึกอบรม จากระยะเวลาการฝึกอบรม หรือค้นหาจากชื่อหลักสูตร</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                        <div class="card-box pd-10 pt-10 height-100-p">
                            <div class="bar">
                                <div class="mainclass">
                                    <div class="addData">
                                        <span>ค้นหาข้อมูลการฝึกอบรม</span>
                                        <a href="usertraining-add.php"><i class="bi bi-plus-circle-fill"></i> เพิ่มข้อมูล</a>
                                    </div>

                                    <div class="box">
                                        <div class="start">
                                            <label for="">วันที่เริ่มอบรม</label>
                                            <input type="date">
                                        </div>
                                        <div class="end">
                                            <label for="">วันที่สิ้นสุดอบรม</label>
                                            <input type="date">
                                        </div>
                                        <div class="name">
                                            <label for="">ชื่อหลักสูตร</label>
                                            <input type="text" placeholder="โปรดค้นหาชื่อหลักสูตร" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="datatable">
                                        <table>
                                            <thead>
                                                <th>ตั้งแต่วันที่</th>
                                                <th>จนถึงวันที่</th>
                                                <th>ชื่อหลักสูตร</th>
                                                <th>ราคาต่อหัว</th>
                                                <th>ชั่วโมง</th>
                                                <th>สถานะ</th>
                                                <th>รายละเอียด</th>
                                                <th>แก้ไข/ลบ</th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1 Jan 2024</td>
                                                    <td>1 Jan 2024</td>
                                                    <td>Project management การจัดการโครงการ</td>
                                                    <td>100.00</td>
                                                    <td>8 ชม.</td>
                                                    <td><button id="yellow"></button></td>
                                                    <td>
                                                        <div class="btintb">
                                                            <a class="detail" href="usertraining-preview.php"><i class="bi bi-info"></i></a>
                                                        </div>
                                                    </td>
                                                    <td>
                                                    <div class="btintb">
                                                            <a class="edit" href="usertraining-edit.php"><i class="bi bi-pencil-square"></i></a>
                                                            <a class="delete" href=""><i class="bi bi-trash-fill"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>


                </div>

            </div>
        </div>
    </div>

    <?php include('../../components/script.php') ?>
</body>

</html>