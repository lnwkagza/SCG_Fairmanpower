<?php
session_start();
require_once '../../connect/connect.php';
if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: ../../../../linelogin/index.html');
    exit();
}

// ถ้าผู้ใช้กด Logout
if (isset($_GET['logout']) && isset($_SESSION['admin_login'])) {
    // ลบ session และส่งผู้ใช้กลับไปยังหน้าล็อกอิน
    unset($_SESSION['admin_login']);
    $_SESSION['error'] = 'คุณได้ออกจากระบบแล้ว';
    header('location: ../../../../linelogin/index.html');
    exit();
}

if (isset($_SESSION['last_viewed_lesson'])) {
    unset($_SESSION['last_viewed_lesson']);
    if (!isset($_SESSION['last_viewed_lesson'])) {
    }
}

?>

<?php include('../../components/head.php') ?>
<link rel="stylesheet" href="../../plugin/bootstrap-icons-1.11.2/font/bootstrap-icons.css">
<link rel="stylesheet" href="../../plugin/boxicons-2.1.4/css/boxicons.min.css">
<link rel="stylesheet" href="../css/uploadadminmain.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300&display=swap" rel="stylesheet">
<title>AdminPage</title>

<script>
    let numTest = 0
    const fileInput = [];
    const videoForm = [];
</script>

<body>
    

    <?php


    if(isset($_SESSION['user_login'])) {
        $user_id = $_SESSION['user_login'];
        echo $user_id;
    } elseif(isset($_SESSION['admin_login'])) {
        $user_id = $_SESSION['admin_login'];
        echo $user_id;
    } else {
        // ทำการกำหนดค่าเริ่มต้นสำหรับ $user_id ในกรณีที่ไม่มีค่าใน session ที่ต้องการ
        $user_id = null; // หรือค่าอื่น ๆ ตามที่ต้องการ
        echo $user_id;
    }
    
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


    // $sql1 = "SELECT * FROM Tablecourse 
    // LEFT JOIN Tablechapter ON Tablecourse.course_id = Tablechapter.course_id";
    // $stmt1 = sqlsrv_query($conn, $sql1);
    // if ($stmt1 === false) {
    //     die(print_r(sqlsrv_errors(), true));
    // }

    $sql1 = "SELECT *, Tablecourse.course_id AS course_code, Tablepositiontarget.chapter_id AS quizchapter_id, Tablechapter.chapter_id AS chapter_code FROM Tablecourse 
        LEFT JOIN Tablechapter ON Tablecourse.course_id = Tablechapter.course_id
		LEFT JOIN Tablepositiontarget ON Tablechapter.chapter_id = Tablepositiontarget.chapter_id
        LEFT JOIN Tablequiz ON Tablechapter.chapter_id = Tablequiz.chapter_id";
    // ORDER BY course_date DESC
    if (isset($_GET['search_course'])) {
        $search_term = $_GET['search_course'];
        $sql1 .= " WHERE Tablecourse.course_name LIKE ? OR Tablecourse.course_id LIKE ? OR Tablechapter.chapter_name LIKE ? OR Tablechapter.chapter_id LIKE ? ";
        $params_search = array("%$search_term%", "%$search_term%", "%$search_term%", "%$search_term%"   );
        $stmt1 = sqlsrv_query($conn, $sql1, $params_search);
        if ($stmt1 === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    } else {
        $stmt1 = sqlsrv_query($conn, $sql1);
        if ($stmt1 === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }

    $sql2 = "SELECT course_id, course_name FROM Tablecourse";
    $stmt2 = sqlsrv_query($conn, $sql2);
    if ($stmt2 === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    $displayedCourses = [];
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
                                <span id="head">อัปโหลดข้อมูล</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-9 col-md-6 col-sm-12 mb-30">
                        <div class="card-box pd-10 pt-10 height-100-p">
                            <div class="bar">
                                <div class="mainclass">
                                    <div class="addcourse">
                                        <form action="" method="GET">
                                            <div class="secrsebutton">
                                                <input type="text" name="search_course" placeholder="search" class="input1" autocomplete="off">
                                            </div>
                                        </form>
                                    </div>

                                    <div class="tablecourse">
                                        <table>
                                            <tr>
                                                <th>หลักสูตร</th>
                                                <th>ประเภท</th>
                                                <th>VDO</th>
                                                <th>กลุ่มเป้าหมายหลัก</th>
                                                <th>กลุ่มเป้าหมายอื่นๆ</th>
                                                <th>แบบประเมิน</th>
                                                <th>ลบข้อมูล</th>
                                            </tr>
                                            <?php
                                            $prev_course_name = null;
                                            $num = 0;
                                            while ($row1 = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
                                                $isChecked = $row1['general_target'];
                                                $isCheckedAttr = $isChecked ? "checked" : "";
                                                echo "<tr>";
                                                if ($row1['course_name'] !== $prev_course_name) { //ป้องกันไม่ให้ลูปเเสดงหัวข้อหลักสูตรซ้ำซ้อน
                                                    $prev_course_name = $row1['course_name'];
                                            ?>
                                                    <td class="td1" colspan="6">
                                                        <div class="form-container">
                                                            <form action="../../backend/db_editcourseid.php" method="post">
                                                                <input type="hidden" name="course_id" value="<?php echo $row1['course_id']; ?>">
                                                                <input class="course_code" type="text" name="course_code" value="<?php echo htmlspecialchars($row1['course_code']); ?>">:
                                                            </form>

                                                            <form id="editcourse" action="../../backend/db_editcourse.php" method="post">
                                                                <input type="hidden" name="course_code" value="<?php echo $row1['course_code']; ?>">
                                                                <input class="course_name" type="text" name="course_name" value="<?php echo htmlspecialchars($row1['course_name']); ?>">
                                                            </form>


                                                        </div>
                                                    </td>
                                                    <td class="td1">
                                                        <form id="deleteFormcourse" action="../../backend/db_upload_to_delcourse.php" method="post">
                                                            <input type="hidden" name="course_name" value="<?php echo $row1['course_name']; ?>">
                                                            <button type="submit" name="deletecoure" class="buttondelete"><i class='bi bi-trash-fill'></i></button>
                                                        </form>
                                                    </td>
                                                <?php
                                                    echo "</tr><tr>";
                                                }
                                                ?>
                                                <?php
                                                if (!in_array($row1['chapter_name'], $displayedCourses)) {
                                                    if ($row1['chapter_name'] !== null) { //ใช้เช็คว่าถ้าchapter_nameไม่ใช่ค่าว่างให้เอามาเเสดง
                                                ?>
                                                        <td class="td2">
                                                            <div class="form-container">
                                                                <form id="editForm" action="../../backend/db_editchapter.php" method="post">
                                                                    <input type="hidden" name="chapter_id" value="<?php echo $row1['chapter_code']; ?>">
                                                                    <input class="chapter_name" type="text" name="chapter_name" value="<?php echo htmlspecialchars($row1['chapter_name']); ?>">

                                                                </form>


                                                            </div>
                                                        </td>

                                                        <td>
                                                            <form action="../../backend/db_edittype.php" method="POST" id="editFormtype">
                                                                <input type="hidden" name="chapter_id" value="<?php echo $row1['chapter_code']; ?>">
                                                                <select class="chapter_type" name="chapter_type">
                                                                    <option value="ตามตำแหน่ง" <?php if ($row1['chapter_type'] === 'ตามตำแหน่ง') echo 'selected'; ?>>ตามตำแหน่ง</option>
                                                                    <option value="กฎหมายบังคับ" <?php if ($row1['chapter_type'] === 'กฎหมายบังคับ') echo 'selected'; ?>>กฎหมายบังคับ</option>
                                                                    <option value="พื้นฐาน" <?php if ($row1['chapter_type'] === 'พื้นฐาน') echo 'selected'; ?>>พื้นฐาน</option>
                                                                    <option value="อื่นๆ" <?php if ($row1['chapter_type'] === 'อื่นๆ') echo 'selected'; ?>>อื่นๆ</option>
                                                                </select>
                                                            </form>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            if ($row1['VDO'] !== '') {
                                                            ?>
                                                                <form class="VDOtext1" action="../../backend/db_editVDO.php" method="POST" id="<?php echo "FOMVDO" . $num; ?>" enctype="multipart/form-data">
                                                                    <input type="hidden" name="chapter_id" id="<?php echo "chapter_id" . $num; ?>" value="<?php echo $row1['chapter_code']; ?>">
                                                                    <input type="file" name="VDO" id="<?php echo $num; ?>" class="VDO" accept="video/*"hidden>
                                                                    <label for="<?php echo $num; ?>" class="VDOtext1" class="btn btn-secondary" data-toggle="tooltip" data-placement="left" title="<?php echo  $row1['VDO'];?>">
                                                                    <i class="bi bi-pencil-square"></i> แก้ไข<br>
                                                                    <span class="vdoname" id = "vdoname"><?php echo  $row1['VDO'];?></span>
                                                                    </label>
                                                                    
                                                                </form>
                                                            <?php
                                                            } else {
                                                            ?>
                                                                <form class="VDOtext2" action="../../backend/db_editVDO.php" method="POST" id="<?php echo "FOMVDO" . $num; ?>" enctype="multipart/form-data">
                                                                    <input type="hidden" name="chapter_id" id="<?php echo "chapter_id" . $num; ?>" value="<?php echo $row1['chapter_code']; ?>">
                                                                    <input type="file" name="VDO" id="<?php echo $num; ?>" class="VDO" accept="video/*" hidden>
                                                                    <label for="<?php echo $num; ?>" class="VDOtext2"><i class="bi bi-plus"></i> เพิ่ม <br><span class="vdoname" >ยังไม่ได้อัพโหลด</span></label>
                                                                    
                                                                </form>

                                                            <?php
                                                            }
                                                            ?>
                                                        </td>
                                                        
                                                        <script>
                                                            console.log(numTest)
                                                            fileInput[numTest] = document.getElementById("<?php echo $num; ?>");
                                                            videoForm[numTest] = document.getElementById("<?php echo "FOMVDO" . $num; ?>");

                                                            fileInput[numTest].addEventListener('change', function(e) {
                                                                videoForm[Number(e.target.id)].submit();
                                                                console.log(typeof(Number(e.target.id)))
                                                                console.log("FOM" + e.target.id)
                                                            });
                                                            console.log(videoForm)
                                                            numTest += 1
                                                        </script>
                                                        <?php $num += 1; ?>

                                                        <td>
                                                            <?php
                                                            if ($row1['quizchapter_id'] !== NULL) {
                                                            ?>
                                                                <a href="addtarget.php?chapter_id=<?= $row1['chapter_code']; ?>"><button class="buttonedit"><i class="bi bi-pencil-square"></i> แก้ไข</button></a>
                                                            <?php
                                                            } else {
                                                            ?>
                                                                <a href="addtarget.php?chapter_id=<?= $row1['chapter_code']; ?>"><button class="buttonadd"><i class="bi bi-plus"></i> เพิ่ม</button></a>

                                                            <?php
                                                            }
                                                            ?>
                                                        </td>

                                                        <td>
                                                            <form id="checkboxForm" action="../../backend/db_editgeneral_target.php" method="POST">
                                                                <input type="checkbox" id="myCheckbox" name="myCheckbox" <?php echo $isCheckedAttr; ?> style="margin-right: 5px;">
                                                                <input type="hidden" id="chapter_id" name="chapter_id" value="<?php echo $row1['chapter_code']; ?>">
                                                                <button type="submit" name="save" class="savecheckbox"><i class="bi bi-floppy-fill"></i></button>
                                                            </form>
                                                        </td>


                                                        <td>
                                                            <?php
                                                            if ($row1['quiz_id'] !== null) {
                                                            ?>
                                                                <a href="editquiz.php?chapter_id=<?= $row1['chapter_code']; ?>"><button class="buttonedit"><i class="bi bi-pencil-square"></i> แก้ไข</button></a>
                                                            <?php
                                                            } else {
                                                            ?>
                                                                <a href="addquiz.php?chapter_id=<?= $row1['chapter_code']; ?>"><button class="buttonadd"><i class="bi bi-plus"></i> เพิ่ม</button></a>

                                                            <?php
                                                            }
                                                            ?>
                                                        </td>

                                                        <td>
                                                            <form id="deleteForm" action="../../backend/db_delchapter.php" method="post">
                                                                <input type="hidden" name="chapter_id" value="<?php echo $row1['chapter_code']; ?>">
                                                                <button type="submit" name="deletechapter" class="buttondelete"><i class='bi bi-trash-fill'></i></button>
                                                            </form>
                                                        </td>
                                            <?php
                                                        echo "</tr>";
                                                        $displayedCourses[] = $row1['chapter_name'];
                                                    }
                                                }
                                            }
                                            ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-3 col-md-3 ">
                        <div class="card-box1 pd-10 pt-10 height-50-p">
                            <span id="add"><i class="bi bi-plus-circle-fill"></i>เพิ่มหลักสูตร</span>
                            <div class="addform">
                                <form action="../../backend/db_addcourse.php" method="post">
                                    <label class="form-label">รหัสหลักสูตร<label style="color: red;">*</label></label> 
                                    <input  type="text" class="course_id" name="course_id" required autocomplete="off">
                                    <label style="margin-top: 10px;" class="form-label">ชื่อหลักสูตร<label style="color: red;">*</label></label>
                                    <input  type="text" class="course_name1" name="course_name" required autocomplete="off">
                                    <button type="submit" name="save" class="addbutton">บันทึก</button>
                                </form>
                            </div>

                        </div>
                        <div class="  pt-10 height-50-p">
                            <div class="card-box1 pd-10">
                                <form action="../../backend/db_addchapter.php" method="post" enctype="multipart/form-data">
                                    <span id="add"><i class="bi bi-plus-circle-fill"></i>เพิ่มบทเรียน</span>
                                    <div class="addlesson">
                                        <div class="manuaddchapter">
                                            <div class="row1">
                                                <label class="form-label">เลือกหลักสูตร</label>
                                                <select name="course_id" id="course_id" required>
                                                    <?php
                                                    while ($row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
                                                        echo '<option value="' . $row2['course_id'] . '">' . $row2['course_name'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="row2">
                                                <label class="form-label">กรอกชื่อบทเรียน<label style="color: red;">*</label></label>
                                                <input type="text" name="chapter_name" class="chapter_name" autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="manuaddchapter">
                                            <div class="row3">
                                                <label class="form-label">เลือกประเภทของบทเรียน</label>
                                                <select name="chapter_type" id="chapter_type">
                                                    <option value="ตามตำแหน่ง" selected="selected">ตามตำแหน่ง</option>
                                                    <option value="กฎหมายบังคับ">กฎหมายบังคับ</option>
                                                    <option value="พื้นฐาน">พื้นฐาน</option>
                                                    <option value="อื่นๆ">อื่นๆ</option>
                                                </select>
                                            </div>
                                            <div class="row4">
                                                <label class="form-label">เลือกคลิปวิดีโอการสอน</label>
                                                <input type="file" name="VDO" id="VDO" class="VDO" accept="video/*" hidden>
                                                <label for="VDO" class="VDOtext">เลือกไฟล์วิดีโอ</label>
                                            </div>
                                        </div>

                                        <div class="manuaddchapter">
                                            <div class="row5">
                                                <label class="form-label">เวลา Manday ที่ได้<label style="color: red;">*</label></label>
                                                <input type="number" step="0.1" name="chapter_time" class="chapter_time"  required autocomplete="off"> 
                                            </div>
                                            <div class="row6">
                                                <label class="form-label-checkbox">
                                                    <input type="checkbox" name="general_target" class="general_target">
                                                    <span>พนักงานทุกคนสามารถเรียนได้หรือไม่</span>
                                                </label>
                                            </div>

                                        </div>
                                        <div class="buttonsubmit"> <a href="addtarget.php?chapter_id=<?= $row1['chapter_code']; ?>"></a><button type="submit" name="save" class="addbutton">ถัดไป</button></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const deleteForms = document.querySelectorAll('form#deleteFormcourse');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(event) {
                const confirmDelete = confirm('คุณต้องการลบหรือไม่? ถ้าลบข้อมูลบทเรียนหรือข้อมูลบทเรียนในหลักสูตรทั้งหมดจะถูกลบ!!');
                if (!confirmDelete) {
                    event.preventDefault();
                } else {
                    console.log('Submitting delete form...');
                }
            });
        });
    </script>





    <script>
        document.querySelectorAll('form#deleteForm').forEach(form => {
            form.addEventListener('submit', function(event) {
                const confirmDelete = confirm('คุณต้องการลบหรือไม่?');
                if (!confirmDelete) {
                    event.preventDefault();
                } else {
                    console.log('Submitting delete form...');
                }
            });
        });
    </script>
    <script>
        const selectChapters = document.querySelectorAll('.chapter_type');
        selectChapters.forEach(selectChapter => {
            selectChapter.addEventListener('change', function() {
                const form = selectChapter.closest('form');
                form.submit();
            });
        });
    </script>
    <script>
        // รับ Element input ด้วยชื่อ class 'chapter_name'
        const inputField3 = document.querySelector('.chapter_name');
        // เมื่อมีการเปลี่ยนแปลงใน input field
        inputField3.addEventListener('change', function() {
            // เลือกแบบฟอร์มที่ต้องการส่งข้อมูลไป
            const form = document.getElementById('../../backend/db_editchapter.php');
            // ทำการส่งฟอร์มโดยใช้เมทอด submit() เมื่อมีการเปลี่ยนแปลงข้อมูล
            form.submit();
        });
    </script>

    <?php include('../../components/script.php') ?>
</body>

</html>