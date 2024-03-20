<?php
// header("Cache-Control: no-cache, must-revalidate");
session_start();
date_default_timezone_set('Asia/Bangkok');

require_once('../components-desktop/employee/include/header.php');
require_once('../database/connectdb.php');
require_once('../check-in/check-inout-query.php');
// require_once('getEmployeesList.php');

$SELECTapprover = "SELECT card_id, scg_employee_id, prefix_thai, firstname_thai, lastname_thai FROM employee";
$stmtapprover = sqlsrv_query($conn, $SELECTapprover);

if ($stmtapprover === false) {
    die(print_r(sqlsrv_errors(), true));
}

?>
<script>
    var personID = <?= $_SESSION["card_id"]; ?>
    // var personID = 1949999999904;
</script>



<head>

    <!-- Custom -->
    <link rel="shortcut icon" href="#">
    <link rel="stylesheet" href="../assets/css/homepage.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/shift-progress.css">
    <link rel="stylesheet" href="../assets/css/shift-manage-table.css">


</head>

<title>กำหนดการทำงานนอกสถานที่</title>

<body>

    <div class="navbar-BS">
        <div class="div-span">
            <span>กำหนดปฏิบัติการนอกสถานที่</span>
        </div>
    </div>

    <div class="container">

        <div class="box-add-team">

            <form id="gps-check-inout" method="post" enctype="multipart/form-data">

                <div id="select-Time-container" class="select-time-employee col-md-6">

                    <div class="form-group">
                        <div class="topic-select">
                            <span class="time-work">เลือกระยะเวลาทำงาน</span>
                        </div>
                    </div>
                    <br>

                    <div class="col-md-6">
                        <div class="select-time">

                            <div class="select-hour">
                                <div class="select-hour-long">
                                    <input class="input-box" type="radio" id="shift_time1" name="shift_time" value="option1">
                                    <label for="shift_time1">ระยะยาว</label><br>
                                </div>
                                <div class="select-hour-short">
                                    <input class="input-box" type="radio" id="shift_time2" name="shift_time" value="option2">
                                    <label for="shift_time2">ชั่วคราว</label><br>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="select-time-start-end">

                            <div id="shift_time_permit">
                                <label>วันเริ่มต้น</label><br>
                                <input class="datepicker input-box" readonly required min="<?php echo date('Y-m-d'); ?>" id="shift_start_date" name="shift_start_date" type="text" placeholder="เลือกวันเรื่มต้น">
                            </div>

                            <div id="shift_time_tempo">
                                <label>วันสิ้นสุด</label><br>
                                <input class="datepicker input-box" readonly id="shift_end_date" min="<?php echo date('Y-m-d'); ?>" name="shift_end_date" type="text" placeholder="เลือกวันสิ้นสุด">
                            </div>

                        </div>
                    </div>

                </div>

                <div id="select-Employees-container" class="select-time-employee col-md-6">

                    <div class="form-group">
                        <div class="topic-select">
                            <span>โปรดเลือกพนักงานที่ต้องการ</span>
                        </div>
                    </div>

                    <div id="shift_group-container" class="form-group-select">

                        <!-- <div class="select-costCenter">
                            <span>เลือก Cost-Center</span>
                            <div id="CostCenterShow">
                                <select id="CostCenterList">
                                    <option>เลือก cost center</option>
                                    <option>นาย A</option>
                                    <option>นาย B</option>
                                    <option>นาย C</option>
                                </select>
                            </div>
                        </div> -->

                        <div class="select-employee">
                            <span>เลือกพนักงานที่ต้องการ</span>
                            <div id="EmployeesShow">
                                <select class="input-box" id="EmployeesList">
                                    <option>เลือกพนักงาน</option>
                                    <?php
                                    while ($rs_emp = sqlsrv_fetch_array($stmtapprover, SQLSRV_FETCH_ASSOC)) {
                                        echo '<option value="' . $rs_emp['card_id'] . '|' . $rs_emp['scg_employee_id'] . '">'
                                            . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="display-employee">
                            <div id="employee-list" style="margin: auto;">
                                <span class="list-employee">รายการพนักงาน</span>
                                <ul class="employeeUL" id="employees"></ul>
                            </div>
                        </div>


                    </div>

                </div>

                <div id="select-Coords-container" class="container-checkin ">
                    <div class="type-checkInOut">
                        <span class="type-work">เลือกประเภทการเข้าและออกงาน</span>

                        <div class="col-md-6 btn-group" role="group">

                            <div class="form-group-same-point">
                                <input type="radio" class="btn-check input-box" name="gps_type" id="gps_type1" value="option1">
                                <label class="btn btn-outline-primary" for="gps_type1">จุดเดียวกัน</label>
                                <script>
                                    $(document).ready(function() {
                                        let gpsType1 = $("#gps_type1");
                                        let formGroupPositionCheckOut = $(".form-group-position-checkOut");

                                        gpsType1.on("change", function() {
                                            if (gpsType1.prop("checked")) {
                                                formGroupPositionCheckOut.hide();
                                            } else {
                                                formGroupPositionCheckOut.show();
                                            }
                                        });
                                    });
                                </script>
                            </div>

                            <div class="form-group-different-points">
                                <input type="radio" class="btn-check input-box" name="gps_type" id="gps_type2" value="option2">
                                <label class="btn btn-outline-primary" for="gps_type2">คนละจุด</label>
                            </div>

                            <div class="form-group-anywhere">
                                <input type="radio" class="btn-check input-box" name="gps_type" id="gps_type3" value="option3">
                                <label class="btn btn-outline-primary" for="gps_type3">ที่ไหนก็ได้</label>
                                <script>
                                    $(document).ready(function() {
                                        let gpsType2 = $("#gps_type2");
                                        let formGroupPositionCheckOut = $(".form-group-position-checkOut");

                                        gpsType2.on("change", function() {
                                            if (gpsType2.prop("checked")) {
                                                formGroupPositionCheckOut.show();
                                            } else {
                                                formGroupPositionCheckOut.hide();
                                            }
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                    </div>

                    <div id="map-container">
                        <span class="position-work" style="text-decoration: underline;">เลือกตำแหน่งการเข้าและออกงาน</span>

                        <div class="col-md-6">
                            <div class="form-group-nameWork">
                                <label for="coords_nameWork">ชื่อภารกิจการทำงาน</label>
                                <input class="input-box" placeholder="งานวันเด็ก" type="text" id="coords_nameWork" value="">
                            </div>

                            <div class="form-group-position-name">
                                <label id="label_coordIN" for="coords_nameIN" name="coords_name"></label>
                                <input class="form-control-sm input-box" type="text" id="coords_nameIN" name="coords_name" value="">
                                <span id="span_showIN_lat" name="coords_name"></span>
                                <span id="span_showIN_lng" name="coords_name"></span>
                            </div>
                                    
                            <div class="form-group-position-checkOut">
                                <label id="label_coordOUT" for="coords_nameOUT" name="coords_name">ชื่อสถานที่ออกงาน
                                    (จุดออกงาน)</label>
                                <input class="form-control-sm input-box" type="text" id="coords_nameOUT" name="coords_name" value=""><br>
                                <span id="span_showOUT_lat" name="coords_name"></span><br>
                                <span id="span_showOUT_lng" name="coords_name"></span>
                            </div>
                            <div class="form-group-radius">
                                <label for="coords_range">รัศมีจากจุดที่กำหนด (เมตร)</label>
                                <input class="input-box" pattern="[0-9]+" title="กรุณากรอกตัวเลขเท่านั้น" placeholder="1000 = 1 กิโลเมตร " type="text" id="coords_range" value="">
                            </div>
                        </div>

                        <div>
                            <button type="button" class="btn btn-primary input-box" id="mark-check-in-button">เข้างาน</button>
                            <button type="button" class="btn btn-primary input-box" id="mark-check-out-button">ออกงาน</button>
                            <button type="button" class="btn btn-primary input-box" id="mark-check-both-button">เข้าและออกงาน</button>
                            <button type="button" class="btn btn-danger input-box" id="mark-clear-button">ล้างตำแหน่ง</button>
                        </div>

                        <div class="show-map" id="map"></div>


                    </div>

                    <!-- <section id="summary-data-btn">
                        <div class="col">
                            <button type="button" class="btn btn-sum" id="confirm-form-button">สรุปข้อมูลคำร้อง</button>
                        </div>

                        <div class="container-checkin-btn">

                            <div class="col">
                                <button type="button" class="btn btn-success" id="submit-form-button">ยืนยันและอนุมัติ</button>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-danger" id="cancel-form-button">ยกเลิก</button>
                            </div>

                        </div>
                    </section> -->





                </div>

            </form>

            <section id="summary-data-btn">

                <div class="col">
                    <button type="button" class="btn btn-success" id="confirm-form-button">สรุปข้อมูลคำร้อง</button>
                </div>

                <div class="container-checkin-btn">

                    <div class="col">
                        <button type="button" class="btn btn-success" id="submit-form-button">ยืนยันและอนุมัติ</button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-danger" id="cancel-form-button">ยกเลิก</button>
                    </div>

                </div>
            </section>

            <button type="button" class="btn btn-success" id="next-step-btn">ขั้นตอนถัดไป</button>

        </div>


    </div>

</body>

<!-- <?php include('../includes/footer.php') ?> -->

<script src="../assets/script/set_coords.js"></script>