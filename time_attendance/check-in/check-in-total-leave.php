<?php
session_start();
// session_regenerate_id(true);

header("Cache-Control: no-cache, must-revalidate");

include("../database/connectdb.php");
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
$start_date = new DateTime('first day of this year');
$end_date = new DateTime('first day of next year');
$interval = new DateInterval('P1D');
$date_range = new DatePeriod($start_date, $interval, $end_date);

$select_absence_record_Query = "SELECT * FROM absence_record WHERE card_id = ? AND date_start <= ? AND date_end >= ? ORDER BY date_start ASC";

?>
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
                                    <h2>รายละเอียดการลางาน</h2>
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
                                            รายละเอียดการลางาน
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
                                    <label for="">จำนวนการลางานในปีนี้</label>
                                    <div class="count">
                                        <input type="text" value="<?php echo $_SESSION['countLeave']; ?>" readonly>
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
                                                $is_weekend = in_array($date->format('N'), [6, 7]);

                                                // Execute the statements
                                                if (sqlsrv_execute($check_inout_stmt) && sqlsrv_execute($absence_record_stmt) && sqlsrv_execute($holiday_stmt)) {
                                                    // Fetch the results
                                                    $check_inout_result = sqlsrv_fetch_array($check_inout_stmt, SQLSRV_FETCH_ASSOC);
                                                    $absence_record_result = sqlsrv_fetch_array($absence_record_stmt, SQLSRV_FETCH_ASSOC);
                                                    $holiday_stmt_result = sqlsrv_fetch_array($holiday_stmt, SQLSRV_FETCH_ASSOC);

                                                    if ($absence_record_result) {
                                                        $status = " <label style='color: #007acc;'>ลางาน</label> ";
                                            ?>
                                                        <tr>
                                                            <td><?php echo $day_date . ' ' . $thaiMonthYear; ?></td>
                                                            <td>วัน<?php echo $day_thai; ?></td>
                                                            <td style="color: #007acc;"><?php echo $status; ?></td>
                                                        </tr>
                                            <?php
                                                    } elseif ($check_inout_result) {
                                                    } elseif ($is_weekend) {
                                                    } elseif ($date > (new DateTime())->modify('-1 day')) {
                                                    } elseif ($check_inout_result) {
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
                <span>รายละเอียดการลางาน</span>
            </div>
        </div>

        <div class="topic-top">
            <span>จำนวนการลางานในปีนี้</span>
            <input type="text" placeholder="<?php echo $countLeave; ?>" readonly>
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

                if ($absence_record_result) {
                    $status = " <span style='color: #007acc;'>ลางาน</span> ";
        ?>
                    <div class="detailMissing">
                        <span style="color: #0B6BC1;width: 15%;"><?php echo $day_date . ' ' . $thaiMonthYear; ?></span>
                        <span style="width: 20%;">วัน<?php echo $day_thai; ?></span>
                        <span style="color: #FF5643;"><?php echo $status; ?></span>
                    </div>

        <?php

                } elseif ($check_inout_result) {
                } elseif ($is_weekend) {
                } elseif ($date > (new DateTime())->modify('-1 day')) {
                } elseif ($check_inout_result) {
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
                    ".detailMissing span[style='color: #6FD5DC;']");

                if (detailMissingElement) {
                    detailMissingElement.style.color = "#5BAF33"; // Change to green color
                }
            }
        </script>
    </div>
</body>
<?php include('../includes/footer.php'); ?>