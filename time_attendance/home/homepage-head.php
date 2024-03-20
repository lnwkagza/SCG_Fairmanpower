<?php
//---------------------------------------------------------------------------------------
session_start();
session_regenerate_id(true);
//--------------------------------------------------------------------------------------------------------
include("../database/connectdb.php");
// include("dbconnect.php");
//--------------------------------------------------------------------------------------------------------
include('../components-desktop/head/include/header.php');
// require_once("../includes/header.php");

include("../thisWeek.php");
require_once("../check-in/test.php");


// Start the session

?>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/head/homepage-head.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/homepage.css">
<link rel="stylesheet" href="../assets/css/custom.css">
<link rel="stylesheet" href="node_modules\leaflet\dist\leaflet.css" />

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="script/loader-normal.js"></script>

<?php
// Check if the user is logged in (you can customize this check based on your requirements)
// if (!isset($_SESSION["scg_employee_id"]) || !isset($_SESSION["permission_id"])) {
//     // Redirect the user to the login page if not logged in
//     // header("Location: login.php");
//     exit();
// }
// Output some information

// echo "SCG Employee ID: " . $_SESSION["card_id"] . "<br>";


// if ($_SESSION["permission_id"] == "4") {
//     echo "พนักงาน";
// } elseif ($_SESSION["permission_id"] == "2") {
//     echo "หัวหน้า";
// } elseif ($_SESSION["permission_id"] == "1") {
//     echo "แอดมิน";
// }

// echo "<br>";



date_default_timezone_set('Asia/Bangkok');
$time_stamp = date("Y-m-d H:i:s");
$time = new week();
$time->set_day($time_stamp);
$start_mon = $time->get_start();
$end_mon = $time->get_end();
$start_mon_timestamp = strtotime($start_mon);
$start_mon_formatted = date("d/m", $start_mon_timestamp);
$dateshow = DateThai($time_stamp);

$time_now = date("H:i");
$date_now = date("Y-m-d");

// ป้องกัน SQL injection และใช้ prepared statements
$sql_emp = sqlsrv_query($conn, "SELECT * FROM employee WHERE card_id = ?", array($_SESSION["card_id"]));

// ดำเนินการต่อเพื่อดึงข้อมูล
$rs_emp = sqlsrv_fetch_array($sql_emp, SQLSRV_FETCH_ASSOC);

// ตรวจสอบว่ามีข้อมูลหรือไม่ก่อนนำไปใช้
if ($rs_emp) {
    $id = $rs_emp['card_id'];
    $name = $rs_emp['firstname_thai'];
    $lastname = $rs_emp['lastname_thai'];
    $fullname = $name . ' ' . $lastname;

    // ป้องกัน SQL injection ใน query ที่สอง
    // Initialize counters
    $countLeave = $countMiss = $countMissingWork = $countPunctual = $countBack = $countall = 0;

    // Fetch data from the first query
    $sql_checkincount = sqlsrv_query($conn, "SELECT *
        FROM manager 
        INNER JOIN check_inout ON manager.card_id = check_inout.card_id 
        WHERE manager.manager_card_id = ? AND check_inout.date = ?", array($_SESSION["card_id"], $time_stamp));

    // Process check-in and check-out data
    while ($rs_checkincount = sqlsrv_fetch_array($sql_checkincount, SQLSRV_FETCH_ASSOC)) {
        $time_in = $rs_checkincount['time_in'];
        $time_out = $rs_checkincount['time_out'];

        if ($time_in >= "07:30") {
            $countMiss++;
        } elseif ($time_in == "" && $time_out == "") {
            $countMissingWork++;
        } elseif ($time_in <= "07:30" && $time_out >= "16:30") {
            $countPunctual++;
        } elseif ($time_out <= "16:30") {
            $countBack++;
        }
    }

    // Fetch data from the second query
    $sql_absencecount = sqlsrv_query($conn, "SELECT *
    FROM manager 
    INNER JOIN absence_record ON manager.card_id = absence_record.card_id 
    WHERE manager.manager_card_id = ?
      AND ? BETWEEN absence_record.date_start AND absence_record.date_end;
    ", array($_SESSION["card_id"], $time_stamp));

    // Count leave occurrences
    while ($rs_absencecount = sqlsrv_fetch_array($sql_absencecount, SQLSRV_FETCH_ASSOC)) {
        $countLeave++;
    }

    // Calculate total count
    $countall = $countLeave + $countBack + $countPunctual + $countMissingWork + $countMiss;
}

?>

<script>
function submit_dayOff() {
    window.location.href = '../dayoff/day-off-head.php';
}

function submit_Leave() {
    window.location.href = '../leave/leave-rights-head.php';
}

function submit_workka() {
    window.location.href = '../shift/shift-head.php';
}

function submit_ot() {
    window.location.href = '../ot/ot-head.php';
}

function check_in_attendance_schedule() {
    window.location.href = '../check-in/check-in-warning-head.php';
}

function check_in_detail_more() {
    window.location.href = '../check-in/check_in_more.php';
}

function approval() {
    window.location.href = '../views/approval-head.php';
}

function report() {
    window.location.href = '../report/report-head.php';
}

function warning_checkinout() {
    Swal.fire({
        title: "<strong>ขออภัย...</strong>",
        icon: "warning",
        html: `โปรดใช้ระบบนี้ใน Line!`,
        showCloseButton: true,
        focusConfirm: false,
        confirmButtonText: `ตกลง`,
    });
}
</script>
<style>
table,
th,
td {
    border: 0.1px solid black;
}
</style>
</head>

<body>
    <div id="loader"></div>
    <div style="display:none;" id="Content" class="animate-bottom">

        <div class="desktop">
            <?php include('../components-desktop/head/include/sidebar.php'); ?>
            <?php include('../components-desktop/head/include/navbar.php'); ?>
            <div class="main-container">
                <div class="pd-ltr-20 xs-pd-20-10">
                    <div class="min-height-200px">
                        <div class="row">
                            <div class="col-lg-12 col-md-6 col-sm-12 mb-30" id="box1">
                                <div class="card-box pd-30 pt-10 height-100-p" id="card-box-1">
                                    <div>

                                        <div class="row">
                                            <div class="display-date">
                                                <img src="../IMG/sun-home.png" alt="">
                                                <span>
                                                    <?php echo $dateshow ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="display-status-checkin">
                                                <div class="Time-stamp">
                                                    <span>พนักงาน</span>
                                                    <input type="text" value="<?php echo $countPunctual; ?>" readonly
                                                        placeholder="1" />
                                                    <span>ลงเวลา</span>
                                                </div>
                                                <div class="em-leave">
                                                    <span>พนักงาน</span>
                                                    <input type="text" value="<?php echo $countLeave; ?>" readonly />
                                                    <span>ลา</span>
                                                </div>
                                                <div class="em-missing">
                                                    <span>พนักงาน</span>
                                                    <input type="text" value="<?php echo $countMissingWork; ?>"
                                                        readonly />
                                                    <span>ขาด</span>
                                                </div>
                                                <div class="em-late">
                                                    <span>พนักงาน</span>
                                                    <input type="text" value="<?php echo $countMiss; ?>" readonly />
                                                    <span>สาย</span>
                                                </div>
                                                <div class="em-total">
                                                    <span>พนักงาน</span>
                                                    <input type="text" value="<?php echo $countall; ?>" readonly />
                                                    <span>ทั้งหมด</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="check-in" style="user-select:none;">
                                                <span>เลือกรูปแบบการลงเวลา</span>
                                                <div class="btn-check-in">

                                                    <div class="btn-WIFI">
                                                        <button type="button" onclick="warning_checkinout()">
                                                            <div><img src="../IMG/wifi.png" alt=""></div>
                                                            <div>WIFI</div>
                                                        </button>
                                                    </div>

                                                    <div class="btn-QR">
                                                        <button type="button" onclick="warning_checkinout()">
                                                            <div><img src="../IMG/qr2.png" alt=""></div>
                                                            <div>QR</div>
                                                        </button>
                                                    </div>

                                                    <div class="btn-GPS">
                                                        <button type="button" onclick="warning_checkinout()">
                                                            <div><img src="../IMG/gps.png" alt=""></div>
                                                            <div>GPS</div>
                                                        </button>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="display-menu">
                                                <div>
                                                    <button type="button" onclick="submit_dayOff()">
                                                        <div><img src="../IMG/holidayH.png" alt=""></div>
                                                        <div><span>วันหยุด</span></div>
                                                    </button>
                                                </div>
                                                <div>
                                                    <button type="button" onclick="submit_Leave()">
                                                        <div><img src="../IMG/leave1.png" alt=""></div>
                                                        <div><span>วันลา</span></div>
                                                    </button>
                                                </div>
                                                <div>
                                                    <button type="button" onclick="submit_workka()">
                                                        <div><img src="../IMG/shift1.png" alt=""></div>
                                                        <div><span>กะทำงาน</span></div>
                                                    </button>
                                                </div>
                                                <div>
                                                    <button type="button" onclick="submit_ot()">
                                                        <div><img src="../IMG/ot.png" alt=""></div>
                                                        <div><span>OT</span></div>
                                                    </button>
                                                </div>
                                                <div>
                                                    <button type="button" onclick="approval()">
                                                        <div><img src="../IMG/approve.png" alt=""></div>
                                                        <div><span>การอนุมัติ</span></div>
                                                    </button>
                                                </div>
                                                <div>
                                                    <button type="button" onclick="report()">
                                                        <div><img src="../IMG/pb.png" alt=""></div>
                                                        <div><span>แจ้งปัญหา</span></div>
                                                    </button>
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
            <center>
                <div class="navbar">
                </div>
                <div class="top-home">
                    <div class="display-user">
                        <img src="<?php echo (!empty($data['employee_image']))
                            ? '../../admin/uploads_img/' . $data['employee_image']
                            : 'IMG/user22.png';
                        ?>" alt="">
                        <div class="name-id">
                            <span>Name :
                                <?php echo $fullname ?>
                                <br>
                            </span>
                            <span>ตำแหน่ง : หัวหน้า </span>
                        </div>
                    </div>
                    <div class="display-date">
                        <img src="../IMG/sun-home.png" alt="">
                        <span>
                            <?php echo $dateshow ?>
                        </span>
                    </div>
                    <button onclick="check_in_attendance_schedule()">ตารางเข้างาน</button>
                    <br><br>

                    <div class="display-status-checkin-head">
                        <div class="Time-stamp">
                            <span>พนักงาน</span>
                            <input type="text" value="<?php echo $countPunctual; ?>" disabled />
                            <span>ลงเวลา</span>
                        </div>
                        <div class="em-leave">
                            <span>พนักงาน</span>
                            <input type="text" value="<?php echo $countLeave; ?>" disabled />
                            <span>ลา</span>
                        </div>
                        <div class="em-missing">
                            <span>พนักงาน</span>
                            <input type="text" value="<?php echo $countMissingWork; ?>" placeholder="1" disabled />
                            <span>ขาด</span>
                        </div>
                        <div class="em-late">
                            <span>พนักงาน</span>
                            <input type="text" value="<?php echo $countMiss; ?>" disabled />
                            <span>สาย</span>
                        </div>
                        <div class="em-total">
                            <span>พนักงาน</span>
                            <input type="text" value="<?php echo $countall; ?>" disabled />
                            <span>ทั้งหมด</span>
                        </div>
                    </div>
                </div>

                <div class="workList">
                    <a href="../gps/approve-status-timeEm.php">รายการเข้างาน</a>
                </div>

                <div class="check-in">
                    <span>เลือกรูปแบบการลงเวลา</span>


                    <div class="btn-check-in">

                        <div class="btn-WIFI">

                            <button type="button" data-bs-toggle="modal" data-bs-target="#check-inout-Modal-wifi">
                                <div><img src="../IMG/wifi.png" alt=""></div>
                                <div>WIFI</div>
                            </button>

                            <!-- part wifi  -->
                            <div class="modal fade" id="check-inout-Modal-wifi" tabindex="-1"
                                aria-labelledby="exampleModalLabel">
                                <div class="modal-dialog">
                                    <div class="containerWIFI">
                                        <div class="modal-content">

                                            <div class="btnClose">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close">
                                                    <img src="../IMG/cross-warn.png" alt=""
                                                        class="custom-image"></button>
                                            </div>

                                            <div class="modal-header">
                                                <span class="modal-title">ลงชื่อด้วย WiFi</span>
                                            </div>

                                            <div class="displayImg">
                                                <img src="../IMG/wf2.png" alt="">
                                            </div>

                                            <div class="modal-body">
                                                <div class="displayTime">
                                                    <div>
                                                        <span class="Topic"
                                                            style="text-align:center;">ยืนยันการลงเวลา</span>
                                                    </div>
                                                    <div>
                                                        <span class="Time" style="text-align:center;">
                                                            <?php echo "เวลา " . $time_now . " น."; ?>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span style="text-align:center;" id="subnet"></span>
                                                    </div>
                                                </div>

                                                <div class="btnWIFI-check-in">
                                                    <div>
                                                        <button type="button" id="check-in-button-wifi"
                                                            style="background-color: #29ab29;">ลงชื่อเข้างาน</button>
                                                        <button type="button" id="check-out-button-wifi"
                                                            style="background-color: #e1574b;">ลงชื่อออกงาน</button>
                                                    </div>
                                                </div>


                                                <form hidden id="form-check-wifi" method="post"
                                                    enctype="multipart/form-data">
                                                    <input type="text" id="wifi-card-id" name="wifi-card-id"
                                                        value="<?php echo $card_id; ?>">
                                                    <input type="text" id="wifi-date" name="wifi-date"
                                                        value="<?php echo $date_now; ?>">
                                                    <input type="text" id="wifi-time" name="wifi-time"
                                                        value="<?php echo $time_now; ?>">


                                                    <input type="text" id="wifi-ip-subnet" name="wifi-ip-subnet"
                                                        value="<?php echo $clientIP ?>">

                                                    <input type="text" id="client-coords" name="client-coords" value="">
                                                    <input type="text" id="check_type" name="check_type"
                                                        value="wifi_type">
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="btn-QR">

                            <button type="button" data-bs-toggle="modal" data-bs-target="#check-chose-Modal-qr">
                                <div><img src="../IMG/qr2.png" alt=""></div>
                                <div>QR</div>
                            </button>

                            <div class="modal fade" id="check-chose-Modal-qr">
                                <div class="modal-dialog ">
                                    <div class="container-chose-QR">
                                        <div class="modal-content">

                                            <div class="btnClose">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close">
                                                    <img src="../IMG/cross-warn.png" alt=""
                                                        class="custom-image"></button>
                                            </div>

                                            <div class="modal-headerQR center">
                                                <span class="modal-title">ลงชื่อด้วย QR-Code</span>
                                            </div>

                                            <div class="displayImg">
                                                <img src="../IMG/qr3.png" alt="">
                                            </div>

                                            <div class="modal-body">
                                                <div>
                                                    <button class="sign-in" type="button" data-bs-toggle="modal"
                                                        data-bs-target="#check-in-Modal-qr">สแกน<br>ลงชื่อเข้างาน</button>
                                                    <button class="sign-out" type="button" data-bs-toggle="modal"
                                                        data-bs-target="#check-out-Modal-qr">สแกน<br>ลงชื่อออกงาน</button>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="modal fade" id="check-in-Modal-qr" tabindex="-1"
                                aria-labelledby="exampleModalLabel">
                                <div class="modal-dialog">
                                    <div class="containerQR">
                                        <div class="modal-content">

                                            <div class="btnClose">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close">
                                                    <img src="../IMG/cross-warn.png" alt=""
                                                        class="custom-image"></button>
                                            </div>

                                            <div class="modal-headerQR center">
                                                <span class="modal-title">ลงชื่อด้วย QR-Code</span>
                                            </div>

                                            <div class="modal-body">

                                                <div class="center">
                                                    <video class="center" id="video" width="300" height="300"
                                                        style="border: 1px solid red"></video>
                                                </div>

                                                <div class="displayTime-qr center">
                                                    <div>
                                                        <span class="Topic" style="text-align:center;">สแกน QR-Code
                                                            เพื่อลงชื่อ</span>
                                                    </div>
                                                    <div>
                                                        <span class="Time" style="text-align:center;">
                                                            <?php echo "เวลา " . $time_now . " น."; ?>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span style="text-align:center;" id="location"></span><br>
                                                    </div>
                                                </div>

                                                <div class="btnQR-check-in center">
                                                    <div>
                                                        <button type="button" class="scan-now"
                                                            id="startButton">สแกน</button>
                                                        <button type="button" class="scan-reset"
                                                            id="resetButton">สแกนใหม่</button>
                                                    </div>
                                                </div>

                                                <form hidden id="form-check-qr" method="post"
                                                    enctype="multipart/form-data">
                                                    <input type="text" id="qr-card-id" name="qr-card-id"
                                                        value="<?php echo $card_id; ?>">
                                                    <input type="text" id="qr-date" name="qr-date"
                                                        value="<?php echo $date_now; ?>">
                                                    <input type="text" id="qr-time" name="qr-time"
                                                        value="<?php echo $time_now; ?>">

                                                    <input type="text" id="qr-in-coords" name="qr-in-coords" value="">
                                                    <input type="text" id="qr-out-coords" name="qr-out-coords" value="">
                                                    <input type="text" id="check_type" name="check_type"
                                                        value="qr_type">
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="check-out-Modal-qr" tabindex="-1"
                                aria-labelledby="exampleModalLabel">
                                <div class="modal-dialog">
                                    <div class="containerQR">
                                        <div class="modal-content">

                                            <div class="btnClose">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close">
                                                    <img src="../IMG/cross-warn.png" alt=""
                                                        class="custom-image"></button>
                                            </div>

                                            <div class="modal-headerQR center">
                                                <span class="modal-title">ลงชื่อด้วย QR-Code</span>
                                            </div>

                                            <div class="modal-body">

                                                <div class="center">
                                                    <video class="center" id="video" width="300" height="300"
                                                        style="border: 1px solid red"></video>
                                                </div>

                                                <div class="displayTime-qr center">
                                                    <div>
                                                        <span class="Topic" style="text-align:center;">สแกน QR-Code
                                                            เพื่อลงชื่อ</span>
                                                    </div>
                                                    <div>
                                                        <span class="Time" style="text-align:center;">
                                                            <?php echo "เวลา " . $time_now . " น."; ?>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span style="text-align:center;" id="location"></span><br>
                                                    </div>
                                                </div>

                                                <div class="btnQR-check-in center">
                                                    <div>
                                                        <button class="scan-now" type="button"
                                                            id="startButton">สแกน</button>
                                                        <button class="scan-reset" type="button"
                                                            id="resetButton">สแกนใหม่</button>
                                                    </div>
                                                </div>

                                                <form hidden id="form-check-qr" method="post"
                                                    enctype="multipart/form-data">
                                                    <input type="text" id="qr-card-id" name="qr-card-id"
                                                        value="<?php echo $card_id; ?>">
                                                    <input type="text" id="qr-date" name="qr-date"
                                                        value="<?php echo $date_now; ?>">
                                                    <input type="text" id="qr-time" name="qr-time"
                                                        value="<?php echo $time_now; ?>">

                                                    <input type="text" id="qr-in-coords" name="qr-in-coords" value="">
                                                    <input type="text" id="qr-out-coords" name="qr-out-coords" value="">
                                                    <input type="text" id="check_type" name="check_type"
                                                        value="qr_type">
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="btn-GPS">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#check-inout-Modal-gps">
                                <div><img src="../IMG/gps.png" alt=""></div>
                                <div>GPS</div>
                            </button>

                            <div class="modal fade" id="check-inout-Modal-gps" tabindex="-1"
                                aria-labelledby="exampleModalLabel">
                                <div class="modal-dialog">
                                    <div class="containerGPS">
                                        <div class="modal-content">

                                            <div class="btnCloseGPS">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close">
                                                    <img src="../IMG/cross-warn.png" alt=""
                                                        class="custom-image"></button>
                                            </div>

                                            <div class="modal-headerGPS">
                                                <span class="modal-title">ลงชื่อด้วย GPS</span>
                                            </div>

                                            <div class="modal-body">

                                                <div class="center" id="map"></div>

                                                <div class="displayTime-gps">
                                                    <div>
                                                        <span class="Topic"
                                                            style="text-align:center;">ยืนยันการลงเวลา</span>
                                                    </div>
                                                    <div>
                                                        <span class="Time" style="text-align:center;">
                                                            <?php echo "เวลา " . $time_now . " น."; ?>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span style="text-align:center;" id="location"></span><br>
                                                    </div>
                                                </div>

                                                <div class="btnGPS-check-in">
                                                    <div>
                                                        <button type="button" id="check-in-button-gps"
                                                            style="background-color: #F2CB00;">ลงชื่อเข้างาน</button>
                                                        <button type="button" id="check-out-button-gps"
                                                            style="background-color: #FF5643;">ลงชื่อออกงาน</button>
                                                    </div>
                                                </div>

                                                <form hidden id="form-check-gps" method="post"
                                                    enctype="multipart/form-data">
                                                    <input type="text" id="gps-card-id" name="gps-card-id"
                                                        value="<?php echo $card_id; ?>">
                                                    <input type="text" id="gps-date" name="gps-date"
                                                        value="<?php echo $date_now; ?>">
                                                    <input type="text" id="gps-time" name="gps-time"
                                                        value="<?php echo $time_now; ?>">

                                                    <input type="text" id="gps-in-coords" name="gps-in-coords" value="">
                                                    <input type="text" id="gps-out-coords" name="gps-out-coords"
                                                        value="">

                                                    <input type="text" id="check_type" name="check_type"
                                                        value="gps_type">
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <button onclick="submit_CheckNow()"> Check Now </button> -->

                <div class="display-menu">
                    <div class="display-menu-bar1">
                        <div>
                            <a href="#" onclick="submit_dayOff()">
                                <div><img src="../IMG/holidayH.png" alt=""></div>
                                <div><span>วันหยุด</span></div>
                            </a>
                        </div>
                        <div>
                            <a href="#" onclick="submit_Leave()">
                                <div><img src="../IMG/leave1.png" alt=""></div>
                                <div><span>วันลา</span></div>
                            </a>
                        </div>

                        <div>
                            <a href="#" onclick="approval()">
                                <div><img src="../IMG/approve.png" alt=""></div>
                                <div><span>การอนุมัติ</span></div>
                            </a>
                        </div>
                    </div>
                    <div class="display-menu-bar2">
                        <div>
                            <a href="#" onclick="submit_workka()">
                                <div><img src="../IMG/shift1.png" alt=""></div>
                                <div><span>กะทำงาน</span></div>
                            </a>
                        </div>
                        <div>
                            <a href="#" onclick="submit_ot()">
                                <div><img src="../IMG/ot.png" alt=""></div>
                                <div><span>OT</span></div>
                            </a>
                        </div>
                        <div>
                            <a href="#" onclick="report()">
                                <div><img src="../IMG/pb.png" alt=""></div>
                                <div><span>แจ้งปัญหา</span></div>
                            </a>
                        </div>

                    </div>
                </div>
            </center>
        </div>
    </div>
</body>

<script>
const default_coords = {
    lat: <?php echo $lat; ?>,
    lng: <?php echo $lng; ?>
};

// var gps_range = <?php echo $coords_range ?>;
var gps_range = "2000";

$(document).ready(function() {
    getCurrentGPS();

    var targetElement = $('#TodayDiv');
    targetElement.css('background-color', 'yellow');

});
</script>


<!-- <script src="../assets/script/check_inout_copy.js"></script> -->
<script src="../assets/script/check_inout.js"></script>

<?php include('../includes/footer.php'); ?>