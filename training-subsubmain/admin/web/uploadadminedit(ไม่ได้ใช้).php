<?php 
    session_start();
    require_once 'connect\connect.php';
    if(!isset($_SESSION['admin_login'])){
        $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
        header('location: login.php');
        exit();
    }

    // ถ้าผู้ใช้กด Logout
    if(isset($_GET['logout']) && isset($_SESSION['admin_login'])) {
        // ลบ session และส่งผู้ใช้กลับไปยังหน้าล็อกอิน
        unset($_SESSION['admin_login']);
        $_SESSION['error'] = 'คุณได้ออกจากระบบแล้ว';
        header('location: login.php');
        exit();
    }

    if(isset($_SESSION['last_viewed_lesson'])) {
        unset($_SESSION['last_viewed_lesson']);
        if(!isset($_SESSION['last_viewed_lesson'])){
        }
    }
    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap-icons-1.11.2\font\bootstrap-icons.css">
    <link rel="stylesheet" href="boxicons-2.1.4\css\boxicons.min.css">
    <link rel="stylesheet" href="components\sidebaradminupload.css">
    <link rel="stylesheet" href="uploadadminedit.css">
    <link rel="stylesheet" href="components\navbarprofile.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300&display=swap" rel="stylesheet">
    <title>AdminPage</title>
</head>
<script>
    let numTest = 0
    const fileInput = [];
    const videoForm = [];
</script>
<body>

<?php

if(isset($_SESSION['user_login'])){
    $user_id = $_SESSION['user_login'];
    $role = $user_id;
    $sql = "SELECT 
    employee.employee_image,
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
    department.name_eng AS department_name_eng
    FROM employee 
    JOIN position_info ON employee.card_id = position_info.card_id
    JOIN position ON position_info.position_id = position.position_id
    JOIN cost_center ON employee.cost_center_organization_id = cost_center.cost_center_code
    JOIN section ON cost_center.section_id = section.section_id
    JOIN department ON section.department_id = department.department_id
     
            WHERE employee.person_id = ?";
    $params = array($user_id);
    $stmt = sqlsrv_query($conn, $sql, $params);
    if( $stmt === false ) {
        die( print_r( sqlsrv_errors(), true));
    }
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $image_path = 'imageprofile/'.$row['employee_image'];
    $years = $row['service_year'];
    $months = $row['service_month'];
}

        // $sql1 = "SELECT * FROM Tablecourse 
        // LEFT JOIN Tablechapter ON Tablecourse.course_id = Tablechapter.course_id";
        // $stmt1 = sqlsrv_query($conn, $sql1);
        // if ($stmt1 === false) {
        //     die(print_r(sqlsrv_errors(), true));
        // }

        $sql1 = "SELECT *, Tablecourse.course_id AS course_code FROM Tablecourse 
        LEFT JOIN Tablechapter ON Tablecourse.course_id = Tablechapter.course_id ";
        // ORDER BY course_date DESC
        if (isset($_GET['search_course'])) {
            $search_term = $_GET['search_course'];
            $sql1 .= " WHERE Tablecourse.course_name LIKE ? ";
            $params_search = array("%$search_term%");
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
        

?>

    <div class="contianer">
        
    <?php include('components\sidebaradmin.php');?>
    <?php include('components\navbarprofileadmin.php');?>

        <div class="title"><div class="titlename">การอัพโหลดข้อมูล</div></div>

        <div class="mainclass">
            <div class="buttoncon">
                <a href="uploadadminmain.php"><button try="button" class="uploadmanu2">เพิ่มและอัพโหลดข้อมูล</button></a>
                <a href="uploadadminedit.php"><button try="button" class="uploadmanu1">เเก้ไขข้อมูล</button></a>
            </div>
            <div class="addcourse">
            <form action="" method="GET">
                <div class="secrsebutton">
                    <input type="text" name="search_course"placeholder="search"class="input1">
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
                    </tr>
                    <?php
                        $prev_course_name = null;
                        $num = 0;
                        while ($row1 = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
                            $isChecked = $row1['general_target'];
                            $isCheckedAttr = $isChecked ? "checked" : "";
                                    echo "<tr>";
                                    if ($row1['course_name'] !== $prev_course_name) {//ป้องกันไม่ให้ลูปเเสดงหัวข้อหลักสูตรซ้ำซ้อน
                                        $prev_course_name = $row1['course_name'];
                                        ?>
                                                <td class="td1" colspan="6">
                                                    <div class="form-container">
                                                        <form action="db_editcourseid.php" method="post">
                                                            <input type="hidden" name="course_id" value="<?php echo $row1['course_id']; ?>">
                                                            <input class="course_code" type="text" name="course_code" value="<?php echo htmlspecialchars($row1['course_code']); ?>">:
                                                        </form>

                                                        <form id="editcourse" action="db_editcourse.php" method="post">
                                                            <input type="hidden" name="course_code" value="<?php echo $row1['course_code']; ?>">
                                                            <input class="course_name" type="text" name="course_name" value="<?php echo htmlspecialchars($row1['course_name']); ?>">
                                                        </form>
                                                        
                                                        <form id="deleteFormcourse" action="db_upload_to_delcourse.php" method="post">
                                                            <input type="hidden" name="course_name" value="<?php echo $row1['course_name']; ?>">
                                                            <button type="submit" name="deletecoure"class="buttondelete"><i class='bi bi-trash-fill'></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                        <?php 
                                        echo "</tr><tr>";
                                    }
                                    ?>
                                        <?php
                                        if ($row1['chapter_name']!== null){//ใช้เช็คว่าถ้าchapter_nameไม่ใช่ค่าว่างให้เอามาเเสดง
                                            ?>
                                        <td class="td2">
                                            <div class="form-container">
                                                <form id="editForm" action="db_editchapter.php" method="post">
                                                    <input type="hidden" name="chapter_id" value="<?php echo $row1['chapter_id']; ?>">
                                                    <input class="chapter_name" type="text" name="chapter_name" value="<?php echo htmlspecialchars($row1['chapter_name']); ?>">
                                                    
                                                </form>
                                                
                                                <form id="deleteForm" action="db_delchapter.php" method="post">
                                                    <input type="hidden" name="chapter_id" value="<?php echo $row1['chapter_id']; ?>">
                                                    <button type="submit" name="deletechapter"class="buttondelete"><i class='bi bi-trash-fill'></i></button>
                                                </form>
                                            </div>
                                        </td>

                                        <td> 
                                            <form action="db_edittype.php" method="POST" id="editFormtype">
                                                <input type="hidden" name="chapter_id" value="<?php echo $row1['chapter_id']; ?>">
                                                <select class="chapter_type" name="chapter_type">
                                                    <option value="ตามตำแหน่ง" <?php if ($row1['chapter_type'] === 'ตามตำแหน่ง') echo 'selected'; ?>>ตามตำแหน่ง</option>
                                                    <option value="กฎหมายบังคับ" <?php if ($row1['chapter_type'] === 'กฎหมายบังคับ') echo 'selected'; ?>>กฎหมายบังคับ</option>
                                                    <option value="พื้นฐาน" <?php if ($row1['chapter_type'] === 'พื้นฐาน') echo 'selected'; ?>>พื้นฐาน</option>
                                                    <option value="อื่นๆ" <?php if ($row1['chapter_type'] === 'อื่นๆ') echo 'selected'; ?>>อื่นๆ</option>
                                                </select>
                                            </form>
                                        </td>
                                <td>
                                    <form action="db_editVDO.php" method="POST" id="<?php echo "FOMVDO".$num; ?>" enctype="multipart/form-data">                    
                                        <input type="hidden" name="chapter_id" id="<?php echo "chapter_id".$num; ?>" value="<?php echo $row1['chapter_id']; ?>">
                                        <input type="file" name="VDO" id="<?php echo $num; ?>" class="VDO">
                                    </form>
                                </td>
                                <script>
                                    console.log(numTest)                      
                                    fileInput[numTest] = document.getElementById("<?php echo $num; ?>");
                                    videoForm[numTest] = document.getElementById("<?php echo "FOMVDO".$num; ?>");
                                   
                                    fileInput[numTest].addEventListener('change', function( e) {
                                        videoForm[Number(e.target.id)].submit();
                                        console.log(typeof (Number(e.target.id)))
                                        console.log("FOM"+e.target.id)   
                                    });
                                    console.log(videoForm)
                                    numTest+= 1
                                </script>
                                <?php $num+= 1; ?>

                                <td><a href="addtarget.php?chapter_id=<?= $row1['chapter_id']; ?>"><button class="buttonedit"><i class='bx bxs-edit-alt'></i></button></a></td>
                                <td>
                                    <form id="checkboxForm" action="db_editgeneral_target.php" method="POST">
                                        <input type="checkbox" id="myCheckbox" name="myCheckbox" <?php echo $isCheckedAttr; ?>>
                                        <input type="hidden" id="chapter_id" name="chapter_id" value="<?php echo $row1['chapter_id']; ?>">
                                        <button type="submit" name="save" class="savecheckbox"><i class="bi bi-floppy-fill"></i></button>
                                    </form>
                                </td>

                                <td><a href="editquiz.php?chapter_id=<?= $row1['chapter_id']; ?>"><button class="buttonedit"><i class="bi bi-file-earmark-arrow-down"></i></button></a></td>

                            <?php
                                echo "</tr>";
                    }
                    }
                    ?>
                </table>
                <button onclick="goBack()" class = "backbutton"><i class='bx bx-left-arrow-alt' ></i></button>
            </div>

        </div>
      
    </div>
                                            
                                            <script>
                                            function goBack() {
                                            window.history.back();
                                            }
                                            </script>
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
                                                const form = document.getElementById('db_editchapter.php');
                                                // ทำการส่งฟอร์มโดยใช้เมทอด submit() เมื่อมีการเปลี่ยนแปลงข้อมูล
                                                form.submit();
                                            });
                                            </script>

                                            <!-- <script>
                                            const inputField1 = document.querySelector('.course_name');
                                            inputField1.addEventListener('change', function() {
                                                const form = document.getElementById('db_editcourse.php');
                                                form.submit();
                                            });
                                            </script> -->

                                    
                                            <!-- <script>
                                            // รับ Element input ด้วยชื่อ class 'chapter_name'
                                            const inputFiel2 = document.querySelector('.course_code');
                                            // เมื่อมีการเปลี่ยนแปลงใน input field
                                            inputField2.addEventListener('change', function() {
                                                // เลือกแบบฟอร์มที่ต้องการส่งข้อมูลไป
                                                const form = document.getElementById('db_editcourseid.php');
                                                // ทำการส่งฟอร์มโดยใช้เมทอด submit() เมื่อมีการเปลี่ยนแปลงข้อมูล
                                                form.submit();
                                            });
                                            </script> -->
</body>
</html>