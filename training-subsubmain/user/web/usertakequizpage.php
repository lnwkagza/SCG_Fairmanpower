<?php 
    session_start();
    require_once '../../connect/connect.php';
    if(!isset($_SESSION['user_login'])){
        $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
        header('location: ../../../../linelogin/index.html');
        exit();
    }

    // ถ้าผู้ใช้กด Logout
    if(isset($_GET['logout']) && isset($_SESSION['user_login'])) {
        // ลบ session และส่งผู้ใช้กลับไปยังหน้าล็อกอิน
        unset($_SESSION['user_login']);
        $_SESSION['error'] = 'คุณได้ออกจากระบบแล้ว';
        header('location: ../../../../linelogin/index.html');
        exit();
    }

?>

<!DOCTYPE html>
<html lang="en">
<?php include('../../components/head.php') ?>
<link rel="stylesheet" href="../../plugin/bootstrap-icons-1.11.2/font/bootstrap-icons.css">
<link rel="stylesheet" href="../../plugin/boxicons-2.1.4/css/boxicons.min.css">
<link rel="stylesheet" href="../css/usertakequizpage.css">
<title>Admin Page</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.3/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.3/dist/sweetalert2.all.min.js"></script>

</head>
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


    $sql1 = "SELECT * FROM Tablequiz WHERE chapter_id = ?";
    $params1 = array($_SESSION['chapterId']);
    $stmt1 = sqlsrv_query($conn, $sql1, $params1);

?>
<?php include('../../components/navbaruserall.php') ?>
    <?php include('../../components/sidebaruserall.php') ?>
    <?php include('../../components/script.php') ?>
    <div class="main-container">
        <div class="pd-ltr-20 xs-pl-10 pt-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <span id="head">เเบบทดสอบเพื่อวัดความรู้</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 mb-30">
                        <div class="card-box pd-10 pt-10 height-100-p">
                            <div class="bar">
                                <div class="mainclass">
                                    <div class="contianer">
                                        <div class="back">
                                            <!-- <div class="navbar">
            <a href="admincoursetargetpage.php"><button class="goback"><i class='bx bx-chevron-left'></i></button></a>
            <div class="title">เเบบทดสอบเพื่อวัดความรู้</div>
        </div> -->
                                            <div class="header-name">
                                                <div class="headercourse">
                                                    <div class="namecourse">
                                                        หลักสูตรตามตำแหน่ง : <?php echo $_SESSION['courseName']; ?>
                                                    </div>
                                                    <div class="headertitle">
                                                        แบบทดสอบเพื่อวัดความรู้
                                                    </div>
                                                </div>
                                                <div class="timecon">
                                                    <div id="countdown-display" class="time">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- แสดงผลลัพธ์ของการนับถอยหลัง -->

                                        </div>
                                        <div class="quizform">
                                            <?php
                                            // เก็บคำถามในอาร์เรย์
                                            $questions = array();

                                            while ($row1 = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
                                                $questions[] = $row1;
                                            }

                                            // สลับลำดับของคำถาม
                                            shuffle($questions);

                                            // ตัวแปรสำหรับเก็บเลขข้อ
                                            $questionNumber = 1;

                                            // แสดงคำถามที่ถูกสุ่มลำดับ
                                            foreach ($questions as $question) {
                                            ?>

                                                <div class="quiz">
                                                    <?php echo $questionNumber; ?>: <?php echo $question['question'] ?>
                                                    <br>
                                                    <?php if ($question['choice_a'] !== NULL) : ?>
                                                        <input type="radio" name="question_<?php echo $question['quiz_id']; ?>" value="<?php echo $question['choice_a'] ?>" data-quiz-id="<?php echo $question['quiz_id']; ?>"><?php echo ' ' . $question['choice_a'] ?>
                                                        <br>
                                                    <?php endif; ?>
                                                    <?php if ($question['choice_b'] !== NULL) : ?>
                                                        <input type="radio" name="question_<?php echo $question['quiz_id']; ?>" value="<?php echo $question['choice_b'] ?>" data-quiz-id="<?php echo $question['quiz_id']; ?>"><?php echo ' ' . $question['choice_b'] ?>
                                                        <br>
                                                    <?php endif; ?>
                                                    <?php if ($question['choice_c'] !== NULL) : ?>
                                                        <input type="radio" name="question_<?php echo $question['quiz_id']; ?>" value="<?php echo $question['choice_c'] ?>" data-quiz-id="<?php echo $question['quiz_id']; ?>"><?php echo ' ' . $question['choice_c'] ?>
                                                        <br>
                                                    <?php endif; ?>
                                                    <?php if ($question['choice_d'] !== NULL) : ?>
                                                        <input type="radio" name="question_<?php echo $question['quiz_id']; ?>" value="<?php echo $question['choice_d'] ?>" data-quiz-id="<?php echo $question['quiz_id']; ?>"><?php echo ' ' . $question['choice_d'] ?>
                                                        <br>
                                                    <?php endif; ?>
                                                </div>

                                            <?php
                                                // เพิ่มเลขข้อทีละ 1
                                                $questionNumber++;
                                            }
                                            ?>
                                            <div class="submit"><button type="button" onclick="submitAnswers()" class="buttonsubmit">ส่งคำตอบ</button></div>
                                        </div>
                                        
                                        <script>
                                            function submitAnswers() {
                                                var allQuestionsAnswered = true;
                                                <?php
                                                foreach ($questions as $question) {
                                                    echo "if (!document.querySelector('input[name=\"question_{$question['quiz_id']}\"]:checked')) {
                    allQuestionsAnswered = false;
                }\n";
                                                }
                                                ?>

                                                if (!allQuestionsAnswered) {
                                                    // แสดง SweetAlert2 เมื่อยังไม่ได้ทำครบทุกคำถาม
                                                    Swal.fire({
                                                        title: 'คำเตือน',
                                                        text: 'กรุณาตอบให้ครบทุกข้อ',
                                                        icon: 'warning',
                                                        confirmButtonText: 'OK'
                                                    });
                                                    return;
                                                }
                                                // แสดง SweetAlert2 ก่อนส่งคำตอบ
                                                Swal.fire({
                                                    title: 'ยืนยันการส่งคำตอบ?',
                                                    text: 'คุณต้องการส่งคำตอบทั้งหมดหรือไม่?',
                                                    icon: 'question',
                                                    showCancelButton: true,
                                                    confirmButtonText: 'ส่งคำตอบ',
                                                    cancelButtonText: 'ยกเลิก'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        // ตรวจสอบคำตอบ
                                                        var answers = {};
                                                        <?php
                                                        foreach ($questions as $question) {
                                                            echo "answers[{$question['quiz_id']}] = document.querySelector('input[name=\"question_{$question['quiz_id']}\"]:checked')?.value || null;\n";
                                                        }
                                                        ?>

                                                        // ส่งคำตอบไปยังเซิร์ฟเวอร์ผ่าน AJAX
                                                        var xhr = new XMLHttpRequest();
                                                        xhr.open('POST', '../../backend/db_autoupdatescore.php', true);
                                                        xhr.setRequestHeader('Content-Type', 'application/json');
                                                        xhr.onreadystatechange = function() {
                                                            if (xhr.readyState === 4 && xhr.status === 200) {
                                                                var response = JSON.parse(xhr.responseText);
                                                                if (response.success) {
                                                                    // จัดการกับการตอบสนองที่ประสบความสำเร็จ (เช่น แสดงผลลัพธ์ให้ผู้ใช้เห็น)
                                                                    console.log(response.message);
                                                                    // แสดง SweetAlert2 อื่นๆ หรือทำสิ่งที่ต้องการ
                                                                    showSuccessAlert();
                                                                } else {
                                                                    // จัดการกับการตอบสนองข้อผิดพลาด
                                                                    console.error(response.message);
                                                                    // แสดง SweetAlert2 อื่นๆ หรือทำสิ่งที่ต้องการ
                                                                    showErrorAlert();
                                                                }
                                                            }
                                                        };
                                                        xhr.send(JSON.stringify(answers));
                                                    }
                                                });
                                            }

                                            // ฟังก์ชันแสดง SweetAlert2 เมื่อส่งคำตอบสำเร็จ
                                            function showSuccessAlert() {
                                                Swal.fire({
                                                    title: 'สำเร็จ!',
                                                    text: 'คำตอบถูกส่งเรียบร้อย',
                                                    icon: 'success',
                                                    confirmButtonText: 'OK'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        // ทำสิ่งที่ต้องการหลังจากกด OK
                                                        window.location.href = "userresults.php";
                                                    }
                                                });
                                            }

                                            // ฟังก์ชันแสดง SweetAlert2 เมื่อเกิดข้อผิดพลาด
                                            function showErrorAlert() {
                                                Swal.fire({
                                                    title: 'ข้อผิดพลาด!',
                                                    text: 'เกิดข้อผิดพลาดในการส่งคำตอบ',
                                                    icon: 'error',
                                                    confirmButtonText: 'OK'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        // ทำสิ่งที่ต้องการหลังจากกด OK
                                                        // ยกเลิกหรือลองส่งคำตอบอีกครั้ง
                                                    }
                                                });
                                            }
                                            
                                            jQuery(document).ready(function () {
                                            // โค้ด jQuery ที่ให้มาด้านบน
                                                // กำหนดเวลาถอยหลังที่ต้องการ (ในมิลลิวินาที)
                                            var countdownTime = 600000; // 1000 = 1วิ

                                                // แสดงผลลัพธ์ของการนับถอยหลัง
                                                var countdownDisplay = document.getElementById("countdown-display");

                                                // เริ่มนับถอยหลัง
                                                function startCountdown() {
                                                    var currentTime = countdownTime;

                                                    function updateDisplay() {
                                                        var minutes = Math.floor(currentTime / 60000);
                                                        var seconds = Math.floor((currentTime % 60000) / 1000);

                                                        countdownDisplay.innerHTML = '<i class="bi bi-clock"></i>: ' + minutes + ' นาที ' + seconds + ' วินาที';
                                                    }

                                                    function countdown() {
                                                        updateDisplay();
                                                        currentTime -= 1000;

                                                        if (currentTime >= 0) {
                                                            setTimeout(countdown, 1000);
                                                        } else {
                                                            // เวลาหมดแล้ว
                                                            countdownDisplay.textContent = "หมดเวลา!";
                                                            // เรียกใช้ SweetAlert2
                                                            showSummaryAlert();
                                                        }
                                                    }

                                                    countdown();
                                                }
                                            // เรียกใช้ฟังก์ชัน startCountdown เมื่อคำนวณเวลาถอยหลัง
                                            startCountdown();

                                            // ฟังก์ชันสำหรับคัดลอกข้อความไปยังคลิปบอร์ด
                                            $(".fa-hover").click(function (e) {
                                                e.preventDefault();
                                                CopyToClipboard($(this).find(".icon-copy").first().prop("outerHTML"), !0, "Copied");
                                            });

                                            // ฟังก์ชันสำหรับคัดลอกโค้ดไปยังคลิปบอร์ด
                                            new ClipboardJS(".code-copy").on("success", function (e) {
                                                CopyToClipboard("", !0, "Copied");
                                                e.clearSelection();
                                            });
                                            });

                                            // jQuery เรียกใช้หลังจากโหลดสมบูรณ์ทั้งหมด
                                            jQuery(window).on("load", function () {
                                            // โค้ด jQuery ที่ให้มาด้านล่าง
                                            });

                                            // แสดง SweetAlert2
                                            function showSummaryAlert() {
                                                Swal.fire({
                                                    title: "หมดเวลา!",
                                                    text: "ทำเเบบทดสอบใหม่หรือไม่?",
                                                    icon: "warning",
                                                    showCancelButton: true,
                                                    confirmButtonText: "Yes",
                                                    cancelButtonText: "No",
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        // ทำสิ่งที่ต้องการเมื่อผู้ใช้คลิก "Yes"
                                                        window.location.href = "usertakequizpage.php";
                                                    } else {
                                                        // ทำสิ่งที่ต้องการเมื่อผู้ใช้คลิก "No"
                                                        window.location.href = "usercoursetargetpage.php";
                                                    }
                                                });
                                            }

                                            // เริ่มการนับถอยหลังเมื่อหน้าเว็บโหลดเสร็จ
                                            window.onload = startCountdown;
                                        </script>

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