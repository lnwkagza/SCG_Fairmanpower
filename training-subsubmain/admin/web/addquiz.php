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

if (isset($_GET['chapter_id'])) {
    $chapter_id = $_GET['chapter_id'];
    $_SESSION['last_viewed_lesson'] = $chapter_id;
    // echo $_SESSION['last_viewed_lesson'];
}
?>
<?php include('../../components/head.php') ?>
<link rel="stylesheet" href="../../plugin/bootstrap-icons-1.11.2/font/bootstrap-icons.css">
<link rel="stylesheet" href="../../plugin/boxicons-2.1.4/css/boxicons.min.css">
<link rel="stylesheet" href="../css/addquiz.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300&display=swap" rel="stylesheet">
<title>AdminPage</title>

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



    $sql1 = "SELECT *, Tablecourse.course_id AS course_code FROM Tablecourse 
        LEFT JOIN Tablechapter ON Tablecourse.course_id = Tablechapter.course_id
		LEFT JOIN Tablequiz ON Tablechapter.chapter_id = Tablequiz.chapter_id";

    if (isset($_GET['search_course'])) {
        $search_term = $_GET['search_course'];
        $sql1 .= " WHERE Tablecourse.course_name LIKE ?";
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

    $lesson_id = $_SESSION['last_viewed_lesson'];
    ?>
    <!-- <div class="contianer">

        <div class="title">
            <div class="titlename">เพิ่มเเบบประเมิน</div>
        </div> -->
    <?php include('../../components/navbar.php') ?>
    <?php include('../../components/sidebar.php') ?>
    <div class="main-container">
        <div class="pd-ltr-20 xs-pl-10 pt-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <span id="head">เพิ่มเเบบประเมิน</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                        <!-- <div class="card-box pd-10 pt-10 height-100-p"> -->
                        <div class="bar">
                            <div class="mainclass">
                                <div class="formquizcon">
                                    <div class="formquiz">
                                        <form action="../../backend/db_addquiz(ins).php" method="POST" name="formData">
                                            <input type="hidden" id="lesson_id" value="<?php echo $_SESSION['last_viewed_lesson'];?>">
                                            <div class="groupquestions">
                                                <div class="question">
                                                    <label for="question">คำถาม</label>
                                                    <input type="text" name="question[]" id="question" required autocomplete="off">
                                                </div>

                                                <div class="options">
                                                    <label for="choice_a">ตัวเลือกA</label>
                                                    <input type="text" name="choicea[]" id="choice_a" required autocomplete="off">
                                                    <br>
                                                    <label for="choice_b">ตัวเลือกB</label>
                                                    <input type="text" name="choiceb[]" id="choice_b"  autocomplete="off">
                                                    <br>
                                                    <label for="choice_c">ตัวเลือกC</label>
                                                    <input type="text" name="choicec[]" id="choice_c"  autocomplete="off">
                                                    <br>
                                                    <label for="choice_d">ตัวเลือกD</label>
                                                    <input type="text" name="choiced[]" id="choice_d"  autocomplete="off">
                                                </div>

                                                <div class="answer">
                                                    <label for="answer">เลือกคำตอบที่ถูกต้อง</label>
                                                    <select name="answer[]" id="answer" required>
                                                        <option value="choicea">ตัวเลือกA</option>
                                                        <option value="choiceb">ตัวเลือกB</option>
                                                        <option value="choicec">ตัวเลือกC</option>
                                                        <option value="choiced">ตัวเลือกD</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="buttonsave">
                                                <button type="submit">บันทึก</button>
                                            </div>
                                        </form>


                                    </div>

                                    <div class="buttonadddel">
                                        <div class="delformadd"><button onclick="deleteLastQuestion()" type="delete" name="delete"><i class='bx bxs-trash'></i></button></div>
                                        <button onclick="addQuestion()" type="add" name="add"><i class='bx bxs-add-to-queue'></i></button>
                                    </div>
                                </div>

                                <script>
                                    // เพิ่มฟอร์ม groupquestions
                                    function addQuestion() {
                                        // สร้าง div ใหม่สำหรับ groupquestions
                                        var newGroupQuestions = document.createElement('div');
                                        newGroupQuestions.classList.add('groupquestions');

                                        // คัดลอก HTML ของฟอร์ม groupquestions เข้าไปใน div ใหม่
                                        var originalGroupQuestions = document.querySelector('.formquiz .groupquestions');
                                        newGroupQuestions.innerHTML = originalGroupQuestions.innerHTML;

                                        // เพิ่มฟอร์ม groupquestions ใหม่ลงใน form ที่มี ID เท่ากับ "quizForm"
                                        var quizForm = document.querySelector('.formquiz form');
                                        quizForm.appendChild(newGroupQuestions);
                                    }

                                    // ลบฟอร์ม groupquestions ล่าสุด
                                    function deleteLastQuestion() {
                                        var quizForm = document.querySelector('.formquiz form');
                                        var groupQuestions = quizForm.querySelectorAll('.groupquestions');

                                        // ถ้ามีฟอร์มมากกว่า 1 ให้ลบฟอร์มล่าสุด
                                        if (groupQuestions.length > 1) {
                                            var lastGroupQuestion = groupQuestions[groupQuestions.length - 1];
                                            quizForm.removeChild(lastGroupQuestion);
                                        }
                                    }
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
    <?php include('../../components/script.php') ?>
</body>

</html>