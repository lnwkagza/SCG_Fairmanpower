<?php
session_start();
session_regenerate_id(true);
header("Cache-Control: no-cache, must-revalidate");
include("../database/connectdb.php");
// include("dbconnect.php");
include('../components-desktop/employee/include/header.php');
echo '<div id="loader"></div>';
?>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI/t1f1qODW6fzZll4H5veNvJfyl26D8qx2vNc5A=" crossorigin="anonymous"></script>

<!-- Include Calendar -->
<link href="../calendar/zabuto_calendar.css" rel="stylesheet">
<script src="../calendar/zabuto_calendar.js"></script>
<link href="../calendar/demo.css" rel="stylesheet">

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/employee/check-in-attendance-schedule.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/check-in-attendance-schedule.css">
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">


<link rel="stylesheet" href="../assets/css/loader.css">
<script src="../assets/script/loader-normal.js"></script>

<?php
date_default_timezone_set('Asia/Bangkok');

//--------------------------------------------------------------------------------------------------

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

//----------------------------------------------------------------------------------------------------

$card_id = $_SESSION["card_id"];

$start_date = new DateTime('first day of this month');
$end_date = new DateTime('first day of next month');
$interval = new DateInterval('P1D');
$date_range = new DatePeriod($start_date, $interval, $end_date);

$select_check_inout_Query = "SELECT * FROM check_inout WHERE card_id = ? AND date = ? ORDER BY date ASC";

$select_absence_record = "SELECT * FROM absence_record WHERE card_id = ? AND date_start <= ? AND date_end >= ?";

$select_holiday = "SELECT * FROM holiday WHERE date = ?";

$select_transaction_work_Query = "SELECT * FROM transaction_work WHERE card_id = ? AND date = ? ORDER BY date ASC";
// Your SQL query with a date range condition
$select_holiday_Query = "SELECT * FROM holiday WHERE date = ? ORDER BY date ASC";


// Initialize counters
$countholiday = 0;
$countLeave = 0;
$countMiss = 0;
$countMissingWork = 0;
$countPunctual = 0;
$countBack = 0;

foreach ($date_range as $date) {
    $day_date = $date->format('d');
    $formatted_date = $date->format('Y-m-d');
    $Actionsmode = 0;

    $check_inout_stmt = sqlsrv_prepare($conn, $select_check_inout_Query, array(&$card_id, &$formatted_date));
    $holiday_stmt = sqlsrv_prepare($conn, $select_holiday, array(&$formatted_date));
    $absence_record_stmt = sqlsrv_prepare($conn, $select_absence_record, array(&$card_id, &$formatted_date, &$formatted_date));
    $transaction_work_stmt = sqlsrv_prepare($conn, $select_transaction_work_Query, array(&$card_id, &$formatted_date));

    if (
        sqlsrv_execute($check_inout_stmt) &&
        sqlsrv_execute($absence_record_stmt) &&
        sqlsrv_execute($holiday_stmt) &&
        sqlsrv_execute($transaction_work_stmt)
    ) {

        $holiday_result = sqlsrv_fetch_array($holiday_stmt, SQLSRV_FETCH_ASSOC);
        $absence_record_result = sqlsrv_fetch_array($absence_record_stmt, SQLSRV_FETCH_ASSOC);
        $check_inout_result = sqlsrv_fetch_array($check_inout_stmt, SQLSRV_FETCH_ASSOC);
        $transaction_work_result = sqlsrv_fetch_array($transaction_work_stmt, SQLSRV_FETCH_ASSOC);

        $shift = array(
            'DD01' => 'ปกติ 1',
            'DD02' => 'ปกติ 2',
            'HOLIDAY' => 'วันหยุดนักขัต',
            'LEAVE' => 'ลา',
            'OFF' => 'วันหยุด',
            'SA01' => 'กะ 1',
            'SB01' => 'กะ 2',
            'SC01' => 'กะ 3',
            'TRAIN' => 'อบรม'
        );
        $shiftStatus = isset($shift[$transaction_work_result["shift_main"]]) ? $shift[$transaction_work_result["shift_main"]] : "";

        if ($transaction_work_result["shift_main"] == "DD01") {
            $time_in_set = "07:30:00";
            $time_out_set = "16:30:00";
        } elseif ($transaction_work_result["shift_main"] == "DD02") {
            $time_in_set = "08:00:00";
            $time_out_set = "17:00:00";
        } elseif ($transaction_work_result["shift_main"] == "SA01") {
            $time_in_set = "08:00:00";
            $time_out_set = "16:00:00";
        } elseif ($transaction_work_result["shift_main"] == "SB01") {
            $time_in_set = "16:00:00";
            $time_out_set = "00:00:00"; // Midnight, next day
        } elseif ($transaction_work_result["shift_main"] == "SC01") {
            $time_in_set = "00:00:00";
            $time_out_set = "08:00:00";
        } else {
            // Handle default case
            $time_in_set = "00:00:00";
            $time_out_set = "00:00:00";
        }


        if ($holiday_result) {
            $event["classname"] = "event-holiday";
            $countholiday++;
        } elseif ($transaction_work_result["shift_main"] == "OFF") {
        } elseif ($absence_record_result) {
            $event["classname"] = "event-leave";
            $countLeave++;
        } elseif ($date > (new DateTime())->modify('+0 day')) {
        } elseif ($check_inout_result) {
            $time_in = $check_inout_result['time_in'] ? $check_inout_result['time_in']->format('H:i') : "";
            $time_out = $check_inout_result['time_out'] ? $check_inout_result['time_out']->format('H:i') : "";
            if ($time_in >= $time_in_set) {
                $event["classname"] = "event-Miss";
                $countMiss++;
            } elseif ($time_in == "" && $time_out == "") {
                $event["classname"] = "event-MissingWork";
                $countMissingWork++;
            } elseif ($time_in <= $time_in_set && $time_out >= $time_out_set) {
                $event["classname"] = "event-Punctual";
                $countPunctual++;
            } elseif ($time_in <= $time_in_set && $time_out == "") {
                $event["classname"] = "event-Punctual";
                $countPunctual++;
            } elseif ($time_out <= $time_out_set) {
                $event["classname"] = "event-EarlyLeave";
                $countBack++;
            }
        } else {
            $countMissingWork++;
            $event["classname"] = "event-MissingWork";
        }

        $events[] = $event;
    }
}

// $countMissingWork = $countMissingWork - $countMiss - $countPunctual - $countBack;

$_SESSION['countholiday'] = $countholiday;
$_SESSION['countLeave'] = $countLeave;
$_SESSION['countMiss'] = $countMiss;
$_SESSION['countMissingWork'] = $countMissingWork;
$_SESSION['countPunctual'] = $countPunctual;
$_SESSION['countBack'] = $countBack;

?>
</head>

<body>
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
                                        <h2>ตารางการเข้า-ออกงานประจำเดือน<?= $thaiMonthYear; ?>
                                        </h2>
                                    </div>
                                    <nav aria-label="breadcrumb" role="navigation">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item">
                                                <a style="pointer-events:none;cursor:default;">ลงชื่อเข้า-ออก</a>
                                            </li>
                                            <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                                ตารางการเข้า-ออกงาน
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                                <div class="card-box pd-30 pt-10 height-100-p">
                                    <div class="bar">
                                        <div class="btn-bar absence" onclick="window.location='check-in-total-absences.php'">
                                            <button> ขาด
                                                <?php echo $countMissingWork; ?> วัน
                                            </button>
                                        </div>
                                        <div class="btn-bar late" onclick="window.location='check-in-total-late.php'">
                                            <button> สาย
                                                <?php echo $countMiss; ?> วัน
                                            </button>
                                        </div>
                                        <div class="btn-bar leave" onclick="window.location='check-in-total-leave.php'">
                                            <button> ลา
                                                <?php echo $countLeave ?> วัน
                                            </button>
                                        </div>
                                        <div class="btn-bar on-time" onclick="window.location='check-in-total-ontime.php'">
                                            <button>
                                                ตรงเวลา
                                                <?php echo $countPunctual ?> วัน
                                            </button>
                                        </div>
                                        <div class="btn-bar back" onclick="window.location='check-in-total-back.php'">
                                            <button>
                                                กลับก่อน
                                                <?php echo $countBack ?> วัน
                                            </button>
                                        </div>
                                    </div>
                                    <div class="calendar">
                                        <button class="show-popup-btn" onclick="togglePopupDesktop()"><img src="../IMG/caleder.png" alt=""></button>
                                        <div class="popup-container" id="calendar-popup" style="background-color: #E9E6E6;">
                                            <div class="demo-calendar" id="calendar-data"></div>
                                            <div class="detail-color">
                                                <label>*หมายเหตุ*</label><br>
                                                <label><img src="../IMG/red.png" alt="" style="width: 10px;">&nbsp;ขาดงาน</label>
                                                <label><img src="../IMG/orange.png" alt="" style="width: 10px;">&nbsp;สาย</label>
                                                <label><img src="../IMG/blue.png" alt="" style="width: 10px;">&nbsp;ลา</label>
                                                <label><img src="../IMG/green.png" alt="" style="width: 10px;">&nbsp;ตรงเวลา</label><br>
                                                <label><img src="../IMG/pulple.png" alt="" style="width: 10px;">&nbsp;กลับก่อน</label>
                                                <label><img src="../IMG/dark blue.png" alt="" style="width: 10px;">&nbsp;วันหยุดตามประเพณี</label>
                                            </div>
                                        </div>
                                        <div class="overlay" id="overlay-desktop" onclick="togglePopupDesktop()">
                                        </div>
                                        <script>
                                            function togglePopupDesktop() {
                                                var popup = document.getElementById('calendar-popup');
                                                var overlay = document.getElementById('overlay-desktop');
                                                // Toggle the display of the pop - up and overlay
                                                if (popup.style.display === 'none') {
                                                    popup.style.display = 'block';
                                                    overlay.style.display = 'block';
                                                } else {
                                                    popup.style.display = 'none';
                                                    overlay.style.display = 'none';
                                                }
                                            }
                                        </script>
                                    </div>
                                    <div class="desktop-table">
                                        <table class="table stripe hover nowrap" id="table1">
                                            <thead>
                                                <tr>
                                                    <th>วันที่</th>
                                                    <th>ประเภทการทำงาน</th>
                                                    <th>IN</th>
                                                    <th>OUT</th>
                                                    <th>รายการ</th>
                                                    <th>สถานะ</th>
                                                    <th>จัดการ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($date_range as $date) {
                                                    $day_date = $date->format('d');
                                                    $formatted_date = $date->format('Y-m-d');
                                                    $Actionsmode = 0;

                                                    $check_inout_stmt = sqlsrv_prepare($conn, $select_check_inout_Query, array(&$card_id, &$formatted_date));
                                                    $holiday_stmt = sqlsrv_prepare($conn, $select_holiday, array(&$formatted_date));
                                                    $absence_record_stmt = sqlsrv_prepare($conn, $select_absence_record, array(&$card_id, &$formatted_date, &$formatted_date));
                                                    $transaction_work_stmt = sqlsrv_prepare($conn, $select_transaction_work_Query, array(&$card_id, &$formatted_date));

                                                    if (
                                                        sqlsrv_execute($check_inout_stmt) &&
                                                        sqlsrv_execute($absence_record_stmt) &&
                                                        sqlsrv_execute($holiday_stmt) &&
                                                        sqlsrv_execute($transaction_work_stmt)
                                                    ) {

                                                        $holiday_result = sqlsrv_fetch_array($holiday_stmt, SQLSRV_FETCH_ASSOC);
                                                        $absence_record_result = sqlsrv_fetch_array($absence_record_stmt, SQLSRV_FETCH_ASSOC);
                                                        $check_inout_result = sqlsrv_fetch_array($check_inout_stmt, SQLSRV_FETCH_ASSOC);
                                                        $transaction_work_result = sqlsrv_fetch_array($transaction_work_stmt, SQLSRV_FETCH_ASSOC);

                                                        $shift = array(
                                                            'DD01' => 'กะปกติ 1',
                                                            'DD02' => 'กะปกติ 2',
                                                            'HOLIDAY' => 'วันหยุดนักขัตฯ',
                                                            'LEAVE' => 'ลา',
                                                            'OFF' => 'วันหยุด',
                                                            'SA01' => 'กะ 1',
                                                            'SB01' => 'กะ 2',
                                                            'SC01' => 'กะ 3',
                                                            'TRAIN' => 'อบรม'
                                                        );
                                                        $shiftStatus = isset($shift[$transaction_work_result["shift_main"]]) ? $shift[$transaction_work_result["shift_main"]] : "";

                                                        if ($transaction_work_result["shift_main"] == "DD01") {
                                                            $time_in_set = "07:30:00";
                                                            $time_out_set = "16:30:00";
                                                        } elseif ($transaction_work_result["shift_main"] == "DD02") {
                                                            $time_in_set = "08:00:00";
                                                            $time_out_set = "17:00:00";
                                                        } elseif ($transaction_work_result["shift_main"] == "SA01") {
                                                            $time_in_set = "08:00:00";
                                                            $time_out_set = "16:00:00";
                                                        } elseif ($transaction_work_result["shift_main"] == "SB01") {
                                                            $time_in_set = "16:00:00";
                                                            $time_out_set = "00:00:00"; // Midnight, next day
                                                        } elseif ($transaction_work_result["shift_main"] == "SC01") {
                                                            $time_in_set = "00:00:00";
                                                            $time_out_set = "08:00:00";
                                                        } else {
                                                            // Handle default case
                                                            $time_in_set = "00:00:00";
                                                            $time_out_set = "00:00:00";
                                                        }

                                                        if ($holiday_result) {
                                                            $status = "<label style='color: #1b3d5e;'>" . $holiday_result["name"] . "</label>";
                                                            $symbol = $shiftStatus;
                                                            $time_in = '-';
                                                            $time_out = '-';
                                                            $Actionsmode = 0;
                                                            $approve_status = "-";
                                                            $OTmode = 0;
                                                        } elseif ($transaction_work_result["shift_main"] == "OFF") {
                                                            $symbol = $shiftStatus;
                                                            $status = " <label style='color: #007acc;'>วันหยุดประจำสัปดาห์</label> ";
                                                            $time_in = "-";
                                                            $time_out = "-";
                                                            $approve_status = "-";
                                                            $OTmode = 0;
                                                            $approve_status = "-";
                                                        } elseif ($absence_record_result) {
                                                            $status = 'LEAVE';
                                                            $symbol = $shiftStatus;
                                                            $time_in = '-';
                                                            $time_out = '-';
                                                            $Actionsmode = 0;
                                                            $approve_status = "-";
                                                        } elseif ($date > (new DateTime())->modify('+0 day')) {
                                                            $status = "-";
                                                            $symbol = $shiftStatus;
                                                            $status = "-"; // วันที่ยังไม่ถึง
                                                            $time_in = '-';
                                                            $time_out = '-';
                                                            $Actionsmode = 0;
                                                            $approve_status = "-";
                                                        } elseif ($check_inout_result) {
                                                            $Actionsmode = 1;
                                                            $symbol = $shiftStatus;
                                                            $time_in = $check_inout_result['time_in'] ? $check_inout_result['time_in']->format('H:i') : "";
                                                            $time_out = $check_inout_result['time_out'] ? $check_inout_result['time_out']->format('H:i') : "";
                                                            $approve_status = $check_inout_result['approve_status'];
                                                            //เวลา
                                                            if ($time_in >= $time_in_set) {
                                                                $status = '<label style="color: #FF9922;">มาสาย</label>';
                                                            } elseif ($time_in == "" && $time_out == "") {
                                                                $status = '<label style="color: red;">ขาดงาน</label>';
                                                                $time_in = "<label style='color: red;'>ขาดงาน</label>";
                                                                $time_out = "<label style='color: red;'>ขาดงาน</label>";
                                                            } elseif ($time_in <= $time_in_set && $time_out >= $time_out_set) {
                                                                $status = '<label style="color: #5BAF33;">ตรงเวลา</label>';
                                                                $OTmode = 1;
                                                            } elseif ($time_in <= $time_in_set && $time_out == "") {
                                                                $status = '<label style="color: #5BAF33;">ตรงเวลา</label>';
                                                                $OTmode = 0;
                                                            } elseif ($time_out <= $time_out_set) {
                                                                $status = '<label style="color: #9747FF; ">กลับก่อน</label>';
                                                            } else {
                                                                $status = " <label style='color: red;'>ขาดงาน</label>";
                                                                $time_in = "-";
                                                                $time_out = "-";
                                                            }

                                                            $Link = "check_inout_id=" . $check_inout_result['check_inout_id'];
                                                        } else {
                                                            $symbol = $shiftStatus;
                                                            $status = " <label style='color: red;'>ขาดงาน</label>";
                                                            $Actionsmode = 1;
                                                            $time_in = "-";
                                                            $time_out = "-";
                                                            $OTmode = 0;
                                                            $Link = "check_inout_date=" . $formatted_date;
                                                        }
                                                    }
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $day_date . ' ' . $thaiMonthYear; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $symbol; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $time_in; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $time_out; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $status; ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            switch ($approve_status) {
                                                                case 'waiting':
                                                                    echo '<div class="status-edit"><span class="waiting-status" style="color:orange">รออนุมัติ</span></div>';
                                                                    break;
                                                                case 'approve':
                                                                    echo '<div class="status-edit"><span class="approve-status" style="color:green">อนุมัติ</span></div>';
                                                                    break;
                                                                case 'reject':
                                                                    echo '<div class="status-edit"><span class="reject-status" style="color:red">ไม่อนุมัติ</span></div>';
                                                                    break;
                                                                default:
                                                                    break;
                                                            }
                                                            ?>
                                                        </td>
                                                        <?php if ($Actionsmode == 0) : ?>
                                                            <td></td>
                                                        <?php else : ?>
                                                            <td>
                                                                <div>
                                                                    <div class="shift" onclick="window.location.href='../shift/shift-progress-step4-employee.php.?date=<?php echo $formatted_date; ?>'" title="สลับกะ">
                                                                        <i class="fa-solid fa-shuffle"></i>
                                                                    </div>
                                                                    <?php if ($OTmode == 1) { ?>
                                                                        <div class="ot" onclick="window.location.href=''">
                                                                            <span>OT</span>
                                                                        </div>
                                                                    <?php } ?>
                                                                    <div class="edit-time" onclick="window.location.href='check-in-edit.php?<?php echo $Link; ?>'" title="แก้ไขเวลา">
                                                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                                                    </div>
                                                                    <div class="edit-info" onclick="window.location.href='check-in-detail-edit.php?<?php echo $Link; ?>'" title="รายละเอียดแก้ไข">
                                                                        <i class="fa-solid fa-circle-info"></i>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        <?php endif; ?>
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
                    <span>การเข้างาน</span>
                </div>
            </div>


            <div class="detail-topic">
                <span>ตารางการเข้างานประจำเดือนนี้</span>
            </div>

            <div class="month-now">
                <span class='month-now1'>
                    <?php echo $thaiMonthYear; ?>
                </span>
                <button class="show-popup-btn" onclick="togglePopupMobile()"><img src="../IMG/caleder.png" alt=""></button>
            </div>

            <!-- <div class="demo-calendar" id="demo-calendar-data"></div> -->

            <div class="btn-display-status">
                <div class="btn-row1">
                    <a href="check-in-total-absences.php">
                        <button class='btn-missing'> ขาด <br>
                            <?php echo $countMissingWork; ?> วัน
                        </button>
                    </a>
                    <a href="check-in-total-late.php">
                        <button class='btn-late'> สาย <br>
                            <?php echo $countMiss; ?> วัน
                        </button><br>
                    </a>
                    <a href="check-in-total-leave.php">
                        <button class='btn-leave'> ลา <br>
                            <?php echo $countLeave ?> วัน
                        </button>
                    </a>
                    <a href="check-in-total-ontime.php">
                        <button class='btn-on-time'> ตรงเวลา <br>
                            <?php echo $countPunctual ?> วัน
                        </button>
                    </a>
                    <a href="check-in-total-back.php">
                        <button class='btn-go-back'> กลับก่อน <br>
                            <?php echo $countBack ?> วัน
                        </button>
                    </a>
                </div>
            </div>

            <div class="popup-container" id="demo-calendar-popup" style="background-color: #E9E6E6;">
                <div class="demo-calendar" id="demo-calendar-data"></div>
                <div class="detail-color">
                    <span>*หมายเหตุ*</span>
                    <div class="remark-color-first">
                        <div class="color-label">
                            <img src="../IMG/red.png" alt="" style="width: 10px;">
                            <span>ขาดงาน</span>
                        </div>

                        <div class="color-label">
                            <img src="../IMG/orange.png" alt="" style="width: 10px;">
                            <span>สาย</span>
                        </div>

                        <div class="color-label">
                            <img src="../IMG/blue.png" alt="" style="width: 10px; ">
                            <span>ลา</span>
                        </div>
                    </div>
                    <div class="remark-color-last">
                        <div class="color-label">
                            <img src="../IMG/green.png" alt="" style="width: 10px; ">
                            <span>ตรงเวลา</span><br>
                        </div>

                        <div class="color-label">
                            <img src="../IMG/pulple.png" alt="" style="width: 10px; ">
                            <span>กลับก่อน</span>
                        </div>

                        <div class="color-label">
                            <img src="../IMG/dark blue.png" alt="" style="width: 10px; ">
                            <span>วันหยุดประจำปี</span>
                        </div>
                    </div>
                </div>
            </div>


            <div class="overlay" id="overlay" onclick="togglePopupMobile()"></div>

            <script>
                function togglePopupMobile() {
                    var popup = document.getElementById('demo-calendar-popup');
                    var overlay = document.getElementById('overlay');

                    // Toggle the display of the pop-up and overlay
                    if (popup.style.display === 'none') {
                        popup.style.display = 'block';
                        overlay.style.display = 'block';
                    } else {
                        popup.style.display = 'none';
                        overlay.style.display = 'none';
                    }
                }
            </script>

            <?php
            foreach ($date_range as $date) {
                $day_date = $date->format('d');
                $formatted_date = $date->format('Y-m-d');
                $Actionsmode = 0;

                $check_inout_stmt = sqlsrv_prepare($conn, $select_check_inout_Query, array(&$card_id, &$formatted_date));
                $holiday_stmt = sqlsrv_prepare($conn, $select_holiday, array(&$formatted_date));
                $absence_record_stmt = sqlsrv_prepare($conn, $select_absence_record, array(&$card_id, &$formatted_date, &$formatted_date));
                $transaction_work_stmt = sqlsrv_prepare($conn, $select_transaction_work_Query, array(&$card_id, &$formatted_date));

                if (
                    sqlsrv_execute($check_inout_stmt) &&
                    sqlsrv_execute($absence_record_stmt) &&
                    sqlsrv_execute($holiday_stmt) &&
                    sqlsrv_execute($transaction_work_stmt)
                ) {

                    $holiday_result = sqlsrv_fetch_array($holiday_stmt, SQLSRV_FETCH_ASSOC);
                    $absence_record_result = sqlsrv_fetch_array($absence_record_stmt, SQLSRV_FETCH_ASSOC);
                    $check_inout_result = sqlsrv_fetch_array($check_inout_stmt, SQLSRV_FETCH_ASSOC);
                    $transaction_work_result = sqlsrv_fetch_array($transaction_work_stmt, SQLSRV_FETCH_ASSOC);

                    $shift = array(
                        'DD01' => 'ปกติ 1',
                        'DD02' => 'ปกติ 2',
                        'HOLIDAY' => 'วันหยุดนักขัต',
                        'LEAVE' => 'ลา',
                        'OFF' => 'วันหยุด',
                        'SA01' => 'กะ 1',
                        'SB01' => 'กะ 2',
                        'SC01' => 'กะ 3',
                        'TRAIN' => 'อบรม'
                    );
                    $shiftStatus = isset($shift[$transaction_work_result["shift_main"]]) ? $shift[$transaction_work_result["shift_main"]] : "";

                    if ($transaction_work_result["shift_main"] == "DD01") {
                        $time_in_set = "07:30:00";
                        $time_out_set = "16:30:00";
                    } elseif ($transaction_work_result["shift_main"] == "DD02") {
                        $time_in_set = "08:00:00";
                        $time_out_set = "17:00:00";
                    } elseif ($transaction_work_result["shift_main"] == "SA01") {
                        $time_in_set = "08:00:00";
                        $time_out_set = "16:00:00";
                    } elseif ($transaction_work_result["shift_main"] == "SB01") {
                        $time_in_set = "16:00:00";
                        $time_out_set = "00:00:00"; // Midnight, next day
                    } elseif ($transaction_work_result["shift_main"] == "SC01") {
                        $time_in_set = "00:00:00";
                        $time_out_set = "08:00:00";
                    } else {
                        // Handle default case
                        $time_in_set = "00:00:00";
                        $time_out_set = "00:00:00";
                    }

                    // Check the results and print the appropriate message
                    if ($holiday_result) {
                        $status = "<span style='color: #1b3d5e;'>" . $holiday_result["name"] . "</span>";
                        $symbol = $shiftStatus;
                        $time_in = '-';
                        $time_out = '-';
                        $Actionsmode = 0;
                        $approve_status = "-";
                        $OTmode = 0;

            ?>
                        <div class="display-status-checkin-edit">
                            <div class="display-rowData">
                                <span>
                                    <?php echo $day_date; ?>
                                </span>
                                <span class="work">
                                    <?php echo $symbol; ?>
                                </span>

                            </div>
                            <div class="status-check">
                                <span>
                                    <?php echo $status; ?>
                                </span>
                            </div>
                        </div>
                    <?php
                    } elseif ($absence_record_result) {
                        $status = " <span style='color: #007acc;'>ลางาน</span> ";
                        $symbol = $shiftStatus;
                        $time_in = '-';
                        $time_out = '-';
                        $Actionsmode = 0;
                        $approve_status = "-";
                        $OTmode = 0;
                    ?>
                        <div class="display-status-checkin-edit">
                            <div class="display-rowData">
                                <span>
                                    <?php echo $day_date; ?>
                                </span>
                                <span class="work">
                                    <?php echo $symbol; ?>
                                </span>
                            </div>
                            <div class="status-check">
                                <span>
                                    <?php echo $status; ?>
                                </span>
                            </div>
                        </div>
                    <?php
                    } elseif ($transaction_work_result["shift_main"] == "OFF") {
                        $status = " <span style='color: #007acc;'>วันหยุดประจำสัปดาห์</span> ";
                        $symbol = $shiftStatus;
                        $time_in = '-';
                        $time_out = '-';
                        $Actionsmode = 0;
                        $approve_status = "-";
                        $OTmode = 0;
                    ?>
                        <div class="display-status-checkin-edit">
                            <div class="display-rowData">
                                <span>
                                    <?php echo $day_date; ?>
                                </span>
                                <span class="work">
                                    <?php echo $symbol; ?>
                                </span>
                            </div>
                            <div class="status-check">
                                <span>
                                    <?php echo $status; ?>
                                </span>
                            </div>
                        </div>
                    <?php
                    } elseif ($date > (new DateTime())->modify('+0 day')) {
                        $status = " - ";  // วันที่ยังไม่ถึง
                        $symbol = $shiftStatus;
                        $time_in = '-';
                        $time_out = '-';
                        $Actionsmode = 0;
                        $approve_status = "-";
                        $OTmode = 0;
                    ?>
                        <div class="display-status-checkin-edit">
                            <div class="display-rowData">
                                <span>
                                    <?php echo $day_date; ?>
                                </span>
                                <span class="work">
                                    <?php echo $symbol; ?>
                                </span>
                            </div>
                            <span>
                                <?php echo $status; ?>
                            </span>
                        </div>

                    <?php
                    } elseif ($check_inout_result) {
                        $Actionsmode = 1;
                        $symbol = $shiftStatus;
                        $time_in = $check_inout_result['time_in'] ? $check_inout_result['time_in']->format('H:i') : "";
                        $time_out = $check_inout_result['time_out'] ? $check_inout_result['time_out']->format('H:i') : "";
                        $approve_status = $check_inout_result['approve_status'];
                        //เวลา
                        if ($time_in >= $time_in_set) {
                            $status = '<label style="color: #FF9922;">มาสาย</label>';
                        } elseif ($time_in == "" && $time_out == "") {
                            $status = '<label style="color: red;">ขาดงาน</label>';
                            $time_in = "<label style='color: red;'>ขาดงาน</label>";
                            $time_out = "<label style='color: red;'>ขาดงาน</label>";
                        } elseif ($time_in <= $time_in_set && $time_out >= $time_out_set) {
                            $status = '<label style="color: #5BAF33;">ตรงเวลา</label>';
                            $OTmode = 1;
                        } elseif ($time_in <= $time_in_set && $time_out == "") {
                            $status = '<label style="color: #5BAF33;">ตรงเวลา</label>';
                            $OTmode = 0;
                        } elseif ($time_out <= $time_out_set) {
                            $status = '<label style="color: #9747FF; ">กลับก่อน</label>';
                        } else {
                            $status = " <label style='color: red;'>ขาดงาน</label>";
                            $time_in = "-";
                            $time_out = "-";
                        }
                        $Link = "check_inout_id=" . $check_inout_result['check_inout_id'];
                    ?>

                        <div class="display-status-checkin-edit">
                            <div class="display-rowData">
                                <span>
                                    <?php echo $day_date; ?>
                                </span>
                                <span class="work">
                                    <?php echo $symbol; ?>
                                </span>
                            </div>

                            <div class="diplay-rowDetail">
                                <div class="display-status">
                                    <div class="status-check">
                                        <span>
                                            <?php echo $status; ?>
                                            <?php
                                            switch ($approve_status) {
                                                case 'waiting':
                                                    echo '<div class="status-edit"><span class="waiting-status">รออนุมัติ</span></div>';
                                                    break;
                                                case 'approve':
                                                    echo '<div class="status-edit"><span class="approve-status">อนุมัติ</span></div>';
                                                    break;
                                                case 'reject':
                                                    echo '<div class="status-edit"><span class="reject-status">ไม่อนุมัติ</span></div>';
                                                    break;
                                                default:
                                                    break;
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="button-action">
                                        <?php if ($Actionsmode == 0) : ?>

                                        <?php else : ?>

                                            <a id="shift-button" href="../shift/shift-progress-step4-employee.php.?date=<?php echo $formatted_date; ?>">สลับกะ</a>

                                            <?php if ($OTmode == 1) { ?>
                                                <a id="ot-button" href="">OT</a>
                                            <?php } ?>

                                            <a id="edit-button" href="check-in-edit.php?<?php echo $Link; ?>">แก้เวลา</a>
                                            <a id="detail-button" href="check-in-detail-edit.php?<?php echo $Link; ?>"><img src="../IMG/warn.png" alt=""></a>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="displayTime-in-out">
                                    <span>เข้างาน :
                                        <?php echo $time_in; ?>
                                    </span>
                                    <span>ออกงาน :
                                        <?php echo $time_out; ?>
                                    </span>
                                </div>

                            </div>
                        </div>

                    <?php
                    } else {
                        $status = " <span style='color: red;'>ขาดงาน</span>";
                        $Actionsmode = 1;
                        $symbol = $shiftStatus;
                        $Link = "check_inout_date=" . $formatted_date;
                    ?>
                        <div class="display-status-checkin-edit">
                            <div class="display-rowData">
                                <span>
                                    <?php echo $day_date; ?>
                                </span>
                                <span class="work">
                                    <?php echo $symbol; ?>
                                </span>
                            </div>

                            <div class="diplay-rowDetail">
                                <div class="display-status">
                                    <div class="status-check">
                                        <span>
                                            <?php echo $status;
                                            ?>
                                        </span>
                                    </div>
                                    <div class="button-action">
                                        <?php if ($Actionsmode == 0) : ?>

                                        <?php else : ?>
                                            <a id="shift-button" href="../shift/shift-progress-step4-employee.php.?date=<?php echo $formatted_date; ?>">สลับกะ</a>
                                            <a id="edit-button" href="check-in-edit.php?<?php echo $Link; ?>">แก้เวลา</a>
                                            <a id="detail-button" href="check-in-detail-edit.php?<?php echo $Link; ?>"><img src="../IMG/warn.png" alt=""></a>
                                        <?php endif; ?>
                                    </div>

                                </div>

                                <div class="displayTime-in-out">
                                    <span>เข้างาน : <a style='color: red;'>ขาดงาน</a></span>
                                    <span>ออกงาน : <a style='color: red;'>ขาดงาน</a></span>
                                    <!-- <span>เวลาเข้างาน :
                                        <?php echo $time_in; ?>
                                    </span>
                                    <span>เวลาออกงาน :
                                        <?php echo $time_out; ?>
                                    </span> -->
                                </div>
                            </div>
                        </div>
            <?php
                    }
                }
            }
            ?>
        </div>

    </div>
</body>
<?php include('../includes/footer.php') ?>


<script>
    $(document).ready(function() {
        $("#demo-calendar-data").zabuto_calendar({

            translation: {
                "months": {
                    "1": "มกราคม",
                    "2": "กุมภาพันธ์",
                    "3": "มีนาคม",
                    "4": "เมษายน",
                    "5": "พฤษภาคม",
                    "6": "มิถุนายน",
                    "7": "กรกฎาคม",
                    "8": "สิงหาคม",
                    "9": "กันยายน",
                    "10": "ตุลาคม",
                    "11": "พฤศจิกายน",
                    "12": "ธันวาคม"
                },
                "days": {
                    "0": "อา.",
                    "1": "จ.",
                    "2": "อ.",
                    "3": "พ.",
                    "4": "พฤ.",
                    "5": "ศ.",
                    "6": "ส."
                }
            },

            events: <?php echo json_encode($events, JSON_UNESCAPED_UNICODE); ?>
        });

        $("#calendar-data").zabuto_calendar({

            translation: {
                "months": {
                    "1": "มกราคม",
                    "2": "กุมภาพันธ์",
                    "3": "มีนาคม",
                    "4": "เมษายน",
                    "5": "พฤษภาคม",
                    "6": "มิถุนายน",
                    "7": "กรกฎาคม",
                    "8": "สิงหาคม",
                    "9": "กันยายน",
                    "10": "ตุลาคม",
                    "11": "พฤศจิกายน",
                    "12": "ธันวาคม"
                },
                "days": {
                    "0": "อา.",
                    "1": "จ.",
                    "2": "อ.",
                    "3": "พ.",
                    "4": "พฤ.",
                    "5": "ศ.",
                    "6": "ส."
                }
            },

            events: <?php echo json_encode($events, JSON_UNESCAPED_UNICODE); ?>
        });
    });
</script>

</html>