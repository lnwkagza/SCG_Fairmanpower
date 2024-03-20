<?php
session_start();
session_regenerate_id(true);
//---------------------------------------------------------------------------------------
include("../database/connectdb.php");
include('../components-desktop/head/include/header.php');

// include("dbconnect.php");
// include("update_dayoff.php");
//---------------------------------------------------------------------------------------
?>

<!-- Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="node_modules\sweetalert2\dist\sweetalert2.css">

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/head/day-off-change-history-head.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/day-off-change-history.css">

<!-- datatable -->
<link href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.bootstrap5.min.js"></script>

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="script/loader-normal.js"></script>

<?php

//---------------------------------------------------------------------------------------

// ทำความสะอาดและป้องกันข้อมูล
$_SESSION['card_id'] = trim($_SESSION['card_id']);  // ลบช่องว่างที่อาจเกิดขึ้นทั้งด้านหน้าและด้านหลังของข้อมูล
$_SESSION['card_id'] = preg_replace('/[^a-zA-Z0-9]/', '', $_SESSION['card_id']);  // ลบทุกอักขระที่ไม่ใช่ a-z, A-Z, และ 0-9
$_SESSION['card_id'] = htmlspecialchars($_SESSION['card_id'], ENT_QUOTES, 'UTF-8');  // แปลงสัญลักษณ์ HTML เพื่อป้องกัน XSS

//---------------------------------------------------------------------------------------

// if (!empty($_SESSION["card_id"])) {
    // สร้าง query เพื่อดึงข้อมูลพนักงาน
    $query = "SELECT *
    FROM day_off_request
    JOIN employee AS emp1 ON emp1.card_id = day_off_request.card_id
    JOIN employee AS approver ON approver.card_id = ?
    WHERE emp1.cost_center_payment_id = approver.cost_center_organization_id";
    
    $params = array($_SESSION['card_id']);
    $sql_card_id = sqlsrv_query($conn, $query, $params);
    $rowg = sqlsrv_fetch_array($sql_card_id);

    // if (sqlsrv_has_rows($sql_card_id)) {

        $sql_day_off_request_confirm = sqlsrv_query($conn, "SELECT
        day_off_req_id,
        emp1.scg_employee_id AS emp1_scg_employee_id, 
        emp1.prefix_thai AS emp1_prefix_thai, 
        emp1.firstname_thai AS emp1_firstname_thai, 
        emp1.lastname_thai AS emp1_lastname_thai,
        emp1.employee_email AS emp1_employee_email,
        emp1.employee_image AS emp1_employee_image,
        CONCAT(old_work_format.day_off1, ' - ', old_work_format.day_off2) AS old_dayoff,
        CONCAT(new_work_format.day_off1, ' - ', new_work_format.day_off2) AS new_dayoff,
        emp2.scg_employee_id AS emp2_scg_employee_id, 
        emp2.prefix_thai AS emp2_prefix_thai, 
        emp2.firstname_thai AS emp2_firstname_thai, 
        emp2.lastname_thai AS emp2_lastname_thai,
        emp2.employee_email AS emp2_employee_email,
        emp2.employee_image AS emp2_employee_image 
        FROM day_off_request 
        JOIN employee AS emp1 ON emp1.card_id = day_off_request.card_id
        JOIN employee AS emp2 ON emp2.card_id = day_off_request.request_card_id
        JOIN work_format AS old_work_format ON old_work_format.work_format_code = emp1.work_format_code
        JOIN work_format AS new_work_format ON new_work_format.work_format_code = day_off_request.edit_work_format_code
        WHERE day_off_request.approve_status = ? AND day_off_request.approver = ?", array("confirm",$_SESSION['card_id']));

        $day_off_request_confirm = array();

        while ($row = sqlsrv_fetch_array($sql_day_off_request_confirm, SQLSRV_FETCH_ASSOC)) {
            $day_off_request_confirm[] = $row;
        }

        $sql_day_off_request_confirm_inspector = sqlsrv_query($conn, "SELECT
        day_off_req_id,
        emp1.scg_employee_id AS emp1_scg_employee_id, 
        emp1.prefix_thai AS emp1_prefix_thai, 
        emp1.firstname_thai AS emp1_firstname_thai, 
        emp1.lastname_thai AS emp1_lastname_thai,
        emp1.employee_email AS emp1_employee_email,
        emp1.employee_image AS emp1_employee_image,
        CONCAT(old_work_format.day_off1, ' - ', old_work_format.day_off2) AS old_dayoff,
        CONCAT(new_work_format.day_off1, ' - ', new_work_format.day_off2) AS new_dayoff,
        emp2.scg_employee_id AS emp2_scg_employee_id, 
        emp2.prefix_thai AS emp2_prefix_thai, 
        emp2.firstname_thai AS emp2_firstname_thai, 
        emp2.lastname_thai AS emp2_lastname_thai,
        emp2.employee_email AS emp2_employee_email,
        emp2.employee_image AS emp2_employee_image 
        FROM day_off_request 
        JOIN employee AS emp1 ON emp1.card_id = day_off_request.card_id
        JOIN employee AS emp2 ON emp2.card_id = day_off_request.request_card_id
        JOIN work_format AS old_work_format ON old_work_format.work_format_code = emp1.work_format_code
        JOIN work_format AS new_work_format ON new_work_format.work_format_code = day_off_request.edit_work_format_code
        WHERE day_off_request.approve_status = ? AND day_off_request.approver = ?", array("confirm",$_SESSION['card_id']));

        $day_off_request_confirm_inspector = array();

        while ($row = sqlsrv_fetch_array($sql_day_off_request_confirm_inspector, SQLSRV_FETCH_ASSOC)) {
            $day_off_request_confirm_inspector[] = $row;
        }


        $sql_day_off_request_waiting = sqlsrv_query($conn, "SELECT
        day_off_req_id,
        emp1.scg_employee_id AS emp1_scg_employee_id, 
        emp1.prefix_thai AS emp1_prefix_thai, 
        emp1.firstname_thai AS emp1_firstname_thai, 
        emp1.lastname_thai AS emp1_lastname_thai,
        emp1.employee_email AS emp1_employee_email,
        emp1.employee_image AS emp1_employee_image,
        CONCAT(old_work_format.day_off1, ' - ', old_work_format.day_off2) AS old_dayoff,
        CONCAT(new_work_format.day_off1, ' - ', new_work_format.day_off2) AS new_dayoff,
        emp2.scg_employee_id AS emp2_scg_employee_id, 
        emp2.prefix_thai AS emp2_prefix_thai, 
        emp2.firstname_thai AS emp2_firstname_thai, 
        emp2.lastname_thai AS emp2_lastname_thai,
        emp2.employee_email AS emp2_employee_email,
        emp2.employee_image AS emp2_employee_image 
        FROM day_off_request 
        JOIN employee AS emp1 ON emp1.card_id = day_off_request.card_id
        JOIN employee AS emp2 ON emp2.card_id = day_off_request.request_card_id
        JOIN work_format AS old_work_format ON old_work_format.work_format_code = emp1.work_format_code
        JOIN work_format AS new_work_format ON new_work_format.work_format_code = day_off_request.edit_work_format_code
        WHERE day_off_request.approve_status = ? AND day_off_request.approver = ?", array("waiting",$_SESSION['card_id']));

        $day_off_request_waiting = array();

        while ($row = sqlsrv_fetch_array($sql_day_off_request_waiting, SQLSRV_FETCH_ASSOC)) {
            $day_off_request_waiting[] = $row;
        }

        $sql_day_off_request_reject = sqlsrv_query($conn, "SELECT
        day_off_req_id,
        emp1.scg_employee_id AS emp1_scg_employee_id, 
        emp1.prefix_thai AS emp1_prefix_thai, 
        emp1.firstname_thai AS emp1_firstname_thai, 
        emp1.lastname_thai AS emp1_lastname_thai,
        emp1.employee_email AS emp1_employee_email,
        emp1.employee_image AS emp1_employee_image,
        CONCAT(old_work_format.day_off1, ' - ', old_work_format.day_off2) AS old_dayoff,
        CONCAT(new_work_format.day_off1, ' - ', new_work_format.day_off2) AS new_dayoff,
        emp2.scg_employee_id AS emp2_scg_employee_id, 
        emp2.prefix_thai AS emp2_prefix_thai, 
        emp2.firstname_thai AS emp2_firstname_thai, 
        emp2.lastname_thai AS emp2_lastname_thai,
        emp2.employee_email AS emp2_employee_email,
        emp2.employee_image AS emp2_employee_image 
        FROM day_off_request 
        JOIN employee AS emp1 ON emp1.card_id = day_off_request.card_id
        JOIN employee AS emp2 ON emp2.card_id = day_off_request.request_card_id
        JOIN work_format AS old_work_format ON old_work_format.work_format_code = emp1.work_format_code
        JOIN work_format AS new_work_format ON new_work_format.work_format_code = day_off_request.edit_work_format_code
        WHERE day_off_request.approve_status = ? AND day_off_request.approver = ?", array("reject",$_SESSION['card_id']));

        $day_off_request_reject = array();

        while ($row = sqlsrv_fetch_array($sql_day_off_request_reject, SQLSRV_FETCH_ASSOC)) {
            $day_off_request_reject[] = $row;
        }

    // } else {
    //     // แจ้งเตือนถ้ายังไม่ได้ลงทะเบียน
    //     echo '<script>
    //     alert("คุณยังไม่ได้ลงทะเบียน");
    //     window.location.href = "../index.html";
    //     </script>';
    // }
// } else {
//     // แจ้งเตือนถ้ายังไม่ได้ลงทะเบียน
//     echo '<script>
//     alert("คุณยังไม่ได้ลงทะเบียน");
//     window.location.href = "../index.html";
//      </script>';
// }
?>
<!-- script desktop -->
<script>
$(document).ready(function() {
    //approve
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
    new DataTable('#mobile-table1', {
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
    //waiting
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
    new DataTable('#mobile-table2', {
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
    //reject
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
    new DataTable('#mobile-table3', {
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
        if (index === 1) {
            table.style.display = '';
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

document.addEventListener('DOMContentLoaded', function() {
    const btn = document.querySelector('.buttonStatus');
    const table = document.querySelectorAll('.table-container');

    // Set initial state
    table.forEach((table, index) => {
        if (index === 1) {
            table.style.display = '';
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
                                    <h2>รายการขออนุมัติเปลี่ยนวันหยุด</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor: default;">วันหยุด</a>
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
                                    <button id="btn1" class="">อนุมัติแล้ว</button>
                                    <button id="btn2" class="">รออนุมัติ</button>
                                    <button id="btn3" class="">ปฏิเสธ</button>
                                </div>

                                <div class="desktop-table-container table1" style="display:none;">
                                    <table id="table1" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th class="old-dayoff">วันเดิม</th>
                                                <th class="new-dayoff">วันใหม่</th>
                                                <th class="emp1">ผู้ถูกร้องขอ</th>
                                                <th class="emp2">ผู้ยื่นอนุมัติ</th>
                                                <th class="status">สถานะ</th>
                                                <th class="action">การจัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($day_off_request_confirm as $row ) { ?>
                                            <tr>
                                                <td class="old-dayoff">
                                                    <?php echo $row['old_dayoff'] ?>
                                                </td>
                                                <td class="new-dayoff">
                                                    <?php echo $row['new_dayoff'] ?>
                                                </td>
                                                <td class="emp1">
                                                    <div class="row">
                                                        <div style="margin-right: 5px;">
                                                            <img src="<?php echo (!empty($row['emp1_employee_image'])) ? '../../admin/uploads_img/' . $row['emp1_employee_image'] : '../IMG/user.png'; ?>"
                                                                class="border-radius-100 shadow" width="40" height="40"
                                                                alt="">
                                                        </div>
                                                        <div>
                                                            <b>
                                                                <?php echo $row['emp1_scg_employee_id'] . " " . $row['emp1_prefix_thai'] . $row['emp1_firstname_thai'] . " " . $row['emp1_lastname_thai'] ?>
                                                            </b><br>
                                                            <a class="
                                                                text-primary">
                                                                <?php echo $row['emp1_employee_email'] ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="emp2">
                                                    <div class="row">
                                                        <div style="margin-right: 5px;">
                                                            <img src="<?php echo (!empty($row['emp2_employee_image'])) ? '../../admin/uploads_img/' . $row['emp2_employee_image'] : '../IMG/user.png'; ?>"
                                                                class="border-radius-100 shadow" width="40" height="40"
                                                                alt="">
                                                        </div>
                                                        <div>
                                                            <b>
                                                                <?php echo $row['emp2_scg_employee_id'] . " " . $row['emp2_prefix_thai'] . $row['emp2_firstname_thai'] . " " . $row['emp2_lastname_thai'] ?>
                                                            </b><br>
                                                            <a class="
                                                                text-primary">
                                                                <?php echo $row['emp2_employee_email'] ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="status" style="color: green !important;">อนุมัติแล้ว</td>
                                                <td class=" action"><button class="btn btn-info"
                                                        onclick="window.location.href='day-off-detail-status-head.php?id=<?php echo $row['day_off_req_id'] ?>'">รายละเอียด</button>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="desktop-table-container table2">
                                    <table id="table2" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th class="old-dayoff">วันเดิม</th>
                                                <th class="new-dayoff">วันใหม่</th>
                                                <th class="emp1">ผู้ถูกร้องขอ</th>
                                                <th class="emp2">ผู้ยื่นอนุมัติ</th>
                                                <th class="status">สถานะ</th>
                                                <th class="action">การจัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($day_off_request_waiting as $row ) { ?>
                                            <tr>
                                                <td class="old-dayoff">
                                                    <?php echo $row['old_dayoff'] ?>
                                                </td>
                                                <td class="new-dayoff">
                                                    <?php echo $row['new_dayoff'] ?>
                                                </td>
                                                <td class="emp1">
                                                    <div class="row">
                                                        <div style="margin-right: 5px;">
                                                            <img src="<?php echo (!empty($row['emp1_employee_image'])) ? '../../admin/uploads_img/' . $row['emp1_employee_image'] : '../IMG/user.png'; ?>"
                                                                class="border-radius-100 shadow" width="40" height="40"
                                                                alt="">
                                                        </div>
                                                        <div>
                                                            <b>
                                                                <?php echo $row['emp1_scg_employee_id'] . " " . $row['emp1_prefix_thai'] . $row['emp1_firstname_thai'] . " " . $row['emp1_lastname_thai'] ?>
                                                            </b><br>
                                                            <a class="
                                                                text-primary">
                                                                <?php echo $row['emp1_employee_email'] ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="emp2">
                                                    <div class="row">
                                                        <div style="margin-right: 5px;">
                                                            <img src="<?php echo (!empty($row['emp2_employee_image'])) ? '../../admin/uploads_img/' . $row['emp2_employee_image'] : '../IMG/user.png'; ?>"
                                                                class="border-radius-100 shadow" width="40" height="40"
                                                                alt="">
                                                        </div>
                                                        <div>
                                                            <b>
                                                                <?php echo $row['emp2_scg_employee_id'] . " " . $row['emp2_prefix_thai'] . $row['emp2_firstname_thai'] . " " . $row['emp2_lastname_thai'] ?>
                                                            </b><br>
                                                            <a class="
                                                                text-primary">
                                                                <?php echo $row['emp2_employee_email'] ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="status" style="color: orange !important;">รออนุมัติ</td>
                                                <td class=" action"><button class="btn btn-info"
                                                        onclick="window.location.href='day-off-manage-approve-head.php?id=<?php echo $row['day_off_req_id'] ?>'">รายละเอียด</button>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="desktop-table-container table3" style="display:none;">
                                    <table id="table3" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th class="old-dayoff">วันเดิม</th>
                                                <th class="new-dayoff">วันใหม่</th>
                                                <th class="emp1">ผู้ถูกร้องขอ</th>
                                                <th class="emp2">ผู้ยื่นอนุมัติ</th>
                                                <th class="status">สถานะ</th>
                                                <th class="action">การจัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($day_off_request_reject as $row ) { ?>
                                            <tr>
                                                <td class="old-dayoff">
                                                    <?php echo $row['old_dayoff'] ?>
                                                </td>
                                                <td class="new-dayoff">
                                                    <?php echo $row['new_dayoff'] ?>
                                                </td>
                                                <td class="emp1">
                                                    <div class="row">
                                                        <div style="margin-right: 5px;">
                                                            <img src="<?php echo (!empty($row['emp1_employee_image'])) ? '../../admin/uploads_img/' . $row['emp1_employee_image'] : '../IMG/user.png'; ?>"
                                                                class="border-radius-100 shadow" width="40" height="40"
                                                                alt="">
                                                        </div>
                                                        <div>
                                                            <b>
                                                                <?php echo $row['emp1_scg_employee_id'] . " " . $row['emp1_prefix_thai'] . $row['emp1_firstname_thai'] . " " . $row['emp1_lastname_thai'] ?>
                                                            </b><br>
                                                            <a class="
                                                                text-primary">
                                                                <?php echo $row['emp1_employee_email'] ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="emp2">
                                                    <div class="row">
                                                        <div style="margin-right: 5px;">
                                                            <img src="<?php echo (!empty($row['emp2_employee_image'])) ? '../../admin/uploads_img/' . $row['emp2_employee_image'] : '../IMG/user.png'; ?>"
                                                                class="border-radius-100 shadow" width="40" height="40"
                                                                alt="">
                                                        </div>
                                                        <div>
                                                            <b>
                                                                <?php echo $row['emp2_scg_employee_id'] . " " . $row['emp2_prefix_thai'] . $row['emp2_firstname_thai'] . " " . $row['emp2_lastname_thai'] ?>
                                                            </b><br>
                                                            <a class="
                                                                text-primary">
                                                                <?php echo $row['emp2_employee_email'] ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="status" style="color: red !important;">ปฏิเสธ</td>
                                                <td class=" action"><button class="btn btn-info"
                                                        onclick="window.location.href='day-off-detail-status-head.php?id=<?php echo $row['day_off_req_id'] ?>'">รายละเอียด</button>
                                                </td>
                                            </tr>
                                            <?php } ?>
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
        <div class="navbar">
            <div class="div-span">
                <span>ประวัติการขอเปลี่ยนวันหยุด</span>
            </div>
        </div>
        <div class="boxFirst">
            <div class="text-left">
                <span>วันเริ่มต้น</span>
            </div>
            <span style="font-size: 3.5vw; font-weight: bold;">ถึง</span>
            <div class="text-right">
                <span>วันสิ้นสุด</span>
            </div>
        </div>
        <div class="buttonStatus">
            <button class="buttonl" id="btn1">
                อนุมัติแล้ว
            </button>
            <button class="buttonl active" id="btn2">
                รออนุมัติ
            </button>
            <button class="buttonl" id="btn3">
                ปฏิเสธ
            </button>
        </div>

        <div class="table-container table1" style="display:none;">
            <table id="mobile-table1" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th class="old-dayoff">วันเดิม</th>
                        <th class="new-dayoff">วันใหม่</th>
                        <th class="emp1">ผู้ถูกร้องขอ</th>
                        <th class="emp2">ผู้ยื่นอนุมัติ</th>
                        <th class="status">สถานะ</th>
                        <th class="action">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($day_off_request_confirm as $row ) { ?>
                    <tr>
                        <td class="old-dayoff">
                            <?php echo $row['old_dayoff'] ?>
                        </td>
                        <td class="new-dayoff">
                            <?php echo $row['new_dayoff'] ?>
                        </td>
                        <td class="emp1">
                            <?php echo $row['emp1_prefix_thai'] . $row['emp1_firstname_thai'] . " " . $row['emp1_lastname_thai'] ?>
                        </td>
                        <td class="emp2">
                            <?php echo $row['emp2_prefix_thai'] . $row['emp2_firstname_thai'] . " " . $row['emp2_lastname_thai'] ?>
                        </td>
                        <td class="status" style="color: green !important;">อนุมัติแล้ว</td>
                        <td class=" action"><button class="btn btn-info"
                                onclick="window.location.href='day-off-detail-status-head.php?id=<?php echo $row['day_off_req_id'] ?>'">รายละเอียด</button>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="table-container table2">
            <table id="mobile-table2" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th class="old-dayoff">วันเดิม</th>
                        <th class="new-dayoff">วันใหม่</th>
                        <th class="emp1">ผู้ถูกร้องขอ</th>
                        <th class="emp2">ผู้ยื่นอนุมัติ</th>
                        <th class="status">สถานะ</th>
                        <th class="action">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($day_off_request_waiting as $row ) { ?>
                    <tr>
                        <td class="old-dayoff">
                            <?php echo $row['old_dayoff'] ?>
                        </td>
                        <td class="new-dayoff">
                            <?php echo $row['new_dayoff'] ?>
                        </td>
                        <td class="emp1">
                            <?php echo $row['emp1_prefix_thai'] . $row['emp1_firstname_thai'] . " " . $row['emp1_lastname_thai'] ?>
                        </td>
                        <td class="emp2">
                            <?php echo $row['emp2_prefix_thai'] . $row['emp2_firstname_thai'] . " " . $row['emp2_lastname_thai'] ?>
                        </td>
                        <td class="status" style="color: orange !important;">รออนุมัติ</td>
                        <td class=" action"><button class="btn btn-info"
                                onclick="window.location.href='day-off-manage-approve-head.php?id=<?php echo $row['day_off_req_id'] ?>'">รายละเอียด</button>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="table-container table3" style="display:none;">
            <table id="mobile-table3" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th class="old-dayoff">วันเดิม</th>
                        <th class="new-dayoff">วันใหม่</th>
                        <th class="emp1">ผู้ถูกร้องขอ</th>
                        <th class="emp2">ผู้ยื่นอนุมัติ</th>
                        <th class="status">สถานะ</th>
                        <th class="action">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($day_off_request_reject as $row ) { ?>
                    <tr>
                        <td class="old-dayoff">
                            <?php echo $row['old_dayoff'] ?>
                        </td>
                        <td class="new-dayoff">
                            <?php echo $row['new_dayoff'] ?>
                        </td>
                        <td class="emp1">
                            <?php echo $row['emp1_prefix_thai'] . $row['emp1_firstname_thai'] . " " . $row['emp1_lastname_thai'] ?>
                        </td>
                        <td class="emp2">
                            <?php echo $row['emp2_prefix_thai'] . $row['emp2_firstname_thai'] . " " . $row['emp2_lastname_thai'] ?>
                        </td>
                        <td class="status" style="color: red !important;">ปฏิเสธ</td>
                        <td class=" action"><button class="btn btn-info"
                                onclick="window.location.href='day-off-detail-status-head.php?id=<?php echo $row['day_off_req_id'] ?>'">รายละเอียด</button>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
<?php include('../includes/footer.php') ?>