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
<link rel="stylesheet" href="../components-desktop/employee/check-in-detail.css">

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
        check_inout.check_inout_id,
        check_inout.card_id,
        check_inout.date,
        check_inout.approve_status,
        check_inout.time_in,
        check_inout.time_out,
        check_inout.edit_time_in,
        check_inout.edit_time_out,
        check_inout.edit_detail,
        check_inout.edit_attachment,
        check_inout.edit_image,
        check_inout.approver,
        check_inout.inspector,
        approver_employee.firstname_thai AS approver_firstname,
        approver_employee.lastname_thai AS approver_lastname,
        inspector_employee.firstname_thai AS inspector_firstname,
        inspector_employee.lastname_thai AS inspector_lastname
        FROM check_inout 
        LEFT JOIN employee AS approver_employee ON check_inout.approver = approver_employee.card_id
        LEFT JOIN employee AS inspector_employee ON check_inout.inspector = inspector_employee.card_id
        WHERE check_inout_id = ?";

    $params = array(&$check_inout_id);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $fullname_approver = $row['approver_firstname'] . ' ' . $row['approver_lastname'];

        if ($row['inspector_firstname'] == "" && $row['inspector_lastname'] == "") {
            $inspector_fullname = "ไม่มี";
        } else {
            $inspector_fullname = $row['inspector_firstname'] . ' ' . $row['inspector_lastname'];
        }
    }

    $time_in = isset($row['time_in']) ? ($row['time_in']->format('H:i') ?? null) : null;
    $time_out = isset($row['time_out']) ? ($row['time_out']->format('H:i') ?? null) : null;
    $start_time = isset($row["start_time"]) ? $row["start_time"] : null;
    $end_time = isset($row["end_time"]) ? $row["end_time"] : null;
    $symbol = isset($row["symbol"]) ? $row["symbol"] : null;
    $date = isset($row["date"]) ? $row["date"]->format('d-m-y') : null;
    $edit_detail = isset($row["edit_detail"]) ? $row["edit_detail"] : null;
    if (isset($row["edit_image"], $row["edit_attachment"])) {
        // ตรวจสอบว่าทั้ง 'edit_image' และ 'edit_attachment' มีค่าหรือไม่ก่อนที่จะเข้าถึง
        $edit_image = $row["edit_image"];
        $edit_attachment = $row["edit_attachment"];
    } else {
        // กำหนดค่าเริ่มต้นให้กับ $edit_image และ $edit_attachment ในกรณีที่บางคีย์ไม่มีค่า
        $edit_image = isset($row["edit_image"]) ? $row["edit_image"] : null;
        $edit_attachment = isset($row["edit_attachment"]) ? $row["edit_attachment"] : null;
    }
    $Link = "check_inout_id=" . $check_inout_result['check_inout_id'];
} elseif (!empty($_GET["check_inout_date"])) {
    $status = 'ขาดงาน';
    $date = sanitizeInput($_GET["check_inout_date"]);
    $symbol = 'ทำงานปกติ';

    $fullname_approver = "-";
    $inspector_fullname = "-";
    $time_in = 'N/A';
    $time_out = 'N/A';
    $start_time = 'N/A';
    $end_time = 'N/A';
    $symbol = 'ขาดงาน';
    $edit_detail = '-';
    $inspector_fullname = "-";

    if (isset($row["edit_image"], $row["edit_attachment"])) {
        // ตรวจสอบว่าทั้ง 'edit_image' และ 'edit_attachment' มีค่าหรือไม่ก่อนที่จะเข้าถึง
        $edit_image = $row["edit_image"];
        $edit_attachment = $row["edit_attachment"];
    } else {
        // กำหนดค่าเริ่มต้นให้กับ $edit_image และ $edit_attachment ในกรณีที่บางคีย์ไม่มีค่า
        $edit_image = isset($row["edit_image"]) ? $row["edit_image"] : null;
        $edit_attachment = isset($row["edit_attachment"]) ? $row["edit_attachment"] : null;
    }
    $Link = "check_inout_date=" . $date;
}
?>

<!DOCTYPE html>
<html lang="en">

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
                                    <h2>แก้ไขรายละเอียดสถานะการเข้า-ออกงาน</h2>
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
                                            แก้ไขรายละเอียดการเข้า-ออกงาน
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class=" row">
                        <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="bar">
                                    <div class="bar-title">
                                        <label> Status:
                                            <?php
                                            $status = isset($row['approve_status']) ? $row['approve_status'] : 'N/A';
                                            if ($status === 'wait') {
                                                echo '<label style="color: orange;">รออนุมัติ</label>';
                                            } else if ($status === 'approve') {
                                                echo '<label style="color: green;">อนุมัติแล้ว</label>';
                                            } else if ($status === 'reject') {
                                                echo '<label style="color: red;">ปฎิเสธ</label>';
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </label>
                                        <label>Date: <input type="text" value="<?php echo $date; ?>" readonly></label>
                                    </div>
                                </div>

                                <div class="wizard-content">
                                    <form>
                                        <section>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="time-title">
                                                            <label>เวลาเข้างาน</label>
                                                            <label>เวลาออกงาน</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="old-time">
                                                            <label>เวลาเดิม:</label>
                                                            <div class="input-time">
                                                                <input class="form-control" type="text" value="<?php echo $time_in; ?>" readonly />
                                                                <input class="form-control" type="text" value="<?php echo $time_out; ?>" readonly />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="new-time">
                                                            <label>เวลาใหม่:</label>
                                                            <div class="input-time">
                                                                <input class="form-control" type="text" value="<?php echo isset($row['edit_time_in']) ? $row['edit_time_in']->format('H:i') : 'N/A'; ?>" readonly />
                                                                <input class="form-control" type="text" value="<?php echo isset($row['edit_time_out']) ? $row['edit_time_out']->format('H:i') : 'N/A'; ?>" readonly />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="reason">
                                                            <label>เหตุผล:</label>
                                                            <input class="form-control" type="text" value="<?php echo $edit_detail ?>" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="inspector">
                                                            <label>ผู้ตรวจสอบ (ถ้ามี):</label>
                                                            <input class="form-control" type="text" value="<?php echo $inspector_fullname; ?>" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="head">
                                                            <label>หัวหน้า:</label>
                                                            <input class="form-control" type="text" value="<?php echo $fullname_approver; ?>" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="attachment">
                                                            <label>สิ่งที่แนบมาด้วย:</label>
                                                            <div class="display-attachment">
                                                                <?php if ($edit_image != NULL) : ?>
                                                                    <a href="<?php echo $edit_image; ?>" target="_blank">รูป</a>
                                                                <?php else : ?>
                                                                    <span>ไม่มีไฟล์รูป</span>
                                                                <?php endif ?>

                                                                <?php if ($edit_attachment != NULL) : ?>
                                                                    <a href="<?php echo $edit_attachment; ?>" target="_blank"><i class="fa-solid fa-file-pdf"></i></a>
                                                                <?php else : ?>
                                                                    <span>ไม่มีไฟล์ PDF</span>
                                                                <?php endif ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12" style="display:flex;justify-content:flex-end;margin-top:10px">
                                                    <div class="form-group">
                                                        <input type="button" value="แก้ไข" class="btn-primary" onclick="editForm()">
                                                        <script>
                                                            function editForm() {
                                                                window.location.href =
                                                                    'check-in-edit.php?<?php echo $Link; ?>';
                                                            }

                                                            function submit_back() {
                                                                window.history.back();
                                                            }
                                                        </script>
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

    <div class=" mobile">
        <div class="navbar">
            <div class="div-span">
                <span>สถานะการเข้างาน</span>
            </div>
        </div>

        <center>
            <div class="topic-detail-status">
                <span>รายละเอียดสถานะการเข้างาน</span>
            </div>
        </center>

        <div class="status-checkIn">
            <div class="display-status-date">
                <label style="width: 65%;text-align: center; margin-bottom:2%; font-weight: bold;">Status:
                    <?php
                    $status = $row['approve_status'];
                    if ($status === 'wait') {
                        echo '<span style="color: orange;">รออนุมัติ</span>';
                    } else if ($status === 'approve') {
                        echo '<span style="color: green;">อนุมัติแล้ว</span>';
                    } else if ($status === 'reject') {
                        echo '<span style="color: red;">ปฎิเสธ</span>';
                    } else {
                        echo '-';
                    }
                    ?>
                </label>
                <label style="width: 65%; text-align: center; margin-bottom: 2%; font-weight: bold; ">Date:
                    <input type="text" style="width: 50%; padding: 2%; box-sizing: border-box; text-align: center; font-weight: bold; border: 1px solid #ccc; border-radius: 10px; font-family: 'Chakra Petch', sans-serif; color: #818181;" value="<?php echo $row['date']->format('d/m/y') ? $row['date']->format('d/m/y') : 'N/A'; ?>" readonly><br></label>
            </div>


            <form>
                <!-- Repeat similar input fields for other columns -->
                <hr>

                <div class="timeOldNew">
                    <span>เวลาเข้างาน</span>
                    <span>เวลาออกงาน</span>
                </div>

                <div class="old-time">
                    <label>เวลาเดิม</label>
                    <div class="time-inout">
                        <input type="text" value=" <?php echo isset($row['time_in']) ? ($row['time_in']->format('H:i') ?? 'N/A') : 'N/A'; ?>" readonly />
                        <input type="text" value="<?php echo isset($row['time_out']) ? ($row['time_out']->format('H:i') ?? 'N/A') : 'N/A'; ?>" readonly />
                    </div>
                </div>

                <div class="new-time">
                    <label>เวลาใหม่ </label>
                    <div class="time-inout">
                        <input type="text" value="<?php echo $row['edit_time_in']->format('H:i'); ?>" readonly />
                        <input type="text" value="<?php echo $row['edit_time_out']->format('H:i'); ?>" readonly />
                    </div>
                </div>

                <!-- Repeat similar input fields for other columns -->
                <div class="reason-edit">
                    <label>เหตุผล</label>
                    <input type="text" value="<?php echo $row['edit_detail']; ?>" readonly>
                </div>

                <div class="inspector-edit">
                    <label>ผู้ตรวจสอบ</label>
                    <input type="text" value="<?php echo $inspector_fullname; ?>" readonly>
                </div>

                <div class="approver-edit">
                    <label>หัวหน้า</label>
                    <input type="text" value="<?php echo $approver_fullname; ?>" readonly>

                </div>

                <div class="display-file-img">
                    <label>สิ่งที่แนบมาด้วย:</label>
                    <div class="display-img">
                        <?php if ($row['edit_image'] == NULL) : ?>
                        <?php else : ?>
                            <a href="<?php echo $row['edit_image']; ?>" alt="">รูป</a>
                        <?php endif ?>
                        <?php if ($row['edit_attachment'] == NULL) : ?>
                        <?php else : ?>
                            <a href="<?php echo $row['edit_attachment']; ?>">ไฟล์ PDF</a>
                        <?php endif ?>
                    </div>
                </div>
                <br>
            </form>
        </div>


        <!-- Add an edit button if you want to allow editing -->
        <div class="btn-edit">
            <input type="button" value="แก้ไข" onclick="editForm()">
            <script>
                function editForm() {
                    window.location.href =
                        'check-in-edit.php?<?php echo $Link; ?>';
                }

                function submit_back() {
                    window.history.back();
                }
            </script>
        </div>
    </div>

</body>
<?php include('../includes/footer.php') ?>