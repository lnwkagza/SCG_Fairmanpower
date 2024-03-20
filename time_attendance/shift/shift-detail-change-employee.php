<?php
session_start();
session_regenerate_id(true);
//---------------------------------------------------------------------------------------
header("Cache-Control: no-cache, must-revalidate");
include("../database/connectdb.php");
include('../components-desktop/employee/include/header.php');
//---------------------------------------------------------------------------------------

$shiftChangeId = $_GET['shift_change_id']; // Assuming you're getting the shift_change_id from a request, adjust this based on your input method (GET, POST, etc.)

$sql = "
    SELECT 
        date,
        employee.firstname_thai AS emp_firstname,
        employee.lastname_thai AS emp_lastname,
        inspector.firstname_thai AS inspector_firstname,
        inspector.lastname_thai AS inspector_lastname,
        approver.firstname_thai AS approver_firstname,
        approver.lastname_thai AS approver_lastname,
        old_shift_id,
        new_shift_id,
        approve_status,
        request_detail
    FROM 
        shift_change 
    INNER JOIN 
        employee ON shift_change.card_id = employee.card_id
    INNER JOIN 
        shift_type AS old_shift ON shift_change.old_shift_id = old_shift.shift_type_id
    INNER JOIN 
        shift_type AS new_shift ON shift_change.new_shift_id = new_shift.shift_type_id
    LEFT JOIN 
        employee AS inspector ON shift_change.inspector = inspector.card_id
    LEFT JOIN 
        employee AS approver ON shift_change.approver = approver.card_id
    WHERE 
        shift_change.shift_change_id = ?"; // Use parameterized query to avoid SQL injection

// Prepare and bind parameters
$params = array($shiftChangeId);
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$stmt = sqlsrv_query($conn, $sql, $params, $options);


// Fetch data and output
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
// Output or process data as needed
$shiftTypeMapping = array(
    'DD01' => 'normal1',
    'DD02' => 'normal2',
    'HOLIDAY' => 'นักขัต',
    'LEAVE' => 'ลา',
    'OFF' => 'หยุด',
    'SA01' => 'กะ 1',
    'SB01' => 'กะ 2',
    'SC01' => 'กะ 3',
    'TRAIN' => 'อบรม'
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
                                    <h2>รายละเอียดคำขออนุมัติการขอเปลี่ยนกะ</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor:default;">กะการทำงาน</a>
                                        </li>
                                        <li class=" breadcrumb-item">
                                            <a href="shift-request-employee.php">คำขออนุมัติ</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            รายละเอียดคำขออนุมัติการขอเปลี่ยนกะ
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
                                                            <label>ชื่อพนักงาน:</label>
                                                            <span><?= $row['emp_firstname'] . " " . $row['emp_lastname'] ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="shift">
                                                            <label>กะเดิม:</label>
                                                            <div class="shift-new">
                                                                <span><?= $shiftTypeMapping[$row['old_shift_id']] ?></span>
                                                                <label>กะใหม่:</label>
                                                                <span><?= $shiftTypeMapping[$row['new_shift_id']] ?></span>
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
                                                            <label>ผู้ตรวจสอบ:</label>
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
                        <span><?= $row['emp_firstname'] . " " . $row['emp_lastname'] ?></span>
                    </div>
                </div>
                <div class="display-shift">
                    <div class="shift-old">
                        <span>กะเดิม</span>
                        <div class="old">
                            <span><?= $shiftTypeMapping[$row['old_shift_id']] ?></span>
                        </div>
                    </div>
                    <div class="shift-new">
                        <span>กะใหม่</span>
                        <div class="new">
                            <span><?= $shiftTypeMapping[$row['new_shift_id']] ?></span>
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
                    <span>ผู้ตรวจสอบ</span>
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
<?php include('..\includes\footer.php') ?>