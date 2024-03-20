<?php
session_start();
include('../components-desktop/employee/include/header.php');
include("../database/connectdb.php");
?>
<!-- เทส -->
<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/employee/approval-employee.css">

<!-- CSS Mobile -->
<link rel="stylesheet" href="../assets/css/approve-status-timeEm.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/navbar.css">

<link rel="stylesheet" href="../assets/css/loader.css">

<?php

$query = "SELECT * FROM employee INNER JOIN work_format ON employee.work_format_code = work_format.work_format_code WHERE card_id = ?";
$params = array($_SESSION["card_id"]);
$sql_card_id = sqlsrv_query($conn, $query, $params);
$rowg = sqlsrv_fetch_array($sql_card_id);

$sql_absence_waiting = sqlsrv_query($conn, "SELECT * FROM absence_record 
INNER JOIN absence_type ON absence_record.absence_type_id = absence_type.absence_type_id
INNER JOIN employee ON absence_record.card_id = employee.card_id
WHERE absence_record.card_id = ? AND approve_status = ?
", array($_SESSION["card_id"], "waiting"));

$absence_waiting = array();
while ($row = sqlsrv_fetch_array($sql_absence_waiting, SQLSRV_FETCH_ASSOC)) {
    $absence_waiting[] = $row;
}

$sql_check_inout_waiting = sqlsrv_query($conn, "SELECT *
FROM check_inout 
LEFT JOIN employee ON check_inout.card_id = employee.card_id
WHERE check_inout.card_id = ? AND approve_status = ?;
", array($_SESSION["card_id"], "waiting"));

$checkin_waiting = array();
while ($row = sqlsrv_fetch_array($sql_check_inout_waiting, SQLSRV_FETCH_ASSOC)) {
    $checkin_waiting[] = $row;
}

$sql_day_off_request_waiting = sqlsrv_query($conn, "SELECT * FROM day_off_request 
INNER JOIN work_format ON day_off_request.edit_work_format_code = work_format.work_format_code
INNER JOIN employee ON day_off_request.approver = employee.card_id 
WHERE day_off_request.card_id = ? AND day_off_request.approve_status = ?
", array($_SESSION["card_id"], "waiting"));

$dayoff_waiting = array();
while ($row = sqlsrv_fetch_array($sql_day_off_request_waiting, SQLSRV_FETCH_ASSOC)) {
    $dayoff_waiting[] = $row;
}

// Fetch shift add data
$select_shift_add_data_query = "SELECT shift_add_id,approve_status,firstname_thai, lastname_thai, date, 
before_shift_type.symbol AS before_shift_type,
add_shift_type.symbol AS add_shift_type, 
request_detail,
prefix_thai,
scg_employee_id,
employee_email,
employee_image
FROM shift_add
INNER JOIN employee ON shift_add.request_card_id = employee.card_id
INNER JOIN shift_type AS before_shift_type ON shift_add.before_shift_type_id = before_shift_type.shift_type_id
INNER JOIN shift_type AS add_shift_type ON shift_add.add_shift_type_id = add_shift_type.shift_type_id
WHERE shift_add.card_id = ? AND shift_add.approve_status = ? ORDER BY shift_add.date ASC;";

// Prepare and execute the SQL statement for shift add data
$shift_add_data_stmt = sqlsrv_prepare($conn, $select_shift_add_data_query, array($_SESSION["card_id"], "waiting"));
sqlsrv_execute($shift_add_data_stmt);

// Fetch shift add data and store it in an array
$shiftAddData = array();
while ($row = sqlsrv_fetch_array($shift_add_data_stmt, SQLSRV_FETCH_ASSOC)) {
    $shiftAddData[] = $row;
}

// Fetch shift change data
$select_shift_change_data_query = "SELECT shift_change_id,approve_status,firstname_thai, lastname_thai, date, 
    before_shift_type.symbol AS before_shift_type,
    new_shift_type.symbol AS new_shift_type, 
    prefix_thai,
    request_detail,
    scg_employee_id,
    employee_email,
    employee_image
    FROM shift_change
    INNER JOIN employee ON shift_change.request_card_id = employee.card_id
    INNER JOIN shift_type AS before_shift_type ON shift_change.old_shift_id = before_shift_type.shift_type_id
    INNER JOIN shift_type AS new_shift_type ON shift_change.new_shift_id = new_shift_type.shift_type_id
    WHERE shift_change.card_id =  ? AND shift_change.approve_status = ? ORDER BY shift_change.date ASC;";

// Prepare and execute the SQL statement for shift change data
$shift_change_data_stmt = sqlsrv_prepare($conn, $select_shift_change_data_query, array($_SESSION["card_id"], "waiting"));
sqlsrv_execute($shift_change_data_stmt);

// Fetch shift change data and store it in an array
$shiftChangeData = array();
while ($row = sqlsrv_fetch_array($shift_change_data_stmt, SQLSRV_FETCH_ASSOC)) {
    $shiftChangeData[] = $row;
}

// Fetch shift switch data
$select_shift_switch_data_query = "SELECT shift_switch_id,
    employee.firstname_thai,
    employee.lastname_thai,
    scg_employee_id,
    employee_email,
    employee_image,
    prefix_thai,
    shift_switch.date,
    shift_switch.request_detail,
    shift_switch.approve_status
    FROM shift_switch
    INNER JOIN employee ON shift_switch.request_card_id = employee.card_id
    WHERE shift_switch.request_card_id = ? AND shift_switch.approve_status = ? ORDER BY shift_switch.date ASC;";

// Prepare and execute the SQL statement for shift switch data
$shift_switch_data_stmt = sqlsrv_prepare($conn, $select_shift_switch_data_query, array($_SESSION["card_id"], "waiting"));
sqlsrv_execute($shift_switch_data_stmt);

// Fetch shift switch data and store it in an array
$shiftSwitchData = array();
while ($row = sqlsrv_fetch_array($shift_switch_data_stmt, SQLSRV_FETCH_ASSOC)) {
    $shiftSwitchData[] = $row;
}

// Fetch shift lock data
$select_shift_lock_data_query = "SELECT shift_lock_id,approve_status,employee.firstname_thai, employee.lastname_thai, shift_lock.date, shift_type.symbol, shift_lock.request_detail,
prefix_thai,
scg_employee_id,
employee_email,
employee_image
 FROM shift_lock
 INNER JOIN employee ON shift_lock.request_card_id = employee.card_id
 INNER JOIN shift_type ON shift_lock.shift_type_id = shift_type.shift_type_id
 WHERE shift_lock.card_id = ? AND shift_lock.approve_status = ? ORDER BY shift_lock.date ASC;";


// Prepare and execute the SQL statement for shift lock data
$shift_lock_data_stmt = sqlsrv_prepare($conn, $select_shift_lock_data_query, array($_SESSION["card_id"], "waiting"));
sqlsrv_execute($shift_lock_data_stmt);

// Fetch shift lock data and store it in an array
$shiftLockData = array();
while ($row = sqlsrv_fetch_array($shift_lock_data_stmt, SQLSRV_FETCH_ASSOC)) {
    $shiftLockData[] = $row;
}

$select_sub_team_data_query = "SELECT
    sub_team.sub_team_id,
    sub_team.approve_status, 
    sub_team.request_time, 
    employee.firstname_thai, 
    employee.lastname_thai,
    employee.prefix_thai,
    employee.scg_employee_id,
    employee.employee_email,
    employee.employee_image
FROM
    sub_team
INNER JOIN
    employee
ON 
    sub_team.request_card_id = employee.card_id
WHERE 
    sub_team.request_card_id = ?
    AND sub_team.approve_status = ? ";

// Prepare and execute the SQL statement for sub team data
$sub_team_data_stmt = sqlsrv_prepare($conn, $select_sub_team_data_query, array($_SESSION["card_id"], "waiting"));
sqlsrv_execute($sub_team_data_stmt);

// Fetch sub team data and store it in an array
$sub_teamData = array();
while ($row = sqlsrv_fetch_array($sub_team_data_stmt, SQLSRV_FETCH_ASSOC)) {
    $sub_teamData[] = $row;
}

$time_stamp_YEAR = date("Y");
$time_stamp_MONTH = date("M");
$current_date = date("F");
$englishMonths = array(
    'January' => 'มกราคม',
    'February' => 'กุมภาพันธ์',
    'March' => 'มีนาคม',
    'April' => 'เมษายน',
    'May' => 'พฤษภาคม',
    'June' => 'มิถุนายน',
    'July' => 'กรกฎาคม',
    'August' => 'สิงหาคม',
    'September' => 'กันยายน',
    'October' => 'ตุลาคม',
    'November' => 'พฤศจิกายน',
    'December' => 'ธันวาคม',
);
$thaiYear = date("Y") + 543;
$thaiMonthYear = strtr($current_date, $englishMonths) . " " . $thaiYear;

?>

<script>
    function submit_back() {
        window.history.back();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const btnList = document.querySelector('.btn-list');
        const tables = document.querySelectorAll('.table-container');

        // Set initial state
        tables.forEach((table, index) => {
            if (index === 0) {
                table.style.display = 'block';
                btnList.children[index].classList.add('active');
            } else {
                table.style.display = 'none';
                btnList.children[index].classList.remove('active');
            }
        });

        btnList.addEventListener('click', function(event) {
            if (event.target.tagName === 'BUTTON') {
                const buttonIndex = Array.from(btnList.children).indexOf(event.target);

                // Hide all tables
                tables.forEach(table => table.style.display = 'none');

                // Show the selected table
                tables[buttonIndex].style.display = '';

                // Remove 'active' class from all buttons
                btnList.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));

                // Add 'active' class to the clicked button
                event.target.classList.add('active');
            }
        });
    });

    // สร้างฟังก์ชันเรียกใช้เมื่อหน้าเว็บโหลดเสร็จ
    window.onload = function() {
        // เรียกใช้ showStatus และส่งพารามิเตอร์เป็น 'normal' เพื่อให้แสดงข้อมูลสถานะเริ่มต้น
        showStatus('normal');
    };

    function showStatus(status) {
        // ซ่อนทั้งหมดก่อน
        document.querySelectorAll('.status-detail > div').forEach(function(div) {
            div.style.display = 'none';
        });

        // แสดงเฉพาะที่เลือก
        document.querySelector('.display-' + status).style.display = 'block';

        // แสดงเป็น modal
        selectedTable.classList.add('modal');
    }

    function showTable(table) {
        if (table === 'checkin') {
            document.getElementById('box1').style.display = '';
            document.getElementById('box2').style.display = 'none';
            document.getElementById('box3').style.display = 'none';
            document.getElementById('box4').style.display = 'none';
        } else if (table === 'leave') {
            document.getElementById('box1').style.display = 'none';
            document.getElementById('box2').style.display = '';
            document.getElementById('box3').style.display = 'none';
            document.getElementById('box4').style.display = 'none';
        } else if (table === 'dayoff') {
            document.getElementById('box1').style.display = 'none';
            document.getElementById('box2').style.display = 'none';
            document.getElementById('box3').style.display = '';
            document.getElementById('box4').style.display = 'none';
        } else if (table === 'shift') {
            document.getElementById('box1').style.display = 'none';
            document.getElementById('box2').style.display = 'none';
            document.getElementById('box3').style.display = 'none';
            document.getElementById('box4').style.display = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const btnList = document.querySelector('.button-bar');
        const tables = document.querySelectorAll('.desktop-table-container ');

        // Set initial state
        tables.forEach((table, index) => {
            if (index === 0) {
                table.style.display = 'block';
                btnList.children[index].classList.add('active');
            } else {
                table.style.display = 'none';
                btnList.children[index].classList.remove('active');
            }
        });

        btnList.addEventListener('click', function(event) {
            if (event.target.tagName === 'BUTTON') {
                const buttonIndex = Array.from(btnList.children).indexOf(event.target);

                // Hide all tables
                tables.forEach(table => table.style.display = 'none');

                // Show the selected table
                tables[buttonIndex].style.display = '';

                // Remove 'active' class from all buttons
                btnList.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));

                // Add 'active' class to the clicked button
                event.target.classList.add('active');
            }
        });
    });
</script>
</head>

<body>
    <div id="loader"></div>
    <div style="display:none;" id="Content" class="animate-bottom">

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
                                        <h2>รายการรออนุมัติ</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-bar-container">
                            <div class="table-bar">
                                <div>
                                    <button onclick="showTable('checkin')">
                                        <img src="../IMG/ci.png" alt="">เช็กอิน
                                    </button>
                                </div>
                                <div>
                                    <button onclick="showTable('leave')">
                                        <img src="../IMG/leave-4.png" alt="">การลา
                                    </button>
                                </div>
                                <div>
                                    <button onclick="showTable('dayoff')">
                                        <img src="../IMG/day-off-1.png" alt="">วันหยุด
                                    </button>
                                </div>
                                <div>
                                    <button onclick="showTable('shift')">
                                        <img src="../IMG/shift.png" alt="">กะ
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-lg-12 col-md-6 col-sm-12 mb-30" id="box1" style="display:none;">
                                <div class="card-box pd-30 pt-10 height-100-p">
                                    <div class="pt-3 pb-3">
                                        <div class="desktop-table">
                                            <table class="table table-striped" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>รหัส</th>
                                                        <th>ชื่อ-สกุล</th>
                                                        <th>วันที่</th>
                                                        <th>IN</th>
                                                        <th>OUT</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($checkin_waiting as $row) { ?>
                                                        <tr>
                                                            <td><?= $row['scg_employee_id'] ?></td>
                                                            <td><?= $row['firstname_thai'] . ' ' . $row['lastname_thai'] ?>
                                                            </td>
                                                            <td><?= $row['date']->format('d-m-Y') ?></td>
                                                            <td><?= $row['edit_time_in']->format('H:i') ?></td>
                                                            <td><?= $row['edit_time_out']->format('H:i') ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-6 col-sm-12 mb-30" id="box2" style="display:none;">
                                <div class="card-box pd-30 pt-10 height-100-p">
                                    <div class="pt-3 pb-3">
                                        <div class="desktop-table">
                                            <table class="table table-striped" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>รหัส</th>
                                                        <th>ชื่อ-สกุล</th>
                                                        <th>วันที่</th>
                                                        <th>ประเภท</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($absence_waiting as $row) { ?>
                                                        <tr>
                                                            <td><?= $row['scg_employee_id'] ?></td>
                                                            <td><?= $row['firstname_thai'] . ' ' . $row['lastname_thai'] ?>
                                                            </td>
                                                            <td><?= $row['date_start']->format('d-m-Y') ?></td>
                                                            <td><?= $row['name'] ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-6 col-sm-12 mb-30" id="box3" style="display:none;">
                                <div class="card-box pd-30 pt-10 height-100-p">
                                    <div class="pt-3 pb-3">
                                        <div class="desktop-table">
                                            <table class="table table-striped" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>วันเดิม</th>
                                                        <th>วันใหม่</th>
                                                        <th>ผู้อนุมัติ</th>
                                                        <th>สถานะ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($dayoff_waiting as $row) { ?>
                                                        <tr>
                                                            <td><?= $rowg['day_off1'] . "-" . $rowg['day_off2'] ?></td>
                                                            <td><?= $row['day_off1'] . "-" . $row['day_off2'] ?>
                                                            </td>
                                                            <td><?= $row['firstname_thai'] . " " . $row['lastname_thai'] ?>
                                                            </td>
                                                            <td style="color:orange !important;">รออนุมัติ</td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-6 col-sm-12 mb-30" id="box4" style="display:none;">
                                <div class="card-box pd-30 pt-10 height-100-p">
                                    <div class="pt-3 pb-3">
                                        <div class="button-bar">
                                            <button id="btn1" class="">จัดการทีม</button>
                                            <button id="btn2" class="">เปลี่ยนกะ</button>
                                            <button id="btn3" class="">สลับกะ</button>
                                            <button id="btn4" class="">เพิ่มกะ</button>
                                            <button id="btn5" class="">ล็อกเหลี่ยม</button>
                                        </div>

                                        <div class="desktop-table-container table1">
                                            <table id="table1" class="table table-striped" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th class="status">สถานะ</th>
                                                        <th class="name">ผู้ยื่นอนุมัติ</th>
                                                        <th class="date">วันที่ยื่นอนุมัติ</th>
                                                        <th class="action">การจัดการ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($sub_teamData as $row) : ?>
                                                        <tr>
                                                            <td class="status">
                                                                <?php // ตรวจสอบ $row['approve_status'] และแปลงเป็นข้อความที่กำหนด
                                                                if ($row['approve_status'] === 'waiting') {
                                                                    $statusText = 'รออนุมัติ';
                                                                } elseif ($row['approve_status'] === 'approve') {
                                                                    $statusText = 'อนุมัติ';
                                                                } elseif ($row['approve_status'] === 'reject') {
                                                                    $statusText = 'ไม่อนุมัติ';
                                                                } else {
                                                                    $statusText = 'ไม่ทราบสถานะ'; // หรือข้อความที่คุณต้องการสำหรับสถานะที่ไม่รู้จัก
                                                                }

                                                                // ใช้ $statusText ต่อไปในโค้ดของคุณ
                                                                echo $statusText;
                                                                ?>
                                                            </td>
                                                            <td class="name">
                                                                <div class="row">
                                                                    <div style="margin-right: 10px;margin-left: 5px;">
                                                                        <img src="<?php echo (!empty($row['employee_image'])) ? '../../admin/uploads_img/' . $row['employee_image'] : '../IMG/user.png'; ?>" class="border-radius-100 shadow" width="40" height="40" alt="">
                                                                    </div>
                                                                    <div>
                                                                        <b><?= $row['scg_employee_id'] . " " . $row['prefix_thai'] . $row['firstname_thai'] . ' ' . $row['lastname_thai']; ?></b><br>
                                                                        <a class="text-primary"><?= $row['employee_email'] ?></a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="date" style="text-align: center;">
                                                                <?= $row['request_time']->format('d-m-Y'); ?>
                                                            </td>
                                                            <td class="action"><button class="btn btn-info" onclick="window.location.href='shift-detail-manage-employee.php?sub_team_id=<?= $row['sub_team_id']; ?>'">รายละเอียด</button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="desktop-table-container table2" style="display:none;">
                                            <table id="table2" class="table table-striped" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th class="status">สถานะ</th>
                                                        <th class="name">ผู้ยื่นอนุมัติ</th>
                                                        <th class="date">วันที่ยื่นอนุมัติ</th>
                                                        <th class=" action">การจัดการ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($shiftChangeData as $row) : ?>
                                                        <tr>
                                                            <td class="status">
                                                                <?php // ตรวจสอบ $row['approve_status'] และแปลงเป็นข้อความที่กำหนด
                                                                if ($row['approve_status'] === 'waiting') {
                                                                    $statusText = 'รออนุมัติ';
                                                                } elseif ($row['approve_status'] === 'approve') {
                                                                    $statusText = 'อนุมัติ';
                                                                } elseif ($row['approve_status'] === 'reject') {
                                                                    $statusText = 'ไม่อนุมัติ';
                                                                } else {
                                                                    $statusText = 'ไม่ทราบสถานะ'; // หรือข้อความที่คุณต้องการสำหรับสถานะที่ไม่รู้จัก
                                                                }

                                                                // ใช้ $statusText ต่อไปในโค้ดของคุณ
                                                                echo $statusText;
                                                                ?>
                                                            </td>
                                                            <td class="name">
                                                                <div class="row">
                                                                    <div style="margin-right: 10px;margin-left: 5px;">
                                                                        <img src="<?php echo (!empty($row['employee_image'])) ? '../../admin/uploads_img/' . $row['employee_image'] : '../IMG/user.png'; ?>" class="border-radius-100 shadow" width="40" height="40" alt="">
                                                                    </div>
                                                                    <div>
                                                                        <b><?= $row['scg_employee_id'] . " " . $row['prefix_thai'] . $row['firstname_thai'] . ' ' . $row['lastname_thai']; ?></b><br>
                                                                        <a class="text-primary"><?= $row['employee_email'] ?></a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="date">
                                                                <?= $row['input_timestamp']->format('d-m-Y'); ?>
                                                            </td>
                                                            <td class="action"><button class="btn btn-info" onclick="window.location.href='../shift/shift-detail-change-employee.php?shift_change_id=<?= $row['shift_change_id']; ?>'">รายละเอียด</button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="desktop-table-container table3" style="display:none;">
                                            <table id="table3" class="table table-striped" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th class="status">สถานะ</th>
                                                        <th class="name">ผู้ยื่นอนุมัติ</th>
                                                        <th class="date">วันที่ยื่นอนุมัติ</th>
                                                        <th class=" action">การจัดการ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($shiftSwitchData as $row) : ?>
                                                        <tr>
                                                            <td class="status">
                                                                <?php // ตรวจสอบ $row['approve_status'] และแปลงเป็นข้อความที่กำหนด
                                                                if ($row['approve_status'] === 'waiting') {
                                                                    $statusText = 'รออนุมัติ';
                                                                } elseif ($row['approve_status'] === 'approve') {
                                                                    $statusText = 'อนุมัติ';
                                                                } elseif ($row['approve_status'] === 'reject') {
                                                                    $statusText = 'ไม่อนุมัติ';
                                                                } else {
                                                                    $statusText = 'ไม่ทราบสถานะ'; // หรือข้อความที่คุณต้องการสำหรับสถานะที่ไม่รู้จัก
                                                                }

                                                                // ใช้ $statusText ต่อไปในโค้ดของคุณ
                                                                echo $statusText;
                                                                ?>
                                                            </td>
                                                            <td class="name">
                                                                <div class="row">
                                                                    <div style="margin-right: 10px;margin-left: 5px;">
                                                                        <img src="<?php echo (!empty($row['employee_image'])) ? '../../admin/uploads_img/' . $row['employee_image'] : '../IMG/user.png'; ?>" class="border-radius-100 shadow" width="40" height="40" alt="">
                                                                    </div>
                                                                    <div>
                                                                        <b><?= $row['scg_employee_id'] . " " . $row['prefix_thai'] . $row['firstname_thai'] . ' ' . $row['lastname_thai']; ?></b><br>
                                                                        <a class="text-primary"><?= $row['employee_email'] ?></a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="date">
                                                                <?= $row['input_timestamp']->format('d-m-Y'); ?>
                                                            </td>
                                                            <td class="action"><button class="btn btn-info" onclick="window.location.href='../shift/shift-detail-switch-employee.php?shift_switch_id=<?= $row['shift_switch_id']; ?>'">รายละเอียด</button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="desktop-table-container table4" style="display:none;">
                                            <table id="table4" class="table table-striped" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th class="status">สถานะ</th>
                                                        <th class="name">ผู้ยื่นอนุมัติ</th>
                                                        <th class="date">วันที่ยื่นอนุมัติ</th>
                                                        <th class=" action">การจัดการ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($shiftAddData as $row) : ?>
                                                        <tr>
                                                            <td class="status">
                                                                <?php // ตรวจสอบ $row['approve_status'] และแปลงเป็นข้อความที่กำหนด
                                                                if ($row['approve_status'] === 'waiting') {
                                                                    $statusText = 'รออนุมัติ';
                                                                } elseif ($row['approve_status'] === 'approve') {
                                                                    $statusText = 'อนุมัติ';
                                                                } elseif ($row['approve_status'] === 'reject') {
                                                                    $statusText = 'ไม่อนุมัติ';
                                                                } else {
                                                                    $statusText = 'ไม่ทราบสถานะ'; // หรือข้อความที่คุณต้องการสำหรับสถานะที่ไม่รู้จัก
                                                                }

                                                                // ใช้ $statusText ต่อไปในโค้ดของคุณ
                                                                echo $statusText;
                                                                ?>
                                                            </td>
                                                            <td class="name">
                                                                <div class="row">
                                                                    <div style="margin-right: 10px;margin-left: 5px;">
                                                                        <img src="<?php echo (!empty($row['employee_image'])) ? '../../admin/uploads_img/' . $row['employee_image'] : '../IMG/user.png'; ?>" class="border-radius-100 shadow" width="40" height="40" alt="">
                                                                    </div>
                                                                    <div>
                                                                        <b><?= $row['scg_employee_id'] . " " . $row['prefix_thai'] . $row['firstname_thai'] . ' ' . $row['lastname_thai']; ?></b><br>
                                                                        <a class="text-primary"><?= $row['employee_email'] ?></a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="date">
                                                                <?= $row['input_timestamp']->format('d-m-Y'); ?>
                                                            </td>
                                                            <td class="action"><button class="btn btn-info" onclick="window.location.href='../shift/shift-detail-add-employee.php?shift_add_id=<?= $row['shift_add_id']; ?>'">รายละเอียด</button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="desktop-table-container table5" style="display:none;">
                                            <table id="table5" class="table table-striped" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th class="status">สถานะ</th>
                                                        <th class="name">ผู้ยื่นอนุมัติ</th>
                                                        <th class="date">วันที่ยื่นอนุมัติ</th>
                                                        <th class=" action">การจัดการ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($shiftLockData as $row) : ?>
                                                        <tr>
                                                            <td class="status">
                                                                <?php // ตรวจสอบ $row['approve_status'] และแปลงเป็นข้อความที่กำหนด
                                                                if ($row['approve_status'] === 'waiting') {
                                                                    $statusText = 'รออนุมัติ';
                                                                } elseif ($row['approve_status'] === 'approve') {
                                                                    $statusText = 'อนุมัติ';
                                                                } elseif ($row['approve_status'] === 'reject') {
                                                                    $statusText = 'ไม่อนุมัติ';
                                                                } else {
                                                                    $statusText = 'ไม่ทราบสถานะ'; // หรือข้อความที่คุณต้องการสำหรับสถานะที่ไม่รู้จัก
                                                                }

                                                                // ใช้ $statusText ต่อไปในโค้ดของคุณ
                                                                echo $statusText;
                                                                ?>
                                                            </td>
                                                            <td class="name">
                                                                <div class="row">
                                                                    <div style="margin-right: 10px;margin-left: 5px;">
                                                                        <img src="<?php echo (!empty($row['employee_image'])) ? '../../admin/uploads_img/' . $row['employee_image'] : '../IMG/user.png'; ?>" class="border-radius-100 shadow" width="40" height="40" alt="">
                                                                    </div>
                                                                    <div>
                                                                        <b><?= $row['scg_employee_id'] . " " . $row['prefix_thai'] . $row['firstname_thai'] . ' ' . $row['lastname_thai']; ?></b><br>
                                                                        <a class="text-primary"><?= $row['employee_email'] ?></a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="date">
                                                                <?= $row['input_timestamp']->format('d-m-Y'); ?>
                                                            </td>
                                                            <td class="action"><button class="btn btn-info" onclick="window.location.href='../shift/shift-detail-lock-employee.php?shift_lock_id=<?= $row['shift_lock_id']; ?>'">รายละเอียด</button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
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

        <div class="mobile">
            <div class="navbar">
                <div class="div-span">
                    <span>รายการรออนุมัติ</span>
                </div>
            </div>
            <div class="btn-status">
                <div class="btn-select">
                    <div class="btn-normal">
                        <button onclick="showStatus('normal')"><img src="../IMG/ci.png" alt="">เช็กอิน</button>
                    </div>
                    <div class="btn-leave">
                        <button onclick="showStatus('leave')"><img src="../IMG/leave-4.png" alt="">ลา</button>
                    </div>
                    <div class="btn-late">
                        <button onclick="showStatus('late')"><img src="../IMG/day-off-1.png" alt="">วันหยุด</button>
                    </div>
                    <div class="btn-goBack">
                        <button onclick="showStatus('goBack')"><img src="../IMG/shift.png" alt="">กะ</button>
                    </div>
                </div>
            </div>

            <div class="status-detail">

                <div class="display-normal">
                    <!-- เช็คอิน -->
                    <table>
                        <?php
                        echo '<tr>';
                        echo '<th>รหัส</th>';
                        echo '<th>ชื่อ - สกุล</th>';
                        echo '<th>วันที่</th>';
                        echo '<th>in</th>';
                        echo '<th>out</th>';
                        echo '</tr>';

                        foreach ($checkin_waiting as $row) {
                            echo '<tr>';
                            echo '<td>' . $row['scg_employee_id'] . '</td>';
                            echo '<td>' . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . '</td>';
                            echo '<td class="date">' . $row['date']->format('d-m-Y') . '</td>';
                            echo '<td>' . $row['edit_time_in']->format('H:i') . '</td>';
                            echo '<td>' . $row['edit_time_out']->format('H:i') . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </table>
                </div>

                <div class="display-late">
                    <!-- วันหยุด -->
                    <table>
                        <?php
                        echo '<tr>';
                        echo '<th>วันเดิม</th>';
                        echo '<th>วันใหม่</th>';
                        echo '<th>ผู้อนุมัติ</th>';
                        echo '<th>สถานะ</th>';
                        echo '<th></th>';
                        echo '</tr>';

                        foreach ($dayoff_waiting as $row) {
                            echo '<tr>';
                            echo '<td>' . $rowg['day_off1'] . "-" . $rowg['day_off2'] . '</td>';
                            echo '<td>' . $row['day_off1'] . "-" . $row['day_off2'] . '</td>';
                            echo '<td>' . $row['firstname_thai'] . " " . $row['lastname_thai'] . '</td>';
                            echo '<td style="color:orange;">รออนุมัติ</td>';
                            echo '<td><a href="../dayoff/day-off-detail-status-employee.php?id=' . $row['day_off_req_id'] . '"><img src="../IMG/edit.png" style="width: 20px; height: 20px;"></a></td>';
                            echo '</tr>';
                        }
                        // End the HTML table
                        ?>
                    </table>
                </div>

                <div class="display-goBack">

                    <div class="btn-list">
                        <button id="btn1">จัดการทีม</button>
                        <button id="btn2">เปลี่ยนกะ</button>
                        <button id="btn3">สลับกะ</button>
                        <button id="btn4">เพิ่มกะ</button>
                        <button id="btn5">ล็อกเหลี่ยม</button>
                    </div>

                    <div class="table-container table1">
                        <table id="example1" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="status">สถานะ</th>
                                    <th>ผู้ขอ</th>
                                    <th>วันที่</th>
                                    <th class="action"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sub_teamData as $row) : ?>
                                    <tr>
                                        <td class="status">
                                            <?php // ตรวจสอบ $row['approve_status'] และแปลงเป็นข้อความที่กำหนด
                                            if ($row['approve_status'] === 'waiting') {
                                                $statusText = 'รออนุมัติ';
                                            } elseif ($row['approve_status'] === 'approve') {
                                                $statusText = 'อนุมัติ';
                                            } elseif ($row['approve_status'] === 'reject') {
                                                $statusText = 'ไม่อนุมัติ';
                                            } else {
                                                $statusText = 'ไม่ทราบสถานะ'; // หรือข้อความที่คุณต้องการสำหรับสถานะที่ไม่รู้จัก
                                            }

                                            // ใช้ $statusText ต่อไปในโค้ดของคุณ
                                            echo $statusText;
                                            ?>
                                        </td>
                                        <td class="name">
                                            <?php echo $row['firstname_thai'] . ' ' . $row['lastname_thai']; ?>
                                        </td>
                                        <td class="date">
                                            <?php echo $row['request_time']->format('d-m-Y'); ?>
                                        </td>
                                        <td class="action"><button class="detail" onclick="window.location.href='../shift/shift-detail-manage-employee.php?sub_team_id=<?= $row['sub_team_id']; ?>'">รายละเอียด</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-container table2">
                        <table id="example2" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="status">สถานะ</th>
                                    <th>ผู้ขอ</th>
                                    <th>วันที่</th>
                                    <th class="action"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($shiftChangeData as $row) : ?>
                                    <tr>
                                        <td class="status">
                                            <?php // ตรวจสอบ $row['approve_status'] และแปลงเป็นข้อความที่กำหนด
                                            if ($row['approve_status'] === 'waiting') {
                                                $statusText = 'รออนุมัติ';
                                            } elseif ($row['approve_status'] === 'approve') {
                                                $statusText = 'อนุมัติ';
                                            } elseif ($row['approve_status'] === 'reject') {
                                                $statusText = 'ไม่อนุมัติ';
                                            } else {
                                                $statusText = 'ไม่ทราบสถานะ'; // หรือข้อความที่คุณต้องการสำหรับสถานะที่ไม่รู้จัก
                                            }

                                            // ใช้ $statusText ต่อไปในโค้ดของคุณ
                                            echo $statusText;
                                            ?>
                                        </td>
                                        <td class="name">
                                            <?php echo $row['firstname_thai'] . ' ' . $row['lastname_thai']; ?>
                                        </td>
                                        <td class="date">
                                            <?php echo $row['date']->format('d-m-Y'); ?>
                                        </td>
                                        <td class="action"><button class="detail" onclick="window.location.href='../shift/shift-detail-change-employee.php?shift_change_id=<?= $row['shift_change_id']; ?>'">รายละเอียด</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-container table3">
                        <table id="example3" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="status">สถานะ</th>
                                    <th>ผู้ขอ</th>
                                    <th>วันที่</th>
                                    <th class="action"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($shiftSwitchData as $row) : ?>
                                    <tr>
                                        <td class="status">
                                            <?php // ตรวจสอบ $row['approve_status'] และแปลงเป็นข้อความที่กำหนด
                                            if ($row['approve_status'] === 'waiting') {
                                                $statusText = 'รออนุมัติ';
                                            } elseif ($row['approve_status'] === 'approve') {
                                                $statusText = 'อนุมัติ';
                                            } elseif ($row['approve_status'] === 'reject') {
                                                $statusText = 'ไม่อนุมัติ';
                                            } else {
                                                $statusText = 'ไม่ทราบสถานะ'; // หรือข้อความที่คุณต้องการสำหรับสถานะที่ไม่รู้จัก
                                            }

                                            // ใช้ $statusText ต่อไปในโค้ดของคุณ
                                            echo $statusText;
                                            ?>
                                        </td>
                                        <td class="name">
                                            <?php echo $row['firstname_thai'] . ' ' . $row['lastname_thai']; ?>
                                        </td>
                                        <td class="date">
                                            <?php echo $row['date']->format('d-m-Y'); ?>
                                        </td>
                                        <td class="action"><button class="detail" onclick="window.location.href='../shift/shift-detail-switch-employee.php?shift_switch_id=<?= $row['shift_switch_id']; ?>'">รายละเอียด</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-container table4">
                        <table id="example4" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="status">สถานะ</th>
                                    <th>ผู้ขอ</th>
                                    <th>วันที่</th>
                                    <th class="action"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($shiftAddData as $row) : ?>
                                    <tr>
                                        <td class="status">
                                            <?php // ตรวจสอบ $row['approve_status'] และแปลงเป็นข้อความที่กำหนด
                                            if ($row['approve_status'] === 'waiting') {
                                                $statusText = 'รออนุมัติ';
                                            } elseif ($row['approve_status'] === 'approve') {
                                                $statusText = 'อนุมัติ';
                                            } elseif ($row['approve_status'] === 'reject') {
                                                $statusText = 'ไม่อนุมัติ';
                                            } else {
                                                $statusText = 'ไม่ทราบสถานะ'; // หรือข้อความที่คุณต้องการสำหรับสถานะที่ไม่รู้จัก
                                            }

                                            // ใช้ $statusText ต่อไปในโค้ดของคุณ
                                            echo $statusText;
                                            ?>
                                        </td>
                                        <td class="name">
                                            <?php echo $row['firstname_thai'] . ' ' . $row['lastname_thai']; ?>
                                        </td>
                                        <td class="date">
                                            <?php echo $row['date']->format('d-m-Y'); ?>
                                        </td>
                                        <td class="action"><button class="detail" onclick="window.location.href='../shift/shift-detail-add-employee.php?shift_add_id=<?= $row['shift_add_id']; ?>'">รายละเอียด</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-container table5">
                        <table id="example5" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="status">สถานะ</th>
                                    <th>ผู้ขอ</th>
                                    <th>วันที่</th>
                                    <th class="action"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($shiftLockData as $row) : ?>
                                    <tr>
                                        <td class="status">
                                            <?php // ตรวจสอบ $row['approve_status'] และแปลงเป็นข้อความที่กำหนด
                                            if ($row['approve_status'] === 'waiting') {
                                                $statusText = 'รออนุมัติ';
                                            } elseif ($row['approve_status'] === 'approve') {
                                                $statusText = 'อนุมัติ';
                                            } elseif ($row['approve_status'] === 'reject') {
                                                $statusText = 'ไม่อนุมัติ';
                                            } else {
                                                $statusText = 'ไม่ทราบสถานะ'; // หรือข้อความที่คุณต้องการสำหรับสถานะที่ไม่รู้จัก
                                            }

                                            // ใช้ $statusText ต่อไปในโค้ดของคุณ
                                            echo $statusText;
                                            ?>
                                        </td>
                                        <td class="name">
                                            <?php echo $row['firstname_thai'] . ' ' . $row['lastname_thai']; ?>
                                        </td>
                                        <td class="date">
                                            <?php echo $row['date']->format('d-m-Y'); ?>
                                        </td>
                                        <td class="action"><button class="detail" onclick="window.location.href='../shift/shift-detail-lock-employee.php?shift_lock_id=<?= $row['shift_lock_id']; ?>'">รายละเอียด</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="display-missing">
                </div>

                <div class="display-total">
                </div>

                <div class="display-leave">
                    <!-- ลา -->
                    <table>
                        <?php
                        echo '<tr>';
                        echo '<th>รหัส</th>';
                        echo '<th>ชื่อ - สกุล</th>';
                        echo '<th>วันที่</th>';
                        echo '<th>ประเภท</th>';
                        echo '</tr>';

                        foreach ($absence_waiting as $row) {
                            echo '<tr>';
                            echo '<td>' . $row['scg_employee_id'] . '</td>';
                            echo '<td>' . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . '</td>';
                            echo '<td class="date">' . $row['date_start']->format('d-m-Y') . '</td>';
                            echo '<td>' . $row['name'] . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>