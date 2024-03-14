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
$lesson_id = $_SESSION['last_viewed_lesson'];
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
    echo $user_id;
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

    $sql2 = "SELECT * FROM Tablequiz WHERE chapter_id = ?";
    $params2 = array($lesson_id);
    $stmt2 = sqlsrv_query($conn, $sql2, $params2);
    if ($stmt2 === false) {
        die(print_r(sqlsrv_errors(), true));
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
                                <span id="head">แก้ไขแบบประเมิน</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                        <div class="bar">
                            <div class="mainclass">

                                <div class="formquizcon">
                                    <div class="formquiz">
                                        <form action="../../backend/db_editquiz.php" method="POST" name="formData">
                                            <input type="hidden" id="lesson_id" value="<?php echo $_SESSION['last_viewed_lesson']; ?>">
                                            <?php
                                            while ($row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
                                            ?>
                                                <input type="hidden" name="quiz_id[]" id="quiz_id" value="<?php echo ($row2['quiz_id']); ?>">
                                                <div class="groupquestions">
                                                    <div class="question">
                                                        <label for="question">คำถามข้อที่</label>
                                                        <input class="old" type="text" name="question[]" id="question" value="<?php echo ($row2['question']); ?>">
                                                    </div>

                                                    <div class="options">
                                                        <label for="choice_a">ตัวเลือกA</label>
                                                        <input class="old" type="text" name="choicea[]" id="choice_a" value="<?php echo ($row2['choice_a']); ?>">
                                                        <br>
                                                        <label for="choice_b">ตัวเลือกB</label>
                                                        <input class="old" type="text" name="choiceb[]" id="choice_b" value="<?php echo ($row2['choice_b']); ?>">
                                                        <br>
                                                        <label for="choice_c">ตัวเลือกC</label>
                                                        <input class="old" type="text" name="choicec[]" id="choice_c" value="<?php echo ($row2['choice_c']); ?>">
                                                        <br>
                                                        <label for="choice_d">ตัวเลือกD</label>
                                                        <input class="old" type="text" name="choiced[]" id="choice_d" value="<?php echo ($row2['choice_d']); ?>">
                                                    </div>

                                                    <div class="answer">
                                                        <label for="answer">เลือกคำตอบที่ถูกต้อง</label>
                                                        <select name="answer[]" id="answer" required>
                                                            <option value="choicea" <?php if ($row2['choice_a'] === $row2['answer']) echo 'selected'; ?>>ตัวเลือกA</option>
                                                            <option value="choiceb" <?php if ($row2['choice_b'] === $row2['answer']) echo 'selected'; ?>>ตัวเลือกB</option>
                                                            <option value="choicec" <?php if ($row2['choice_c'] === $row2['answer']) echo 'selected'; ?>>ตัวเลือกC</option>
                                                            <option value="choiced" <?php if ($row2['choice_d'] === $row2['answer']) echo 'selected'; ?>>ตัวเลือกD</option>
                                                        </select>
                                                    </div>
                                                    <div class="delform">
                                                        <div class="delformadd1">
                                                            <?php
                                                            echo "<a href='../../backend/db_delquiz.php?quiz_id={$row2['quiz_id']}' onclick='return confirm(\"คุณต้องการลบข้อมูลนี้ใช่หรือไม่?\")'><i class='bx bxs-trash'></i></a>";
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                            <div class="buttonsave">
                                                <button type="submit">บันทึก</button>
                                            </div>
                                        </form>


                                    </div>

                                    <div class="buttonadddel">
                                        <button onclick="deleteLastQuestion()" type="delete" name="delete"><i class='bx bxs-trash'></i></button>
                                        <button onclick="addQuestion()" type="add" name="add"><i class='bx bxs-add-to-queue'></i></button>
                                    </div>
                                </div>
                                <!-- <button onclick="goBack()" class="backbutton"><i class='bx bx-left-arrow-alt'></i></button> -->

                                <script>
                                    // เพิ่มฟอร์ม groupquestions
                                    function addBlankQuestionForm() {
                                        // สร้าง div สำหรับฟอร์มคำถามใหม่
                                        var newGroupQuestions = document.createElement('div');
                                        newGroupQuestions.classList.add('groupquestions');

                                        // สร้าง HTML สำหรับแบบฟอร์มคำถามว่างๆ
                                        newGroupQuestions.innerHTML = `
    <input type="hidden" name = "quiz_id[]"id="quiz_id" value="0">
        <div class="question">
            <label for="question">คำถามข้อที่</label>
            <input type="text" name="question[]" id="question" autocomplete="off"> 
        </div>
        <div class="options">
            <label for="choice_a">ตัวเลือกA</label>
            <input type="text" name="choicea[]" id="choice_a" autocomplete="off">
            <br>
            <label for="choice_b">ตัวเลือกB</label>
            <input type="text" name="choiceb[]" id="choice_b" autocomplete="off">
            <br>
            <label for="choice_c">ตัวเลือกC</label>
            <input type="text" name="choicec[]" id="choice_c" autocomplete="off">
            <br>
            <label for="choice_d">ตัวเลือกD</label>
            <input type="text" name="choiced[]" id="choice_d" autocomplete="off">
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
        
        
    `;

                                        // เพิ่มแบบฟอร์มคำถามใหม่เข้าไปในฟอร์มคำถาม
                                        var quizForm = document.querySelector('.formquiz form');
                                        quizForm.appendChild(newGroupQuestions);
                                    }

                                    function addQuestion() {
                                        // เรียกใช้ฟังก์ชันเพื่อเพิ่มแบบฟอร์มคำถามว่าง
                                        addBlankQuestionForm();
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


                                    // function goBack() {
                                    //     window.history.back();
                                    // }
                                </script>


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