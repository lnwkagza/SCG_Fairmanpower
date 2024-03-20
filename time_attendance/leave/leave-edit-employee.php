<?php
session_start();
session_regenerate_id(true);

include("../database/connectdb.php");
include('../components-desktop/employee/include/header.php');
?>

<!-- Script -->
<link rel="stylesheet" href="node_modules\sweetalert2\dist\sweetalert2.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/employee/leave-edit-employee.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/leave-edit.css">
<link rel=" stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="../assets/script/loader-normal.js"></script>

<?php
//---------------------------------------------------------------------------------------

$id = isset($_GET['id']) ? $_GET['id'] : '';
$id = trim($id);
$id = preg_replace('/[^a-zA-Z0-9]/', '', $id);
$id = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');

// $_SESSION['card_id'] = isset($_SESSION['card_id']) ? $_SESSION['card_id'] : '';
$_SESSION['card_id'] = trim($_SESSION['card_id']);
$_SESSION['card_id'] = preg_replace('/[^a-zA-Z0-9]/', '', $_SESSION['card_id']);
$_SESSION['card_id'] = htmlspecialchars($_SESSION['card_id'], ENT_QUOTES, 'UTF-8');

//---------------------------------------------------------------------------------------

if (!empty($_SESSION['card_id'])) {
    $sql = "SELECT 
                absence_record.card_id as card_id,
                absence_record.absence_type_id as absence_type_id,
                absence_record.date_start as date_start,
                absence_record.date_end as date_end,
                absence_record.time_start as time_start,
                absence_record.time_end as time_end,
                absence_record.request_detail as request_detail,
                absence_record.approve_status as approve_status,
                absence_record.approve_time as approve_time,
                absence_type.name as absence_type_name
            FROM absence_record
            INNER JOIN absence_type ON absence_record.absence_type_id = absence_type.absence_type_id
            WHERE absence_record_id = ? AND card_id = ?";
    $params = array($id, $_SESSION['card_id']);
    $sql_absence = sqlsrv_query($conn, $sql, $params);
    $row = sqlsrv_fetch_array($sql_absence, SQLSRV_FETCH_ASSOC);

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
        $remainingDays = $remainingDays -1;
    }
    //---------------------------------------------------------------------------------------

    if ($row != false) {
        // echo '<script>
        //         alert("คุณไม่ได้รับอนุญาต");
        //         window.location.href = "../index.html";    
        //     </script>';
    } else {
    }
    //---------------------------------------------------------------------------------------

} else {
    // echo '<script>
    //         alert("คุณไม่ได้รับอนุญาต");
    //         window.location.href = "../index.html";
    //   </script>';
}
?>

<script type="text/javascript">
    function myFunction() {
        swal.fire({
            html: '<div style="font-weight: bold; font-size: 4vw;">ยืนยันการยื่นคำขอ</div><br>' +
                '<img class="img" src="../IMG/question 1.png"> </img>',
            padding: '2em',
            confirmButtonText: 'ตกลง',
            confirmButtonColor: '#29ab29',
            showCancelButton: true,
            cancelButtonText: 'ยกเลิก',
            cancelButtonColor: '#FF5643',
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

    function myFunction3() {
        swal.fire({
            html: '<div style="font-weight: bold; font-size: 4vw;">ยืนยันการยกเลิกคำขอ</div><br>' +
                '<img class="img" src="../IMG/question 1.png" ></img>',
            padding: '3em',
            confirmButtonText: 'ตกลง',
            confirmButtonColor: '#29ab29',
            showCancelButton: true,
            cancelButtonText: 'ยกเลิก',
            cancelButtonColor: '#F22738',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "../processing/process_Leave_Request_delete.php?id=<?= $id; ?>";
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });
    }

    function calculateLeaveDuration() {
        // ดึงข้อมูลจากฟอร์ม
        var startDate = new Date(document.getElementById('startDate').value + 'T' + document.getElementById('startTime')
            .value);
        var endDate = new Date(document.getElementById('endDate').value + 'T' + document.getElementById('endTime').value);
        // คำนวณจำนวนวันที่ลา
        var timeDifference = endDate - startDate;
        var days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
        var hours = ((timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)).toFixed(2);
        // แสดงผลลัพธ์
        document.getElementById('daysDuration').textContent = days;
        document.getElementById('hoursDuration').textContent = hours;
        // สามารถเพิ่มการดำเนินการเพิ่มเติมที่นี่ตามต้องการ
    }
</script>

<script>
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

    function edit_submit() {
        Swal.fire({
            title: "<strong>ยืนยันการแก้ไขการลาหรือไม่</strong>",
            icon: "question",
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: `ตกลง`,
            cancelButtonText: `ยกเลิก`,
        }).then((result) => {
            if (result.isConfirmed) {
                edit_confirm();
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });

    }

    function edit_confirm() {
        Swal.fire({
            title: "<strong>แก้ไขการลาสำเร็จ</strong>",
            icon: "success",
            showCloseButton: true,
            focusConfirm: false,
            confirmButtonText: `ตกลง`,
        }).then((result) => {
            if (result.isConfirmed) {
                submit_form();
            }
        });
    }

    function edit_cancel() {
        Swal.fire({
            title: "<strong>ยืนยันยกเลิกคำขอการลา</strong>",
            icon: "warning",
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: `ตกลง`,
            cancelButtonText: `ยกเลิก`,
        }).then((result) => {
            if (result.isConfirmed) {
                edit_cancel_confirm();
            }
        });
    }

    function edit_cancel_confirm() {
        Swal.fire({
            title: "<strong>ยกเลิกคำขอการลาสำเร็จ</strong>",
            icon: "success",
            showCloseButton: true,
            focusConfirm: false,
            confirmButtonText: `ตกลง`,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "../processing/process_Leave_Request_delete.php?id=<?= $id; ?>";
            }
        });
    }

    function submit_form() {
        let form = document.getElementById("desktop-form");
        form.action = '../processing/process_Leave_Request_edit.php';
        form.method = 'POST';
        form.submit();
    }
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
                                    <h2>แก้ไขคำขอการลา : Leave Edit</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor:default;">การลา</a>
                                        </li>
                                        <li class=" breadcrumb-item">
                                            <a href="leave-history-employee.php">ประวัติการลา</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            แก้ไขคำขอการลา
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
                                    <form id="desktop-form">
                                        <input type="hidden" name="id" value="<?= $id; ?>">
                                        <section>
                                            <div class="row">
                                                <div class="col-md-7 col-sm-12">
                                                    <div class="form-group">
                                                        <label>ประเภทการลา</label>
                                                        <input class="form-control" value="<?php echo $row['absence_type_name']; ?>" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-7 col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label for="startDate">วันที่เริ่มต้น</label>
                                                                <input class=" form-control" type="date" name="startDate" id="desktop-start-date" value="<?php echo $row['date_start']->format('Y-m-d'); ?>" onchange="desktop_cal_duration()">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label for="endDate">ถึงวันที่</label>
                                                                <input class="form-control" type="date" name="endDate" id="desktop-end-date" value="<?php echo $row['date_end']->format('Y-m-d'); ?>" onchange="desktop_cal_duration()">
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
                                                                <label for="startTime">ตั้งแต่เวลา</label>
                                                                <input class="form-control" type="time" id="desktop-start-time" value="<?php echo $row['time_start']->format('H:i'); ?>" name="startTime" onchange="desktop_cal_duration()">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label for="endTime">ถึงเวลา</label>
                                                                <input class="form-control" type="time" id="desktop-end-time" name="endTime" value="<?php echo $row['time_end']->format('H:i'); ?>" onchange="desktop_cal_duration()">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-7 col-sm-12">
                                                    <div class="form-group">
                                                        <label>รายละเอียดคำขอลา</label>
                                                        <input class="form-control" name="request_detail" id="desktop-leave-detail" value="<?php echo $row['request_detail']; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-7 col-sm-12" style="display:flex;justify-content:flex-end;gap:10%">
                                                    <div class="form-group" name="duration">
                                                        <label>จำนวนวันลา</label>
                                                        <label><a id="daysDuration"><?= $daysDuration ?></a>
                                                            วัน</label>
                                                        <label><a id="hoursDuration"><?= $hoursDuration ?></a>
                                                            ชม.</label>
                                                    </div>
                                                    <div class="form-group" name="balance">
                                                        <label>สิทธิคงเหลือ</label>
                                                        <label><a id="day_balance">
                                                                <?= $remainingDays  ?>
                                                            </a>
                                                            วัน</label>
                                                        <label><a id="hours_balance"><?= gmdate("H . i ", $remainingHours * 3600); ?></a>
                                                            ชม.</label>
                                                    </div>
                                                </div>
                                        </section>
                                    </form>
                                    <div class="row">
                                        <div class="col-md-7 col-sm-12" style="display:flex;justify-content:flex-end;margin-top:20px">
                                            <div class="form-group">
                                                <input type="button" value="ยืนยัน" class="btn-primary" onclick="edit_submit()">
                                                <input type="button" class="btn-danger" value="ยกเลิกคำขอ" onclick="edit_cancel()"><br><br>
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
                <span>คำขอการลา</span>
            </div>
        </div>

        <div class="imgTop">
            <img src="../IMG/user.png" alt="">
        </div>

        <div class="detailTopic">
            <span>รายละเอียดการลา</span>
        </div>

        <form id="leaveForm" action="../processing/process_Leave_Request_edit.php" method="post">
            <input type="hidden" name="id" value="<?= $id; ?>">
            <div class="boxKey">
                <div class="typeLeave">
                    <span>ประเภทการลา</span>
                    <div class="display-type">
                        <input type="text" name="" id="" value="<?php echo $row['absence_type_name']; ?>">
                    </div>
                </div>

                <div class="edit-date">
                    <div class="date">
                        <span>วันเริ่มต้น</span>
                        <input type="date" name="startDate" id="startDate" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $row['date_start']->format('Y-m-d'); ?>" onchange="calculateLeaveday()">
                    </div>
                    <div class="date">
                        <span>ถึงวันที่</span>
                        <input type="date" name="endDate" id="endDate" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $row['date_end']->format('Y-m-d'); ?>" onchange="calculateLeaveday()">
                    </div>
                </div>


                <div class="edit-time">
                    <div class="time">
                        <span>ตั้งแต่เวลา</span>
                        <input type="time" name="startTime" id="startTime" value="<?php echo $row['time_start']->format('H:i'); ?>" onchange="calculateLeaveDuration()">
                    </div>
                    <div class="time">
                        <span>ถึงเวลา</span>
                        <input type="time" name="endTime" id="endTime" value="<?php echo $row['time_end']->format('H:i'); ?>" onchange="calculateLeaveDuration()">
                    </div>
                </div>


                <div class="detail">
                    <span>รายละเอียดคำขอลา</span>
                    <input name="request_detail" value="<?php echo $row['request_detail']; ?>">
                </div>

                <div class="time-cal">
                    <div class="duration">
                        <div class="high-light">
                            <span>
                                <label><a id="daysDuration"><?= $daysDuration ?></a> วัน</label>
                                <label><a id="hoursDuration"><?= $hoursDuration ?></a> ชม.</label>
                            </span>

                        </div>
                        <span>จำนวนการลา</span>
                    </div>
                    <div class="balance">
                        <div class="high-light">
                            <span>
                                <?= $remainingDays ?> วัน
                            </span>
                            <span><?= gmdate("H . i ", $remainingHours * 3600); ?> ชม.</span>
                        </div>
                        <span>สิทธิคงเหลือ</span>
                    </div>
                </div>
            </div>
        </form>

        <div class="button-action">
            <div class="btn-submit">
                <input type="submit" value="ยืนยัน" onclick="myFunction()" class="btnConfirm">
            </div>
            <div class="btn-reject">
                <input type="submit" value="ยกเลิกคำขอ" onclick="myFunction3()" class="btnReject"><br><br>
            </div>
        </div>
    </div>
</body>
<?php include('../includes/footer.php'); ?>