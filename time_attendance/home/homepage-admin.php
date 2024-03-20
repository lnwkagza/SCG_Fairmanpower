<?php
session_start();
session_regenerate_id(true);
require("../database/connectdb.php");
// include("dbconnect.php");
//--------------------------------------------------------------------------------------------------------
include('../components-desktop/admin/include/header.php');
// require_once("../includes/header.php");

require('../check-in/check-inout-query.php');
include("../thisWeek.php");
// require_once("..test.php");
//--------------------------------------------------------------------------------------------------------
// $status_date = $_GET["status_date"];
// $status_date = (int)$status_date;
?>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/admin/homepage-admin.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/homepage.css">
<link rel="stylesheet" href="../assets/css/custom.css">
<link rel="stylesheet" href="node_modules\leaflet\dist\leaflet.css" />


<link rel="stylesheet" href="../assets/css/loader.css">
<script src="script/loader-normal.js"></script>

<?php
$card_id = $_SESSION["card_id"];
// $card_id = 1949999999901;

date_default_timezone_set('Asia/Bangkok');
$time_stamp = date("Y-m-d H:i:s");
$time = new week();
$time->set_day($time_stamp);
$start_mon = $time->get_start();
$end_mon = $time->get_end();
$start_mon_timestamp = strtotime($start_mon);
$start_mon_formatted = date("d/m", $start_mon_timestamp);
// $dateshow = DateThai($time_stamp);

$time_now = date("H:i");
$date_now = date("Y-m-d");

$clientIP = $_SERVER['REMOTE_ADDR'];
// echo $clientIP;

// ป้องกัน SQL injection และใช้ prepared statements
$sql_emp = sqlsrv_query($conn, "SELECT * FROM employee WHERE card_id = ?", array($card_id));

// ดำเนินการต่อเพื่อดึงข้อมูล
$rs_emp = sqlsrv_fetch_array($sql_emp, SQLSRV_FETCH_ASSOC);

// ตรวจสอบว่ามีข้อมูลหรือไม่ก่อนนำไปใช้
if ($rs_emp) {

    $id = $rs_emp['card_id'];
    $name = $rs_emp['firstname_thai'];
    $lastname = $rs_emp['lastname_thai'];
    $fullname = $name . ' ' . $lastname;


    // ป้องกัน SQL injection ใน query ที่สอง
    $sql_checkin = sqlsrv_query($conn, "SELECT * FROM check_inout WHERE card_id = ? AND date BETWEEN ? AND ?", array($id, $start_mon, $end_mon));
    // print_r($sql_checkin);
    // ดำเนินการตรวจสอบและเก็บข้อมูล
    $checkin_data = array();

    while ($rs_checkin = sqlsrv_fetch_array($sql_checkin, SQLSRV_FETCH_ASSOC)) {
        $date = $rs_checkin['date']->format("Y-m-d");
        $checkin_data[$date]['time_in'] = $rs_checkin['time_in'];
        $checkin_data[$date]['time_out'] = $rs_checkin['time_out'];
    }
}


?>

<script>
// btnวันหยุด
function submit_dayOff() {
    window.location.href = '../dayoff/day-off-admin.php';
}

function submit_Leave() {
    window.location.href = '../leave/leave-rights-admin.php';
}

function submit_workka() {
    window.location.href = '../shift/shift-admin.php';
}

function submit_ot() {
    window.location.href = '../ot/ot-admin.php';
}

function check_in_attendance_schedule() {
    window.location.href = '../check-in/check-in-warning-admin.php';
}

function check_in_detail_more() {
    window.location.href = '../check-in/check_in_more.php';
}

function approval() {
    window.location.href = '../views/approval-admin.php';
}

function report() {
    window.location.href = '../report/report.php';
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
</head>

<body>
    <div id="loader"></div>
    <div style="display:none;" id="Content" class="animate-bottom">

        <div class="desktop">
            <?php include('../components-desktop/admin/include/sidebar.php'); ?>
            <?php include('../components-desktop/admin/include/navbar.php'); ?>
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
                                            <button onclick="check_in_attendance_schedule()">ตารางการเข้างาน</button>
                                        </div>

                                        <div class="row">
                                            <div class="display-status-checkin" style="user-select: none;">
                                                <?php
                                                // Assuming $start_mon is defined and contains the start date for the week
                                                $currentDate = strtotime($start_mon);
                                                $todayDateFormatted = date("Y-m-d");
                                                // $is_weekend = in_array($date->format('N'), [$day_off1, $day_off2]);
                                                
                                                for ($day = 0; $day < 31; $day++) {
                                                    $currentDateFormatted = date("Y-m-d", $currentDate);
                                                    $isToday = ($currentDateFormatted === $todayDateFormatted);
                                                    ?>

                                                <div <?php echo ($isToday) ? 'id="TodayDiv"' : ''; ?>>
                                                    <table border="1"
                                                        <?php echo ($isToday) ? 'id="TodayTable"' : ''; ?>>
                                                        <tr>
                                                            <th colspan="2" style="text-align: center; ">
                                                                <?php echo ($isToday) ? '<span style="color:#0B6BC1; font-weight: bold; font-size: 18px;">Today</span>' : date("d/m", $currentDate); ?>

                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <td> เข้า :
                                                                <?php
                                                                    desktop_attendance($currentDateFormatted, 'time_in');
                                                                    ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td> ออก :
                                                                <?php
                                                                    desktop_attendance($currentDateFormatted, 'time_out');
                                                                    ?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>

                                                <?php
                                                    $currentDate = strtotime("+1 day", $currentDate);
                                                }
                                                ?>

                                                <?php
                                                // Function to display attendance information
                                                function desktop_attendance($currentDateFormatted, $timeType)
                                                {
                                                    global $checkin_data;

                                                    // Check if time is not null before formatting
                                                    if (isset($checkin_data[$currentDateFormatted][$timeType])) {
                                                        $time_show = $checkin_data[$currentDateFormatted][$timeType];

                                                        if ($time_show instanceof DateTime) {
                                                            $time_show = $time_show->format("H:i");

                                                            if ($timeType == 'time_in' && $time_show > "07:30") {
                                                                echo '<span style="color:red;">' . $time_show . '</span>';
                                                            } elseif ($timeType == 'time_out' && $time_show < "16:30") {
                                                                echo '<span style="color:red;">' . $time_show . '</span>';
                                                            } else {
                                                                echo $time_show;
                                                            }
                                                        } else {
                                                            echo '<span style="color:red;">ขาดงาน</span>';
                                                        }
                                                    } else {
                                                        if (strtotime($currentDateFormatted) >= strtotime(date('Y-m-d'))) {
                                                            echo ' - '; // Date is in the future
                                                        } else {
                                                            echo '<span style="color:red;">ขาดงาน</span>';
                                                        }
                                                    }
                                                }
                                                ?>

                                            </div>

                                            <script>
                                            let mouseDown = false;
                                            let startX, scrollLeft;
                                            const slider = document.querySelector('.display-status-checkin');

                                            const startDragging = (e) => {
                                                mouseDown = true;
                                                startX = e.pageX - slider.offsetLeft;
                                                scrollLeft = slider.scrollLeft;
                                            }

                                            const stopDragging = (e) => {
                                                mouseDown = false;
                                            }

                                            const move = (e) => {
                                                e.preventDefault();
                                                if (!mouseDown) {
                                                    return;
                                                }
                                                const x = e.pageX - slider.offsetLeft;
                                                const scroll = x - startX;
                                                slider.scrollLeft = scrollLeft - scroll;
                                            }

                                            // Add the event listeners
                                            slider.addEventListener('mousemove', move, false);
                                            slider.addEventListener('mousedown', startDragging, false);
                                            slider.addEventListener('mouseup', stopDragging, false);
                                            slider.addEventListener('mouseleave', stopDragging, false);
                                            </script>
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
                            <span>ตำแหน่ง : แอดมิน </span>
                        </div>
                    </div>
                    <div class="display-date">
                        <img src="../IMG/sun-home.png" alt="">
                        <span>
                            <?php echo $dateshow ?>
                        </span>
                    </div>

                    <button onclick="check_in_attendance_schedule()">ตารางการเข้างาน</button>
                    <br>

                    <div class="display-status-checkin">
                        <?php
                        // Assuming $start_mon is defined and contains the start date for the week
                        $currentDate = strtotime($start_mon);
                        $todayDateFormatted = date("Y-m-d");
                        // $is_weekend = in_array($date->format('N'), [$day_off1, $day_off2]);
                        
                        for ($day = 0; $day < 7; $day++) {
                            $currentDateFormatted = date("Y-m-d", $currentDate);
                            $isToday = ($currentDateFormatted === $todayDateFormatted);
                            ?>

                        <div <?php echo ($isToday) ? 'id="TodayDiv"' : ''; ?>>
                            <table border="1" <?php echo ($isToday) ? 'id="TodayTable"' : ''; ?>>
                                <tr>
                                    <th colspan="2" style="text-align: center; ">
                                        <?php echo ($isToday) ? '<span style="color:#0B6BC1; font-weight: bold; font-size: 18px;">Today</span>' : date("d/m", $currentDate); ?>

                                    </th>
                                </tr>
                                <tr>
                                    <td> เข้า :
                                        <?php
                                            displayAttendance($currentDateFormatted, 'time_in');
                                            ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td> ออก :
                                        <?php
                                            displayAttendance($currentDateFormatted, 'time_out');
                                            ?>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <?php
                            $currentDate = strtotime("+1 day", $currentDate);
                        }
                        ?>

                        <?php
                        // Function to display attendance information
                        function displayAttendance($currentDateFormatted, $timeType)
                        {
                            global $checkin_data;

                            // Check if time is not null before formatting
                            if (isset($checkin_data[$currentDateFormatted][$timeType])) {
                                $time_show = $checkin_data[$currentDateFormatted][$timeType];

                                if ($time_show instanceof DateTime) {
                                    $time_show = $time_show->format("H:i");

                                    if ($timeType == 'time_in' && $time_show > "07:30") {
                                        echo '<span style="color:red;">' . $time_show . '</span>';
                                    } elseif ($timeType == 'time_out' && $time_show < "16:30") {
                                        echo '<span style="color:red;">' . $time_show . '</span>';
                                    } else {
                                        echo $time_show;
                                    }
                                } else {
                                    echo '<span style="color:red;">ขาดงาน</span>';
                                }
                            } else {
                                if (strtotime($currentDateFormatted) >= strtotime(date('Y-m-d'))) {
                                    echo ' - '; // Date is in the future
                                } else {
                                    echo '<span style="color:red;">ขาดงาน</span>';
                                }
                            }
                        }
                        ?>


                    </div>



                </div>

                <div class="check-in">
                    <span>เลือกรูปแบบการลงเวลา</span>
                    <br>


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
                                                <button class="sign-in" type="button" data-bs-toggle="modal"
                                                    data-bs-target="#check-in-Modal-qr">สแกน<br>ลงชื่อเข้างาน</button>
                                                <button class="sign-out" type="button" data-bs-toggle="modal"
                                                    data-bs-target="#check-out-Modal-qr">สแกน<br>ลงชื่อออกงาน</button>
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

                                                <div class="center-video">
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
                                                        <span style="text-align:center;" id="location"></span>
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
                                                        <span style="text-align:center;" id="location"></span>
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
                                                        <span style="text-align:center;" id="location"></span>
                                                    </div>
                                                </div>

                                                <div class="btnGPS-check-in">
                                                    <div>
                                                        <button type="button" id="check-in-button-gps"
                                                            style="background-color: #29ab29;">ลงชื่อเข้างาน</button>
                                                        <button type="button" id="check-out-button-gps"
                                                            style="background-color: #e1574b;">ลงชื่อออกงาน</button>
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
                                <div><span>รออนุมัติ</span></div>
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