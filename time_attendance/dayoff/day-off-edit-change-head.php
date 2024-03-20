<?php
session_start();
session_regenerate_id(true);
//---------------------------------------------------------------------------------------
include("../database/connectdb.php");
include('../components-desktop/head/include/header.php');
// include("dbconnect.php");
include("update_dayoff.php");
//---------------------------------------------------------------------------------------
?>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/head/day-off-edit-change-head.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/day-off-detail-status.css">

<!-- Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
function confirm() {
    swal.fire({
        html: '<div style="font-weight: bold; font-size: 4vw;">แก้ไขคำขอสำเร็จ</div><br>' +
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


function cancel() {
    swal.fire({
        html: '<div style="font-weight: bold; font-size: 4vw;">ยืนยันการยกเลิกคำขอ</div><br>' +
            '<img class="img" src="../IMG/question 1.png"></img>',
        padding: '2em',
        confirmButtonText: 'ตกลง',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#ECCD03',
        cancelButtonColor: '#FF0000',
        showCancelButton: true,

        customClass: {
            confirmButtonText: 'swal2-confirm',
            cancelButtonText: 'swal2-cancel',
        },
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'day-off-change-history-head.php';
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
                                    <h2>แก้ไขคำขอเปลี่ยนวันหยุด</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor:default;">วันหยุด</a>
                                        </li>
                                        <li class=" breadcrumb-item">
                                            <a href="day-off-change-history-head.php">รายการขออนุมัติเปลี่ยนวันหยุด</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            แก้ไขคำขอเปลี่ยนวันหยุด
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class=" row">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10">
                                <div class="employee-image">
                                    <img src="<?php echo (!empty($row['employee_employee_image'])) ? '../../admin/uploads_img/' . $row['employee_employee_image'] : '../IMG/user.png'; ?>"
                                        alt="">
                                </div>
                                <div class="employee-info">
                                    <label>รหัสพนักงาน: <?= $row['employee_scg_employee_id'] ?></label>
                                    <label>ชื่อ-สกุล:
                                        <?= $row['employee_prefix_thai'] . $row['employee_firstname_thai'] . " " . $row['employee_lastname_thai'] ?></label>
                                    <label>ตำแหน่ง: <?= $row['emp_position_name_thai'] ?></label>
                                    <label>Cost Center: <?= $row['cost_center_code'] ?></label>
                                    <label>หน่วยงาน: <?= $row['section_name_thai'] ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="wizard-content">
                                    <form>
                                        <section>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="old-dayoff text-center">
                                                            <label>วันหยุดประจำสัปดาห์ (เก่า)</label>
                                                            <div class="display-day-week">
                                                                <?php
                                                                // Loop through the days of the week
                                                                for ($i = 1; $i <= 7; $i++) {
                                                                    // Check if the current day is a day off
                                                                    $isDayOff = ($i == $day_off_old1 || $i == $day_off_old2);

                                                                    // Add the appropriate CSS class based on whether it's a day off or not
                                                                    $cssClass = $isDayOff ? 'dayoff' : '';

                                                                    // Output the day with the corresponding CSS class
                                                                    echo '<label class="' . $cssClass . '">' . getThaiDay($i) . '</label>';
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="new-dayoff text-center">
                                                            <label>วันหยุดประจำสัปดาห์ (ใหม่)</label>
                                                            <div class="display-day-week">
                                                                <?php
                                                                // Loop through the days of the week
                                                                for ($i = 1; $i <= 7; $i++) {
                                                                    // Check if the current day is a day off
                                                                    $isDayOff = ($i == $day_off_new1 || $i == $day_off_new2);

                                                                    // Add the appropriate CSS class based on whether it's a day off or not
                                                                    $cssClass = $isDayOff ? 'dayoff' : '';

                                                                    // Output the day with the corresponding CSS class
                                                                    echo '<label class="' . $cssClass . '">' . getThaiDay($i) . '</label>';
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="date text-center">
                                                            <label>วันที่เริ่มเปลี่ยน</label>
                                                            <input class="form-control" type="text"
                                                                value="<?php echo $thaiDate; ?>" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="detail text-center">
                                                            <label>เหตุผลที่ขอเปลี่ยน</label>
                                                            <input class="form-control" type="text"
                                                                value="<?php echo isset($row['detail']) ? $row['detail'] : '-'; ?>"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="inspector text-center">
                                                            <label>ผู้ตรวจสอบ</label>
                                                            <input class="form-control" type="text" value="<?php
                                                                                                            echo isset($row['inspector_firstname_thai']) && isset($row['inspector_lastname_thai'])
                                                                                                                ? $row['inspector_firstname_thai'] . " " . $row['inspector_lastname_thai']
                                                                                                                : '-';
                                                                                                            ?>"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12 ">
                                                    <div class="form-group">
                                                        <div class="head text-center">
                                                            <label>หัวหน้า</label>
                                                            <input class="form-control" type="text"
                                                                value="<?php echo $row['approver_prefix_thai'] . $row['approver_firstname_thai'] . " " . $row['approver_lastname_thai']; ?>"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 text-center">
                                                <div class="dropdown">
                                                    <div class="btn btn-primary status" onclick="confirm()">ยืนยัน
                                                    </div>
                                                    <div class="btn btn-danger status" onclick="cancel()">ยกเลิก</div>
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
                <span>คำขอเปลี่ยนวันหยุด</span>
            </div>
        </div>
        <div class="container">
            <div class="display-name-em">
                <img src="../IMG/user.png" alt="">
                <span>รายละเอียดคำขอ</span>
            </div>
        </div>
        <div class="container-detail">
            <div class="display-dayoff-old">
                <span class="dayTopic">วันหยุดประจำสัปดาห์เดิม</span>
                <div class="display-day-week">
                    <span class="dayoff">อา.</span>
                    <span>จ.</span>
                    <span>อ.</span>
                    <span>พ.</span>
                    <span>พฤ.</span>
                    <span>ศ.</span>
                    <span class="dayoff">ส.</span>
                </div>
            </div>
            <div class="display-dayoff-new">
                <span class="dayTopic">วันหยุดประจำสัปดาห์ใหม่</span>
                <div class="display-day-week">
                    <button>อา.</button>
                    <button>จ.</button>
                    <button>อ.</button>
                    <button>พ.</button>
                    <button>พฤ.</button>
                    <button>ศ.</button>
                    <button>ส.</button>
                </div>
            </div>
            <div class="reason-change">
                <span class="dayTopic">เหตุผลการขอเปลี่ยนวันหยุด</span>
                <span class="reason">วันหยุดอาจเปลี่ยนไป แต่หัวใจยังคนเดิม</span>
            </div>
            <div class="display-approve">
                <span>ผู้ร้องขอ :
                    <?php echo $row['emp2_prefix_thai'] . $row['emp2_firstname_thai'] . " " . $row['emp2_lastname_thai']; ?>
                </span>
                <span>ชื่อผู้ตรวจสอบ :</span>
                <span>ช่อผู้อนุมัติ : เอกพงศ์ มีสุข</span>
                <!-- <span>ตำแหน่ง : Manager</span>
                <span>อีเมลล์ : Ekkaphong@scg.com</span> -->
            </div>
        </div>
        <div class="button">
            <div class="submit">
                <input type="submit" value="ยืนยัน" onclick="confirm('')" class="btnConfirm">
            </div>
            <div class="reject">
                <input type="submit" value="ยกเลิก" onclick="cancel('')" class="btnReject"><br><br>
            </div>
        </div>
    </div>
</body>
<?php include('../includes/footer.php') ?>