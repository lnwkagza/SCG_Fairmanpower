<?php
session_start();
include('../components-desktop/head/include/header.php');
include("../database/connectdb.php");
?>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/head/approval-head.css">

<!-- CSS Mobile -->
<link rel="stylesheet" href="../assets/css/approve-status-timeEm.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/navbar.css">

<link rel="stylesheet" href="../assets/css/loader.css">

<?php

$sql_absence_waiting = sqlsrv_query($conn, "SELECT *
FROM manager
LEFT JOIN absence_record ON manager.card_id = absence_record.card_id
RIGHT JOIN absence_type ON absence_record.absence_type_id = absence_type.absence_type_id
LEFT JOIN employee ON absence_record.card_id = employee.card_id
WHERE manager.manager_card_id = ? AND absence_record.approve_status = ?;
", array($_SESSION["card_id"], "waiting"));

$absence_waiting = array();
while ($row = sqlsrv_fetch_array($sql_absence_waiting, SQLSRV_FETCH_ASSOC)) {
    $absence_waiting[] = $row;
}

$sql_check_inout_waiting = sqlsrv_query($conn, "SELECT *
FROM check_inout
LEFT JOIN employee ON check_inout.approver = employee.card_id
WHERE check_inout.approver = ? AND check_inout.approve_status = ?;
", array($_SESSION["card_id"], "waiting"));

$checkin_waiting = array();
while ($row = sqlsrv_fetch_array($sql_check_inout_waiting, SQLSRV_FETCH_ASSOC)) {
    $checkin_waiting[] = $row;
}

$sql_day_off_request_waiting = sqlsrv_query($conn, "SELECT * FROM day_off_request 
INNER JOIN work_format ON day_off_request.edit_work_format_code = work_format.work_format_code
INNER JOIN employee ON day_off_request.approver = employee.card_id 
WHERE day_off_request.approver = ? AND day_off_request.approve_status = ?
", array($_SESSION["card_id"], "waiting"));

if ($sql_day_off_request_waiting === false) {
    // Handle query error
    die(print_r(sqlsrv_errors(), true));
}

$dayoff_waiting_approver = array(); // Different variable name for the first result set
while ($row = sqlsrv_fetch_array($sql_day_off_request_waiting, SQLSRV_FETCH_ASSOC)) {
    $dayoff_waiting_approver[] = $row;
}

$sql_day_off_request_waiting_inspector = sqlsrv_query($conn, "SELECT * FROM day_off_request 
INNER JOIN work_format ON day_off_request.edit_work_format_code = work_format.work_format_code
INNER JOIN employee ON day_off_request.inspector = employee.card_id 
WHERE day_off_request.inspector = ? AND day_off_request.inspector_status = ?
", array($_SESSION["card_id"], "waiting"));

if ($sql_day_off_request_waiting_inspector === false) {
    // Handle query error
    die(print_r(sqlsrv_errors(), true));
}

$dayoff_waiting_inspector = array(); // Different variable name for the second result set
while ($row = sqlsrv_fetch_array($sql_day_off_request_waiting_inspector, SQLSRV_FETCH_ASSOC)) {
    $dayoff_waiting_inspector[] = $row;
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
LEFT JOIN shift_type AS before_shift_type ON shift_add.before_shift_type_id = before_shift_type.shift_type_id
LEFT JOIN shift_type AS add_shift_type ON shift_add.add_shift_type_id = add_shift_type.shift_type_id
WHERE shift_add.approver = ? AND shift_add.approve_status = ? ORDER BY shift_add.date ASC;";

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
    date,
    shift_change.input_timestamp,
    prefix_thai,
    request_detail,
    scg_employee_id,
    employee_email,
    employee_image
    FROM shift_change
    INNER JOIN employee ON shift_change.request_card_id = employee.card_id
    INNER JOIN shift_type AS before_shift_type ON shift_change.old_shift_id = before_shift_type.shift_type_id
    INNER JOIN shift_type AS new_shift_type ON shift_change.new_shift_id = new_shift_type.shift_type_id
    WHERE shift_change.approver =  ? AND shift_change.approve_status = ? ORDER BY shift_change.date ASC;";

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
    shift_switch.input_timestamp,
    shift_switch.request_detail,
    shift_switch.approve_status
    FROM shift_switch
    INNER JOIN employee ON shift_switch.request_card_id = employee.card_id
    WHERE shift_switch.approver = ? AND shift_switch.approve_status = ? ORDER BY shift_switch.date ASC;";

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
request_time,
shift_lock.input_timestamp,
employee_image
 FROM shift_lock
 INNER JOIN employee ON shift_lock.request_card_id = employee.card_id
 INNER JOIN shift_type ON shift_lock.shift_type_id = shift_type.shift_type_id
 WHERE shift_lock.approver = ? AND shift_lock.approve_status = ? ORDER BY shift_lock.date ASC;";


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
    sub_team.approver = employee.card_id
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
                                                    <th>การจัดการ</th>
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
                                                        <td><a href="../processing/process_check_in_edit_approve.php?id='<?= $row['check_inout_id'] ?>'">อนุมัติ</a>
                                                            <a href="../processing/process_check_in_edit_reject.php?id='<?= $row['check_inout_id'] ?>'">ปฎิเสธ</a>
                                                        </td>
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
                                                    <th>การจัดการ</th>
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
                                                        <td><a href="../processing/process_Leave_approve.php?id='<?= $row['day_off_req_id'] ?>'">อนุมัติ</a>
                                                            <a href="../processing/process_Leave_reject.php?id='<?= $row['day_off_req_id'] ?>'">ปฎิเสธ</a>
                                                        </td>
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
                                                    <th>บทบาท</th>
                                                    <th>การจัดการ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                $dayoff_approve_inspector_url = "process_dayoff_approve_inspector.php";
                                                $dayoff_reject_inspector_url = "process_dayoff_reject_inspector.php";
                                                $dayoff_approve_url = "process_dayoff_approve.php";
                                                $dayoff_reject_url = "process_dayoff_reject.php";
                                                
                                                $inspector = "ผู้ตรวจสอบ";
                                                $approve = "ผู้อนุมัติ";
                                                // Display rows for the first query
                                                foreach ($dayoff_waiting_approver as $row) {
                                                    // Check conditions before displaying rows
                                                    if ($row['inspector'] == "" && $row['inspector_status'] == "") {
                                                        displayRows1($row, $dayoff_approve_url, $dayoff_reject_url,$approve);
                                                    }elseif ($row['approve_status'] == "waiting" && $row['inspector_status'] == "confirm") {
                                                        displayRows1($row, $dayoff_approve_url, $dayoff_reject_url,$approve);
                                                    }
                                                }

                                                // Display rows for the second query
                                                foreach ($dayoff_waiting_inspector as $row) {
                                                    // Check conditions before displaying rows
                                                    if ($row['inspector'] != "" && $row['inspector_status'] == "waiting") {
                                                        displayRows1($row, $dayoff_approve_inspector_url, $dayoff_reject_inspector_url,$inspector);
                                                    } elseif ($row['inspector'] != "" && $row['inspector_status'] == "confirm") {
                                                        displayRows1($row, $dayoff_approve_url, $dayoff_reject_url,$approve);
                                                    } elseif ($row['inspector'] == "" && $row['inspector_status'] == "") {
                                                        displayRows1($row, $dayoff_approve_url, $dayoff_reject_url,$approve);
                                                    }elseif ($row['approve_status'] == "waiting" && $row['inspector_status'] == "confirm") {
                                                        displayRows1($row, $dayoff_approve_url, $dayoff_reject_url,$approve);
                                                    }
                                                }

                                                function displayRows1($row, $approve_url, $reject_url,$role)
                                                {
                                                    echo '<tr>';
                                                    echo '<td>' . $row['day_off1'] . '-' . $row['day_off2'] . '</td>';
                                                    echo '<td>' . $row['day_off1'] . '-' . $row['day_off2'] . '</td>';
                                                    echo '<td>' . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . '</td>';
                                                    echo '<td>' . $role . '</td>';
                                                    echo '<td>';
                                                    echo '<button class="approve" onclick="window.location.href=\'../processing/' . $approve_url . '?id=' . $row['day_off_req_id'] . '\'">อนุมัติ</button>';
                                                    echo '<button class="reject" onclick="window.location.href=\'../processing/' . $reject_url . '?id=' . $row['day_off_req_id'] . '\'">ปฎิเสธ</button>';
                                                    echo '</td>';
                                                    echo '</tr>';
                                                }
                                                ?>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class=" col-lg-12 col-md-6 col-sm-12 mb-30" id="box4" style="display:none;">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="pt-3 pb-3">
                                    <div class="button-bar">
                                        <button id="btn1" class="">จัดการทีม</button>
                                        <button id="btn5" class="">ล็อกเหลี่ยม</button>
                                        <button id="btn3" class="">สลับกะ</button>
                                        <button id="btn2" class="">เปลี่ยนกะ</button>
                                        <button id="btn4" class="">เพิ่มกะ</button>
                                    </div>

                                    <div class="desktop-table-container table1">
                                        <table id="table1" class="table table-striped" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th class="status">สถานะ</th>
                                                    <th class="name">ผู้ยื่นอนุมัติ</th>
                                                    <th class="date">วันที่ยื่นอนุมัติ
                                                    </th>
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
                                                        <td class="action">
                                                            <button class="btn btn-info" onclick="window.location.href='../processing/process_shift_update_approve.php?sub_team_id=<?= $row['sub_team_id']; ?>'">อนุมัติ</button>
                                                            <button class="btn btn-info" onclick="window.location.href='../processing/process_shift_update_reject.php?sub_team_id=<?= $row['sub_team_id']; ?>'">ไม่อนุมัติ</button>
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
                                                    <th class="date">วันที่ยื่นอนุมัติ
                                                    </th>
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
                                                        <td class="action">
                                                            <button class="btn btn-info" onclick="window.location.href='../processing/process_shift_update_approve.php?shift_lock_id=<?= $row['shift_lock_id']; ?>'">อนุมัติ</button>
                                                            <button class="btn btn-info" onclick="window.location.href='../processing/process_shift_update_reject.php?shift_lock_id=<?= $row['shift_lock_id']; ?>'">ไม่อนุมัติ</button>
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
                                                    <th class="date">วันที่ยื่นอนุมัติ
                                                    </th>
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
                                                        <td class="action">
                                                            <button class="btn btn-info" onclick="window.location.href='../processing/process_shift_update_approve.php?shift_switch_id=<?= $row['shift_switch_id']; ?>'">อนุมัติ</button>
                                                            <button class="btn btn-info" onclick="window.location.href='../processing/process_shift_update_reject.php?shift_switch_id=<?= $row['shift_switch_id']; ?>'">ไม่อนุมัติ</button>
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
                                                    <th class="date">วันที่ยื่นอนุมัติ
                                                    </th>
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
                                                        <td class="action">
                                                            <button class="btn btn-info" onclick="window.location.href='../processing/process_shift_update_approve.php?shift_change_id=<?= $row['shift_change_id']; ?>'">อนุมัติ</button>
                                                            <button class="btn btn-info" onclick="window.location.href='../processing/process_shift_update_reject.php?shift_change_id=<?= $row['shift_change_id']; ?>'">ไม่อนุมัติ</button>
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
                                                    <th class="date">วันที่ยื่นอนุมัติ
                                                    </th>
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
                                                        <td class="action">
                                                            <button class="btn btn-info" onclick="window.location.href='../processing/process_shift_update_approve.php?shift_add_id=<?= $row['shift_add_id']; ?>'">อนุมัติ</button>
                                                            <button class="btn btn-info" onclick="window.location.href='../processing/process_shift_update_reject.php?shift_add_id=<?= $row['shift_add_id']; ?>'">ไม่อนุมัติ</button>
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
                <span>รายงานอนุมัติ</span>
            </div>
        </div>

        <div class="btn-status">
            <div class="btn-select">
                <div class="btn-normal">
                    <button onclick="showStatus('normal')"><img src="../IMG/ci.png" alt="">เช็คอิน</button>
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
                <table>
                    <tr>
                    <th>รหัส</th>
                    <th>ชื่อ - สกุล</th>
                    <th>วันที่</th>
                    <th>in</th>
                    <th>out</th>
                    </tr>
                    <?php
                    foreach ($checkin_waiting as $row) {
                        echo '<tr>';
                        echo '<td>' . $row['scg_employee_id'] . '</td>';
                        echo '<td>' . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . '</td>';
                        echo '<td>' . $row['date']->format('d-m-Y') . '</td>';
                        echo '<td>' . $row['edit_time_in']->format('H:i') . '</td>';
                        echo '<td>' . $row['edit_time_out']->format('H:i') . '</td>';
                        echo '<td><a href="../processing/process_check_in_edit_approve.php?id=' . $row['check_inout_id'] . '">อนุมัติ</a></td>';
                        echo '<td><a href="../processing/process_check_in_edit_reject.php?id=' . $row['check_inout_id'] . '">ปฎิเสธ</a></td>';
                        echo '</tr>';
                    }
                    ?>
                </table>
            </div>

            <div class="display-late">
                <table>

                    <tr>
                    <th>รหัส</th>
                    <th>ชื่อ - สกุล</th>
                    <th>เดิม</th>
                    <th>ใหม่</th>
                    <th>สถานะ</th>
                    </tr>
                    <?php
                                                $dayoff_approve_inspector_url = "process_dayoff_approve_inspector.php";
                                                $dayoff_reject_inspector_url = "process_dayoff_reject_inspector.php";
                                                $dayoff_approve_url = "process_dayoff_approve.php";
                                                $dayoff_reject_url = "process_dayoff_reject.php";
                                                
                                                $inspector = "ผู้ตรวจสอบ";
                                                $approve = "ผู้อนุมัติ";
                                                // Display rows for the first query
                                                foreach ($dayoff_waiting_approver as $row) {
                                                    // Check conditions before displaying rows
                                                    if ($row['inspector'] == "" && $row['inspector_status'] == "") {
                                                        displayRows($row, $dayoff_approve_url, $dayoff_reject_url,$approve);
                                                    }elseif ($row['approve_status'] == "waiting" && $row['inspector_status'] == "confirm") {
                                                        displayRows($row, $dayoff_approve_url, $dayoff_reject_url,$approve);
                                                    }
                                                }

                                                // Display rows for the second query
                                                foreach ($dayoff_waiting_inspector as $row) {
                                                    // Check conditions before displaying rows
                                                    if ($row['inspector'] != "" && $row['inspector_status'] == "waiting") {
                                                        displayRows($row, $dayoff_approve_inspector_url, $dayoff_reject_inspector_url,$inspector);
                                                    } elseif ($row['inspector'] != "" && $row['inspector_status'] == "confirm") {
                                                        displayRows($row, $dayoff_approve_url, $dayoff_reject_url,$approve);
                                                    } elseif ($row['inspector'] == "" && $row['inspector_status'] == "") {
                                                        displayRows($row, $dayoff_approve_url, $dayoff_reject_url,$approve);
                                                    }elseif ($row['approve_status'] == "waiting" && $row['inspector_status'] == "confirm") {
                                                        displayRows($row, $dayoff_approve_url, $dayoff_reject_url,$approve);
                                                    }
                                                }

                                                function displayRows($row, $approve_url, $reject_url,$role)
                                                {
                                                    echo '<tr>';
                                                    echo '<td>' . $row['day_off1'] . '-' . $row['day_off2'] . '</td>';
                                                    echo '<td>' . $row['day_off1'] . '-' . $row['day_off2'] . '</td>';
                                                    echo '<td>' . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . '</td>';
                                                    echo '<td>' . $role . '</td>';
                                                    echo '<td>';
                                                    echo '<button class="approve" onclick="window.location.href=\'../processing/' . $approve_url . '?id=' . $row['day_off_req_id'] . '\'">อนุมัติ</button>';
                                                    echo '<button class="reject" onclick="window.location.href=\'../processing/' . $reject_url . '?id=' . $row['day_off_req_id'] . '\'">ปฎิเสธ</button>';
                                                    echo '</td>';
                                                    echo '</tr>';
                                                }
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
                                    <td class="action">
                                        <button class="detail" onclick="window.location.href='../processing/process_shift_update_approve.php?sub_team_id=<?= $row['sub_team_id']; ?>'">อนุมัติ</button>
                                        <button class="detail" onclick="window.location.href='../processing/process_shift_update_reject.php?sub_team_id=<?= $row['sub_team_id']; ?>'">ไม่อนุมัติ</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>


                <!-- ตารางล็อกเหลี่ยม -->
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
                                        <?php echo $row['input_timestamp']->format('d-m-Y'); ?>
                                    </td>
                                    <td class="action">
                                        <button class="detail" onclick="window.location.href='../processing/process_shift_update_approve.php?shift_lock_id=<?= $row['shift_lock_id']; ?>'">อนุมัติ</button>
                                        <button class="detail" onclick="window.location.href='../processing/process_shift_update_reject.php?shift_lock_id=<?= $row['shift_lock_id']; ?>'">ไม่อนุมัติ</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- ตารางสลับกะ -->
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
                                    <td class="action">
                                        <button class="detail" onclick="window.location.href='../processing/process_shift_update_approve.php?shift_switch_id=<?= $row['shift_switch_id']; ?>'">อนุมัติ</button>
                                        <button class="detail" onclick="window.location.href='../processing/process_shift_update_reject.php?shift_switch_id=<?= $row['shift_switch_id']; ?>'">ไม่อนุมัติ</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- ตารางเปลี่ยนกะ -->
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
                                        <?php echo $row['request_time']->format('d-m-Y'); ?>
                                    </td>
                                    <td class="action">
                                        <button class="detail" onclick="window.location.href='../processing/process_shift_update_approve.php?shift_change_id=<?= $row['shift_change_id']; ?>'">อนุมัติ</button>
                                        <button class="detail" onclick="window.location.href='../processing/process_shift_update_reject.php?shift_change_id=<?= $row['shift_change_id']; ?>'">ไม่อนุมัติ</button>
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
                                    <td class="action">
                                        <button class="detail" onclick="window.location.href='../processing/process_shift_update_approve.php?shift_add_id=<?= $row['shift_add_id']; ?>'">อนุมัติ</button>
                                        <button class="detail" onclick="window.location.href='../processing/process_shift_update_reject.php?shift_add_id=<?= $row['shift_add_id']; ?>'">ไม่อนุมัติ</button>
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
                        echo '<td>' . $row['date_start']->format('d-m-Y') . '</td>';
                        echo '<td>' . $row['name'] . '</td>';
                        echo '<td><a href="../processing/process_Leave_approve.php?id=' . $row['absence_record_id'] . '">อนุมัติ</a></td>';
                        echo '<td><a href="../processing/process_Leave_reject.php?id=' . $row['absence_record_id'] . '">ปฎิเสธ</a></td>';
                        echo '</tr>';
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</body>
<?php include('../includes/footer.php'); ?>