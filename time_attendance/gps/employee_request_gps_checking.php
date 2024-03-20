<?php
// header("Cache-Control: no-cache, must-revalidate");
session_start();
date_default_timezone_set('Asia/Bangkok');

include('head.php');
require_once('../database/connectdb.php');
require_once('check-inout-query.php');
require_once("getEmployeesList.php");

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
                        <h3>เลือกการทำงาน</h3>

                        <input disabled type="radio" id="shift_group1" name="shift_group" value="option1">
                        <label for="shift_group1">ของตนเอง</label><br>

                        <input disabled type="radio" id="shift_group2" name="shift_group" value="option2">
                        <label for="shift_group2">ของทีม</label><br>
                    </div>
                    <br>

                    <div id="shift_group-container" class="form-group">
                        <h5>เลือก Cost-Center</h5>

                        <div id="CostCenterShow">
                            <select id="CostCenterList">
                                <option>นาย A</option>
                                <option>นาย B</option>
                                <option>นาย C</option>
                            </select>
                        </div>
                        <br>

                        <h4>เลือกพนักงานในทีม</h4>

                        <div id="EmployeesShow">
                            <select id="employeesList">
                                <option>เลือกพนักงาน</option>
                                <?php
                                while ($rs_emp = sqlsrv_fetch_array($stmtapprover, SQLSRV_FETCH_ASSOC)) {
                                    echo '<option value="' . $rs_emp['scg_employee_id'] . '">' . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai'] . '</option>';
                                }
                                ?>
                            </select>
                            <!-- <button type="button" id="add-employee-btn">เพิ่มพนักงาน</button> -->
                        </div>
                        <br>

                        <div id="employee-list" style="margin: auto;">
                            <h5>รายการพนักงาน</h5>
                            <ul class="employeeUL" id="employees"></ul>
                        </div>



                        <br>

                    </div>

                </div>


                <!-- <div style="border:1px solid blue">

                    <div>
                        <h3>เลือกการทำงาน</h3>

                        <input type="radio" id="shift_group1" name="shift_group" value="option1">
                        <label for="shift_group1">ของตนเอง</label><br>

                        <input type="radio" id="shift_group2" name="shift_group" value="option2">
                        <label for="shift_group2">ของทีม</label><br>

                    </div>
                    <br>
                    <div id="shift_group-container">

                        <div>
                            <h4>เลือก Cost-Center</h4>

                            <div id="CostCenterShow">
                                <select id="CostCenterList">
                                    <option>นาย A</option>
                                    <option>นาย B</option>
                                    <option>นาย C</option>
                                </select>
                            </div>

                        </div>
                        <br>

                        <div>

                            <h4>เลือกพนักงานในทีม</h4>

                            <div id="EmployeesShow">
                                <select id="employeesList">
                                    <option>เลือกพนักงาน</option>
                                    <?php
                                    while ($rs_emp = sqlsrv_fetch_array($stmtapprover, SQLSRV_FETCH_ASSOC)) {
                                        echo '<option value="' . $rs_emp['scg_employee_id'] . '">' . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai'] . '</option>';
                                    }
                                    ?>
                                </select>
                                <button type="button" id="add-employee-btn">เพิ่มพนักงาน</button>
                            </div>
                            <br>

                            <div id="employee-list">
                                <h5>รายการพนักงาน</h5>
                                <ul id="employees"></ul>
                            </div>

                        </div>

                    </div>

                </div>
                <br> -->

                <div style="border:1px solid red">
                    <div class="col-md-6">
                        <h4>เลือกระยะเวลาทำงาน</h4>

                        <input type="radio" id="shift_time1" name="shift_time" value="option1">
                        <label for="shift_time1">ระยะยาว</label><br>

                        <input type="radio" id="shift_time2" name="shift_time" value="option2">
                        <label for="shift_time2">ชั่วคราว</label><br>

                    </div>
                    <br>

                    <div class="col-md-6" id="shift_date-container">

                        <div id="shift_time_permit">
                            <label>วันเริ่มต้น</label><br>
                            <input required min="<?php echo date('Y-m-d'); ?>" id="shift_start_date" name="shift_start_date" type="date">
                        </div>

                        <div id="shift_time_tempo">
                            <label>วันสิ้นสุด</label><br>
                            <input id="shift_end_date" min="<?php echo date('Y-m-d'); ?>" name="shift_end_date" type="date">
                        </div>

                    </div>

                </div>
                <br>

                <div style="border:1px solid red">
                    <div>
                        <h4>เลือกประเภทการเข้าและออกงาน</h4>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="gps_type" id="gps_type1" value="option1">
                            <label class="btn btn-outline-primary" for="gps_type1">จุดเดียวกัน</label><br>

                            <input type="radio" class="btn-check" name="gps_type" id="gps_type2" value="option2">
                            <label class="btn btn-outline-primary" for="gps_type2">คนละจุด</label><br>

                            <input type="radio" class="btn-check" name="gps_type" id="gps_type3" value="option3">
                            <label class="btn btn-outline-primary" for="gps_type3">ที่ไหนก็ได้</label><br>

                        </div>
                    </div>
                    <br>
                </div>
                <br>

                <div id="map-container" style="border:1px solid red;">

                    <div>
                        <h4>เลือกตำแหน่งการเข้าและออกงาน</h4>
                    </div>

                    <div>
                        <button type="button" class="btn btn-primary" id="mark-check-in-button">เข้างาน</button>
                        <button type="button" class="btn btn-primary" id="mark-check-out-button">ออกงาน</button>
                        <button type="button" class="btn btn-primary" id="mark-check-both-button">เข้าและออกงาน</button>
                        <button type="button" class="btn btn-danger" id="mark-clear-button">ล้างตำแหน่ง</button>
                    </div>
                    <br>
                    <div>
                        <label for="coords_nameWork">ชื่อการทำงาน</label><br>
                        <input placeholder="งานวันเด็ก" type="text" id="coords_nameWork" value="">
                        <br>
                        <label id="label_coordIN" for="coords_nameIN" name="coords_name"></label><br>
                        <input placeholder="โรงงานปูน ณ ดาวอังคาร" type="text" id="coords_nameIN" name="coords_name" value="">
                        <br>
                        <label id="label_coordOUT" for="coords_nameOUT" name="coords_name">ชื่อสถานที่ออกงาน</label><br>
                        <input placeholder="โรงบาล ณ ดาวพุธ" type="text" id="coords_nameOUT" name="coords_name" value="">
                    </div>
                    <div>
                        <label for="coords_range">รัศมีของพิกัด (เมตร)</label><br>
                        <input pattern="[0-9]+" title="กรุณากรอกตัวเลขเท่านั้น" placeholder="1000 = 1 กิโลเมตร " type="text" id="coords_range" value="">
                    </div>

                    <br>
                    <div class="show-map" id="map"></div>
                    <br>



                </div>
                <br>

                <div id="inspector-container" style="border:1px solid red">

                    <h4>ผู้อนุมัติ</h4>

                    <div id="InspectorShow">
                        <select id="InspectorList">
                            <option>นาย A</option>
                            <option>นาย B</option>
                            <option>นาย C</option>
                        </select>
                    </div>

                </div>
                <br>

                <div>
                    <button class="btn btn-success" id="submit-form-button" type="button">ส่งคำร้อง</button>
                </div>
                <br>

            </form>
        </div>
    </div>

</body>

<!-- <?php include('../includes/footer.php') ?> -->

<script src="../assets/script/requests_coords.js"></script>