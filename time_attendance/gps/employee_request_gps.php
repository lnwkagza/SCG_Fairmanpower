<?php
// header("Cache-Control: no-cache, must-revalidate");
session_start();
date_default_timezone_set('Asia/Bangkok');

include('../includes/header.php');
require_once('../database/connectdb.php');
require_once('../check-in/check-inout-query.php');
require_once('getEmployeesList.php');

// echo $person_id; 

?>



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

<title>คำร้องทำงานนอกสถานที่</title>

<body>

    <div class="navbar-BS">
        <div class="div-span">
            <span>คำร้องทำงานนอกสถานที่</span>
        </div>
    </div>

    <!-- <h1 class="topic">ส่งคำร้องพิกัดไปยังหัวหน้า</h1> -->
    <!-- <span class="topic">รายชื่อพนักงานในทีม</span> -->
    <div class="container">
        <div class="box-add-team">

            <form id="gps-check-inout" method="post" enctype="multipart/form-data">

                <div class="col-md-6">
                    <div class="form-group">

                        <div class="topic-select">
                            <span>เลือกการทำงาน</span>
                        </div>

                        <div class="select-work">
                            <div class="select-own">
                                <input type="radio" id="shift_group1" name="shift_group" value="option1">
                                <label for="shift_group1">ของตนเอง</label><br>
                            </div>

                            <div class="select-team">
                                <input type="radio" id="shift_group2" name="shift_group" value="option2">
                                <label for="shift_group2">ของทีม</label><br>
                            </div>
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
                            <span>เลือกพนักงานในทีม</span>
                            <div id="EmployeesShow">
                                <select id="employeesList">
                                    <option>เลือกพนักงาน</option>
                                    <?php
                                    while ($rs_emp = sqlsrv_fetch_array($stmtapprover, SQLSRV_FETCH_ASSOC)) {
                                        echo '<option value="' . $rs_emp['card_id'] . '|' . $rs_emp['scg_employee_id'] . '">'
                                            . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai'] .
                                            '</option>';
                                    }
                                    ?>
                                </select>
                                <!-- <button type="button" id="add-employee-btn">เพิ่มพนักงาน</button> -->
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


                <div class="select-time-employee">
                    <div class="col-md-6">
                        <div class="select-time">
                            <span class="time-work">เลือกระยะเวลาทำงาน</span>

                            <div class="select-hour">
                                <div class="select-hour-long">
                                    <input type="radio" id="shift_time1" name="shift_time" value="option1">
                                    <label for="shift_time1">ระยะยาว</label><br>
                                </div>
                                <div class="select-hour-short">
                                    <input type="radio" id="shift_time2" name="shift_time" value="option2">
                                    <label for="shift_time2">ชั่วคราว</label><br>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6" id="shift_date-container">

                        <div class="select-time-start-end">
                            <div id="shift_time_permit">
                                <label>วันเริ่มต้น</label><br>
                                <input class="datepicker" required min="<?php echo date('Y-m-d'); ?>"
                                    id="shift_start_date" name="shift_start_date" type="text"
                                    placeholder="เลือกวันเรื่มต้น">
                            </div>

                            <div id="shift_time_tempo">
                                <label>วันสิ้นสุด</label><br>
                                <input class="datepicker" id="shift_end_date" min="<?php echo date('Y-m-d'); ?>"
                                    name="shift_end_date" type="text" placeholder="เลือกวันสิ้นสุด">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container-checkin">
                    <div class="type-checkInOut">
                        <span class="type-work">เลือกประเภทการเข้าและออกงาน</span>

                        <div class="col-md-6 btn-group" role="group">

                            <div class="form-group-same-point">
                                <input type="radio" class="btn-check" name="gps_type" id="gps_type1" value="option1">
                                <label class="btn btn-outline-primary" for="gps_type1">จุดเดียวกัน</label>
                                <script>
                                    document.addEventListener("DOMContentLoaded", function () {
                                        var gpsType1 = document.getElementById("gps_type1");
                                        var formGroupPositionCheckOut = document.querySelector(".form-group-position-checkOut");

                                        gpsType1.addEventListener("change", function () {
                                            if (gpsType1.checked) {
                                                formGroupPositionCheckOut.style.display = "none";
                                            }
                                        });
                                    });
                                </script>
                            </div>

                            <div class="form-group-different-points">
                                <input type="radio" class="btn-check" name="gps_type" id="gps_type2" value="option2">
                                <label class="btn btn-outline-primary" for="gps_type2">คนละจุด</label>
                            </div>

                            <div class="form-group-anywhere">
                                <input type="radio" class="btn-check" name="gps_type" id="gps_type3" value="option3">
                                <label class="btn btn-outline-primary" for="gps_type3">ที่ไหนก็ได้</label>
                                <script>
                                    document.addEventListener("DOMContentLoaded", function () {
                                        var gpsType2 = document.getElementById("gps_type2");
                                        var formGroupPositionCheckOut = document.querySelector(".form-group-position-checkOut");

                                        gpsType2.addEventListener("change", function () {
                                            if (gpsType2.checked) {
                                                formGroupPositionCheckOut.style.display = "block";
                                            } else {
                                                formGroupPositionCheckOut.style.display = "none";
                                            }
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                    </div>

                    <div id="map-container">
                        <span class="position-work"
                            style="text-decoration: underline;">เลือกตำแหน่งการเข้าและออกงาน</span>

                        <div class="col-md-6">
                            <div class="form-group-nameWork">
                                <label for="coords_nameWork">โปรดระบุชื่อภารกิจการทำงาน</label>
                                <input placeholder="ระบุภารกิจการทำงาน" type="text" id="coords_nameWork" value="">
                            </div>

                            <div class="form-group-position-name">
                                <label id="label_coordIN" for="coords_nameIN" name="coords_name"></label>
                                <input placeholder="ระบุสถานที่เข้าและออกงาน" class="form-control-sm" type="text" id="coords_nameIN" name="coords_name"
                                    value="">
                                <span id="span_showIN_lat" name="coords_name"></span>
                                <span id="span_showIN_lng" name="coords_name"></span>
                            </div>

                            <div class="form-group-position-checkOut">
                                <label id="label_coordOUT" for="coords_nameOUT"
                                    name="coords_name">โปรดระบุชื่อสถานที่ออกงาน</label>
                                <input placeholder="ระบุสถานที่ออกงาน" class="form-control-sm" type="text" id="coords_nameOUT" name="coords_name"
                                    value=""><br>
                                <span id="span_showOUT_lat" name="coords_name"></span><br>
                                <span id="span_showOUT_lng" name="coords_name"></span>
                            </div>
                            <div class="form-group-radius">
                                <label for="coords_range">โปรดระบุรัศมีจากจุดที่กำหนด (เมตร)</label>
                                <input pattern="[0-9]+" title="กรุณากรอกตัวเลขเท่านั้น" placeholder="1000 = 1 กิโลเมตร "
                                    type="text" id="coords_range" value="">
                            </div>
                        </div>
                        <div>
                            <span class="set-location-InOut">โปรดกำหนดตำแหน่งเข้างาน-ออกงาน</span><br>
                            <button type="button" class="btn btn-primary" id="mark-check-in-button">เข้างาน</button>
                            <button type="button" class="btn btn-primary" id="mark-check-out-button">ออกงาน</button>
                            <button type="button" class="btn btn-primary"
                                id="mark-check-both-button">เข้าและออกงาน</button>
                            <button type="button" class="btn btn-danger" id="mark-clear-button">ล้างตำแหน่ง</button>
                        </div>

                        <div class="show-map map-container" id="map"></div>

                        <div class="container-checkin-request">
                            <div id="inspector-container">

                                <div id="InspectorShow">
                                    <span>ผู้ตรวจสอบ</span>
                                    <select id="inspectorList">
                                        <option>เลือกผู้ตรวจสอบ (หากมี)</option>
                                        <?php
                                        while ($rs_emp = sqlsrv_fetch_array($rs_inspector, SQLSRV_FETCH_ASSOC)) {
                                            echo '<option value="' . $rs_emp['card_id'] . '">' . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div id="approval-container">
                                <span>ผู้อนุมัติ</span>
                                <input hidden disabled id="cost-center-approval-id" name="cost-center" type="text"
                                    value="<?= $approver_id; ?>">
                                <input disabled id="cost-center-approval-name" name="cost-center" type="text"
                                    value="<?= $approver_name; ?>">
                            </div>

                            <div>
                                <button class="btn btn-success" id="submit-form-button" type="button">ส่งคำร้อง</button>
                            </div>
                        </div>

                    </div>

                </div>




            </form>
        </div>
    </div>

</body>

<?php include('../includes/footer.php') ?>

<script src="../assets/script/requests_coords.js"></script>