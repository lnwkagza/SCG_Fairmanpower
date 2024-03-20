<?php
session_start();
session_regenerate_id(true);
//---------------------------------------------------------------------------------------
header("Cache-Control: no-cache, must-revalidate");
include("../database/connectdb.php");
include('../components-desktop/employee/include/header.php');
//---------------------------------------------------------------------------------------
$shiftManageId = $_GET['sub_team_id']; // Assuming you're getting the shift_change_id from a request, adjust this based on your input method (GET, POST, etc.)

$sql = "SELECT 
        sub_team.sub_team_id,
        sub_team.request_time,
        sub_team.name,
        approve_status,
        requester.firstname_thai AS requester_firstname,
        requester.lastname_thai AS requester_lastname,
        inspector.firstname_thai AS inspector_firstname,
        inspector.lastname_thai AS inspector_lastname,
        approver.firstname_thai AS approver_firstname,
        approver.lastname_thai AS approver_lastname
        FROM 
        sub_team
        INNER JOIN 
        employee AS requester
        ON 
        sub_team.request_card_id = requester.card_id
        LEFT JOIN
        employee AS inspector
        ON 
        sub_team.inspector = inspector.card_id
        LEFT JOIN
        employee AS approver
        ON 
        sub_team.approver = approver.card_id
        WHERE 
        sub_team.sub_team_id = ?"; // Use parameterized query to avoid SQL injection

// Prepare and bind parameters
$params = array($shiftManageId);
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
// Execute the query
$stmt = sqlsrv_query($conn, $sql, $params, $options);
// Fetch the data
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);


// Second Query
$sql2 = "SELECT firstname_thai, lastname_thai,scg_employee_id
         FROM employee
         WHERE sub_team_id = ?"; // Adjust the condition based on your requirement

// Execute the second query
$stmt2 = sqlsrv_query($conn, $sql2, $params);

// Fetch the data for the second query into an array
$employeedata = array();

while ($row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
    $employeedata[] = $row2;
}

// Now $employeedata contains the fetched data as an array

?>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/employee/shift-detail-employee.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/shift-request-detail.css">
<link rel=" stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="script/loader-normal.js"></script>

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
                                    <h2>รายละเอียดคำขออนุมัติการขอจัดการทีม</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor:default;">กะการทำงาน</a>
                                        </li>
                                        <li class=" breadcrumb-item">
                                            <a href="shift-request-employee.php">คำขออนุมัติ</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            รายละเอียดคำขออนุมัติการขอจัดการทีม
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class=" row">
                        <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="wizard-content">
                                    <form>
                                        <section>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="date">
                                                            <label>วันที่ทำรายการ:</label>
                                                            <span><?= $row['request_time']->format('Y/m/d') ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="employee">
                                                            <label>ชื่อผู้ทำรายการ:</label>
                                                            <span><?= $row['requester_firstname'] . " " . $row['requester_lastname'] ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="team">
                                                            <label>รายชื่อทีม:</label>
                                                            <span><?= $row['name'] ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="team-member">
                                                            <label>สมาชิกในทีม:</label>
                                                            <?php
                                                            // Assuming you've fetched the employee data into the $employeedata array
                                                            foreach ($employeedata as $employee) {
                                                                $employeeId = $employee['scg_employee_id']; // Replace 'employee_id' with the actual column name
                                                                $firstName = $employee['firstname_thai'];
                                                                $lastName = $employee['lastname_thai'];
                                                            ?>
                                                                <span><?php echo $employeeId . ' ' . $firstName . ' ' . $lastName; ?></span>
                                                            <?php
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="inspector">
                                                            <label>ผู้ตรวจสอบ:</label>
                                                            <span><?= $row['inspector_firstname'] . " " . $row['inspector_lastname'] ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="head">
                                                            <label>หัวหน้า:</label>
                                                            <span><?= $row['approver_firstname'] . " " . $row['approver_lastname'] ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="display-status">
                                                            <label>สถานะ:</label>
                                                            <?php if ($row['approve_status'] == "approve") : ?>
                                                                <span class="approve">อนุมัติแล้ว</span>
                                                            <?php elseif ($row['approve_status'] == "waiting") : ?>
                                                                <span class="wait">รออนุมัติ</span>
                                                            <?php elseif ($row['approve_status'] == "reject") : ?>
                                                                <span class="reject">ปฏิเสธ</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
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
                <span>รายละเอียดคำขออนุมัติ</span>
            </div>
        </div>

        <div class="container">
            <div class="display-topic">
                <span>รายละเอียดคำขออนุมัติการจัดการทีม</span>
            </div>
            <div class="display-detail">
                <div class="display-date">
                    <span>วันที่ทำรายการ</span>
                    <div class="date">
                        <span><?= $row['request_time']->format('Y/m/d') ?></span>
                    </div>
                </div>
                <div class="display-name">
                    <span>ชื่อผู้ทำรายการ</span>
                    <div class="name">
                        <span><?= $row['requester_firstname'] . " " . $row['requester_lastname'] ?></span>
                    </div>

                </div>
                <div class="display-team">
                    <div class="name-team">
                        <span>รายชื่อทีม</span>
                        <div class="team-name">
                            <span><?= $row['name'] ?></span>
                        </div>
                    </div>
                    <div class="member-team">
                        <div class="topic-member">
                            <span>สมาชิกในทีม</span>
                        </div>
                        <div class="member-all-team">
                            <?php
                            // Assuming you've fetched the employee data into the $employeedata array
                            foreach ($employeedata as $employee) {
                                $employeeId = $employee['scg_employee_id']; // Replace 'employee_id' with the actual column name
                                $firstName = $employee['firstname_thai'];
                                $lastName = $employee['lastname_thai'];
                            ?>
                                <span><?php echo $employeeId . ' ' . $firstName . ' ' . $lastName; ?></span>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class="display-inspector">
                        <span>ผู้ตรวจสอบ</span>
                        <div class="inspector">
                            <span><?= $row['inspector_firstname'] . " " . $row['inspector_lastname'] ?></span>
                        </div>
                    </div>
                    <div class="display-head">
                        <span>หัวหน้า</span>
                        <div class="head">
                            <span><?= $row['approver_firstname'] . " " . $row['approver_lastname'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="display-status">
                    <?php if ($row['approve_status'] == "approve") : ?>
                        <span class="approve">อนุมัติแล้ว</span>
                    <?php elseif ($row['approve_status'] == "waiting") : ?>
                        <span class="wait">รออนุมัติ</span>
                    <?php elseif ($row['approve_status'] == "reject") : ?>
                        <span class="reject">ปฏิเสธ</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</body>
<?php include('../includes/footer.php') ?>