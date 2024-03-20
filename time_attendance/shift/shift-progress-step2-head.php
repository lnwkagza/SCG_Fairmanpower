<?php
session_start();
session_regenerate_id(true);
//---------------------------------------------------------------------------------------
header("Cache-Control: no-cache, must-revalidate");
include("../database/connectdb.php");
include("../components-desktop/head/include/header.php")
//---------------------------------------------------------------------------------------
?>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/head/shift-table-now-head.css">

<!-- CSS Mobile -->
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/shift-progress.css">
<link rel="stylesheet" href="../assets/css/shift-now.css">
<link rel="stylesheet" href="node_modules\sweetalert2\dist\sweetalert2.css">

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="../assets/script/loader-normal.js"></script>

<!-- datatables -->
<link href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.bootstrap5.min.js"></script>

<?php
// ทำความสะอาดและป้องกันข้อมูล
$_SESSION['card_id'] = isset($_SESSION['card_id']) ? $_SESSION['card_id'] : '';  // ตรวจสอบว่ามีค่าหรือไม่
$_SESSION['card_id'] = trim($_SESSION['card_id']);  // ลบช่องว่างที่อาจเกิดขึ้นทั้งด้านหน้าและด้านหลังของข้อมูล
$_SESSION['card_id'] = preg_replace('/[^a-zA-Z0-9]/', '', $_SESSION['card_id']);  // ลบทุกอักขระที่ไม่ใช่ a-z, A-Z, และ 0-9
$_SESSION['card_id'] = htmlspecialchars($_SESSION['card_id'], ENT_QUOTES, 'UTF-8');  // แปลงสัญลักษณ์ HTML เพื่อป้องกัน XSS

if (isset($_SESSION["card_id"])) {

    //--------------------------------------------------------------------------------------------

    $sql = "SELECT * FROM work_format";
    $work_format = sqlsrv_query($conn, $sql);
    $work_formatdata = array();
    while ($row = sqlsrv_fetch_array($work_format, SQLSRV_FETCH_ASSOC)) {
        $work_formatdata[] = $row;
    }
    //--------------------------------------------------------------------------------------------------------------------------------------------------
    $query = "SELECT manager_card_id,firstname_thai,lastname_thai FROM manager INNER JOIN employee ON manager.manager_card_id = employee.card_id WHERE manager.card_id = ? ";
    $params = array($_SESSION['card_id']);
    $stmt = sqlsrv_query($conn, $query, $params);
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    $approver_firstname = $row['firstname_thai'];
    $approver_lastname = $row['lastname_thai'];
    $approver_card_id = $row['manager_card_id'];
    $fullname_approver = $approver_firstname . ' ' . $approver_lastname;

    // -----------------------------------------------------------------------------------------------------------------------------------------------
    $SELECTapprover = "SELECT card_id, firstname_thai, lastname_thai FROM employee WHERE permission_id = ? and card_id != ?";
    $paramsapprover = array('2', $approver_card_id);
    $stmtapprover = sqlsrv_query($conn, $SELECTapprover, $paramsapprover);

    $select_team = "SELECT * FROM sub_team WHERE head_card_id = ?";
    $dayoff_stmt = sqlsrv_prepare($conn, $select_team, array($_SESSION['card_id']));
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
                        WHERE employee.sub_team_id = ? ORDER BY employee.card_id ASC;";
    $dayoffteam_stmt = sqlsrv_prepare($conn, $select_dayoffteam, array(&$row['sub_team_id']));
    sqlsrv_execute($dayoffteam_stmt);
    $work_dayoffteam = array();  // Initialize the array to store results
    while ($row = sqlsrv_fetch_array($dayoffteam_stmt, SQLSRV_FETCH_ASSOC)) {
        $work_dayoffteam[] = $row;
    }
    $countwork_dayoffteam = count($work_dayoffteam) + 1;

    if (isset($_GET['month']) && isset($_GET['year'])) {
        $selected_month = $_GET['month'];
        $selected_year = $_GET['year'];
    } else {
        $selected_month = date('m');
        $selected_year = date('Y');
    }

    // Array mapping month numbers to Thai month names
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

    $select_shift_team = "SELECT scg_employee_id, firstname_thai, lastname_thai, employee.card_id, date, day, shift_main, shift_add, shift_lock
    FROM employee
    INNER JOIN transaction_work ON employee.card_id = transaction_work.card_id
    WHERE employee.sub_team_id = (SELECT sub_team_id FROM employee WHERE card_id = ?)
    AND FORMAT(date, 'MM-yyyy') = ? ORDER BY employee.card_id, date ASC;";

    $dayoff_team_stmt = sqlsrv_prepare($conn, $select_shift_team, array($_SESSION["card_id"], "{$selected_month}-{$selected_year}"));

    sqlsrv_execute($dayoff_team_stmt);
    $shiftTeamData = array();
    while ($row = sqlsrv_fetch_array($dayoff_team_stmt, SQLSRV_FETCH_ASSOC)) {
        $shiftTeamData[] = $row;
    }
}

?>
<script>
function updateDayoffOptions() {
    var newFormat = document.getElementById("work_format").value;
    var dayoffSelect = document.getElementById("dayoff");
    var workSelect = document.getElementById("work");

    dayoffSelect.innerHTML = "";
    workSelect.innerHTML = "";

    var defaultOption = document.createElement("option");
    defaultOption.text = "เลือกวันหยุด";
    dayoffSelect.add(defaultOption);

    <?php foreach ($work_formatdata as $row) : ?>
    if ("<?= $row["remark"] ?>" === newFormat) {
        var option = document.createElement("option");
        option.value = "<?= $row["day_off1"] ?> - <?= $row["day_off2"] ?>";
        option.text = "<?= $row["day_off1"] ?> - <?= $row["day_off2"] ?>";
        dayoffSelect.add(option);
    }
    <?php endforeach; ?>
}

function updateWorkOptions() {
    var selectedDayoff = document.getElementById("dayoff").value;
    var workSelect = document.getElementById("work");

    workSelect.innerHTML = "";

    <?php foreach ($work_formatdata as $row) : ?>
    if ("<?= $row["day_off1"] ?> - <?= $row["day_off2"] ?>" === selectedDayoff) {
        var optionWork = document.createElement("option");
        optionWork.value = "<?= $row["work_format_code"] ?>";
        optionWork.text = "<?= $row["format"] ?>";
        workSelect.add(optionWork);
    }
    <?php endforeach; ?>
}

document.getElementById("dayoff").addEventListener("change", updateWorkOptions);

function showEmployeeDayOff() {
    var employeeid = document.getElementById("employeeid").value;
    var remarkDisplay = document.getElementById("remarkDisplay");
    var dayoffDisplay = document.getElementById("dayoffDisplay");
    <?php foreach ($work_dayoffteam as $row2) : ?>
    if ("<?= $row2["card_id"] ?>" === employeeid) {
        console.log("รูปแบบการทำงานเดิม :", "<?= $row2["remark"] ?>");
        console.log("วันหยุดเดิม", " <?= $row2["day_off1"] ?> - <?= $row2["day_off2"] ?>");
        remarkDisplay.innerHTML = "<?= $row2["remark"] ?>";
        dayoffDisplay.innerHTML = "<?= $row2["day_off1"] ?> - <?= $row2["day_off2"] ?>";
    }
    <?php endforeach; ?>
}
</script>
<!-- --swal popup-- -->
<!-- --ปุ่มยืนยัน-- -->
<script type="text/javascript">
function mobile_editWork_submit() {
    Swal.fire({
        html: '<div style="font-weight: bold; font-size: 4vw;">ยืนยันการแก้ไขการทำงาน</div><br>' +
            '<img class="img" src="../IMG/question 1.png"></img>',
        padding: '2em',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#29ab29',
        showCancelButton: true,
        cancelButtonText: 'ยกเลิก',
        cancelButtonColor: '#e1574b',
        customClass: {
            confirmButtonText: 'swal2-confirm',
            cancelButtonText: 'swal2-cancel',
        },
    }).then((result) => {
        if (result.isConfirmed) {
            mobile_editWork_success(result);
        } else {
            swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
        }
    });

}

function mobile_editWork_success() {
    Swal.fire({
        html: '<div style="font-weight: bold; font-size: 4vw;">แก้ไขการทำงานสำเร็จ</div><br>' +
            '<img class="img" src="../IMG/check1.png"></img>',
        padding: '2em',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#29ab29',
        showConfirmButton: true,
        showCancelButton: false // ไม่แสดงปุ่มยกเลิก
    }).then((result) => {
        if (result.isConfirmed) {
            submitForm();
        } else {
            swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
        }
    });
}
</script>

<!-- --data table-- -->
<script>
$(document).ready(function() {
    new DataTable('#example', {
        "pageLength": 5,
        "lengthChange": false

    });

    $(".desktop-table").on("scroll", function() {
        // รับค่าตำแหน่งการเลื่อนขององค์ประกอบปัจจุบัน
        var currentScrollTop = $(this).scrollTop();

        // ซิงโครไนส์การเลื่อนขององค์ประกอบอื่น ๆ ให้ตรงกับตำแหน่งการเลื่อนขององค์ประกอบปัจจุบัน
        $(".desktop-table").not(this).scrollTop(currentScrollTop);
    });
});

function toggleEditWork() {
    var editWorkSection = document.querySelector('.display-editWork-step2');
    if (editWorkSection.style.display === 'none' || editWorkSection.style.display === '') {
        editWorkSection.style.display = 'block';
    } else {
        editWorkSection.style.display = 'none';
    }
}

function submitForm() {
    var form = document.getElementById("myForm");
    form.submit();
}

//desktop script
function new_dayoff() {
    var des_newwork = document.getElementById("desktop-newwork").value;
    var des_newdayoff = document.getElementById("desktop-newdayoff");
    var des_shift = document.getElementById("desktop-shift");

    des_newdayoff.innerHTML = "";
    des_shift.innerHTML = "";

    var defaultOption = document.createElement("option");
    defaultOption.text = "เลือกวันหยุด";
    des_newdayoff.add(defaultOption);

    <?php foreach ($work_formatdata as $row) : ?>
    if ("<?= $row["remark"] ?>" === des_newwork) {
        var option = document.createElement("option");
        option.value = "<?= $row["day_off1"] ?> - <?= $row["day_off2"] ?>";
        option.text = "<?= $row["day_off1"] ?> - <?= $row["day_off2"] ?>";
        des_newdayoff.add(option);
    }
    <?php endforeach; ?>
}

function new_shift() {
    var des_newdayoff = document.getElementById("desktop-newdayoff").value;
    var des_shift = document.getElementById("desktop-shift");

    des_shift.innerHTML = "";

    <?php foreach ($work_formatdata as $row) : ?>
    if ("<?= $row["day_off1"] ?> - <?= $row["day_off2"] ?>" === des_newdayoff) {
        var optionWork = document.createElement("option");
        optionWork.value = "<?= $row["work_format_code"] ?>";
        optionWork.text = "<?= $row["format"] ?>";
        des_shift.add(optionWork);
    }
    <?php endforeach; ?>
}

document.getElementById("desktop-newdayoff").addEventListener("change", new_shift);

function display_employee_old_dayoff() {
    var employeeid = document.getElementById("desktop-employeeid").value;
    var des_oldwork = document.getElementById("desktop-oldwork");
    var des_olddayoff = document.getElementById("desktop-olddayoff");
    var des_newwork = document.getElementById("desktop-newwork");

    var option1 = document.createElement("option");
    var option2 = document.createElement("option");
    var option3 = document.createElement("option");
    var option4 = document.createElement("option");

    option1.text = "เลือกรูปแบบ";
    option2.text = "ปกติ(STS)";
    option3.text = "ปกติ(CPAC)";
    option4.text = "กะ(STS)";

    <?php foreach ($work_dayoffteam as $row2) : ?>
    if ("<?= $row2["card_id"] ?>" === employeeid) {

        // console.log("รูปแบบการทำงานเดิม :", "<?= $row2["remark"] ?>");
        // console.log("วันหยุดเดิม", " <?= $row2["day_off1"] ?> - <?= $row2["day_off2"] ?>");

        des_newwork.appendChild(option1);
        des_newwork.appendChild(option2, "ปกติ(STS)");
        des_newwork.appendChild(option3, "ปกติ(CPAC)");
        des_newwork.appendChild(option4, "กะ(STS)");

        des_oldwork.value = "<?= $row2["remark"] ?>";
        des_olddayoff.value = "<?= $row2["day_off1"] ?> - <?= $row2["day_off2"] ?>";

    } else if (employeeid === 'select-member') {
        des_oldwork.value = "";
        des_olddayoff.value = "";

        des_newwork.remove(0);
        des_newwork.remove(1);
        des_newwork.remove(2);
        des_newwork.remove(3);
    }
    <?php endforeach; ?>
}

function edit_workformat_submit() {
    Swal.fire({
        title: "<strong>ยืนยันการแก้ไขรูปแบบการทำงาน</strong>",
        icon: "question",
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: `ตกลง`,
        cancelButtonText: `ยกเลิก`,
    }).then((result) => {
        if (result.isConfirmed) {
            edit_workformat_confirm(result);
        } else {
            swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
        }
    });

}

function edit_workformat_confirm() {
    Swal.fire({
        title: "<strong>แก้ไขรูปแบบการทำงานสำเร็จ</strong>",
        icon: "success",
        showCloseButton: true,
        focusConfirm: false,
        confirmButtonText: `ตกลง`,
    }).then((result) => {
        if (result.isConfirmed) {
            submit_form();
        }
    });
}

function submit_form() {
    let form = document.getElementById("desktop-form");
    form.action = '../processing/process_shift_edit_workformat_Request_head.php';
    form.method = 'POST';
    form.submit();
}
</script>
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
                                    <h2>แก้ไขรูปแบบการทำงาน</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor: default;">กะการทำงาน</a>
                                        </li>
                                        <li class=" breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            แก้ไขรูปแบบการทำงาน
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="progress-step">
                        <div class="progress-container step-2">
                            <div class="circle active"><a href="shift-progress-step1-head.php"><span>1</span><span
                                        class="title">จัดทีม</span></a></div>
                            <div class="circle active"><a href="shift-progress-step2-head.php">
                                    <span>2</span><span class="title">แก้ไขรูปแบบ</span></a></div>
                            <div class="circle"><a href="shift-progress-step3-head.php">
                                    <span>3</span><span class="title">ล็อกเหลี่ยม</span></a></div>
                            <div class="circle"><a href="shift-progress-step4-head.php">
                                    <span>4</span><span class="title">สลับกะ</span></a></div>
                            <div class="circle"><a href="shift-progress-step5-head.php">
                                    <span>5</span><span class="title">เปลี่ยนกะ</span></a></div>
                            <div class="circle"><a href="shift-progress-step6-head.php">
                                    <span>6</span><span class="title">เพิ่มกะ</span></a></div>
                        </div>
                    </div>
                    <div class="btn-action-step">
                        <button class="button-step" id="prev"
                            onclick="location.href='shift-progress-step1-head.php'">ก่อนหน้า</button>
                        <button class="button-step" id="next"
                            onclick="location.href='shift-progress-step3-head.php'">ถัดไป</button>
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
                                                            foreach ($shiftTeamData as $row) {
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
                                                                    echo "<td>-</td>";
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
                                                    style="background-color:#e1574b;"></button><span>วันหยุดประจำสัปดาห์</span>
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

                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30" id="box2">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <h2 class="mt-15 mb-10 h2 text-left">
                                    แก้ไขรูปแบบการทำงาน
                                </h2>
                                <hr>
                                <section>
                                    <form id="desktop-form">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>เลือกพนักงาน</label>
                                                    <select name="employeeid" id="desktop-employeeid"
                                                        class="custom-select form-control"
                                                        onchange="display_employee_old_dayoff()">
                                                        <option value="select-member">เลือกสมาชิกในทีม</option>
                                                        <?php
                                                        foreach ($work_dayoffteam as $rs_emp) {
                                                            echo '<option value="' . $rs_emp['card_id'] . '">' . $rs_emp['prefix_thai'] . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>วันที่มีผล (โปรดเลือกวันจันทร์เท่านั้น)</label>
                                                    <input type="date" name="startDate" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>วันที่สิ้นสุด (โปรดเลือกวันอาทิตย์เท่านั้น)</label>
                                                    <input type="date" name="endDate" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>รูปแบบการทำงานเดิม</label>
                                                    <input type="text" class="form-control" id="desktop-oldwork"
                                                        style="background-color:transparent" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>รูปแบบการทำงานใหม่</label>
                                                    <select name="work_format" id="desktop-newwork"
                                                        class="custom-select  form-control" onchange="new_dayoff()">
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>วันหยุดเดิม</label>
                                                    <input type="text" class="form-control" id="desktop-olddayoff"
                                                        style="background-color:transparent" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>วันหยุดใหม่</label>
                                                    <select name="dayoff" id="desktop-newdayoff"
                                                        class="custom-select form-control"
                                                        onchange="new_shift()"></select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>เลือกรูปแบบกะ</label>
                                                    <select class="custom-select form-control" name="work"
                                                        id="desktop-shift"></select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>โปรดระบุเหตุผล</label>
                                                    <input type="text" class="form-control" name="detail"
                                                        id="desktop-detail">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="col-sm-12 text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-primary"
                                                onclick="edit_workformat_submit()">แก้ไขรูปแบบการทำงาน</button>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mobile">
        <div class="navbar-BS">
            <div class="div-span">
                <span>แก้ไขรูปแบบการทำงาน</span>
            </div>
        </div>
        <div class="container">
            <!-- --ส่วนของ progress bar-- -->
            <div class="container-progress">
                <div class="progress-container step-2">
                    <div class="circle active"><a href="shift-progress-step1-head.php"><span>1</span><span
                                class="title">จัดทีม</span></a></div>
                    <div class="circle active"><a href="shift-progress-step2-head.php">
                            <span>2</span><span class="title">แก้ไขรูปแบบ</span></a></div>
                    <div class="circle"><a href="shift-progress-step3-head.php">
                            <span>3</span><span class="title">ล็อกเหลี่ยม</span></a></div>
                    <div class="circle"><a href="shift-progress-step4-head.php">
                            <span>4</span><span class="title">สลับกะ</span></a></div>
                    <div class="circle"><a href="shift-progress-step5-head.php">
                            <span>5</span><span class="title">เปลี่ยนกะ</span></a></div>
                    <div class="circle"><a href="shift-progress-step6-head.php">
                            <span>6</span><span class="title">เพิ่มกะ</span></a></div>
                </div>
            </div>
            <!-- --ส่วนของการแก้ไขชุดการทำงาน-- -->
            <div class="container-editWork">
                <div class="container-table-team">
                    <div class="topic-table-now">
                        <span>ตารางกะการทำงานของทีม ณ ปัจจุบัน</span>
                    </div>
                    <div class="table-shift">
                        <div class="display-monthNow">
                            <a href="?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>">
                                <button id="prevMonth"><img src="../IMG/arrowleft.png" alt=""></button>
                            </a>
                            <div class="current" id="currentDate">
                                <?= $showmonththai ?>
                                <?= $showyearthai ?>
                            </div>
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
                                            <td class="team" rowspan="<?= $countwork_dayoffteam ?>">
                                                <span class="team-name-toggle"
                                                    data-short="<?= htmlentities(mb_strimwidth($nameteam, 0, 15, '...')) ?>"
                                                    data-full="<?= htmlentities($nameteam) ?>">
                                                    <?= htmlentities(mb_strimwidth($nameteam, 0, 15, '...')) ?>
                                                </span>
                                            </td>
                                            <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                const teamNameSpan = document.querySelector(
                                                    '.team-name-toggle');

                                                teamNameSpan.addEventListener('click', function() {
                                                    const shortName = this.getAttribute(
                                                        'data-short');
                                                    const fullName = this.getAttribute('data-full');

                                                    if (this.textContent === shortName) {
                                                        this.textContent = fullName;
                                                    } else {
                                                        this.textContent = shortName;
                                                    }
                                                });
                                            });
                                            </script>

                                        </tr>

                                        <?php
                                        foreach ($work_dayoffteam as $rs_emp) { ?>
                                        <tr>
                                            <td class="id">
                                                <?php echo $rs_emp['scg_employee_id']; ?>
                                            </td>
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
                                            foreach ($shiftTeamData as $row) {
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
                                                    echo "<td>-</td>";
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
                <div class="edit-dataSet">
                    <button onclick="toggleEditWork()">
                        <img src="../IMG/edit1.png" alt="">
                        <span> &nbsp;แก้ไขรูปแบบการทำงาน</span>
                    </button>
                </div>
                <div class="display-editWork-step2">
                    <form id="myForm" action="../processing/process_shift_edit_workformat_Request_head.php"
                        method="POST">
                        <div class="select-nameEm">
                            <span>เลือกชื่อพนักงาน</span>
                            <select name="employeeid" id="employeeid" class="js-example-basic-single"
                                onchange="showEmployeeDayOff()">
                                <option value="">เลือกสมาชิกในทีม</option>
                                <?php
                                foreach ($work_dayoffteam as $rs_emp) {
                                    echo '<option value="' . $rs_emp['card_id'] . '">' . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="display-date-start">
                            <span>วันที่มีผล (โปรดเลือกวันจันทร์เท่านั้น)</span>
                            <input type="date" name="startDate" id="startDate">
                        </div>
                        <div class="display-date-start">
                            <span>วันที่สิ้นสุด (โปรดเลือกวันอาทิตย์เท่านั้น)</span>
                            <input type="date" name="endDate" id="endDate">
                        </div>
                        <div class="display-format">
                            <div class="display-old">
                                <span>รูปแบบการทำงานเดิม</span>
                                <span class="detailSpan" id="remarkDisplay"> - </span>
                            </div>
                            <div class="display-new">
                                <span>รูปแบบการทำงานใหม่</span>
                                <select name="work_format" id="work_format" class="newFormat"
                                    onchange="updateDayoffOptions()">
                                    <option value="">เลือกรูปแบบ</option>
                                    <option value="ปกติ(STS)">ปกติ(STS)</option>
                                    <option value="ปกติ(CPAC)">ปกติ(CPAC)</option>
                                    <option value="กะ(STS)">กะ(STS)</option>
                                </select>
                            </div>
                        </div>
                        <div class="display-dayOff">
                            <div class="display-day-old">
                                <span>วันหยุดเดิม</span>
                                <span class="detailSpan" id="dayoffDisplay"> - </span>
                            </div>
                            <div class="display-day-new">
                                <span>วันหยุดใหม่</span>
                                <select name="dayoff" id="dayoff" class="dayoff"
                                    onchange="updateWorkOptions()"></select>
                            </div>
                        </div>
                        <div class="display-shift">
                            <span>เลือกรูปแบบกะ</span>
                            <select name="work" id="work"></select>
                        </div>
                        <div class="display-detail-change">
                            <span>โปรดระบุเหตุผล</span>
                            <input class="add-detail" name="detail" id="detail"></input>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-6">
                            <input type="submit" value="ยืนยัน" onclick="mobile_editWork_submit()" class="btnConfirm">
                        </div>
                    </div>
                </div>
            </div>
            <!-- --ปุ่ม action เลื่อนไปข้างหน้า เลื่อนไปข้างหลัง-- -->
            <div class="btn-action-step">
                <button class="btn" id="prev" onclick="location.href='shift-progress-step1-head.php'">Prev</button>
                <button class="btn" id="next" onclick="location.href='shift-progress-step3-head.php'">Next</button>
            </div>
        </div>
    </div>
</body>
<?php include('../includes/footer.php') ?>