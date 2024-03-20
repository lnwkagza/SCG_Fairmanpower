<?php
session_start();
session_regenerate_id(true);
//---------------------------------------------------------------------------------------
header("Cache-Control: no-cache, must-revalidate");
include("../database/connectdb.php");
include('../components-desktop/employee/include/header.php');
//---------------------------------------------------------------------------------------
$shiftswitchId = $_GET['shift_switch_id']; // Assuming you're getting the shift_switch_id from a request, adjust this based on your input method (GET, POST, etc.)

$sql = "SELECT 
        date,
        employee.firstname_thai AS emp_firstname,
        employee.lastname_thai AS emp_lastname,
        employee1.firstname_thai AS employee1_firstname,
        employee1.lastname_thai AS employee1_lastname,
        employee2.firstname_thai AS employee2_firstname,
        employee2.lastname_thai AS employee2_lastname,
        employee3.firstname_thai AS employee3_firstname,
        employee3.lastname_thai AS employee3_lastname,
        shift_type1.symbol AS employee1_old_shift_symbol,
        shift_type2.symbol AS employee2_old_shift_symbol,
        shift_type3.symbol AS employee3_old_shift_symbol,
        new_shift_type1.symbol AS employee1_new_shift_symbol,
        new_shift_type2.symbol AS employee2_new_shift_symbol,  -- Corrected alias
        new_shift_type3.symbol AS employee3_new_shift_symbol,
                    
        sub_team_name1.name AS employee1_sub_team_name1,
        sub_team_name2.name AS employee2_sub_team_name2,  -- Corrected alias
        sub_team_name3.name AS employee3_sub_team_name3,
                    
        inspector.firstname_thai AS inspector_firstname,
        inspector.lastname_thai AS inspector_lastname,
        approver.firstname_thai AS approver_firstname,
        approver.lastname_thai AS approver_lastname,
        shift_switch.approve_status,
        shift_switch.request_detail
        FROM 
        shift_switch 
        INNER JOIN 
        employee ON shift_switch.request_card_id = employee.card_id
        LEFT JOIN 
        employee AS employee1 ON shift_switch.employee_1 = employee1.card_id
        LEFT JOIN 
        employee AS employee2 ON shift_switch.employee_2 = employee2.card_id
        LEFT JOIN 
        employee AS employee3 ON shift_switch.employee_3 = employee3.card_id
        LEFT JOIN 
        shift_type AS shift_type1 ON shift_switch.old_shift_1 = shift_type1.shift_type_id
        LEFT JOIN 
        shift_type AS shift_type2 ON shift_switch.old_shift_2 = shift_type2.shift_type_id
        LEFT JOIN 
        shift_type AS shift_type3 ON shift_switch.old_shift_3 = shift_type3.shift_type_id
        LEFT JOIN 
        shift_type AS new_shift_type1 ON shift_switch.new_shift_1 = new_shift_type1.shift_type_id
        LEFT JOIN 
        shift_type AS new_shift_type2 ON shift_switch.new_shift_2 = new_shift_type2.shift_type_id
        LEFT JOIN 
        shift_type AS new_shift_type3 ON shift_switch.new_shift_3 = new_shift_type3.shift_type_id
                    
        LEFT JOIN 
        sub_team AS sub_team_name1 ON employee1.sub_team_id = sub_team_name1.sub_team_id
        LEFT JOIN 
        sub_team AS sub_team_name2 ON employee2.sub_team_id = sub_team_name2.sub_team_id
        LEFT JOIN 
        sub_team AS sub_team_name3 ON employee3.sub_team_id = sub_team_name3.sub_team_id
                    
        LEFT JOIN 
        employee AS inspector ON shift_switch.inspector = inspector.card_id
        LEFT JOIN 
        employee AS approver ON shift_switch.approver = approver.card_id
        WHERE 
        shift_switch.shift_switch_id = ?"; // Use parameterized query to avoid SQL injection

// Prepare and bind parameters
$params = array($shiftswitchId);
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$stmt = sqlsrv_query($conn, $sql, $params, $options);


// Fetch data and output
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
// Output or process data as needed
$shiftTypeMapping = array(
    'normal1' => 'ปกติ',
    'normal2' => 'ปกติ',
    'นักขัต' => 'นักขัต',
    'ลา' => 'ลา',
    'หยุด' => 'หยุด',
    '1' => 'กะ 1',
    '2' => 'กะ 2',
    '3' => 'กะ 3',
    'อบรม' => 'อบรม'
);
?>
<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/employee/shift-detail-employee.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/shift-request-detail.css">
<link rel=" stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="script/loader-normal.js"></script>

</head>

<body>
    <div class="desktop">
        <?php include('../components-desktop/employee/include/sidebar.php'); ?>
        <?php include('../components-desktop/employee/include/navbar.php'); ?>

        <div class="main-container">
            <div class="pd-ltr-20 xs-pd-20-10">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <h2>รายละเอียดคำขออนุมัติการขอสลับกะ</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor:default;">กะการทำงาน</a>
                                        </li>
                                        <li class=" breadcrumb-item">
                                            <a href="shift-request-employee.php">คำขออนุมัต/</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            รายละเอียดคำขออนุมัติการขอสลับกะ
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class=" row">
                        <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="wizard-content">
                                    <form>
                                        <section>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="date">
                                                            <label>วันที่ทำรายการ:</label>
                                                            <span><?= $row['date']->format('Y/m/d') ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="employee">
                                                            <label>ชื่อพนักงาน 1:</label>
                                                            <span><?= $row['employee1_firstname'] . " " . $row['employee1_lastname'] ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="team">
                                                            <label>รายชื่อทีม:</label>
                                                            <span><?= $row['employee1_sub_team_name1'] ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="switch-shift">
                                                            <label>กะเดิม:</label>
                                                            <div class="shift-new">
                                                                <span><?= $shiftTypeMapping[$row['employee1_old_shift_symbol']] ?></span>
                                                                <label>กะใหม่:</label>
                                                                <span><?= $shiftTypeMapping[$row['employee1_new_shift_symbol']] ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="employee">
                                                            <label>ชื่อพนักงาน 2:</label>
                                                            <?= $row['employee2_firstname'] . " " . $row['employee2_lastname'] ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="team">
                                                            <label>รายชื่อทีม:</label>
                                                            <span><?= $row['employee2_sub_team_name2'] ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="switch-shift">
                                                            <label>กะเดิม:</label>
                                                            <div class="shift-new">
                                                                <span><?= $shiftTypeMapping[$row['employee2_old_shift_symbol']] ?></span>
                                                                <label>กะใหม่:</label>
                                                                <span><?= $shiftTypeMapping[$row['employee2_new_shift_symbol']] ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="employee">
                                                            <label>ชื่อพนักงาน 3:</label>
                                                            <?= $row['employee3_firstname'] . " " . $row['employee3_lastname'] ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="team">
                                                            <label>รายชื่อทีม:</label>
                                                            <span><?= $row['employee3_sub_team_name3'] ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="switch-shift">
                                                            <label>กะเดิม:</label>
                                                            <div class="shift-new">
                                                                <span><?= $shiftTypeMapping[$row['employee3_old_shift_symbol']] ?></span>
                                                                <label>กะใหม่:</label>
                                                                <span><?= $shiftTypeMapping[$row['employee3_new_shift_symbol']] ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="detail">
                                                            <label>เหตุผล:</label>
                                                            <span><?= $row['request_detail'] ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="inspector">
                                                            <label>ผู้ตรวจสอบ (ถ้ามี):</label>
                                                            <span><?= $row['inspector_firstname'] . " " . $row['inspector_lastname'] ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="head">
                                                            <label>หัวหน้า:</label>
                                                            <span><?= $row['approver_firstname'] . " " . $row['approver_lastname'] ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="display-status">
                                                            <label>สถานะ:</label>
                                                            <?php if ($row['approve_status'] == "approve") : ?>
                                                                <span class="approve">อนุมัติแล้ว</span>
                                                            <?php elseif ($row['approve_status'] == "waiting") : ?>
                                                                <span class="wait">รออนุมัติ</span>
                                                            <?php elseif ($row['approve_status'] == "reject") : ?>
                                                                <span class="reject">ปฏิเสธ</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mobile">
        <div class="navbar">
            <div class="div-span">
                <span>รายละเอียดคำขออนุมัติ</span>
            </div>
        </div>

        <div class="container">
            <div class="display-topic">
                <span>รายละเอียดคำขออนุมัติการจัดการทีม</span>
            </div>
            <div class="display-detail">

                <div class="display-date">
                    <span>วันที่ทำรายการ</span>
                    <div class="date">
                        <span><?= $row['date']->format('Y/m/d') ?></span>
                    </div>
                </div>
                <div class="display-name">
                    <span>ชื่อพนักงาน</span>
                    <div class="name">
                        <span><?= $row['employee1_firstname'] . " " . $row['employee1_lastname'] ?></span>
                    </div>
                </div>
                <div class="name-team">
                    <span>รายชื่อทีม</span>
                    <div class="team-name">
                        <span><?= $row['employee1_sub_team_name1'] ?></span>
                    </div>
                </div>
                <div class="display-shift">
                    <div class="shift-old">
                        <span>กะเดิม</span>
                        <div class="old">
                            <span><?= $shiftTypeMapping[$row['employee1_old_shift_symbol']] ?></span>
                        </div>
                    </div>
                    <div class="shift-new">
                        <span>กะใหม่</span>
                        <div class="new">
                            <span><?= $shiftTypeMapping[$row['employee1_new_shift_symbol']] ?></span>
                        </div>
                    </div>
                </div>
                <div class="display-name">
                    <span>ชื่อพนักงาน</span>
                    <div class="name">
                        <span><?= $row['employee2_firstname'] . " " . $row['employee2_lastname'] ?></span>
                    </div>
                </div>
                <div class="name-team">
                    <span>รายชื่อทีม</span>
                    <div class="team-name">
                        <span><?= $row['employee2_sub_team_name2'] ?></span>
                    </div>
                </div>
                <div class="display-shift">
                    <div class="shift-old">
                        <span>กะเดิม</span>
                        <div class="old">
                            <span><?= $shiftTypeMapping[$row['employee2_old_shift_symbol']] ?></span>
                        </div>
                    </div>
                    <div class="shift-new">
                        <span>กะใหม่</span>
                        <div class="new">
                            <span><?= $shiftTypeMapping[$row['employee2_new_shift_symbol']] ?></span>
                        </div>
                    </div>
                </div>
                <div class="display-name">
                    <span>ชื่อพนักงาน</span>
                    <div class="name">
                        <span><?= $row['employee3_firstname'] . " " . $row['employee3_lastname'] ?></span>
                    </div>
                </div>
                <div class="name-team">
                    <span>รายชื่อทีม</span>
                    <div class="team-name">
                        <span><?= $row['employee3_sub_team_name3'] ?></span>
                    </div>
                </div>
                <div class="display-shift">
                    <div class="shift-old">
                        <span>กะเดิม</span>
                        <div class="old">
                            <span><?= $shiftTypeMapping[$row['employee3_old_shift_symbol']] ?></span>
                        </div>
                    </div>
                    <div class="shift-new">
                        <span>กะใหม่</span>
                        <div class="new">
                            <span><?= $shiftTypeMapping[$row['employee3_new_shift_symbol']] ?></span>
                        </div>
                    </div>
                </div>
                <div class="reason">
                    <span>เหตุผล</span>
                    <div class="reason-req">
                        <span><?= $row['request_detail'] ?></span>
                    </div>
                </div>
                <div class="display-inspector">
                    <span>ผู้ตรวจสอบ (ถ้ามี):</span>
                    <div class="inspector">
                        <span><?= $row['inspector_firstname'] . " " . $row['inspector_lastname'] ?></span>
                    </div>
                </div>
                <div class="display-head">
                    <span>หัวหน้า</span>
                    <div class="head">
                        <span><?= $row['approver_firstname'] . " " . $row['approver_lastname'] ?></span>
                    </div>
                </div>
                <div class="display-status">
                    <?php if ($row['approve_status'] == "approve") : ?>
                        <span class="approve">อนุมัติแล้ว</span>
                    <?php elseif ($row['approve_status'] == "waiting") : ?>
                        <span class="wait">รออนุมัติ</span>
                    <?php elseif ($row['approve_status'] == "reject") : ?>
                        <span class="reject">ปฏิเสธ</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include('../includes/footer.php') ?>