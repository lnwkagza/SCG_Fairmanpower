<?php

session_start();
require_once('../database/connectdb.php');
require_once('../components-desktop/employee/include/header.php');
require_once('../check-in/check-inout-query.php');
require_once('../check-in/test.php');

?>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/employee/approval-employee.css">

<!-- CSS Mobile -->
<link rel="stylesheet" href="../assets/css/employee-detail-gps.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/navbar.css">
<!-- <link rel="stylesheet" href="../assets/css/homepage.css"> -->
<link rel="stylesheet" href="../assets/css/custom.css">
<link rel="stylesheet" href="../node_modules/leaflet/dist/leaflet.css" />

</head>
<?php

$_SESSION['card_id'] = isset($_SESSION['card_id']) ? $_SESSION['card_id'] : '';  // ตรวจสอบว่ามีค่าหรือไม่
$_SESSION['card_id'] = trim($_SESSION['card_id']);  // ลบช่องว่างที่อาจเกิดขึ้นทั้งด้านหน้าและด้านหลังของข้อมูล
$_SESSION['card_id'] = preg_replace('/[^a-zA-Z0-9]/', '', $_SESSION['card_id']);  // ลบทุกอักขระที่ไม่ใช่ a-z, A-Z, และ 0-9
$_SESSION['card_id'] = htmlspecialchars($_SESSION['card_id'], ENT_QUOTES, 'UTF-8');  // แปลงสัญลักษณ์ HTML เพื่อป้องกัน XSS

// $card_id = $_SESSION["card_id"];

$time_stamp = date("Y-m-d H:i:s");
$time_now = date("H:i:s");
$date_now = date("Y-m-d");
$Thai_date_show = DateThai($time_stamp);

$date = date('j F Y'); // 1 มกราคม 2550
$time = date('H:i:s'); // 07:30:00

?>

<body>

    <div class="mobile">

        <!-- Navbar -->
        <div class="navbar-fix">
            <div>
            </div>
            <div class="div-span">
                <span>บันทึกเวลาด้วย GPS</span>
            </div>

            <div class="btn-fix">
                <a onclick="window.location.href='employee_request_gps.php';"><img src="../IMG/fix1.png" alt=""></a>
            </div>

        </div>

        <!-- modal perview GPS -->
        <!-- <div class="modal fade" id="preview_gps_Modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Map Modal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

                        <div class="preview-map-container" id="preview-map-box"></div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div> -->

        <div hidden id="mockup-map"></div>

        <!-- modal check in -->
        <div class="modal fade modal-map" id="check-in-Modal-gps" aria-hidden="true" aria-labelledby="check-in-Modal-gps-Label" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">

                        <span class="modal-title" id="check-in-Modal-gps-Label">Check-In เข้างาน</span>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <!-- <img src="../IMG/cross-warn.png" alt="" class="custom-image"> -->
                        </button>

                    </div>

                    <div class="modal-body">
                        <!-- display-map -->
                        <div class="center" id="modal-checkIN-map"></div>

                        <div class="type-time">

                            <div>
                                <span>สถานะ : </span>
                                <span class="add" id="location-in"></span>
                            </div>

                            <div>
                                <span>ลิติจูด : </span>
                                <span class="check-in-lat"></span>
                            </div>

                            <div>
                                <span>ลองติจูด : </span>
                                <span class="check-in-lng"></span>
                            </div>

                            <div>
                                <span>รัศมีจากจุดที่กำหนด : </span>
                                <span class="check-in-range"></span>
                                <span>กม.</span>
                            </div>

                        </div>

                        <div class="displayTime-gps">

                            <div>
                                <span class="Time" style="text-align:center;">
                                    <br><span class="Display_datetime"></span>
                                </span>
                            </div>

                            <div>
                                <span style="text-align:center;" id="location"></span>
                            </div>

                        </div>

                        <div>
                            <button class="btn btn-success" type="button" id="check-in-button-gps" style="background-color: #F2CB00;">ยืนยัน</button>
                        </div>

                        <form hidden id="form-check-gps" method="post" enctype="multipart/form-data">
                            <input type="text" id="gps-card-id" name="gps-card-id" value="<?php echo $card_id; ?>">
                            <input type="text" id="gps-date" name="gps-date" value="<?php echo $date_now; ?>">
                            <input type="text" id="gps-time" name="gps-time" value="<?php echo $time_now; ?>">

                            <input type="text" id="gps-in-coords" name="gps-in-coords" value="">
                            <input type="text" id="gps-out-coords" name="gps-out-coords" value="">

                            <input type="text" id="check_type" name="check_type" value="gps_type">
                        </form>

                    </div>

                    <!-- <div class="modal-footer">
                        <button class="btn btn-primary" data-bs-target="#exampleModalToggle2" data-bs-toggle="modal" data-bs-dismiss="modal">ยืนยัน</button>
                    </div> -->

                </div>
            </div>
        </div>

        <!-- modal check out -->
        <div class="modal fade modal-map" id="check-out-Modal-gps" aria-hidden="true" aria-labelledby="check--Modal-gps-Label" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title" id="check-out-Modal-gps-Label">Check-Out ออกงาน</h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <!-- <img src="../IMG/cross-warn.png" alt="" class="custom-image"> -->
                        </button>

                    </div>

                    <div class="modal-body">
                        <!-- display-map -->
                        <div class="map center" id="modal-checkOUT-map"></div>
                        <br>

                        <div class="type-time">

                            <div>
                                <span>สถานะ : </span>
                                <span class="add" id="location-out"></span>
                            </div>

                            <div>
                                <span>ลิติจูด : </span>
                                <span class="check-out-lat"></span>
                            </div>

                            <div>
                                <span>ลองติจูด : </span>
                                <span class="check-out-lng"></span>
                            </div>

                            <div>
                                <span>รัศมีจากจุดที่กำหนด : </span>
                                <span class="check-in-range"></span>
                                <span>กม.</span>
                            </div>

                        </div>

                        <div class="displayTime-gps">

                            <div>
                                <span class="Time" style="text-align:center;">
                                    <br><span class="Display_datetime"></span>
                                </span>
                            </div>

                            <div>
                                <span style="text-align:center;" id="location"></span>
                            </div>

                        </div>

                        <div class="col">
                            <button class="btn btn-success" type="button" id="check-out-button-gps" style="background-color: #F2CB00; ">ยืนยัน</button>
                        </div>

                        <form hidden id="form-check-gps" method="post" enctype="multipart/form-data">
                            <input type="text" id="gps-card-id" name="gps-card-id" value="<?php echo $card_id; ?>">
                            <input type="text" id="gps-date" name="gps-date" value="<?php echo $date_now; ?>">
                            <input type="text" id="gps-time" name="gps-time" value="<?php echo $time_now; ?>">

                            <input type="text" id="gps-in-coords" name="gps-in-coords" value="">
                            <input type="text" id="gps-out-coords" name="gps-out-coords" value="">

                            <input type="text" id="check_type" name="check_type" value="gps_type">
                        </form>

                    </div>

                    <!-- <div class="modal-footer">
                        <button class="btn btn-primary" data-bs-target="#exampleModalToggle2" data-bs-toggle="modal" data-bs-dismiss="modal">ยืนยัน</button>
                    </div> -->

                </div>
            </div>
        </div>

        <div class="container">

            <!-- ----box first (To Day)---- -->
            <div class="checkInOut">

                <div class="topic">
                    <span style="font-weight: bold;">สถานที่บันทึกเวลาวันนี้</span>
                </div>

                <!-- <div class="box-detail-type">
                    <div class="type-time">
                        <span>1. รูปแบบ : </span>
                        <span class="add">ระยะยาว</span>
                    </div>

                    <div class="type-checkInOut">
                        <span>2. ประเภท : </span>
                        <span class="add">เข้าออกจุดเดียวกัน</span>
                    </div>
                </div> -->

                <!-- <div class="location-checkInOut">

                    <div class="location">

                        <div class="location-checkIn">

                            <div class="topic-checkIn">
                                <span>จุดเข้างาน</span>
                            </div>

                            <div class="detail-location">
                                <span>ละติจูด : <span class="check-in-lat add"></span></span>
                                <span>ลองติจูด : <span class=" check-in-lng add"></span></span>

                                <div class="btn-map">
                                    <button class="btn btn-primary open-modal-btn" data-lat="13.090" data-lng="100.4366">แสดงแผนที่<img src="../IMG/earth.png" alt=""></button>
                                </div>

                            </div>

                        </div>

                        <div class="location-checkOut">

                            <div class="topic-checkOut">
                                <span>จุดออกงาน</span>
                            </div>

                            <div class="detail-location">
                                <span>ละติจูด : <span class="check-out-lat add"></span></span>
                                <span>ลองติจูด : <span class=" check-out-lng add"></span></span>

                                <div class="btn-map">
                                    <button class="btn btn-primary open-modal-btn" data-lat="555" data-lng="666">แสดงแผนที่<img src="../IMG/earth.png" alt=""></button>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="radius">
                        <span>รัศมีจากจุดที่กำหนด : <span class="check-in-range add"></span><span> กม.</span></span>
                    </div>

                </div> -->

                <!-- <div class="btnGPS-check-in">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#check-in-Modal-gps" id="check-in-button-gps" style="background-color: #2db396;"><img src="../IMG/checkin.png" alt="">ลงชื่อเข้างาน</button>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#check-out-Modal-gps" id="check-out-button-gps" style="background-color: #e1574b;"><img src="../IMG/checkout.png" alt="">ลงชื่อออกงาน</button>
                </div> -->

            </div>

            <!-- ----box last (All)---- -->
            <div class="detail-set">

                <div class="topic">
                    <span style="font-weight: bold;">รายละเอียดที่ถูกกำหนดจุดไว้</span>
                </div>

                <div class="detail-saveTime-all">

                    <!-- ภารกิจทำงานระยะยาว -->
                    <?php if (sqlsrv_has_rows($checkINOUT_permit_stmt)) {
                        $checkINOUT_permit_result = sqlsrv_fetch_array($checkINOUT_permit_stmt, SQLSRV_FETCH_ASSOC);
                        //วันที่แบบไทย
                        $start_date_thai = DateThaiWithoutTime($checkINOUT_permit_result['shift_start_date']->format('Y-m-d'));


                        $checkIN_coords_str = explode(',', $checkINOUT_permit_result['coords_in_lat_lng']);
                        $checkIN_lat_str = $checkIN_coords_str[0];
                        $checkIN_lng_str = $checkIN_coords_str[1];

                        $checkIN_lat_str_short = substr($checkIN_lat_str, 0, 7) . "...";
                        $checkIN_lng_str_short = substr($checkIN_lng_str, 0, 7) . "...";

                        $checkOUT_coords_str = explode(',', $checkINOUT_permit_result['coords_out_lat_lng']);
                        $checkOUT_lat_str = $checkOUT_coords_str[0];
                        $checkOUT_lng_str = $checkOUT_coords_str[1];

                        $checkOUT_lat_str_short = substr($checkOUT_lat_str, 0, 7) . "...";
                        $checkOUT_lng_str_short = substr($checkOUT_lng_str, 0, 7) . "...";

                        $coords_rangeKM = $checkINOUT_permit_result['coords_range'] / 1000;
                    ?>

                        <div class="set-longTerm">
                            <div class="topic-saveTime">
                                <span style="text-align: left; text-decoration: underline;">ภารกิจระยะยาว</span>
                            </div>
                            <div class="detail-saveTime">

                                <div class="row shift-type">
                                    <span>รูปแบบ : <span class="add">ระยะยาว</span></span>
                                    <span>ประเภท : <span class="add">เข้าและออก<?= $checkINOUT_permit_result['coords_type'] ?>
                                        </span></span>
                                </div>

                                <div class="col date">
                                    <span>วันเริ่มต้น : <span class="add">
                                            <?= $start_date_thai ?>
                                        </span></span>
                                    <span>วันสิ้นสุด : <span class="add">ไม่มีกำหนด</span></span>
                                </div>

                            </div>

                            <div class="location-checkInOut">
                                <div class="location">
                                    <div class="location-checkIn">

                                        <div class="topic-checkIn">
                                            <span>จุดเข้างาน</span>
                                        </div>

                                        <div class="detail-location">
                                            <span>ละติจูด : <span class="add">
                                                    <?= $checkIN_lat_str_short ?>
                                                </span></span>
                                            <span>ลองติจูด : <span class="add">
                                                    <?= $checkIN_lng_str_short ?>
                                                </span></span>


                                            <div class="btn-map">
                                                <button class="open-modal-btn" data-lat="<?= $checkIN_lat_str ?>" data-lng="<?= $checkIN_lng_str ?>" data-range="<?= $checkINOUT_permit_result['coords_range'] ?>">ดูแผนที่<img src="../IMG/earth.png" alt=""></button>
                                            </div>

                                        </div>

                                    </div>
                                    <div class="location-checkOut">

                                        <div class="topic-checkOut">
                                            <span>จุดออกงาน</span>
                                        </div>
                                        <div class="detail-location">
                                            <span>ละติจูด : <span class="add">
                                                    <?= $checkOUT_lat_str_short ?>
                                                </span></span>
                                            <span>ลองติจูด : <span class="add">
                                                    <?= $checkOUT_lng_str_short ?>
                                                </span></span>
                                            <div class="btn-map">
                                                <button class="open-modal-btn" data-lat="<?= $checkOUT_lat_str ?>" data-lng="<?= $checkOUT_lng_str ?>" data-range="<?= $checkINOUT_permit_result['coords_range'] ?>">ดูแผนที่<img src="../IMG/earth.png" alt=""></button>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="radius">
                                    <span>รัศมีจากจุดที่กำหนด : <span class="add">
                                            <?= $coords_rangeKM ?> กม.
                                        </span></span>

                                </div>

                                <div class="btn-request-approve">
                                    <button onclick="window.location.href='employee_request_gps_show.php?id=<?= $checkINOUT_permit_result['shift_id'] ?>';" class="btn-request-approve">ขออนุมัติแก้ไข</button>

                                </div>

                            </div>
                            <input hidden class="input-checkIN-coords" data-lat="<?= $checkIN_lat_str ?>" data-lng="<?= $checkIN_lng_str ?>">
                            <input hidden class="input-checkOUT-coords" data-lat="<?= $checkOUT_lat_str ?>" data-lng="<?= $checkOUT_lng_str ?>">

                            <input hidden class="input-check-range" data-range="<?= $checkINOUT_permit_result['coords_range'] ?>">


                        </div>

                    <?php } ?>

                    <!-- ภารกิจทำงานระยะสั้น -->
                    <?php if (sqlsrv_has_rows($checkINOUT_tempo_stmt)) {
                        $checkINOUT_tempo_result = sqlsrv_fetch_array($checkINOUT_tempo_stmt, SQLSRV_FETCH_ASSOC);

                        //วันที่แบบไทย
                        $start_date_thai = DateThaiWithoutTime($checkINOUT_tempo_result['shift_start_date']->format('Y-m-d'));
                        $end_date_thai = DateThaiWithoutTime($checkINOUT_tempo_result['shift_end_date']->format('Y-m-d'));

                        $checkIN_coords_str = explode(',', $checkINOUT_tempo_result['coords_in_lat_lng']);
                        $checkIN_lat_str = $checkIN_coords_str[0];
                        $checkIN_lng_str = $checkIN_coords_str[1];

                        $checkIN_lat_str_short = substr($checkIN_lat_str, 0, 7) . "...";
                        $checkIN_lng_str_short = substr($checkIN_lng_str, 0, 7) . "...";

                        $checkOUT_coords_str = explode(',', $checkINOUT_tempo_result['coords_out_lat_lng']);
                        $checkOUT_lat_str = $checkOUT_coords_str[0];
                        $checkOUT_lng_str = $checkOUT_coords_str[1];

                        $checkOUT_lat_str_short = substr($checkOUT_lat_str, 0, 7) . "...";
                        $checkOUT_lng_str_short = substr($checkOUT_lng_str, 0, 7) . "...";

                        $coords_rangeKM = $checkINOUT_tempo_result['coords_range'] / 1000;
                        // echo $checkINOUT_tempo_result['coords_range'];


                    ?>
                        <div class="set-temporary">

                            <div class="topic-saveTime">
                                <span style="text-align: left; text-decoration: underline;">ภารกิจระยะชั่วคราว</span>
                            </div>

                            <div class="detail-saveTime">

                                <div class="row shift-type">
                                    <span>รูปแบบ : <span class="add">ระยะชั่วคราว</span></span>
                                    <span>ประเภท : <span class="add">เข้าและออก<?= $checkINOUT_tempo_result['coords_type'] ?>
                                        </span></span>
                                </div>

                                <div class="col date">
                                    <span>วันเริ่มต้น : <span class="add">
                                            <?= $start_date_thai ?>
                                        </span></span>
                                    <span>วันสิ้นสุด : <span class="add">
                                            <?= $end_date_thai ?>
                                        </span></span>
                                </div>

                            </div>

                            <div class="location-checkInOut">
                                <div class="location">
                                    <div class="location-checkIn">

                                        <div class="topic-checkIn">
                                            <span>จุดเข้างาน</span>
                                        </div>

                                        <div class="detail-location">
                                            <span>ละติจูด : <span class="add">
                                                    <?= $checkIN_lat_str_short ?>
                                                </span></span>
                                            <span>ลองติจูด : <span class="add">
                                                    <?= $checkIN_lng_str_short ?>
                                                </span></span>

                                            <div class="btn-map">
                                                <button class="open-modal-btn" data-lat="<?= $checkIN_lat_str ?>" data-lng="<?= $checkIN_lng_str ?>" data-range="<?= $checkINOUT_tempo_result['coords_range'] ?>">ดูแผนที่<img src="../IMG/earth.png" alt=""></button>
                                            </div>

                                        </div>

                                    </div>
                                    <div class="location-checkOut">

                                        <div class="topic-checkOut">
                                            <span>จุดออกงาน</span>
                                        </div>
                                        <div class="detail-location">
                                            <span>ละติจูด : <span class="add">
                                                    <?= $checkOUT_lat_str_short ?>
                                                </span></span>
                                            <span>ลองติจูด : <span class="add">
                                                    <?= $checkOUT_lng_str_short ?>
                                                </span></span>

                                            <div class="btn-map">
                                                <button class="open-modal-btn" data-lat="<?= $checkOUT_lat_str ?>" data-lng="<?= $checkOUT_lng_str ?>" data-range="<?= $checkINOUT_tempo_result['coords_range'] ?>">ดูแผนที่<img src="../IMG/earth.png" alt=""></button>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="radius">
                                    <span>รัศมีจากจุดที่กำหนด : <span class="add">
                                            <?= $coords_rangeKM ?> กม.
                                        </span></span>
                                </div>

                                <div class="btn-request-approve">
                                    <button onclick="window.location.href='employee_request_gps_show.php?id=<?= $checkINOUT_tempo_result['shift_id'] ?>';" class="btn-request-approve">ขออนุมัติแก้ไข</button>
                                </div>

                            </div>

                            <input hidden class="input-checkIN-coords" data-lat="<?= $checkIN_lat_str ?>" data-lng="<?= $checkIN_lng_str ?>">
                            <input hidden class="input-checkOUT-coords" data-lat="<?= $checkOUT_lat_str ?>" data-lng="<?= $checkOUT_lng_str ?>">

                            <input hidden class="input-check-range" data-range="<?= $checkINOUT_tempo_result['coords_range'] ?>">

                        </div>

                    <?php } ?>




                </div>

            </div>

        </div>

    </div>

</body>

<!-- <script src="../assets/script/check_inout.js"></script> -->
<script>
    var originalDiv_temporary = $('.set-temporary').eq(0); // ใช้ .eq(0) เพื่อเลือก element แรกที่มี class นี้
    var originalDiv_permit = $('.set-longTerm').eq(0); // ใช้ .eq(0) เพื่อเลือก element แรกที่มี class นี้

    var setTemporaryDiv = $('.detail-saveTime-all').find('.set-temporary');
    var setTemporaryDiv2 = $('.detail-saveTime-all').find('.set-longTerm');

    if (originalDiv_temporary.length == 1 && originalDiv_permit.length == 1) {

        console.log('1')

        var clonedDiv = originalDiv_temporary.clone();

        var btnGPSCheckInHTML = $('<div class="btnGPS-check-in">' +
            '<button type="button" data-bs-toggle="modal" data-bs-target="#check-in-Modal-gps" id="check-in-button-gps" style="background-color: #2db396;"><img src="../IMG/checkin.png" alt="">ลงชื่อเข้างาน</button>' +
            '<button type="button" data-bs-toggle="modal" data-bs-target="#check-out-Modal-gps" id="check-out-button-gps" style="background-color: #e1574b;"><img src="../IMG/checkout.png" alt="">ลงชื่อออกงาน</button>' +
            '</div>');

        clonedDiv.append(btnGPSCheckInHTML);
        clonedDiv.removeClass('set-temporary');

        clonedDiv.find('.topic-saveTime').remove();
        clonedDiv.find('.btn-request-approve button').remove();

        clonedDiv.appendTo('.checkInOut');

    } else if (originalDiv_temporary.length == 0 && originalDiv_permit.length == 1) {

        console.log('2')

        var clonedDiv = originalDiv_permit.clone();

        var btnGPSCheckInHTML = $('<div class="btnGPS-check-in">' +
            '<button type="button" data-bs-toggle="modal" data-bs-target="#check-in-Modal-gps" id="check-in-button-gps" style="background-color: #2db396;"><img src="../IMG/checkin.png" alt="">ลงชื่อเข้างาน</button>' +
            '<button type="button" data-bs-toggle="modal" data-bs-target="#check-out-Modal-gps" id="check-out-button-gps" style="background-color: #e1574b;"><img src="../IMG/checkout.png" alt="">ลงชื่อออกงาน</button>' +
            '</div>');

        clonedDiv.append(btnGPSCheckInHTML);
        clonedDiv.removeClass('set-temporary');

        clonedDiv.find('.topic-saveTime').remove();
        clonedDiv.find('.btn-request-approve button').remove();

        clonedDiv.appendTo('.checkInOut');

    }



    jQuery(() => {

        $('.modal').on('hidden.bs.modal', function(e) {
            // หา DOM elements ของแผนที่ในโมดัลและลบทิ้ง
            $(this).find('.leaflet-container').each(function() {
                $(this).remove(); // ลบ DOM element ทิ้ง
            });
        });






        setInterval(showThaiDateTime, 1000);

        setTimeout(function() {

            //พิกัด เข้างาน ทั้งหมด
            // $('.check-in-lat').html(Dis_Position.lat);
            // $('.check-in-lng').html(Dis_Position.lng);

            // $('.check-out-lat').html(Dis_Position.lat);
            // $('.check-out-lng').html(Dis_Position.lng);

            // $('.check-in-range').html(gps_rangeKM);


        }, 750);

    });



    function showThaiDateTime() {

        // สร้าง Object Date สำหรับเวลาปัจจุบัน
        let now = new Date();

        // แสดงผลวันที่และเวลาแบบปี พ.ศ.
        let thaiDateTime = formatThaiDateTime(now);

        // แสดงผลวันที่และเวลา
        $('.Display_datetime').text(thaiDateTime);
    }

    function formatThaiDateTime(dateObject) {
        let thaiMonths = [
            'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
            'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
        ];

        let thaiYear = dateObject.getFullYear() + 543;
        let thaiMonth = thaiMonths[dateObject.getMonth()];
        let thaiDay = dateObject.getDate();

        let hours = dateObject.getHours();
        let minutes = dateObject.getMinutes();
        let seconds = dateObject.getSeconds();

        // รูปแบบเวลาให้เป็น 2 หลัก
        minutes = ('0' + minutes).slice(-2);
        seconds = ('0' + seconds).slice(-2);

        let thaiTime = hours + ':' + minutes + ':' + seconds + ' น.';

        return 'วันที่: ' + thaiDay + ' ' + thaiMonth + ' ' + thaiYear + ' เวลา: ' + thaiTime;
    }

    function show_outside_range(distance, range) {
        let result = (distance - range) + 3;
        return result
    }

    function getCurrentLocation(callback) {
        const options = {
            enableHighAccuracy: true,
            timeout: 10000,
        };

        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    callback({
                        latitude,
                        longitude
                    });
                },
                (error) => {
                    // หากเกิดข้อผิดพลาดในการรับค่าพิกัด หรือผู้ใช้ไม่เปิด GPS
                    callback(null);
                },
                options // เพิ่ม options ที่กำหนดการเรียกใช้งานของ geolocation.getCurrentPosition
            );
        } else {
            // หากเบราว์เซอร์ไม่รองรับการรับค่าพิกัด
            callback(null);
        }
    }

    function meterToKilometer(meter) {
        return meter / 1000;
    }

    function combineCoords(latitude, longitude) {
        return latitude + ',' + longitude;
    }

    function CoordsTo_Object(coordinatesString) {
        // แยกค่าพิกัดตาม comma (,)
        const [latitude, longitude] = coordinatesString.split(',');

        // สร้างอ็อบเจ็กต์ที่เก็บค่าพิกัด
        const coordinatesObject = {
            lat: parseFloat(latitude),
            lng: parseFloat(longitude),
        };

        return coordinatesObject;
    }

    function coords_str(coords) {
        let {
            lat,
            lng
        } = coords;
        let result = lat + "," + lng;
        return result;
    }

    function getDistance() {
        let distance = calculateDistance(Cur_Position, Dis_Position);
        console.log('Cur_Position: ' + Cur_Position);
        console.log('Dis_Position: ' + Dis_Position);
        distance.bold();
        Cal_distance = distance;
    }

    function calculateDistanceInMeters(lat1, lon1, lat2, lon2) {
        const R = 6371000; // รัศมีของโลกในหน่วยเมตร
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        const distance = Math.round(R * c); // ปัดเลขทศนิยม
        return distance;
    }


    getCurrentLocation(function(location) {
        if (location !== null) {

            // ทำสิ่งที่ต้องการกับพิกัดที่ได้รับ
            console.log("Latitude:", location.latitude);
            console.log("Longitude:", location.longitude);

            $('#check-in-Modal-gps').on('show.bs.modal', function() {

                // $(this).find('.leaflet-container').remove();

                let closestInput = $('.input-checkIN-coords'); // หา element ที่ใกล้ที่สุด
                let closestInputRange = $('.input-check-range'); // หา element ที่ใกล้ที่สุด

                let checkIN_lat = closestInput.data('lat'); // ดึงค่าของ data-lat
                let checkIN_lng = closestInput.data('lng'); // ดึงค่าของ data-lng
                let checkIN_range = closestInputRange.data('range'); // ดึงค่าของ data-lng

                console.log(checkIN_lat);
                console.log(checkIN_lng);
                console.log(checkIN_range);

                let checkIN_rangeKM = meterToKilometer(checkIN_range);

                let checkIN_str = combineCoords(checkIN_lat, checkIN_lng);

                // console.log('range:', checkIN_range); // แสดงค่าของ lng ใน console
                // console.log('lat_lng:', checkIN_str); // แสดงค่าของ lat ใน console

                checkIN_coords_obj = CoordsTo_Object(checkIN_str);


                user_coords_str = {
                    lat: location.latitude,
                    lng: location.longitude
                };

                // console.log('ปลายทาง: ' + checkIN_coords_obj)
                // console.log('ผู้ใช้: ' + user_coords_str)

                let map = L.map('modal-checkIN-map');
                map.setView(user_coords_str, 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

                // console.log(user_coords_str)
                // console.log(checkIN_coords_obj)

                let Cur_Position_marker = new L.marker(user_coords_str).addTo(map);
                let Dis_Position_marker = new L.marker(checkIN_coords_obj).addTo(map);

                // console.warn(Cur_Position_marker);

                let circleP = L.circle(checkIN_coords_obj, {
                    color: '#1E90FF',
                    fillColor: '#82EEFD',
                    fillOpacity: 0.4,
                    radius: checkIN_range
                }).addTo(map);

                circleP.bindPopup("จุดเข้างาน.");


                map.on('click', function() {
                    map.invalidateSize();
                    console.log('map reload!');
                });
                setTimeout(function() {
                    map.invalidateSize();
                    console.log('map reload!');
                }, 500);

                console.log('range: ' + checkIN_rangeKM);

                $('.check-in-lat').html(checkIN_lat);
                $('.check-in-lng').html(checkIN_lng);
                $('.check-in-range').html(checkIN_rangeKM);

                const distance = calculateDistanceInMeters(user_coords_str.lat, user_coords_str.lng, checkIN_coords_obj.lat, checkIN_coords_obj.lng);

                // console.log('Distance: ' + distance);

                let outside_range_result = show_outside_range(distance, checkIN_range);
                let distance_result = distance - checkIN_range;

                // console.log(outside_range_result);
                // console.log(distance_result);

                if (distance_result >= 0) {

                    let closestInput = $('#location-in'); // หา element ที่ใกล้ที่สุด
                    $('#location-in').html("อีก " + outside_range_result + " เมตร ถึงจะลงชื่อได้.");

                } else if (distance_result < 0) {

                    $('#location-in').html("ท่านอยู่ในรัศมีพิกัดที่กำหนด.");
                }

                $("#check-in-Modal-gps").on('click', '#check-in-button-gps', function() {

                    console.log('Modal ลงชื่อเข้า');

                    check_in_str = checkIN_str;

                    const localTime = getCurrentLocalTime();

                    let distance_result = distance - checkIN_range;

                    if (distance_result < 0) {
                        if (map != undefined) {
                            // ถ้ามีให้ทำลายแผนที่เก่าก่อนที่จะสร้างใหม่
                            map.remove();
                        }
                        Swal.fire({
                            title: '<span style="font-size: 7vw;">ยืนยันลงชื่อเข้างานหรือไม่?</span>',
                            html: `
                            <div>
                                <span class="Time" style="text-align:center;">
                                    <br><span class="Display_datetime"></span>
                                </span>
                            </div>
                                `,
                            icon: "question",
                            showCancelButton: true,
                            confirmButtonColor: "#29ab29",
                            confirmButtonText: "ลงชื่อ",
                            cancelButtonColor: "#e1574b",
                            cancelButtonText: "ยกเลิก",

                        }).then((result) => {
                            if (result.isConfirmed) {

                                $('#gps-in-coords').val(check_in_str);
                                let formData = $("#form-check-gps").serialize();

                                $.ajax({
                                    type: "POST",
                                    url: "../processing/process_check_in.php", // ตั้งค่า URL ของไฟล์ PHP
                                    data: formData,
                                    success: function(response) {
                                        if (response === 'check-in-complete') {
                                            Swal.fire({
                                                title: "ลงชื่อแล้ว",
                                                text: "คุณได้ลงชื่อเข้างานแล้ว.",
                                                icon: "success",
                                                timer: 2000,

                                            }).then((result) => {
                                                if (result.dismiss === Swal.DismissReason.timer) {
                                                    setTimeout(function() {
                                                        window.location.href = "../home/homepage-employee.php";
                                                    }, 500);
                                                }
                                            }).finally(() => {
                                                setTimeout(function() {
                                                    window.location.href = "../home/homepage-employee.php";
                                                }, 500);
                                            });
                                            console.log(response);
                                        } else if (response === 'already-checked-in') {
                                            Swal.fire({
                                                title: "เข้างานไปแล้ว",
                                                text: "คุณได้ลงชื่อเข้างานไปแล้ว.",
                                                icon: "warning",
                                            });
                                            console.log('คุณได้ลงชื่อเข้างานไปแล้ว.');
                                        } else if (response === 'already-request-leave') {
                                            console.log('คุณได้ลางานไว้แล้ว.');
                                        } else if (response === 'no-data-in-record') {
                                            console.log('ไม่พบข้อมูล.');
                                        } else if (response === 'not-check-in') {
                                            console.log('ยังไม่ได้ลงชื่อเข้างาน.');
                                        } else {
                                            console.log('ERROR = ', response);
                                        }
                                        $('#gps-in-coords').val('');
                                    },
                                    error: function(error) {
                                        Swal.fire({
                                            title: "เกิดข้อผิดพลาด",
                                            text: "ไม่สามารถลงชื่อได้.",
                                            icon: "error"
                                        });
                                        console.error(error);
                                    }
                                });
                            }
                            return;
                        });

                    } else if (distance_result >= 0) {
                        $('#location-in').html("อยู่ห่างเกินไป.")
                    }


                });

            });


            $('#check-out-Modal-gps').on('show.bs.modal', function() {

                let closestInput = $('.input-checkIN-coords'); // หา element ที่ใกล้ที่สุด
                let closestInputRange = $('.input-check-range'); // หา element ที่ใกล้ที่สุด

                let checkIN_lat = closestInput.data('lat'); // ดึงค่าของ data-lat
                let checkIN_lng = closestInput.data('lng'); // ดึงค่าของ data-lng
                let checkIN_range = closestInputRange.data('range'); // ดึงค่าของ data-lng

                let checkIN_rangeKM = meterToKilometer(checkIN_range);

                let checkIN_str = combineCoords(checkIN_lat, checkIN_lng);

                // console.log('range:', checkIN_range); // แสดงค่าของ lng ใน console
                // console.log('lat_lng:', checkIN_str); // แสดงค่าของ lat ใน console

                checkIN_coords_obj = CoordsTo_Object(checkIN_str);

                user_coords_str = {
                    lat: location.latitude,
                    lng: location.longitude
                };

                // console.log('ปลายทาง: ' + checkIN_coords_obj)
                // console.log('ผู้ใช้: ' + user_coords_str)

                let map = L.map('modal-checkOUT-map');
                map.setView(user_coords_str, 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

                let Cur_Position_marker = new L.marker(user_coords_str).addTo(map);
                let Dis_Position_marker = new L.marker(checkIN_coords_obj).addTo(map);

                // console.warn(Cur_Position_marker);

                let circleP = L.circle(checkIN_coords_obj, {
                    color: '#1E90FF',
                    fillColor: '#82EEFD',
                    fillOpacity: 0.4,
                    radius: checkIN_range
                }).addTo(map);

                circleP.bindPopup("จุดเข้างาน.");


                map.on('click', function() {
                    map.invalidateSize();
                    console.log('map reload!');
                });
                setTimeout(function() {
                    map.invalidateSize();
                    console.log('map reload!');
                }, 500);

                $('.check-out-lat').html(checkIN_lat);
                $('.check-out-lng').html(checkIN_lng);
                $('.check-out-range').html(checkIN_rangeKM);

                const distance = calculateDistanceInMeters(user_coords_str.lat, user_coords_str.lng, checkIN_coords_obj.lat, checkIN_coords_obj.lng);

                // console.log('Distance: ' + distance);

                let outside_range_result = show_outside_range(distance, checkIN_range);
                let distance_result = distance - checkIN_range;

                // console.log(outside_range_result);
                // console.log(distance_result);

                if (distance_result >= 0) {

                    let closestInput = $('#location-out'); // หา element ที่ใกล้ที่สุด
                    $('#location-out').html("อีก " + outside_range_result + " เมตร ถึงจะลงชื่อได้.");

                } else if (distance_result < 0) {

                    $('#location-out').html("ท่านอยู่ในรัศมีพิกัดที่กำหนด.");
                }

                $("#check-out-Modal-gps").on('click', '#check-out-button-gps', function() {

                    console.log('Modal ลงชื่อเข้า');

                    check_in_str = checkIN_str;

                    const localTime = getCurrentLocalTime();

                    let distance_result = distance - checkIN_range;

                    if (distance_result < 0) {
                        Swal.fire({
                            title: '<span style="font-size: 7vw;">ยืนยันลงชื่อออกงานหรือไม่?</span>',
                            html: `
                            <div>
                                <span class="Time" style="text-align:center;">
                                    <br><span class="Display_datetime"></span>
                                </span>
                            </div>
                                `,
                            icon: "question",
                            showCancelButton: true,
                            confirmButtonColor: "#29ab29",
                            confirmButtonText: "ลงชื่อ",
                            cancelButtonColor: "#e1574b",
                            cancelButtonText: "ยกเลิก",

                        }).then((result) => {
                            if (result.isConfirmed) {

                                $('#gps-out-coords').val(check_in_str);
                                let formData = $("#form-check-gps").serialize();

                                $.ajax({
                                    type: "POST",
                                    url: "../processing/process_check_in.php", // ตั้งค่า URL ของไฟล์ PHP
                                    data: formData,
                                    success: function(response) {
                                        if (response === 'check-out-complete') {
                                            Swal.fire({
                                                title: "ลงชื่อแล้ว",
                                                text: "คุณได้ลงชื่อออกงานแล้ว.",
                                                icon: "success",
                                            }).then((result) => {
                                                if (result.dismiss === Swal.DismissReason.timer) {
                                                    setTimeout(function() {
                                                        window.location.href = "../home/homepage-employee.php";
                                                    }, 500);
                                                }
                                            }).finally(() => {
                                                setTimeout(function() {
                                                    window.location.href = "../home/homepage-employee.php";
                                                }, 500);
                                            });
                                            console.log(response);
                                        } else if (response === 'already-checked-out') {
                                            Swal.fire({
                                                title: "ออกงานไปแล้ว",
                                                text: "คุณได้ลงชื่อออกงานไปแล้ว.",
                                                icon: "warning",
                                            });
                                            console.log('คุณได้ลงชื่อออกงานไปแล้ว.');
                                        } else if (response === 'not-check-in') {
                                            Swal.fire({
                                                title: "ยังไม่ได้เข้างาน",
                                                text: "คุณยังไม่ได้ลงชื่อเข้างาน.",
                                                icon: "warning",
                                            });
                                            console.log('คุณไม่ได้ลงชื่อเข้างาน.');
                                        } else if (response === 'already-request-leave') {
                                            console.log('คุณได้ลางานไว้แล้ว.');
                                        } else if (response === 'no-data-in-record') {
                                            console.log('ไม่พบข้อมูล.');
                                        } else {
                                            console.log('ERROR = ', response);
                                        }
                                        $('#gps-out-coords').val('');
                                    },
                                    error: function(error) {
                                        Swal.fire({
                                            title: "เกิดข้อผิดพลาด",
                                            text: "ไม่สามารถลงชื่อได้.",
                                            icon: "error"
                                        });
                                        console.error(error);
                                    }
                                });
                            }
                            return;
                        });

                    } else if (distance_result >= 0) {
                        $('#location-out').html("อยู่ห่างเกินไป.")
                    }


                });

            });

        } else {
            // ไม่สามารถรับค่าพิกัดได้หรือผู้ใช้ไม่ได้เปิด GPS
            console.log("ไม่สามารถรับค่าพิกัดได้หรือผู้ใช้ไม่ได้เปิด GPS");
        }
    });

    $('.open-modal-btn').on('click', function() {

        let lat = $(this).data('lat');
        let lng = $(this).data('lng');
        let range = $(this).data('range');

        let rangeKM = meterToKilometer(range);

        console.log('lat: ' + lat)
        console.log('lng: ' + lng)
        console.log('range: ' + range)

        let typeTimeHTML =

            '<div class="type-time">' +
            '<div class="center" id="display-map"></div><br>' +

            '<div >' +
            '<span>ลิติจูด : </span>' +
            '<span name="work_lat" class="check-in-lat"></span>' +
            '</div>' +

            '<div>' +
            '<span>ลองติจูด : </span>' +
            '<span name="work_lng" class="check-in-lng"></span>' +
            '</div>' +

            '<div>' +
            '<span>รัศมีจากจุดที่กำหนด : </span>' +
            '<span name="work_range" class="check-in-range"></span>' + '<span> กม.</span>' +
            '</div>' +

            '</div>';

        Swal.fire({
            html: typeTimeHTML,
            padding: '2em',
            showConfirmButton: false,
            showCancelButton: true,
            cancelButtonText: 'ปิด',
            cancelButtonColor: '#e1574b',
            customClass: {
                // cancelButtonText: 'swal2-cancel'
                popup: 'custom-popup-class'
            },
            didOpen: () => {

                check_in_obj = {
                    lat: lat,
                    lng: lng
                };

                let map = L.map('display-map').setView(check_in_obj, 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

                new L.marker(check_in_obj).addTo(map);

                console.log("พิกัดเดียวกัน.");

                let circleP = L.circle(check_in_obj, {
                    color: '#1E90FF',
                    fillColor: '#82EEFD',
                    fillOpacity: 0.4,
                    radius: range
                }).addTo(map);

                //ละติจูดและลองติจูดเข้างาน
                $("span[name='work_lat']").html(lat);
                $("span[name='work_lng']").html(lng);

                //ละติจูดและลองติจูดเข้างาน
                $("span[name='work_range']").html(rangeKM);


            }


        })



    });

    function getCurrentLocalTime() {
        const time_now = new Date();
        const options = {
            timeZone: 'Asia/Bangkok',
            hour12: false,
            hour: '2-digit',
            minute: '2-digit'
        };
        return time_now.toLocaleTimeString('en-US', options);
    }
</script>



<?php include('../includes/footer.php'); ?>