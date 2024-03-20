<?php
session_start();
session_regenerate_id(true);

header("Cache-Control: no-cache, must-revalidate");

include("../database/connectdb.php");
include('../components-desktop/employee/include/header.php');
?>

<!-- Script -->
<link rel="stylesheet" href="node_modules\sweetalert2\dist\sweetalert2.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Include Select2  -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI/t1f1qODW6fzZll4H5veNvJfyl26D8qx2vNc5A=" crossorigin="anonymous"></script>


<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/employee/check-in-edit.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/check-in-detail-edit.css">
<link rel=" stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="../assets/script/loader-normal.js"></script>

<?php
function sanitizeInput($input)
{
    return $input;
}


if (!empty($_GET["check_inout_id"])) {
    $check_inout_id = sanitizeInput($_GET["check_inout_id"]);

    $query = "SELECT 
    check_inout.card_id AS check_inout_card_id,
    check_inout.shift_type_id,
    check_inout.date,
    time_in,
    time_out,
    start_time,
    end_time,
    manager_card_id,
    firstname_thai,
    lastname_thai
FROM 
    check_inout 
    LEFT JOIN shift_type ON check_inout.shift_type_id = shift_type.shift_type_id
    LEFT JOIN manager ON check_inout.card_id = manager.card_id
    LEFT JOIN employee ON manager.manager_card_id = employee.card_id
WHERE check_inout.card_id = ? AND check_inout_id = ?";
    $params = array($_SESSION["card_id"], $check_inout_id);

    // Attempt to execute the query
    $stmt = sqlsrv_query($conn, $query, $params);

    // Fetch the data
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    $approver_firstname = $row['firstname_thai'];
    $approver_lastname = $row['lastname_thai'];
    $approver_card_id = $row['manager_card_id'];
    $fullname_approver = $approver_firstname . ' ' . $approver_lastname;

    $time_in = isset($row['time_in']) ? ($row['time_in']->format('H:i') ?? null) : null;
    $time_out = isset($row['time_out']) ? ($row['time_out']->format('H:i') ?? null) : null;


    $start_time = isset($row["start_time"]) ? $row["start_time"] : null;
    $end_time = isset($row["end_time"]) ? $row["end_time"] : null;
    $symbol = isset($row["symbol"]) ? $row["symbol"] : null;

    $date = isset($row["date"]) ? $row["date"]->format('d-m-y') : null;

    if ($time_in >= $start_time) {
        $status = 'มาสาย';
    } elseif ($time_in == "" && $time_out == "") {
        $status = 'ขาดงาน';
    } elseif ($time_in <= $start_time && $time_out >= $end_time) {
        $status = 'เข้างานตรงเวลา';
    } elseif ($time_in <= $start_time && $time_out <= $end_time) {
        $status = 'กลับก่อน';
    }
    if ($symbol == "normal1") {
        $symbol = "กะปกติ 1";
    } elseif ($symbol == "normal2") {
        $symbol = "กะปกติ 2";
    } elseif ($symbol == "1") {
        $symbol = "กะ 1";
    } elseif ($symbol == "2") {
        $symbol = "กะ 2";
    } elseif ($symbol == "3") {
        $symbol = "กะ 3";
    }

    $SELECTapprover = "SELECT card_id, scg_employee_id, firstname_thai, lastname_thai FROM employee WHERE permission_id = ? and card_id != ?";
    $paramsapprover = array('2', $_SESSION["card_id"]);
    $stmtapprover = sqlsrv_query($conn, $SELECTapprover, $paramsapprover);
    $options_inspector = '';
    while ($rs_emp = sqlsrv_fetch_array($stmtapprover, SQLSRV_FETCH_ASSOC)) {
        $options_inspector .= '<option value="' . $rs_emp['card_id'] . '">' . $rs_emp['scg_employee_id'] . ' ' . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai'] . '</option>';
    }
} elseif (!empty($_GET["check_inout_date"])) {
    $status = 'ขาดงาน';
    $date = sanitizeInput($_GET["check_inout_date"]);
    $symbol = 'ทำงานปกติ';

    $query = "SELECT manager_card_id,firstname_thai,lastname_thai FROM manager INNER JOIN employee ON manager.manager_card_id = employee.card_id WHERE manager.card_id = ? ";
    $params = array($_SESSION["card_id"]);
    $stmt = sqlsrv_query($conn, $query, $params);
    $row2 = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    $approver_firstname = $row2['firstname_thai'];
    $approver_lastname = $row2['lastname_thai'];
    $approver_card_id = $row2['manager_card_id'];
    $fullname_approver = $approver_firstname . ' ' . $approver_lastname;

    $SELECTapprover = "SELECT card_id, scg_employee_id, firstname_thai, lastname_thai FROM employee WHERE permission_id = ? and card_id != ?";
    $paramsapprover = array('2', $approver_card_id);
    $stmtapprover = sqlsrv_query($conn, $SELECTapprover, $paramsapprover);
    $options_inspector = '';
    while ($rs_emp = sqlsrv_fetch_array($stmtapprover, SQLSRV_FETCH_ASSOC)) {
        $options_inspector .= '<option value="' . $rs_emp['card_id'] . '">' . $rs_emp['scg_employee_id'] . ' ' . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai'] . '</option>';
    }
}

// Further processing or rendering based on $status and $date can be added here
?>


<script>
    function submit_back() {
        window.history.back();
    }

    function displayFileName(infoId) {
        const fileInput = event.target;
        const fileInfo = document.getElementById(infoId);
        const files = fileInput.files;

        if (files.length > 0) {
            fileInfo.textContent = `ไฟล์ที่เลือก: ${files[0].name}`;
        } else {
            fileInfo.textContent = "ไม่มีไฟล์ที่เลือก";
        }
    }

    function cancelFile(inputId, infoId) {
        const fileInput = document.getElementById(inputId);
        const fileInfo = document.getElementById(infoId);

        fileInput.value = null;
        fileInfo.textContent = "ไฟล์ถูกยกเลิก";
    }

    function showInspector() {
        document.getElementById('inspectorshow').style.display = '';
    }

    function hideInspector() {
        document.getElementById('inspectorshow').style.display = 'none';
    }

    function myFunction() {
        swal.fire({
            html:
                // '<img class="d-flex; align-items: center; justify-content-center" src="../../images/warningyall.png" style="width:45%px;  height:95%;"></img>' +
                '<div style="font-weight: bold; font-size: 20px;">ยืนยันการแก้ไข</div><br>' +
                '<img src="../IMG/question 1.png" style="width:80px; margin-top: -10px;  height:80px;"></img>',
            padding: '2em',
            confirmButtonText: 'ตกลง',
            confirmButtonColor: '#95E302',
            showCancelButton: true,
            cancelButtonText: 'ยกเลิก',
            cancelButtonColor: '#FF5643',
            customClass: {
                confirmButtonText: 'swal2-confirm',
                cancelButtonText: 'swal2-cancel',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "check-in-attendance-schedule-edit.php?transaction=";
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });
    }


    // desktop

    function show_inspector() {
        document.getElementById('desktop-inspector').style.display = '';
    }

    function hide_inspector() {
        document.getElementById('desktop-inspector').style.display = 'none';
    }

    function check_edit_submit() {
        Swal.fire({
            title: "<strong>ยืนยันการแก้ไขเวลาหรือไม่</strong>",
            icon: "question",
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: `ตกลง`,
            cancelButtonText: `ยกเลิก`,
        }).then((result) => {
            if (result.isConfirmed) {
                check_edit_confirm()
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });
    }

    function check_edit_confirm() {
        Swal.fire({
            title: "<strong>ขอแก้ไขเวลาสำเร็จ</strong>",
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
        form.action = '../processing/process_check_in_edit.php';
        form.method = 'POST';
        form.submit();
    }

    function check_edit_cancel() {
        Swal.fire({
            title: "<strong>ยืนยันยกเลิกแก้ไขเวลาหรือไม่</strong>",
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
                                    <h2>แก้ไขเวลาเข้า-ออกงาน</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor:default;">ลงชื่อเข้า-ออก</a>
                                        </li>
                                        <li class=" breadcrumb-item">
                                            <a href="check-in-attendance-schedule.php">ประวัติการลงชื่อเข้า-ออก</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            แก้ไขเวลาเข้า-ออกงาน
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class=" row">
                        <div class="col-lg-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="bar">
                                    <div class="bar-title">
                                        <label><?= $date ?></label>
                                        <label><?= $symbol ?></label>
                                        <label><?= $status ?></label>
                                    </div>

                                </div>

                                <div class="wizard-content">
                                    <form id="desktop-form">
                                        <input type="hidden" name="transactioncheckin_id" value="<?php echo $check_inout_id; ?>">
                                        <input type="hidden" name="date" value="<?= htmlspecialchars($date); ?>">
                                        <input type="hidden" name="supervisor" value="<?= $approver_card_id; ?>">
                                        <section>
                                            <div class="row">
                                                <div class="col-md-9 col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-5">
                                                            <div class="form-group text-center">
                                                                <label>เวลาเข้างาน</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-5">
                                                            <div class="form-group text-center">
                                                                <label>เวลาออกงาน</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-9 col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>เวลาเดิม:</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-5">
                                                            <div class="form-group">
                                                                <input class="form-control" type="text" value="<?php echo isset($row['time_in']) ? ($row['time_in']->format('H:i') ?? 'N/A') : 'ขาดงาน'; ?>" readonly />
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-5">
                                                            <div class="form-group">
                                                                <input class="form-control" type="text" value="<?php echo isset($row['time_out']) ? ($row['time_out']->format('H:i') ?? 'N/A') : 'ขาดงาน'; ?>" readonly />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-9 col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>เวลาใหม่:</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-5">
                                                            <div class="form-group">
                                                                <input class="form-control" type="time" name="edit_time_in" id="edit_time_in">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-5">
                                                            <div class="form-group">
                                                                <input class="form-control" type="time" name="edit_time_out" id="edit_time_out">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-9 col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>เหตุผล:</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-10">
                                                            <div class="form-group">
                                                                <textarea class="form-control" name="reason" id="reason" rows="4" cols="50" required></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-9 col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>ผู้ตรวจสอบ (ถ้ามี):</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-10">
                                                            <div class="form-group" style="display:flex;gap:10px;">
                                                                <div class="select-radio">
                                                                    <input type="radio" class="radio-y" id="radio-y" name="inspectorStatus" value="มี" onclick="show_inspector()">
                                                                    <label for="radio-y">มี</label>
                                                                </div>
                                                                <div class="select-radio">
                                                                    <input type="radio" class="radio-n" id="radio-n" name="inspectorStatus" value="ไม่มี" onclick="hide_inspector()">
                                                                    <label for="radio-n">ไม่มี</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" id="desktop-inspector" style="display: none;">
                                                <div class="col-md-9 col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-10">
                                                            <div class="form-group">
                                                                <div>
                                                                    <select class="custom-select form-control" name="inspector" id="inspector" class="js-example-basic-single">
                                                                        <option value="">เลือกผู้ตรวจสอบ
                                                                        </option>
                                                                        <?php
                                                                        echo $options_inspector;
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-9 col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>หัวหน้า:</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-10">
                                                            <div class="form-group">
                                                                <input class="form-control" type="text" name="" id="" value="<?php echo $fullname_approver; ?> " readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-9 col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label>เลือกไฟล์:</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-10">
                                                            <div class="form-group">
                                                                <div class="file-attachment" id="file1">
                                                                    <label for="image-attachment">
                                                                        <img src="../IMG/camera.png" alt="Image Button" onclick="displayFileName(' image-file-info')">
                                                                        <input type="file" id="image-attachment" name="image-attachment" onchange="displayFileName('image-file-info')" accept="image/*" style="display: none;">
                                                                    </label>
                                                                    <label for="pdf-attachment">
                                                                        <img src="../IMG/file.png" alt="file Button" onclick="displayFileName('pdf-file-info')">
                                                                        <input type="file" id="pdf-attachment" name="pdf-attachment" onchange="displayFileName('pdf-file-info')" accept=".pdf" style="display: none;">
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-9 col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-10">
                                                            <div class="form-group">
                                                                <div class="div-file">
                                                                    <div class="file-name">
                                                                        <div id="image-file-info"></div>
                                                                        <div id="pdf-file-info"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    </form>
                                    <div class="row">
                                        <div class="col-md-9 col-sm-12" style="display:flex;justify-content:flex-end;margin-top:10px">
                                            <div class="form-group">
                                                <input type="button" value="ยืนยัน" class="btn-primary" onclick="check_edit_submit()">
                                                <input type="button" value="ยกเลิก" class="btn-danger" onclick="check_edit_cancel()">
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
                <span>ทำรายการขอแก้ไข</span>
            </div>
        </div>
        <center>
            <div class="topic-detail-status">
                <span>แก้ไขรายละเอียดสถานะการเข้างาน</span>
            </div>
        </center>

        <div class="today-status">
            <span>
                <?= $date ?>
            </span>
            <span>
                <?= $symbol ?>
            </span>
            <span>
                <?= $status ?>
            </span>
        </div>


        <div class="status-checkIn">
            <form action="../processing/process_check_in_edit.php" method="post" enctype="multipart/form-data">

                <div class="timeOldNew">
                    <span>เวลาเข้างาน</span>
                    <span>เวลาออกงาน</span>
                </div>

                <input type="hidden" name="transactioncheckin_id" value="<?php echo $check_inout_id; ?>">
                <input type="hidden" name="date" value="<?= htmlspecialchars($date); ?>">
                <input type="hidden" name="supervisor" value="<?= $approver_card_id; ?>">

                <!-- Original Check-in Time -->
                <div class="old-time">
                    <label>เวลาเดิม</label>
                    <div class="time-inout">
                        <!-- Original Check-in Time -->
                        <input type="text" value="<?php echo isset($row['time_in']) ? ($row['time_in']->format('H:i') ?? 'N/A') : 'N/A'; ?>" />
                        <!-- Original Check-out Time -->
                        <input type="text" value="<?php echo isset($row['time_out']) ? ($row['time_out']->format('H:i') ?? 'N/A') : 'N/A'; ?>" />
                    </div>
                </div>


                <div class="new-time">
                    <label>เวลาใหม่</label>
                    <div class="time-inout">
                        <!-- New Check-in/out Time -->
                        <input type="time" name="edit_time_in" id="edit_time_in">
                        <input type="time" name="edit_time_out" id="edit_time_out">
                    </div>
                </div>

                <!-- Reason -->
                <div class="reason-edit">
                    <label>เหตุผล</label>
                    <textarea name="reason" id="reason" rows="4" cols="50" required></textarea>
                </div>

                <!-- Inspector -->
                <div class="inspector-edit">
                    <label>ผู้ตรวจสอบ</label>
                    <input type="radio" class="radio-y" id="radio-y" name="inspectorStatus" value="มี" onclick="showInspector()"><label for="radio-y">มี</label>
                    <input type="radio" class="radio-n" id="radio-n" name="inspectorStatus" value="ไม่มี" onclick="hideInspector()"><label for="radio-y">ไม่มี</label>

                    <div id="inspectorshow" style="display: none;">
                        <select name="inspector" id="inspector" class="js-example-basic-single">
                            <option value="">เลือกผู้ตรวจสอบ</option>
                            <?php
                            echo $options_inspector;
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Supervisor -->
                <div class="approver-edit">
                    <label>หัวหน้า</label>
                    <input type="text" name="" id="" value="<?php echo $fullname_approver; ?> " readonly>
                </div>

                <!-- Attachment -->
                <div class="select-img-file">
                    <label>เลือกไฟล์:</label>
                    <!-- File input for images -->
                    <div class="file-label" id="file1">
                        <label for="imageAttachment">
                            <img src="../IMG/camera.png" alt="Image Button" onclick="displayFileName('image-file-info')">
                            <input type="file" id="imageAttachment" name="imageAttachment" onchange="displayFileName('image-file-info')" accept="image/*" style="display: none;">
                        </label>

                        <label for="pdfAttachment">
                            <img src="../IMG/file.png" alt="file Button" onclick="displayFileName('pdf-file-info')">
                            <input type="file" id="pdfAttachment" name="pdfAttachment" onchange="displayFileName('pdf-file-info')" accept=".pdf" style="display: none;">
                        </label>
                    </div>
                </div>

                <!-- Display file info (optional, adjust as needed) -->
                <div id="image-file-info"></div>
                <div id="pdf-file-info"></div>

            </form>
        </div>
        <div class="btn-submit-edit">
            <input type="submit" value="ยืนยัน" class="btnConfirm">
        </div>
    </div>

</body>
<?php include('../includes/footer.php'); ?>