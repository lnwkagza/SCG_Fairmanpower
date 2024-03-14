<?php 
    session_start();
    require_once '../../connect/connect.php';
    if(!isset($_SESSION['user_login']) ){
        $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
        header('location: ../../../../linelogin/index.html');
        exit();
    }

    // ถ้าผู้ใช้กด Logout
    if(isset($_GET['logout']) && isset($_SESSION['admin_login'])) {
        // ลบ session และส่งผู้ใช้กลับไปยังหน้าล็อกอิน
        unset($_SESSION['admin_login']);
        $_SESSION['error'] = 'คุณได้ออกจากระบบแล้ว';
        header('location: ../../../../linelogin/index.html');
        exit();
    }


?>
<?php include('../../components/head.php') ?>
<link rel="stylesheet" href="../../plugin/bootstrap-icons-1.11.2/font/bootstrap-icons.css">
<link rel="stylesheet" href="../../plugin/boxicons-2.1.4/css/boxicons.min.css">
<link rel="stylesheet" href="../css/userlearnsummary.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.3/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.3/dist/sweetalert2.all.min.js"></script>
<title>Admin Page</title>

<body>


<?php

$displayedCourses[]=null;
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

    
    $search_query = isset($_GET['search']) ? $_GET['search'] : '';
    // $sql1 = "SELECT Tablechapter.course_id, course_name, chapter_name, status_VDO, status_total
    //         FROM Tablecourse 
    //         LEFT JOIN Tablechapter ON Tablechapter.course_id = Tablecourse.course_id
    //         LEFT JOIN Tablepositiontarget ON Tablepositiontarget.chapter_id = Tablechapter.chapter_id
    //         LEFT JOIN Tabletrainningdata ON Tabletrainningdata.person_id = Tablepositiontarget.person_id
    //                                 AND Tabletrainningdata.chapter_id = Tablechapter.chapter_id
    //         WHERE Tablepositiontarget.person_id = ? AND (Tablecourse.course_id LIKE '%$search_query%' OR Tablecourse.course_name LIKE '%$search_query%' OR Tablechapter.chapter_name LIKE '%$search_query%')
    //         ORDER BY Tablechapter.course_id DESC";
    $sql1 = "SELECT 
        Tablechapter.course_id, 
        course_name, 
        chapter_name, 
        status_VDO, 
        status_total,
        ojt_chapter,
        SUM(ojt_manday) AS total_ojt_manday,
        COUNT(ojt_chapter) AS duplicate_count
    FROM 
        Tablecourse 
        LEFT JOIN Tablechapter ON Tablechapter.course_id = Tablecourse.course_id
        LEFT JOIN Tablepositiontarget ON Tablepositiontarget.chapter_id = Tablechapter.chapter_id
        LEFT JOIN Tabletrainningdata ON Tabletrainningdata.person_id = Tablepositiontarget.person_id
                                    AND Tabletrainningdata.chapter_id = Tablechapter.chapter_id
        LEFT JOIN TableOJT ON TableOJT.ojt_chapter = Tablechapter.chapter_id
    WHERE 
        Tablepositiontarget.person_id = ? AND (Tablecourse.course_id LIKE N'%$search_query%' OR Tablecourse.course_name LIKE N'%$search_query%' OR Tablechapter.chapter_name LIKE N'%$search_query%')
    GROUP BY
        Tablechapter.course_id, 
        course_name, 
        chapter_name, 
        status_VDO, 
        status_total,
        ojt_chapter
    ORDER BY 
        Tablechapter.course_id DESC
    ";
    

    $params1 = array($user_id);
    $stmt1 = sqlsrv_query($conn, $sql1, $params1);
    if( $stmt === false ) {
        die( print_r( sqlsrv_errors(), true));
    }

    // $sql2 = "SELECT Tablecourse.course_id,course_name,chapter_name,status_VDO,status_total,score,Tablepositiontarget.person_id
    // FROM Tablecourse 
    // LEFT JOIN Tablechapter ON Tablechapter.course_id = Tablecourse.course_id
    // LEFT JOIN Tabletrainningdata ON Tabletrainningdata.chapter_id = Tablechapter.chapter_id
    // LEFT JOIN Tablepositiontarget ON Tablepositiontarget.chapter_id = Tablechapter.chapter_id
    //                                 AND Tablepositiontarget.person_id = Tabletrainningdata.person_id
    // WHERE Tablechapter.general_target = 1
    // AND (Tablechapter.chapter_id IS NULL OR Tabletrainningdata.person_id = ? OR Tabletrainningdata.person_id IS NULL)
    // AND Tablepositiontarget.chapter_id IS NULL
    // ORDER BY Tablechapter.course_id DESC";
    $sql2 = "SELECT 
        Tablecourse.course_id,
        course_name,
        chapter_name,
        status_VDO,
        status_total,
        score,

        SUM(ojt_manday) AS total_ojt_manday,
        COUNT(ojt_chapter) AS duplicate_count
    FROM 
        Tablecourse 
        LEFT JOIN Tablechapter ON Tablechapter.course_id = Tablecourse.course_id
        LEFT JOIN Tabletrainningdata ON Tabletrainningdata.chapter_id = Tablechapter.chapter_id
        LEFT JOIN TableOJT ON TableOJT.ojt_chapter = Tablechapter.chapter_id
    WHERE 
        Tablechapter.general_target = 1
        AND (Tablechapter.chapter_id IS NULL OR Tabletrainningdata.person_id = ? OR Tabletrainningdata.person_id IS NULL)

    GROUP BY 
        Tablecourse.course_id,
        course_name,
        chapter_name,
        status_VDO,
        status_total,
        score

    ORDER BY 
        Tablecourse.course_id DESC";
    $params2 = array($user_id);
    $stmt2 = sqlsrv_query($conn, $sql2, $params2);
    if( $stmt === false ) {
    die( print_r( sqlsrv_errors(), true));
    }

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
                                <span id="head">สรุปผลการเรียนรู้</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 mb-30">
                        <div class="card-box pd-10 pt-10 height-100-p">
                            <div class="bar">
                                <div class="mainclass">
                                    <div class="contianer">

                                        <div class="searchcon">
                                            <form action="" method="GET">
                                                <div class="searchbar">
                                                    <div class="inputsearch">
                                                        <i class='bx bx-search'></i>
                                                        <input type="text" name="search" placeholder="Search..." autocomplete="off">
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="tablecon">

                                            <div class="tablecourse">
                                                <h1>หลักสูตรหลัก</h1>
                                                <table>
                                                    <tr>
                                                        <th class="th1">หลักสูตรหลัก</th>
                                                        <th>การเรียนรู้</th>
                                                        <th>เเบบทดสอบ</th>
                                                        <th>สถานะ</th>
                                                        <th>จำนวนครั้ง</th>
                                                        <th class="th2">Manday</th>
                                                    </tr>
                                                    <?php $prev_course_name = null; ?>
                                                    <?php while ($row1 = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {

                                                    ?>
                                                        <tr>
                                                            <?php if ($row1['course_name'] !== $prev_course_name) { ?>
                                                                <?php $prev_course_name = $row1['course_name']; ?>
                                                                <td colspan='6' class="td1"><?php echo $row1['course_id']; ?> : <?php echo $row1['course_name']; ?></td>
                                                            <?php } ?>
                                                        </tr>
                                                        <?php if ($row1['chapter_name'] !== null) {
                                                            $displayedCourses[] = $row1['chapter_name'];
                                                        ?>
                                                            <tr>
                                                                <td class="td2"><?php echo $row1['chapter_name']; ?></td>

                                                                <td><?php echo ($row1['status_VDO'] === 1) ? '<i class="bi bi-play"></i>100%' : '<i class="bi bi-play"></i>0%'; ?></td>

                                                                <td>
                                                                    <?php
                                                                    if ($row1['status_total'] === null) {
                                                                        echo '<div class = "status1">ทดสอบ</div>';
                                                                    } elseif ($row1['status_total'] === 1) {
                                                                        echo '<div class = "status2">ทดสอบ</div>';
                                                                    } elseif ($row1['status_total'] === 2) {
                                                                        echo '<div class = "status3">ทดสอบ</div>';
                                                                    }
                                                                    ?>
                                                                </td>

                                                                <td>
                                                                    <?php
                                                                    if ($row1['status_total'] === null) {
                                                                        echo '<div class = "statusfull1">ยังไม่เรียน</div>';
                                                                    } elseif ($row1['status_total'] === 1) {
                                                                        echo '<div class = "statusfull2">รอสอบ</div>';
                                                                    } elseif ($row1['status_total'] === 2) {
                                                                        echo '<div class = "statusfull3">เสร็จสิ้น</div>';
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php if ($row1['duplicate_count'] !== 0) {
                                                                        echo $row1['duplicate_count'];
                                                                    } else {
                                                                        echo 'ยังไม่OJT';
                                                                    }

                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php if ($row1['total_ojt_manday'] !== null) {
                                                                        echo $row1['total_ojt_manday'];
                                                                    } else {
                                                                        echo 'ยังไม่OJT';
                                                                    }

                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </table>
                                            </div>

                                            <div class="tablecourse1">
                                                <h1>หลักสูตรอื่นๆ</h1>
                                                <table>
                                                    <tr>
                                                        <th class="th1">หลักสูตรอื่นๆ</th>
                                                        <th>การเรียนรู้</th>
                                                        <th>เเบบทดสอบ</th>
                                                        <th>สถานะ</th>
                                                        <th>จำนวนครั้ง</th>
                                                        <th class="th2">Manday</th>
                                                    </tr>
                                                    <?php $prev_course_name = null; ?>
                                                    <?php while ($row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {

                                                    ?>
                                                        <tr>

                                                            <?php if (!empty($row2['chapter_name']) && !in_array($row2['chapter_name'], $displayedCourses) && $row2['course_name'] !== $prev_course_name) { ?>
                                                                <?php $prev_course_name = $row2['course_name']; ?>
                                                                <td colspan='6' class="td1"><?php echo $row2['course_id']; ?> : <?php echo $row2['course_name']; ?></td>
                                                            <?php } ?>
                                                        </tr>
                                                        <?php
                                                        if (!in_array($row2['chapter_name'], $displayedCourses)) {
                                                            if ($row2['chapter_name'] !== null) { ?>
                                                                <tr>
                                                                    <td class="td2"><?php echo $row2['chapter_name']; ?></td>

                                                                    <td><?php echo ($row2['status_VDO'] === 1) ? '<i class="bi bi-play"></i>100%' : '<i class="bi bi-play"></i>0%'; ?></td>

                                                                    <td>
                                                                        <?php
                                                                        if ($row2['status_total'] === null) {
                                                                            echo '<div class = "status1">ทดสอบ</div>';
                                                                        } elseif ($row2['status_total'] === 1) {
                                                                            echo '<div class = "status2">ทดสอบ</div>';
                                                                        } elseif ($row2['status_total'] === 2) {
                                                                            echo '<div class = "status3">ทดสอบ</div>';
                                                                        }
                                                                        ?>
                                                                    </td>

                                                                    <td>
                                                                        <?php
                                                                        if ($row2['status_total'] === null) {
                                                                            echo '<div class = "statusfull1">ยังไม่เรียน</div>';
                                                                        } elseif ($row2['status_total'] === 1) {
                                                                            echo '<div class = "statusfull2">รอสอบ</div>';
                                                                        } elseif ($row2['status_total'] === 2) {
                                                                            echo '<div class = "statusfull3">เสร็จสิ้น</div>';
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php if ($row2['duplicate_count'] !== 0) {
                                                                            echo $row2['duplicate_count'];
                                                                        } else {
                                                                            echo 'ยังไม่OJT';
                                                                        }

                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php if ($row2['total_ojt_manday'] !== null) {
                                                                            echo $row2['total_ojt_manday'];
                                                                        } else {
                                                                            echo 'ยังไม่OJT';
                                                                        }

                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    <?php } ?>
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
        </div>
    </div>
    <?php include('../../components/script.php') ?>
</body>

</html>