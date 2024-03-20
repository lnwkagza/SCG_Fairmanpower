<?php
include('../includes/header.php');
require_once('../database/connectdb.php');
require_once('../gps/inspector_gps_approval_query.php');
require_once('../check-in/test.php');
echo $_SESSION["card_id"];
?>
<!-- <meta http-equiv="Content-Security-Policy" content="default-src 'self'"> -->

<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/approval_gps.css">

</head>

<title>[คำร้องชั่วคราว] จัดการคำร้อง </title>

<body>
    <div class="navbar">
        <div class="div-span">
            <span>รายการอนุมัติการทำงานนอกสถานที่</span>
        </div>
    </div>

    <div class="container">
        <!-- --ส่วนของการจัดทีม-- -->
        <div class="tab-content mt-2 container-manageTable ">

            <div id="div-approval">
                <div class="topic">
                    <span>คำร้องขอทำงานนอกสถานที่ของพนักงาน</span>
                </div>

                <div class="display-transection">
                    <div class="topic-manage">
                        <span class="cost-center">จัดการคำร้องทำงานนอกสถานที่[ผู้อนุมัติ]</span>
                    </div>

                    <div class="mobile-table">

                        <table id="employee_request_gps_approval" class="displayTB table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th hidden>รหัสไอดี</th>
                                    <th hidden>รหัสพนักงาน</th>
                                    <th>คำร้อง</th>
                                    <th>ผู้ยื่น</th>
                                    <th>วันเริ่ม</th>
                                    <th>วันสิ้นสุด</th>
                                    <th hidden>วันเริ่มทำงาน</th>
                                    <th hidden>วันสิ้นสุดทำงาน</th>
                                    <th>รายละเอียด</th>
                                    <th>สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = sqlsrv_fetch_array($approver_result, SQLSRV_FETCH_ASSOC)) {

                                    $sql_approval = "SELECT * FROM approval_status
                                                        WHERE approval_id != ?
                                                        AND approval_id NOT IN (1,4,6,5)
                                                    ";

                                    $params_approval = array($row['approval_id']);

                                    $approval_result = sqlsrv_query($conn, $sql_approval, $params_approval);

                                    if ($approval_result === false) {
                                        die(print_r(sqlsrv_errors(), true));
                                    }

                                    $employee_name = $row["prefix_thai"] . $row["firstname_thai"] . " " . $row["lastname_thai"];
                                    $start_date = DateThaiWithoutTime($row['shift_start_date']->format('Y-m-d'));

                                    isset($row['shift_end_date']) ? $row['shift_end_date'] : null;

                                    if ($row['shift_end_date'] != null) {
                                        $end_date = DateThaiWithoutTime($row['shift_end_date']->format('Y-m-d'));
                                    }

                                ?>
                                    <tr>
                                        <!-- <td hidden class="tb_employee_id"><?= $row['card_id']; ?></td> -->
                                        <td hidden class="tb_shift_id"><?= $row['shift_id']; ?>
                                        </td>
                                        <td hidden class="tb_employee_id"><?= $row['request_card_id']; ?>
                                        </td>
                                        <td class="tb_coords_name"><?= $row['coords_name']; ?>
                                        </td>
                                        <td class="tb_employee_name"><?= $employee_name ?>
                                        </td>
                                        <td><?= $start_date ?>
                                        </td>
                                        <td><?php
                                            if ($row['shift_end_date'] != null) {
                                                echo  $end_date;
                                            } else {
                                                echo "ไม่มีสิ้นสุด";
                                            }
                                            ?>
                                        </td>

                                        <td hidden class="tb_start_date"><?= $row['shift_start_date']->format('Y-m-d') ?>
                                        </td>

                                        <td hidden class="tb_end_date">
                                            <?php if ($row['shift_end_date'] == !null) {
                                                echo $row['shift_end_date']->format('Y-m-d');
                                            } else {
                                                echo NULL;
                                            }
                                            ?>
                                        </td>

                                        <td><button onclick="location.href='employee_request_gps_show.php?id=<?= $row['shift_id']; ?>'">ดูข้อมูล</button>
                                        </td>

                                        <td><select class="approval_status_selection">
                                                <option value="<?= $row['approval_id'] ?>">
                                                    <?= $row['approval_thainame'] ?>
                                                </option>
                                                <?php while ($result_status = sqlsrv_fetch_array($approval_result)) : ?>
                                                    <option value="<?= $result_status['approval_id'] ?>">
                                                        <?= $result_status['approval_thainame'] ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </td>

                                    </tr>

                                    <!-- <div class="modal fade" id="modal_<?= $row['request_card_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="employeeDetailsModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                            <div class="modal-content">

                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="employeeDetailsModalLabel">รายละเอียดคำร้อง
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="display-request-gps">
                                                        <div class="row">

                                                            <input hidden id="coords_range" type="text" name="coords_range" class="form-control" value="<?= $row['coords_range']; ?>" readonly>
                                                            <input hidden id="coords_in_name" type="text" name="coords_in_name" class="form-control" value="<?= $row['coords_in_name']; ?>" readonly>
                                                            <input hidden id="coords_out_name" type="text" name="coords_out_name" class="form-control" value="<?= $row['coords_out_name']; ?>" readonly>
                                                            <input hidden id="checkIN_coords" type="text" name="checkIN_coords" class="form-control" value="<?= $row['coords_in_lat_lng']; ?>" readonly>
                                                            <input hidden id="checkOUT_coords" type="text" name="checkOUT_coords" class="form-control" value="<?= $row['coords_out_lat_lng']; ?>" readonly>

                                                            <div class="col-md-6">

                                                                <div class="form-group">
                                                                    <label for="employee-Name">ชื่อผู้ยื่นคำร้อง</label>
                                                                    <input id="employee-Name" type="text" class="form-control" value="<?= $row["prefix_thai"] . $row["firstname_thai"] . " " . $row["lastname_thai"]; ?>" readonly>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="work_location">สถานที่ทำงาน</label>
                                                                    <input id="work_location" type="text" name="#" class="form-control" value="<?= $row['coords_name']; ?>" readonly>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="checkIN_coords">พิกัดสถานที่ทำงาน</label>
                                                                    <button type="button" name="checkIN_coords_btn" class="btn btn-primary">ดูข้อมูล</button>
                                                                </div>

                                                            </div>

                                                            <div class="col-md-6">

                                                                <div class="form-group">
                                                                    <label for="start_date">วันเริ่มต้นทำงาน</label>
                                                                    <input id="start_date" type="text" name="#" class="form-control" value="<?= $start_date ?>" readonly>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="end_date">วันสิ้นสุดทำงาน</label>
                                                                    <input id="end_date" type="text" name="#" class="form-control" value="<?= $end_date ?>" readonly>
                                                                </div>

                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                                </div>

                                            </div>

                                        </div>
                                    </div> -->

                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>

<!-- <script defer src="../assets/script/gps_approval.js"></script> -->
<script>
    $('.approval_status_selection').on('change', function() {
        let selectedValue = $(this).val();
        let closestTr = $(this).closest('tr');
        let closestSelected = closestTr.find('.approval_status_selection').val();
        let cardId = closestTr.find('td:first').text();

        let shift_id = closestTr.find('.tb_shift_id').text();

        let employeeName = closestTr.find('.tb_employee_name').text();
        let start_date = closestTr.find('.tb_start_date').text();
        let end_date = closestTr.find('.tb_end_date').text();

        console.log('shift_id: ', shift_id.trim());

        if (closestSelected === '2') {
            console.log('ไม่อนุมัติ');

            Swal.fire({
                title: 'ไม่อนุมัติ',
                input: 'textarea',
                inputLabel: 'กรุณากรอกรายละเอียดการไม่อนุมัติ',
                inputPlaceholder: 'กรอกรายละเอียด...',
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก',
                allowOutsideClick: true,
                showLoaderOnConfirm: true,
                preConfirm: (reason) => {
                    if (!reason) {
                        Swal.showValidationMessage('กรุณากรอกรายละเอียดการไม่อนุมัติ');
                    } else {
                        return reason;
                    }
                }

            }).then((result) => {
                // หากผู้ใช้กด "ยืนยัน"
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../gps/gps_update_approval_approver.php',
                        method: 'POST',
                        data: {
                            shift_id: shift_id,
                            card_id: cardId,
                            approval_id: selectedValue,
                            start_date: start_date,
                            end_date: end_date,
                            approvarID: cardID,
                            reason: result.value
                        },
                        success: function(response) {
                            console.log(response);
                            Swal.fire({
                                icon: "success",
                                title: "เปลี่ยนสถานะแล้ว",
                                text: "คำร้องของ: " + employeeName,
                                toast: true,
                                position: "top-end",
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.onmouseenter = Swal.stopTimer;
                                    toast.onmouseleave = Swal.resumeTimer;
                                }
                            });
                            // ทำการอัปเดต <select> ใน DOM หลังจากเปลี่ยนสถานะแล้ว
                            closestTr.find('.approval_status_selection').val(selectedValue);
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                } else if (result.isDismissed) {
                    // หากผู้ใช้กด "ยกเลิก" ให้ทำการเปลี่ยน select เป็น "รอการตรวจสอบ"
                    closestTr.find('.approval_status_selection').val(5);
                }
            });
        } else {
            // กรณีที่ไม่ใช่[ไม่อนุมัติ]
            $.ajax({
                url: '../gps/gps_update_approval_approver.php',
                method: 'POST',
                data: {
                    shift_id: shift_id,
                    card_id: cardId,
                    approval_id: selectedValue,
                    start_date: start_date,
                    end_date: end_date,
                    approvarID: cardID

                },
                success: function(response) {
                    console.log(response);
                    Swal.fire({
                        icon: "success",
                        title: "เปลี่ยนสถานะแล้ว",
                        text: "คำร้องของ: " + employeeName,
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    // ทำการอัปเดต <select> ใน DOM หลังจากเปลี่ยนสถานะแล้ว
                    closestTr.find('.approval_status_selection').val(selectedValue);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    });
</script>


<!-- <?php include('../includes/footer.php') ?> -->

</html>