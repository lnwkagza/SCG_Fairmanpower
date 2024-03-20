<?php
session_start();
session_regenerate_id(true);
//---------------------------------------------------------------------------------------
header("Cache-Control: no-cache, must-revalidate");
include("../database/connectdb.php");
include('../components-desktop/admin/include/header.php');
//---------------------------------------------------------------------------------------
?>
<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/admin/shift-switch-admin.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/shift-progress.css">
<link rel="stylesheet" href="../assets/css/shift-switch.css">
<link rel=" stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="../assets/script/loader-normal.js"></script>

<!-- datatables -->
<link href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.bootstrap5.min.js"></script>

<?php

// ทำความสะอาดและป้องกันข้อมูล
$_SESSION['card_id'] = trim($_SESSION['card_id']);  // ลบช่องว่างที่อาจเกิดขึ้นทั้งด้านหน้าและด้านหลังของข้อมูล
$_SESSION['card_id'] = preg_replace('/[^a-zA-Z0-9]/', '', $_SESSION['card_id']);  // ลบทุกอักขระที่ไม่ใช่ a-z, A-Z, และ 0-9
$_SESSION['card_id'] = htmlspecialchars($_SESSION['card_id'], ENT_QUOTES, 'UTF-8');  // แปลงสัญลักษณ์ HTML เพื่อป้องกัน XSS

if (isset($_SESSION["card_id"])) {

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

    // Fetch employee data
    // Fetch shift types
    $shift_select = "SELECT * FROM shift_type WHERE shift_type_id IN ('SA01', 'SB01', 'SC01')";
    $shift_stmt = sqlsrv_prepare($conn, $shift_select);
    sqlsrv_execute($shift_stmt);
    $shift_select_data = array();

    while ($row = sqlsrv_fetch_array($shift_stmt, SQLSRV_FETCH_ASSOC)) {
        $shift_select_data[] = $row;
    }

    // Fetch approver data
    $query = "SELECT manager_card_id, firstname_thai, lastname_thai, employee_email, employee_image FROM manager INNER JOIN employee ON manager.manager_card_id = employee.card_id WHERE manager.card_id = ? ";
    $params = array($_SESSION['card_id']);
    $stmt = sqlsrv_query($conn, $query, $params);
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    $approver_firstname = $row['firstname_thai'];
    $approver_lastname = $row['lastname_thai'];
    $approver_card_id = $row['manager_card_id'];
    $fullname_approver = $approver_firstname . ' ' . $approver_lastname;

    // Fetch inspector options
    $SELECT_approver = "SELECT card_id, prefix_thai, firstname_thai, lastname_thai, employee_email, employee_image FROM employee WHERE permission_id = ? and card_id != ?";
    $params_approver = array('2', $approver_card_id);
    $stmt_approver = sqlsrv_query($conn, $SELECT_approver, $params_approver);

    $query = "SELECT card_id, scg_employee_id, firstname_thai, lastname_thai, remark, day_off1, day_off2, sub_team.name AS TeamName FROM employee 
            INNER JOIN work_format ON employee.work_format_code = work_format.work_format_code
            LEFT JOIN sub_team ON employee.sub_team_id = sub_team.sub_team_id 
            WHERE card_id = ? OR cost_center_organization_id IN (SELECT cost_center_organization_id FROM employee WHERE card_id = ?)";
    $params = array($_SESSION['card_id'], $_SESSION['card_id']);

    $shiftteam = sqlsrv_query($conn, $query, $params);
    $shiftteamdata = array();

    while ($row = sqlsrv_fetch_array($shiftteam, SQLSRV_FETCH_ASSOC)) {
        $shiftteamdata[] = $row;
    }
    sqlsrv_free_stmt($shiftteam);

    $options = '';
    foreach ($shiftteamdata as $rs_emp) {
        $options .= '<option value="' . $rs_emp['card_id'] . '">' . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai'] . '</option>';
    }

    $options_inspector = '';
    while ($rs_emp = sqlsrv_fetch_array($stmt_approver, SQLSRV_FETCH_ASSOC)) {
        $options_inspector .= '<option value="' . $rs_emp['card_id'] . '">' . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai'] . '</option>';
    }

    $options_shift = '';
    foreach ($shift_select_data as $rs_shift) {
        $options_shift .= '<option value="' . $rs_shift['shift_type_id'] . '">' . $rs_shift['symbol'] . '</option>';
    }


    // SQL query to select shift data for a specific user
    $select_shift_data_query = "SELECT 
                employee1.prefix_thai AS employee1_prefix,
                employee1.firstname_thai AS employee1_firstname,
                employee1.lastname_thai AS employee1_lastname,
                employee1.employee_email AS employee1_email,
                employee1.employee_image AS employee1_image,
                employee2.prefix_thai AS employee2_prefix,
                employee2.firstname_thai AS employee2_firstname,
                employee2.lastname_thai AS employee2_lastname,
                employee2.employee_email AS employee2_email,
                employee2.employee_image AS employee2_image,
                employee3.prefix_thai AS employee3_prefix,
                employee3.firstname_thai AS employee3_firstname,
                employee3.lastname_thai AS employee3_lastname,
                employee3.employee_email AS employee3_email,
                employee3.employee_image AS employee3_image,
                shift_switch.date,
                shift_switch.input_timestamp,
                shift_type1.symbol AS employee1_old_shift_symbol,
                shift_type2.symbol AS employee2_old_shift_symbol,
                shift_type3.symbol AS employee3_old_shift_symbol,
                new_shift_type1.symbol AS employee1_new_shift_symbol,
                new_shift_type2.symbol AS employee2_new_shift_symbol,  -- Corrected alias
                new_shift_type3.symbol AS employee3_new_shift_symbol,
                shift_switch.request_detail
            FROM 
                shift_switch
            
            INNER JOIN 
                employee AS employee1 ON shift_switch.employee_1 = employee1.card_id
            INNER JOIN 
                employee AS employee2 ON shift_switch.employee_2 = employee2.card_id
            INNER JOIN 
                employee AS employee3 ON shift_switch.employee_3 = employee3.card_id
            
            INNER JOIN 
                shift_type AS shift_type1 ON shift_switch.old_shift_1 = shift_type1.shift_type_id
            INNER JOIN 
                shift_type AS shift_type2 ON shift_switch.old_shift_2 = shift_type2.shift_type_id
            INNER JOIN 
                shift_type AS shift_type3 ON shift_switch.old_shift_3 = shift_type3.shift_type_id
                    
            INNER JOIN 
                shift_type AS new_shift_type1 ON shift_switch.new_shift_1 = new_shift_type1.shift_type_id
            INNER JOIN 
                shift_type AS new_shift_type2 ON shift_switch.new_shift_2 = new_shift_type2.shift_type_id
            INNER JOIN 
                shift_type AS new_shift_type3 ON shift_switch.new_shift_3 = new_shift_type3.shift_type_id
            
            WHERE 
                shift_switch.request_card_id = ? ORDER BY shift_switch.date ASC;";

    // Prepare the SQL statement
    $shift_data_stmt = sqlsrv_prepare($conn, $select_shift_data_query, array($_SESSION["card_id"]));

    // Execute the SQL statement
    sqlsrv_execute($shift_data_stmt);

    // Fetch shift data and store it in an array
    $shiftDataApprove = array();
    while ($row = sqlsrv_fetch_array($shift_data_stmt, SQLSRV_FETCH_ASSOC)) {
        $shiftDataApprove[] = $row;
    }

    $sql = "SELECT sub_team_id FROM employee WHERE card_id = ?";
    $result = sqlsrv_query($conn, $sql, array($_SESSION['card_id']));

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
            $select_shift_team = "SELECT 
                                scg_employee_id,
                                prefix_thai,
                                firstname_thai, 
                                lastname_thai,
                                card_id,
                                date, 
                                day, 
                                shift_main,
                                shift_add, 
                                shift_lock
                                FROM employee
                                INNER JOIN transaction_work ON employee.card_id = transaction_work.card_id
                                WHERE employee.sub_team_id = (
                                    SELECT sub_team_id
                                    FROM employee
                                    WHERE card_id = ?
                                )
                                ORDER BY employee.card_id ASC;";

            $shift_team_stmt = sqlsrv_prepare($conn, $select_shift_team, array(&$_SESSION['card_id']));
            sqlsrv_execute($shift_team_stmt);

            $shiftData = array(); // Corrected array name

            while ($row = sqlsrv_fetch_array($shift_team_stmt, SQLSRV_FETCH_ASSOC)) {
                $shiftData[] = $row; // Corrected array name
            }

            //------------------------------------------------------------------------------------------

            $select_team = "SELECT * FROM sub_team WHERE head_card_id = ?";
            $dayoff_stmt = sqlsrv_prepare($conn, $select_team, array(&$_SESSION['card_id']));
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

            $select_shift_team = "SELECT scg_employee_id, firstname_thai, lastname_thai, employee.card_id, date, day, shift_main, shift_add, shift_lock
            FROM employee
            INNER JOIN transaction_work ON employee.card_id = transaction_work.card_id
            WHERE employee.sub_team_id = (SELECT sub_team_id FROM employee WHERE card_id = ?)
            AND FORMAT(date, 'MM-yyyy') = ? ORDER BY employee.card_id, date ASC;";

            $dayoff_team_stmt = sqlsrv_prepare($conn, $select_shift_team, array($_SESSION['card_id'], "{$selected_month}-{$selected_year}"));

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
            $dayoffteam_stmt = sqlsrv_prepare($conn, $select_dayoffteam, array(&$_SESSION['card_id']));
            sqlsrv_execute($dayoffteam_stmt);
            $work_dayoffteam = array();  // Initialize the array to store results
            while ($row = sqlsrv_fetch_array($dayoffteam_stmt, SQLSRV_FETCH_ASSOC)) {
                $work_dayoffteam[] = $row;
            }
            $countwork_dayoffteam = count($work_dayoffteam) + 1;

            $select_shift_team = "SELECT 
            scg_employee_id,
            prefix_thai,
            firstname_thai, 
            lastname_thai, 
            employee.card_id, 
            date, 
            day, 
            shift_main, 
            shift_add, 
            shift_lock
            FROM employee
            INNER JOIN transaction_work ON employee.card_id = transaction_work.card_id
            WHERE employee.card_id = ?
            AND FORMAT(date, 'MM-yyyy') = ? ORDER BY date ASC;";

            $shift_team_stmt = sqlsrv_prepare($conn, $select_shift_team, array($_SESSION['card_id'], "{$selected_month}-{$selected_year}"));
            sqlsrv_execute($shift_team_stmt);
            $shiftData = array();
            while ($row = sqlsrv_fetch_array($shift_team_stmt, SQLSRV_FETCH_ASSOC)) {
                $shiftData[] = $row;
            }
        }
    }
} else {
    echo "No card_id in session.";
}


?>

<!-- --swal popup-- -->
<!-- --ปุ่มยืนยัน-- -->
<script type="text/javascript">
    function myFunction() {
        Swal.fire({
            html: '<div style="font-weight: bold; font-size: 4vw;">สลับกะการทำงานสำเร็จ</div><br>' +
                '<img class="img" src="../IMG/check1.png"></img>',
            padding: '2em',
            showConfirmButton: false, // ไม่แสดงปุ่มตกลง
            showCancelButton: false // ไม่แสดงปุ่มยกเลิก
        }).then((result) => {
            if (result.isConfirmed) {} else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");

            }
        });

    }

    function myFunction2() {
        Swal.fire({
            html: '<div style="font-weight: bold; font-size: 20px;">ยืนยันการยกเลิกคำขอ</div><br>' +
                '<img src="../IMG/question 1.png" style="width:80px; margin-top: -10px; height:80px;"></img>',
            padding: '2em',
            confirmButtonText: 'ตกลง',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#29ab29',
            cancelButtonColor: '#e1574b',
            showCancelButton: true,

            customClass: {
                confirmButtonText: 'swal2-confirm',
                cancelButtonText: 'swal2-cancel',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'day-off-change-history-admin.php';
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });
    }

    function createEmployeeForm(employeeCount) {
        var formContent =
            '<form id="myForm" action="../processing/process_shift_insert_shift_switch_employee_admin.php" method="POST">' +
            '<div class="topic">เพิ่มรายการใหม่</div><br>' +
            '<div class="dateFix"><span>วันที่</span><input id="date" type="date"></div>';

        for (var i = 1; i <= employeeCount; i++) {
            formContent +=
                '<div class="content-wrapper">' + // เพิ่มคลาส .content-wrapper เพื่อครอบทั้ง .content1 และ .content3
                '<div class="content1">' +
                '<div class="name"><span>สมาชิกคนที่ ' + i + '</span><select name="employeeid' + i + '" id="employeeid' +
                i +
                '" class="js-example-basic-single" onchange="showEmployeeDayOff(' + i + ')">' +
                '<option value="">ชื่อ-สกุล</option>' +
                '<?php echo $options; ?>' +
                '</select></div>' +
                '<div class="team"><span>ทีม</span><br><span class="display-team" id="TeamDisplay' + i + '"></span></div>' +
                '</div>' +
                '<div class="content3">' +
                '<div class="shift-old"><span>กะเดิม</span><span class="detailSpan" id="remarkDisplay' + i +
                '"> - </span></div>' +
                '<div class="shift-new"><span>กะใหม่</span><select name="shiftNew' + i + '" id="shiftNew' + i + '">' +
                '<option value="" disabled selected>เลือกกะ</option>' +
                '<?php echo $options_shift; ?>' +
                '</select></div>' +
                '</div>' +
                '</div>'; // ปิดคลาส .content-wrapper

        }

        formContent +=
            '<div class="content5">เหตุผล</div>' +
            '<div><input name="request_detail" class="detail" type="text" placeholder="กรอกเหตุผล"></div>' +
            '<div class="content6">' +
            '<div class="inspector"><span>ผู้ตรวจสอบ (ถ้ามี)</span><select name="inspector_id" id="inspector_id"><option value="">เลือกผู้ตรวจสอบ</option>' +
            '<?php echo $options_inspector; ?>' +
            '</select></div>' +
            '<div class="head"><span>หัวหน้า</span><span class="display-head" id="approver_id"><?php echo $fullname_approver; ?></span></div>' +
            '</div></form>';

        return formContent;
    }

    function mobile_add_switchShift() {
        var employeeCount = 3;
        var popupContent = createEmployeeForm(employeeCount);

        Swal.fire({
            html: popupContent,
            padding: '2em',
            confirmButtonText: 'ตกลง',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#29ab29',
            cancelButtonColor: '#e1574b',
            showCancelButton: true,
            customClass: {
                confirmButtonText: 'swal2-confirm',
                cancelButtonText: 'swal2-cancel',
                popup: 'custom-popup-class',
                container: 'custom-swal-container',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form using AJAX
                var formData = new FormData(document.getElementById("myForm"));
                $.ajax({
                    type: "POST",
                    url: "../processing/process_shift_insert_shift_switch_employee_admin.php",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        handleFormSubmissionSuccess();
                    },
                    error: function() {
                        handleFormSubmissionError();
                    }
                });
            } else {
                Swal.fire("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });
    }

    // Additional functions for handling form submission
    function handleFormSubmissionSuccess() {
        // Handle success logic, e.g., show a success message
        Swal.fire({
            html: '<div style="font-weight: bold; font-size: 4vw;">เพิ่มกะการทำงานสำเร็จ</div><br>' +
                '<img class="img" src="../IMG/check1.png"></img>',
            padding: '2em',
            confirmButtonText: 'ตกลง',
            confirmButtonColor: '#29ab29',
            showCancelButton: false
        });
    }

    function handleFormSubmissionError() {
        // Handle error logic, e.g., show an error message
        Swal.fire("Error submitting the form. Please try again.");
    }

    function showEmployeeDayOff(index) {
        var employeeid = document.getElementById("employeeid" + index).value;
        var remarkDisplay = document.getElementById("remarkDisplay" + index);
        var TeamDisplay = document.getElementById("TeamDisplay" + index);

        <?php foreach ($shiftteamdata as $row) : ?>
            if ("<?= $row['card_id'] ?>" === employeeid) {
                remarkDisplay.innerHTML = "<?= $row['remark'] ?>";
                TeamDisplay.innerHTML = "<?= $row['TeamName'] ?>";
            }
        <?php endforeach; ?>
    }

    function submitForm() {
        var form = document.getElementById("myForm");
        form.submit();
    }

    function mobile_add_switchShift_submit() {
        Swal.fire({
            html: '<div style="font-weight: bold; font-size: 4vw;">ยืนยันสลับกะการทำงานหรือไม่</div>' +
                '<div style="font-weight: bold; font-size: 3vw;">ขณะนี้กะการทำงานของทีม ครบ 3 กะ</div><br>' +
                '<img class="img" src="../IMG/question 1.png"></img>',
            padding: '2em',
            confirmButtonText: 'ตกลง',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#29ab29',
            cancelButtonColor: '#e1574b',
            showCancelButton: true,

            customClass: {
                confirmButtonText: 'swal2-confirm',
                cancelButtonText: 'swal2-cancel',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                myFunction();
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });
    }

    function myFunction5() {
        Swal.fire({
            html: '<div style="font-weight: bold; font-size: 20px;">ลบรายการสำเร็จ</div><br>' +
                '<img src="../IMG/check1.png" style="width:80px; margin-top: 2px;  height:80px;"></img>',
            padding: '2em',
            showConfirmButton: false, // ไม่แสดงปุ่มตกลง
            showCancelButton: false // ไม่แสดงปุ่มยกเลิก
        }).then((result) => {
            if (result.isConfirmed) {} else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");

            }
        });

    }

    function toggleTable(tableType) {
        if (tableType === 'employee') {
            document.querySelector('.display-table-employee').style.display = '';
            document.querySelector('.display-table-admin').style.display = 'none';
            document.querySelector('.search-em').style.display = 'block'; // แสดง search-em เมื่อกด btn-employee
        } else if (tableType === 'admin') {
            document.querySelector('.display-table-admin').style.display = '';
            document.querySelector('.display-table-employee').style.display = 'none';
            document.querySelector('.search-em').style.display = 'none'; // ซ่อน search-em เมื่อกด btn-admin
        }
    }
</script>

<!-- --data table-- -->
<!-- --เปลี่ยนภาษาอังกฤษของ data table-- -->
<script>
    $(document).ready(function() {
        //data-table
        new DataTable('#example', {
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "ทั้งหมด"]
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
            },
            "pageLength": 5,
            "lengthChange": false

        });
    });
</script>
<!-- --current วัน -- -->

<!-- desktop script -->
<script>
    $(document).ready(function() {
        //data-table
        new DataTable('#table3', {
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "ทั้งหมด"]
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

    function show_form() {
        const box3 = document.getElementById("box3");
        box3.style.display = "";
    }

    function close_form() {
        const box3 = document.getElementById("box3");
        box3.style.display = "none";
    }

    function display_employee_info(i) {
        var employeeid = document.getElementById("desktop-employeeid" + i).value;
        var des_oldshift = document.getElementById("desktop-oldshift" + i);
        var des_team = document.getElementById("desktop-team" + i);

        <?php foreach ($shiftteamdata as $row) : ?>
            if ("<?= $row['card_id'] ?>" === employeeid) {
                des_oldshift.value = "<?= $row['remark'] ?>";
                des_team.value = "<?= $row['TeamName'] ?>";
            } else if (employeeid === 'select-member') {
                des_oldshift.value = "";
                des_team.value = "";
            }
        <?php endforeach; ?>
    }

    function add_switch_submit() {
        Swal.fire({
            title: "<strong>ยืนยันคำขอเพิ่มรายการสลับกะการทำงานหรือไม่</strong>",
            icon: "question",
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: `ตกลง`,
            cancelButtonText: `ยกเลิก`,
        }).then((result) => {
            if (result.isConfirmed) {
                add_switch_confirm(result);
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });

    }

    function add_switch_confirm() {
        Swal.fire({
            title: "<strong>ยื่นคำขอเพิ่มรายการขอสลับกะการทำงานสำเร็จ</strong>",
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
        form.action = '../processing/process_shift_insert_shift_switch_employee_admin.php';
        form.method = 'POST';
        form.submit();
    }

    function switch_table(button) {
        if (button === 'button-switch1') {
            document.querySelector('.admin').style.display = '';
            document.querySelector('.employee').style.display = 'none';
            document.querySelector('#button-switch1').style.display = 'none';
            document.querySelector('#button-switch2').style.display = '';
        } else if (button === 'button-switch2') {
            document.querySelector('.admin').style.display = 'none';
            document.querySelector('.employee').style.display = '';
            document.querySelector('#button-switch1').style.display = '';
            document.querySelector('#button-switch2').style.display = 'none';
        }
    }

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
        <?php include('../components-desktop/admin/include/sidebar.php'); ?>
        <?php include('../components-desktop/admin/include/navbar.php'); ?>
        <div class="main-container">
            <div class="pd-ltr-20 xs-pd-20-10">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <h2>สลับกะการทำงาน</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor: default;">กะการทำงาน</a>
                                        </li>
                                        <li class=" breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            สลับกะการทำงาน
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="progress-step">
                        <div class="progress-container step-4">
                            <div class="circle active"><a href="shift-progress-step1-admin.php"><span>1</span><span class="title">จัดทีม</span></a></div>
                            <div class="circle active"><a href="shift-progress-step2-admin.php">
                                    <span>2</span><span class="title">แก้ไขรูปแบบ</span></a></div>
                            <div class="circle active"><a href="shift-progress-step3-admin.php">
                                    <span>3</span><span class="title">ล็อกเหลี่ยม</span></a></div>
                            <div class="circle active"><a href="shift-progress-step4-admin.php">
                                    <span>4</span><span class="title">สลับกะ</span></a></div>
                            <div class="circle"><a href="shift-progress-step5-admin.php">
                                    <span>5</span><span class="title">เปลี่ยนกะ</span></a></div>
                            <div class="circle"><a href="shift-progress-step6-admin.php">
                                    <span>6</span><span class="title">เพิ่มกะ</span></a></div>
                        </div>
                    </div>
                    <div class="btn-action-step">
                        <button class="button-step" id="prev" onclick="location.href='shift-progress-step3-admin.php'">ก่อนหน้า</button>
                        <button class="button-step" id="next" onclick="location.href='shift-progress-step5-admin.php'">ถัดไป</button>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-6 col-sm-12 mb-30" id="box1">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="pt-3 pb-3">
                                    <div class="bar">
                                        <div class="button-switch-table">
                                            <button id="button-switch1" style="display:none;" onclick=" switch_table('button-switch1')">ดูกะการทำงานของตัวเอง</button>
                                            <button id="button-switch2" onclick=" switch_table('button-switch2')">ดูกะการทำงานของพนักงาน</button>
                                        </div>
                                        <button id="prevMonth" onclick="javascript:location.href='?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>'"><img src="
                                            ../IMG/arrowleft.png" alt=""></button>
                                        <div class="current" id="currentDate">
                                            <?= $showmonththai . " " . $showyearthai ?>
                                        </div>
                                        <button id="nextMonth" onclick="javascript:location.href='?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>'"><img src="../IMG/arrowright.png" alt=""></button>
                                    </div>

                                    <div class="desktop-table-container admin">
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
                                                                        <img src="<?php echo (!empty($rs_emp['employee_image'])) ? '../../admin/uploads_img/' . $rs_emp['employee_image'] : '../IMG/user.png'; ?>" class="border-radius-100 shadow" width="40" height="40" alt="">
                                                                    </div>
                                                                    <div>
                                                                        <b><?php echo $rs_emp['prefix_thai'] . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai']; ?></b><br>
                                                                        <a class="text-primary"><?php echo $rs_emp['employee_email'] ?></a>
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
                                                            $dayOfWeekThai = convert_day_admin($dayOfWeek);

                                                            echo "<th>$dayOfWeekThai</th>";
                                                        }
                                                        // Function to convert English day abbreviation to Thai
                                                        function convert_day_admin($dayOfWeek)
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
                                                        } ?>
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
                                    </div>
                                    <div class="desktop-table-container employee" style="display:none;">
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
                                                                        <img src="<?php echo (!empty($rs_emp['employee_image'])) ? '../../admin/uploads_img/' . $rs_emp['employee_image'] : '../IMG/user.png'; ?>" class="border-radius-100 shadow" width="40" height="40" alt="">
                                                                    </div>
                                                                    <div>
                                                                        <b><?php echo $rs_emp['prefix_thai'] . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai']; ?></b><br>
                                                                        <a class="text-primary"><?php echo $rs_emp['employee_email'] ?></a>
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
                                                            $dayOfWeekThai = convert_day_employee($dayOfWeek);

                                                            echo "<th>$dayOfWeekThai</th>";
                                                        }
                                                        // Function to convert English day abbreviation to Thai
                                                        function convert_day_employee($dayOfWeek)
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
                                                        } ?>
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
                                                <button style="background-color:#ff5643;"></button><span>วันหยุดประจำสัปดาห์</span>
                                            </div>
                                        </div>
                                        <div id="status2">
                                            <div>
                                                <button style="background-color:#02a426;"></button><span>วันหยุดชดเชย</span>
                                            </div>
                                            <div>
                                                <button style="background-color:#616061;"></button><span>วันหยุดนักขัตฤกษ์</span>
                                            </div>
                                            <div>
                                                <button style="background-color:#9747ff;"></button><span>อบรม</span>
                                            </div>
                                        </div>
                                        <div id="status3">
                                            <div>
                                                <button style="background-color:#00b0db;"></button><span>ลาพักร้อน</span>
                                            </div>
                                            <div>
                                                <button style="background-color:#a3a3a3;"></button><span>ลาป่วย</span>
                                            </div>
                                            <div>
                                                <button style="background-color:#ff439d;"></button><span>ล็อกเหลี่ยม</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8 col-md-6 col-sm-12 mb-30" id="box2">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="bar-2">
                                    <div class="button-show-form" onclick="show_form()">
                                        <i class="fa-solid fa-circle-plus"></i>
                                        <label>สลับกะการทำงาน</label>
                                    </div>
                                </div>

                                <div class="desktop-data-table-container table3" id="desktop-table3">
                                    <table id="table3" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ผู้ยื่นอนุมัติ</th>
                                                <th>วันที่ยื่นอนุมัติ</th>
                                                <th>วันที่มีผล</th>
                                                <th>กะเดิม</th>
                                                <th>กะใหม่</th>
                                                <th>เหตุผล</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($shiftDataApprove as $row) : ?>
                                                <?php $employeeData = [
                                                    ['firstname' => $row['employee1_firstname'], 'lastname' => $row['employee1_lastname'], 'old_shift' => $row['employee1_old_shift_symbol'], 'new_shift' => $row['employee1_new_shift_symbol']],
                                                    ['firstname' => $row['employee2_firstname'], 'lastname' => $row['employee2_lastname'], 'old_shift' => $row['employee2_old_shift_symbol'], 'new_shift' => $row['employee2_new_shift_symbol']],
                                                    ['firstname' => $row['employee3_firstname'], 'lastname' => $row['employee3_lastname'], 'old_shift' => $row['employee3_old_shift_symbol'], 'new_shift' => $row['employee3_new_shift_symbol']],
                                                ]; ?>

                                                <?php foreach ($employeeData as $employee) : ?>
                                                    <tr>
                                                        <td style="text-align:left;">
                                                            <?php echo $employee['firstname'] . ' ' . $employee['lastname']; ?>
                                                        </td>
                                                        <td style="text-align:center;">
                                                            <?php echo $row['input_timestamp']->format('d-m-Y'); ?>
                                                        </td>
                                                        <td style="text-align:center;">
                                                            <?php echo $row['date']->format('d-m-Y'); ?>
                                                        </td>
                                                        <td style="text-align:center;">
                                                            <?php echo $employee['old_shift']; ?>
                                                        </td>
                                                        <td style="text-align:center;">
                                                            <?php echo $employee['new_shift']; ?>
                                                        </td>
                                                        <td style="text-align:left;">
                                                            <span class="detail-toggle" data-short="<?= htmlentities(mb_strimwidth($row['request_detail'], 0, 15, '...')) ?>" data-full="<?= htmlentities($row['request_detail']) ?>">
                                                                <?= htmlentities(mb_strimwidth($row['request_detail'], 0, 15, '...')) ?>
                                                            </span>
                                                        </td>
                                                        <script>
                                                            document.addEventListener('DOMContentLoaded', function() {
                                                                const detailSpans = document.querySelectorAll(
                                                                    '.detail-toggle');

                                                                detailSpans.forEach(function(span) {
                                                                    span.addEventListener('click',
                                                                        function() {
                                                                            const shortDetail = this
                                                                                .getAttribute(
                                                                                    'data-short');
                                                                            const fullDetail = this
                                                                                .getAttribute(
                                                                                    'data-full');

                                                                            if (this.textContent ===
                                                                                shortDetail) {
                                                                                this.textContent =
                                                                                    fullDetail;
                                                                            } else {
                                                                                this.textContent =
                                                                                    shortDetail;
                                                                            }
                                                                        });
                                                                });
                                                            });
                                                        </script>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30" id="box3" style="display:none;">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <button class="btn" onclick="close_form()"><i class="fa-solid fa-xmark"></i></button>
                                <h2 class="mt-15 mb-10 h2 text-left">
                                    สลับกะการทำงาน
                                </h2>
                                <hr>
                                <section>
                                    <form id="desktop-form">
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>วันที่ต้องการสลับกะ</label>
                                                    <input type="date" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <?php $employee_number = 3; ?>
                                        <div id="employees-container">
                                            <?php
                                            for ($i = 1; $i <= $employee_number; $i++) { ?>
                                                <h4>พนักงานคนที่
                                                    <?= $i ?>
                                                </h4>
                                                <hr>
                                                <div class="<?php echo "employee" . $i ?> mb-2">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                                <label>ชื่อ - สกุล</label>
                                                                <select class="custom-select form-control" name="<?php echo "employeeid" . $i ?>" id="<?php echo "desktop-employeeid" . $i ?>" <?php echo 'onchange="display_employee_info(' . $i . ')"' ?>>
                                                                    <option value="select-member">
                                                                        เลือกสมาชิกในทีม
                                                                    </option>
                                                                    <?php echo $options; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>ทีม</label>
                                                                <input type="text" class="form-control" id="<?php echo "desktop-team" . $i ?>" style="background-color:transparent" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>กะเดิม</label>
                                                                <input type="text" class="form-control" id="<?php echo "desktop-oldshift" . $i ?>" style="background-color:transparent" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>กะใหม่</label>
                                                                <select class="custom-select form-control" name="<?php echo "shiftNew" . $i ?>" id="<?php echo "desktop-newshift" . $i ?>" onchange="showEmployeeDayOff(<?php $i ?>)">
                                                                    <?php echo $options_shift; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <hr>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>ระบุเหตุผล</label>
                                                    <input type="text" class="form-control" name="detail" id="desktop-detail">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>ผู้ตรวจสอบ (ถ้ามี)</label>
                                                    <select name="inspector" id="desktop-inspector" class="custom-select form-control">
                                                        <option value="">เลือกผู้ตรวจสอบ</option>
                                                        <?php echo $options_inspector; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>หัวหน้า</label>
                                                    <input type="hidden" name="approver_id" value="<?php echo $fullname_approver; ?>" style="background-color:transparent" readonly>
                                                    <input type="text" class="form-control" value="<?php echo $fullname_approver; ?>" style="background-color:transparent" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="col-sm-12 text-center mt-3">
                                        <div class="dropdown">
                                            <div class="btn btn-primary" onclick="add_switch_submit()">
                                                เพิ่มรายการสลับกะการทำงาน</div>
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
                <span>สลับกะการทำงาน</span>
            </div>
        </div>
        <div class="container">
            <!-- --ส่วนของ progress bar-- -->
            <div class="container-progress">
                <div class="progress-container step-4">
                    <div class="circle active"><a href="shift-progress-step1-admin.php"><span>1</span><span class="title">จัดทีม</span></a></div>
                    <div class="circle active"><a href="shift-progress-step2-admin.php">
                            <span>2</span><span class="title">แก้ไขรูปแบบ</span></a></div>
                    <div class="circle active"><a href="shift-progress-step3-admin.php">
                            <span>3</span><span class="title">ล็อกเหลี่ยม</span></a></div>
                    <div class="circle active"><a href="shift-progress-step4-admin.php">
                            <span>4</span><span class="title">สลับกะ</span></a></div>
                    <div class="circle "><a href="shift-progress-step5-admin.php">
                            <span>5</span><span class="title">เปลี่ยนกะ</span></a></div>
                    <div class="circle"><a href="shift-progress-step6-admin.php">
                            <span>6</span><span class="title">เพิ่มกะ</span></a></div>
                </div>
            </div>
            <!-- --ส่วนของการแก้ไขชุดการทำงาน-- -->
            <div class="container-swicth">
                <div class="container-table-team">
                    <div class="btn-select-shift">
                        <div class="btn-admin">
                            <button onclick="toggleTable('admin')">ดูกะการทำงานของตัวเอง</button>
                        </div>
                        <div class="btn-employee">
                            <button onclick="toggleTable('employee')">ดูกะการทำงานของพนักงาน</button>

                        </div>
                    </div>
                    <div class="search-em" style="display: none;">
                        <div class="search">
                            <input type="text" placeholder="กรอก Cost Center">
                            <img src="../IMG/search.png" alt="">
                        </div>
                        <div class="display-cost-center">
                            <span>00015-000011 ผลิตปูนเม็ด 1 : เอกพงศ์ มีสุข</span>
                        </div>
                    </div>

                    <div class="table-shift">
                        <div class="display-monthNow">
                            <a href="?month=<?= $prev_month ?>&year=<?= $prev_year ?>">
                                <button id="prevMonth"><img src="../IMG/arrowleft.png" alt=""></button>
                            </a>
                            <div class="current" id="currentDate">
                                <?= $showmonththai ?>
                                <?= $showyearthai ?>
                            </div>
                            <a href="?month=<?= $next_month ?>&year=<?= $next_year ?>">
                                <button id="nextMonth"><img src="../IMG/arrowright.png" alt=""></button>
                            </a>
                        </div>

                        <div class="add-change-shift">
                            <button class="add-order" onclick="mobile_add_switchShift()">
                                <img src="../IMG/add3.png" alt="">
                                <span>เพิ่มรายการสลับกะการทำงาน</span>
                            </button>
                        </div>

                        <div class="display-table-admin">
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
                                                <span class="team-name-toggle" data-short="<?= htmlentities(mb_strimwidth($nameteam, 0, 15, '...')) ?>" data-full="<?= htmlentities($nameteam) ?>">
                                                    <?= htmlentities(mb_strimwidth($nameteam, 0, 15, '...')) ?>
                                                </span>
                                            </td>
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    const teamNameSpan = document.querySelector(
                                                        '.team-name-toggle');

                                                    teamNameSpan.addEventListener('click', function() {
                                                        const shortName = this.getAttribute('data-short');
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
                                                $dayOfWeekThai = convertDayToThaiAdmin($dayOfWeek);

                                                echo "<th>$dayOfWeekThai</th>";
                                            }

                                            // Function to convert English day abbreviation to Thai
                                            function convertDayToThaiAdmin($dayOfWeek)
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
                        <div class="display-table-employee" style="display:none;">
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
                                                <span class="team-name-toggle" data-short="<?= htmlentities(mb_strimwidth($nameteam, 0, 15, '...')) ?>" data-full="<?= htmlentities($nameteam) ?>">
                                                    <?= htmlentities(mb_strimwidth($nameteam, 0, 15, '...')) ?>
                                                </span>
                                            </td>
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    const teamNameSpan = document.querySelector(
                                                        '.team-name-toggle');

                                                    teamNameSpan.addEventListener('click', function() {
                                                        const shortName = this.getAttribute('data-short');
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
                                                $dayOfWeekThai = convertDayToThaiEmployee($dayOfWeek);

                                                echo "<th>$dayOfWeekThai</th>";
                                            }

                                            // Function to convert English day abbreviation to Thai
                                            function convertDayToThaiEmployee($dayOfWeek)
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

                <div class="topic-wait-approve">
                    <span>รายการรออนุมัติ</span>
                </div>

                <div class="display-transaction">
                    <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>ชื่อ - สกุล</th>
                                <th>วันที่</th>
                                <th>เดิม</th>
                                <th>ใหม่</th>
                                <th>เหตุผล</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($shiftDataapprove as $row) : ?>
                                <?php $employeeData = [
                                    ['firstname' => $row['employee1_firstname'], 'lastname' => $row['employee1_lastname'], 'old_shift' => $row['employee1_old_shift_symbol'], 'new_shift' => $row['employee1_new_shift_symbol']],
                                    ['firstname' => $row['employee2_firstname'], 'lastname' => $row['employee2_lastname'], 'old_shift' => $row['employee2_old_shift_symbol'], 'new_shift' => $row['employee2_new_shift_symbol']],
                                    ['firstname' => $row['employee3_firstname'], 'lastname' => $row['employee3_lastname'], 'old_shift' => $row['employee3_old_shift_symbol'], 'new_shift' => $row['employee3_new_shift_symbol']],
                                ]; ?>

                                <?php foreach ($employeeData as $employee) : ?>
                                    <tr>
                                        <td class="name">
                                            <?php echo $employee['firstname'] . ' ' . $employee['lastname']; ?>
                                        </td>
                                        <td class="date">
                                            <?php echo $row['date']->format('d-m-Y'); ?>
                                        </td>
                                        <td class="shift-old">
                                            <?php echo $employee['old_shift']; ?>
                                        </td>
                                        <td class="shift-new">
                                            <?php echo $employee['new_shift']; ?>
                                        </td>
                                        <td class="detail-change">
                                            <?php
                                            $shortDetail = htmlentities(mb_strimwidth($row['request_detail'], 0, 10, '...'));
                                            echo $shortDetail;
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>


                        </tbody>

                    </table>
                </div>
            </div>
            <!-- --ปุ่ม action เลื่อนไปข้างหน้า เลื่อนไปข้างหลัง-- -->
            <div class="btn-action-step">
                <button class="btn" id="prev" onclick="location.href='shift-progress-step3-admin.php'">Prev</button>
                <button class="btn" id="next" onclick="location.href='shift-progress-step5-admin.php'">Next</button>
            </div>
        </div>


    </div>
</body>

<?php include('../includes/footer.php') ?>

</html>