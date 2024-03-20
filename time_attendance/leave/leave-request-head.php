<?php
session_start();
session_regenerate_id(true);
//-------------------------------------------------------------------------------------------------------------------------------
include("../database/connectdb.php");
header("Cache-Control: no-cache, must-revalidate");
include('../components-desktop/head/include/header.php')
?>

<!-- Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="node_modules\sweetalert2\dist\sweetalert2.css">

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/head/leave-request-head.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/leave-request.css">
<link rel=" stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="../assets/script/loader-process.js"></script>

<?php
//---------------------------------------------------------------------------------------

// ทำความสะอาดและป้องกันข้อมูล
$_SESSION['card_id'] = isset($_SESSION['card_id']) ? $_SESSION['card_id'] : '';  // ตรวจสอบว่ามีค่าหรือไม่
$_SESSION['card_id'] = trim($_SESSION['card_id']);  // ลบช่องว่างที่อาจเกิดขึ้นทั้งด้านหน้าและด้านหลังของข้อมูล
$_SESSION['card_id'] = preg_replace('/[^a-zA-Z0-9]/', '', $_SESSION['card_id']);  // ลบทุกอักขระที่ไม่ใช่ a-z, A-Z, และ 0-9
$_SESSION['card_id'] = htmlspecialchars($_SESSION['card_id'], ENT_QUOTES, 'UTF-8');  // แปลงสัญลักษณ์ HTML เพื่อป้องกัน XSS

//---------------------------------------------------------------------------------------

// ตรวจสอบการลงทะเบียน
if (!empty($_SESSION['card_id'])) {
    // สร้าง query เพื่อดึงข้อมูลพนักงาน
    $query = "SELECT * FROM employee WHERE card_id = ?";
    $params = array($_SESSION['card_id']);
    $sql_absence = sqlsrv_query($conn, $query, $params);

    $select_team = "SELECT * FROM sub_team WHERE head_card_id = ?";

    // Prepare the SQL statement
    $dayoff_stmt = sqlsrv_prepare($conn, $select_team, array(&$_SESSION["card_id"]));
    // Execute the statement
    sqlsrv_execute($dayoff_stmt);
    // Fetch the results
    $row = sqlsrv_fetch_array($dayoff_stmt, SQLSRV_FETCH_ASSOC);

    //------------------------------------------------------------------------------------------

    $select_leaveteam = "SELECT
    employee.scg_employee_id AS employee_id,
    employee.prefix_thai AS prefix_thai,
    employee.firstname_thai AS firstname_thai,
    employee.lastname_thai AS lastname_thai,
    employee.card_id AS card_id
     FROM employee
     INNER JOIN work_format ON employee.work_format_code = work_format.work_format_code
     WHERE employee.cost_center_organization_id IN (SELECT cost_center_organization_id FROM employee WHERE card_id = ? )";
    // Prepare the SQL statement
    $leaveteam_stmt = sqlsrv_prepare($conn, $select_leaveteam, array($_SESSION["card_id"]));
    sqlsrv_execute($leaveteam_stmt);

    $option_employee = '';
    while ($rs_emp = sqlsrv_fetch_array($leaveteam_stmt, SQLSRV_FETCH_ASSOC)) {
        $option_employee .= '<option value="' . $rs_emp['card_id'] . '">' . $rs_emp['employee_id'] . ' ' . $rs_emp['prefix_thai'] . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai'] . '</option>';
    }


    //--------------------------------------------------------------------------------------------


    // ตรวจสอบว่ามีข้อมูลหรือไม่
    if ($sql_absence === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_has_rows($sql_absence)) {
        // ดึงข้อมูลจากตาราง absence_type
        $sql_emp = sqlsrv_query($conn, "SELECT * FROM absence_type");
        $option_absence_type = '';
        while ($rs_emp = sqlsrv_fetch_array($sql_emp, SQLSRV_FETCH_ASSOC)) {
            $option_absence_type .=  '<option value="' . $rs_emp['absence_type_id'] . '">' . $rs_emp['name'] . '</option>';
        }

        // ตรวจสอบว่า query สำเร็จหรือไม่
        if ($sql_emp === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    } else {
        // แจ้งเตือนถ้ายังไม่ได้ลงทะเบียน
        // echo '<script>
        // alert("คุณยังไม่ได้ลงทะเบียน");
        // window.location.href = "index.html";
        // </script>';
    }
} else {
    // แจ้งเตือนถ้ายังไม่ได้ลงทะเบียน
    //     echo '<script>
    //     alert("คุณยังไม่ได้ลงทะเบียน");
    //     window.location.href = "index.html";
    //     </script>';
}
?>

<!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

<script type="text/javascript">
function myFunction() {
    swal.fire({
        html: '<div style="font-weight: bold; font-size: 4vw;">ยืนยันการยื่นคำขอ</div><br>' +
            '<img class="img" src="../IMG/question 1.png"></img>',
        padding: '2em',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#29ab29',
        showCancelButton: true,
        cancelButtonText: 'ยกเลิก',
        cancelButtonColor: '#e1574b',
        customClass: {
            confirmButtonText: 'swal2-confirm',
            cancelButtonText: 'swal2-cancel',
        },
    }).then((result) => {
        if (result.isConfirmed) {
            myFunction2(result);
        } else {
            swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
        }
    });
}

function myFunction2() {
    swal.fire({
        html: '<div style="font-weight: bold; font-size: 4vw;">ยื่นคำขอสำเร็จ</div><br>' +
            '<img class="img" src="../IMG/check1.png"></img>',
        padding: '2em',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#29ab29',
        customClass: {
            confirmButtonText: 'swal2-confirm',
            cancelButtonText: 'swal2-cancel',
        },
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('leaveForm').submit();
        } else {
            swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
        }
    });
}
</script>

<!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

<script>
function calculateLeaveday() {
    // ดึงข้อมูลจากฟอร์ม
    var startDate = new Date(document.getElementById('startDate').value);
    var endDate = new Date(document.getElementById('endDate').value);

    // คำนวณระยะเวลา
    var durationInMilliseconds = endDate - startDate;

    // แปลงระยะเวลาเป็นวัน
    var days = calculateDays(durationInMilliseconds);

    // แสดงผลลัพธ์
    document.getElementById('daysDuration').textContent = days;
    // สามารถเพิ่มการดำเนินการเพิ่มเติมที่นี่ตามต้องการ
}

function calculateLeavehours() {
    var startTime = new Date(document.getElementById('startTime').value);
    var endTime = new Date(document.getElementById('endTime').value);

    // คำนวณระยะเวลา
    var durationInMilliseconds = endTime - startTime;

    // แปลงระยะเวลาเป็นชั่วโมง
    var hours = calculateHours(durationInMilliseconds);

    // แสดงผลลัพธ์
    document.getElementById('hoursDuration').textContent = hours;
    // สามารถเพิ่มการดำเนินการเพิ่มเติมที่นี่ตามต้องการ
}

function calculateDays(durationInMilliseconds) {
    var millisecondsInOneDay = 24 * 60 * 60 * 1000;
    return Math.floor(durationInMilliseconds / millisecondsInOneDay);
}

function calculateHours(durationInMilliseconds) {
    var millisecondsInOneHour = 60 * 60 * 1000;
    return Math.floor(durationInMilliseconds / millisecondsInOneHour);
}

function count_day() {
    var leaveType = parseInt(document.getElementById('leaveType').value);
    var day_balance;
    if (leaveType == "") {
        alert("กรุณาเลือกประเภทการลา");
        return false; // Prevent the form from submitting
    } else {
        switch (leaveType) {
            case 1:
                // 1 วันลาพักร้อน (วัน)
                day_balance = <?php echo $_SESSION["annual_leave_balance"]; ?>;
                break;
            case 2:
                // 2 วันลาพักร้อนสะสม (วัน)
                day_balance = 20;
                break;
            case 3:
                // 3 วันลาคลอด (วัน)
                day_balance = <?php echo $_SESSION["maternity_leave_balance"]; ?>;
                hours_balance = 0;
                break;
            case 4:
                // 4 วันลาบวช (วัน)
                day_balance = <?php echo $_SESSION["ordination_leave_balance"]; ?>;
                hours_balance = 0;
                break;
            case 5:
                // 5 วันลาบวช ไม่ได้รับค่าจ้าง (วัน)
                day_balance = <?php echo $_SESSION["ordination_leave_nopaid_balance"]; ?>;
                hours_balance = 0;
                break;
            case 6:
                // 6 วันลาฮัจจี (วัน)
                day_balance = <?php echo $_SESSION["haj_leave_balance"]; ?>;
                hours_balance = 0;
                break;
            case 7:
                // 7 วันลาฮัจจี ไม่ได้รับค่าจ้าง (วัน)
                day_balance = <?php echo $_SESSION["haj_leave_nopaid_balance"]; ?>;
                hours_balance = 0;
                break;
            case 8:
                // 8 วันลาอบรม ไม่ได้รับค่าจ้าง (วัน)
                day_balance = <?php echo $_SESSION["training_leave_nopaid_balance"]; ?>;
                hours_balance = 0;
                break;
            case 9:
                // 9 วันลา CSR (วัน)
                day_balance = <?php echo $_SESSION["csr_leave_balance"]; ?>;
                hours_balance = 0;
                break;
            case 10:
                // 10 วันลาป่วยเกี่ยวกับการทำงาน (ชั่วโมง)
                hours_balance = <?php echo $_SESSION["work_sick_leave_balance"]; ?>;
                day_balance = 0;
                break;
            case 11:
                // 11 วันลาทหาร (วัน)
                day_balance = <?php echo $_SESSION["military_service_leave_balance"]; ?>;
                hours_balance = 0;
                break;
            case 12:
                // 12 วันลาอื่น ๆ (วัน)
                day_balance = <?php echo $_SESSION["other_leave_balance"]; ?>;
                hours_balance = 0;
                break;
            case 13:
                // 13 วันลาอื่น ๆ ไม่ได้รับค่าจ้าง (วัน)
                day_balance = <?php echo $_SESSION["other_leave_nopaid_balance"]; ?>;
                hours_balance = 0;
                break;
            case 14:
                // 14 วันลาป่วย (วัน)
                day_balance = <?php echo $_SESSION["sick_leave_balance"]; ?>;
                hours_balance = 0;
                break;
            default:
                // กรณีที่ไม่ตรงกับทุก case ข้างต้น
                break;
        }
    }

    document.getElementById('day_balance').textContent = day_balance;
    document.getElementById('hours_balance').textContent = hours_balance;
}
</script>

<!-- desktop script -->
<script>
function add_leave_submit() {
    Swal.fire({
        title: "<strong>ยืนยันการทำรายการลาหรือไม่</strong>",
        icon: "question",
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: `ตกลง`,
        cancelButtonText: `ยกเลิก`,
    }).then((result) => {
        if (result.isConfirmed) {
            add_leave_confirm()
        } else {
            swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
        }
    });
}

function add_leave_confirm() {
    Swal.fire({
        title: "<strong>ทำรายการลาสำเร็จ</strong>",
        icon: "success",
        showCloseButton: true,
        focusConfirm: false,
        confirmButtonText: `ตกลง`,
    }).then((result) => {
        if (result.isConfirmed) {
            let form = document.getElementById("desktop-form");
            form.action = '../processing/process_Leave_Request_head.php';
            form.method = 'POST';
            form.submit();
        }
    });
}

function add_leave_cancel() {
    Swal.fire({
        title: "<strong>ยืนยันยกเลิกทำรายการลา</strong>",
        icon: "warning",
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: `ตกลง`,
        cancelButtonText: `ยกเลิก`,
    }).then((result) => {
        if (result.isConfirmed) {
            add_leave_cancel_confirm();
        }
    });
}

function add_leave_cancel_confirm() {
    Swal.fire({
        title: "<strong>ยกเลิกการทำรายการลาสำเร็จ</strong>",
        icon: "success",
        showCloseButton: true,
        focusConfirm: false,
        confirmButtonText: `ตกลง`,
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "leave-rights-head.php";
        }
    });
}

function desktop_count_day() {
    var leaveType = parseInt(document.getElementById('desktop-leave-type').value);
    if (leaveType == "") {
        alert("กรุณาเลือกประเภทการลา");
        return false; // Prevent the form from submitting
    } else {
        switch (leaveType) {
            case 1:
                // 1 วันลาพักร้อน (วัน)
                day_balance = <?php echo $_SESSION["annual_leave_balance"]; ?>;
                hours_balance = 0;
                break;
            case 2:
                // 2 วันลาพักร้อนสะสม (วัน)
                day_balance = 20;
                hours_balance = 0;
                break;
            case 3:
                // 3 วันลาคลอด (วัน)
                day_balance = <?php echo $_SESSION["maternity_leave_balance"]; ?>;
                hours_balance = 0;
                break;
            case 4:
                // 4 วันลาบวช (วัน)
                day_balance = <?php echo $_SESSION["ordination_leave_balance"]; ?>;
                hours_balance = 0;
                break;
            case 5:
                // 5 วันลาบวช ไม่ได้รับค่าจ้าง (วัน)
                day_balance = <?php echo $_SESSION["ordination_leave_nopaid_balance"]; ?>;
                hours_balance = 0;
                break;
            case 6:
                // 6 วันลาฮัจจี (วัน)
                day_balance = <?php echo $_SESSION["haj_leave_balance"]; ?>;
                hours_balance = 0;
                break;
            case 7:
                // 7 วันลาฮัจจี ไม่ได้รับค่าจ้าง (วัน)
                day_balance = <?php echo $_SESSION["haj_leave_nopaid_balance"]; ?>;
                hours_balance = 0;
                break;
            case 8:
                // 8 วันลาอบรม ไม่ได้รับค่าจ้าง (วัน)
                day_balance = <?php echo $_SESSION["training_leave_nopaid_balance"]; ?>;
                hours_balance = 0;
                break;
            case 9:
                // 9 วันลา CSR (วัน)
                day_balance = <?php echo $_SESSION["csr_leave_balance"]; ?>;
                hours_balance = 0;
                break;
            case 10:
                // 10 วันลาป่วยเกี่ยวกับการทำงาน (ชั่วโมง)
                hours_balance = <?php echo $_SESSION["work_sick_leave_balance"]; ?>;
                day_balance = 0;
                break;
            case 11:
                // 11 วันลาทหาร (วัน)
                day_balance = <?php echo $_SESSION["military_service_leave_balance"]; ?>;
                hours_balance = 0;
                break;
            case 12:
                // 12 วันลาอื่น ๆ (วัน)
                day_balance = <?php echo $_SESSION["other_leave_balance"]; ?>;
                hours_balance = 0;
                break;
            case 13:
                // 13 วันลาอื่น ๆ ไม่ได้รับค่าจ้าง (วัน)
                day_balance = <?php echo $_SESSION["other_leave_nopaid_balance"]; ?>;
                hours_balance = 0;
                break;
            case 14:
                // 14 วันลาป่วย (วัน)
                day_balance = <?php echo $_SESSION["sick_leave_balance"]; ?>;
                hours_balance = 0;
                break;
            default:
                // กรณีที่ไม่ตรงกับทุก case ข้างต้น
                break;
        }
    }

    document.getElementById('desktop-day-balance').textContent = day_balance;
    document.getElementById('desktop-hour-balance').textContent = hours_balance;
}

function desktop_cal_duration() {
    // ดึงข้อมูลจากฟอร์ม
    var startDate = new Date(document.getElementById('desktop-start-date').value + 'T' + document.getElementById(
        'desktop-start-time').value);
    var endDate = new Date(document.getElementById('desktop-end-date').value + 'T' + document.getElementById(
        'desktop-end-time').value);
    // คำนวณจำนวนวันที่ลา
    var timeDifference = endDate - startDate;
    var days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
    var hours = ((timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)).toFixed(2);
    // แสดงผลลัพธ์
    document.getElementById('desktop-day-duration').textContent = days;
    document.getElementById('desktop-hour-duration').textContent = hours;
    // สามารถเพิ่มการดำเนินการเพิ่มเติมที่นี่ตามต้องการ
}
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
                                        <h2>ทำรายการลา : Leave Request</h2>
                                    </div>
                                    <nav aria-label="breadcrumb" role="navigation">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item">
                                                <a style="pointer-events:none;cursor: default;">การลา</a>
                                            </li>
                                            <li class=" breadcrumb-item active" aria-current="page"
                                                style="cursor:default;">
                                                ทำรายการลา
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                                <div class="card-box pd-30 pt-10 height-100-p">
                                    <div class="wizard-content">
                                        <section>
                                            <form id="desktop-form">
                                                <div class="row">
                                                    <div class="col-md-7 col-sm-12">
                                                        <div class="form-group">
                                                            <label for="employeeid">สมาชิกในทีม</label>
                                                            <select
                                                                class="custom-select form-control js-example-basic-single"
                                                                name="employeeid" id="desktop-employeeid">
                                                                <option value="" disabled selected>
                                                                    เลือกสมาชิกในทีม
                                                                </option>
                                                                <?php
                                                                echo $option_employee;
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-7 col-sm-12">
                                                        <div class="form-group">
                                                            <label>เลือกประเภทการลา</label>
                                                            <select class="custom-select form-control"
                                                                id="desktop-leave-type" name="leaveType"
                                                                onchange="desktop_count_day()">
                                                                <option value="" disabled selected>เลือกประเภทการลา
                                                                </option>
                                                                <?php
                                                                echo $option_absence_type;
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-7 col-sm-12">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label>วันที่เริ่มต้น</label>
                                                                    <input class="form-control" type="date"
                                                                        id="desktop-start-date" name="startDate"
                                                                        onchange="desktop_cal_duration()">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label>ถึงวันที่</label>
                                                                    <input class="form-control" type="date"
                                                                        id="desktop-end-date" name="endDate"
                                                                        onchange="desktop_cal_duration()">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-7 col-sm-12">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label>ตั้งแต่เวลา</label>
                                                                    <input class="form-control" type="time"
                                                                        id="desktop-start-time" name="startTime"
                                                                        onchange="desktop_cal_duration()">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label>ถึงเวลา</label>
                                                                    <input class="form-control" type="time"
                                                                        id="desktop-end-time" name="endTime"
                                                                        onchange="desktop_cal_duration()">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-7 col-sm-12">
                                                        <div class="form-group">
                                                            <label>รายละเอียดคำขอลา</label>
                                                            <input class="form-control" type="text"
                                                                id="desktop-leave-detail" name="leaveDetails"
                                                                placeholder="รายละเอียด (ถ้ามี)">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-7 col-sm-12"
                                                        style="display:flex;justify-content:flex-end;gap:5%;align-items:center;">
                                                        <div class="form-group" name="duration">
                                                            <label>จำนวนการลา</label>
                                                            <label><a id="desktop-day-duration">0 </a>
                                                                วัน</label>
                                                            <label><a id="desktop-hour-duration">0 </a>
                                                                ชม.</label>
                                                        </div>
                                                        <div class="form-group" name="balance">
                                                            <label>สิทธิคงเหลือ</label>
                                                            <label><a id="desktop-day-balance">0</a>
                                                                วัน</label>
                                                            <label><a id="desktop-day-balance">0</a>
                                                                ชม.</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </section>
                                        <div class="row">
                                            <div class="col-md-7 col-sm-12"
                                                style="display:flex;justify-content:flex-end;margin-top:20px">
                                                <div class="form-group">
                                                    <input type="button" value="ยืนยัน" class="btn-primary"
                                                        onclick="add_leave_submit()">
                                                    <input type="button" class="btn-danger" value="ยกเลิกคำขอ"
                                                        onclick="add_leave_cancel()"><br><br>
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
                    <span>ร้องขอการลา</span>
                </div>
            </div>

            <div class="topic">
                <span>กรอกข้อมูลการลา</span>
            </div>

            <form id="leaveForm" action="../processing/process_Leave_team_Request.php" method="post"
                onsubmit="return validateForm();">
                <div class="boxKey">

                    <div class="member">
                        <span>สมาชิกในทีม</span>
                        <select name="employeeid" id="employeeid" class="js-example-basic-single">
                            <option value="">เลือกสมาชิกในทีม</option>
                            <?php
                            echo $option_employee;
                            ?>
                        </select>
                    </div>
                    <div class="typeLeave">
                        <span>ประเภทการลา</span>
                        <select name="leaveType" id="leaveType" onchange="count_day()">
                            <option value="">เลือกประเภทการลา</option>
                            <?php
                            echo $option_absence_type;
                            ?>
                        </select>
                    </div>
                    <div class="req-date">
                        <div class="date">
                            <span>วันเริ่มต้น</span>
                            <input type="date" id="startDate" name="startDate" placeholder="วัน/เดือน/ปี"
                                onchange="calculateLeaveday()">
                        </div>
                        <div class="date">
                            <span>ถึงวันที่</span>
                            <input type="date" id="endDate" name="endDate" placeholder="วัน/เดือน/ปี"
                                onchange="calculateLeaveday()">
                        </div>
                    </div>
                    <div class="req-time">
                        <div class="time">
                            <span>ตั้งแต่เวลา</span>
                            <input type="time" id="startTime" name="startTime" placeholder="00:00"
                                onchange="calculateLeavehours()">
                        </div>
                        <div class="time">
                            <span>ถึงเวลา</span>
                            <input type="time" id="endTime" name="endTime" placeholder="00:00"
                                onchange="calculateLeavehours()">
                        </div>
                    </div>
                    <div class="detail">
                        <span>รายละเอียดคำขอลา</span>
                        <input type="text" id="leaveDetails" name="leaveDetails" placeholder="รายละเอียด (ถ้ามี)">
                    </div>
                    <div class="time-cal">
                        <div class="duration">
                            <div class="high-light">
                                <span><a id="daysDuration">0 </a> วัน</span>
                                <span><a id="hoursDuration">0 </a> ชม.</span>
                            </div>
                            <span>จำนวนการลา</span>
                        </div>
                        <div class="balance">
                            <div class="high-light">
                                <span><a id="day_balance">0</a> วัน</span>
                                <span><a id="hours_balance">0</a> ชม.</span>
                            </div>
                            <span>สิทธิ์การลาคงเหลือ</span>
                        </div>
                    </div>
                </div>
            </form>

            <div class="approve">
                <div class="btn-approve">
                    <input type="button" value="ยืนยัน" class="btnSubmit" onclick="myFunction()">
                </div>
            </div>
        </div>

    </div>
</body>
<?php include('../includes/footer.php'); ?>