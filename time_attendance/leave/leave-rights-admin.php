<?php
include("../database/connectdb.php");
include('../components-desktop/employee/include/header.php');

session_start();
?>
<!-- Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="node_modules\sweetalert2\dist\sweetalert2.css">

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/employee/leave-rights-employee.css">

<!-- CSS Mobile -->
<link rel="stylesheet" href="../assets/css/leave-rights.css">
<link rel=" stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">

<!-- Loading Screen -->
<link rel="stylesheet" href="../assets/css/loader.css">
<script src="../assets/script/loader-normal.js"></script>

<?php
//-------------------------------------------------------------------------------------------------------------------------------

//เวลาปัจจุบัน
date_default_timezone_set('Asia/Bangkok');
$time_stamp = date("Y-m-d");
$time_stamp_year = date("Y");

//-------------------------------------------------------------------------------------------------------------------------------

$date_first = date("01/01/Y", strtotime($time_stamp));
$date_last = date("t/12/Y", strtotime($time_stamp));

//-------------------------------------------------------------------------------------------------------------------------------

$query = "SELECT card_id, pl_id FROM pl_info WHERE card_id = ?";
$params = array($_SESSION['card_id']);
$stmt = sqlsrv_query($conn, $query, $params);

$pl_ids = array();
while (sqlsrv_fetch($stmt)) {
    // Retrieve the values after sqlsrv_fetch
    $card_id = sqlsrv_get_field($stmt, 0);
    $pl_id = sqlsrv_get_field($stmt, 1);

    // echo $pl_id;  // Output card_id
    $pl_ids[] = $pl_id;
}


// Fetching pl_ids from pl_info table
$query = "SELECT card_id, pl_id FROM pl_info WHERE card_id = ?";
$params = array($_SESSION['card_id']);
$stmt = sqlsrv_query($conn, $query, $params);

$pl_ids = array();
while (sqlsrv_fetch($stmt)) {
    $card_id = sqlsrv_get_field($stmt, 0);
    $pl_id = sqlsrv_get_field($stmt, 1);

    // echo $pl_id;  // Output pl_id
    $pl_ids[] = $pl_id;
}

// Fetching data from absence_quota table
$query = "SELECT * FROM absence_quota WHERE card_id = ? AND date_year = ?";
$params = array($_SESSION['card_id'], $time_stamp_year);
$stmt = sqlsrv_query($conn, $query, $params);

// If no data exists, perform an INSERT
if (!sqlsrv_has_rows($stmt)) {
    $query = "INSERT INTO absence_quota
    (card_id, annual_leave, annual_leave_collect, maternity_leave, ordination_leave, ordination_leave_nopaid, haj_leave, haj_leave_nopaid, training_leave_nopaid, csr_leave, work_sick_leave, military_service_leave, other_leave, other_leave_nopaid, sick_leave, date_year)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $params = array(
        $_SESSION['card_id'],
        20,
        20,
        90,
        90,
        30,
        90,
        30,
        30,
        90,
        480,
        60,
        90,
        30,
        180,
        $time_stamp_year
    );

    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "Inserted successfully. Rows affected: " . sqlsrv_rows_affected($stmt);
    }
} else {
    // Data exists, check if annual_leave is not set before updating
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if (empty($row['annual_leave'])) {
        // Checking pl_info and updating annual_leave
        $updated_annual_leave = null;

        if (!empty($pl_ids)) {
            $intersect = array_intersect($pl_ids, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]);
            if (!empty($intersect)) {
                $updated_annual_leave = ($intersect[0] <= 4) ? 10 : (($intersect[0] <= 8) ? 15 : 20);
            }
        }

        if ($updated_annual_leave !== null) {
            $query = "UPDATE absence_quota SET annual_leave = ? WHERE card_id = ? AND date_year = ?";
            $params = array($updated_annual_leave, $_SESSION['card_id'], $time_stamp_year);
            $stmt = sqlsrv_query($conn, $query, $params);

            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            } else {
                echo "Updated successfully. Rows affected: " . sqlsrv_rows_affected($stmt);
            }
        }
    }
}

//-------------------------------------------------------------------------------------------------------------------------------

$sql_employee = sqlsrv_query($conn, "SELECT * FROM employee WHERE card_id = ?", array($_SESSION['card_id']));
$rs_employee = sqlsrv_fetch_array($sql_employee, SQLSRV_FETCH_ASSOC);
// echo $rs_employee["gender"];

//-------------------------------------------------------------------------------------------------------------------------------

$sql_absence_quota = sqlsrv_query($conn, "SELECT * FROM absence_quota WHERE card_id = ?", array($_SESSION['card_id']));
$rs_absence_quota = sqlsrv_fetch_array($sql_absence_quota, SQLSRV_FETCH_ASSOC);

//-------------------------------------------------------------------------------------------------------------------------------

$sql_calculate_leave_days = sqlsrv_query(
    $conn,
    " SELECT
  card_id,
  SUM(CASE WHEN absence_type_id = 1 THEN DATEDIFF(DAY, date_start, date_end) + 1 ELSE 0 END) AS annual_leave, 
  SUM(CASE WHEN absence_type_id = 2 THEN DATEDIFF(DAY, date_start, date_end) + 1 ELSE 0 END) AS annual_leave_collect,
  SUM(CASE WHEN absence_type_id = 3 THEN DATEDIFF(DAY, date_start, date_end) + 1 ELSE 0 END) AS maternity_leave,
  SUM(CASE WHEN absence_type_id = 4 THEN DATEDIFF(DAY, date_start, date_end) + 1 ELSE 0 END) AS ordination_leave,
  SUM(CASE WHEN absence_type_id = 5 THEN DATEDIFF(DAY, date_start, date_end) + 1 ELSE 0 END) AS ordination_leave_nopaid,
  SUM(CASE WHEN absence_type_id = 6 THEN DATEDIFF(DAY, date_start, date_end) + 1 ELSE 0 END) AS haj_leave,
  SUM(CASE WHEN absence_type_id = 7 THEN DATEDIFF(DAY, date_start, date_end) + 1 ELSE 0 END) AS haj_leave_nopaid,
  SUM(CASE WHEN absence_type_id = 8 THEN DATEDIFF(DAY, date_start, date_end) + 1 ELSE 0 END) AS training_leave_nopaid,
  SUM(CASE WHEN absence_type_id = 9 THEN DATEDIFF(DAY, date_start, date_end) + 1 ELSE 0 END) AS csr_leave,
  SUM(CASE WHEN absence_type_id = 11 THEN DATEDIFF(DAY, date_start, date_end) + 1 ELSE 0 END) AS military_service_leave,
  SUM(CASE WHEN absence_type_id = 12 THEN DATEDIFF(DAY, date_start, date_end) + 1 ELSE 0 END) AS other_leave,
  SUM(CASE WHEN absence_type_id = 13 THEN DATEDIFF(DAY, date_start, date_end) + 1 ELSE 0 END) AS other_leave_nopaid,
  SUM(CASE WHEN absence_type_id = 14 THEN DATEDIFF(DAY, date_start, date_end) + 1 ELSE 0 END) AS sick_leave
FROM
  absence_record
WHERE
  card_id = ?
  AND approve_status = ?
  AND YEAR(date_start) = ?
GROUP BY
  card_id",
    array($_SESSION['card_id'], "confirm", $time_stamp_year)
);
$rs_calculate_leave_days = sqlsrv_fetch_array($sql_calculate_leave_days, SQLSRV_FETCH_ASSOC);


$sql_calculate_work_sick_leave = sqlsrv_query(
    $conn,
    " SELECT card_id,
    SUM(CASE WHEN absence_type_id = 10 THEN DATEDIFF(hour, time_start, time_end) ELSE 0 END) AS work_sick_leave_hours
    FROM absence_record
WHERE
  card_id = ?
  AND approve_status = ?
  AND YEAR(input_timestamp) = ?
GROUP BY
  card_id",
    array($_SESSION['card_id'], "confirm", $time_stamp_year)
);
$rs_calculate_work_sick_leave = sqlsrv_fetch_array($sql_calculate_work_sick_leave, SQLSRV_FETCH_ASSOC);

// วันหยุดพักผ่อนประจำปี (วัน)
// วันลาพักร้อนประจำปีสะสม (วัน)
// ลาฝากครรภ์/ลาคลอด (วัน)
// ลาอุปสมบท (วัน)
// ลาอุปสมบท ไม่จ่าย (วัน)
// ลาฮัจญ์ (วัน)
// ลาฮัจญ์ ไม่จ่าย (วัน)
// ลาเพื่อการฝึกอบรม ไม่จ่าย (วัน)
// ลาเพื่่อทำประโยชน์ฯ (CSR) (วัน)
// ลาป่วยเนื่องจากการทำงาน (ชั่วโมง)
// ลาเพื่อรับราชการทหาร (วัน)
// ลาประเภทอื่น ๆ (วัน)
// ลาประเภทอื่น ๆ ไม่จ่าย (วัน)
// ลาป่วย (วัน)

//-------------------------------------------------------------------------------------------------------------------------------
// วันหยุดพักผ่อนประจำปี (วัน)

$annual_leave_collect = $rs_absence_quota['annual_leave_collect'];
$annual_leave_collect_us = isset($rs_calculate_leave_days['annual_leave_collect']) ? ($rs_calculate_leave_days['annual_leave_collect'] ?: '0') : '0';
$annual_leave_collect_balance = $annual_leave_collect - $annual_leave_collect_us;
$_SESSION["annual_leave_collect"] = $annual_leave_collect;
$_SESSION["annual_leave_collect_balance"] = $annual_leave_collect_balance;

// วันลาพักร้อน (วัน)
$annual_leave = $rs_absence_quota['annual_leave'];
$annual_leave_us = isset($rs_calculate_leave_days['annual_leave']) ? ($rs_calculate_leave_days['annual_leave'] ?: '0') : '0';
$annual_leave_balance = $annual_leave - $annual_leave_us;
$_SESSION["annual_leave"] = $annual_leave;
$_SESSION["annual_leave_balance"] = $annual_leave_balance;

// วันลาคลอด (วัน)
$maternity_leave = $rs_absence_quota['maternity_leave'];
$maternity_leave_us = isset($rs_calculate_leave_days['maternity_leave']) ? ($rs_calculate_leave_days['maternity_leave'] ?: '0') : '0';
$maternity_leave_balance = $maternity_leave - $maternity_leave_us;
$_SESSION["maternity_leave"] = $maternity_leave;
$_SESSION["maternity_leave_balance"] = $maternity_leave_balance;

// วันลาบวช (วัน)
$ordination_leave = $rs_absence_quota['ordination_leave'];
$ordination_leave_us = isset($rs_calculate_leave_days['ordination_leave']) ? ($rs_calculate_leave_days['ordination_leave'] ?: '0') : '0';
$ordination_leave_balance = $ordination_leave - $ordination_leave_us;
$_SESSION["ordination_leave"] = $ordination_leave;
$_SESSION["ordination_leave_balance"] = $ordination_leave_balance;

// วันลาบวช ไม่ได้รับค่าจ้าง (วัน)
$ordination_leave_nopaid = $rs_absence_quota['ordination_leave_nopaid'];
$ordination_leave_nopaid_us = isset($rs_calculate_leave_days['ordination_leave_nopaid']) ? ($rs_calculate_leave_days['ordination_leave_nopaid'] ?: '0') : '0';
$ordination_leave_nopaid_balance = $ordination_leave_nopaid - $ordination_leave_nopaid_us;
$_SESSION["ordination_leave_nopaid"] = $ordination_leave_nopaid;
$_SESSION["ordination_leave_nopaid_balance"] = $ordination_leave_nopaid_balance;

// วันลาฮัจจี (วัน)
$haj_leave = $rs_absence_quota['haj_leave'];
$haj_leave_us = isset($rs_calculate_leave_days['haj_leave']) ? ($rs_calculate_leave_days['haj_leave'] ?: '0') : '0';
$haj_leave_balance = $haj_leave - $haj_leave_us;
$_SESSION["haj_leave"] = $haj_leave;
$_SESSION["haj_leave_balance"] = $haj_leave_balance;

// วันลาฮัจจี ไม่ได้รับค่าจ้าง (วัน)
$haj_leave_nopaid = $rs_absence_quota['haj_leave_nopaid'];
$haj_leave_nopaid_us = isset($rs_calculate_leave_days['haj_leave_nopaid']) ? ($rs_calculate_leave_days['haj_leave_nopaid'] ?: '0') : '0';
$haj_leave_nopaid_balance = $haj_leave_nopaid - $haj_leave_nopaid_us;
$_SESSION["haj_leave_nopaid"] = $haj_leave_nopaid;
$_SESSION["haj_leave_nopaid_balance"] = $haj_leave_nopaid_balance;

// วันลาอบรม ไม่ได้รับค่าจ้าง (วัน)
$training_leave_nopaid = $rs_absence_quota['training_leave_nopaid'];
$training_leave_nopaid_us = isset($rs_calculate_leave_days['training_leave_nopaid']) ? ($rs_calculate_leave_days['training_leave_nopaid'] ?: '0') : '0';
$training_leave_nopaid_balance = $training_leave_nopaid - $training_leave_nopaid_us;
$_SESSION["training_leave_nopaid"] = $training_leave_nopaid;
$_SESSION["training_leave_nopaid_balance"] = $training_leave_nopaid_balance;

// วันลา CSR (วัน)
$csr_leave = $rs_absence_quota['csr_leave'];
$csr_leave_us = isset($rs_calculate_leave_days['csr_leave']) ? ($rs_calculate_leave_days['csr_leave'] ?: '0') : '0';
$csr_leave_balance = $csr_leave - $csr_leave_us;
$_SESSION["csr_leave"] = $csr_leave;
$_SESSION["csr_leave_balance"] = $csr_leave_balance;

// วันลาป่วยเกี่ยวกับการทำงาน (ชั่วโมง)
$work_sick_leave = $rs_absence_quota['work_sick_leave'];
$work_sick_leave_us = isset($rs_calculate_work_sick_leave['work_sick_leave_hours']) ? ($rs_calculate_work_sick_leave['work_sick_leave_hours'] ?: '0') : '0';
$work_sick_leave_balance = $work_sick_leave - $work_sick_leave_us;
$_SESSION["work_sick_leave"] = $work_sick_leave;
$_SESSION["work_sick_leave_balance"] = $work_sick_leave_balance;

// วันลาทหาร (วัน)
$military_service_leave = $rs_absence_quota['military_service_leave'];
$military_service_leave_us = isset($rs_calculate_leave_days['military_service_leave']) ? ($rs_calculate_leave_days['military_service_leave'] ?: '0') : '0';
$military_service_leave_balance = $military_service_leave - $military_service_leave_us;
$_SESSION["military_service_leave"] = $military_service_leave;
$_SESSION["military_service_leave_balance"] = $military_service_leave_balance;

// วันลาอื่น ๆ (วัน)
$other_leave = $rs_absence_quota['other_leave'];
$other_leave_us = isset($rs_calculate_leave_days['other_leave']) ? ($rs_calculate_leave_days['other_leave'] ?: '0') : '0';
$other_leave_balance = $other_leave - $other_leave_us;
$_SESSION["other_leave"] = $other_leave;
$_SESSION["other_leave_balance"] = $other_leave_balance;

// วันลาอื่น ๆ ไม่ได้รับค่าจ้าง (วัน)
$other_leave_nopaid = $rs_absence_quota['other_leave_nopaid'];
$other_leave_nopaid_us = isset($rs_calculate_leave_days['other_leave_nopaid']) ? ($rs_calculate_leave_days['other_leave_nopaid'] ?: '0') : '0';
$other_leave_nopaid_balance = $other_leave_nopaid - $other_leave_nopaid_us;
$_SESSION["other_leave_nopaid"] = $other_leave_nopaid;
$_SESSION["other_leave_nopaid_balance"] = $other_leave_nopaid_balance;

// วันลาป่วย (วัน)
$sick_leave = $rs_absence_quota['sick_leave'];
$sick_leave_us = isset($rs_calculate_leave_days['sick_leave']) ? ($rs_calculate_leave_days['sick_leave'] ?: '0') : '0';
$sick_leave_balance = $sick_leave - $sick_leave_us;
$_SESSION["sick_leave"] = $sick_leave;
$_SESSION["sick_leave_balance"] = $sick_leave_balance;

?>

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
                                        <h2>สิทธิวันลาคงเหลือ : View Leave Balance</h2>
                                    </div>
                                    <nav aria-label="breadcrumb" role="navigation">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item">
                                                <a style="pointer-events:none;cursor:default;">การลา</a>
                                            </li>
                                            <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                                สิทธิวันลาคงเหลือ
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
                                        <div class="button-add">
                                            <a href="leave-request-employee.php">
                                                <i class="fa-solid fa-circle-plus" style="color: #ffffff;"></i>
                                                ทำรายการลา
                                            </a>
                                        </div>
                                    </div>

                                    <div class="desktop-table">
                                        <table class="data-table table stripe hover nowrap">
                                            <thead>
                                                <tr>
                                                    <th>วันลา</th>
                                                    <th>สิทธิลาทั้งหมด</th>
                                                    <th>ใช้ไป</th>
                                                    <th>คงเหลือ</th>
                                                    <th>วันเริ่มต้น</th>
                                                    <th>วันสิ้นสุด</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>ลาพักร้อนประจำปี (วัน)</td>
                                                    <td>
                                                        <?= $annual_leave ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $annual_leave_us ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $annual_leave_balance ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $date_first ?>
                                                    </td>
                                                    <td>
                                                        <?= $date_last ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>ลาพักร้อนประจำปีสะสม (วัน)</td>
                                                    <td>
                                                        <?= $annual_leave_collect ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $annual_leave_collect_us ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $annual_leave_collect_balance ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $date_first ?>
                                                    </td>
                                                    <td>
                                                        <?= $date_last ?>
                                                    </td>
                                                </tr>
                                                <?php if ($rs_employee["gender"] == "หญิง") : ?>
                                                    <tr>
                                                        <td>ลาคลอด (วัน)</td>
                                                        <td>
                                                            <?= $maternity_leave ?> วัน
                                                        </td>
                                                        <td>
                                                            <?= $maternity_leave_us ?> วัน
                                                        </td>
                                                        <td>
                                                            <?= $maternity_leave_balance ?> วัน
                                                        </td>
                                                        <td>
                                                            <?= $date_first ?>
                                                        </td>
                                                        <td>
                                                            <?= $date_last ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                                <?php if ($rs_employee["gender"] == "ชาย") : ?>
                                                    <tr>
                                                        <td>ลาเพื่อรับราชการทหาร (วัน)</td>
                                                        <td>
                                                            <?= $military_service_leave ?> วัน
                                                        </td>
                                                        <td>
                                                            <?= $military_service_leave_us ?> วัน
                                                        </td>
                                                        <td>
                                                            <?= $military_service_leave_balance ?> วัน
                                                        </td>
                                                        <td>
                                                            <?= $date_first ?>
                                                        </td>
                                                        <td>
                                                            <?= $date_last ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>ลาอุปสมบท (วัน)</td>
                                                        <td>
                                                            <?= $ordination_leave ?> วัน
                                                        </td>
                                                        <td>
                                                            <?= $ordination_leave_us ?> วัน
                                                        </td>
                                                        <td>
                                                            <?= $ordination_leave_balance ?> วัน
                                                        </td>
                                                        <td>
                                                            <?= $date_first ?>
                                                        </td>
                                                        <td>
                                                            <?= $date_last ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>ลาอุปสมบทไม่จ่าย (วัน)</td>
                                                        <td>
                                                            <?= $ordination_leave_nopaid ?> วัน
                                                        </td>
                                                        <td>
                                                            <?= $ordination_leave_nopaid_us ?> วัน
                                                        </td>
                                                        <td>
                                                            <?= $ordination_leave_nopaid_balance ?> วัน
                                                        </td>
                                                        <td>
                                                            <?= $date_first ?>
                                                        </td>
                                                        <td>
                                                            <?= $date_last ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                                <tr>
                                                    <td>ลาฮัจญ์ (วัน)</td>
                                                    <td>
                                                        <?= $haj_leave ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $haj_leave_us ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $haj_leave_balance ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $date_first ?>
                                                    </td>
                                                    <td>
                                                        <?= $date_last ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>ลาฮัจญ์ไม่จ่าย (วัน)</td>
                                                    <td>
                                                        <?= $haj_leave_nopaid ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $haj_leave_nopaid_us ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $haj_leave_nopaid_balance ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $date_first ?>
                                                    </td>
                                                    <td>
                                                        <?= $date_last ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>ลาเพื่อการฝึกอบรมไม่จ่าย (วัน)</td>
                                                    <td>
                                                        <?= $training_leave_nopaid ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $training_leave_nopaid_us ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $training_leave_nopaid_balance ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $date_first ?>
                                                    </td>
                                                    <td>
                                                        <?= $date_last ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>ลาเพื่อทำประโยชน์ (วัน)</td>
                                                    <td>
                                                        <?= $csr_leave ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $csr_leave_us ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $csr_leave_balance ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $date_first ?>
                                                    </td>
                                                    <td>
                                                        <?= $date_last ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>ลาป่วย (วัน)</td>
                                                    <td>
                                                        <?= $sick_leave ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $sick_leave_us ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $sick_leave_balance ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $date_first ?>
                                                    </td>
                                                    <td>
                                                        <?= $date_last ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>ลาป่วยเนื่องจากการทำงาน (ชั่วโมง)</td>
                                                    <td>
                                                        <?= $work_sick_leave ?> ชั่วโมง
                                                    </td>
                                                    <td>
                                                        <?= $work_sick_leave_us ?> ชั่วโมง
                                                    </td>
                                                    <td>
                                                        <?= $work_sick_leave_balance ?> ชั่วโมง
                                                    </td>
                                                    <td>
                                                        <?= $date_first ?>
                                                    </td>
                                                    <td>
                                                        <?= $date_last ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>ลาอื่น ๆ (วัน)</td>
                                                    <td>
                                                        <?= $other_leave ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $other_leave_us ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $other_leave_balance ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $date_first ?>
                                                    </td>
                                                    <td>
                                                        <?= $date_last ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>ลาอื่น ๆ ไม่จ่าย (วัน)</td>
                                                    <td>
                                                        <?= $other_leave_nopaid ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $other_leave_nopaid_us ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $other_leave_nopaid_balance ?> วัน
                                                    </td>
                                                    <td>
                                                        <?= $date_first ?>
                                                    </td>
                                                    <td>
                                                        <?= $date_last ?>
                                                    </td>
                                                </tr>
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
                    <span>สิทธิ์วันลา</span>
                </div>
            </div>

            <div class="button-5">
                <div class="step">
                    <div><a href="leave-request-admin.php"><img src="../IMG/more0.png" alt=""></a></div>
                    <span>ทำรายการลา</span>
                </div>
                <div class="step">
                    <div><a href="leave-history-admin.php"><img src="../IMG/history.png" alt=""></a></div>
                    <span>ประวัติการลา</span>
                </div>
                <div class="step">
                    <div><a href="leave-team-admin.php"><img src="../IMG/Group1.png" alt=""></a></div>
                    <span>ปฏิทินลาทีม</span>
                </div>
                <div class="step">
                    <div><a href="leave-set-rights-admin.php"><img src="../IMG/police-badge 1.png" alt=""></a></div>
                    <span>กำหนดสิทธิลา</span>
                </div>

                <!-- <div class="step">
                    <div><a href="leave-approve-waiting-admin.php"><img src="../IMG/ask 1.png" alt=""></a></div>
                    <span>คำขอการลา</span>
                </div> -->
            </div>

            <div class="break">

                <div class="box">
                    <div class="topic-text">
                        <span>วันหยุดพักร้อนประจำปี </span>
                        <hr>
                    </div>

                    <div class="dataDate">
                        <div class="imgDate">
                            <img src="../IMG/leave.png">
                        </div>

                        <div class="boxLeft">
                            <div><span>สิทธิลาทั้งหมด </span></div>
                            <div><span>ใช้ไป </span></div>
                            <div><span>คงเหลือ </span></div>
                            <div class="div-4">
                                <span>วันเริ่มต้น &nbsp;</span>
                                <p>
                                    <?= $date_first ?>
                                </p>
                            </div>
                        </div>

                        <div class="boxRight">
                            <div class="div-1">
                                <p>
                                    <?= $annual_leave ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-2">
                                <p>
                                    <?= $annual_leave_us ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-3">

                                <p>
                                    <?= $annual_leave_balance ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-4">
                                <span>วันสิ้นสุด &nbsp;</span>
                                <p>
                                    <?= $date_last ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="topic-text">
                        <span>วันหยุดพักร้อนประจำปีสะสม</span>
                        <hr>
                    </div>

                    <div class="dataDate">
                        <div class="imgDate">
                            <img src="../IMG/leaveSum.png">
                        </div>

                        <div class="boxLeft">
                            <div><span>สิทธิ์ลาทั้งหมด </span></div>
                            <div><span>ใช้ไป </span></div>
                            <div><span>คงเหลือ </span></div>
                            <div class="div-4">
                                <span>วันเริ่มต้น &nbsp;</span>
                                <p>
                                    <?= $date_first ?>
                                </p>
                            </div>
                        </div>

                        <div class="boxRight">
                            <div class="div-1">
                                <p>
                                    <?= $annual_leave_collect ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-2">
                                <p>
                                    <?= $annual_leave_collect_us ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-3">

                                <p>
                                    <?= $annual_leave_collect_balance ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-4">
                                <span>วันสิ้นสุด &nbsp;</span>
                                <p>
                                    <?= $date_last ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($rs_employee["gender"] == "หญิง") : ?>

                    <div class="box">
                        <div class="topic-text">
                            <span>ลาคลอด</span>
                            <hr>
                        </div>

                        <div class="dataDate">
                            <div class="imgDate">
                                <img src="../IMG/Pregnant.png">
                            </div>

                            <div class="boxLeft">
                                <div><span>สิทธิ์ลาทั้งหมด </span></div>
                                <div><span>ใช้ไป </span></div>
                                <div><span>คงเหลือ </span></div>
                                <div class="div-4">
                                    <span>วันเริ่มต้น &nbsp;</span>
                                    <p>
                                        <?= $date_first ?>
                                    </p>
                                </div>
                            </div>

                            <div class="boxRight">
                                <div class="div-1">
                                    <p>
                                        <?= $maternity_leave ?>
                                    </p>
                                    <span>วัน</span>
                                </div>
                                <div class="div-2">
                                    <p>
                                        <?= $maternity_leave_us ?>
                                    </p>
                                    <span>วัน</span>
                                </div>
                                <div class="div-3">

                                    <p>
                                        <?= $maternity_leave_balance ?>
                                    </p>
                                    <span>วัน</span>
                                </div>
                                <div class="div-4">
                                    <span>วันสิ้นสุด &nbsp;</span>
                                    <p>
                                        <?= $date_last ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($rs_employee["gender"] == "ชาย") : ?>
                    <div class="box">
                        <div class="topic-text">
                            <span>ลาอุปสมบท</span>
                            <hr>
                        </div>
                        <div class="dataDate">
                            <div class="imgDate">
                                <img src="../IMG/monk1.png">
                            </div>

                            <div class="boxLeft">
                                <div><span>สิทธิ์ลาทั้งหมด </span></div>
                                <div><span>ใช้ไป </span></div>
                                <div><span>คงเหลือ </span></div>
                                <div class="div-4">
                                    <span>วันเริ่มต้น &nbsp;</span>
                                    <p>
                                        <?= $date_first ?>
                                    </p>
                                </div>
                            </div>

                            <div class="boxRight">
                                <div class="div-1">
                                    <p>
                                        <?= $ordination_leave ?>
                                    </p>
                                    <span>วัน</span>
                                </div>
                                <div class="div-2">
                                    <p>
                                        <?= $ordination_leave_us ?>
                                    </p>
                                    <span>วัน</span>
                                </div>
                                <div class="div-3">

                                    <p>
                                        <?= $ordination_leave_balance ?>
                                    </p>
                                    <span>วัน</span>
                                </div>
                                <div class="div-4">
                                    <span>วันสิ้นสุด &nbsp;</span>
                                    <p>
                                        <?= $date_last ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <div class="topic-text">
                            <span>ลาอุปสมบท (ไม่จ่าย)</span>
                            <hr>
                        </div>

                        <div class="dataDate">
                            <div class="imgDate">
                                <img src="../IMG/monk2.png">
                            </div>

                            <div class="boxLeft">
                                <div><span>สิทธิ์ลาทั้งหมด </span></div>
                                <div><span>ใช้ไป </span></div>
                                <div><span>คงเหลือ </span></div>
                                <div class="div-4">
                                    <span>วันเริ่มต้น &nbsp;</span>
                                    <p>
                                        <?= $date_first ?>
                                    </p>
                                </div>
                            </div>

                            <div class="boxRight">
                                <div class="div-1">
                                    <p>
                                        <?= $ordination_leave_nopaid ?>
                                    </p>
                                    <span>วัน</span>
                                </div>
                                <div class="div-2">
                                    <p>
                                        <?= $ordination_leave_nopaid_us ?>
                                    </p>
                                    <span>วัน</span>
                                </div>
                                <div class="div-3">

                                    <p>
                                        <?= $ordination_leave_nopaid_balance ?>
                                    </p>
                                    <span>วัน</span>
                                </div>
                                <div class="div-4">
                                    <span>วันสิ้นสุด &nbsp;</span>
                                    <p>
                                        <?= $date_last ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>

                <div class="box">
                    <div class="topic-text">
                        <span>ลาฮัจญ์</span>
                        <hr>
                    </div>

                    <div class="dataDate">
                        <div class="imgDate">
                            <img src="../IMG/huj.png">
                        </div>

                        <div class="boxLeft">
                            <div><span>สิทธิ์ลาทั้งหมด </span></div>
                            <div><span>ใช้ไป </span></div>
                            <div><span>คงเหลือ </span></div>
                            <div class="div-4">
                                <span>วันเริ่มต้น &nbsp;</span>
                                <p>
                                    <?= $date_first ?>
                                </p>
                            </div>
                        </div>

                        <div class="boxRight">
                            <div class="div-1">
                                <p>
                                    <?= $haj_leave ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-2">
                                <p>
                                    <?= $haj_leave_us ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-3">

                                <p>
                                    <?= $haj_leave_balance ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-4">
                                <span>วันสิ้นสุด &nbsp;</span>
                                <p>
                                    <?= $date_last ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="topic-text">
                        <span>ลาฮัจญ์ (ไม่จ่าย)</span>
                        <hr>
                    </div>

                    <div class="dataDate">
                        <div class="imgDate">
                            <img src="../IMG/huj1.png">
                        </div>

                        <div class="boxLeft">
                            <div><span>สิทธิ์ลาทั้งหมด </span></div>
                            <div><span>ใช้ไป </span></div>
                            <div><span>คงเหลือ </span></div>
                            <div class="div-4">
                                <span>วันเริ่มต้น &nbsp;</span>
                                <p>
                                    <?= $date_first ?>
                                </p>
                            </div>
                        </div>

                        <div class="boxRight">
                            <div class="div-1">
                                <p>
                                    <?= $haj_leave_nopaid ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-2">
                                <p>
                                    <?= $haj_leave_nopaid_us ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-3">

                                <p>
                                    <?= $haj_leave_nopaid_balance ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-4">
                                <span>วันสิ้นสุด &nbsp;</span>
                                <p>
                                    <?= $date_last ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="topic-text">
                        <span>ลาเพื่อการฝึกอบรม (ไม่จ่าย)</span>
                        <hr>
                    </div>

                    <div class="dataDate">
                        <div class="imgDate">
                            <img src="../IMG/training.png">
                        </div>

                        <div class="boxLeft">
                            <div><span>สิทธิ์ลาทั้งหมด </span></div>
                            <div><span>ใช้ไป </span></div>
                            <div><span>คงเหลือ </span></div>
                            <div class="div-4">
                                <span>วันเริ่มต้น &nbsp;</span>
                                <p>
                                    <?= $date_first ?>
                                </p>
                            </div>
                        </div>

                        <div class="boxRight">
                            <div class="div-1">
                                <p>
                                    <?= $training_leave_nopaid ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-2">
                                <p>
                                    <?= $training_leave_nopaid_us ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-3">

                                <p>
                                    <?= $training_leave_nopaid_balance ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-4">
                                <span>วันสิ้นสุด &nbsp;</span>
                                <p>
                                    <?= $date_last ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="topic-text">
                        <span>ลาเพื่อทำประโยชน์</span>
                        <hr>
                    </div>

                    <div class="dataDate">
                        <div class="imgDate">
                            <img src="../IMG/benefit.png">
                        </div>

                        <div class="boxLeft">
                            <div><span>สิทธิ์ลาทั้งหมด </span></div>
                            <div><span>ใช้ไป </span></div>
                            <div><span>คงเหลือ </span></div>
                            <div class="div-4">
                                <span>วันเริ่มต้น &nbsp;</span>
                                <p>
                                    <?= $date_first ?>
                                </p>
                            </div>
                        </div>

                        <div class="boxRight">
                            <div class="div-1">
                                <p>
                                    <?= $csr_leave ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-2">
                                <p>
                                    <?= $csr_leave_us ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-3">

                                <p>
                                    <?= $csr_leave_balance ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-4">
                                <span>วันสิ้นสุด &nbsp;</span>
                                <p>
                                    <?= $date_last ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="topic-text">
                        <span>ลาป่วยเนื่องจากการทำงาน</span>
                        <hr>
                    </div>

                    <div class="dataDate">
                        <div class="imgDate">
                            <img src="../IMG/sick-work.png">
                        </div>

                        <div class="boxLeft">
                            <div><span>สิทธิ์ลาทั้งหมด </span></div>
                            <div><span>ใช้ไป </span></div>
                            <div><span>คงเหลือ </span></div>
                            <div class="div-4">
                                <span>วันเริ่มต้น &nbsp;</span>
                                <p>
                                    <?= $date_first ?>
                                </p>
                            </div>
                        </div>

                        <div class="boxRight">
                            <div class="div-1">
                                <p>
                                    <?= $work_sick_leave ?>
                                </p>
                                <span>ชั่วโมง</span>
                            </div>
                            <div class="div-2">
                                <p>
                                    <?= $work_sick_leave_us ?>
                                </p>
                                <span>ชั่วโมง</span>
                            </div>
                            <div class="div-3">

                                <p>
                                    <?= $work_sick_leave_balance ?>
                                </p>
                                <span>ชั่วโมง</span>
                            </div>
                            <div class="div-4">
                                <span>วันสิ้นสุด &nbsp;</span>
                                <p>
                                    <?= $date_last ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($rs_employee["gender"] == "ชาย") : ?>
                    <div class="box">
                        <div class="topic-text">
                            <span>ลาเพื่อรับราชการทหาร</span>
                            <hr>
                        </div>

                        <div class="dataDate">
                            <div class="imgDate">
                                <img src="../IMG/soldier.png">
                            </div>

                            <div class="boxLeft">
                                <div><span>สิทธิ์ลาทั้งหมด </span></div>
                                <div><span>ใช้ไป </span></div>
                                <div><span>คงเหลือ </span></div>
                                <div class="div-4">
                                    <span>วันเริ่มต้น &nbsp;</span>
                                    <p>
                                        <?= $date_first ?>
                                    </p>
                                </div>
                            </div>

                            <div class="boxRight">
                                <div class="div-1">
                                    <p>
                                        <?= $military_service_leave ?>
                                    </p>
                                    <span>วัน</span>
                                </div>
                                <div class="div-2">
                                    <p>
                                        <?= $military_service_leave_us ?>
                                    </p>
                                    <span>วัน</span>
                                </div>
                                <div class="div-3">

                                    <p>
                                        <?= $military_service_leave_balance ?>
                                    </p>
                                    <span>วัน</span>
                                </div>
                                <div class="div-4">
                                    <span>วันสิ้นสุด &nbsp;</span>
                                    <p>
                                        <?= $date_last ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="box">
                    <div class="topic-text">
                        <span>ลาอื่น ๆ </span>
                        <hr>
                    </div>

                    <div class="dataDate">
                        <div class="imgDate">
                            <img src="../IMG/another.png">
                        </div>

                        <div class="boxLeft">
                            <div><span>สิทธิ์ลาทั้งหมด </span></div>
                            <div><span>ใช้ไป </span></div>
                            <div><span>คงเหลือ </span></div>
                            <div class="div-4">
                                <span>วันเริ่มต้น &nbsp;</span>
                                <p>
                                    <?= $date_first ?>
                                </p>
                            </div>
                        </div>

                        <div class="boxRight">
                            <div class="div-1">
                                <p>
                                    <?= $other_leave ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-2">
                                <p>
                                    <?= $other_leave_us ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-3">

                                <p>
                                    <?= $other_leave_balance ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-4">
                                <span>วันสิ้นสุด &nbsp;</span>
                                <p>
                                    <?= $date_last ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="topic-text">
                        <span>ลาอื่น ๆ (ไม่จ่าย)</span>
                        <hr>
                    </div>

                    <div class="dataDate">
                        <div class="imgDate">
                            <img src="../IMG/another1.png">
                        </div>

                        <div class="boxLeft">
                            <div><span>สิทธิ์ลาทั้งหมด </span></div>
                            <div><span>ใช้ไป </span></div>
                            <div><span>คงเหลือ </span></div>
                            <div class="div-4">
                                <span>วันเริ่มต้น &nbsp;</span>
                                <p>
                                    <?= $date_first ?>
                                </p>
                            </div>
                        </div>

                        <div class="boxRight">
                            <div class="div-1">
                                <p>
                                    <?= $other_leave_nopaid ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-2">
                                <p>
                                    <?= $other_leave_nopaid_us ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-3">

                                <p>
                                    <?= $other_leave_nopaid_balance ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-4">
                                <span>วันสิ้นสุด &nbsp;</span>
                                <p>
                                    <?= $date_last ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="topic-text">
                        <span>ลาป่วย</span>
                        <hr>
                    </div>

                    <div class="dataDate">
                        <div class="imgDate">
                            <img src="../IMG/sick.png">
                        </div>

                        <div class="boxLeft">
                            <div><span>สิทธิ์ลาทั้งหมด </span></div>
                            <div><span>ใช้ไป </span></div>
                            <div><span>คงเหลือ </span></div>
                            <div class="div-4">
                                <span>วันเริ่มต้น &nbsp;</span>
                                <p>
                                    <?= $date_first ?>
                                </p>
                            </div>
                        </div>

                        <div class="boxRight">
                            <div class="div-1">
                                <p>
                                    <?= $sick_leave ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-2">
                                <p>
                                    <?= $sick_leave_us ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-3">

                                <p>
                                    <?= $sick_leave_balance ?>
                                </p>
                                <span>วัน</span>
                            </div>
                            <div class="div-4">
                                <span>วันสิ้นสุด &nbsp;</span>
                                <p>
                                    <?= $date_last ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</body>
<?php include('../includes/footer.php'); ?>

</html>