<?php
session_start(); // เรียกใช้ session_start() ก่อนที่จะใช้ session

require_once('..\config\connection.php');

// ตรวจสอบว่ามี Session 'line_id' หรือไม่ และค่าของ 'line_id' ไม่เป็นค่าว่าง
if (
    isset($_SESSION['card_id'], $_SESSION['prefix_thai'], $_SESSION['firstname_thai'], $_SESSION['lastname_thai']) &&
    !empty($_SESSION['line_id']) && !empty($_SESSION['card_id']) && !empty($_SESSION['prefix_thai']) &&
    !empty($_SESSION['firstname_thai']) && !empty($_SESSION['lastname_thai'])
) {
    $card_id = $_SESSION['card_id'];
    $prefix = $_SESSION['prefix_thai'];
    $fname = $_SESSION['firstname_thai'];
    $lname = $_SESSION['lastname_thai'];

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

if (isset($_GET['id'])) {
    $tr_id = $_GET['id'];
    $_SESSION['id'] = $tr_id;
    $emp_id = $_GET['emp'];
    $_SESSION['emp'] = $emp_id;
} else {
    echo "$tr_id";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>doassessment</title>
    <link rel="icon" href="../favicon.ico" type="image/png">
    <!-- Mobile Responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/do_assessment.css">

    <link rel="stylesheet" type="text/css" href="../vendors/styles/core.css">
    <link rel="stylesheet" type="text/css" href="../vendors/styles/style.css">

    <link rel="stylesheet" type="text/css" href="../src/plugins/jquery-steps/jquery.steps.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/responsive.bootstrap4.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />


</head>

<body>
    <!-- Navbar start -->
    <?php include('../Evaluate/include/emp_navbar.php') ?>
    <?php include('../Evaluate/include/emp_sidebar.php') ?>
    <!-- Navbar end -->

    <div class="body">
        <div class="main-container">

            <div class="pd-10">

                <!-- Main start -->
                <div class="row pl-2">
                    <div class="col-lg-12 col-md-6 col-sm-6 mb-30">
                        <div class="card-box pd-20 height-50-p">
                            <form action="insert_do_assessment.php" method="post">
                                <table class="data-table table stripe hover">
                                    <thead>
                                        <tr>
                                            <th >คำถาม</th>
                                            <th>1 <br> ไม่ค่อยแสดงออก</th>
                                            <th >2 <br> แสดงออกในบางครั้ง</th>
                                            <th >3 <br> แสดงออกอย่างต่อเนื่อง</th>
                                            <th >4 <br> แสดงออกตลอดเวลา</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT a.assessment_id,q.question_id,q.answer_1,q.answer_2,q.answer_3,q.answer_4,a.contract_type_id,a.date_start,a.date_end ,q.name AS q_name, a.name AS a_name  FROM question q , assessment a 
                    WHERE q.assessment_id = a.assessment_id
                    AND date_start = (SELECT MAX(date_start) FROM assessment a WHERE a.contract_type_id = ?)";
                                        $params = array($emp_id);
                                        $result = sqlsrv_query($conn, $sql, $params);
                                        if ($result === false) {
                                            die(print_r(sqlsrv_errors(), true));
                                        }
                                        $firstAssessment = true;
                                        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                                            if ($firstAssessment) {
                                                $assessment_id = $row['assessment_id'];
                                                $_SESSION['assessment_id'] = $assessment_id;
                                                // ทำงานเฉพาะครั้งแรกที่ได้ค่า assessment
                                                echo '<div class="section"><span class="section">' . $row["a_name"] . '</span></div>';
                                                echo "<br>";
                                                $firstAssessment = false; // เปลี่ยนค่าเป็น false เพื่อไม่ให้เข้าเงื่อนไขนี้อีก
                                            }
                                            echo '<tr>';
                                            echo '<td class="t-small">' . $row["q_name"] . '</td>';
                                            echo '<td><input class="radio" type="radio" name="answer' . $row["question_id"] . '" value="25">' . '</td>';
                                            echo '<td><input class="radio" type="radio" name="answer' . $row["question_id"] . '" value="50">' . '</td>';
                                            echo '<td><input class="radio" type="radio" name="answer' . $row["question_id"] . '" value="75">' . '</td>';
                                            echo '<td><input class="radio" type="radio" name="answer' . $row["question_id"] . '" value="100">' . '</td>';
                                            echo '</tr>';
                                        }

                                        ?>
                                    </tbody>
                                </table>
                                <div class="btn-submit">
                                    <input type="submit" class="btn-doassessment" value="ยืนยัน" onclick="showSweetAlert()">
                                </div>

                            </form>
                            <script>
                                function showSweetAlert() {
                                    alert('ทำแบบประเมินเสร็จสิ้น');
                                }
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Main end -->
    </div>
    <?php include('../admin/include/scripts.php') ?>

</body>

</html>