<?php
session_start();
// session_regenerate_id(true);

header("Cache-Control: no-cache, must-revalidate");

include("../database/dbconnect.php");
// include("dbconnect.php");


include('../components-desktop/employee/include/header.php');
echo '<div id="loader"></div>';
?>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/employee/check-in-total.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/check-in-total.css">
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">


<link rel="stylesheet" href="../assets/css/loader.css">
<script src="../assets/script/loader-normal.js"></script>

<?php

//--------------------------------------------------------------------------------------------------
$select_dayoff = "SELECT work_format.day_off1 AS day_off1,
                  work_format.day_off2 AS day_off2
                  FROM employee 
                  INNER JOIN work_format 
                  ON employee.work_format_code = work_format.work_format_code
                  WHERE employee.card_id = ?";

// Prepare the SQL statement
$dayoff_stmt = sqlsrv_prepare($conn, $select_dayoff, array(&$_SESSION["card_id"]));

// Execute the statement
sqlsrv_execute($dayoff_stmt);

// Fetch the results
$row = sqlsrv_fetch_array($dayoff_stmt, SQLSRV_FETCH_ASSOC);

$dayMap = [
    'Mon' => 1,
    'Tue' => 2,
    'Wed' => 3,
    'Thu' => 4,
    'Fri' => 5,
    'Sat' => 6,
    'Sun' => 7,
];

$day_off1 = $dayMap[$row['day_off1']];
$day_off2 = $dayMap[$row['day_off2']];

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
function getThaiDay($englishDay)
{
    $thaiDays = array(
        'Monday' => 'จันทร์',
        'Tuesday' => 'อังคาร',
        'Wednesday' => 'พุธ',
        'Thursday' => 'พฤหัสบดี',
        'Friday' => 'ศุกร์',
        'Saturday' => 'เสาร์',
        'Sunday' => 'อาทิตย์',
    );

    return isset($thaiDays[$englishDay]) ? $thaiDays[$englishDay] : $englishDay;
}
//----------------------------------------------------------------------------------------------------

// Define the date range
$card_id = $_SESSION["card_id"];

$start_date = new DateTime('first day of this month');
$end_date = new DateTime('first day of next month');
$interval = new DateInterval('P1D');
$date_range = new DatePeriod($start_date, $interval, $end_date);

$select_check_inout_Query = "SELECT * FROM check_inout WHERE card_id = ? AND date = ? ORDER BY date ASC";

?>
</head>

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
                                    <h2>รายละเอียดการขาดงาน</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor:default;">ลงชื่อเข้า-ออกงาน</a>
                                        </li>
                                        <li class="breadcrumb-item" style="cursor:default;">
                                            <a href="check-in-attendance-schedule.php">รายละเอียดการเข้า-ออกงาน</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            รายละเอียดการขาดงาน
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
                                    <label for="">จำนวนการขาดงานในปีนี้</label>
                                    <div class="count">
                                        <input type="text" value="<?php echo $_SESSION['countMissingWork']; ?>" readonly>
                                    </div>
                                    <label>ครั้ง</label>
                                </div>

                                <div class="desktop-table">
                                    <table class="data-table table stripe hover nowrap">
                                        <thead>
                                            <tr>
                                                <th>วันที่</th>
                                                <th>วัน</th>
                                                <th>สถานะ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($date_range as $date) {
                                                // Format the current date
                                                $formatted_date = $date->format('Y-m-d');
                                                $day_date = $date->format('d');
                                                $day = $date->format('l'); // Get the day of the week in English
                                                $day_thai = getThaiDay($day);

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

                                                    if ($holiday_result) {
                                                    } elseif ($absence_record_result) {
                                                    } elseif ($transaction_work_result["shift_main"] == "OFF") {
                                                    } elseif ($check_inout_result) {
                                                    } elseif ($date > (new DateTime())->modify('-1 day')) {
                                                    } elseif ($check_inout_result) {

                                                        $symbol = $check_inout_result['symbol'];
                                                        $time_in = $check_inout_result['time_in'] ? $check_inout_result['time_in']->format('H:i') : null;
                                                        $time_out = $check_inout_result['time_out'] ? $check_inout_result['time_out']->format('H:i') : null;

                                                        //เวลา
                                                        if ($time_in == "" && $time_out == "") {
                                                            $status = '<label style="color: red;">ขาดงาน</label>';
                                                            $time_in = "<a style='color: red;'>ขาดงาน</a>";
                                                            $time_out = "<a style='color: red;'>ขาดงาน</a>";
                                                            $Actionsmode = 1;
                                                            $OTmode = 0;
                                                        }

                                                        //การทำงาน
                                                        if ($symbol == "normal1") {
                                                            $symbol = "ทำงานปกติ";
                                                        } elseif ($symbol == "normal2") {
                                                            $symbol = "ทำงานปกติ 2";
                                                        } elseif ($symbol == "1") {
                                                            $symbol = "กะ 1";
                                                        } elseif ($symbol == "2") {
                                                            $symbol = "กะ 2";
                                                        } elseif ($symbol == "3") {
                                                            $symbol = "กะ 3";
                                                        }
                                            ?>
                                                        <tr>
                                                            <td><?php echo $day_date . ' ' . $thaiMonthYear; ?></td>
                                                            <td>วัน<?php echo $day_thai; ?></td>
                                                            <td style="color: #red;"><?php echo $status; ?></td>
                                                        </tr>
                                                    <?php

                                                    } else {
                                                        $status = " <label style='color: red;'>ขาดงาน</label>";
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $day_date . ' ' . $thaiMonthYear; ?></td>
                                                            <td>วัน<?php echo $day_thai; ?></td>
                                                            <td style="color: red;"><?php echo $status; ?></td>
                                                        </tr>
                                            <?php
                                                    }
                                                }
                                            }
                                            ?>
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
                <span>รายละเอียดการขาดงาน</span>
            </div>
        </div>

        <div class="topic-top">
            <span>จำนวนการขาดงานในปีนี้</span>
            <input type="text" placeholder="<?php echo $countMissingWork; ?>" readonly>
            <span>ครั้ง</span>
        </div>

        <?php
        foreach ($date_range as $date) {
            // Format the current date
            $formatted_date = $date->format('Y-m-d');
            $day_date = $date->format('d');
            $day = $date->format('l'); // Get the day of the week in English
            $day_thai = getThaiDay($day);
            $is_weekend = in_array($date->format('N'), [6, 7]);

            // Execute the statements
            if (sqlsrv_execute($check_inout_stmt) && sqlsrv_execute($absence_record_stmt) && sqlsrv_execute($holiday_stmt)) {
                // Fetch the results
                $check_inout_result = sqlsrv_fetch_array($check_inout_stmt, SQLSRV_FETCH_ASSOC);
                $absence_record_result = sqlsrv_fetch_array($absence_record_stmt, SQLSRV_FETCH_ASSOC);
                $holiday_stmt_result = sqlsrv_fetch_array($holiday_stmt, SQLSRV_FETCH_ASSOC);

                if ($holiday_stmt_result) {
                } elseif ($absence_record_result) {
                } elseif ($check_inout_result) {
                } elseif ($is_weekend) {
                } elseif ($date > (new DateTime())->modify('-1 day')) {
                } elseif ($check_inout_result) {

                    $symbol = $check_inout_result['symbol'];
                    $time_in = $check_inout_result['time_in'] ? $check_inout_result['time_in']->format('H:i') : null;
                    $time_out = $check_inout_result['time_out'] ? $check_inout_result['time_out']->format('H:i') : null;

                    //เวลา
                    if ($time_in == "" && $time_out == "") {
                        $status = '<span style="color: red;">ขาดงาน</span>';
                        $time_in = "<a style='color: red;'>ขาดงาน</a>";
                        $time_out = "<a style='color: red;'>ขาดงาน</a>";
                        $Actionsmode = 1;
                        $OTmode = 0;
                    }

                    //การทำงาน
                    if ($symbol == "normal1") {
                        $symbol = "ทำงานปกติ";
                    } elseif ($symbol == "normal2") {
                        $symbol = "ทำงานปกติ 2";
                    } elseif ($symbol == "1") {
                        $symbol = "กะ 1";
                    } elseif ($symbol == "2") {
                        $symbol = "กะ 2";
                    } elseif ($symbol == "3") {
                        $symbol = "กะ 3";
                    }
        ?>
                    <div class="detailMissing">
                        <span style="color: #0B6BC1;width: 15%;"><?php echo $day_date . ' ' . $thaiMonthYear; ?></span>
                        <span style="width: 20%;">วัน<?php echo $day_thai; ?></span>
                        <span style="color: #red;"><?php echo $status; ?></span>
                    </div>

                <?php

                } else {
                    $status = " <span style='color: red;'>ขาดงาน</span>";
                ?>
                    <div class="detailMissing">
                        <span style="color: #0B6BC1;width: 15%;"><?php echo $day_date . ' ' . $thaiMonthYear; ?></span>
                        <span style="width: 20%;">วัน<?php echo $day_thai; ?></span>
                        <span style="color: #red;"><?php echo $status; ?></span>
                    </div>

        <?php
                }
            }
        }

        ?>

        <div class="btn-confirm">
            <button onclick="changeStatusColor()">ยืนยัน</button>
        </div>

        <script>
            function changeStatusColor() {
                var detailMissingElement = document.querySelector(
                    ".detailMissing span[style='color: #FF5643;']");

                if (detailMissingElement) {
                    detailMissingElement.style.color = "#5BAF33"; // Change to green color
                }
            }
        </script>


    </div>
</div>
</body>
<?php include('../includes/footer.php'); ?>