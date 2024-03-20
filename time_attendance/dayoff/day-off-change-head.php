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
<link rel="stylesheet" href="../components-desktop/head/day-off-change-head.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/day-off-change.css">

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="script/loader-normal.js"></script>

<?php

$select_team = "SELECT * FROM sub_team WHERE head_card_id = ?";
$dayoff_stmt = sqlsrv_prepare($conn, $select_team, array(&$_SESSION["card_id"]));
sqlsrv_execute($dayoff_stmt);
$row = sqlsrv_fetch_array($dayoff_stmt, SQLSRV_FETCH_ASSOC);

//------------------------------------------------------------------------------------------

$select_dayoffteam = "SELECT employee.firstname_thai AS firstname_thai,
                                employee.lastname_thai AS lastname_thai,
                                employee.card_id AS card_id,
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


//--------------------------------------------------------------------------------------------

$sql = "SELECT * FROM work_format";
$work_format = sqlsrv_query($conn, $sql);
$work_formatdata = array();
while ($row = sqlsrv_fetch_array($work_format, SQLSRV_FETCH_ASSOC)) {
    $work_formatdata[] = $row;
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

    function showEmployeeDayOff1() {
        var employeeid = document.getElementById("employeeid1").value;
        var remarkDisplay = document.getElementById("remarkDisplay1");
        var dayoffDisplay = document.getElementById("dayoffDisplay1");
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
<script type="text/javascript">
    function confirm() {
        swal.fire({
            html: '<div style="font-weight: bold; font-size: 4vw;">ยืนยันการยื่นคำขอ</div><br>' +
                '<img class="img" src="../IMG/question 1.png"></img>',
            padding: '2em',
            confirmButtonText: 'ตกลง',
            confirmButtonColor: '#f1ba3d',
            showCancelButton: true,
            cancelButtonText: 'ยกเลิก',
            cancelButtonColor: '#FF5643',
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

    function submit() {
        swal.fire({
            html: '<div style="font-weight: bold; font-size: 4vw;">ยื่นคำขอสำเร็จ</div><br>' +
                '<img class="img" src="../IMG/check1.png"></img>',
            padding: '2em',
            confirmButtonText: 'ตกลง',
            confirmButtonColor: '#00d042',
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

    function confirm1() {
        swal.fire({
            html: '<div style="font-weight: bold; font-size: 4vw;">ยืนยันการยื่นคำขอ</div><br>' +
                '<img class="img" src="../IMG/question 1.png"></img>',
            padding: '2em',
            confirmButtonText: 'ตกลง',
            confirmButtonColor: '#f1ba3d',
            showCancelButton: true,
            cancelButtonText: 'ยกเลิก',
            cancelButtonColor: '#FF5643',
            customClass: {
                confirmButtonText: 'swal2-confirm',
                cancelButtonText: 'swal2-cancel',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                submit1(result);
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });

    }

    function submit1() {
        swal.fire({
            html: '<div style="font-weight: bold; font-size: 4vw;">ยื่นคำขอสำเร็จ</div><br>' +
                '<img class="img" src="../IMG/check1.png"></img>',
            padding: '2em',
            confirmButtonText: 'ตกลง',
            confirmButtonColor: '#00d042',
            customClass: {
                confirmButtonText: 'swal2-confirm',
                cancelButtonText: 'swal2-cancel',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('addDayOffForm1').submit();
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
            confirmButtonColor: '#ECCD03',
            cancelButtonColor: '#FF0000',
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
                                    <form id="addDayOffForm" action="../processing/process_dayoff_working_head_Request.php" method="post">
                                        <section>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="employeeid">เลือกชื่อพนักงาน</label>
                                                        <select name="employeeid" id="employeeid" class=" js-example-basic-single custom-selelct form-control " onchange=" showEmployeeDayOff()">
                                                            <option value="">เลือกสมาชิกในทีม</option>
                                                            <?php
                                                            foreach ($work_dayoffteam as $rs_emp) {
                                                                echo '<option value="' . $rs_emp['card_id'] . '">' . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai'] . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="employeeid">วันเริ่มต้น
                                                            (โปรดเลือกวันจันทร์เท่านั้น)</label>
                                                        <input class="form-control" type="date" name="startDate" id="startDate">
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="employeeid">วันสิ้นสุด
                                                            (โปรดเลือกวันอาทิตย์เท่านั้น)</label>
                                                        <input class="form-control" type="date" name="endDate" id="endDate">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="remarkDisplay">รูปแบบการทำงานเดิม</label>
                                                        <span class="detailSpan form-control" id="remarkDisplay"> -
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="work_format">รูปแบบการทำงานใหม่</label>
                                                        <select name="work_format" id="work_format" class="newFormat custom-select form-control" onchange="updateDayoffOptions()">
                                                            <option value="">เลือกรูปแบบ</option>
                                                            <option value="ปกติ(STS)">ปกติ(STS)</option>
                                                            <option value="ปกติ(CPAC)">ปกติ(CPAC)</option>
                                                            <option value="กะ(STS)">กะ(STS)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="dayoffDisplay">วันหยุดเดิม</label>
                                                        <span class="detailSpan form-control" id="dayoffDisplay"> -
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="dayoff">วันหยุดใหม่</label>
                                                        <select name="dayoff" id="dayoff" class="dayoff custom-select form-control" onchange="updateWorkOptions()"></select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="work">เลือกรูปแบบกะ</label>
                                                        <select name="work" id="work" class="custom-select form-control"></select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="detail">โปรดระบุเหตุผล</label>
                                                        <input class="add-detail form-control" name="detail" id="detail"></input>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="detail">หัวหน้า</label>
                                                        <input type="hidden" name="approve" value="<?PHP echo $approver_card_id; ?>">
                                                        <input type="text" class="form-control" value="<?php echo $fullname_approver; ?>" style="background-color:transparent" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="col-sm-12 text-center">
                                                <div class="dropdown">
                                                    <div class="btn btn-primary" onclick="confirm()">ยืนยัน</div>
                                                    <div class=" btn btn-danger" onclick="cancel()">ยกเลิก
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
                <span>ขอเปลี่ยนรูปแบบวันหยุดและการทำงาน</span>
            </div>
        </div>
        <div class="container">
            <form id="addDayOffForm1" action="../processing/process_dayoff_working_head_Request.php" method="post">
                <div class="select-nameEm">
                    <span>เลือกชื่อพนักงาน</span>
                    <select name="employeeid" id="employeeid1" class="js-example-basic-single" onchange="showEmployeeDayOff1()">
                        <option value="">เลือกสมาชิกในทีม</option>
                        <?php
                        foreach ($work_dayoffteam as $rs_emp) {
                            echo '<option value="' . $rs_emp['card_id'] . '">' . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="display-date-start">
                    <span>วันเริ่มต้น(โปรดเลือกวันจันทร์เท่านั้น)</span>
                    <input type="date" name="startDate" id="startDate">
                </div>
                <div class="display-date-start">
                    <span>วันสิ้นสุด(โปรดเลือกวันอาทิตย์เท่านั้น)</span>
                    <input type="date" name="endDate" id="endDate">
                </div>
                <div class="display-format">
                    <div class="display-old">
                        <span>รูปแบบการทำงานเดิม</span>
                        <span class="detailSpan" id="remarkDisplay1"> - </span>
                    </div>
                    <div class="display-new">
                        <span>รูปแบบการทำงานใหม่</span>
                        <select name="work_format" id="work_format1" class="newFormat" onchange="updateDayoffOptions1()">
                            <option value="">เลือกรูปแบบการทำงาน</option>
                            <option value="ปกติ(STS)">ปกติ(STS)</option>
                            <option value="ปกติ(CPAC)">ปกติ(CPAC)</option>
                            <option value="กะ(STS)">กะ(STS)</option>
                        </select>
                    </div>
                </div>
                <div class="display-dayOff">
                    <div class="display-day-old">
                        <span>วันหยุดเดิม</span>
                        <span class="detailSpan" id="dayoffDisplay1"> - </span>
                    </div>
                    <div class="display-day-new">
                        <span>วันหยุดใหม่</span>
                        <select name="dayoff" id="dayoff1" class="dayoff" onchange="updateWorkOptions1()"></select>
                    </div>
                </div>
                <div class="display-shift">
                    <span>เลือกรูปแบบกะ</span>
                    <select name="work" id="work1"></select>
                </div>
                <div class="display-detail-change">
                    <span>โปรดระบุเหตุผล</span>
                    <input class="add-detail" name="detail" id="detail"></input>
                </div>
        </div>
        </form>

        <div class="button">
            <div class="confirm">
                <input type="submit" value="ยืนยัน" onclick="confirm1()" class="btnConfirm">
            </div>
            <div class="reject">
                <input type="submit" value="ยกเลิก" onclick="cancel()" class="btnReject">
            </div>
        </div>
    </div>
</body>
<?php include('../includes/footer.php') ?>