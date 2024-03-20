<?php
// header("Cache-Control: no-cache, must-revalidate");
session_start();
date_default_timezone_set('Asia/Bangkok');

require_once('../components-desktop/employee/include/header.php');
require_once('../database/connectdb.php');
require_once('getEmployeesList.php');
require_once('../check-in/test.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['fname']);


    if (empty($name)) {
        echo "Name is empty";
    } else {
        echo $name;
    }

    
}





$shift_ID = $_GET['id'];
// echo $shift_ID;
$Multi_employee_stmt = null;

$sql_Single_employee = "SELECT * FROM shift_work_location_temporary_request
                        LEFT JOIN location_coords_default ON shift_work_location_temporary_request.coords_id = location_coords_default.coords_id
                        LEFT JOIN employee ON shift_work_location_temporary_request.card_id = employee.card_id
                        WHERE shift_id = ?
                        ";

$params = array($shift_ID);
$re_Single_employee = sqlsrv_prepare($conn, $sql_Single_employee, $params);

if (sqlsrv_execute($re_Single_employee) === false) {
    die(print_r(sqlsrv_errors(), true));
    // echo "ERROR 2";
}

// sqlsrv_free_stmt($re_Single_employee);

$rs_emp_info = sqlsrv_fetch_array($re_Single_employee, SQLSRV_FETCH_ASSOC);

$employee_name = $rs_emp_info["prefix_thai"] . $rs_emp_info["firstname_thai"] . " " . $rs_emp_info["lastname_thai"];
$start_date = DateThaiWithoutTime($rs_emp_info['shift_start_date']->format('Y-m-d'));

isset($rs_emp_info['shift_end_date']) ? $rs_emp_info['shift_end_date'] : null;
if ($rs_emp_info['shift_end_date'] != null) {
    $end_date = DateThaiWithoutTime($rs_emp_info['shift_end_date']->format('Y-m-d'));
}

$coords_type_str = trim($rs_emp_info['coords_type']);

?>
<script>
    var coords_typeJS = "<?= $coords_type_str ?>";

    const checkIN = {
        lat: null,
        lng: null,
        range: null
    }

    const checkOUT = {
        lat: null,
        lng: null,
        range: null
    }
</script>

<?php if ($coords_type_str == "คนละจุด") {

    $coordsIN_str = explode(',', $rs_emp_info['coords_in_lat_lng']);
    $coordsOUT_str = explode(',', $rs_emp_info['coords_out_lat_lng']);

?>
    <script>
        console.log('1');

        checkIN.lat = <?= $coordsIN_str[0] ?>;
        checkIN.lng = <?= $coordsIN_str[1] ?>;
        checkIN.range = <?= $rs_emp_info['coords_range'] ?>;

        checkOUT.lat = <?= $coordsOUT_str[0] ?>;
        checkOUT.lng = <?= $coordsOUT_str[1] ?>;
    </script>

<?php } else if ($coords_type_str == "จุดเดียวกัน") {

    $coordsIN_str = explode(',', $rs_emp_info['coords_in_lat_lng']);

?>

    <script>
        console.log('2');

        checkIN.lat = <?= $coordsIN_str[0] ?>;
        checkIN.lng = <?= $coordsIN_str[1] ?>;
        checkIN.range = <?= $rs_emp_info['coords_range'] ?>;
    </script>

<?php } ?>

<head>
    <!-- CSS Desktop -->
    <link rel="stylesheet" href="../components-desktop/employee/approval-employee.css">

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

    <div class="container">
        <div class="box-add-team">

            <form id="gps-check-inout" method="post" enctype="multipart/form-data">

                <div class="col-md-6">
                    <div class="form-group">

                        <div class="topic-select">
                            <span>รายการพนักงาน</span>
                        </div>

                        <!-- <div class="select-work">
                            <div class="select-own">
                                <input type="radio" id="shift_group1" name="shift_group" value="option1">
                                <label for="shift_group1">ของตนเอง</label><br>
                            </div>

                            <div class="select-team">
                                <input type="radio" id="shift_group2" name="shift_group" value="option2">
                                <label for="shift_group2">ของทีม</label><br>
                            </div>
                        </div> -->

                    </div>

                    <div id="shift_group-container" class="form-group-select">

                        <div class="select-employee">

                            <div id="EmployeesShow">
                                <ul class="employeeUL" id="employees">
                                    <?php
                                    if ($rs_emp_info['firstname_thai'] && $rs_emp_info['lastname_thai']) {
                                        echo '<li>' . $employee_name . '</li>';
                                    }

                                    if ($rs_emp_info['multi_employee_status'] == 1 && $rs_emp_info['multi_employee_token'] != null) {

                                        $sql_Multi_employee = "SELECT * FROM shift_work_location_temporary_request
                                                                    LEFT JOIN location_coords_default ON shift_work_location_temporary_request.coords_id = location_coords_default.coords_id
                                                                    LEFT JOIN employee ON (shift_work_location_temporary_request.card_id = employee.card_id AND shift_work_location_temporary_request.request_card_id = employee.card_id)
                                                                    WHERE shift_id != ? 
                                                                    AND multi_employee_status = ?
                                                                    AND multi_employee_token = ?
                                                                    ";

                                        $params = array($shift_ID, 1, $rs_emp_info['multi_employee_token']);
                                        $Multi_employee_stmt = sqlsrv_query($conn, $sql_Multi_employee, $params);

                                        if ($Multi_employee_stmt  === false) {
                                            die(print_r(sqlsrv_errors(), true));
                                        }
                                    }


                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="select-time-employee">
                    <div class="col-md-6">
                        <div class="select-time">
                            <span class="time-work">ระยะเวลาทำงาน</span>

                            <div class="select-hour">
                                <?php
                                if ($rs_emp_info['shift_start_date'] != null && $rs_emp_info['shift_end_date'] != null) {
                                    echo '<span>แบบชั่วคราว</span>';
                                } else {
                                    echo '<span>แบบถาวร</span>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6" id="shift_date-container">

                        <div class="select-time-start-end">

                            <div id="shift_time_permit">
                                <label>วันเริ่มต้นการทำงาน</label><br>
                                <input readonly id="shift_start_date" name="shift_start_date" value="<?= $start_date ?>">
                            </div>

                            <div id="shift_time_tempo">
                                <label>วันสิ้นสุดการทำงาน</label><br>
                                <?php

                                if ($rs_emp_info['shift_end_date'] != null) {
                                    echo '<input readonly id="shift_start_date" name="shift_start_date" value="' . $end_date . '"' . '>';
                                } else {
                                    echo '<input readonly id="shift_start_date" name="shift_start_date" value="ไม่มีกำหนด"';
                                }

                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container-checkin">

                    <div class="type-checkInOut">
                        <span class="type-work">ประเภทการเข้าและออกงาน</span>

                        <div class="col-md-6 btn-group" role="group">

                            <div class="form-group-same-point">
                                <input type="radio" class="btn-check" name="gps_type" id="gps_type1" value="option1">
                                <label class="btn btn-outline-primary" for="gps_type1">จุดเดียวกัน</label>
                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        var gpsType1 = document.getElementById("gps_type1");
                                        var formGroupPositionCheckOut = document.querySelector(".form-group-position-checkOut");

                                        gpsType1.addEventListener("change", function() {
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
                                    document.addEventListener("DOMContentLoaded", function() {
                                        var gpsType2 = document.getElementById("gps_type2");
                                        var formGroupPositionCheckOut = document.querySelector(".form-group-position-checkOut");

                                        gpsType2.addEventListener("change", function() {
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

                        <span class="position-work" style="text-decoration: underline;">เลือกตำแหน่งการเข้าและออกงาน</span>

                        <div class="col-md-6">

                            <div class="form-group-nameWork">
                                <label for="coords_nameWork">ชื่อการทำงาน</label>
                                <input placeholder="งานวันเด็ก" type="text" id="coords_nameWork" value="">
                            </div>

                            <div class="form-group-position-name">
                                <label id="label_coordIN" for="coords_nameIN" name="coords_name"></label>
                                <input class="form-control-sm" type="text" id="coords_nameIN" name="coords_name" value="">
                                <span id="span_showIN_lat" name="coords_name"></span>
                                <span id="span_showIN_lng" name="coords_name"></span>
                            </div>

                            <div class="form-group-position-checkOut">
                                <label id="label_coordOUT" for="coords_nameOUT" name="coords_name">ชื่อสถานที่ออกงาน</label>
                                <input class="form-control-sm" type="text" id="coords_nameOUT" name="coords_name" value=""><br>
                                <span id="span_showOUT_lat" name="coords_name"></span><br>
                                <span id="span_showOUT_lng" name="coords_name"></span>
                            </div>

                            <div class="form-group-radius">
                                <label for="coords_range">รัศมีของพิกัด (เมตร)</label>
                                <input pattern="[0-9]+" title="กรุณากรอกตัวเลขเท่านั้น" placeholder="1000 = 1 กิโลเมตร " type="text" id="coords_range" value="">
                            </div>

                        </div>

                        <div>
                            <button type="button" class="btn btn-primary" id="mark-check-in-button">เข้างาน</button>
                            <button type="button" class="btn btn-primary" id="mark-check-out-button">ออกงาน</button>
                            <button type="button" class="btn btn-primary" id="mark-check-both-button">เข้าและออกงาน</button>
                            <button type="button" class="btn btn-danger" id="mark-clear-button">ล้างตำแหน่ง</button>
                        </div>

                        <div class="show-map" id="map"></div>

                        <div id="inspector-container">
                            <!-- <h4>เลือกผู้ตรวจสอบ</h4>
                            <div id="InspectorShow">
                                <select id="inspectorList">
                                    <option>เลือกผู้ตรวจสอบ</option>

                                </select>
                            </div> -->

                            <span>ผู้ตรวจสอบ</span>
                            <input hidden disabled id="cost-center-approval-id" name="cost-center" type="text" value="<?= $approver_id; ?>">
                            <input disabled id="cost-center-approval-name" name="cost-center" type="text" value="<?= $approver_name; ?>">

                        </div>

                        <div id="approval-container">

                            <span>ผู้อนุมัติ</span>
                            <input hidden disabled id="cost-center-approval-id" name="cost-center" type="text" value="<?= $approver_id; ?>">
                            <input disabled id="cost-center-approval-name" name="cost-center" type="text" value="<?= $approver_name; ?>">

                        </div>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-success" id="submit-form-button" type="button">อนุมัติคำร้อง</button>
                        <button class="btn btn-danger" type="button">ปฏิเสธคำร้อง</button>
                        <button class="btn btn-warning" type="button">แก้ไขคำร้อง</button>
                    </div>
                </div>

            </form>

        </div>
    </div>

</body>


<?php include('../includes/footer.php') ?>


<script src="../assets/script/requests_coords_show.js"></script>
<script>
    if (coords_typeJS === "จุดเดียวกัน") {
        console.log('จุดเดียวกัน');



        $('input[name="gps_type"][value="option1"]').prop('checked', true);

        $("#label_coordIN").html("ชื่อสถานที่เข้าและออกงาน");

        $('#mark-check-both-button').show();
        $('#mark-clear-button').show();
        $('#mark-check-in-button').hide();
        $('#mark-check-out-button').hide();

        $('.form-group-position-checkOut').hide();

        $('#label_coordOUT').hide();
        $('#coords_nameOUT').hide();

        $('#map-container').show();

        $('#coords_nameWork').val("<?= $rs_emp_info['coords_name'] ?>");
        $('#coords_nameIN').val("<?= $rs_emp_info['coords_in_name'] ?>");
        $('#coords_range').val("<?= $rs_emp_info['coords_range'] ?>");

        let markerIN = L.marker([checkIN.lat, checkIN.lng]).addTo(markersGroup);

        let circleIN = L.circle([checkIN.lat, checkIN.lng], {
            color: '#1E90FF',
            fillColor: '#82EEFD',
            fillOpacity: 0.3,
            radius: checkIN.range
        }).addTo(markersGroup);

        markerIN.bindPopup("จุดเข้าและออกงาน.").openPopup();
        circleIN.bindPopup("จุดเข้าและออกงาน.").openPopup();

    } else if (coords_typeJS === "คนละจุด") {
        console.log('คนละจุด');

        $('input[name="gps_type"][value="option2"]').prop('checked', true);

        $("#label_coordIN").html("ชื่อสถานที่เข้างาน");

        $('#mark-check-in-button').show();
        $('#mark-check-out-button').show();
        $('#mark-clear-button').show();
        $('#mark-check-both-button').hide();

        $('.form-group-position-checkOut').show();

        $('#label_coordOUT').show();
        $('#coords_nameOUT').show();

        $('#map-container').show();

        $('#coords_nameWork').val("<?= $rs_emp_info['coords_name'] ?>");
        $('#coords_nameIN').val("<?= $rs_emp_info['coords_in_name'] ?>");
        $('#coords_nameOUT').val("<?= $rs_emp_info['coords_out_name'] ?>");
        $('#coords_range').val("<?= $rs_emp_info['coords_range'] ?>");


        let markerIN = L.marker([checkIN.lat, checkIN.lng]).addTo(markersGroup);
        let markerOUT = L.marker([checkOUT.lat, checkOUT.lng]).addTo(markersGroup);

        let circleIN = L.circle([checkIN.lat, checkIN.lng], {
            color: '#1E90FF',
            fillColor: '#82EEFD',
            fillOpacity: 0.3,
            radius: checkIN.range
        }).addTo(markersGroup);

        let circleOUT = L.circle([checkOUT.lat, checkOUT.lng], {
            color: '#1E90FF',
            fillColor: '#82EEFD',
            fillOpacity: 0.3,
            radius: checkIN.range
        }).addTo(markersGroup);

        markerIN.bindPopup("จุดเข้างาน.").openPopup();

        circleOUT.bindPopup("จุดออกงาน.").openPopup();
















    }
</script>