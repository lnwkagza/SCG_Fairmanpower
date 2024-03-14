<?php 
    session_start();
    require_once '../../connect/connect.php';
    if(!isset($_SESSION['user_login']) && !isset($_SESSION['admin_login'])){
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


<?php include('../../components/head.php') ?>
<link rel="stylesheet" href="../../plugin/bootstrap-icons-1.11.2/font/bootstrap-icons.css">
<link rel="stylesheet" href="../../plugin/boxicons-2.1.4/css/boxicons.min.css">
<link rel="stylesheet" href="../css/adminresults.css">
<title>Admin Page</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.3/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.3/dist/sweetalert2.all.min.js"></script>

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


    $sql1 = "SELECT * FROM Tablequiz WHERE chapter_id = ?";
    $params1 = array($_SESSION['chapterId']);
    $stmt1 = sqlsrv_query($conn, $sql1, $params1);
    $fullscore = 0;
    while ($row1 = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
        $fullscore++;
    }
    // echo $fullscore.' ';

    $sql2 = "SELECT * FROM Tabletrainningdata WHERE chapter_id = ? AND person_id = ?";
    $params2 = array($_SESSION['chapterId'],$user_id);
    $stmt2 = sqlsrv_query($conn, $sql2, $params2);
    $row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC);
    $score = $row2['score'];
    // echo $score.' ';
    $percentage = intval(($score / $fullscore) * 100);
?>

<?php include('../../components/navbar.php') ?>
    <?php include('../../components/sidebar.php') ?>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pl-10 pt-10">
            <div class="min-height-200px">
                <!-- <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <span id="head">เเบบทดสอบเพื่อวัดความรู้</span>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 mb-30">
                        <div class="card-box pd-10 pt-10 height-100-p">
                            <div class="bar">
                                <div class="mainclass">
                                    <div class="container">
                                        <div class="subcon1">
                                            <div class="title">แบบทดสอบเพื่อวัดความรู้ (Examination)</div>

                                            <?php
                                            if ($score >= ($fullscore * 0.8)) {
                                            ?>
                                                <div class="pass">ผ่าน</div>
                                            <?php
                                            } else {
                                            ?>
                                                <div class="nopass">ไม่ผ่าน</div>
                                            <?php
                                            }
                                            ?>

                                            <div class="score">คะเเนนของคุณ <?php echo $score; ?>/<?php echo $fullscore; ?> คะเเนน</div>
                                        </div>
                                        <div class="graph">
                                            <div class="semi-donut margin" style="--percentage : <?php echo $percentage ?> ; --fill: #0CBDC8 ;">
                                                <div class="percent"><?php echo $percentage ?>%</div>
                                            </div>
                                        </div>
                                        <div class="subcon2">
                                            <div class="fullcon">
                                                <div class="fullscore1">จำนวนคำถาม</div>
                                                <div class="fullscore2"><?php echo $fullscore; ?> ข้อ</div>
                                            </div>
                                            <div class="timecon">
                                                <div class="time1">จำกัดเวลา</div>
                                                <div class="time2">10 นาที</div>
                                            </div>
                                            <div class="criterioncon">
                                                <div class="criteriontitle">เกณฑ์การทำแบบทดสอบ</div>
                                                <div class="criterion">ผู้เรียนจะต้องทำะแนนให้ได้เกณฑ์มากกว่า 80%</div>
                                            </div>
                                        </div>
                                        <div class="buttoncon">
                                            <div class="buttoncon1"><a href="../../../admin/dashboard.php" class="button1">กลับหน้าเเรก</a></div>
                                            <div class="buttoncon2"><a href="admintakequizpage.php" class="button2">ทำแบบทดสอบอีกครั้ง</a></div>
                                        </div>
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