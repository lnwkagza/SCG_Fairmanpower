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
    // vbvb
    $permission_id = $_SESSION['permission_id'];
	if ($permission_id == 4) {

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
        $nboss = $row['firstname_thai'] . ' ' . $row['lastname_thai'];
        $_SESSION['nboss'] = $nboss;
        $manager_card_id = $row['manager_card_id'];
        $_SESSION['manager_card_id'] = $manager_card_id;
    } else {
        // หากไม่พบข้อมูลที่ตรงกัน
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
    <title>ReviewModule</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="icon" href="../favicon.ico" type="image/png">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/allmain.css">
    <link rel="stylesheet" href="css/Navbar.css">
    <link rel="stylesheet" href="css/bottomnav.css">
    <link rel="stylesheet" href="css/addreviewer.css">



    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> -->

    <link rel="stylesheet" type="text/css" href="../vendors/styles/core.css">
    <link rel="stylesheet" type="text/css" href="../vendors/styles/style.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/jquery-steps/jquery.steps.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/responsive.bootstrap4.min.css">

    <script src="js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

</head>

<body>
    <!-- Navbar start -->
    <?php include('../Evaluate/include/emp_navbar.php') ?>
    <?php include('../Evaluate/include/emp_sidebar.php') ?>
    <!-- Navbar end -->

    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-30">

            <div class="page-header">
                <div class="row">
                    <div class="col-xl-8 col-lg-2 col-md-7 mb-10 title ">
                        <h2 class="h3 mb-0 "><i class="fa-solid fa-users fa-lg "></i> กรุณาเลือกคนที่ท่านต้องการให้ประเมินท่าน</h2>
                    </div>
                    <div class="col-xl-4 col-lg-2 col-md-5 mb-10">
                        <div class="text-right">
                            <button onclick="window.location.href = 'addreviewer.php'" class='btn btn-primary'><i class="bi bi-plus"></i> เพิ่มคนประเมิน</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="main">
                <div class="pd-20 card-box">
                    <div class="section">
                        <span class="section">สถานะการประเมินของท่าน</span>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>ชื่อ</th>
                                <th>บทบาท</th>
                                <th>สถานะ</th>
                                <th>หัวหน้ายอมรับ</th>
                                <th>แก้ไข</th>
                                <th>เพิ่มเติม</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // เตรียมคำสั่ง SQL                    
                            $sql = "SELECT tr.detail,tr.tr_id, tr.review_to, tr.reviewer, tr.role, tr.status, tr.date, e.firstname_thai, e.lastname_thai, rs.status AS score_status
                    FROM transaction_review tr
                    INNER JOIN employee e ON tr.reviewer = e.card_id
                    JOIN review_score rs ON rs.tr_id = tr.tr_id
                    WHERE tr.review_to = ? 
                    ORDER BY
                     tr.date DESC 
                    OFFSET 0 ROWS FETCH NEXT 5 ROWS ONLY";

                            $params = array($card_id);

                            $stmt = sqlsrv_query($conn, $sql, $params);

                            // ตรวจสอบการทำงานของคำสั่ง SQL
                            if ($stmt === false) {
                                die(print_r(sqlsrv_errors(), true));
                            }

                            // แสดงผลลัพธ์ในรูปแบบของตาราง HTML
                            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>" . $row["firstname_thai"] . " " . $row["lastname_thai"] . "</td>";
                                echo "<td>" . $row["role"] . "</td>";
                                echo "<td>";
                                if (is_null($row["score_status"])) {
                                    echo '<div class="permission-2">รอดำเนินการ</div>';
                                } elseif ($row["score_status"] === "success") {
                                    echo '<div class="status-approve">เรียบร้อยแล้ว</div>';
                                }
                                echo "</td>";
                                echo "<td>";
                                if (is_null($row["status"])) {
                                    echo '<div class="status-peding"><i class="fa-regular fa-hourglass-half"></i> รอการอนุมัติ</div>';
                                } elseif ($row["status"] === "approve") {
                                    echo '<div class="status-approve"><i class="fa-solid fa-circle-check"></i> อนุมัติ</div>';
                                } elseif ($row["status"] === "reject") {
                                    echo '<div class="status-reject"><i class="fa-solid fa-circle-xmark"></i> ไม่อนุมัติ</div>';
                                }
                                echo "</td>";
                                if ($row["status"] === "reject") {
                                    echo "<td><button class='edit-btn' onclick=\"redirectToAssessment('" . urlencode($row["tr_id"]) . "','" . urlencode($row["review_to"]) . "','" . urlencode($row["role"]) . "')\"><span class='checkmark'>✎</span></button></td>";
                                } else {
                                    echo "<td><button class='edit-btn-disabled' disabled ><span class='checkmark'>✎</span></button></td>";
                                }
                                if ($row["detail"] !== null) {
                                    echo "<td><button class='edit-btn' onclick=\"showDetail('" . urlencode($row["detail"]) . "')\"><span class='checkmark'>?</span></button></td>";
                                } else {
                                    echo "<td><button class='edit-btn-disabled'disabled><span class='checkmark'>?</span></button></td>";
                                }
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <!-- start ส่วนของประเมินตนเอง -->
                <div class="btn-add">
                    <?php
                    $sqlme = "SELECT tr.review_to,tr.reviewer,tr.role,tr.tr_id,tr.status,e.firstname_thai,e.lastname_thai,e.contract_type_id FROM transaction_review tr
                            INNER JOIN employee e ON tr.review_to = e.card_id AND tr.review_to = tr.reviewer
                            INNER JOIN review_score rs ON rs.tr_id = tr.tr_id
                            WHERE tr.reviewer = ? AND rs.status IS NULL ";
                    $params = array($card_id);
                    // ดึงข้อมูลจากฐานข้อมูล
                    $stmtme = sqlsrv_query($conn, $sqlme, $params);

                    // ตรวจสอบการทำงานของคำสั่ง SQL
                    if ($stmtme === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }
                    while ($row = sqlsrv_fetch_array($stmtme, SQLSRV_FETCH_ASSOC)) {
                        echo "<h2 class='h3 mb-0 '><i class='fa-solid fa-user-check fa-lg'></i> กรุณาประเมินตัวเอง</h2>";
                        echo "<button class='btn-doassessment-me'   onclick=\"redirectTodoassessment('" . urlencode($row["tr_id"]) . "','" . urlencode($row["contract_type_id"]) . "')\"><span>ทำแบบทดสอบตัวเอง</span></button>";
                    }
                    ?>
                </div>
                <!-- end ส่วนประเมินตนเอง -->

            </div>
            <!-- end ส่วนตารางสถานะการประเมิน -->
            <!-- ตารางคนที่ขอให้ประเมิน -->
            <div class="main">
                <div class="pd-20 card-box">
                    <div class="section-review">
                        <h2 class='h3 mb-0 '><i class="fa-solid fa-triangle-exclamation fa-lg"></i> คนที่ขอให้ท่านประเมิน</h2>
                        <br>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>ชื่อ</th>
                                <th>บทบาท</th>
                                <th>เวลาดำเนินการ</th>
                                <th>ทำแบบทดสอบ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // เตรียมคำสั่ง SQL
                            $sql = "SELECT a.date_start,a.date_end,tr.review_to,tr.reviewer,tr.role,tr.tr_id,tr.status,e.firstname_thai,e.lastname_thai, rs.status, e.contract_type_id FROM transaction_review tr
                    INNER JOIN employee e ON tr.review_to = e.card_id AND tr.review_to != tr.reviewer
                    INNER JOIN assessment a ON a.contract_type_id = e.contract_type_id
                    INNER JOIN review_score rs ON rs.tr_id = tr.tr_id
                    WHERE tr.reviewer = ? AND tr.status = 'approve' AND rs.status IS NULL
                    ORDER BY a.date_start DESC
                    ";
                            $params = array($card_id);
                            // ดึงข้อมูลจากฐานข้อมูล
                            $stmt = sqlsrv_query($conn, $sql, $params);

                            // ตรวจสอบการทำงานของคำสั่ง SQL
                            if ($stmt === false) {
                                die(print_r(sqlsrv_errors(), true));
                            }

                            // แสดงผลลัพธ์ในรูปแบบของตาราง HTML
                            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                $time_start  = $row["date_start"]; // สร้างวัตถุ DateTime
                                $formattedDateStart = $time_start->format('d-m-Y');

                                $time_end  = $row["date_end"]; // สร้างวัตถุ DateTime
                                $formattedDateEnd =  $time_end->format('d-m-Y');

                                echo "<tr>";
                                echo "<td>" . $row["firstname_thai"] . " " . $row["lastname_thai"] . "</td>";
                                echo "<td>" . $row["role"] . "</td>";
                                echo "<td>" . $formattedDateStart . ' - ' . $formattedDateEnd .  "</td>";
                                echo "<td><button class='btn-doassessment' onclick=\"redirectTodoassessment('" . urlencode($row["tr_id"]) . "','" . urlencode($row["contract_type_id"]) . "')\"><span>ทำแบบทดสอบ</span></button></td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- main end -->
            <div class="btn-permiss">
                <button onclick="window.location.href = 'boss_main.php'" class='btn btn-secondary '>เปลี่ยนเป็นหัวหน้า</button>
                <button onclick="window.location.href = 'addmin_main.php'" class='btn btn-secondary'>เปลี่ยนเป็นแอดมิน</button>
            </div>
            <?php
            // ตรวจสอบว่า $row ถูกกำหนดค่าและมี $row["detail"] หรือไม่
            if (isset($row) && isset($row["detail"])) {
                $encodedDetail = urlencode($row["detail"]);
            } else {
                $encodedDetail = '';
            }
            ?>

            <script>
                var encodedData = <?php echo json_encode($encodedDetail); ?>;

                // ตรวจสอบว่า encodedData ไม่เป็น null หรือ undefined ก่อนที่จะใช้
                if (encodedData) {
                    showDetail(encodedData);
                } else {
                    // กรณีที่ encodedData เป็น null หรือ undefined
                    console.error('Error: encodedData is null or undefined');
                }
            </script>
            <!-- modal -->
            <div class="modal fade" id="editModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">เหตุผลที่ถูกปฏิเสธ</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <h1 id="detailIdInput">Loading...</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('../admin/include/scripts.php') ?>

</body>

</html>