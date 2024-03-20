<?php
session_start();
session_regenerate_id(true);
//---------------------------------------------------------------------------------------
include("../database/connectdb.php");
include('../components-desktop/employee/include/header.php');
// include("dbconnect.php");
//---------------------------------------------------------------------------------------
?>

<!-- Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="node_modules\sweetalert2\dist\sweetalert2.css">

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/employee/day-off-change-history-employee.css">

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

if (!empty($_SESSION["card_id"])) {
    // สร้าง query เพื่อดึงข้อมูลพนักงาน
    $query = "SELECT * FROM employee INNER JOIN work_format ON employee.work_format_code = work_format.work_format_code WHERE card_id = ?";
    $params = array($_SESSION['card_id']);
    $sql_card_id = sqlsrv_query($conn, $query, $params);
    $rowg = sqlsrv_fetch_array($sql_card_id);
    // For confirmed requests

    $sql_day_off_request_confirm = sqlsrv_query($conn, "SELECT * FROM day_off_request 
    INNER JOIN work_format ON day_off_request.edit_work_format_code = work_format.work_format_code
    INNER JOIN employee ON day_off_request.approver = employee.card_id WHERE day_off_request.card_id = ? AND day_off_request.approve_status = ?", array($_SESSION["card_id"], "confirm"));
    $confirmed_requests = array();
    while ($row_confirm = sqlsrv_fetch_array($sql_day_off_request_confirm, SQLSRV_FETCH_ASSOC)) {
        $confirmed_requests[] = $row_confirm;
    }

    // For requests waiting for approval
    $sql_day_off_request_waiting = sqlsrv_query($conn, "SELECT * FROM day_off_request 
    INNER JOIN work_format ON day_off_request.edit_work_format_code = work_format.work_format_code
    INNER JOIN employee ON day_off_request.approver = employee.card_id WHERE day_off_request.card_id = ? AND day_off_request.approve_status = ?", array($_SESSION["card_id"], "waiting"));
    $waiting_requests = array();
    while ($row_waiting = sqlsrv_fetch_array($sql_day_off_request_waiting, SQLSRV_FETCH_ASSOC)) {
        $waiting_requests[] = $row_waiting;
    }

    // For rejected requests
    $sql_day_off_request_reject = sqlsrv_query($conn, "SELECT * FROM day_off_request 
    INNER JOIN work_format ON day_off_request.edit_work_format_code = work_format.work_format_code
    INNER JOIN employee ON day_off_request.approver = employee.card_id WHERE day_off_request.card_id = ? AND day_off_request.approve_status = ?", array($_SESSION["card_id"], "reject"));
    $rejected_requests = array();
    while ($row_reject = sqlsrv_fetch_array($sql_day_off_request_reject, SQLSRV_FETCH_ASSOC)) {
        $rejected_requests[] = $row_reject;
    }

} else {
    // แจ้งเตือนถ้ายังไม่ได้ลงทะเบียน
    echo '<script>
    alert("คุณยังไม่ได้ลงทะเบียน");
    window.location.href = "../index.html";
     </script>';
}
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

<script>
    window.addEventListener('DOMContentLoaded', (event) => {
        const button1 = document.getElementById('button1');
        const button2 = document.getElementById('button2');
        const button3 = document.getElementById('button3');
        const nametable1 = document.getElementById('nametable1');
        const nametable2 = document.getElementById('nametable2');
        const nametable3 = document.getElementById('nametable3');

        // Initial state: Show nametable2
        button2.classList.add('active');
        nametable2.style.display = '';
        nametable1.style.display = 'none';
        nametable3.style.display = 'none';

        // Button 2 click event
        button2.addEventListener('click', () => {
            // Toggle button active state
            button2.classList.add('active');
            button1.classList.remove('active');
            button3.classList.remove('active');

            // Toggle table visibility
            nametable2.style.display = '';
            nametable1.style.display = 'none';
            nametable3.style.display = 'none';
        });

        // Button 1 click event
        button1.addEventListener('click', () => {
            // Toggle button active state
            button1.classList.add('active');
            button2.classList.remove('active');
            button3.classList.remove('active');

            // Toggle table visibility
            nametable1.style.display = '';
            nametable2.style.display = 'none';
            nametable3.style.display = 'none';
        });

        // Button 3 click event
        button3.addEventListener('click', () => {
            // Toggle button active state
            button3.classList.add('active');
            button1.classList.remove('active');
            button2.classList.remove('active');

            // Toggle table visibility
            nametable3.style.display = '';
            nametable1.style.display = 'none';
            nametable2.style.display = 'none';
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
                                    <h2>ประวัติการขอเปลี่ยนวันหยุด</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor: default;">วันหยุด</a>
                                        </li>
                                        <li class=" breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            ประวัติการขอเปลี่ยนวันหยุด
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
                                                <th class="approver">บทบาท</th>
                                                <th class="status">สถานะ</th>
                                                <th class="action">การจัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($confirmed_requests as $confirmed_request) { ?>
                                                <tr>
                                                    <td class="old-dayoff">
                                                        <?php echo $confirmed_request['day_off1'] . "-" . $confirmed_request['day_off2'] ?>
                                                    </td>
                                                    <td class="new-dayoff">
                                                        <?php echo $confirmed_request['day_off1'] . "-" . $confirmed_request['day_off2'] ?>
                                                    </td>
                                                    <td class="approver">
                                                        <div class="row">
                                                            <div style="margin-right: 5px;">
                                                                <img src="<?php echo (!empty($confirmed_request['employee_image'])) ? '../../admin/uploads_img/' . $confirmed_request['employee_image'] : '../IMG/user.png'; ?>" class="border-radius-100 shadow" width="40" height="40" alt="">
                                                            </div>
                                                            <div>
                                                                <b>
                                                                    <?php echo $confirmed_request['prefix_thai'] . $confirmed_request['firstname_thai'] . " " . $confirmed_request['lastname_thai'] ?>
                                                                </b><br>
                                                                <a class="
                                                                text-primary">
                                                                    <?php echo $confirmed_request['employee_email'] ?>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="status" style="color: green !important;">อนุมัติแล้ว</td>
                                                    <td class=" action"><button class="btn btn-info" onclick="window.location.href='day-off-detail-status-employee.php?id=<?php echo $confirmed_request['day_off_req_id'] ?>'">รายละเอียด</button>
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
                                                <th class="approver">บทบาท</th>
                                                <th class="status">สถานะ</th>
                                                <th class="action">การจัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($waiting_requests as $waiting_request) { ?>
                                                <tr>
                                                    <td class="old-dayoff">
                                                        <?php echo $waiting_request['day_off1'] . "-" . $waiting_request['day_off2'] ?>
                                                    </td>
                                                    <td class="new-dayoff">
                                                        <?php echo $waiting_request['day_off1'] . "-" . $waiting_request['day_off2'] ?>
                                                    </td>
                                                    <td class="approver">
                                                        <div class="row">
                                                            <div style="margin-right: 5px;">
                                                                <img src="<?php echo (!empty($waiting_request['employee_image'])) ? '../../admin/uploads_img/' . $waiting_request['employee_image'] : '../IMG/test.jpg'; ?>" class="border-radius-100 shadow" width="40" height="40" alt="">
                                                            </div>
                                                            <div>
                                                                <b>
                                                                    <?php echo $waiting_request['prefix_thai'] . $waiting_request['firstname_thai'] . " " . $waiting_request['lastname_thai'] ?>
                                                                </b><br>
                                                                <a class="
                                                                text-primary">
                                                                    <?php echo $waiting_request['employee_email'] ?>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="status" style="color: orange !important;">รออนุมัติ</td>
                                                    <td class=" action"><button class="btn btn-info" onclick="window.location.href='day-off-detail-status-employee.php?id=<?php echo $waiting_request['day_off_req_id'] ?>'">รายละเอียด</button>
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
                                                <th class="approver">บทบาท</th>
                                                <th class="status">สถานะ</th>
                                                <th class="action">การจัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($rejected_requests as $rejected_request) { ?>
                                                <tr>
                                                    <td class="old-dayoff">
                                                        <?php echo $rejected_request['day_off1'] . "-" . $rejected_request['day_off2'] ?>
                                                    </td>
                                                    <td class="new-dayoff">
                                                        <?php echo $rejected_request['day_off1'] . "-" . $rejected_request['day_off2'] ?>
                                                    </td>
                                                    <td class="approver">
                                                        <div class="row">
                                                            <div style="margin-right: 5px;">
                                                                <img src="<?php echo (!empty($rejected_request['employee_image'])) ? '../../admin/uploads_img/' . $rejected_request['employee_image'] : '../IMG/user.png'; ?>" class="border-radius-100 shadow" width="40" height="40" alt="">
                                                            </div>
                                                            <div>
                                                                <b>
                                                                    <?php echo $rejected_request['prefix_thai'] . $rejected_request['firstname_thai'] . " " . $rejected_request['lastname_thai'] ?>
                                                                </b><br>
                                                                <a class="
                                                                text-primary">
                                                                    <?php echo $rejected_request['employee_email'] ?>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="status" style="color: red !important;">ปฏิเสธ</td>
                                                    <td class=" action"><button class="btn btn-info" onclick="window.location.href='day-off-detail-status-employee.php?id=<?php echo $rejected_request['day_off_req_id'] ?>'">รายละเอียด</button>
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
                        <th class="approver">บทบาท</th>
                        <th class="status">สถานะ</th>
                        <th class="action">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($confirmed_requests as $confirmed_request) { ?>
                        <tr>
                            <td class="old-dayoff">
                                <?php echo $confirmed_request['day_off1'] . "-" . $confirmed_request['day_off2'] ?>
                            </td>
                            <td class="new-dayoff">
                                <?php echo $confirmed_request['day_off1'] . "-" . $confirmed_request['day_off2'] ?>
                            </td>
                            <td class="approver">
                                <div class="row">
                                    
                                    <div>
                                        <b>
                                            <?php echo $confirmed_request['prefix_thai'] . $confirmed_request['firstname_thai'] . " " . $confirmed_request['lastname_thai'] ?>
                                        </b><br>
                                        <a class="
                                                                text-primary">
                                            <?php echo $confirmed_request['employee_email'] ?>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="status" style="color: green !important;">อนุมัติ</td>
                            <td class=" action"><button class="btn btn-info" onclick="window.location.href='day-off-detail-status-employee.php?id=<?php echo $confirmed_request['day_off_req_id'] ?>'">รายละเอียด</button>
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
                        <th class="approver">บทบาท</th>
                        <th class="status">สถานะ</th>
                        <th class="action">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($waiting_requests as $waiting_request) { ?>
                        <tr>
                            <td class="old-dayoff">
                                <?php echo $waiting_request['day_off1'] . "-" . $waiting_request['day_off2'] ?>
                            </td>
                            <td class="new-dayoff">
                                <?php echo $waiting_request['day_off1'] . "-" . $waiting_request['day_off2'] ?>
                            </td>
                            <td class="approver">
                                <div class="row">
                                    <div style="margin-right: 5px;">
                                        <img src="<?php echo (!empty($waiting_request['employee_image'])) ? '../../admin/uploads_img/' . $waiting_request['employee_image'] : '../IMG/test.jpg'; ?>" class="border-radius-100 shadow" width="40" height="40" alt="">
                                    </div>
                                    <div>
                                        <b>
                                            <?php echo $waiting_request['prefix_thai'] . $waiting_request['firstname_thai'] . " " . $waiting_request['lastname_thai'] ?>
                                        </b><br>
                                        <a class="
                                                                text-primary">
                                            <?php echo $waiting_request['employee_email'] ?>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="status" style="color: yellow !important;">รออนุมัติ</td>
                            <td class=" action"><button class="btn btn-info" onclick="window.location.href='day-off-detail-status-employee.php?id=<?php echo $waiting_request['day_off_req_id'] ?>'">รายละเอียด</button>
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
                        <th class="approver">บทบาท</th>
                        <th class="status">สถานะ</th>
                        <th class="action">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rejected_requests as $rejected_request) { ?>
                        <tr>
                            <td class="old-dayoff">
                                <?php echo $rejected_request['day_off1'] . "-" . $rejected_request['day_off2'] ?>
                            </td>
                            <td class="new-dayoff">
                                <?php echo $rejected_request['day_off1'] . "-" . $rejected_request['day_off2'] ?>
                            </td>
                            <td class="approver">
                                <div class="row">
                                    <div style="margin-right: 5px;">
                                        <img src="<?php echo (!empty($rejected_request['employee_image'])) ? '../../admin/uploads_img/' . $rejected_request['employee_image'] : '../IMG/user.png'; ?>" class="border-radius-100 shadow" width="40" height="40" alt="">
                                    </div>
                                    <div>
                                        <b>
                                            <?php echo $rejected_request['prefix_thai'] . $rejected_request['firstname_thai'] . " " . $rejected_request['lastname_thai'] ?>
                                        </b><br>
                                        <a class="
                                                                text-primary">
                                            <?php echo $rejected_request['employee_email'] ?>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="status" style="color: red !important;">ปฏิเสธ</td>
                            <td class=" action"><button class="btn btn-info" onclick="window.location.href='day-off-detail-status-employee.php?id=<?php echo $rejected_request['day_off_req_id'] ?>'">รายละเอียด</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
<?php include('../includes/footer.php') ?>