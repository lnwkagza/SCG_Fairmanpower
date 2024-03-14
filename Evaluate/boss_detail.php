<?php
session_start(); // เรียกใช้ session_start() ก่อนที่จะใช้ session

require_once('..\config\connection.php');

// ตรวจสอบว่ามี Session 'line_id' หรือไม่ และค่าของ 'line_id' ไม่เป็นค่าว่าง
if (
	isset($_SESSION['line_id'], $_SESSION['card_id'], $_SESSION['prefix_thai'], $_SESSION['firstname_thai'], $_SESSION['lastname_thai'], $_SESSION['permission_id']) &&
    !empty($_SESSION['line_id']) && !empty($_SESSION['card_id']) && !empty($_SESSION['prefix_thai']) &&
    !empty($_SESSION['firstname_thai']) && !empty($_SESSION['lastname_thai'])
) {
    $line_id = $_SESSION['line_id'];
    $card_id = $_SESSION['card_id'];
    $prefix = $_SESSION['prefix_thai'];
    $fname = $_SESSION['firstname_thai'];
    $lname = $_SESSION['lastname_thai'];
    $costcenter = $_SESSION['cost_center_organization_id'];
    $contract_type_id = $_SESSION['contract_type_id'];

    $permission_id = $_SESSION['permission_id'];
	if ($permission_id == 2) {

	}
	else {
		header('location: ../checkrole.php');
	}

    // ส่วนคำสั่ง SQL ควรตรงกับโครงสร้างของตารางในฐานข้อมูล
    $sql = "SELECT *
            FROM manager mn 
            INNER JOIN employee e ON mn.manager_card_id = e.card_id
            WHERE mn.card_id = ?";
    $params = array($card_id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if ($row) {
    } else {
        // หากไม่พบข้อมูลที่ตรงกัน
        echo "ไม่พบข้อมูลที่ตรงกับ line_id: $line_id";
    }

    // ส่วนคำสั่ง SQL ควรตรงกับโครงสร้างของตารางในฐานข้อมูล
    $e_sql = "SELECT *,
        permission.name as permission, permission.permission_id as permissionID, contract_type.name_eng as contracts, contract_type.name_thai as contract_th,
        section.name_thai as section, department.name_thai as department 
        
        FROM employee
        INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id
        INNER JOIN section ON section.section_id = cost_center.section_id
        INNER JOIN department ON department.department_id = section.department_id
        INNER JOIN permission ON permission.permission_id = employee.permission_id
        INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id WHERE employee.card_id = ?";

    $params = array($card_id);
    $stmt = sqlsrv_query($conn, $e_sql, $params);

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
    <title>detail</title>
    <link rel="icon" href="../favicon.ico" type="image/png">
    <!-- Mobile Responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <!-- <link rel="stylesheet" href="css/allmain.css"> -->
    <link rel="stylesheet" href="css/boss_detail.css">
        <link rel="stylesheet" href="css/index.css">


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <link rel="stylesheet" type="text/css" href="../vendors/styles/core.css">
    <link rel="stylesheet" type="text/css" href="../vendors/styles/style.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/jquery-steps/jquery.steps.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/responsive.bootstrap4.min.css">
</head>

<body>
    <!-- Navbar start -->
    <?php include('../Evaluate/include/head_navbar.php') ?>
    <?php include('../Evaluate/include/head_sidebar.php') ?>
    <!-- Navbar end -->

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-30">
            <div class="profile-tab  pt-10">
                <div class="tab height-50-p">
                    <div class="nav nav-tabs customtab" role="tablist">
                        <a class="nav-link" href='boss_main.php'><img src="img/review.png" width="40" height="40"></a>
                        <a class="nav-link" href='boss_approve.php'><img src="img/approve.png" width="40" height="40"></a>
                        <a class="nav-link active" href='boss_detail.php'><img src="img/emp_profile.png" width="40" height="40"></a>
                    </div>
                </div>
            </div>
            <div class="pd-20 card-box">

                <!-- section start -->
                <div class="search_allapprove">
                    <form action="" method="GET">
                        <div class="searchbar">
                            <div class="inputsearch">
                                <div class="section">
                                    <h3 class="text-primary">รายละเอียดผู้ใต้บังคับบัญชา</h3>
                                    <input class="input1" type="text" name="search" placeholder="Search...">
                                    <button class="btn-search"><img src="img/search.png" class="img-search"></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- section end -->
                <!-- main start -->
                <div class="main pt-20">
                    <table>
                        <thead>
                            <tr>
                                <th>ชื่อ</th>
                                <th>นามสกุล</th>
                                <th>ประเภท</th>
                                <th>การประเมิน</th>
                                <th>สถานะ</th>
                                <th>บทบาท</th>
                                <th>คะแนน</th>
                                <th>AVG</th>

                                <!-- <th>Manager</th>
                        <th>Peer1</th>
                        <th>Peer2</th>
                        <th>Subordinate</th>
                        <th>Customer</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $search_query = isset($_GET['search']) ? $_GET['search'] : '';
                            $sql = "SELECT tr.review_to, 
                                    tr.tr_id,
                                    e.prefix_thai, 
                                    e.firstname_thai, 
                                    e.lastname_thai,
                                    ct.name_thai, 
                                    a.name, 
                                    rs.score,
                                    rs.status,
                                    tr.role,
                                    rs.assessment_id
                            FROM employee e
                            JOIN transaction_review tr ON tr.review_to = e.card_id
                            JOIN employee AS er ON tr.reviewer = er.card_id
                            JOIN manager mn ON e.card_id = mn.card_id
                            JOIN cost_center c ON e.cost_center_organization_id = c.cost_center_id
                            JOIN section s ON c.section_id = s.section_id
                            JOIN contract_type ct ON ct.contract_type_id = e.contract_type_id
                            JOIN review_score rs ON rs.tr_id = tr.tr_id							
                            JOIN assessment a ON a.contract_type_id = ct.contract_type_id AND a.assessment_id = rs.assessment_id
                            WHERE mn.manager_card_id = ? AND (a.name LIKE '%$search_query%' OR e.firstname_thai LIKE '%$search_query%' 
                                    OR e.lastname_thai LIKE '%$search_query%' OR tr.role LIKE '%$search_query%')
                            GROUP BY tr.review_to, tr.tr_id, e.prefix_thai, e.firstname_thai, e.lastname_thai,
                             ct.name_thai, a.name ,tr.role,rs.score,rs.status,rs.assessment_id";


                            $params = array($card_id);

                            $stmt = sqlsrv_query($conn, $sql, $params);

                            // ตรวจสอบการทำงานของคำสั่ง SQL
                            if ($stmt === false) {
                                die(print_r(sqlsrv_errors(), true));
                            }

                            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

                                $assessment_id = $row["assessment_id"];
                                $review_to = $row["review_to"];
                                $score = $row["score"];
                                // $displayscore = $row["score"];
                                // $fullname = $row["firstname_thai"] . ' ' . $row["lastname_thai"];
                                // $contract_type = $row["name_thai"];
                                // $assessment_name = $row["name"];
                                // $status = $row["status"];
                                // $role = $row["role"];

                                if (!isset($scoreTotals[$assessment_id][$review_to])) {
                                    $scoreTotals[$assessment_id][$review_to] = 0; // ถ้ายังไม่มีให้กำหนดค่าเริ่มต้นเป็น 0
                                }

                                // เพิ่มค่า score เข้าไปใน total
                                $scoreTotals[$assessment_id][$review_to] += $score;
                            }

                            $totalsArray = array();

                            foreach ($scoreTotals as $assessment_id => $totals) {
                                foreach ($totals as $review_to => $totalScore) {
                                    // echo "ass : $assessment_id review $review_to score $totalScore";
                                    // เพิ่มค่าลงใน array
                                    $totalsArray[] = array(
                                        'assessment_id' => $assessment_id,
                                        'review_to' => $review_to,
                                        'total_score' => $totalScore
                                    );
                                }
                            }

                            $search_query = isset($_GET['search']) ? $_GET['search'] : '';
                            $sqls = "SELECT tr.review_to, 
                                            tr.tr_id,
                                            e.prefix_thai, 
                                            e.firstname_thai, 
                                            e.lastname_thai,
                                            ct.name_thai, 
                                            a.name, 
                                            rs.score,
                                            rs.status,
                                            tr.role,
                                            rs.assessment_id
                                    FROM employee e
                                    JOIN transaction_review tr ON tr.review_to = e.card_id
                                    JOIN employee AS er ON tr.reviewer = er.card_id
                                    JOIN manager mn ON e.card_id = mn.card_id
                                    JOIN cost_center c ON e.cost_center_organization_id = c.cost_center_id
                                    JOIN section s ON c.section_id = s.section_id
                                    JOIN contract_type ct ON ct.contract_type_id = e.contract_type_id
                                    JOIN review_score rs ON rs.tr_id = tr.tr_id
                                    JOIN assessment a ON a.contract_type_id = ct.contract_type_id AND a.assessment_id = rs.assessment_id
                                    WHERE mn.manager_card_id = ? AND (a.name LIKE '%$search_query%' OR e.firstname_thai LIKE '%$search_query%' 
                                            OR e.lastname_thai LIKE '%$search_query%' OR tr.role LIKE '%$search_query%')
                                    GROUP BY tr.review_to, tr.tr_id, e.prefix_thai, e.firstname_thai, e.lastname_thai,
                                    ct.name_thai, a.name ,tr.role,rs.score,rs.status,rs.assessment_id";

                            $paramss = array($card_id);

                            $stmts = sqlsrv_query($conn, $sqls, $paramss);

                            // ตรวจสอบการทำงานของคำสั่ง SQL
                            if ($stmts === false) {
                                die(print_r(sqlsrv_errors(), true));
                            }

                            // ลูป while นอก foreach
                            while ($rows = sqlsrv_fetch_array($stmts, SQLSRV_FETCH_ASSOC)) {

                                echo "<tr>";
                                echo "<td>" . $rows["firstname_thai"] . "</td>";
                                echo "<td>" . $rows["lastname_thai"] .  "</td>";
                                echo "<td>" . $rows["name_thai"] .  "</td>";
                                echo "<td>" . $rows["name"] .  "</td>";
                                echo "<td>";
                                if ($rows["status"] == 'success') {
                                    echo "เสร็จสิ้น";
                                } else {
                                    echo "ยังไม่ดำเนินการ";
                                }
                                echo "<td>" . $rows["role"] .  "</td>";
                                echo "<td>" . $rows["score"] .  "</td>";
                                echo "</td>";

                                $foundTotalScore = null;
                                foreach ($totalsArray as $data) {
                                    if ($data['assessment_id'] == $rows['assessment_id'] && $data['review_to'] == $rows['review_to']) {
                                        $foundTotalScore = $data['total_score'];
                                        break;
                                    }
                                }
                                if ($foundTotalScore !== NULL) {
                                    echo "<td>" . $foundTotalScore . "</td>";

                                } else {
                                    echo "<td>" . 'ไม่มีคะแนน' . "</td>";
                                }
                                echo "</tr>";
                            }

                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="space"></div>
            </div>
        </div>
        <!-- end ส่วนตารางสถานะการประเมิน -->
        <!-- bottom Nav start -->
        <script src="js/script.js"></script>
        <!-- bottom Nav end -->
    </div>
    <?php include('../admin/include/scripts.php') ?>

</body>

</html>