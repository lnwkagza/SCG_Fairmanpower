<?php
session_start();
session_regenerate_id(true);
//---------------------------------------------------------------------------------------
include("../database/connectdb.php");
include('../components-desktop/head/include/header.php');
// include("dbconnect.php");
// include("update_dayoff.php");
//---------------------------------------------------------------------------------------
?>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/head/day-off-detail-status-head.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/day-off-detail-status.css">

<?php

//---------------------------------------------------------------------------------------

// ทำความสะอาดและป้องกันข้อมูล
$_SESSION['card_id'] = isset($_SESSION['card_id']) ? $_SESSION['card_id'] : '';  // ตรวจสอบว่ามีค่าหรือไม่
$_SESSION['card_id'] = trim($_SESSION['card_id']);  // ลบช่องว่างที่อาจเกิดขึ้นทั้งด้านหน้าและด้านหลังของข้อมูล
$_SESSION['card_id'] = preg_replace('/[^a-zA-Z0-9]/', '', $_SESSION['card_id']);  // ลบทุกอักขระที่ไม่ใช่ a-z, A-Z, และ 0-9
$_SESSION['card_id'] = htmlspecialchars($_SESSION['card_id'], ENT_QUOTES, 'UTF-8');  // แปลงสัญลักษณ์ HTML เพื่อป้องกัน XSS

//---------------------------------------------------------------------------------------

// ทำความสะอาดและป้องกันข้อมูล
$id = isset($_GET['id']) ? $_GET['id'] : '';
$id = trim($id);
$id = preg_replace('/[^a-zA-Z0-9]/', '', $id);
$id = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');

//---------------------------------------------------------------------------------------

// if (!empty($_SESSION["card_id"])) {
// สร้าง query เพื่อดึงข้อมูลพนักงาน
$query = "SELECT *
    FROM day_off_request
    JOIN employee AS emp1 ON emp1.card_id = day_off_request.card_id
    JOIN employee AS approver ON approver.card_id = ?
    WHERE emp1.cost_center_payment_id = approver.cost_center_organization_id";
$params = array($_SESSION['card_id']);
$sql_card_id = sqlsrv_query($conn, $query, $params);

// if (sqlsrv_has_rows($sql_card_id)) {
$sql_day_off_request = sqlsrv_query($conn, "SELECT 
        day_off_request.edit_time AS date,
        day_off_request.edit_detail AS detail,
        approve_status,
        edit_format.day_off1 AS day_off_new_1,
        edit_format.day_off2 AS day_off_new_2,
        old_format.day_off1 AS day_off_old_1,
        old_format.day_off2 AS day_off_old_2,
        approver.prefix_thai AS approver_prefix_thai,
        approver.firstname_thai AS approver_firstname_thai,
        approver.lastname_thai AS approver_lastname_thai,
        inspector.prefix_thai AS inspector_prefix_thai,
        inspector.firstname_thai AS inspector_firstname_thai,
        inspector.lastname_thai AS inspector_lastname_thai,
        position.name_thai AS position_name_thai,
        employee.scg_employee_id AS employee_scg_employee_id,
        employee.prefix_thai AS employee_prefix_thai,
        employee.firstname_thai AS employee_firstname_thai,
        employee.lastname_thai AS employee_lastname_thai,
        employee.employee_image AS employee_employee_image,
        emp_position.name_thai AS emp_position_name_thai, 
        cost_center.cost_center_code AS cost_center_code,
        section.name_thai AS section_name_thai
        FROM day_off_request
        LEFT JOIN work_format AS edit_format ON day_off_request.edit_work_format_code = edit_format.work_format_code 
        LEFT JOIN work_format AS old_format ON day_off_request.old_work_format_code = old_format.work_format_code
        LEFT JOIN employee AS approver ON day_off_request.approver = approver.card_id
        LEFT JOIN  employee AS inspector ON day_off_request.inspector = inspector.card_id
        LEFT JOIN position_info ON day_off_request.card_id = position_info.card_id
        LEFT JOIN position ON position_info.position_id = position.position_id
        LEFT JOIN employee ON employee.card_id = day_off_request.card_id
        LEFT JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_payment_id
        LEFT JOIN section ON section.section_id = cost_center.section_id
        LEFT JOIN position_info AS emp_position_info ON emp_position_info.card_id = employee.card_id
        LEFT JOIN position AS emp_position ON emp_position.position_id = emp_position_info.position_id
        WHERE day_off_request.day_off_req_id = ?", array($id));
$row = sqlsrv_fetch_array($sql_day_off_request);

$dayMap = [
    'Sun' => 7,
    'Mon' => 1,
    'Tue' => 2,
    'Wed' => 3,
    'Thu' => 4,
    'Fri' => 5,
    'Sat' => 6,
];

$Months = array(
    1 => 'มกราคม',
    2 => 'กุมภาพันธ์',
    3 => 'มีนาคม',
    4 => 'เมษายน',
    5 => 'พฤษภาคม',
    6 => 'มิถุนายน',
    7 => 'กรกฎาคม',
    8 => 'สิงหาคม',
    9 => 'กันยายน',
    10 => 'ตุลาคม',
    11 => 'พฤศจิกายน',
    12 => 'ธันวาคม',
);

// Assuming $row['date'] is already a DateTime object
$date = $row['date'];
$yearBE = $date->format('Y') + 543;

// Create the Thai date format
$thaiDate = $date->format('d') . ' ' . $Months[(int)$date->format('m')] . ' ' . $yearBE;

$day_off_old1 = $dayMap[$row['day_off_new_1']];
$day_off_old2 = $dayMap[$row['day_off_new_2']];

$day_off_new1 = $dayMap[$row['day_off_old_1']];
$day_off_new2 = $dayMap[$row['day_off_old_2']];

function getThaiDay($dayNumber)
{
    $thaiDays = ['จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส', 'อา'];
    return $thaiDays[$dayNumber - 1];
}
//     } else {
//         // แจ้งเตือนถ้ายังไม่ได้ลงทะเบียน
//         echo '<script>
//         alert("คุณยังไม่ได้ลงทะเบียน");
//         window.location.href = "../index.html";
//         </script>';
//     }
// } else {
//     // แจ้งเตือนถ้ายังไม่ได้ลงทะเบียน
//     echo '<script>
//     alert("คุณยังไม่ได้ลงทะเบียน");
//     window.location.href = "../index.html";
//      </script>';
// }
?>
</head>

<body>
    <div class="desktop">
        <?php include('../components-desktop/head/include/sidebar.php'); ?>
        <?php include('../components-desktop/head/include/navbar.php'); ?>

        <div class="main-container">
            <div class="pd-ltr-20 xs-pd-20-10">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <h2>รายละเอียดคำขอเปลี่ยนวันหยุด</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor:default;">วันหยุด</a>
                                        </li>
                                        <li class=" breadcrumb-item">
                                            <a href="day-off-change-history-head.php">ประวัติการขอเปลี่ยนวันหยุด</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            รายละเอียดคำขอเปลี่ยนวันหยุด
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class=" row">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10">
                                <div class="employee-image">
                                    <img src="<?php echo (!empty($row['employee_employee_image'])) ? '../../admin/uploads_img/' . $row['employee_employee_image'] : '../IMG/user.png'; ?>"
                                        alt="">
                                </div>
                                <div class="employee-info">
                                    <label>รหัสพนักงาน: <?= $row['employee_scg_employee_id'] ?></label>
                                    <label>ชื่อ-สกุล:
                                        <?= $row['employee_prefix_thai'] . $row['employee_firstname_thai'] . " " . $row['employee_lastname_thai'] ?></label>
                                    <label>ตำแหน่ง: <?= $row['emp_position_name_thai'] ?></label>
                                    <label>Cost Center: <?= $row['cost_center_code'] ?></label>
                                    <label>หน่วยงาน: <?= $row['section_name_thai'] ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="wizard-content">
                                    <form>
                                        <section>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="old-dayoff text-center">
                                                            <label>วันหยุดประจำสัปดาห์ (เก่า)</label>
                                                            <div class="display-day-week">
                                                                <?php
                                                                // Loop through the days of the week
                                                                for ($i = 1; $i <= 7; $i++) {
                                                                    // Check if the current day is a day off
                                                                    $isDayOff = ($i == $day_off_old1 || $i == $day_off_old2);

                                                                    // Add the appropriate CSS class based on whether it's a day off or not
                                                                    $cssClass = $isDayOff ? 'dayoff' : '';

                                                                    // Output the day with the corresponding CSS class
                                                                    echo '<label class="' . $cssClass . '">' . getThaiDay($i) . '</label>';
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="new-dayoff text-center">
                                                            <label>วันหยุดประจำสัปดาห์ (ใหม่)</label>
                                                            <div class="display-day-week">
                                                                <?php
                                                                // Loop through the days of the week
                                                                for ($i = 1; $i <= 7; $i++) {
                                                                    // Check if the current day is a day off
                                                                    $isDayOff = ($i == $day_off_new1 || $i == $day_off_new2);

                                                                    // Add the appropriate CSS class based on whether it's a day off or not
                                                                    $cssClass = $isDayOff ? 'dayoff' : '';

                                                                    // Output the day with the corresponding CSS class
                                                                    echo '<label class="' . $cssClass . '">' . getThaiDay($i) . '</label>';
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="date text-center">
                                                            <label>วันที่เริ่มเปลี่ยน</label>
                                                            <input class="form-control" type="text"
                                                                value="<?php echo $thaiDate; ?>" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="detail text-center">
                                                            <label>เหตุผลที่ขอเปลี่ยน</label>
                                                            <input class="form-control" type="text"
                                                                value="<?php echo isset($row['detail']) ? $row['detail'] : '-'; ?>"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="inspector text-center">
                                                            <label>ผู้ตรวจสอบ</label>
                                                            <input class="form-control" type="text" value="<?php
                                                                                                            if (!empty($row['inspector_firstname_thai']) && !empty($row['inspector_lastname_thai'])) {
                                                                                                                echo  $row['inspector_prefix_thai'] . $row['inspector_firstname_thai'] . " " . $row['inspector_lastname_thai'];
                                                                                                            } else {
                                                                                                                echo '-';
                                                                                                            }
                                                                                                            ?>"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12 ">
                                                    <div class="form-group">
                                                        <div class="head text-center">
                                                            <label>หัวหน้า</label>
                                                            <input class="form-control" type="text"
                                                                value="<?php echo $row['approver_prefix_thai'] . $row['approver_firstname_thai'] . " " . $row['approver_lastname_thai']; ?>"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 text-center">
                                                <div class="dropdown">
                                                    <?php if ($row['approve_status'] === 'confirm') : ?>
                                                    <div class="btn btn-primary status">อนุมัติแล้ว</div>
                                                    <?php elseif ($row['approve_status'] === 'waiting') : ?>
                                                    <div class="btn btn-warning status">รออนุมัติ</div>

                                                    <div class="col-sm-12 text-center">
                                                        <div class="dropdown">
                                                            <div class="btn btn-success status">อนุมัติ</div>
                                                            <div class="btn btn-danger status">ปฏิเสธ</div>
                                                        </div>
                                                    </div>

                                                    <?php else : ?>
                                                    <div class="btn btn-danger status">ปฏิเสธ</div>
                                                    <?php endif ?>
                                                </div>
                                            </div>
                                        </section>
                                    </form>
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
                <span>คำขอเปลี่ยนวันหยุด</span>
            </div>
        </div>

        <div class="container">
            <div class="display-name-em">
                <img src="<?php echo (!empty($row['employee_employee_image'])) ? '../../admin/uploads_img/' . $row['employee_employee_image'] : '../IMG/user.png'; ?>"
                    alt="" style="border-radius:50%;">
                <span><?php echo $row['employee_scg_employee_id'] . " " . $row['employee_prefix_thai'] . $row['employee_firstname_thai'] . " " . $row['employee_lastname_thai']; ?></span>
            </div>
            <div class="container-detail-employee">
                <div class="display-dayoff-old">
                    <span class="dayTopic">วันหยุดประจำสัปดาห์</span>
                    <div class="display-day-week">
                        <?php
                        // Loop through the days of the week
                        for ($i = 1; $i <= 7; $i++) {
                            // Check if the current day is a day off
                            $isDayOff = ($i == $day_off_old1 || $i == $day_off_old2);

                            // Add the appropriate CSS class based on whether it's a day off or not
                            $cssClass = $isDayOff ? 'dayoff' : '';

                            // Output the day with the corresponding CSS class
                            echo '<span class="' . $cssClass . '">' . getThaiDay($i) . '</span>';
                        }
                        ?>
                    </div>
                </div>
                <div class="display-dayoff-new">
                    <span class="dayTopic">วันหยุดประจำสัปดาห์</span>
                    <div class="display-day-week">
                        <?php
                        // Loop through the days of the week
                        for ($i = 1; $i <= 7; $i++) {
                            // Check if the current day is a day off
                            $isDayOff = ($i == $day_off_new1 || $i == $day_off_new2);

                            // Add the appropriate CSS class based on whether it's a day off or not
                            $cssClass = $isDayOff ? 'dayoff' : '';

                            // Output the day with the corresponding CSS class
                            echo '<span class="' . $cssClass . '">' . getThaiDay($i) . '</span>';
                        }
                        ?>
                    </div>
                </div>
                <div class="reason-change">
                    <span class="dayTopic">วันที่เริ่มเปลี่ยน : <?php echo $thaiDate; ?></span>
                    <span class="dayTopic">เหตุผลการขอเปลี่ยนวันหยุด</span>
                    <span class="reason">
                        <?php
                        echo isset($row['detail']) && isset($row['another_key'])
                            ? $row['detail'] . " " . $row['another_key']
                            : '-';
                        ?>
                    </span>
                </div>
                <div class="display-approve">
                    <span>ชื่อผู้ตรวจสอบ :
                        <?php
                        if (!empty($row['inspector_firstname_thai']) && !empty($row['inspector_lastname_thai'])) {
                            echo  $row['inspector_prefix_thai'] . $row['inspector_firstname_thai'] . " " . $row['inspector_lastname_thai'];
                        } else {
                            echo '-';
                        }
                        ?>
                    </span>
                    <span>ชื่อผู้อนุมัติ :
                        <?php echo $row['approver_prefix_thai'] . $row['approver_firstname_thai'] . " " . $row['approver_lastname_thai']; ?>
                    </span>
                </div>
                <div class="display-status">
                    <?php if ($row['approve_status'] === 'confirm') : ?>
                    <span style="color:green;">อนุมัติ</span>
                    <?php elseif ($row['approve_status'] === 'waiting') : ?>
                    <span style="color:orange;">รออนุมัติ</span>
                    <div class="display-status">
                        <div class="btn btn-success status">อนุมัติ</div>
                        <div class="btn btn-danger status">ปฏิเสธ</div>
                    </div>
                    <?php else : ?>
                    <span style="color:red;">ปฏิเสธ</span>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include('../includes/footer.php') ?>