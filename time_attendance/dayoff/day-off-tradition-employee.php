<?php
session_start();
session_regenerate_id(true);
//---------------------------------------------------------------------------------------
include("../database/connectdb.php");
include('../components-desktop/employee/include/header.php');
// include("dbconnect.php");
//---------------------------------------------------------------------------------------
?>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/employee/day-off-tradition-employee.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/day-off-tradition.css">

<!-- datatable -->
<link href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.bootstrap5.min.js"></script>


<link rel="stylesheet" href="../assets/css/loader.css">
<script src="../assets/script/loader-normal.js"></script>

<?php
date_default_timezone_set('Asia/Bangkok');
$thaiDayAbbreviations = array(
    'Monday' => 'จันทร์',
    'Tuesday' => 'อังคาร',
    'Wednesday' => 'พุธ',
    'Thursday' => 'พฤหัสบดี',
    'Friday' => 'ศุกร์',
    'Saturday' => 'เสาร์',
    'Sunday' => 'อาทิตย์'
);
function thaiMonthName($month)
{
    $months = [
        '01' => 'มกราคม',
        '02' => 'กุมภาพันธ์',
        '03' => 'มีนาคม',
        '04' => 'เมษายน',
        '05' => 'พฤษภาคม',
        '06' => 'มิถุนายน',
        '07' => 'กรกฎาคม',
        '08' => 'สิงหาคม',
        '09' => 'กันยายน',
        '10' => 'ตุลาคม',
        '11' => 'พฤศจิกายน',
        '12' => 'ธันวาคม',
    ];

    return isset($months[$month]) ? $months[$month] : '';
}

$time_stamp = date("Y");
$Y_thai = 543;
$datethai = $time_stamp + $Y_thai;

$sql = "SELECT * FROM holiday WHERE YEAR(date) = ? ORDER BY date ASC"; // Change the condition to filter by the year 2024
$params = array($time_stamp);

$holiday = sqlsrv_query($conn, $sql, $params);

$holidaydata = array();

while ($row = sqlsrv_fetch_array($holiday, SQLSRV_FETCH_ASSOC)) {
    // Process each row of holiday data
    $holidaydata[] = $row;
}
?>

<script>
$(document).ready(function() {
    //data-table
    new DataTable('#table1', {
        "autoWidth": false,
        "lengthMenu": [
            [10, 20, 50, -1],
            [10, 20, 50, "ทั้งหมด"]
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
                                        <h2>วันหยุดตามประเพณีประจำปี
                                            <?= $datethai; ?>
                                        </h2>
                                    </div>
                                    <nav aria-label="breadcrumb" role="navigation">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item">
                                                <a style="pointer-events:none;cursor:default;">วันหยุด</a>
                                            </li>
                                            <li class="breadcrumb-item active" aria-current="page"
                                                style="cursor:default;">
                                                วันหยุดตามประเพณี
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                                <div class="card-box pd-30 pt-10 height-100-p">

                                    <div class="desktop-data-table-container">
                                        <table class="data table table stripe hover nowrap" id="table1">
                                            <thead>
                                                <tr>
                                                    <th>วันที่</th>
                                                    <th>วัน</th>
                                                    <th>ชื่อวันหยุด</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $currentMonth = '';
                                                foreach ($holidaydata as $row):
                                                    $englishDay = date('l', strtotime($row['date']->format("Y-m-d")));
                                                    $thaiAbbreviation = isset($thaiDayAbbreviations[$englishDay]) ? $thaiDayAbbreviations[$englishDay] : '';
                                                    $month = (new DateTime($row['date']->format("Y-m-d")))->format("m");
                                                    ?>
                                                <tr>
                                                    <?php if ($month != $currentMonth) { ?>
                                                    <td>
                                                        <?php echo $row['date']->format("d") . " " . thaiMonthName($month) . " " . $datethai; ?>
                                                    </td>
                                                    <?php } ?>
                                                    <td>
                                                        <?php echo $thaiAbbreviation; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row['name'] ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
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
                    <span>วันหยุดตามประเพณี</span>
                </div>
            </div>

            <div class="container">
                <div class="display-topic">
                    <span>วันหยุดตามประเพณีนิยม ประจำปี
                        <?= $datethai; ?>
                    </span>
                </div>
                <div class="container-display-day">
                    <div class="display-day-off-thisYear">
                        <?php
                        $currentMonth = ''; // Variable to track the current month
                        
                        foreach ($holidaydata as $row):
                            $englishDay = date('l', strtotime($row['date']->format("Y-m-d")));
                            $thaiAbbreviation = isset($thaiDayAbbreviations[$englishDay]) ? $thaiDayAbbreviations[$englishDay] : '';
                            $month = (new DateTime($row['date']->format("Y-m-d")))->format("m");
                            // Check if the month has changed
                            if ($month != $currentMonth) {
                                echo '<div class="show-month">';
                                echo '<span>' . thaiMonthName($month) . '</span>';
                                echo '</div>';
                                $currentMonth = $month;
                            }
                            ?>
                        <div class="show-detail-day">
                            <div class="container-detail">
                                <span class="number">
                                    <?php echo (new DateTime($row['date']->format("Y-m-d")))->format("d"); ?>
                                </span>
                                <span class="day">
                                    <?php echo $thaiAbbreviation; ?>
                                </span>
                                <span class="detail" onclick="showFullText('<?php echo $row['name']; ?>')">
                                    <?php echo mb_strimwidth($row['name'], 0, 25, '...'); ?>
                                    <span class="popup-text">
                                        <?php echo $row['name']; ?>
                                    </span>
                                </span>

                                <div id="myModal" class="modal">
                                    <div class="modal-content">
                                        <span class="close" onclick="closeModal()">&times;</span>
                                        <span id="fullText"></span>
                                    </div>
                                </div>
                                <script>
                                function showFullText(fullText) {
                                    document.getElementById("fullText").innerHTML = fullText;
                                    document.getElementById("myModal").style.display = "block";
                                }

                                function closeModal() {
                                    document.getElementById("myModal").style.display = "none";
                                }
                                </script>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include('../includes/footer.php') ?>