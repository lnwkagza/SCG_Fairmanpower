<?php
session_start();
session_regenerate_id(true);
//---------------------------------------------------------------------------------------
header("Cache-Control: no-cache, must-revalidate");
include("../database/connectdb.php");
include('../components-desktop/head/include/header.php');
//---------------------------------------------------------------------------------------
?>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/head/shift-request-head.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/shift-request.css">
<link rel=" stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="../assets/script/loader-normal.js"></script>

<!-- datatable -->
<link href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.bootstrap5.min.js"></script>

<?php
// ทำความสะอาดและป้องกันข้อมูล
$_SESSION['card_id'] = trim($_SESSION['card_id']);  // ลบช่องว่างที่อาจเกิดขึ้นทั้งด้านหน้าและด้านหลังของข้อมูล
$_SESSION['card_id'] = preg_replace('/[^a-zA-Z0-9]/', '', $_SESSION['card_id']);  // ลบทุกอักขระที่ไม่ใช่ a-z, A-Z, และ 0-9
$_SESSION['card_id'] = htmlspecialchars($_SESSION['card_id'], ENT_QUOTES, 'UTF-8');  // แปลงสัญลักษณ์ HTML เพื่อป้องกัน XSS

if (isset($_SESSION["card_id"])) {

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

    // Fetch employee data
    $id = $_SESSION["card_id"];
    $waiting = "waiting";
    // Fetch shift add data
    $select_shift_add_data_query = "SELECT 
                                    shift_add_id, 
                                    approve_status, 
                                    scg_employee_id,
                                    prefix_thai,
                                    firstname_thai, 
                                    lastname_thai,
                                    employee_email,
                                    employee_image, 
                                    request_time, 
                                    before_shift_type.symbol AS before_shift_type,
                                    add_shift_type.symbol AS add_shift_type, 
                                    request_detail
                                    FROM shift_add
                                    INNER JOIN employee ON shift_add.request_card_id = employee.card_id
                                    INNER JOIN shift_type AS before_shift_type ON shift_add.before_shift_type_id = before_shift_type.shift_type_id
                                    INNER JOIN shift_type AS add_shift_type ON shift_add.add_shift_type_id = add_shift_type.shift_type_id
                                    WHERE shift_add.approver = ? AND approve_status = ? ORDER BY shift_add.date ASC;";

    // Prepare and execute the SQL statement for shift add data
    $shift_add_data_stmt = sqlsrv_prepare($conn, $select_shift_add_data_query, array($id, $waiting));
    sqlsrv_execute($shift_add_data_stmt);

    // Fetch shift add data and store it in an array
    $shiftAddData = array();
    while ($row = sqlsrv_fetch_array($shift_add_data_stmt, SQLSRV_FETCH_ASSOC)) {
        $shiftAddData[] = $row;
    }

    // Fetch shift change data
    $select_shift_change_data_query = "SELECT 
                                        shift_change_id,
                                        approve_status,
                                        scg_employee_id,
                                        firstname_thai, 
                                        lastname_thai,
                                        employee_email,
                                        employee_image,
                                        request_time, 
                                        before_shift_type.symbol AS before_shift_type,
                                        new_shift_type.symbol AS new_shift_type, 
                                        request_detail
                                        FROM shift_change
                                        INNER JOIN employee ON shift_change.request_card_id = employee.card_id
                                        INNER JOIN shift_type AS before_shift_type ON shift_change.old_shift_id = before_shift_type.shift_type_id
                                        INNER JOIN shift_type AS new_shift_type ON shift_change.new_shift_id = new_shift_type.shift_type_id
                                        WHERE shift_change.approver =  ? AND approve_status = ? ORDER BY shift_change.date ASC;";

    // Prepare and execute the SQL statement for shift change data
    $shift_change_data_stmt = sqlsrv_prepare($conn, $select_shift_change_data_query, array($id, $waiting));
    sqlsrv_execute($shift_change_data_stmt);

    // Fetch shift change data and store it in an array
    $shiftChangeData = array();
    while ($row = sqlsrv_fetch_array($shift_change_data_stmt, SQLSRV_FETCH_ASSOC)) {
        $shiftChangeData[] = $row;
    }

    // Fetch shift switch data
    $select_shift_switch_data_query = "SELECT 
                                        shift_switch_id,
                                        approve_status,
                                        scg_employee_id,
                                        firstname_thai, 
                                        lastname_thai,
                                        employee_email,
                                        employee_image,
                                        request_time
                                        FROM shift_switch
                                        INNER JOIN employee AS employee1 ON shift_switch.employee_1 = employee1.card_id
                                        WHERE shift_switch.approver = ? AND approve_status = ? ORDER BY shift_switch.date ASC;";

    // Prepare and execute the SQL statement for shift switch data
    $shift_switch_data_stmt = sqlsrv_prepare($conn, $select_shift_switch_data_query, array($id, $waiting));
    sqlsrv_execute($shift_switch_data_stmt);

    // Fetch shift switch data and store it in an array
    $shiftSwitchData = array();
    while ($row = sqlsrv_fetch_array($shift_switch_data_stmt, SQLSRV_FETCH_ASSOC)) {
        $shiftSwitchData[] = $row;
    }

    // Fetch shift lock data
    $select_shift_lock_data_query = "SELECT 
                                    shift_lock_id,
                                    approve_status,
                                    scg_employee_id,
                                    employee.prefix_thai, 
                                    employee.firstname_thai, 
                                    employee.lastname_thai, 
                                    employee.employee_email,
                                    shift_lock.date, 
                                    shift_type.symbol, 
                                    shift_lock.request_detail
                                     FROM shift_lock
                                     INNER JOIN employee ON shift_lock.request_card_id = employee.card_id
                                     INNER JOIN shift_type ON shift_lock.shift_type_id = shift_type.shift_type_id
                                     WHERE shift_lock.approver = ? AND approve_status = ? ORDER BY shift_lock.date ASC;";

    // Prepare and execute the SQL statement for shift lock data
    $shift_lock_data_stmt = sqlsrv_prepare($conn, $select_shift_lock_data_query, array($id, $waiting));
    sqlsrv_execute($shift_lock_data_stmt);

    // Fetch shift lock data and store it in an array
    $shiftLockData = array();
    while ($row = sqlsrv_fetch_array($shift_lock_data_stmt, SQLSRV_FETCH_ASSOC)) {
        $shiftLockData[] = $row;
    }

    $select_sub_team_data_query = "SELECT
            sub_team_id,
            sub_team.approve_status, 
            sub_team.request_time, 
            employee.scg_employee_id, 
            employee.prefix_thai, 
            employee.firstname_thai, 
            employee.lastname_thai,
            employee.employee_email,
            employee.employee_image
        FROM
            dbo.sub_team
        INNER JOIN
            dbo.employee
        ON 
            sub_team.head_card_id = employee.card_id
        WHERE sub_team.head_card_id = ?";

    // Prepare and execute the SQL statement for sub team data
    $sub_team_data_stmt = sqlsrv_prepare($conn, $select_sub_team_data_query, array($id, $waiting));
    sqlsrv_execute($sub_team_data_stmt);

    // Fetch sub team data and store it in an array
    $sub_teamData = array();
    while ($row = sqlsrv_fetch_array($sub_team_data_stmt, SQLSRV_FETCH_ASSOC)) {
        $sub_teamData[] = $row;
    }
}
?>

<!-- script desktop -->
<script>
$(document).ready(function() {
    //mange-team
    new DataTable('#table1', {
        "lengthMenu": [
            [5, 15, 50, -1],
            [5, 15, 50, "ทั้งหมด"]
        ],
        "language": {
            "search": "ค้นหา:",
            "lengthMenu": "แสดง _MENU_ รายการ",
            "info": "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            "paginate": {
                "first": "หน้าแรก",
                "last": "หน้าสุดท้าย",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            },
            "infoEmpty": "แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ",
            "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)"
        }
    });
    //edit-work-format
    new DataTable('#table2', {
        "lengthMenu": [
            [5, 15, 50, -1],
            [5, 15, 50, "ทั้งหมด"]
        ],
        "language": {
            "search": "ค้นหา:",
            "lengthMenu": "แสดง _MENU_ รายการ",
            "info": "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            "paginate": {
                "first": "หน้าแรก",
                "last": "หน้าสุดท้าย",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            },
            "infoEmpty": "แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ",
            "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)"
        }
    });
    //lock-shift
    new DataTable('#table3', {
        "lengthMenu": [
            [5, 15, 50, -1],
            [5, 15, 50, "ทั้งหมด"]
        ],
        "language": {
            "search": "ค้นหา:",
            "lengthMenu": "แสดง _MENU_ รายการ",
            "info": "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            "paginate": {
                "first": "หน้าแรก",
                "last": "หน้าสุดท้าย",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            },
            "infoEmpty": "แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ",
            "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)"
        }
    });
    //switch-shift
    new DataTable('#table4', {
        "lengthMenu": [
            [5, 15, 50, -1],
            [5, 15, 50, "ทั้งหมด"]
        ],
        "language": {
            "search": "ค้นหา:",
            "lengthMenu": "แสดง _MENU_ รายการ",
            "info": "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            "paginate": {
                "first": "หน้าแรก",
                "last": "หน้าสุดท้าย",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            },
            "infoEmpty": "แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ",
            "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)"
        }
    });
    //change-shift
    new DataTable('#table5', {
        "lengthMenu": [
            [5, 15, 50, -1],
            [5, 15, 50, "ทั้งหมด"]
        ],
        "language": {
            "search": "ค้นหา:",
            "lengthMenu": "แสดง _MENU_ รายการ",
            "info": "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            "paginate": {
                "first": "หน้าแรก",
                "last": "หน้าสุดท้าย",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            },
            "infoEmpty": "แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ",
            "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)"
        }
    });
    //add-shift
    new DataTable('#table6', {
        "lengthMenu": [
            [5, 15, 50, -1],
            [5, 15, 50, "ทั้งหมด"]
        ],
        "language": {
            "search": "ค้นหา:",
            "lengthMenu": "แสดง _MENU_ รายการ",
            "info": "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            "paginate": {
                "first": "หน้าแรก",
                "last": "หน้าสุดท้าย",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            },
            "infoEmpty": "แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ",
            "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)"
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const btn = document.querySelector('.button-bar');
    const table = document.querySelectorAll('.desktop-table-container ');

    // Set initial state
    table.forEach((table, index) => {
        if (index === 0) {
            table.style.display = 'block';
            btn.children[index].classList.add('active');
        } else {
            table.style.display = 'none';
            btn.children[index].classList.remove('active');
        }
    });

    btn.addEventListener('click', function(event) {
        if (event.target.tagName === 'BUTTON') {
            const buttonIndex = Array.from(btn.children).indexOf(event.target);

            // Hide all tables
            table.forEach(table => table.style.display = 'none');

            // Show the selected table
            table[buttonIndex].style.display = '';

            // Remove 'active' class from all buttons
            btn.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));

            // Add 'active' class to the clicked button
            event.target.classList.add('active');
        }
    });
});
</script>

<!-- script mobile -->
<script>
$(document).ready(function() {
    //mange-team
    new DataTable('#example1');
    //change-shift
    new DataTable('#example2');
    //switch-shift
    new DataTable('#example3');
    //add-shift
    new DataTable('#example4');
    //lock-shift
    new DataTable('#example5');
});

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
</script>
</head>

<body>
    <div class="desktop">
        <?php include('../components-desktop/head/include/sidebar.php'); ?>
        <?php include('../components-desktop/head/include/navbar.php'); ?>
        <div class="main-container">
            <div class="pd-ltr-20 xs-pd-20-10">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <h2>รายการขออนุมัติ</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor: default;">กะการทำงาน</a>
                                        </li>
                                        <li class=" breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            รายการขออนุมัติ
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="button-bar">
                                    <button id="btn1" class="">ขอจัดการทีม</button>
                                    <button id="btn2" class="">ขอล็อกเหลี่ยม</button>
                                    <button id="btn3" class="">ขอสลับกะ</button>
                                    <button id="btn4" class="">ขอเปลี่ยนกะ</button>
                                    <button id="btn5" class="">ขอเพิ่มกะ</button>
                                </div>


                                <!-- ตารางจัดการทีม -->
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
                                                            <img src="<?php echo (!empty($row['employee_image'])) ? '../../admin/uploads_img/' . $row['employee_image'] : '../IMG/user.png'; ?>"
                                                                class="border-radius-100 shadow" width="40" height="40"
                                                                alt="">
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
                                                <td class="action"><button class="btn btn-info"
                                                        onclick="window.location.href='shift-detail-manage-head.php'">รายละเอียด</button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- ตารางล็อกเหลี่ยม -->
                                <div class="desktop-table-container table2" style="display:none;">
                                    <table id="table2" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th class="status">สถานะ</th>
                                                <th class="name">ผู้ยื่นอนุมัติ</th>
                                                <th class="date">วันที่ยื่นอนุมัติ</th>
                                                <th class="action">การจัดการ</th>
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
                                                            <img src="<?php echo (!empty($row['employee_image'])) ? '../../admin/uploads_img/' . $row['employee_image'] : '../IMG/user.png'; ?>"
                                                                class="border-radius-100 shadow" width="40" height="40"
                                                                alt="">
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
                                                <td class="action"><button class="btn btn-info"
                                                        onclick="window.location.href='shift-detail-lock-head.php'">รายละเอียด</button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- ตารางสลับกะ -->
                                <div class="desktop-table-container table3" style="display:none;">
                                    <table id="table3" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th class="status">สถานะ</th>
                                                <th class="name">ผู้ยื่นอนุมัติ</th>
                                                <th class="date">วันที่ยื่นอนุมัติ</th>
                                                <th class="action">การจัดการ</th>
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
                                                            <img src="<?php echo (!empty($row['employee_image'])) ? '../../admin/uploads_img/' . $row['employee_image'] : '../IMG/user.png'; ?>"
                                                                class="border-radius-100 shadow" width="40" height="40"
                                                                alt="">
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
                                                <td class="action"><button class="btn btn-info"
                                                        onclick="window.location.href='shift-detail-switch-head.php'">รายละเอียด</button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- ตารางเปลี่ยนกะ -->
                                <div class="desktop-table-container table4" style="display:none;">
                                    <table id="table4" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th class="status">สถานะ</th>
                                                <th class="name">ผู้ยื่นอนุมัติ</th>
                                                <th class="date">วันที่ยื่นอนุมัติ</th>
                                                <th class="action">การจัดการ</th>
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
                                                            <img src="<?php echo (!empty($row['employee_image'])) ? '../../admin/uploads_img/' . $row['employee_image'] : '../IMG/user.png'; ?>"
                                                                class="border-radius-100 shadow" width="40" height="40"
                                                                alt="">
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
                                                <td class="action"><button class="btn btn-info"
                                                        onclick="window.location.href='shift-detail-change-head.php'">รายละเอียด</button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- ตารางเพิ่มกะ -->
                                <div class="desktop-table-container table5" style="display:none;">
                                    <table id="table5" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th class="status">สถานะ</th>
                                                <th class="name">ผู้ยื่นอนุมัติ</th>
                                                <th class="date">วันที่ยื่นอนุมัติ</th>
                                                <th class="action">การจัดการ</th>
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
                                                            <img src="<?php echo (!empty($row['employee_image'])) ? '../../admin/uploads_img/' . $row['employee_image'] : '../IMG/user.png'; ?>"
                                                                class="border-radius-100 shadow" width="40" height="40"
                                                                alt="">
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
                                                <td class="action"><button class="btn btn-info"
                                                        onclick="window.location.href='shift-detail-add-head.php'">รายละเอียด</button>
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

    <div class="mobile">
        <div class="navbar-BS">
            <div class="div-span">
                <span>รายการขออนุมัติ</span>
            </div>
        </div>

        <div class="container">
            <!-- <span class="topic">รายการขอนุมัติทั้งหมด</span> -->
            <div class="btn-list">
                <button id="btn1"><img src="../IMG/mnTeam.png" alt="">จัดการทีม</button>
                <button id="btn2"><img src="../IMG/change2.png" alt="">เปลี่ยนกะ</button>
                <button id="btn3"><img src="../IMG/switch.png" alt="">สลับกะ</button>
                <button id="btn4"><img src="../IMG/add-shift.png" alt="">เพิ่มกะ</button>
                <button id="btn5"><img src="../IMG/lock.png" alt="">ล็อกเหลี่ยม</button>
            </div>

            <!-- ตารางจัดการทีม -->
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
                            <td class="name"><?php echo $row['firstname_thai'] . ' ' . $row['lastname_thai']; ?></td>
                            <td class="date"><?php echo $row['request_time']->format('d-m-Y'); ?>
                            </td>
                            <td class="action">
                                <button class="detail"
                                    onclick="window.location.href='../processing/process_shift_update_approve.php?sub_team_id=<?= $row['sub_team_id']; ?>'">อนุมัติ</button>
                                <button class="detail"
                                    onclick="window.location.href='../processing/process_shift_update_reject.php?sub_team_id=<?= $row['sub_team_id']; ?>'">ไม่อนุมัติ</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- ตารางล็อกเหลี่ยม -->
            <div class="table-container table2" style="display:none;">
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
                            <td class="name"><?php echo $row['firstname_thai'] . ' ' . $row['lastname_thai']; ?></td>
                            <td class="date"><?php echo $row['request_time']->format('d-m-Y'); ?>
                            </td>
                            <td class="action">
                                <button class="detail"
                                    onclick="window.location.href='../processing/process_shift_update_approve.php?shift_lock_id=<?= $row['shift_lock_id']; ?>'">อนุมัติ</button>
                                <button class="detail"
                                    onclick="window.location.href='../processing/process_shift_update_reject.php?shift_lock_id=<?= $row['shift_lock_id']; ?>'">ไม่อนุมัติ</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- ตารางสลับกะ -->
            <div class="table-container table3" style="display:none;">
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
                            <td class="name"><?php echo $row['firstname_thai'] . ' ' . $row['lastname_thai']; ?></td>
                            <td class="date"><?php echo $row['request_time']->format('d-m-Y'); ?>
                            </td>
                            <td class="action">
                                <button class="detail"
                                    onclick="window.location.href='../processing/process_shift_update_approve.php?shift_switch_id=<?= $row['shift_switch_id']; ?>'">อนุมัติ</button>
                                <button class="detail"
                                    onclick="window.location.href='../processing/process_shift_update_reject.php?shift_switch_id=<?= $row['shift_switch_id']; ?>'">ไม่อนุมัติ</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- ตารางเปลี่ยนกะ -->
            <div class="table-container table4" style="display:none;">
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
                            <td class="status"><?php // ตรวจสอบ $row['approve_status'] และแปลงเป็นข้อความที่กำหนด
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
                                                    ?></td>
                            <td class="name"><?php echo $row['firstname_thai'] . ' ' . $row['lastname_thai']; ?></td>
                            <td class="date"><?php echo $row['request_time']->format('d-m-Y'); ?>
                            </td>
                            <td class="action">
                                <button class="detail"
                                    onclick="window.location.href='../processing/process_shift_update_approve.php?shift_add_id=<?= $row['shift_add_id']; ?>'">อนุมัติ</button>
                                <button class="detail"
                                    onclick="window.location.href='../processing/process_shift_update_reject.php?shift_add_id=<?= $row['shift_add_id']; ?>'">ไม่อนุมัติ</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- ตารางเพิ่มกะ -->
            <div class="table-container table5" style="display:none;">
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
                            <td class="status"><?php // ตรวจสอบ $row['approve_status'] และแปลงเป็นข้อความที่กำหนด
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
                                                    ?></td>
                            <td class="name"><?php echo $row['firstname_thai'] . ' ' . $row['lastname_thai']; ?></td>
                            <td class="date"><?php echo $row['request_time']->format('d-m-Y'); ?>
                            </td>
                            <td class="action">
                                <button class="detail"
                                    onclick="window.location.href='../processing/process_shift_update_approve.php?shift_add_id=<?= $row['shift_add_id']; ?>'">อนุมัติ</button>
                                <button class="detail"
                                    onclick="window.location.href='../processing/process_shift_update_reject.php?shift_add_id=<?= $row['shift_add_id']; ?>'">ไม่อนุมัติ</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
<?php include('../includes/footer.php') ?>