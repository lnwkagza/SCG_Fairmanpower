<?php 
    session_start();
    require_once '../../connect/connect.php';
    if(!isset($_SESSION['user_login']) && !isset($_SESSION['admin_login'])){
        $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
        header('location: ../../../../linelogin/index.html');
        exit();
    }

    // ถ้าผู้ใช้กด Logout
    if(isset($_GET['logout']) && isset($_SESSION['admin_login'])) {
        // ลบ session และส่งผู้ใช้กลับไปยังหน้าล็อกอิน
        unset($_SESSION['admin_login']);
        $_SESSION['error'] = 'คุณได้ออกจากระบบแล้ว';
        header('location: ../../../../linelogin/index.html');
        exit();
    }

?>

<?php include('../../components/head.php') ?>
<link rel="stylesheet" href="../../plugin/bootstrap-icons-1.11.2/font/bootstrap-icons.css">
<link rel="stylesheet" href="../../plugin/boxicons-2.1.4/css/boxicons.min.css">
<link rel="stylesheet" href="../css/adminOJTpage.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.3/dist/sweetalert2.min.css">
<link rel="stylesheet" href="../css/adminOJTselect.css">
<title>Admin Page</title>

<body>


<?php


    if(isset($_SESSION['user_login'])) {
        $user_id = $_SESSION['user_login'];
    } elseif(isset($_SESSION['admin_login'])) {
        $user_id = $_SESSION['admin_login'];
    } else {
        // ทำการกำหนดค่าเริ่มต้นสำหรับ $user_id ในกรณีที่ไม่มีค่าใน session ที่ต้องการ
        $user_id = null; // หรือค่าอื่น ๆ ตามที่ต้องการ
    }
    $role = $user_id;
    $sql = "SELECT 
    employee.employee_image,
    employee.card_id,
    employee.person_id,
    employee.scg_employee_id,
    employee.prefix_thai,
    employee.firstname_thai,
    employee.lastname_thai,
    employee.service_year,
    employee.service_month,
    employee.skill,
    position.name_eng AS position_name_eng,
    section.name_eng AS section_name_eng,
    department.name_eng AS department_name_eng,
    permission.name AS permission
    FROM employee 
    LEFT JOIN position_info ON employee.card_id = position_info.card_id
    LEFT JOIN position ON position_info.position_id = position.position_id
    LEFT JOIN cost_center ON employee.cost_center_organization_id = cost_center.cost_center_id
    LEFT JOIN section ON cost_center.section_id = section.section_id
    LEFT JOIN department ON section.department_id = department.department_id
    LEFT JOIN permission ON employee .permission_id = permission.permission_id  
     
            WHERE employee.person_id = ?";
    $params = array($user_id);
    $stmt = sqlsrv_query($conn, $sql, $params);
    if( $stmt === false ) {
        die( print_r( sqlsrv_errors(), true));
    }
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $image_path = '../../data/imageprofile/'.$row['employee_image'];
    $years = $row['service_year'];
    $months = $row['service_month'];



    $sql2 = "SELECT * FROM Tabletypelearning";
    $stmt2 = sqlsrv_query($conn, $sql2);
    if( $stmt2 === false ) {
        die( print_r( sqlsrv_errors(), true));
    }

    $sql3 = "SELECT Tablecourse.course_id,course_name,chapter_id,chapter_name FROM Tablecourse
    LEFT JOIN Tablechapter ON Tablechapter.course_id = Tablecourse.course_id";
    $stmt3 = sqlsrv_query($conn, $sql3);
    if( $stmt3 === false ) {
        die( print_r( sqlsrv_errors(), true));
    }

?>
<?php include('../../components/navbar.php') ?>
<?php include('../../components/sidebar.php') ?>

<div class="main-container">
    <div class="pd-ltr-20 xs-pl-10 pt-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <span id="head">OJT</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 mb-30">
                    <div class="card-box pd-10 pt-10 height-100-p">
                        <div class="bar">
                            <div class="mainclass">
                                <!-- <div class="subcon"> -->

                                    <div class="menubar">
                                        <div class="menu1"><a href="#" class="a2">OJT</a></div>
                                        <div class="menu2"><a href="adminselflearningpage.php" class="a3">Self Learning</a></div>
                                    </div>

                                    <div class="inputcon">

                                        <div class="inputtitle">กรุณากรอกรายละเอียด OJT</div>

                                        <form action="../../backend/db_adminOJTpage.php" method="post" enctype="multipart/form-data">

                                            <div class="subinput">
                                                <div class="lable1"><label for="ojt_name">OJT เรื่อง</label></div>
                                                <input type="text" class="input1" name="ojt_name" id="ojt_name" autocomplete="off" required>
                                            </div>

                                            <div class="subinput">
                                                <div class="lable1"><label for="ojt_type">ประเภท</label></div>
                                                <!-- <input type="text" class="input1" name="ojt_name" id="ojt_name"> -->
                                                <select name="ojt_type" id="ojt_type" class="input1" required>
                                                    <option value=""></option>
                                                    <?php
                                                    while ($row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
                                                        $value = $row2['tyelearning_name']; // Change 'TypeLearningColumn' to the actual column name in your table
                                                        echo "<option value=\"$value\">$value</option>";
                                                    }
                                                    sqlsrv_free_stmt($stmt2);
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="subinput">
                                                <div class="lable1"><label for="ojt_chapter">บทเรียน</label></div>
                                                <!-- <input type="text" class="input1" name="ojt_name" id="ojt_name"> -->
                                                <select name="ojt_chapter" id="ojt_chapter" class="selectpicker" data-live-search="true" required>
                                                    <option value=""></option>
                                                    <option value="0">อื่นๆ</option>
                                                    <?php
                                                    $prev_course_name = null;
                                                    $current_optgroup_started = false;

                                                    while ($row3 = sqlsrv_fetch_array($stmt3, SQLSRV_FETCH_ASSOC)) {
                                                        if ($row3['course_name'] !== $prev_course_name) {
                                                            // เมื่อพบ course_name ใหม่
                                                            if ($current_optgroup_started) {
                                                                // ถ้ามีการเริ่มต้น optgroup แล้ว ให้ปิด optgroup ก่อน
                                                                echo '</optgroup>';
                                                                $current_optgroup_started = false;
                                                            }

                                                            // เริ่มต้น optgroup ใหม่
                                                            echo '<optgroup label="' . $row3['course_id'] . ' : ' . $row3['course_name'] . '">';
                                                            $prev_course_name = $row3['course_name'];
                                                            $current_optgroup_started = true;
                                                        }

                                                        // แสดง option ของทุก chapter_name ที่เกี่ยวข้องกับ course_name นั้นๆ
                                                        echo '<option value="' . $row3['chapter_id'] . '">' . $row3['chapter_name'] . '</option>';
                                                    }

                                                    // ตรวจสอบว่ามี optgroup ที่ยังไม่ได้ปิดหรือไม่
                                                    if ($current_optgroup_started) {
                                                        // ปิด optgroup ที่ยังไม่ได้ปิด
                                                        echo '</optgroup>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="subinput">
                                                <div class="lable2"><label for="ojt_totallearn">จำนวนชั่วโมงการรียน</label></div>
                                                <input type="number" class="input1" name="ojt_totallearn" id="ojt_totallearn" min="0" pattern="\d*" required autocomplete="off">
                                            </div>

                                            <div class="time">
                                                <div class="subinput1">
                                                    <div class="lable3"><label for="ojt_timestart">เริ่มต้นวันที่</label></div>
                                                    <input type="date" class="input2" name="ojt_timestart" id="ojt_timestart" required>
                                                </div>
                                                <div class="subinput1">
                                                    <div class="lable3"><label for="ojt_timeend">สิ้นสุดวันที่</label></div>
                                                    <input type="date" class="input2" name="ojt_timeend" id="ojt_timeend" required>
                                                </div>
                                            </div>

                                            <div class="subinput">
                                                <div class="lable1"><label for="ojt_description">สรุปผลการเรียนรู้</label></div>
                                                <textarea rows="4" cols="50" class="input3" name="ojt_description" id="ojt_description"></textarea>
                                            </div>
                                            <div class="picture">
                                                <div class="lable5"><label>ไฟล์เเนบ (ถ้ามี)</label></div>
                                                <div class="inputimage">

                                                    <div class="lable4">
                                                        <label for="ojt_img"><i class="bi bi-image"></i> รูปภาพ<p id="fileError" style="color: white; margin: auto;"></p></label>
                                                    </div>
                                                    <input type="file" name="ojt_img" id="ojt_img" onchange="validateFile()" hidden>
                                                </div>
                                            </div>

                                            <button type="submit" name="save" class="button1">บันทึก</button>

                                        </form>

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

</body>

</html>

<!-- <script>
$(document).ready(function() {
    // เลือกตัวเลือก ojt_chapter และเปลี่ยนเป็น dropdown ที่สามารถค้นหาได้
    $('#ojt_chapter').select2({
        placeholder: 'เลือกบทเรียน',
        allowClear: true, // ให้สามารถลบตัวเลือกที่เลือกไว้ได้
        minimumInputLength: 20, // ต้องกรอกอย่างน้อย 1 ตัวอักษรเพื่อให้เริ่มค้นหา
        maximumSelectionLength: 1,
    });
});
</script> -->
<script>
function validateFile() {
    var fileInput = document.getElementById('ojt_img');
    var filePath = fileInput.value;
    var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
    var fileName = filePath.replace(/^.*[\\\/]/, ''); // ดึงชื่อไฟล์จากเส้นทาง

    if (!allowedExtensions.exec(filePath)) {
        document.getElementById('fileError').innerHTML = 'รูปแบบไฟล์ไม่ถูกต้อง';
        fileInput.value = '';
        return false;
    } else {
        document.getElementById('fileError').innerHTML = 'ชื่อไฟล์: ' + fileName;
    }
}
</script>
<script src="../../plugin/vendors/scripts/core.js"></script>
<script src="../../plugin/vendors/scripts/script.min.js"></script>