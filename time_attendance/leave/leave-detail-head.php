<?php
session_start();
session_regenerate_id(true);

include("../database/connectdb.php");
include('../components-desktop/head/include/header.php');
header("Cache-Control: no-cache, must-revalidate");

?>

<!-- Script -->
<link rel="stylesheet" href="node_modules\sweetalert2\dist\sweetalert2.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"
    integrity="sha256-oP6HI/t1f1qODW6fzZll4H5veNvJfyl26D8qx2vNc5A=" crossorigin="anonymous"></script>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/head/leave-detail-head.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/leave-detail.css">
<link rel=" stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="../assets/script/loader-normal.js"></script>

<?php

//---------------------------------------------------------------------------------------

// ทำความสะอาดและป้องกันข้อมูล
$_SESSION['card_id'] = isset($_SESSION['card_id']) ? $_SESSION['card_id'] : '';  // ตรวจสอบว่ามีค่าหรือไม่
$_SESSION['card_id'] = trim($_SESSION['card_id']);  // ลบช่องว่างที่อาจเกิดขึ้นทั้งด้านหน้าและด้านหลังของข้อมูล
$_SESSION['card_id'] = preg_replace('/[^a-zA-Z0-9]/', '', $_SESSION['card_id']);  // ลบทุกอักขระที่ไม่ใช่ a-z, A-Z, และ 0-9
$_SESSION['card_id'] = htmlspecialchars($_SESSION['card_id'], ENT_QUOTES, 'UTF-8');  // แปลงสัญลักษณ์ HTML เพื่อป้องกัน XSS

//---------------------------------------------------------------------------------------

// ทำความสะอาดและป้องกันข้อมูล
$id = isset($_GET['id']) ? $_GET['id'] : '';
$id = trim($id);
$id = preg_replace('/[^a-zA-Z0-9]/', '', $id);
$id = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');

$sql_leave_absence_record = sqlsrv_query($conn, "SELECT
absence_record.absence_record_id AS absence_record_id,
absence_type.absence_type_id AS absence_type_id,
absence_type.name AS absence_type_name,
absence_record.date_start AS date_start,
absence_record.date_end AS date_end,
absence_record.time_start AS time_start,
absence_record.time_end AS time_end,
absence_record.request_detail AS request_detail,
employee.scg_employee_id AS scg_employee_id,
employee.prefix_thai AS prefix_thai,
employee.firstname_thai AS firstname_thai,
employee.lastname_thai AS lastname_thai,
employee.employee_email AS employee_email,
employee.employee_image AS employee_image,
position.name_thai AS position_name,
cost_center.cost_center_code AS cost_center_code,
section.name_thai AS section_name,
department.name_thai AS department_name
FROM absence_record
LEFT JOIN absence_type ON absence_type.absence_type_id = absence_record.absence_type_id 
LEFT JOIN employee ON employee.card_id = absence_record.card_id
LEFT JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_payment_id
LEFT JOIN section ON section.section_id = cost_center.section_id
LEFT JOIN department ON department.department_id = section.department_id
LEFT JOIN position_info ON position_info.card_id = employee.card_id
LEFT JOIN position ON position.position_id = position_info.position_id
WHERE absence_record.absence_record_id = ?", array($id));
$row = sqlsrv_fetch_array($sql_leave_absence_record);

$startDateString = $row['date_start']->format('Y-m-d');
$endDateString = $row['date_end']->format('Y-m-d');

// Convert time parts to string
$timeStartString = $row['time_start']->format('H:i:s');
$timeEndString = $row['time_end']->format('H:i:s');

// Combine date and time strings
$startDateTimeString = $startDateString . ' ' . $timeStartString;
$endDateTimeString = $endDateString . ' ' . $timeEndString;

// Convert combined strings to DateTime objects
$startDateTime = new DateTime($startDateTimeString);
$endDateTime = new DateTime($endDateTimeString);

// Calculate the interval between start and end dates
$interval = $startDateTime->diff($endDateTime);

// Extract days and hours from the interval
$daysDuration = $interval->format('%a');
$hoursDuration = $interval->format('%h');
//---------------------------------------------------------------------------------------
switch ($row['absence_type_id']) {
    case 1:
        // 1 วันลาพักร้อน (วัน)
        $day_balance = $_SESSION["annual_leave_balance"];
        break;
    case 2:
        // 2 วันลาพักร้อนสะสม (วัน)
        $day_balance = $_SESSION["annual_leave_collect_balance"];
        break;
    case 3:
        // 3 วันลาคลอด (วัน)
        $day_balance = $_SESSION["maternity_leave_balance"];
        break;
    case 4:
        // 4 วันลาบวช (วัน)
        $day_balance = $_SESSION["ordination_leave_balance"];
        break;
    case 5:
        // 5 วันลาบวช ไม่ได้รับค่าจ้าง (วัน)
        $day_balance = $_SESSION["ordination_leave_nopaid_balance"];
        break;
    case 6:
        // 6 วันลาฮัจจี (วัน)
        $day_balance = $_SESSION["haj_leave_balance"];
        break;
    case 7:
        // 7 วันลาฮัจจี ไม่ได้รับค่าจ้าง (วัน)
        $day_balance = $_SESSION["haj_leave_nopaid_balance"];
        break;
    case 8:
        // 8 วันลาอบรม ไม่ได้รับค่าจ้าง (วัน)
        $day_balance = $_SESSION["training_leave_nopaid_balance"];
        break;
    case 9:
        // 9 วันลา CSR (วัน)
        $day_balance = $_SESSION["csr_leave_balance"];
        break;
    case 10:
        // 10 วันลาป่วยเกี่ยวกับการทำงาน (ชั่วโมง)
        $day_balance = $_SESSION["work_sick_leave_balance"];
        break;
    case 11:
        // 11 วันลาทหาร (วัน)
        $day_balance = $_SESSION["military_service_leave_balance"];
        break;
    case 12:
        // 12 วันลาอื่น ๆ (วัน)
        $day_balance = $_SESSION["other_leave_balance"];
        break;
    case 13:
        // 13 วันลาอื่น ๆ ไม่ได้รับค่าจ้าง (วัน)
        $day_balance = $_SESSION["other_leave_nopaid_balance"];
        break;
    case 14:
        // 14 วันลาป่วย (วัน)
        $day_balance = $_SESSION["sick_leave_balance"];
        break;
    default:
        // กรณีที่ไม่ตรงกับทุก case ข้างต้น
        break;
}

$remainingDays = $day_balance - $daysDuration;

// คำนวณจำนวนชั่วโมงที่เหลือ
$remainingHours = 0;
if ($remainingDays > 0) {
    $remainingHours = $remainingDays * 24; // ถ้ามีวันที่เหลือ คำนวณจำนวนชั่วโมงจากวันที่เหลือ
    $remainingHours -= $hoursDuration; // ลบชั่วโมงที่ใช้ไปจากการคำนวณ
    if ($remainingHours < 0) {
        $remainingHours = 0; // หากจำนวนชั่วโมงที่เหลือติดลบให้กำหนดให้เป็น 0
    }
    $remainingDays = $remainingDays - 1;
}
?>

<script>
function leave_approve_submit() {
    Swal.fire({
        title: "<strong>ยืนยันอนุมัติการลาหรือไม่</strong>",
        icon: "question",
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: `ตกลง`,
        cancelButtonText: `ยกเลิก`,
    }).then((result) => {
        if (result.isConfirmed) {
            leave_approve_confirm()
        } else {
            Swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
        }
    });
}

function leave_approve_confirm() {
    Swal.fire({
        title: "<strong>อนุมัติการลาสำเร็จ</strong>",
        icon: "success",
        showCloseButton: true,
        focusConfirm: false,
        confirmButtonText: `ตกลง`,
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href =
                '../processing/process_Leave_approve.php?id=<?php echo $row['absence_record_id'] ?>';
        }
    });
}

function leave_reject_submit() {
    Swal.fire({
        title: "<strong>ยืนยันไม่อนุมัติการลาหรือไม่</strong>",
        icon: "question",
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: `ตกลง`,
        cancelButtonText: `ยกเลิก`,
    }).then((result) => {
        if (result.isConfirmed) {
            leave_reject_confirm()
        } else {
            Swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
        }
    });
}

function leave_reject_confirm() {
    Swal.fire({
        title: "<strong>ไม่อนุมัติการลาสำเร็จ</strong>",
        icon: "success",
        showCloseButton: true,
        focusConfirm: false,
        confirmButtonText: `ตกลง`,
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href =
                '../processing/process_Leave_reject.php?id=<?php echo $row['absence_record_id'] ?>';
        }
    });
}

// --swal mobile---
function leave_approve() {
    Swal.fire({
        html: '<div style="font-weight: bold; font-size: 4vw;">ยืนยันการอนุมัติหรือไม่</div><br>' +
            '<img class="img" src="../IMG/question 1.png"></img>',
        padding: '2em',
        confirmButtonText: 'ตกลง',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#29ab29',
        cancelButtonColor: '#e1574b',
        showCancelButton: true,

        customClass: {
            confirmButtonText: 'swal2-confirm',
            cancelButtonText: 'swal2-cancel',
        },
    }).then((result) => {
        if (result.isConfirmed) {
            leave_approve_success()
        } else {
            Swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
        }
    });
}

function leave_approve_success() {
    Swal.fire({
        html: '<div style="font-weight: bold; font-size: 4vw;">อนุมัติการลาสำเร็จ</div><br>' +
            '<img class="img" src="../IMG/check1.png"></img>',
        padding: '2em',
        confirmButtonText: 'ยืนยัน',
        confirmButtonColor: '#29ab29',
        showCancelButton: false // ไม่แสดงปุ่มยกเลิก
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href =
                '../processing/process_Leave_approve.php?id=<?php echo $row['absence_record_id'] ?>';
        }
    });

}

function leave_reject() {
    Swal.fire({
        html: '<div style="font-weight: bold; font-size: 4vw;">ยืนยันการปฏิเสธหรือไม่</div><br>' +
            '<img class="img" src="../IMG/question 1.png"></img>',
        padding: '2em',
        confirmButtonText: 'ตกลง',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#29ab29',
        cancelButtonColor: '#e1574b',
        showCancelButton: true,

        customClass: {
            confirmButtonText: 'swal2-confirm',
            cancelButtonText: 'swal2-cancel',
        },
    }).then((result) => {
        if (result.isConfirmed) {
            leave_reject_success()
        } else {
            Swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
        }
    });
}

function leave_reject_success() {
    Swal.fire({
        html: '<div style="font-weight: bold; font-size: 4vw;">ปฏิเสธการลาสำเร็จ</div><br>' +
            '<img class="img" src="../IMG/check1.png"></img>',
        padding: '2em',
        confirmButtonText: 'ยืนยัน',
        confirmButtonColor: '#29ab29',
        showCancelButton: false // ไม่แสดงปุ่มยกเลิก
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href =
                '../processing/process_Leave_reject.php?id=<?php echo $row['absence_record_id'] ?>';
        }
    });

}
</script>
</head>

<body>
    <div class="desktop">
        <?php include('../components-desktop/head/include/sidebar.php'); ?>
        <?php include('../components-desktop/head/include/navbar.php'); ?>

        <div class=" main-container">
            <div class="pd-ltr-20 xs-pd-20-10">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <h2>รายละเอียดการลา : Leave Detail</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor:default;">การลา</a>
                                        </li>
                                        <li class=" breadcrumb-item">
                                            <a href="leave-approve-head.php">คำขอลาพนักงาน</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            รายละเอียดการลา
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10">
                                <div class="employee-image">
                                    <img src="<?php echo (!empty($row['employee_employee_image'])) ? '../../admin/uploads_img/' . $row['employee_employee_image'] : '../IMG/user.png'; ?>"
                                        alt="">
                                </div>
                                <div class="employee-info">
                                    <label>รหัสพนักงาน: <?= $row['scg_employee_id'] ?></label>
                                    <label>ชื่อ-สกุล:
                                        <?= $row['prefix_thai'] . $row['firstname_thai'] . " " . $row['lastname_thai'] ?></label>
                                    <label>ตำแหน่ง:
                                        <?= $row['position_name'] ?></label>
                                    <label>Cost Center: <?php echo $row['cost_center_code'] ?>
                                    </label>
                                    <label>หน่วยงาน: <?= $row['section_name'] ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="wizard-content">
                                    <section>
                                        <form>
                                            <div class="row">
                                                <div class="col-md-8 col-sm-12">
                                                    <div class="form-group text-center">
                                                        <label>ประเภทการลา</label>
                                                        <input type="hidden" value="<?= $row['absence_type_name'] ?>"
                                                            readonly>
                                                        <input class="form-control" type="text"
                                                            value="<?= $row['absence_type_name'] ?>" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8 col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group text-center">
                                                                <label>วันเริ่มต้น</label>
                                                                <input class="form-control" type="date"
                                                                    value="<?= $row['date_start']->format('d/m/Y') ?>"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group text-center">
                                                                <label>ถึงวันที่</label>
                                                                <input class="form-control" type="date"
                                                                    value="<?= $row['date_end']->format('d/m/Y') ?>"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8 col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group text-center">
                                                                <label>ตั้งแต่เวลา</label>
                                                                <input class="form-control" type="time"
                                                                    value="<?= $row['time_start']->format('H:i') ?>"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group text-center">
                                                                <label>ถึงเวลา</label>
                                                                <input class="form-control" type="time"
                                                                    value="<?= $row['time_end']->format('H:i') ?>"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8 col-sm-12">
                                                    <div class="form-group text-center">
                                                        <label>เหตุผล</label>
                                                        <input class="form-control" type="text"
                                                            value="<?php echo isset($row['request_detail']) ? $row['request_detail'] : '-'; ?>"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8 col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group text-center" name="duration">
                                                                <label>จำนวนการลา</label>
                                                                <label><a
                                                                        id="desktop-day-duration"><?= $daysDuration ?></a>
                                                                    วัน</label>
                                                                <label><a
                                                                        id="desktop-hour-duration"><?= $hoursDuration ?></a>
                                                                    ชม.</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group text-center" name="balance">
                                                                <label>สิทธิคงเหลือ</label>
                                                                <label><a
                                                                        id="desktop-day-balance"><?= $remainingDays  ?></a>
                                                                    วัน</label>
                                                                <label><a
                                                                        id="desktop-hour-balance"><?= gmdate("H . i ", $remainingHours * 3600); ?></a>
                                                                    ชม.</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </section>
                                    <br>
                                    <div class="row" style="display:flex;justify-content:center;">
                                        <div class="col-md-8 col-sm-12">
                                            <div class="form-group text-center"
                                                style="display:flex;justify-content:center;gap:20px;">
                                                <input type="button" value="อนุมัติ" class="btn-primary"
                                                    onclick="leave_approve_submit()">
                                                <input type="button" value="ปฏิเสธ" class="btn-danger"
                                                    onclick="leave_reject_submit()">
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


    <div class="mobile">
        <div class="navbar">
            <div class="div-span">
                <span>รายละเอียดการลา</span>
            </div>
        </div>

        <div class="imgTop">
            <img src="../IMG/user.png" alt="">
        </div>

        <div class="nameEm">
            <span><?= $row['scg_employee_id'] . ' - ' . $row['prefix_thai'] . $row['firstname_thai'] . " " . $row['lastname_thai'] ?></span>
        </div>

        <div class="dataEm">
            <div class="type">
                <span class="name">ประเภทการลา</span>
                <span>:</span>
                <input type="text" value="<?= $row['absence_type_name'] ?>" readonly>
            </div>
            <div class="datestart">
                <span class="name">วันเริ่มต้น</span>
                <span>:</span>
                <input type="text" value="<?= $row['date_start']->format('d/m/Y') ?>" readonly>
            </div>
            <div class="dateend">
                <span class="name">วันสิ้นสุด</span>
                <span>:</span>
                <input type="text" value="<?= $row['date_end']->format('d/m/Y') ?>" readonly>
            </div>
            <div class="timestart">
                <span class="name">เวลาเริ่มต้น</span>
                <span>:</span>
                <input type="text" value="<?= $row['time_start']->format('H:i น.') ?>" readonly>
            </div>
            <div class="timeend">
                <span class="name">เวลาสิ้นสุด</span>
                <span>:</span>
                <input type="text" value="<?= $row['time_end']->format('H:i น.') ?>" readonly>
            </div>
            <div class="sumdate">
                <span class="name">จำนวนวันลา</span>
                <span>:</span>
                <input type="text" value="<?= $daysDuration . ' วัน' ?>">
            </div>
            <div class="detail">
                <span class="name">รายละเอียดการลา</span>
                <span>:</span>
                <input type="text" value="<?php echo isset($row['request_detail']) ? $row['request_detail'] : '-'; ?>"
                    readonly>
            </div>
        </div>

        <div class="button-action">
            <div class="btn-submit">
                <input type="submit" value="อนุมัติ" onclick="leave_approve()" class="btnConfirm">
            </div>
            <div class="btn-reject">
                <input type="submit" value="ปฏิเสธ" onclick="leave_reject()" class="btnReject"><br><br>
            </div>
        </div>
    </div>
</body>
<?php include('../includes/footer.php') ?>