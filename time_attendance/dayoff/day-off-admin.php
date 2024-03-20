<?php
//---------------------------------------------------------------------------------------
session_start();
session_regenerate_id(true);
//---------------------------------------------------------------------------------------
include("../database/connectdb.php");
include('../components-desktop/admin/include/header.php');
// include("dbconnect.php");
// include("update_dayoff.php");
?>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/admin/day-off-admin.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/day-off.css">

<!-- datatable -->
<link href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.bootstrap5.min.js"></script>

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="../assets/script/loader-normal.js"></script>
<?php

// ทำความสะอาดและป้องกันข้อมูล
$_SESSION['card_id'] = isset($_SESSION['card_id']) ? $_SESSION['card_id'] : '';  // ตรวจสอบว่ามีค่าหรือไม่
$_SESSION['card_id'] = trim($_SESSION['card_id']);  // ลบช่องว่างที่อาจเกิดขึ้นทั้งด้านหน้าและด้านหลังของข้อมูล
$_SESSION['card_id'] = preg_replace('/[^a-zA-Z0-9]/', '', $_SESSION['card_id']);  // ลบทุกอักขระที่ไม่ใช่ a-z, A-Z, และ 0-9
$_SESSION['card_id'] = htmlspecialchars($_SESSION['card_id'], ENT_QUOTES, 'UTF-8');  // แปลงสัญลักษณ์ HTML เพื่อป้องกัน XSS

$selectedMonth = isset($_POST['month']) ? $_POST['month'] : date('m');
$selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');
$costID = isset($_POST['cost']) ? $_POST['cost'] :null;

//---------------------------------------------------------------------------------------

if (isset($_SESSION['card_id'])) {

    // update_dayoff($selectedYear, $selectedMonth,$id);
    // update_dayoff_team($selectedYear, $selectedMonth, $id);

    $time_stamp = date("y-m");

    $select_dayoff_set = "SELECT work_format.day_off1 AS day_off1,
                    work_format.day_off2 AS day_off2
                    FROM employee 
                    INNER JOIN work_format ON employee.work_format_code = work_format.work_format_code
                    INNER JOIN sub_team ON employee.sub_team_id = sub_team.sub_team_id
                    WHERE employee.card_id = ?";
    $dayoff_set_stmt = sqlsrv_prepare($conn, $select_dayoff_set, array(&$_SESSION['card_id']));

    //--------------------------------------------------------------------------------------------------

    function getThaiDay($dayNumber)
    {
        $thaiDays = ['จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส', 'อา'];
        return $thaiDays[$dayNumber - 1];
    }

    $thaiDayAbbreviations = array(
        'Monday' => 'จันทร์',
        'Tuesday' => 'อังคาร',
        'Wednesday' => 'พุธ',
        'Thursday' => 'พฤหัสบดี',
        'Friday' => 'ศุกร์',
        'Saturday' => 'เสาร์',
        'Sunday' => 'อาทิตย์',
    );

    $englishMonths = array(
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
    );
    $thaiYear = $selectedYear + 543;
    $thaiMonthYear = strtr($selectedMonth, $englishMonths) . " " . $thaiYear;

    $sql = "SELECT * FROM holiday WHERE MONTH(date) = ? AND YEAR(date) = ? ORDER BY date ASC";
    $params = array($selectedMonth, $selectedYear);
    $holiday = sqlsrv_query($conn, $sql, $params);
    $holidaydata = array();

    while ($row = sqlsrv_fetch_array($holiday, SQLSRV_FETCH_ASSOC)) {
        $holidaydata[] = $row;
    }

    $sql = "SELECT * FROM transaction_work WHERE MONTH(date) = ? AND YEAR(date) = ? AND card_id = ? ORDER BY date ASC";
    $params = array($selectedMonth, $selectedYear, $_SESSION['card_id']);
    $transaction_work_result = sqlsrv_query($conn, $sql, $params);
    $transaction_work = array();

    while ($row = sqlsrv_fetch_array($transaction_work_result, SQLSRV_FETCH_ASSOC)) {
        $transaction_work[] = $row;
    }

    // First query
    $sql = "
    SELECT card_id,firstname_thai,lastname_thai
    FROM employee
    WHERE cost_center_organization_id = ?";
    $params = array($costID);
    $day_off_Team_section_result = sqlsrv_query($conn, $sql, $params);
    $day_off_Team_section = array();

    while ($row = sqlsrv_fetch_array($day_off_Team_section_result, SQLSRV_FETCH_ASSOC)) {
    $day_off_Team_section[] = $row;
    }

    // Second query
    $sql = "SELECT employee.card_id, section.name_thai, date, is_day_off
    FROM employee
    INNER JOIN section ON employee.cost_center_organization_id = section.section_id
    INNER JOIN transaction_work ON employee.card_id = transaction_work.card_id COLLATE Thai_100_CS_AI
    WHERE MONTH(date) = ? AND YEAR(date) = ? AND cost_center_organization_id = ? ORDER BY date ASC";
    $params = array($selectedMonth, $selectedYear,$costID);
    $day_off_Team_info_result = sqlsrv_query($conn, $sql, $params);
    $day_off_Team_info = array();

    while ($row = sqlsrv_fetch_array($day_off_Team_info_result, SQLSRV_FETCH_ASSOC)) {
        $day_off_Team_info[] = $row;
    }

    $sql = "SELECT firstname_thai,lastname_thai,edit_time,day_off1,day_off2,edit_detail
    FROM day_off_request 
    INNER JOIN employee ON day_off_request.card_id = employee.card_id
    INNER JOIN work_format ON day_off_request.edit_work_format_code = work_format.work_format_code
    WHERE employee.cost_center_organization_id IN (SELECT cost_center_organization_id FROM employee WHERE card_id = ?) AND day_off_request.approve_status = ? ORDER BY day_off_request.edit_time ASC";

    $params = array($_SESSION['card_id'], 'waiting');
    $day_off_request = sqlsrv_query($conn, $sql, $params);
    $day_off_requestdata = array();

    while ($row = sqlsrv_fetch_array($day_off_request, SQLSRV_FETCH_ASSOC)) {
        $day_off_requestdata[] = $row;
    }

    $cost_center_code = "SELECT employee.card_id,
    cost_center.cost_center_id AS cost_center_id,
    section.name_thai AS name_thai,
    cost_center_code,firstname_thai,lastname_thai
    FROM cost_center_head
    INNER JOIN employee ON cost_center_head.card_id = employee.card_id
    INNER JOIN cost_center ON cost_center_head.cost_center_id = cost_center.cost_center_id
    INNER JOIN section ON cost_center.section_id = section.section_id";
    $cost_center_code_result = sqlsrv_query($conn, $cost_center_code);
    $cost_center_code_data = array();
    while ($row = sqlsrv_fetch_array($cost_center_code_result, SQLSRV_FETCH_ASSOC)) {
        $cost_center_code_data[] = $row;
    }

    $sql = "SELECT firstname_thai,lastname_thai,edit_time,day_off1,day_off2,edit_detail
    FROM day_off_request 
    INNER JOIN employee ON day_off_request.card_id = employee.card_id
    INNER JOIN work_format ON day_off_request.edit_work_format_code = work_format.work_format_code
    WHERE employee.cost_center_organization_id IN (SELECT cost_center_organization_id FROM employee WHERE card_id = ?) AND day_off_request.approve_status = ? ORDER BY day_off_request.edit_time ASC";

    $params = array($_SESSION['card_id'], 'waiting');
    $day_off_request = sqlsrv_query($conn, $sql, $params);
    $day_off_requestdata = array();

    while ($row = sqlsrv_fetch_array($day_off_request, SQLSRV_FETCH_ASSOC)) {
        $day_off_requestdata[] = $row;
    }

} else {
    // แจ้งเตือนถ้ายังไม่ได้ลงทะเบียน
    echo '<script>
    alert("คุณยังไม่ได้ลงทะเบียน");
    window.location.href = "../index.html";
     </script>';
}
?>

<script>

$(document).ready(function() {
    //mange-team
    new DataTable('#example1', {
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

    new DataTable('#table3', {
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

    $(document).ready(function() {
        $(".desktop-table").on("scroll", function() {
            // รับค่าตำแหน่งการเลื่อนขององค์ประกอบปัจจุบัน
            var currentScrollTop = $(this).scrollTop();

            // ซิงโครไนส์การเลื่อนขององค์ประกอบอื่น ๆ ให้ตรงกับตำแหน่งการเลื่อนขององค์ประกอบปัจจุบัน
            $(".desktop-table").not(this).scrollTop(currentScrollTop);
        });

        $('.js-example-basic-single').select2();
    });

    function validateForm() {
        var month = document.getElementById('month').value;
        var year = document.getElementById('year').value;

        if (month === 'none' || year === 'none') {
            alert('โปรดเลือกทุกฟิลด์ในฟอร์ม');
            return false;
        }

        return true;
    }

    function search_period() {
        var month = document.getElementById('desktop-month').value;
        var year = document.getElementById('desktop-year').value;

        if (month === 'none' || year === 'none') {
            alert('โปรดเลือกทุกฟิลด์ในฟอร์ม');
            return false;
        }

        return true;
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
                        <div class="page-header">
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="title">
                                        <h2>ตารางวันหยุดทีม</h2>
                                    </div>
                                    <nav aria-label="breadcrumb" role="navigation">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item">
                                                <a style="pointer-events:none;cursor: default;">วันหยุด</a>
                                            </li>
                                            <li class=" breadcrumb-item active" aria-current="page" style="cursor:default;">
                                                ตารางวันหยุดทีม
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 col-md-6 col-sm-12 mb-30" id="box1">
                                <div class="card-box pd-30 pt-10 height-100-p" id="card-box-1">
                                    <div class="pt-3 pb-3">
                                        <div class="row1">
                                            <section>
                                                <h5>โปรดเลือกช่วงเวลาที่ต้องการ</h5>
                                                <hr>
                                                <form class="select-time" method="post" onsubmit="return search_period()">
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>ปี:</label>
                                                                <select name="year" class="form-control custom-select" id="desktop-year" required>
                                                                    <option value="none" <?php if (isset($_POST['year']) && $_POST['year'] == 'none')
                                                                                                echo ' selected'; ?>>
                                                                        <?php echo isset($i) ? $i : 'เลือกปี'; ?>
                                                                    </option>
                                                                    <?php
                                                                    $start_year = 2024; // ปีเริ่มต้น
                                                                    $current_year = date('Y'); // ปีปัจจุบัน

                                                                    for ($i = $start_year; $i <= $current_year + 1; $i++) {
                                                                        $selected = (isset($_POST['year']) && $_POST['year'] == $i) ? 'selected' : '';
                                                                        echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>เดือน:</label>
                                                                <select name="month" class="form-control custom-select" id="desktop-month" required>
                                                                    <option value="none" <?php if (isset($_POST['month']) && $_POST['month'] == 'none')
                                                                                                echo ' selected'; ?>>
                                                                        <?php echo isset($value) ? $value : 'เลือกเดือน'; ?>
                                                                    </option>
                                                                    <?php
                                                                    $months = [
                                                                        "01" => "มกราคม",
                                                                        "02" => "กุมภาพันธ์",
                                                                        "03" => "มีนาคม",
                                                                        "04" => "เมษายน",
                                                                        "05" => "พฤษภาคม",
                                                                        "06" => "มิถุนายน",
                                                                        "07" => "กรกฎาคม",
                                                                        "08" => "สิงหาคม",
                                                                        "09" => "กันยายน",
                                                                        "10" => "ตุลาคม",
                                                                        "11" => "พฤศจิกายน",
                                                                        "12" => "ธันวาคม"
                                                                    ];

                                                                    foreach ($months as $key => $value) {
                                                                        $selected = (isset($_POST['month']) && $_POST['month'] == $key) ? 'selected' : '';
                                                                        echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2" style="display:flex;align-items:flex-end">
                                                            <div class="form-group">
                                                                <button type="sumbit" class="btn btn-primary">ค้นหา</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </section>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-6 col-sm-12 mb-30" id="box2">
                                <div class="card-box pd-30 pt-10 height-100-p" id="card-box-2">
                                    <div class="pt-3 pb-3">

                                        <div class="row1">
                                            <h5>วันหยุดประจำสัปดาห์</h5>
                                            <p>
                                                <h7 style="color:red;">*หมายเหตุ*</h7>
                                                <h7>สามารถคลิกและเลื่อนซ้าย-ขวาวันหยุดและตารางได้</h7>
                                            </p>
                                            <div class="desktop-dayoff slide">
                                                <table>
                                                    <tbody>
                                                        <tr>
                                                            <?php foreach ($transaction_work as $row2) :
                                                                $day = $row2['day'];
                                                                $thai_day = array(
                                                                    'Sun' => 'อาทิตย์',
                                                                    'Mon' => 'จันทร์',
                                                                    'Tue' => 'อังคาร',
                                                                    'Wed' => 'พุธ',
                                                                    'Thu' => 'พฤหัสบดี',
                                                                    'Fri' => 'ศุกร์',
                                                                    'Sat' => 'เสาร์'
                                                                );
                                                            ?>
                                                                <td>
                                                                    <?php if ($row2['is_day_off'] == 'Yes') : ?>
                                                                        <div class="desktop-day dayoff">
                                                                            <span class="span1">
                                                                                <?php echo $thai_day[$day]; ?>
                                                                            </span>
                                                                            <span class="span2">
                                                                                <?php echo $row2['date']->format("d"); ?>
                                                                            </span>
                                                                            <span class="span3">หยุด</span>
                                                                        </div>
                                                                    <?php else : ?>
                                                                        <div class="desktop-day work">
                                                                            <span class="span1">
                                                                                <?php echo $thai_day[$day]; ?>
                                                                            </span>
                                                                            <span class="span2">
                                                                                <?php echo $row2['date']->format("d"); ?>
                                                                            </span>
                                                                            <span class="span3">ทำงาน</span>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </td>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <br>

                                        <div class="row2" style="display:flex;flex-direction:column;">
                                            <h5>วันหยุดตามประเพณีเดือน<?php echo $thaiMonthYear; ?>
                                            </h5>
                                            <div class="desktop-day-tradition slide">
                                                <?php foreach ($holidaydata as $row) : ?>
                                                    <div class="tradition">
                                                        <?php
                                                        $englishDay = date('l', strtotime($row['date']->format("Y-m-d")));
                                                        $thaiAbbreviation = isset($thaiDayAbbreviations[$englishDay]) ? $thaiDayAbbreviations[$englishDay] : '';
                                                        ?>
                                                        <div>
                                                            <span>
                                                                <?php echo (new DateTime($row['date']->format("Y-m-d")))->format("d"); ?>
                                                            </span>
                                                            <span>
                                                                <?php echo $thaiAbbreviation; ?>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <span><?= $row['name'] ?></span>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <br>

                                        <div class="row3">
                                            <h5>วันหยุดสมาชิกในทีม</h5>
                                            <div class="desktop-team-dayoff">
                                                <div class="desktop-table left">
                                                    <table id="table1" class="table stripe hover nowrap">
                                                        <thead>
                                                            <tr>
                                                                <th class="name">ชื่อ-สกุล</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($day_off_Team_section as $section) : ?>
                                                                <tr>
                                                                    <td class="employee">
                                                                        <div class="row">
                                                                            <div style="margin-right: 5px;margin-left: 5px;">
                                                                                <img src="<?php echo (!empty($section['employee_image'])) ? '../../admin/uploads_img/' . $section['employee_image'] : '../IMG/user.png'; ?>" class="border-radius-100 shadow" width="40" height="40" alt="">
                                                                            </div>
                                                                            <div>
                                                                                <b><?php echo $section['prefix_thai'] . $section['firstname_thai'] . ' ' . $section['lastname_thai']; ?></b><br><a class="text-primary"><?php echo $section['employee_email'] ?></a>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="desktop-table right slide slide">
                                                    <table id="table2" class="table stripe hover nowrap">
                                                        <thead>
                                                            <?php
                                                            $currentDate = new DateTime("$selectedYear-$selectedMonth-01");
                                                            $endDate = new DateTime($currentDate->format('Y-m-t')); // Set to the last day of the selected month
                                                            $daysOfWeekThai = array('อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์');
                                                            ?>
                                                            <tr>
                                                                <?php
                                                                $currentDate2 = clone $currentDate; // Create a copy of $currentDate for the second loop
                                                                while ($currentDate <= $endDate) :
                                                                ?>
                                                                    <th class="day">
                                                                        <?= $daysOfWeekThai[$currentDate->format('w')]; ?>
                                                                    </th>
                                                                <?php
                                                                    $currentDate->modify("+1 day");
                                                                endwhile;
                                                                ?>
                                                            </tr>
                                                            <tr>
                                                                <?php
                                                                $currentDate = $currentDate2; // Reset $currentDate to its initial value for the second loop
                                                                while ($currentDate <= $endDate) :
                                                                ?>
                                                                    <th class="date"><?= $currentDate->format("d") ?>
                                                                    </th>
                                                                <?php
                                                                    $currentDate->modify("+1 day");
                                                                endwhile;
                                                                ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($day_off_Team_section as $section) : ?>
                                                                <tr>
                                                                    <?php $found = false; ?>
                                                                    <?php foreach ($day_off_Team_info as $info) : ?>
                                                                        <?php if ($section['card_id'] == $info['card_id']) : ?>
                                                                            <td style="user-select:none;">
                                                                                <?php
                                                                                if ($info['is_day_off'] == 'Yes') {
                                                                                    echo '<span class="show-dayoff">หยุด</span>';
                                                                                } else {
                                                                                    echo '<span class="show-daywork"><img class="img-work" src="../IMG/mark.png" alt="วันทำงาน" title="วันทำงาน" style="width:30px"></span>';
                                                                                }
                                                                                ?>
                                                                            </td>
                                                                            <?php $found = true; ?>
                                                                        <?php endif; ?>
                                                                    <?php endforeach; ?>
                                                                    <?php if (!$found) : ?>
                                                                        <?php
                                                                        $statDateline = new DateTime("$selectedYear-$selectedMonth-01");
                                                                        $endline = new DateTime($statDateline->format('Y-m-t'));
                                                                        while ($statDateline <= $endline) : ?>
                                                                            <td style="user-select:none;">-</td>
                                                                    <?php $statDateline->modify('+1 day');
                                                                        endwhile;
                                                                    endif; ?>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <hr>
                                        </div>

                                        <script>
                                            // Function to initialize slider functionality for each element
                                            function tableslide(element) {
                                                let mouseDown = false;
                                                let startX, scrollLeft;

                                                const startDragging = (e) => {
                                                    mouseDown = true;
                                                    startX = e.pageX - element.offsetLeft;
                                                    scrollLeft = element.scrollLeft;
                                                };

                                                const stopDragging = (e) => {
                                                    mouseDown = false;
                                                };

                                                const move = (e) => {
                                                    e.preventDefault();
                                                    if (!mouseDown) {
                                                        return;
                                                    }
                                                    const x = e.pageX - element.offsetLeft;
                                                    const scroll = x - startX;
                                                    element.scrollLeft = scrollLeft - scroll;
                                                };

                                                // Add the event listeners to the current element
                                                element.addEventListener('mousemove', move, false);
                                                element.addEventListener('mousedown', startDragging, false);
                                                element.addEventListener('mouseup', stopDragging, false);
                                                element.addEventListener('mouseleave', stopDragging, false);
                                            }

                                            // Get all elements with the class 'slide' and initialize slider functionality for each
                                            const sliders = document.querySelectorAll('.slide');
                                            sliders.forEach(slider => {
                                                tableslide(slider);
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-6 col-sm-12 mb-30" id="box3">
                                <div class="card-box pd-30 pt-10 height-100-p" id="card-box-3">
                                    <div class="pt-3 pb-3">
                                        <div class="row4">
                                            <h5>รายการรออนุมัติ</h5>
                                            <hr>
                                            <div class="desktop-data-table">
                                                <table id="table3" class="table stripe hover nowrap">
                                                    <thead>
                                                        <th>ชื่อ - สกุล</th>
                                                        <th>วันที่</th>
                                                        <th>วันที่สลับ</th>
                                                        <th>เหตุผล</th>
                                                        <th>สถานะ</th>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($day_off_requestdata as $row) : ?>
                                                            <tr>
                                                                <td class="name">
                                                                    <?php echo $row['firstname_thai'] . ' ' . $row['lastname_thai']; ?>
                                                                </td>
                                                                <td class="date-request">
                                                                    <?php echo $row['edit_time']->format('d-m-Y'); ?>
                                                                </td>
                                                                <td class="shift-old">
                                                                    <?php echo $row['day_off1']; ?> -
                                                                    <?php echo $row['day_off2']; ?>
                                                                </td>
                                                                <td class="detail-change">
                                                                    <?php
                                                                    $shortDetail = htmlentities(mb_strimwidth($row['edit_detail'], 0, 10, '...'));
                                                                    echo $shortDetail;
                                                                    ?>
                                                                </td>
                                                                <td style="color:orange !important;font-weight:600">
                                                                    รออนุมัติ</td>
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
            </div>
        </div>

        <div class="mobile">
            <div class="navbar">

                <div class="div-span">
                    <span>วันหยุด</span>
                </div>

            </div>

            <div class="container">
                <div class="btn-top-3">
                    <div class="btn-1">
                        <div class="display-icon">
                            <button onclick="window.location.href='day-off-tradition-admin.php'"><img src="../IMG/day-off.png" alt=""></button>
                        </div>
                        <span>วันหยุด<br>ตามประเพณีนิยม</span>
                    </div>
                    <div class="btn-2">
                        <div class="display-icon">
                            <button onclick="window.location.href='day-off-change-admin.php'"><img src="../IMG/change.png" alt=""></button>
                        </div>
                        <span>ขอเปลี่ยน<br>รูปแบบวันหยุดและการทำงาน</span>
                    </div>
                    <div class="btn-3">
                        <div class="display-icon">
                            <button onclick="window.location.href='day-off-change-history-admin.php'"><img src="../IMG/dayoff.png" alt=""></button>
                        </div>
                        <span>ประวัติการขอ<br>เปลี่ยนวันหยุด</span>
                    </div>
                </div>
                <div class="display-dayOff">
                    <form class="select-time" method="post" onsubmit="return validateForm()">
                        <span>โปรดเลือกช่วงเวลาที่ท่านต้องการ</span><br>
                        <div class="search-section">
                            <label for="month">หน่วยงาน</label>
                            <select name="cost" class="js-example-basic-single" id="cost" required>
                                <option value="none" <?php if (isset($_POST['cost']) && $_POST['cost'] == 'none')
                                                            echo ' selected'; ?>>
                                    <?php echo isset($costLabel) ? $costLabel : 'เลือกหน่วยงาน'; ?>
                                </option>
                                <?php
                                foreach ($cost_center_code_data as $cost) {
                                    echo '<option value="' . $cost['cost_center_id'] . '"';
                                    if (isset($_POST['cost']) && $_POST['cost'] == $cost['cost_center_id']) {
                                        echo ' selected';
                                    }
                                    echo '>' . $cost['cost_center_code'] . ' - ' . $cost['name_thai'] . ' - ' . $cost['firstname_thai'] . ' ' . $cost['lastname_thai'] . '</option>';
                                    $costLabel = $cost['cost_center_code'] . ' - ' . $cost['firstname_thai'] . ' ' . $cost['lastname_thai'];
                                }
                                ?>
                            </select>
                        </div>
                        <div class="search-time">

                            <label for="year">ปี:</label>
                            <select name="year" class="form-control" id="year" required>
                                <option value="none" <?php if (isset($_POST['year']) && $_POST['year'] == 'none')
                                                            echo ' selected'; ?>>
                                    <?php echo isset($i) ? $i : 'เลือกปี'; ?>
                                </option>
                                <?php
                                $start_year = 2024; // ปีเริ่มต้น
                                $current_year = date('Y'); // ปีปัจจุบัน

                                for ($i = $start_year; $i <= $current_year + 1; $i++) {
                                    $selected = (isset($_POST['year']) && $_POST['year'] == $i) ? 'selected' : '';
                                    echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
                                }
                                ?>
                            </select>

                            <label for="month">เดือน:</label>
                            <select name="month" class="form-control" id="month" required>
                                <option value="none" <?php if (isset($_POST['month']) && $_POST['month'] == 'none')
                                                            echo ' selected'; ?>>
                                    <?php echo isset($value) ? $value : 'เลือกเดือน'; ?>
                                </option>
                                <?php
                                $months = [
                                    "01" => "มกราคม",
                                    "02" => "กุมภาพันธ์",
                                    "03" => "มีนาคม",
                                    "04" => "เมษายน",
                                    "05" => "พฤษภาคม",
                                    "06" => "มิถุนายน",
                                    "07" => "กรกฎาคม",
                                    "08" => "สิงหาคม",
                                    "09" => "กันยายน",
                                    "10" => "ตุลาคม",
                                    "11" => "พฤศจิกายน",
                                    "12" => "ธันวาคม"
                                ];

                                foreach ($months as $key => $value) {
                                    $selected = (isset($_POST['month']) && $_POST['month'] == $key) ? 'selected' : '';
                                    echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                                }
                                ?>
                            </select>

                            <button type="submit">ค้นหา</button>
                        </div>

                    </form>
                </div>

                <!-- ส่วนแสดงวันหยุดประจำตัวของ user -->
                <div class="display-dayOff-week">
                    <span class="dayTopic">วันหยุดประจำสัปดาห์</span>
                    <div class="display-day-week">
                        <table>
                            <tr>
                                <?php foreach ($transaction_work as $row2) : ?>
                                    <td>
                                        <?php echo $row2['day']; ?> <br>
                                        <?php echo $row2['date']->format("d"); ?>
                                        <?php if ($row2['is_day_off'] == 'Yes') : ?>
                                            <span class="day-off">หยุด</span>
                                        <?php else : ?>
                                            <span>ทำงาน</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="dayTopic-dayoff">
                    <span>วันหยุดตามประเพณีในเดือน
                        <?php echo $thaiMonthYear; ?>
                    </span>
                </div>
                <div class="display-dayOff-tradition">
                    <div class="display-day-month">
                        <?php foreach ($holidaydata as $row) : ?>
                            <div class="display-day">
                                <?php
                                $englishDay = date('l', strtotime($row['date']->format("Y-m-d")));
                                $thaiAbbreviation = isset($thaiDayAbbreviations[$englishDay]) ? $thaiDayAbbreviations[$englishDay] : '';
                                ?>
                                <div class="display-num-day">
                                    <span class="dayNum">
                                        <?php echo (new DateTime($row['date']->format("Y-m-d")))->format("d"); ?>
                                    </span>
                                    <span style="font-size: 2.5vw">
                                        <?php echo $thaiAbbreviation; ?>
                                    </span>
                                </div>
                                <span>
                                    <?= $row['name'] ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="display-dayOff-team">
                    <div class="topic-dateTeam">
                        <span class="dayTopic">วันหยุดของสมาชิกในทีม</span>
                    </div>
                    <div class="display-table-dayTeam">
                        <?php if (!empty($day_off_Team_section)) : ?>
                            <div class="table-name">
                                <table id="table1">
                                    <thead>
                                        <tr>
                                            <th class="nameEm">ชื่อสกุล</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($day_off_Team_section as $section) : ?>
                                            <tr>
                                                <td class="nameEm">
                                                    <?= $section['firstname_thai'] ?>
                                                    <?= $section['lastname_thai'] ?>
                                                </td>
                                            <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="table-date">
                                <table id="table2">
                                    <thead>
                                        <?php
                                        $currentDate = new DateTime("$selectedYear-$selectedMonth-01");
                                        $endDate = new DateTime($currentDate->format('Y-m-t')); // Set to the last day of the selected month
                                        $daysOfWeekThai = array('อา.', 'จ.', 'อ.', 'พ.', 'พฤ.', 'ศ.', 'ส.');
                                        while ($currentDate <= $endDate) :
                                        ?>
                                            <th class="dayWeek">
                                                <?= htmlspecialchars($daysOfWeekThai[$currentDate->format('w')] . ' ' . $currentDate->format("d")); ?>
                                            </th>
                                        <?php
                                            $currentDate->modify("+1 day");
                                        endwhile;
                                        ?>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($day_off_Team_section as $section) : ?>
                                            <tr>
                                                <?php $found = false; ?>
                                                <?php foreach ($day_off_Team_info as $info) : ?>
                                                    <?php if ($section['card_id'] == $info['card_id']) : ?>
                                                        <td>
                                                            <?php
                                                            if ($info['is_day_off'] == 'Yes') {
                                                                echo '<span class="show-dayoff">หยุด</span>';
                                                            } else {
                                                                echo '<span ><img class="img-work" src="../IMG/mark.png" alt=""></span>';
                                                            }
                                                            ?>
                                                        </td>
                                                        <?php $found = true; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <?php if (!$found) :
                                                    $statDateline = new DateTime("$selectedYear-$selectedMonth-01");
                                                    $endline = new DateTime($statDateline->format('Y-m-t'));
                                                    while ($statDateline <= $endline) : ?>
                                                        <td><img class="img-work" src="../IMG/mark.png" alt=""></td>
                                                        <!-- <td><span class="show-daywork">ทำงาน</span></td> -->
                                                <?php $statDateline->modify('+1 day');
                                                    endwhile;
                                                endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="topic-wait-approve">
                    <span>รายการรออนุมัติ</span>
                </div>

                <div class="table-container table1">
                    <table id="example1" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>ชื่อ - สกุล</th>
                                <th>วันที่</th>
                                <th style="white-space: nowrap;">วันที่สลับ</th>
                                <th>เหตุผล</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($day_off_requestdata as $row) : ?>
                            <tr>
                                <td class="name">
                                    <?php echo $row['firstname_thai'] . ' ' . $row['lastname_thai']; ?>
                                </td>
                                <td class="date-request">
                                    <?php echo $row['edit_time']->format('d-m-Y'); ?>
                                </td>
                                <td class="shift-old">
                                    <?php echo $row['day_off1']; ?> - <?php echo $row['day_off2']; ?>
                                </td>
                                <td class="detail-change" onclick="showFullText('<?php echo htmlentities($row['edit_detail']); ?>')">
                                    <?php echo htmlentities(mb_strimwidth($row['edit_detail'], 0, 10, '...')); ?>
                                </td>

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
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include('../includes/footer.php') ?>