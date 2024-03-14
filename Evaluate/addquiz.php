<?php
session_start(); // เรียกใช้ session_start() ก่อนที่จะใช้ session

require_once('..\config\connection.php');

// ตรวจสอบว่ามี Session 'line_id' หรือไม่ และค่าของ 'line_id' ไม่เป็นค่าว่าง
if (
    isset($_SESSION['line_id'], $_SESSION['card_id'], $_SESSION['prefix_thai'], $_SESSION['firstname_thai'], $_SESSION['lastname_thai']) &&
    !empty($_SESSION['line_id']) && !empty($_SESSION['card_id']) && !empty($_SESSION['prefix_thai']) &&
    !empty($_SESSION['firstname_thai']) && !empty($_SESSION['lastname_thai'])
) {
    $line_id = $_SESSION['line_id'];
    $card_id = $_SESSION['card_id'];
    $prefix = $_SESSION['prefix_thai'];
    $fname = $_SESSION['firstname_thai'];
    $lname = $_SESSION['lastname_thai'];
    $costcenter = $_SESSION['cost_center_organization_id'];

    // ส่วนคำสั่ง SQL ควรตรงกับโครงสร้างของตารางในฐานข้อมูล
    $sql = "SELECT *,
        permission.name as permission, permission.permission_id as permissionID, contract_type.name_eng as contracts, contract_type.name_thai as contract_th,
        section.name_thai as section, department.name_thai as department 
        FROM employee
        INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id
        INNER JOIN section ON section.section_id = cost_center.section_id
        INNER JOIN department ON department.department_id = section.department_id
        INNER JOIN permission ON permission.permission_id = employee.permission_id
        INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id WHERE employee.card_id = ?";

    $params = array($card_id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $e_row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>addquiz</title>
    <link rel="icon" href="../favicon.ico" type="image/png">

    <!-- <link rel="stylesheet" href="css/index.css"> -->
    <!-- <link rel="stylesheet" href="css/Navbar.css"> -->
    <link rel="stylesheet" href="css/addquiz.css">

    <!-- Mobile Responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" type="text/css" href="../vendors/styles/core.css">
    <link rel="stylesheet" type="text/css" href="../vendors/styles/style.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/jquery-steps/jquery.steps.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/responsive.bootstrap4.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />


</head>

<body>
    <!-- Navbar start -->
    <?php include('../Evaluate/include/admin_navbar.php') ?>
    <?php include('../Evaluate/include/admin_sidebar.php') ?>
    <!-- Navbar end -->
    <div class="main-container">
        <!-- Main start -->
        <div class="pd-30">
            <div class="row">
                <div class="col-xl-3 col-lg-2 col-md-6 mb-10 title pb-20">
                    <h3 class="mb-0 "><i class="fa-solid fa-file-circle-plus fa-lg"></i> เพิ่มแบบประเมิน</h3>
                </div>
            </div>

            <div class="row pl-2">
                <div class="col-lg-5 col-md-6 col-sm-12 mb-30">
                    <div class="card-box pd-30 pt-10 height-50-p">
                        <form action="addquiz.php" method="POST">

                            <div class="form-group">
                                <span class="option o04"> ชื่อแบบประเมิน </span>
                                <input type="text" id="assessment" name="assessment" class="form-control" placeholder="ระบุชื่อแบบประเมิน">
                            </div>
                            <div class="form-group">
                                <span class="option o04"> เลือกประเภทพนักงาน </span>
                                <select id="dropdown1" name="dropdown1" class="form-control selectpicker">
                                    <option value="" disabled selected>เลือกประเภทพนักงาน</option>
                                    <?php
                                    // สร้าง options สำหรับ dropdown 3
                                    $sqlDropdown1 = "SELECT * FROM contract_type";
                                    $resultDropdown1 = sqlsrv_query($conn, $sqlDropdown1);
                                    if ($resultDropdown1) {
                                        while ($row = sqlsrv_fetch_array($resultDropdown1, SQLSRV_FETCH_ASSOC)) {
                                            echo "<option value='" . $row['contract_type_id'] .  "'>" . $row['name_thai'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <span class="option o04"> เลือกวันเริ่มแบบประเมิน </span>
                                <input type="date" id="selectedStartDate" name="selectedStartDate" class="form-control">
                            </div>
                            <div class="form-group">
                                <span class="option o04"> เลือกวันจบแบบประเมิน </span>
                                <input type="date" id="selectedEndDate" name="selectedEndDate" class="form-control">
                            </div>

                            <div id="questionContainer"></div>
                            <div class="text-center">
                                <button type="button" class="btn createdemp-btn" onclick="addQuestion()">เพิ่มคำถาม</button>
                            </div>
                            <div class="text-center pt-3">
                                <button type="button" onclick="window.location.href ='addmin_main.php'" class='delete-swal'>ยกเลิก</button>
                                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                <button id="submitButton" class='green-swal' type="submit" name="submit">
                                    ยืนยัน
                                </button>
                            </div>
                        </form>
                        <script>
                            function addQuestion() {
                                var container = document.getElementById('questionContainer');

                                // สร้างกล่องคำถาม
                                var questionInput = document.createElement('div');
                                questionInput.classList.add('questionContainer');


                                var questionBox = document.createElement('input');
                                questionBox.type = 'text';
                                questionBox.name = 'questions[]'; // เพื่อให้ PHP รับค่าเป็น Array
                                questionBox.placeholder = 'คำถาม';
                                questionBox.classList.add('quiz');
                                questionInput.appendChild(questionBox);

                                var deleteButton = document.createElement('button');
                                deleteButton.textContent = 'ลบคำถาม';
                                deleteButton.classList.add('delquiz');
                                deleteButton.onclick = function() {
                                    container.removeChild(questionInput);
                                };
                                questionInput.appendChild(deleteButton);

                                // เพิ่ม <br> สำหรับการขึ้นบรรทัดใหม่
                                container.appendChild(questionInput);
                                container.appendChild(document.createElement('br'));
                            }
                        </script>


                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            if (isset($_POST['assessment']) && isset($_POST['dropdown1']) && isset($_POST['selectedStartDate']) && isset($_POST['selectedEndDate']) && isset($_POST['questions'])) {
                                if ($conn !== false) {
                                    $assessment = $_POST['assessment'];
                                    $dropdown1 = $_POST['dropdown1'];
                                    $selectedStartDate = $_POST['selectedStartDate'];
                                    $selectedEndDate = $_POST['selectedEndDate'];
                                    $questions = $_POST['questions'];

                                    $sql = "INSERT INTO assessment (name, contract_type_id, date_start, date_end) VALUES (?, ?, ?, ?)";
                                    $params = array($assessment, $dropdown1, $selectedStartDate, $selectedEndDate);
                                    $stmt = sqlsrv_query($conn, $sql, $params);

                                    if ($stmt !== false) {
                                        $lastInsertedId = sqlsrv_fetch_array(sqlsrv_query($conn, "SELECT @@IDENTITY"));

                                        for ($i = 0; $i < count($questions); $i++) {
                                            $question = $questions[$i];
                                            $answer1 = '1';
                                            $answer2 = '2';
                                            $answer3 = '3';
                                            $answer4 = '4';

                                            $questionsSql = "INSERT INTO question (name, answer_1, answer_2, answer_3, answer_4,assessment_id) VALUES (?, ?, ?, ?, ?,?)";
                                            $questionsParams = array($question, $answer1, $answer2, $answer3, $answer4, $lastInsertedId[0]);
                                            $questionsStmt = sqlsrv_query($conn, $questionsSql, $questionsParams);

                                            if ($questionsStmt === false) {
                                                echo "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . print_r(sqlsrv_errors(), true);
                                            }
                                        }

                                        echo "<script>window.location.href = 'checkrole.php';</script>";
                                    } else {
                                        echo "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . print_r(sqlsrv_errors(), true);
                                    }
                                } else {
                                    echo "ไม่สามารถเชื่อมต่อกับฐานข้อมูลได้";
                                }
                            } else {
                                echo "<p class='error-message'>ข้อมูลไม่ครบถ้วน !!!</p>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main end -->
        <?php include('../admin/include/footer.php') ?>

    </div>
    <?php include('../admin/include/scripts.php') ?>

</body>

</html>