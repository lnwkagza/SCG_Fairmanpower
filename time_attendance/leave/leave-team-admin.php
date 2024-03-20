<?php
session_start();
session_regenerate_id(true);
//---------------------------------------------------------------------------------------
include("../database/connectdb.php");
include('../components-desktop/admin/include/header.php')
?>

<!-- Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="node_modules\sweetalert2\dist\sweetalert2.css">

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/admin/leave-team-admin.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/leave-team.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/navbar.css">

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="../assets/script/loader-process.js"></script>


<?php

date_default_timezone_set('Asia/Bangkok');
$time_stamp = date("Y-m-d H:i:s");

// Assuming $start_mon and $end_mon are set based on user input or default values
$start_mon = isset($_POST['startDate']) ? $_POST['startDate'] : date("Y-m-01");
$end_mon = isset($_POST['endDate']) ? $_POST['endDate'] : date("Y-m-t", strtotime($start_mon));

//---------------------------------------------------------------------------------------------------

$card_id_query = "SELECT employee.card_id,manager_card_id FROM employee INNER JOIN manager ON employee.card_id = manager.card_id WHERE employee.card_id = ? ";
$card_id_params = array($_SESSION['card_id']);
$card_id_stmt = sqlsrv_query($conn, $card_id_query, $card_id_params);
$card_id_row = sqlsrv_fetch_array($card_id_stmt, SQLSRV_FETCH_ASSOC);

//---------------------------------------------------------------------------------------------------

$query = "SELECT 
            manager.manager_card_id,
            employee.prefix_thai,
            employee.firstname_thai,
            employee.lastname_thai,
            employee.employee_email,
            employee.employee_image,
            employee.card_id as employee_id,
            cost_center.cost_center_id,
            section.name_thai AS section,
            department.name_thai AS department,
            division.name_thai AS division
        FROM manager
        INNER JOIN employee ON manager.card_id = employee.card_id
        LEFT JOIN cost_center ON employee.cost_center_organization_id = cost_center.cost_center_id
        LEFT JOIN section ON cost_center.section_id = section.section_id 
        LEFT JOIN department ON section.department_id = department.department_id
        LEFT JOIN division ON department.division_id = division.division_id
        WHERE manager.manager_card_id = ? ";
$params = array($card_id_row["manager_card_id"]);
$stmt = sqlsrv_query($conn, $query, $params);

//-------------------------------------------------------------------------------------------------------

// $department = isset($_POST['department']) ? $_POST['department'] : $department;

if (!isset($department)) {
    $query = "SELECT 
    manager.manager_card_id,
    employee.prefix_thai,
    employee.firstname_thai,
    employee.lastname_thai,
    employee.employee_email,
    employee.employee_image,
    employee.card_id as employee_id,
    cost_center.cost_center_id,
    section.name_thai AS section,
    department.name_thai AS department,
    division.name_thai AS division,
    work_format.day_off1 AS day_off1, 
    work_format.day_off2 AS day_off2
    FROM manager
    INNER JOIN employee ON manager.card_id = employee.card_id
    LEFT JOIN cost_center ON employee.cost_center_organization_id = cost_center.cost_center_id
    LEFT JOIN section ON cost_center.section_id = section.section_id 
    LEFT JOIN department ON section.department_id = department.department_id
    LEFT JOIN division ON department.division_id = division.division_id
    INNER JOIN work_format ON employee.work_format_code = work_format.work_format_code
    WHERE manager.manager_card_id = ?";
    $params = array($card_id_row["manager_card_id"]);
    $stmt = sqlsrv_query($conn, $query, $params);
} else {
    $query = "SELECT 
                manager.manager_card_id,
                employee.prefix_thai,
                employee.firstname_thai,
                employee.lastname_thai,
                employee.employee_email,
                employee.employee_image,
                employee.card_id as employee_id,
                cost_center.cost_center_id,
                section.name_thai AS section,
                department.name_thai AS department,
                division.name_thai AS division,
                work_format.day_off1 AS day_off1, 
                work_format.day_off2 AS day_off2
            FROM manager
            INNER JOIN employee ON manager.card_id = employee.card_id
            LEFT JOIN cost_center ON employee.cost_center_organization_id = cost_center.cost_center_id
            LEFT JOIN section ON cost_center.section_id = section.section_id 
            LEFT JOIN department ON section.department_id = department.department_id
            LEFT JOIN division ON department.division_id = division.division_id
            LEFT JOIN work_format ON employee.work_format_code = work_format.work_format_code
            WHERE manager.manager_card_id = ? and employee.cost_center_organization_id = ?";
    $params = array($card_id_row['manager_card_id'], $department);
    $stmt = sqlsrv_query($conn, $query, $params);
}

$rows = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $rows[] = $row;
}

//-------------------------------------------------------------------------------------------------------------------

$cost_center_query = "SELECT 
            manager.manager_card_id AS manager_card_id,
            MAX(cost_center.cost_center_id) AS cost_center_id,
            section.name_thai AS section,
            department.name_thai AS department,
            division.name_thai AS division
            FROM manager 
            INNER JOIN employee  ON manager.card_id = employee.card_id
            LEFT JOIN cost_center ON employee.cost_center_organization_id = cost_center.cost_center_id
            LEFT JOIN section ON cost_center.section_id = section.section_id 
            LEFT JOIN department ON section.department_id = department.department_id
            LEFT JOIN division ON department.division_id = division.division_id
            WHERE manager.manager_card_id = ?
            GROUP BY manager.manager_card_id, section.name_thai, department.name_thai, division.name_thai";
$cost_center_params = array(&$_SESSION["card_id"]);
$cost_center_stmt = sqlsrv_query($conn, $cost_center_query, $cost_center_params);

$cost_center_rows = array();
while ($cost_center_row = sqlsrv_fetch_array($cost_center_stmt, SQLSRV_FETCH_ASSOC)) {
    $cost_center_rows[] = $cost_center_row;
}


//------------------------------------------------------------------------------------------------------------------
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
                                        <h2>ปฏิทินการลาทีม : Team Leave</h2>
                                    </div>
                                    <nav aria-label="breadcrumb" role="navigation">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item">
                                                <a style="pointer-events:none;cursor:default;">การลา</a>
                                            </li>
                                            <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                                ปฏิทินการลาทีม
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
                                        <form method="post" action="">
                                            <div class="form">
                                                <div class="form-dep">
                                                    <label for="department">แผนก</label>
                                                    <select class="custom-select form-control" id="department" name="department">
                                                        <option value="" disabled selected>กรุณาเลือกแผนก</option>
                                                        <!-- Added name attribute -->
                                                        <?php foreach ($cost_center_rows as $rs_emp) {
                                                            echo '<option value="' . $rs_emp['cost_center_id'] . '">' . $rs_emp['department'] . '</option>';
                                                        } ?>
                                                    </select>
                                                </div>
                                                <div class="form-date">
                                                    <input class="form-control" type="date" id="startDate" name="startDate" value="<?php echo $start_mon; ?>">

                                                    <label for="endDate">ถึง</label>
                                                    <input class="form-control" type="date" id="endDate" name="endDate" value="<?php echo $end_mon; ?>">
                                                </div>
                                                <button class="button-add" type="submit">
                                                    <i class=" fa-solid fa-magnifying-glass" style="color:#fff"></i>
                                                    ค้นหา
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="desktop-table-container">
                                        <div class="desktop-table left">
                                            <table id="table1" class="table stripe hover nowrap">
                                                <thead>
                                                    <tr>
                                                        <th class="name">ชื่อ-สกุล</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($rows as $employee) : ?>
                                                        <tr>
                                                            <td class="employee">
                                                                <div class="row">
                                                                    <div style="margin-right: 10px;margin-left: 5px;">
                                                                        <img src="<?php echo (!empty($employee['employee_image'])) ? '../../admin/uploads_img/' . $employee['employee_image'] : '../IMG/user.png'; ?>" class="border-radius-100 shadow" width="50px" height="50px" alt="">
                                                                    </div>
                                                                    <div>
                                                                        <b>
                                                                            <?php echo $employee['firstname_thai'] . " " . $employee['lastname_thai'] ?>
                                                                        </b><br>
                                                                        <a class="text-primary">
                                                                            <?php echo $employee['employee_email'] ?>
                                                                        </a><br>
                                                                        <b class="department" data-short="<?= "แผนก: " . htmlentities(mb_strimwidth($employee['department'], 0, 23, '...')) ?>" data-full="<?= "แผนก: " . htmlentities($employee['department']) ?>">
                                                                            <?= "แผนก: " . htmlentities(mb_strimwidth($employee['department'], 0, 23, '...')) ?>
                                                                        </b>
                                                                        <script>
                                                                            document.addEventListener('DOMContentLoaded',
                                                                                function() {
                                                                                    const detailSpans = document
                                                                                        .querySelectorAll(
                                                                                            '.department');

                                                                                    detailSpans.forEach(function(span) {
                                                                                        span.addEventListener(
                                                                                            'click',
                                                                                            function() {
                                                                                                const
                                                                                                    shortDetail =
                                                                                                    this
                                                                                                    .getAttribute(
                                                                                                        'data-short'
                                                                                                    );
                                                                                                const
                                                                                                    fullDetail =
                                                                                                    this
                                                                                                    .getAttribute(
                                                                                                        'data-full'
                                                                                                    );

                                                                                                if (this
                                                                                                    .textContent ===
                                                                                                    shortDetail
                                                                                                ) {
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
                                                    $currentDate = new DateTime($start_mon);
                                                    $endDate = new DateTime($end_mon); // Set to the last day of the selected month
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
                                                            <th class="date"><?= $currentDate->format("d") ?></th>
                                                        <?php
                                                            $currentDate->modify("+1 day");
                                                        endwhile;
                                                        ?>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($rows as $employee) : ?>
                                                        <tr>
                                                            <?php

                                                            $day1 = '';
                                                            $day2 = '';

                                                            // Check if there is a day-off request for the current date

                                                            $day1 = $employee['day_off1'];
                                                            $day2 = $employee['day_off2'];

                                                            // Map day names to numbers
                                                            $dayMap = [
                                                                'Sun' => 0,
                                                                'Mon' => 1,
                                                                'Tue' => 2,
                                                                'Wed' => 3,
                                                                'Thu' => 4,
                                                                'Fri' => 5,
                                                                'Sat' => 6,
                                                            ];

                                                            $day_off1 = $dayMap[$day1];
                                                            $day_off2 = $dayMap[$day2];

                                                            $currentDate = new DateTime($start_mon);
                                                            $endDate = new DateTime($end_mon);
                                                            while ($currentDate <= $endDate) : ?>
                                                                <td style="user-select:none">
                                                                    <?php

                                                                    // Your database queries and logic here
                                                                    $isWeekend = in_array($currentDate->format('w'), array($day_off1, $day_off2));
                                                                    $holiday_stmt_result = getHolidayInfo($conn, $currentDate->format("Y-m-d"));
                                                                    $eventResult = getEmployeeAbsence($conn, $employee['employee_id'], $currentDate->format("Y-m-d"));

                                                                    // Example: Outputting information
                                                                    echo displayCellContent($holiday_stmt_result, $eventResult, $isWeekend);
                                                                    ?>
                                                                </td>
                                                            <?php
                                                                $currentDate->modify("+1 day");
                                                            endwhile;
                                                            ?>
                                                        </tr>
                                                    <?php endforeach; ?>
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
                    <span>ปฏิทินการลาทีม</span>
                </div>
            </div>

            <form method="post" action="">
                <div class="form">
                    <div class="form-dep">
                        <label for="department">แผนก</label>
                        <select id="department" name="department">
                            <option value="" disabled selected>กรุณาเลือกcost-center</option>
                            <!-- Added name attribute -->
                            <?php foreach ($cost_center_rows as $rs_emp) {
                                echo '<option value="' . $rs_emp['cost_center_id'] . '">' . $rs_emp['department'] . '</option>';
                            } ?>
                        </select>
                    </div>
                    <div class="form-date">
                        <input type="date" id="startDate" name="startDate" value="<?php echo $start_mon; ?>">
                        <label>ถึง</label>
                        <input type="date" id="endDate" name="endDate" value="<?php echo $end_mon; ?>">
                        <button class="button-search" type="submit">
                            <i class=" fa-solid fa-magnifying-glass" style="color:#fff"></i>
                        </button>
                    </div>

                </div>
            </form>

            <div class="dateTeam">
                <div class="tableName">
                    <table>
                        <tr>
                            <th>ชื่อ - สกุล</th>
                        </tr>
                        <?php foreach ($rows as $employee) : ?>
                            <tr>
                                <td>
                                    <div class="nameEm">
                                        <img src="../IMG/user.png" alt="">
                                        <div class="dataEm">
                                            <span>
                                                <?= htmlspecialchars($employee['firstname_thai'] . ' ' . $employee['lastname_thai']); ?>
                                            </span>
                                            <span>แผนก:
                                                <?= htmlspecialchars(strlen($employee['department']) > 10 ? substr($employee['department'], 0, 10) . '...' : $employee['department']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </table>
                </div>

                <div class="tableDetail" id="tableDetail">
                    <table>
                        <tr>
                            <?php
                            $currentDate = new DateTime($start_mon);
                            $endDate = new DateTime($end_mon);
                            $daysOfWeekThai = array('อา.', 'จ.', 'อ.', 'พ.', 'พฤ.', 'ศ.', 'ส.');
                            while ($currentDate <= $endDate) :
                            ?>
                                <th>
                                    <?= htmlspecialchars($daysOfWeekThai[$currentDate->format('w')] . ' ' . $currentDate->format("d")); ?>
                                </th>
                            <?php
                                $currentDate->modify("+1 day");
                            endwhile;
                            ?>
                        </tr>

                        <?php foreach ($rows as $employee) : ?>
                            <tr>
                                <?php
                                $currentDate = new DateTime($start_mon);
                                $endDate = new DateTime($end_mon);
                                ?>
                                <?php while ($currentDate <= $endDate) : ?>
                                    <td>
                                        <?php
                                        // Your database queries and logic here
                                        $isWeekend = in_array($currentDate->format('w'), array(0, 6));
                                        $holiday_stmt_result = getHolidayInfo($conn, $currentDate->format("Y-m-d"));
                                        $eventResult = getEmployeeAbsence($conn, $employee['employee_id'], $currentDate->format("Y-m-d"));

                                        // Example: Outputting information
                                        echo displayCellContent($holiday_stmt_result, $eventResult, $isWeekend);
                                        ?>
                                    </td>
                                <?php
                                    $currentDate->modify("+1 day");
                                endwhile;
                                ?>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

                <?php
                // Function to get holiday information
                function getHolidayInfo($conn, $date)
                {
                    $select_holiday_Query = "SELECT name FROM holiday WHERE date = ? ORDER BY date ASC";
                    $holiday_stmt = sqlsrv_prepare($conn, $select_holiday_Query, array($date));
                    if ($holiday_stmt) {
                        $holiday_stmt_result = sqlsrv_fetch_array($holiday_stmt, SQLSRV_FETCH_ASSOC);
                        return $holiday_stmt_result;
                    }
                    return null;
                }

                // Function to get employee absence information
                function getEmployeeAbsence($conn, $employee_id, $date)
                {
                    $query2 = "SELECT date_start, date_end FROM absence_record WHERE card_id = ? AND date_start <= ? AND date_end >= ? AND approve_status = ?";
                    $params2 = array($employee_id, $date, $date, 'confirm');
                    $stmt2 = sqlsrv_query($conn, $query2, $params2);

                    $eventResult = array();
                    while ($absenceData = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
                        $absenceStartDate = new DateTime($absenceData['date_start']->format("Y-m-d"));
                        $absenceEndDate = new DateTime($absenceData['date_end']->format("Y-m-d"));
                        $interval = new DateInterval('P1D');

                        while ($absenceStartDate <= $absenceEndDate) {
                            $eventResult[] = $absenceStartDate->format('d-m-Y');
                            $absenceStartDate->add($interval);
                        }
                    }

                    return $eventResult;
                }

                // Function to display cell content based on conditions
                function displayCellContent($holiday_stmt_result, $eventResult, $isWeekend)
                {
                    if ($holiday_stmt_result) {
                        return htmlspecialchars($holiday_stmt_result["name"]);
                    } elseif (!empty($eventResult)) {
                        return "ลางาน";
                    } elseif ($isWeekend) {
                        return "วันหยุด";
                    } else {
                        return "-";
                    }
                }
                ?>
                </table>
            </div>
        </div>
    </div>
</body>
<?php include('../includes/footer.php'); ?>