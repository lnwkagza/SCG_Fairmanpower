<?php
session_start();
require_once '../../connect/connect.php';
if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: ../../../../linelogin/index.html');
    exit();
}

// ถ้าผู้ใช้กด Logout
if (isset($_GET['logout']) && isset($_SESSION['admin_login'])) {
    // ลบ session และส่งผู้ใช้กลับไปยังหน้าล็อกอิน
    unset($_SESSION['admin_login']);
    $_SESSION['error'] = 'คุณได้ออกจากระบบแล้ว';
    header('location: ../../../../linelogin/index.html');
    exit();
}

if (isset($_GET['chapter_id'])) {
    $chapter_id = $_GET['chapter_id'];
    $_SESSION['last_viewed_lesson'] = $chapter_id; // เก็บ ID ของบทเรียนที่เข้าดูล่าสุดใน Session

    if (isset($_SESSION['last_viewed_lesson'])) {
        // echo $_SESSION['last_viewed_lesson'];
    }
}

if (!isset($_SESSION['last_viewed_lesson'])) {
    // echo ไม่มีค่า;
} else {
}
?>

<?php include('../../components/head.php') ?>
<link rel="stylesheet" href="../../plugin/bootstrap-icons-1.11.2/font/bootstrap-icons.css">
<link rel="stylesheet" href="../../plugin/boxicons-2.1.4/css/boxicons.min.css">
<link rel="stylesheet" href="../css/addtarget.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300&display=swap" rel="stylesheet">
<title>AdminPage</title>

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

    ?>
<?php include('../../components/navbar.php') ?>
    <?php include('../../components/sidebar.php') ?>
    <div class="main-container">
        <div class="pd-ltr-20 xs-pl-10 pt-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <span id="head">แก้ไขกลุ่มเป้าหมาย</span>
                                <span id="recommend">คำแนะนำ : โปรดค้นหากลุ่มเป้าหมายด้วย ชื่อ-สกุล แผนก หรือตำแหน่ง ในช่องพิมพ์ค้นหากลุ่มเป้าหมาย</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                        <div class="card-box pd-10 pt-10 height-100-p">
                            <div class="bar">
                                <div class="mainclass">
                                    <div class="addcourse">
                                        <!-- <div class="selectall"><button onclick="selectAllCheckboxes()">CheckAll</button></div> -->
                                        <form action="addtarget.php" method="POST">
                                            <div class="searchmanu">
                                                <div class="subsearchmanu">

                                                    <input type="text" name="search" placeholder="พิมพ์ค้นหากลุ่มเป้าหมาย" autocomplete="off">
                                                    <!-- <button type="submit">ค้นหา</button> -->
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="tablecourse">
                                    
                                            <?php
                                            if (!isset($_POST['search'])) {

                                                $search = $_SESSION['last_viewed_lesson'];
                                                $sql1 = "SELECT *, section.name_eng AS section_name_eng,
                                                department.name_eng AS department_name_eng ,employee.person_id AS emperson_id, Tablepositiontarget.chapter_id AS tarchapter,Tablepositiontarget.person_id AS tarperson FROM employee
                                                    LEFT JOIN Tablepositiontarget ON employee.person_id = Tablepositiontarget.person_id
                                                    LEFT JOIN Tablechapter ON Tablepositiontarget.chapter_id = Tablechapter.chapter_id
                                                    LEFT JOIN  cost_center ON employee.cost_center_organization_id = cost_center.cost_center_id
                                                    LEFT JOIN  section ON cost_center.section_id = section.section_id
                                                    LEFT JOIN  department ON section.department_id = department.department_id
                                                    WHERE Tablepositiontarget.chapter_id LIKE '%$search%'";
                                                $stmt1 = sqlsrv_query($conn, $sql1);
                                                if ($stmt1 === false) {
                                                    die(print_r(sqlsrv_errors(), true));
                                                }
                                                if (sqlsrv_has_rows($stmt1)) {
                                            ?>
                                            <table class="data-table table stripe hover nowrap">
                                            <tr>
                                                <th><input type="checkbox" onclick="selectAllCheckboxes()"></th>
                                                <th>รหัสพนักงาน</th>
                                                <th>ชื่อ-นามสกุล</th>
                                                <th>แผนก</th>
                                                <th>ตำเเหน่ง</th>
                                            </tr>
                                                    <form action="../../backend/db_edittarget.php" method="POST">
                                                        <?php
                                                        while ($row1 = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
                                                            $isChecked = ''; // กำหนดให้เริ่มต้นไม่ติ้ก
                                                            if (isset($row1['tarperson'])) {
                                                                $isChecked = 'checked'; // ถ้า person_id อยู่ใน session ที่เก็บไว้แล้ว ให้ติ้ก
                                                        ?>
                                                                <tr>
                                                                    <td>
                                                                        <input type="checkbox" name="myCheckbox[]" value="<?php echo $row1['person_id']; ?>" <?php echo $isChecked; ?>>
                                                                    </td>
                                                                    <td><?php echo $row1['scg_employee_id'] ?></td>
                                                                    <td><?php echo $row1['prefix_thai'] . ' ' . $row1['firstname_thai'] . ' ' . $row1['lastname_thai']; ?></td>
                                                                    <td><?php echo $row1['section_name_eng'] ?></td>
                                                                    <td><?php echo $row1['department_name_eng'] ?></td>
                                                                </tr>
                                                    <?php
                                                            }
                                                        }
                                                    } else {?>
                                                        <table class="data-table table stripe hover nowrap">
                                                        <tr>
                                                            <th><input type="checkbox" onclick="selectAllCheckboxes()"></th>
                                                            <th>รหัสพนักงาน</th>
                                                            <th>ชื่อ-นามสกุล</th>
                                                            <th>แผนก</th>
                                                            <th>ตำเเหน่ง</th>
                                                        </tr>
                                                        <tr><td colspan="5">ยังไม่ได้เลือกกลุ่มเป้าหมาย</td></tr>
                                                        <?php
                                                    }
                                                    sqlsrv_close($conn);
                                                    ?>
                                                        
                                                        </table><div class="buttonsubmmit"><button type="submit" name="save">บันทึก</button></div>
                                                    </form>
                                                <?php
                                            } else {
                                                ?>
                                                <table class="data-table table stripe hover nowrap">
                                                        <tr>
                                                            <th><input type="checkbox" onclick="selectAllCheckboxes()"></th>
                                                            <th>รหัสพนักงาน</th>
                                                            <th>ชื่อ-นามสกุล</th>
                                                            <th>แผนก</th>
                                                            <th>ตำเเหน่ง</th>
                                                        </tr>
                                                    <form action="../../backend/db_edittarget.php" method="POST">
                                                        
                                                        <?php
                                                        $search = $_POST['search'];
                                                        
                                                        $sql1 = "SELECT person_id, scg_employee_id, prefix_thai, firstname_thai, lastname_thai,
                                                        section.name_eng AS section_name_eng, department.name_eng AS department_name_eng
                                                        FROM employee
                                                        LEFT JOIN cost_center ON employee.cost_center_organization_id = cost_center.cost_center_id
                                                        LEFT JOIN section ON cost_center.section_id = section.section_id
                                                        LEFT JOIN department ON section.department_id = department.department_id
                                                        WHERE 
                                                        person_id LIKE N'%$search%' OR
                                                        scg_employee_id LIKE N'%$search%' OR
                                                        prefix_thai LIKE N'%$search%' OR
                                                        firstname_thai LIKE N'%$search%' OR
                                                        lastname_thai LIKE N'%$search%' OR
                                                        section.name_eng LIKE N'%$search%' OR
                                                        department.name_eng LIKE N'%$search%'";
                                                        $stmt1 = sqlsrv_query($conn, $sql1);
                                                        if ($stmt1 === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                        if (sqlsrv_has_rows($stmt1)) {
                                                            while ($row1 = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
                                                        ?>
                                                                <tr>
                                                                    <td><input type="checkbox" name="myCheckbox[]" value="<?php echo $row1['person_id']; ?>"> </td>
                                                                    <td><?php echo $row1['scg_employee_id'] ?></td>
                                                                    <td><?php echo $row1['prefix_thai'] . ' ' . $row1['firstname_thai'] . ' ' . $row1['lastname_thai']; ?></td>
                                                                    <td><?php echo $row1['section_name_eng'] ?></td>
                                                                    <td><?php echo $row1['department_name_eng'] ?></td>
                                                                </tr>
                                                        <?php
                                                            }
                                                        } else {
                                                            echo '<tr><td colspan="5">ไม่พบข้อมูล</td></tr>';
                                                        }
                                                        sqlsrv_close($conn);
                                                        ?>
                                                        
                                                        
                                                    </table><div class="buttonsubmmit"><button type="submit" name="save">บันทึก</button></div>
                                                    </form>
                                                <?php
                                            }
                                                ?>
                                        
                                        
                                    </div>

                                    <!-- <a href="uploadadminedit.php"><button class="backbutton"><i class='bx bx-left-arrow-alt'></i></button></a> -->
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
<script>
    function selectAllCheckboxes() {
        // หาทุก input element ที่มี type เป็น checkbox ในตาราง
        const checkboxes = document.querySelectorAll('table input[type="checkbox"]');

        // วนลูปเพื่อสลับ (toggle) การเลือก-ไม่เลือก ทุก checkbox
        checkboxes.forEach(checkbox => {
            checkbox.checked = !checkbox.checked;
        });
    }
</script>
