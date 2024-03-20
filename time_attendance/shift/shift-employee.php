<?php
session_start();
session_regenerate_id(true);
//---------------------------------------------------------------------------------------
header("Cache-Control: no-cache, must-revalidate");
include("../database/connectdb.php");
include('../components-desktop/employee/include/header.php');
include("../processing/update_shift.php");
?>
<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/employee/shift-employee.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/shift.css">

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="script/loader-normal.js"></script>

<?php

// ทำความสะอาดและป้องกันข้อมูล
$_SESSION['card_id'] = trim($_SESSION['card_id']);  // ลบช่องว่างที่อาจเกิดขึ้นทั้งด้านหน้าและด้านหลังของข้อมูล
$_SESSION['card_id'] = preg_replace('/[^a-zA-Z0-9]/', '', $_SESSION['card_id']);  // ลบทุกอักขระที่ไม่ใช่ a-z, A-Z, และ 0-9
$_SESSION['card_id'] = htmlspecialchars($_SESSION['card_id'], ENT_QUOTES, 'UTF-8');  // แปลงสัญลักษณ์ HTML เพื่อป้องกัน XSS

if (isset($_SESSION["card_id"])) {
    $card_id = $_SESSION["card_id"];
    $sql = "SELECT sub_team_id FROM employee WHERE card_id = ?";
    $result = sqlsrv_query($conn, $sql, array($card_id));

    if (isset($_GET['month']) && isset($_GET['year'])) {
        $selected_month = $_GET['month'];
        $selected_year = $_GET['year'];
        // update_shift($selected_year, $selected_month, $_SESSION["card_id"]);
    } else {
        $selected_month = date('m');
        $selected_year = date('Y');
        // update_shift($selected_year, $selected_month, $_SESSION["card_id"]);
    }
    $thai_month_names = [
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
    $showmonththai = $thai_month_names[$selected_month];
    $showyearthai = $selected_year + 543;

    $prev_month = date('m', strtotime("$selected_year-$selected_month-01 -1 month"));
    $prev_year = date('Y', strtotime("$selected_year-$selected_month-01 -1 month"));
    $next_month = date('m', strtotime("$selected_year-$selected_month-01 +1 month"));
    $next_year = date('Y', strtotime("$selected_year-$selected_month-01 +1 month"));

    if ($result) {
        $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        if (!empty($row['sub_team_id'])) {
            $select_shift_team = "SELECT scg_employee_id, prefix_thai, firstname_thai, lastname_thai,card_id,date, day, shift_main, shift_add, shift_lock
                                FROM employee
                                INNER JOIN transaction_work ON employee.card_id = transaction_work.card_id
                                WHERE employee.sub_team_id = (
                                    SELECT sub_team_id
                                    FROM employee
                                    WHERE card_id = ?
                                )
                                ORDER BY employee.card_id ASC;";

            $shift_team_stmt = sqlsrv_prepare($conn, $select_shift_team, array(&$card_id));
            sqlsrv_execute($shift_team_stmt);

            $shiftData = array(); // Corrected array name

            while ($row = sqlsrv_fetch_array($shift_team_stmt, SQLSRV_FETCH_ASSOC)) {
                $shiftData[] = $row; // Corrected array name
            }

            //------------------------------------------------------------------------------------------

            $select_team = "SELECT * FROM sub_team WHERE head_card_id = ?";
            $dayoff_stmt = sqlsrv_prepare($conn, $select_team, array(&$card_id));
            sqlsrv_execute($dayoff_stmt);
            $row = sqlsrv_fetch_array($dayoff_stmt, SQLSRV_FETCH_ASSOC);
            $nameteam = $row['name'];

            //------------------------------------------------------------------------------------------

            $select_dayoffteam = "SELECT 
                                            employee.prefix_thai AS prefix_thai,
                                            employee.firstname_thai AS firstname_thai,
                                            employee.lastname_thai AS lastname_thai,
                                            employee.card_id AS card_id,
                                            employee.scg_employee_id AS scg_employee_id,
                                            employee.employee_email AS employee_email,
                                            employee.employee_image AS employee_image,
                                            work_format.remark AS remark,
                                            work_format.day_off1 AS day_off1,
                                            work_format.day_off2 AS day_off2
                                FROM employee
                                INNER JOIN work_format ON employee.work_format_code = work_format.work_format_code
                                INNER JOIN sub_team ON employee.sub_team_id = sub_team.sub_team_id
                                WHERE employee.sub_team_id = ?";
            $dayoffteam_stmt = sqlsrv_prepare($conn, $select_dayoffteam, array(&$row['sub_team_id']));
            sqlsrv_execute($dayoffteam_stmt);
            $work_dayoffteam = array();  // Initialize the array to store results
            while ($row = sqlsrv_fetch_array($dayoffteam_stmt, SQLSRV_FETCH_ASSOC)) {
                $work_dayoffteam[] = $row;
            }
            $countwork_dayoffteam = count($work_dayoffteam) + 1;

            $select_shift_team = "SELECT scg_employee_id, prefix_thai, firstname_thai, lastname_thai, employee.card_id, date, day, shift_main, shift_add, shift_lock
            FROM employee
            INNER JOIN transaction_work ON employee.card_id = transaction_work.card_id
            WHERE employee.sub_team_id = (SELECT sub_team_id FROM employee WHERE card_id = ?)
            AND FORMAT(date, 'MM-yyyy') = ? ORDER BY employee.card_id, date ASC;";

            $dayoff_team_stmt = sqlsrv_prepare($conn, $select_shift_team, array($card_id, "{$selected_month}-{$selected_year}"));

            sqlsrv_execute($dayoff_team_stmt);
            $shiftData = array();
            while ($row = sqlsrv_fetch_array($dayoff_team_stmt, SQLSRV_FETCH_ASSOC)) {
                $shiftData[] = $row;
            }
        } else {
            $nameteam = "-";
            $select_dayoffteam = "SELECT 
                                employee.prefix_thai AS prefix_thai,
                                employee.firstname_thai AS firstname_thai,
                                employee.lastname_thai AS lastname_thai,
                                employee.card_id AS card_id,
                                employee.scg_employee_id AS scg_employee_id,
                                employee.employee_email AS employee_email,
                                employee.employee_image AS employee_image,
                                work_format.remark AS remark,
                                work_format.day_off1 AS day_off1,
                                work_format.day_off2 AS day_off2
                                FROM employee
                                INNER JOIN work_format ON employee.work_format_code = work_format.work_format_code
                                WHERE employee.card_id = ?";
            $dayoffteam_stmt = sqlsrv_prepare($conn, $select_dayoffteam, array(&$card_id));
            sqlsrv_execute($dayoffteam_stmt);
            $work_dayoffteam = array();  // Initialize the array to store results
            while ($row = sqlsrv_fetch_array($dayoffteam_stmt, SQLSRV_FETCH_ASSOC)) {
                $work_dayoffteam[] = $row;
            }
            $countwork_dayoffteam = count($work_dayoffteam) + 1;

            $select_shift_team = "SELECT scg_employee_id, firstname_thai, lastname_thai, employee.card_id, date, day, shift_main, shift_add, shift_lock
            FROM employee
            INNER JOIN transaction_work ON employee.card_id = transaction_work.card_id
            WHERE employee.card_id = ?
            AND FORMAT(date, 'MM-yyyy') = ? ORDER BY date ASC;";

            $shift_team_stmt = sqlsrv_prepare($conn, $select_shift_team, array($card_id, "{$selected_month}-{$selected_year}"));
            sqlsrv_execute($shift_team_stmt);
            $shiftData = array();
            while ($row = sqlsrv_fetch_array($shift_team_stmt, SQLSRV_FETCH_ASSOC)) {
                $shiftData[] = $row;
            }
        }
    }
}

?>
<script>
$(document).ready(function() {
    $(".desktop-table").on("scroll", function() {
        // รับค่าตำแหน่งการเลื่อนขององค์ประกอบปัจจุบัน
        var currentScrollTop = $(this).scrollTop();

        // ซิงโครไนส์การเลื่อนขององค์ประกอบอื่น ๆ ให้ตรงกับตำแหน่งการเลื่อนขององค์ประกอบปัจจุบัน
        $(".desktop-table").not(this).scrollTop(currentScrollTop);
    });
});
</script>
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
                                    <h2>ตารางกะการทำงาน</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor: default;">กะการทำงาน</a>
                                        </li>
                                        <li class=" breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            ตารางกะการทำงาน
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-6 col-sm-12 mb-30" id="box1">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="pt-3 pb-3">
                                    <div class="bar">
                                        <button id="prevMonth"
                                            onclick="javascript:location.href='?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>'"><img
                                                src="
                                            ../IMG/arrowleft.png" alt=""></button>
                                        <div class="current" id="currentDate">
                                            <?= $showmonththai . " " . $showyearthai ?>
                                        </div>
                                        <button id="nextMonth"
                                            onclick="javascript:location.href='?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>'"><img
                                                src="../IMG/arrowright.png" alt=""></button>
                                    </div>

                                    <div class="desktop-table-container">
                                        <div class="desktop-table left">
                                            <table id="table1" class="table stripe hover nowrap">
                                                <thead>
                                                    <tr>
                                                        <th colspan="3" data-orderable="false">ข้อมูลพนักงาน
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th>ทีม</th>
                                                        <th>รหัสพนักงาน</th>
                                                        <th>ชื่อ-สกุลพนักงาน</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="team" rowspan="<?= $countwork_dayoffteam ?>">
                                                            <?= $nameteam ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    foreach ($work_dayoffteam as $rs_emp) { ?>
                                                    <tr>
                                                        <td><b><?php echo $rs_emp['scg_employee_id']; ?></b>
                                                        </td>
                                                        <td>
                                                            <div class="row">
                                                                <div style="margin-right: 5px;margin-left: 5px;">
                                                                    <img src="<?php echo (!empty($rs_emp['employee_image'])) ? '../../admin/uploads_img/' . $rs_emp['employee_image'] : '../IMG/user.png'; ?>"
                                                                        class="border-radius-100 shadow" width="40"
                                                                        height="40" alt="">
                                                                </div>
                                                                <div>
                                                                    <b><?php echo $rs_emp['prefix_thai'] . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai']; ?></b><br>
                                                                    <a
                                                                        class="text-primary"><?php echo $rs_emp['employee_email'] ?></a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="desktop-table right slide">
                                            <table id="table2" class="table stripe hover nowrap">
                                                <thead>
                                                    <tr>
                                                        <?php
                                                        for ($day = 1; $day <= date('t', strtotime("$selected_year-$selected_month-01")); $day++) {
                                                            $dayOfWeek = date('D', strtotime("$selected_year-$selected_month-$day"));

                                                            // Convert English day abbreviation to Thai
                                                            $dayOfWeekThai = convert_day($dayOfWeek);

                                                            echo "<th>$dayOfWeekThai</th>";
                                                        }

                                                        // Function to convert English day abbreviation to Thai
                                                        function convert_day($dayOfWeek)
                                                        {
                                                            $daysMap = array(
                                                                'Mon' => 'จันทร์',
                                                                'Tue' => 'อังคาร',
                                                                'Wed' => 'พุธ',
                                                                'Thu' => 'พฤหัสบดี',
                                                                'Fri' => 'ศุกร์',
                                                                'Sat' => 'เสาร์',
                                                                'Sun' => 'อาทิตย์'
                                                            );

                                                            return $daysMap[$dayOfWeek];
                                                        }
                                                        ?>
                                                    </tr>
                                                    <tr>
                                                        <?php
                                                        for ($day = 1; $day <= date('t', strtotime("$selected_year-$selected_month-01")); $day++) {
                                                            echo "<th>" . sprintf("%02d", $day) . "</th>";
                                                        }
                                                        ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($work_dayoffteam as $rs_emp) { ?>
                                                    <tr>
                                                        <?php
                                                            $found = false;
                                                            foreach ($shiftData as $row) {
                                                                if ($row['card_id'] === $rs_emp['card_id']) {
                                                                    $shiftMainClass = '';
                                                                    $shiftAddClass = '';

                                                                    if (!empty($row['shift_add'])) {
                                                                        switch ($row['shift_main']) {
                                                                            case 'DD01':
                                                                            case 'DD02':
                                                                                $shiftMainLabel = 'ป'; // ป for DD01 and DD02
                                                                                $shiftMainClass = 'shift_main';
                                                                                break;
                                                                            case 'SA01':
                                                                                $shiftMainLabel = '1'; // 1 for SA01
                                                                                $shiftMainClass = 'shift_main';
                                                                                break;
                                                                            case 'SB01':
                                                                                $shiftMainLabel = '2'; // 2 for SB01
                                                                                $shiftMainClass = 'shift_main';
                                                                                break;
                                                                            case 'SC01':
                                                                                $shiftMainLabel = '3'; // 3 for SC01
                                                                                $shiftMainClass = 'shift_main';
                                                                                break;
                                                                            case 'OFF':
                                                                                $shiftMainLabel = 'ย'; // ย for OFF
                                                                                $shiftMainClass = 'dayOff'; // class for dayOff
                                                                                break;
                                                                            case 'LEAVE':
                                                                                $shiftMainLabel = 'ล'; // ล for LEAVE
                                                                                $shiftMainClass = 'leave-annual'; // class for leave-annual
                                                                                break;
                                                                            case 'HOLIDAY':
                                                                                $shiftMainLabel = 'ข'; // ข for HOLIDAY
                                                                                $shiftMainClass = 'dayOff-public'; // class for dayOff-public
                                                                                break;
                                                                            case 'TRAIN':
                                                                                $shiftMainLabel = 'TRAIN'; // 'TRAIN' for TRAIN
                                                                                $shiftMainClass = 'training'; // class for training
                                                                                break;
                                                                            default:
                                                                                $shiftMainLabel = $row['shift_main']; // Use the original value as a fallback
                                                                        }

                                                                        switch ($row['shift_add']) {
                                                                            case 'DD01':
                                                                            case 'DD02':
                                                                                $shiftAddLabel = 'ป'; // ป for DD01 and DD02
                                                                                $shiftAddClass = 'shiftAdd';
                                                                                break;
                                                                            case 'SA01':
                                                                                $shiftAddLabel = '1'; // 1 for SA01
                                                                                $shiftAddClass = 'shiftAdd';
                                                                                break;
                                                                            case 'SB01':
                                                                                $shiftAddLabel = '2'; // 2 for SB01
                                                                                $shiftAddClass = 'shiftAdd';
                                                                                break;
                                                                            case 'SC01':
                                                                                $shiftAddLabel = '3'; // 3 for SC01
                                                                                $shiftAddClass = 'shiftAdd';
                                                                                break;
                                                                            case 'OFF':
                                                                                $shiftAddLabel = 'ย'; // ย for OFF
                                                                                $shiftAddClass = 'shiftAdd'; // class for dayOff
                                                                                break;
                                                                            case 'LEAVE':
                                                                                $shiftAddLabel = 'ล'; // ล for LEAVE
                                                                                $shiftAddClass = 'shiftAdd'; // class for leave-annual
                                                                                break;
                                                                            case 'HOLIDAY':
                                                                                $shiftAddLabel = 'ข'; // ข for HOLIDAY
                                                                                $shiftAddClass = 'shiftAdd'; // class for dayOff-public
                                                                                break;
                                                                            case 'TRAIN':
                                                                                $shiftAddLabel = 'TRAIN'; // 'TRAIN' for TRAIN
                                                                                $shiftAddClass = 'shiftAdd'; // class for training
                                                                                break;
                                                                            default:
                                                                                $shiftAddLabel = $row['shift_main']; // Use the original value as a fallback
                                                                        }

                                                                        echo '<td class="' . $shiftMainClass . '"><span>' . $shiftMainLabel . '</span><span class="' . $shiftAddClass . '">' . $shiftAddLabel . '</span></td>';
                                                                    } elseif (!empty($row['shift_lock'])) {
                                                                        switch ($row['shift_lock']) {
                                                                            case 'DD01':
                                                                            case 'DD02':
                                                                                $shiftMainLabel = 'ป'; // ป for DD01 and DD02
                                                                                $shiftMainClass = 'lock';
                                                                                break;
                                                                            case 'SA01':
                                                                                $shiftMainLabel = '1'; // 1 for SA01
                                                                                $shiftMainClass = 'lock';
                                                                                break;
                                                                            case 'SB01':
                                                                                $shiftMainLabel = '2'; // 2 for SB01
                                                                                $shiftMainClass = 'lock';
                                                                                break;
                                                                            case 'SC01':
                                                                                $shiftMainLabel = '3'; // 3 for SC01
                                                                                $shiftMainClass = 'lock';
                                                                                break;
                                                                            default:
                                                                                $shiftMainLabel = $row['shift_main']; // Use the original value as a fallback
                                                                        }
                                                                        echo '<td class="' . $shiftMainClass . '">' . $shiftMainLabel . '</td>';
                                                                    } elseif (!empty($row['shift_main'])) {
                                                                        switch ($row['shift_main']) {
                                                                            case 'DD01':
                                                                            case 'DD02':
                                                                                $shiftMainLabel = 'ป'; // ป for DD01 and DD02
                                                                                $shiftMainClass = 'shift_main';
                                                                                break;
                                                                            case 'SA01':
                                                                                $shiftMainLabel = '1'; // 1 for SA01
                                                                                $shiftMainClass = 'shift_main';
                                                                                break;
                                                                            case 'SB01':
                                                                                $shiftMainLabel = '2'; // 2 for SB01
                                                                                $shiftMainClass = 'shift_main';
                                                                                break;
                                                                            case 'SC01':
                                                                                $shiftMainLabel = '3'; // 3 for SC01
                                                                                $shiftMainClass = 'shift_main';
                                                                                break;
                                                                            case 'OFF':
                                                                                $shiftMainLabel = 'ย'; // ย for OFF
                                                                                $shiftMainClass = 'dayOff'; // class for dayOff
                                                                                break;
                                                                            case 'LEAVE':
                                                                                $shiftMainLabel = 'ล'; // ล for LEAVE
                                                                                $shiftMainClass = 'leave-annual'; // class for leave-annual
                                                                                break;
                                                                            case 'HOLIDAY':
                                                                                $shiftMainLabel = 'ข'; // ข for HOLIDAY
                                                                                $shiftMainClass = 'dayOff-public'; // class for dayOff-public
                                                                                break;
                                                                            case 'TRAIN':
                                                                                $shiftMainLabel = 'TRAIN'; // 'TRAIN' for TRAIN
                                                                                $shiftMainClass = 'training'; // class for training
                                                                                break;
                                                                            default:
                                                                                $shiftMainLabel = $row['shift_main']; // Use the original value as a fallback
                                                                        }
                                                                        echo '<td class="' . $shiftMainClass . '">' . $shiftMainLabel . '</td>';
                                                                    }
                                                                    $found = true;
                                                                }
                                                            }
                                                            if (!$found) {
                                                                $statDateline = new DateTime("$selected_year-$selected_month-01");
                                                                $endline = new DateTime($statDateline->format('Y-m-t'));
                                                                while ($statDateline <= $endline) {
                                                                    echo "<td>ป</td>";
                                                                    $statDateline->modify('+1 day');
                                                                }
                                                            }
                                                            ?>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
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
                                    <hr>
                                    <div class="color-detail">
                                        <div id="status1">
                                            <div>
                                                <button style="background-color:#8ad303;"></button><span>กะหลัก</span>
                                            </div>
                                            <div>
                                                <button style="background-color:#ffda19;"></button><span>กะเสริม</span>
                                            </div>
                                            <div>
                                                <button
                                                    style="background-color:#ff5643;"></button><span>วันหยุดประจำสัปดาห์</span>
                                            </div>
                                        </div>
                                        <div id="status2">
                                            <div>
                                                <button
                                                    style="background-color:#02a426;"></button><span>วันหยุดชดเชย</span>
                                            </div>
                                            <div>
                                                <button
                                                    style="background-color:#616061;"></button><span>วันหยุดนักขัตฤกษ์</span>
                                            </div>
                                            <div>
                                                <button style="background-color:#9747ff;"></button><span>อบรม</span>
                                            </div>
                                        </div>
                                        <div id="status3">
                                            <div>
                                                <button
                                                    style="background-color:#00b0db;"></button><span>ลาพักร้อน</span>
                                            </div>
                                            <div>
                                                <button style="background-color:#a3a3a3;"></button><span>ลาป่วย</span>
                                            </div>
                                            <div>
                                                <button
                                                    style="background-color:#ff439d;"></button><span>ล็อกเหลี่ยม</span>
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
        <div class="navbar">
            <div class="div-span">
                <span>กะการทำงาน</span>
            </div>
        </div>

        <div class="container">
            <div class="btn-top-head">
                <div class="btn-1">
                    <div class="display-icon">
                        <button onclick="window.location.href='shift-progress-step1-employee.php'"><img
                                src="../IMG/mnTeam.png" alt=""></button>
                    </div>
                    <span>จัดการกะการทำงาน</span>
                </div>
                <div class="btn-4">
                    <div class="display-icon">
                        <button onclick="window.location.href='shift-request-employee.php'"><img src="../IMG/rq.png"
                                alt=""></button>
                    </div>
                    <span>รายการขออนุมัติ</span>
                </div>
            </div>
            <div class="container-table-team">
                <div class="topic-table">
                    <span>ตารางกะการทำงานของทีม</span>
                </div>
                <div class="table-shift">

                    <div class="display-monthNow">
                        <a href="?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>">
                            <button id="prevMonth"><img src="../IMG/arrowleft.png" alt=""></button>
                        </a>
                        <div class="current" id="currentDate"><?= $showmonththai ?> <?= $showyearthai ?></div>
                        <a href="?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>">
                            <button id="nextMonth"><img src="../IMG/arrowright.png" alt=""></button>
                        </a>
                    </div>

                    <div class="display-table">
                        <div class="display-table-left">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="topicdata" colspan="3">ข้อมูลพนักงาน</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="nameTeam">ทีม</td>
                                        <td class="idEm">รหัส</td>
                                        <td class="nameEm">ชื่อ</td>
                                    </tr>
                                    <tr>
                                        <td class="team" rowspan="<?= $countwork_dayoffteam ?>"><?= $nameteam ?>
                                        </td>
                                    </tr>

                                    <?php
                                    foreach ($work_dayoffteam as $rs_emp) { ?>
                                    <tr>
                                        <td class="id"><?php echo $rs_emp['scg_employee_id']; ?></td>
                                        <td class="display-name">
                                            <?php echo $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai']; ?>
                                        </td>
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="display-table-right">
                            <table>
                                <thead>
                                    <tr>

                                        <?php
                                        for ($day = 1; $day <= date('t', strtotime("$selected_year-$selected_month-01")); $day++) {
                                            $dayOfWeek = date('D', strtotime("$selected_year-$selected_month-$day"));

                                            // Convert English day abbreviation to Thai
                                            $dayOfWeekThai = convertDayToThai($dayOfWeek);

                                            echo "<th>$dayOfWeekThai</th>";
                                        }

                                        // Function to convert English day abbreviation to Thai
                                        function convertDayToThai($dayOfWeek)
                                        {
                                            $daysMap = array(
                                                'Mon' => 'จ.',
                                                'Tue' => 'อ.',
                                                'Wed' => 'พ.',
                                                'Thu' => 'พฤ.',
                                                'Fri' => 'ศ.',
                                                'Sat' => 'ส.',
                                                'Sun' => 'อา.'
                                            );

                                            return $daysMap[$dayOfWeek];
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <?php
                                        for ($day = 1; $day <= date('t', strtotime("$selected_year-$selected_month-01")); $day++) {
                                            echo "<td class='date'>$day</td>";
                                        }
                                        ?>
                                    </tr>
                                    <?php
                                    $printedNames = array();

                                    foreach ($work_dayoffteam as $employee) {
                                        echo "<tr>";
                                        $found = false;
                                        foreach ($shiftData as $row) {
                                            if ($row['card_id'] === $employee['card_id']) {
                                                $shiftMainClass = '';
                                                $shiftAddClass = '';

                                                if (!empty($row['shift_add'])) {
                                                    switch ($row['shift_main']) {
                                                        case 'DD01':
                                                        case 'DD02':
                                                            $shiftMainLabel = 'ป'; // ป for DD01 and DD02
                                                            $shiftMainClass = 'shift_main';
                                                            break;
                                                        case 'SA01':
                                                            $shiftMainLabel = '1'; // 1 for SA01
                                                            $shiftMainClass = 'shift_main';
                                                            break;
                                                        case 'SB01':
                                                            $shiftMainLabel = '2'; // 2 for SB01
                                                            $shiftMainClass = 'shift_main';
                                                            break;
                                                        case 'SC01':
                                                            $shiftMainLabel = '3'; // 3 for SC01
                                                            $shiftMainClass = 'shift_main';
                                                            break;
                                                        case 'OFF':
                                                            $shiftMainLabel = 'ย'; // ย for OFF
                                                            $shiftMainClass = 'dayOff'; // class for dayOff
                                                            break;
                                                        case 'LEAVE':
                                                            $shiftMainLabel = 'ล'; // ล for LEAVE
                                                            $shiftMainClass = 'leave-annual'; // class for leave-annual
                                                            break;
                                                        case 'HOLIDAY':
                                                            $shiftMainLabel = 'ข'; // ข for HOLIDAY
                                                            $shiftMainClass = 'dayOff-public'; // class for dayOff-public
                                                            break;
                                                        case 'TRAIN':
                                                            $shiftMainLabel = 'TRAIN'; // 'TRAIN' for TRAIN
                                                            $shiftMainClass = 'training'; // class for training
                                                            break;
                                                        default:
                                                            $shiftMainLabel = $row['shift_main']; // Use the original value as a fallback
                                                    }

                                                    switch ($row['shift_add']) {
                                                        case 'DD01':
                                                        case 'DD02':
                                                            $shiftAddLabel = 'ป'; // ป for DD01 and DD02
                                                            $shiftAddClass = 'shiftAdd';
                                                            break;
                                                        case 'SA01':
                                                            $shiftAddLabel = '1'; // 1 for SA01
                                                            $shiftAddClass = 'shiftAdd';
                                                            break;
                                                        case 'SB01':
                                                            $shiftAddLabel = '2'; // 2 for SB01
                                                            $shiftAddClass = 'shiftAdd';
                                                            break;
                                                        case 'SC01':
                                                            $shiftAddLabel = '3'; // 3 for SC01
                                                            $shiftAddClass = 'shiftAdd';
                                                            break;
                                                        case 'OFF':
                                                            $shiftAddLabel = 'ย'; // ย for OFF
                                                            $shiftAddClass = 'shiftAdd'; // class for dayOff
                                                            break;
                                                        case 'LEAVE':
                                                            $shiftAddLabel = 'ล'; // ล for LEAVE
                                                            $shiftAddClass = 'shiftAdd'; // class for leave-annual
                                                            break;
                                                        case 'HOLIDAY':
                                                            $shiftAddLabel = 'ข'; // ข for HOLIDAY
                                                            $shiftAddClass = 'shiftAdd'; // class for dayOff-public
                                                            break;
                                                        case 'TRAIN':
                                                            $shiftAddLabel = 'TRAIN'; // 'TRAIN' for TRAIN
                                                            $shiftAddClass = 'shiftAdd'; // class for training
                                                            break;
                                                        default:
                                                            $shiftAddLabel = $row['shift_main']; // Use the original value as a fallback
                                                    }

                                                    echo '<td class="' . $shiftMainClass . '"><span>' . $shiftMainLabel . '</span><span class="' . $shiftAddClass . '">' . $shiftAddLabel . '</span></td>';
                                                } elseif (!empty($row['shift_lock'])) {
                                                    switch ($row['shift_lock']) {
                                                        case 'DD01':
                                                        case 'DD02':
                                                            $shiftMainLabel = 'ป'; // ป for DD01 and DD02
                                                            $shiftMainClass = 'lock';
                                                            break;
                                                        case 'SA01':
                                                            $shiftMainLabel = '1'; // 1 for SA01
                                                            $shiftMainClass = 'lock';
                                                            break;
                                                        case 'SB01':
                                                            $shiftMainLabel = '2'; // 2 for SB01
                                                            $shiftMainClass = 'lock';
                                                            break;
                                                        case 'SC01':
                                                            $shiftMainLabel = '3'; // 3 for SC01
                                                            $shiftMainClass = 'lock';
                                                            break;
                                                        default:
                                                            $shiftMainLabel = $row['shift_main']; // Use the original value as a fallback
                                                    }

                                                    echo '<td class="' . $shiftMainClass . '">' . $shiftMainLabel . '</td>';
                                                } elseif (!empty($row['shift_main'])) {
                                                    switch ($row['shift_main']) {
                                                        case 'DD01':
                                                        case 'DD02':
                                                            $shiftMainLabel = 'ป'; // ป for DD01 and DD02
                                                            $shiftMainClass = 'shift_main';
                                                            break;
                                                        case 'SA01':
                                                            $shiftMainLabel = '1'; // 1 for SA01
                                                            $shiftMainClass = 'shift_main';
                                                            break;
                                                        case 'SB01':
                                                            $shiftMainLabel = '2'; // 2 for SB01
                                                            $shiftMainClass = 'shift_main';
                                                            break;
                                                        case 'SC01':
                                                            $shiftMainLabel = '3'; // 3 for SC01
                                                            $shiftMainClass = 'shift_main';
                                                            break;
                                                        case 'OFF':
                                                            $shiftMainLabel = 'ย'; // ย for OFF
                                                            $shiftMainClass = 'dayOff'; // class for dayOff
                                                            break;
                                                        case 'LEAVE':
                                                            $shiftMainLabel = 'ล'; // ล for LEAVE
                                                            $shiftMainClass = 'leave-annual'; // class for leave-annual
                                                            break;
                                                        case 'HOLIDAY':
                                                            $shiftMainLabel = 'ข'; // ข for HOLIDAY
                                                            $shiftMainClass = 'dayOff-public'; // class for dayOff-public
                                                            break;
                                                        case 'TRAIN':
                                                            $shiftMainLabel = 'TRAIN'; // 'TRAIN' for TRAIN
                                                            $shiftMainClass = 'training'; // class for training
                                                            break;
                                                        default:
                                                            $shiftMainLabel = $row['shift_main']; // Use the original value as a fallback
                                                    }

                                                    echo '<td class="' . $shiftMainClass . '">' . $shiftMainLabel . '</td>';
                                                }
                                                $found = true;
                                            }
                                        }

                                        echo "</tr>";
                                        if (!$found) {
                                            $statDateline = new DateTime("$selected_year-$selected_month-01");
                                            $endline = new DateTime($statDateline->format('Y-m-t'));

                                            echo "<tr>";

                                            while ($statDateline <= $endline) {
                                                echo "<td>ป</td>";
                                                $statDateline->modify('+1 day');
                                            }

                                            echo "</tr>";
                                        }
                                    }

                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="detail-color">
                    <div class="display-row1">
                        <div class="shift-main">
                            <img src="../IMG/dotGreen.png" alt="">
                            <span>กะหลัก</span>
                        </div>
                        <div class="day-off-compen">
                            <img src="../IMG/dotGreen1.png" alt="">
                            <span>วันหยุดชดเชย</span>
                        </div>
                        <div class="leaveAnnule">
                            <img src="../IMG/dotsky.png" alt="">
                            <span>ลาพักร้อน</span>
                        </div>
                    </div>
                    <div class="display-row2">
                        <div class="shift-Add">
                            <img src="../IMG/dotyl.png" alt="">
                            <span>กะเสริม</span>
                        </div>
                        <div class="dayoff-plublic">
                            <img src="../IMG/dotdark.png" alt="">
                            <span>วันหยุดนักขัตฤกษ์</span>
                        </div>
                        <div class="leave-sick">
                            <img src="../IMG/dotgray.png" alt="">
                            <span>ลาป่วย</span>
                        </div>
                    </div>

                    <div class="display-row3">
                        <div class="dayoff">
                            <img src="../IMG/dotred.png" alt="">
                            <span>วันหยุดประจำสัปดาห์</span>
                        </div>
                        <div class="Training">
                            <img src="../IMG/dotpurple.png" alt="">
                            <span>อบรม</span>
                        </div>
                        <div class="Lock">
                            <img src="../IMG/dotpink.png" alt="">
                            <span>ล็อกเหลี่ยม</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
<?php include('../includes/footer.php') ?>