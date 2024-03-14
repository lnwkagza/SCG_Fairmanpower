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
<link rel="stylesheet" href="../css/adminselflearningpage.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.3/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.3/dist/sweetalert2.all.min.js"></script>
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

    
    $search_query = isset($_GET['search']) ? $_GET['search'] : '';
    $sql1 = "SELECT Tablechapter.course_id, course_name, chapter_name, status_VDO, status_total
            FROM Tablecourse 
            LEFT JOIN Tablechapter ON Tablechapter.course_id = Tablecourse.course_id
            LEFT JOIN Tablepositiontarget ON Tablepositiontarget.chapter_id = Tablechapter.chapter_id
            LEFT JOIN Tabletrainningdata ON Tabletrainningdata.person_id = Tablepositiontarget.person_id
                                    AND Tabletrainningdata.chapter_id = Tablechapter.chapter_id
            WHERE Tablepositiontarget.person_id = ? AND (Tablecourse.course_id LIKE '%$search_query%' OR Tablecourse.course_name LIKE '%$search_query%' OR Tablechapter.chapter_name LIKE '%$search_query%')
            ORDER BY Tablechapter.course_id DESC";
    $params1 = array($user_id);
    $stmt1 = sqlsrv_query($conn, $sql1, $params1);
    if( $stmt1 === false ) {
        die( print_r( sqlsrv_errors(), true));
    }

    $sql2 = "SELECT * FROM Tabletypelearning";
    $stmt2 = sqlsrv_query($conn, $sql2);
    if( $stmt2 === false ) {
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
                            <span id="head">Self Learning</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 mb-30">
                    <div class="card-box pd-10 pt-10 height-100-p">
                        <div class="bar">
                            <div class="mainclass">

                                <div class="menubar">
                                    <div class="menu2"><a href="adminOJTpage.php" class="a3">OJT</a></div>
                                    <div class="menu1"><a href="#" class="a2">Self Learning</a></div>
                                </div>

                                <div class="inputcon">

                                    <div class="inputtitle">กรุณาระบุข้อมูลหลักสูตรที่ท่านเคยเรียนมาให้ครบถ้วน</div>

                                    <form action="../../backend/db_adminselflearningpage.php" method="post" enctype="multipart/form-data">

                                        <div class="subinput">
                                            <div class="lable2"><label for="self_name">Self Learning เรื่อง</label></div>
                                            <input type="text" class="input1" name="self_name" id="self_name" autocomplete="off">
                                        </div>

                                        <div class="subinput">
                                            <div class="lable1"><label for="self_type">ประเภท</label></div>
                                            <!-- <input type="text" class="input1" name="ojt_name" id="ojt_name"> -->
                                            <select name="self_type" id="self_type" class="input1">
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
                                            <div class="lable2"><label for="self_from">ชื่อสถาบัน หรือ วิทยากร</label></div>
                                            <input type="text" class="input1" name="self_from" id="self_from" autocomplete="off">
                                        </div>

                                        <div class="subinput">
                                            <div class="lable2"><label for="self_totallearn">จำนวนชั่วโมงการรียน</label></div>
                                            <input type="number" class="input1" name="self_totallearn" id="self_totallearn" min="0" pattern="\d*">
                                        </div>


                                        <div class="time">
                                            <div class="subinput1">
                                                <div class="lable3"><label for="self_timestart">เริ่มต้นวันที่</label></div>
                                                <input type="date" class="input2" name="self_timestart" id="self_timestart">
                                            </div>
                                            <div class="subinput1">
                                                <div class="lable3"><label for="self_timeend">สิ้นสุดวันที่</label></div>
                                                <input type="date" class="input2" name="self_timeend" id="self_timeend">
                                            </div>
                                        </div>

                                        <div class="subinput">
                                            <div class="lable1"><label for="self_link">ลิงค์</label></div>
                                            <input type="text" class="input1" name="self_link" id="self_link" autocomplete="off">
                                        </div>

                                        <div class="subinput">
                                            <div class="lable1"><label for="self_description">สรุปผลการเรียนรู้</label></div>
                                            <textarea rows="4" cols="50" class="input3" name="self_description" id="self_description"></textarea>
                                        </div>
                                        <div class="picture">
                                            <div class="lable5"><label>ไฟล์เเนบ (ถ้ามี)</label></div>
                                            <div class="inputimage">

                                                <div class="lable4"><label for="self_img"><i class="bi bi-image"></i> รูปภาพ</label>
                                                    <p id="fileError" style="color: white; margin-left: 2px "></p>
                                                </div>
                                                <input type="file" name="self_img" id="self_img" onchange="validateFile()" hidden>

                                                <div class="lable6"><label for="self_certificate"><i class="bi bi-card-checklist"></i> ใบประกาศนียบัตร</label>
                                                    <p id="fileError1" style="color: white; margin-left: 2px "></p>
                                                </div>
                                                <input type="file" name="self_certificate" id="self_certificate" onchange="validateFile1()" hidden>
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
<?php include('../../components/script.php') ?>
</body>

</html>
<script>
function validateFile() {
    var fileInput = document.getElementById('self_img');
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

function validateFile1() {
    var fileInput = document.getElementById('self_certificate');
    var filePath = fileInput.value;
    var allowedExtensions = /(\.pdf)$/i;
    var fileName = filePath.replace(/^.*[\\\/]/, ''); // ดึงชื่อไฟล์จากเส้นทาง

    if (!allowedExtensions.exec(filePath)) {
        document.getElementById('fileError1').innerHTML = 'นามสกุลไฟล์ไม่ถูกต้อง';
        fileInput.value = '';
        return false;
    } else {
        document.getElementById('fileError1').innerHTML = 'ชื่อไฟล์: ' + fileName;
    }
}
</script>