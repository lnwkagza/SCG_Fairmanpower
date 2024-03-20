<?php
session_start();
include('../components-desktop/admin/include/header.php');
include("../database/connectdb.php");
?>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/admin/report-admin.css">

<!-- CSS Mobile -->
<link rel="stylesheet" href="../assets/css/report-admin.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/navbar.css">

<!-- datatable -->
<link href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.bootstrap5.min.js"></script>

<?php
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

$report_log_Query = "SELECT 
    report_log.card_id,
    report_log.input_timestamp,
    employee.scg_employee_id,
    employee.prefix_thai,
    employee.firstname_thai,
    employee.lastname_thai,
    employee.employee_email,
    employee.employee_image
    FROM report_log
    INNER JOIN employee ON report_log.card_id = employee.card_id";

// Check if the connection is successful

$report_log_stmt = sqlsrv_query($conn, $report_log_Query);
$report_log = array();
while ($log = sqlsrv_fetch_array($report_log_stmt, SQLSRV_FETCH_ASSOC)) {
    $report_log[] = $log;
}
?>
<script>
    $(document).ready(function() {
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
    });
</script>
</head>

<body>
    <div class="desktop">
        <?php include('../components-desktop/admin/include/sidebar.php'); ?>
        <?php include('../components-desktop/admin/include/navbar.php'); ?>
        <div class="main-container">
            <div class="pd-ltr-20 xs-pd-20-10">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <h2>คำขอแจ้งปัญหาการใช้งาน</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item active">
                                            <a style="pointer-events:none;cursor:default;">คำขอแจ้งปัญหาการใช้งาน</a>
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p" id="box1">

                                <div class="desktop-table-container">
                                    <table id="table1" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th class="name">ผู้แจ้งปัญหา</th>
                                                <th class="date">เวลาที่แจ้งปัญหา</th>
                                                <th class=" action">การจัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($report_log as $log) : ?>
                                                <tr>
                                                    <td class="name">
                                                        <div class="row">
                                                            <div style="margin-right: 10px;margin-left: 5px;">
                                                                <img src="<?php echo (!empty($log['employee_image'])) ? '../../admin/uploads_img/' . $log['employee_image'] : '../IMG/user.png'; ?>" class="border-radius-100 shadow" width="40" height="40" alt="">
                                                            </div>
                                                            <div>
                                                                <b><?= $log['scg_employee_id'] . " " . $log['prefix_thai'] . $log['firstname_thai'] . ' ' . $log['lastname_thai']; ?></b><br>
                                                                <a class="text-primary"><?= $log['employee_email'] ?></a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="date">
                                                        <?= $log['input_timestamp']->format('d-m-Y') . " " . $log['input_timestamp']->format('H:i:s'); ?>
                                                    </td>
                                                    <td class="action"><button class="btn btn-info" onclick="window.location.href='report-admin-detail.php'">รายละเอียด</button>
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
                <span>แจ้งปัญหาการใช้งาน</span>
            </div>
        </div>
        <div class="status-detail">
            <table>
                <tr>
                    <th colspan=3 style="height: 2vh">รายการ : แจ้งปัญหา</th>
                </tr>
                <tr>
                    <th class="thName">ชื่อ - สกุล</th>
                    <th>เวลาที่แจ้งปัญหา</th>
                    <th></th>
                </tr>
                <?php foreach ($report_log as $log) : ?>
                    <tr>
                        <td><?php echo $log['firstname_thai'] . ' ' . $log['lastname_thai']; ?></td>
                        <td><?php echo $log['input_timestamp']->format('d-m-Y H:i:s'); ?></td>
                        <td><img src="../IMG/warn.png" alt="" onclick="location.href='report-admin-detail.php'"></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>

</html>