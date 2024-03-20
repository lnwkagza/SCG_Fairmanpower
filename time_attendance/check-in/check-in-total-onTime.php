<?php
session_start();
session_regenerate_id(true);

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

$time_stamp_MONTH = date("m");
$time_stamp_YEAR = date("Y");
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

// Database queries with placeholders
$select_check_inout_Query = "SELECT * FROM check_inout WHERE card_id = ? AND YEAR(date) = ? AND MONTH(date) = ? ORDER BY date ASC";
$check_inout_stmt = sqlsrv_prepare($conn, $select_check_inout_Query, array(&$card_id, &$time_stamp_YEAR, &$time_stamp_MONTH));
$check_inout_result = sqlsrv_fetch_array($check_inout_stmt, SQLSRV_FETCH_ASSOC);


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
                                    <h2>รายละเอียดการเข้างานตรงเวลา</h2>
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
                                            รายละเอียดการเข้างานตรงเวลา
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
                                    <label for="">จำนวนการเข้างานตรงเวลาในปีนี้</label>
                                    <div class="count">
                                        <input type="text" value="<?php echo $_SESSION['countPunctual']; ?>" readonly>
                                    </div>
                                    <label>ครั้ง</label>
                                </div>

                                <div class="desktop-table">
                                    <table class="data-table table stripe hover nowrap">
                                        +
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
                <span>รายละเอียดการเข้างานตรงเวลา</span>
            </div>
        </div>

        <div class="topic-top">
            <span>จำนวนการเข้างานตรงเวลาในปีนี้</span>
            <input type="text" placeholder="<?php echo $countPunctual; ?>" readonly>
            <span>ครั้ง</span>
        </div>

        <div class="display-month-totalDay">
            <div class="month-of-this-year">
                <span>มกราคม</span>
            </div>
            <div class="detailMissing">
                <span style="color: #0B6BC1; width: 14%;">1 มค.</span>
                <span style="width: 20%;">วันอาทิตย์ </span>
                <span style="color: #5BAF33;">ตรงเวลา</span>
            </div>
        </div>

        <div class="display-month-totalDay">
            <div class="month-of-this-year">
                <span>กุมภาพันธ์</span>
            </div>
            <div class="detailMissing">
                <span style="color: #0B6BC1; width: 14%;">1 ธค.</span>
                <span style="width: 20%;">วันอาทิตย์ </span>
                <span style="color: #5BAF33;">ตรงเวลา</span>
            </div>
        </div>
    </div>
</body>
<?php include('../includes/footer.php'); ?>