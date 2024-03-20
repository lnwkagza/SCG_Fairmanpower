<?php
session_start();
session_regenerate_id(true);
//---------------------------------------------------------------------------------------
include("../database/connectdb.php");
include('../components-desktop/admin/include/header.php');
// Include header, database connection, and start session
?>
<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/admin/leave-set-rights-admin.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/leave-set-rights.css">
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<?php
// Fetch employee data for dropdown options
$select_employee = "SELECT employee.firstname_thai AS firstname_thai,
                           employee.lastname_thai AS lastname_thai,
                           scg_employee_id,
                           employee.card_id AS card_id
                    FROM employee";
// Prepare the SQL statement
$employee = sqlsrv_query($conn, $select_employee);

$employeedata = array();

// Fetch data and store in $employeedata array
while ($row = sqlsrv_fetch_array($employee, SQLSRV_FETCH_ASSOC)) {
    $employeedata[] = $row;
}

// Generate dropdown options
$options = '';
foreach ($employeedata as $rs_emp) {
    $options .= '<option value="' . $rs_emp['card_id'] . '">' . $rs_emp['scg_employee_id'] . '-' . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai'] . '</option>';
}

// Get empid value from POST request or set it to empty string
$empid = isset($_POST['empid']) ? $_POST['empid'] : '';
if (!empty($empid) && is_numeric($empid)) {
    // Validate and sanitize empid before using it in SQL queries

    // Prepare the SQL statement with a parameter for card_id
    $select_employee1 = "SELECT * FROM absence_quota 
                        LEFT JOIN pl_info ON absence_quota.card_id = pl_info.card_id
                        WHERE absence_quota.card_id = ?";

    // Prepare and execute the SQL statement
    $stmt = sqlsrv_prepare($conn, $select_employee1, array(&$empid));
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Execute the prepared statement
    if (sqlsrv_execute($stmt)) {
        // Fetch absence quota data
        if (sqlsrv_has_rows($stmt)) {
            // Data exists, fetch and process the data...
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            if (empty($row['annual_leave'])) {
                // Update absence quota data...
                $time_stamp_year = date("Y");
                $query = "UPDATE absence_quota SET annual_leave = ? WHERE card_id = ? AND date_year = ?";
                $params = array(20, $empid, $time_stamp_year);
                $stmt_update = sqlsrv_query($conn, $query, $params);

                if ($stmt_update === false) {
                    die(print_r(sqlsrv_errors(), true));
                } else {
                    // Success message or action...
                }
            } else {
                // Annual leave is already set, handle accordingly...
            }
        } else {
            // No data found, perform an insert...
            $time_stamp_year = date("Y");
            $query = "INSERT INTO absence_quota
            (card_id, annual_leave, annual_leave_collect, maternity_leave, ordination_leave, ordination_leave_nopaid, haj_leave, haj_leave_nopaid, training_leave_nopaid, csr_leave, work_sick_leave, military_service_leave, other_leave, other_leave_nopaid, sick_leave, date_year)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $params = array(
                $empid,
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

            $stmt_insert = sqlsrv_query($conn, $query, $params);

            if ($stmt_insert === false) {
                die(print_r(sqlsrv_errors(), true));
            } else {
                // Success message or action...
            }
        }
    } else {
        die(print_r(sqlsrv_errors(), true));
    }
}

?>

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
                                    <h2>ประวัติการลา : Leave History</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor:default;">การลา</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            ประวัติการลา
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mobile">
        <div class=" navbar">
            <div class="div-span">
                <span>จัดการสิทธิการลา</span>
            </div>
        </div>

        <div class="searchEm">
        </div>

        <div class="dataEm">
            <div class="id-nameEm">
                <form method="POST">
                    <span>รหัส-ชื่อพนักงาน :
                        <select name="empid" id="empid">
                            <option value="">เลือกพนักงาน</option>
                            <?php echo $options; ?>
                        </select>
                    </span>
                </form>

                <script>
                    document.getElementById('empid').addEventListener('change', function() {
                        this.form.submit();
                    });
                </script>


            </div>
            <div class="level-ageWork">
                <span>ระดับพนักงาน : <input type="text" value="<?= isset($row['pl_id']) ? $row['pl_id'] : " - " ?>"></span>
                <span>อายุงาน : <input type="text" value="<?= isset($row['total_year']) ? $row['total_year'] : " - " ?>"></span>
            </div>
        </div>

        <form id="updateabsence">
            <input type="hidden" name="empid" value="<?= $empid ?>">
            <div class="break">
                <div class="box">
                    <div class="dataDate">
                        <div class="imgDate">
                            <img src="../IMG/leave.png">
                        </div>
                        <div class="topic-text">
                            <span>วันหยุดพักผ่อนประจำปี </span>
                            <span>ระบุจำนวนวัน </span>
                        </div>
                        <div class="boxRight">
                            <div class="sub-text">
                                <input type="text" name="annual_leave" value="<?= isset($row['annual_leave']) ? $row['annual_leave'] : " - " ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="dataDate">
                        <div class="imgDate">
                            <img src="../IMG/leaveSum.png">
                        </div>
                        <div class="topic-text">
                            <span>วันหยุดพักผ่อนประจำปีสะสม </span>
                            <span>ระบุจำนวนวัน </span>
                        </div>
                        <div class="boxRight">
                            <div class="sub-text">
                                <input type="text" name="annual_leave_collect" value="<?= isset($row['annual_leave_collect']) ? $row['annual_leave_collect'] : " - " ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="dataDate">
                        <div class="imgDate">
                            <img src="../IMG/sick.png">
                        </div>
                        <div class="topic-text">
                            <span>ลาป่วย </span>
                            <span>ระบุจำนวนวัน </span>
                        </div>
                        <div class="boxRight">
                            <div class="sub-text">
                                <input type="text" name="sick_leave" value="<?= isset($row['sick_leave']) ? $row['sick_leave'] : " - " ?>">
                            </div>
                        </div>
                    </div>
                </div>


                <div class="box">
                    <div class="dataDate">
                        <div class="imgDate">
                            <img src="../IMG/birth.png">
                        </div>
                        <div class="topic-text">
                            <span>ลาคลอด </span>
                            <span>ระบุจำนวนวัน </span>
                        </div>
                        <div class="boxRight">
                            <div class="sub-text">
                                <input type="text" name="maternity_leave" value="<?= isset($row['maternity_leave']) ? $row['maternity_leave'] : " - " ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="dataDate">
                        <div class="imgDate">
                            <img src="../IMG/training.png">
                        </div>
                        <div class="topic-text">
                            <span>ลาอบรม </span>
                            <span>ระบุจำนวนวัน </span>
                        </div>
                        <div class="boxRight">
                            <div class="sub-text">
                                <input type="text" name="training_leave_nopaid" value="<?= isset($row['training_leave_nopaid']) ? $row['training_leave_nopaid'] : " - " ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="dataDate">
                        <div class="imgDate">
                            <img src="../IMG/religion.png">
                        </div>
                        <div class="topic-text">
                            <span>ลาศาสนา </span>
                            <span>ระบุจำนวนวัน </span>
                        </div>
                        <div class="boxRight">
                            <div class="sub-text">
                                <input type="text" name="ordination_leave" value="<?= isset($row['ordination_leave']) ? $row['ordination_leave'] : " - " ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="dataDate">
                        <div class="imgDate">
                            <img src="../IMG/religion1.png">
                        </div>
                        <div class="topic-text">
                            <span>ลาศาสนา ไม่จ่าย </span>
                            <span>ระบุจำนวนวัน </span>
                        </div>
                        <div class="boxRight">
                            <div class="sub-text">
                                <input type="text" name="ordination_leave" value="<?= isset($row['ordination_leave']) ? $row['ordination_leave_nopaid'] : " - " ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="dataDate">
                        <div class="imgDate">
                            <img src="../IMG/another.png">
                        </div>
                        <div class="topic-text">
                            <span>ลาอื่น ๆ </span>
                            <span>ระบุจำนวนวัน </span>
                        </div>
                        <div class="boxRight">
                            <div class="sub-text">
                                <input type="text" name="other_leave" value="<?= isset($row['other_leave']) ? $row['other_leave'] : " - " ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn-save" type="button" onclick="saveChanges()">บันทึก</button>
            </div>
        </form>
    </div>

    <script>
        function saveChanges() {
            var formData = new FormData(document.getElementById("updateabsence"));

            // AJAX request
            fetch('../processing/admin_set_leave_employee.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        return response.text();
                    }
                    throw new Error('Network response was not ok.');
                })
                .then(data => {
                    // Handle success response
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกข้อมูลสำเร็จ',
                        text: data
                    });
                })
                .catch(error => {
                    // Handle error
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: error.message
                    });
                });
        }
    </script>


</body>
<?php include('../includes/footer.php'); ?>