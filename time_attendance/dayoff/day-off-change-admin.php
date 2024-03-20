<?php
session_start();
session_regenerate_id(true);
//---------------------------------------------------------------------------------------
include("../database/connectdb.php");
include('../components-desktop/admin/include/header.php');
//---------------------------------------------------------------------------------------
?>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/admin/day-off-change-admin.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/day-off-change.css">

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="..\assets\script\loader-normal.js"></script>

<?php

$sql = "SELECT * FROM work_format";
$work_format = sqlsrv_query($conn, $sql);
$work_formatdata = array();

while ($row = sqlsrv_fetch_array($work_format, SQLSRV_FETCH_ASSOC)) {
    $work_formatdata[] = $row;
}

$sqlday = sqlsrv_query($conn, "SELECT card_id,remark,day_off1,day_off2 FROM employee INNER JOIN work_format ON employee.work_format_code = work_format.work_format_code WHERE employee.card_id = ?", array($_SESSION["card_id"]));
$rs_sqlday = sqlsrv_fetch_array($sqlday, SQLSRV_FETCH_ASSOC);

$query = "SELECT manager_card_id,firstname_thai,lastname_thai FROM manager INNER JOIN employee ON manager.manager_card_id = employee.card_id WHERE manager.card_id = ? ";
$params = array($_SESSION['card_id']);
$stmt = sqlsrv_query($conn, $query, $params);
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

$approver_firstname = $row['firstname_thai'];
$approver_lastname = $row['lastname_thai'];
$approver_card_id = $row['manager_card_id'];
$fullname_approver = $approver_firstname . ' ' . $approver_lastname;

// Fetch inspector options
$SELECT_approver = "SELECT card_id, scg_employee_id, prefix_thai, firstname_thai, lastname_thai FROM employee WHERE permission_id = ? and card_id != ?";
$params_approver = array('2', $approver_card_id);
$stmt_approver = sqlsrv_query($conn, $SELECT_approver, $params_approver);

$options_inspector = '';
while ($rs_emp = sqlsrv_fetch_array($stmt_approver, SQLSRV_FETCH_ASSOC)) {
    $options_inspector .= '<option value="' . $rs_emp['card_id'] . '">' . $rs_emp['scg_employee_id'] . " " . $rs_emp['prefix_thai'] . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai'] . '</option>';
}

?>

<script>
    function updateDayoffOptions() {
        var newFormat = document.getElementById("work_format").value;
        var dayoffSelect = document.getElementById("dayoff");
        var workSelect = document.getElementById("work");

        // Remove existing options
        dayoffSelect.innerHTML = "";
        workSelect.innerHTML = "";

        var defaultOption = document.createElement("option");
        defaultOption.text = "เลือกวันหยุด";
        dayoffSelect.add(defaultOption);

        // Add options based on the selected newFormat
        <?php foreach ($work_formatdata as $row) : ?>
            if ("<?= $row["remark"] ?>" === newFormat) {
                var option = document.createElement("option");
                option.value = "<?= $row["day_off1"] ?> - <?= $row["day_off2"] ?>";
                option.text = "<?= $row["day_off1"] ?> - <?= $row["day_off2"] ?>";
                dayoffSelect.add(option);
            }
        <?php endforeach; ?>
    }

    // Function to update work select options based on the selected dayoff
    function updateWorkOptions() {
        var selectedDayoff = document.getElementById("dayoff").value;
        var workSelect = document.getElementById("work");

        // Remove existing options
        workSelect.innerHTML = "";

        // Add options based on the selected dayoff
        <?php foreach ($work_formatdata as $row) : ?>
            if ("<?= $row["day_off1"] ?> - <?= $row["day_off2"] ?>" === selectedDayoff) {
                var optionWork = document.createElement("option");
                optionWork.value = "<?= $row["work_format_code"] ?>";
                optionWork.text = "<?= $row["format"] ?>";
                workSelect.add(optionWork);
            }
        <?php endforeach; ?>
    }

    function updateDayoffOptions1() {
        var newFormat = document.getElementById("work_format1").value;
        var dayoffSelect = document.getElementById("dayoff1");
        var workSelect = document.getElementById("work1");

        // Remove existing options
        dayoffSelect.innerHTML = "";
        workSelect.innerHTML = "";

        var defaultOption = document.createElement("option");
        defaultOption.text = "เลือกวันหยุด";
        dayoffSelect.add(defaultOption);

        // Add options based on the selected newFormat
        <?php foreach ($work_formatdata as $row) : ?>
            if ("<?= $row["remark"] ?>" === newFormat) {
                var option = document.createElement("option");
                option.value = "<?= $row["day_off1"] ?> - <?= $row["day_off2"] ?>";
                option.text = "<?= $row["day_off1"] ?> - <?= $row["day_off2"] ?>";
                dayoffSelect.add(option);
            }
        <?php endforeach; ?>
    }

    // Function to update work select options based on the selected dayoff
    function updateWorkOptions1() {
        var selectedDayoff = document.getElementById("dayoff1").value;
        var workSelect = document.getElementById("work1");

        // Remove existing options
        workSelect.innerHTML = "";

        // Add options based on the selected dayoff
        <?php foreach ($work_formatdata as $row) : ?>
            if ("<?= $row["day_off1"] ?> - <?= $row["day_off2"] ?>" === selectedDayoff) {
                var optionWork = document.createElement("option");
                optionWork.value = "<?= $row["work_format_code"] ?>";
                optionWork.text = "<?= $row["format"] ?>";
                workSelect.add(optionWork);
            }
        <?php endforeach; ?>
    }

    // Attach event listener to the dayoff select element
    // document.getElementById("dayoff").addEventListener("change", updateWorkOptions);
</script>

<script type="text/javascript">
    function confirm() {
        swal.fire({
            html: '<div style="font-weight: bold; font-size: 4vw;">ยืนยันการยื่นคำขอ</div><br>' +
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
                submit(result);
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });

    }

    function confirm2() {
        swal.fire({
            html: '<div style="font-weight: bold; font-size: 4vw;">ยืนยันการยื่นคำขอ</div><br>' +
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
                submit2(result);
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });

    }


    function submit() {
        swal.fire({
            html: '<div style="font-weight: bold; font-size: 4vw;">ยื่นคำขอสำเร็จ</div><br>' +
                '<img class="img" src="../IMG/check1.png"></img>',
            padding: '2em',
            confirmButtonText: 'ตกลง',
            confirmButtonColor: '#29ab29',
            customClass: {
                confirmButtonText: 'swal2-confirm',
                cancelButtonText: 'swal2-cancel',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('addDayOffForm').submit();
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });
    }

    function submit2() {
        swal.fire({
            html: '<div style="font-weight: bold; font-size: 4vw;">ยื่นคำขอสำเร็จ</div><br>' +
                '<img class="img" src="../IMG/check1.png"></img>',
            padding: '2em',
            confirmButtonText: 'ตกลง',
            confirmButtonColor: '#29ab29',
            customClass: {
                confirmButtonText: 'swal2-confirm',
                cancelButtonText: 'swal2-cancel',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('addDayOffForm2').submit();
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });
    }

    function cancel() {
        swal.fire({
            html: '<div style="font-weight: bold; font-size: 4vw;">ยกเลิกการขอเปลี่ยนรูปแบบวันหยุดและการทำงาน</div><br>' +
                '<img class="img" src="../IMG/question 1.png"></img>',
            padding: '2em',
            confirmButtonText: 'ใช่',
            cancelButtonText: 'ไม่',
            confirmButtonColor: '#29ab29',
            cancelButtonColor: '#e1574b',
            showCancelButton: true,

            customClass: {
                confirmButtonText: 'swal2-confirm',
                cancelButtonText: 'swal2-cancel',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'day-off-head.php';
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });
    }

    function day_off_change_submit() {
        Swal.fire({
            title: "<strong>ยืนยันขอเปลี่ยนรูปแบบวันหยุดและการทำงานหรือไม่</strong>",
            icon: "question",
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: `ตกลง`,
            cancelButtonText: `ยกเลิก`,
        }).then((result) => {
            if (result.isConfirmed) {
                day_off_change_confirm()
            } else {
                Swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });
    }

    function day_off_change_confirm() {
        Swal.fire({
            title: "<strong>ขอเปลี่ยนรูปแบบวันหยุดและการทำงานสำเร็จ</strong>",
            icon: "success",
            showCloseButton: true,
            focusConfirm: false,
            confirmButtonText: `ตกลง`,
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.getElementById("desktop-form");
                form.action = '../processing/process_dayoff_working_admin_Request.php';
                form.method = 'POST';
                form.submit();
            }
        });
    }

    function day_off_change_cancel() {
        Swal.fire({
            title: "<strong>ยืนยันยกเลิกทำรายการลา</strong>",
            icon: "warning",
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: `ตกลง`,
            cancelButtonText: `ยกเลิก`,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "day-off-admin.php";
            }
        });
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
                                        <h2>ขอเปลี่ยนรูปแบบวันหยุดและการทำงาน</h2>
                                    </div>
                                    <nav aria-label="breadcrumb" role="navigation">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item">
                                                <a style="pointer-events:none;cursor:default;">วันหยุด</a>
                                            </li>
                                            <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                                ขอเปลี่ยนรูปแบบวันหยุดและการทำงาน
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
                                        <form id="desktop-form">
                                            <section>
                                                <div class="row">
                                                    <div class="col-md-7 col-sm-12">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="employeeid">วันเริ่มต้น
                                                                    </label>
                                                                    <label style="color: red;font-size:14px !important">*หมายเหตุ:
                                                                        โปรดเลือกวันจันทร์เท่านั้น*</label>
                                                                    <input class="form-control" type="date" name="startDate" id="startDate">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="employeeid">วันสิ้นสุด
                                                                    </label>
                                                                    <label style="color: red;font-size:14px !important">*หมายเหตุ:
                                                                        โปรดเลือกวันอาทิตย์เท่านั้น*</label>
                                                                    <input class="form-control" type="date" name="endDate" id="endDate">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-7 col-sm-12">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="remarkDisplay">รูปแบบการทำงานเดิม</label>
                                                                    <span class="detailSpan form-control" id="remarkDisplay"><?php echo $rs_sqlday["remark"] ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="work_format">รูปแบบการทำงานใหม่</label>
                                                                    <select name="work_format" id="work_format1" class="newFormat custom-select form-control" onchange="updateDayoffOptions1()">
                                                                        <option value="">เลือกรูปแบบ</option>
                                                                        <option value="ปกติ(STS)">ปกติ(STS)
                                                                        </option>
                                                                        <option value="ปกติ(CPAC)">ปกติ(CPAC)
                                                                        </option>
                                                                        <option value="กะ(STS)">กะ(STS)</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-7 col-sm-12">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="dayoffDisplay">วันหยุดเดิม</label>
                                                                    <span class="detailSpan form-control" id="dayoffDisplay">
                                                                        <?php echo $rs_sqlday["day_off1"] ?> -
                                                                        <?php echo $rs_sqlday["day_off2"] ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="dayoff">วันหยุดใหม่</label>
                                                                    <select name="dayoff" id="dayoff1" class="dayoff custom-select form-control" onchange="updateWorkOptions1()"></select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-7 col-sm-12">
                                                        <div class="form-group">
                                                            <label for="work">เลือกรูปแบบกะ</label>
                                                            <select name="work" id="work1" class="custom-select form-control"></select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-7 col-sm-12">
                                                        <div class="form-group">
                                                            <label for="detail">โปรดระบุเหตุผล</label>
                                                            <input class="add-detail form-control" name="detail" id="detail"></input>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-7 col-sm-12">
                                                        <div class="form-group">
                                                            <label for="detail">ผู้ตรวจสอบ
                                                                (ถ้ามี)</label>
                                                            <select class="form-control" name="inspector" id="desktop-inspector">
                                                                <option value="">เลือกผู้ตรวจสอบ
                                                                </option>
                                                                <?php echo $options_inspector; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-7 col-sm-12">
                                                        <div class="form-group">
                                                            <label for="detail">หัวหน้า</label>
                                                            <input type="hidden" name="approve" value="<?PHP echo $approver_card_id; ?>">
                                                            <input type="text" class="form-control" value="<?php echo $fullname_approver; ?>" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </form>
                                        <div class="row">
                                            <div class="col-md-7 col-sm-12" style="display:flex;justify-content:flex-end;">
                                                <div class="form-group">
                                                    <div class="btn btn-primary" onclick="day_off_change_submit()">
                                                        ยืนยัน
                                                    </div>
                                                    <div class=" btn btn-danger" onclick="day_off_change_cancel()">
                                                        ยกเลิก
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
        </div>

        <div class="mobile">
            <div class="navbar">
                <div class="div-span">
                    <span>ขอเปลี่ยนรูปแบบวันหยุดและการทำงาน</span>
                </div>
            </div>
            <div class="container">
                <form id="addDayOffForm2" action="../processing/process_dayoff_working_Request.php" method="post">
                    <div class="display-date-start">
                        <span>วันเริ่มต้น(โปรดเลือกวันจันทร์เท่านั้น)</span>
                        <input type="date" name="startDate" id="startDate" required onchange="validateStartDate()">
                        <span id="startDateError" style="color: red;"></span>
                    </div>
                    <div class="display-date-start">
                        <span>วันสิ้นสุด(โปรดเลือกวันอาทิตย์เท่านั้น)</span>
                        <input type="date" name="endDate" id="endDate" required onchange="validateEndDate()">
                        <span id="endDateError" style="color: red;"></span>
                    </div>

                    <script>
                        function validateStartDate() {
                            var selectedDate = new Date(document.getElementById('startDate').value);
                            if (selectedDate.getDay() != 1) { // 1 is Monday
                                document.getElementById('startDateError').textContent = 'โปรดเลือกวันจันทร์เท่านั้น';
                                document.getElementById('startDate').value = '';
                            } else {
                                document.getElementById('startDateError').textContent = '';
                            }
                        }

                        function validateEndDate() {
                            var selectedDate = new Date(document.getElementById('endDate').value);
                            if (selectedDate.getDay() != 0) { // 0 is Sunday
                                document.getElementById('endDateError').textContent = 'โปรดเลือกวันอาทิตย์เท่านั้น';
                                document.getElementById('endDate').value = '';
                            } else {
                                document.getElementById('endDateError').textContent = '';
                            }
                        }
                    </script>
                    <div class="display-format">
                        <div class="display-old">
                            <span>รูปแบบการทำงานเดิม</span>
                            <span class="detailSpan"><?php echo $rs_sqlday["remark"] ?></span>
                        </div>
                        <div class="display-new">
                            <span>รูปแบบการทำงานใหม่</span>
                            <select name="work_format" id="work_format" class="newFormat" onchange="updateDayoffOptions()">
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
                            <span class="detailSpan"><?php echo $rs_sqlday["day_off1"] ?> -
                                <?php echo $rs_sqlday["day_off2"] ?> </span>
                        </div>
                        <div class="display-day-new">
                            <span>วันหยุดใหม่</span>
                            <select name="dayoff" id="dayoff" class="dayoff" onchange="updateWorkOptions()"></select>
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
                    <div class="display-detail-change">
                        <span>ผู้ตรวจสอบ (ถ้ามี)</span>
                        <select name="inspector" id="desktop-inspector">
                            <option value="">เลือกผู้ตรวจสอบ</option>
                            <?php echo $options_inspector; ?>
                        </select>
                    </div>
                    <div class="display-detail-change">
                        <span>หัวหน้า</span>
                        <input type="hidden" name="approve" value="<?PHP echo $approver_card_id; ?>">
                        <input class="add-detail" type="text" value="<?php echo $fullname_approver; ?>" readonly>
                    </div>
            </div>
            </form>

            <div class="button">
                <div class="confirm">
                    <input type="submit" value="ยืนยัน" onclick="confirm2()" class="btnConfirm">
                </div>
                <div class="reject">
                    <input type="submit" value="ยกเลิก" onclick="cancel()" class="btnReject">
                </div>
            </div>
        </div>
    </div>
</body>
<?php include('../includes/footer.php') ?>