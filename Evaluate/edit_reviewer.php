<?php
session_start(); // เรียกใช้ session_start() ก่อนที่จะใช้ session

require_once('..\config\connection.php');

// ตรวจสอบว่ามี Session 'line_id' หรือไม่ และค่าของ 'line_id' ไม่เป็นค่าว่าง
if (
    isset($_SESSION['line_id'], $_SESSION['card_id'], $_SESSION['prefix_thai'], $_SESSION['firstname_thai'], $_SESSION['lastname_thai']) &&
    !empty($_SESSION['line_id']) && !empty($_SESSION['card_id']) && !empty($_SESSION['prefix_thai']) &&
    !empty($_SESSION['firstname_thai']) && !empty($_SESSION['lastname_thai'])
) {
    $space = " ";
    $line_id = $_SESSION['line_id'];
    $card_id = $_SESSION['card_id'];
    $prefix = $_SESSION['prefix_thai'];
    $fname = $_SESSION['firstname_thai'];
    $lname = $_SESSION['lastname_thai'];
    $costcenter = $_SESSION['cost_center_organization_id'];
    $nboss = $_SESSION['nboss'];
    $manager_card_id = $_SESSION['manager_card_id'];

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
// เช็คว่ามีค่า review_to ที่ถูกส่งมาผ่าน URL หรือไม่
if (isset($_GET['id']) && isset($_GET['rt']) && isset($_GET['role'])) {
    // นำค่า firstname_thai ที่ถูกส่งมาจาก URL parameters ไปใช้งาน
    $tr_id = $_GET['id'];
    $_SESSION['id'] = $tr_id;
    $review_to = $_GET['rt'];
    $_SESSION['rt'] = $review_to;
    $role1 = $_GET['role'];
    $_SESSION['role'] = $role1;
    $roleFriend = 'เพื่อน';
    $roleUnder = 'ลูกน้อง';
    $roleCustomer = 'ลูกค้า';
    // แสดงค่า firstname_thai ที่ถูกส่งมา
} else {
    echo "ไม่พบค่า tr_id ที่ถูกส่งมา";
}


// $sql = "SELECT e.firstname_thai,e.lastname_thai,e.cost_center_organization_id,e.card_id
// FROM employee e 
// WHERE e.cost_center_organization_id = ?";
// $params = array($costcenter);
// $resultDropdown1 = sqlsrv_query($conn, $sqlDropdown1, $params);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>addreviewer</title>
    <link rel="icon" href="../favicon.ico" type="image/png">
    <!-- Mobile Responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">
    <link rel="stylesheet" href="css/addreviewer.css">

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

    <div class="main-container">


        <!-- main start -->
        <div class="pd-30">
            <div class="row">
                <div class="col-xl-6 col-lg-2 col-md-6 mb-10 title pb-20">
                    <h4 class="mb-0 "> เลือกคนที่ท่านต้องการให้ประเมิน</h4>
                </div>
            </div>
            <!-- select start -->
            <div class="row pl-2">
                <div class="col-lg-6 col-md-6 col-sm-12 mb-30">
                    <div class="card-box pd-30 pt-10 height-50-p">
                        <form method="POST" action="update_reviewer.php" id="update_reviewer">
                            <div class="form-group">

                                <h5 class="text-light-blue">ประเมินตนเอง</h5>
                                <select id="dropdown" disabled class="form-control">
                                    <option value="$manager_card_id"><?php echo $fname . $space . $lname ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <h5 class="text-light-blue">หัวหน้า</h5>
                                <select id="dropdown" disabled class="form-control">
                                    <option value="$manager_card_id"><?php echo $nboss ?></option>
                                </select>
                            </div>
                            <div class="form-group">

                                <?php
                                $role = "Peer";
                                $dropdown1Disabled = ($role1 !== $role) ? 'readonly' : '';
                                ?>
                                <h5 class="text-light-blue"> เลือกเพื่อน </h5>
                                <select id="dropdown1" name="dropdown1" <?php echo $dropdown1Disabled; ?> class="form-control selectpicker">
                                    <option value=""> เลือกเพื่อน</option>
                                    <?php
                                    if ($role === $role1) {
                                        // สร้าง options สำหรับ dropdown 1
                                        $sqlDropdown1 = "SELECT e.firstname_thai,e.lastname_thai,e.cost_center_organization_id,e.card_id
                                    FROM employee e 
                                    WHERE e.cost_center_organization_id = ?";
                                        $params = array($costcenter);
                                        $resultDropdown1 = sqlsrv_query($conn, $sqlDropdown1, $params);
                                        if ($resultDropdown1) {
                                            while ($row = sqlsrv_fetch_array($resultDropdown1, SQLSRV_FETCH_ASSOC)) {
                                                // เช็คว่าข้อมูลที่ดึงมาไม่ตรงกับค่าของ $fname ก่อนที่จะแสดงใน dropdown
                                                if ($row['firstname_thai'] !== $fname && $row['firstname_thai'] . ' ' . $row['lastname_thai'] !== $nboss) {
                                                    echo "<option value='" . $row['card_id'] . "'>" . $row['firstname_thai'] . ' ' . $row['lastname_thai']  . "</option>";
                                                }
                                            }
                                        }
                                    } elseif ($role !== $role1) {
                                        $sqlDropdown1 = "SELECT e.firstname_thai,e.lastname_thai,e.card_id,tr.role
                                        FROM employee e 
                                        JOIN transaction_review tr ON e.card_id = tr.reviewer
                                        WHERE tr.role = ?
                                        ORDER BY tr.date DESC
                    OFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY";
                                        $params = array($roleFriend);
                                        $resultDropdown1 = sqlsrv_query($conn, $sqlDropdown1, $params);
                                        if ($resultDropdown1) {
                                            while ($row = sqlsrv_fetch_array($resultDropdown1, SQLSRV_FETCH_ASSOC)) {
                                                // เช็คว่าข้อมูลที่ดึงมาไม่ตรงกับค่าของ $fname ก่อนที่จะแสดงใน dropdown
                                                if ($row['firstname_thai'] !== $fname && $row['firstname_thai'] . ' ' . $row['lastname_thai'] !== $nboss) {
                                                    echo "<option value='" . $row['card_id'] . "' selected>" . $row['firstname_thai'] . ' ' . $row['lastname_thai']  . "</option>";
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <h5 class="text-light-blue"> เลือกผู้ใต้บังคับบัญชา </h5>
                                <?php
                                $role2 = "Subordinate";
                                $dropdown2Disabled = ($role1 !== $role2) ? 'readonly' : '';
                                ?>
                                <select id="dropdown2" name="dropdown2" <?php echo $dropdown2Disabled; ?> class="form-control selectpicker">
                                    <option value="">เลือกผู้ใต้บังคับบัญชา</option>
                                    <?php
                                    if ($role2 === $role1) {

                                        // สร้าง options สำหรับ dropdown 2
                                        $sqlDropdown2 = "SELECT e.firstname_thai,e.lastname_thai,e.card_id
                                    FROM  employee e
                                    INNER JOIN manager mn ON e.card_id = mn.card_id
                                    WHERE mn.manager_card_id = ?";
                                        $params = array($card_id);
                                        $resultDropdown2 = sqlsrv_query($conn, $sqlDropdown2, $params);
                                        if ($resultDropdown2) {
                                            while ($row = sqlsrv_fetch_array($resultDropdown2, SQLSRV_FETCH_ASSOC)) {
                                                echo "<option value='"  . $row['card_id'] . "'>" . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . "</option>";
                                            }
                                        }
                                    } elseif ($role2 !== $role1) {
                                        $sqlDropdown2 = "SELECT e.firstname_thai,e.lastname_thai,e.card_id,tr.role
                                        FROM employee e 
                                        JOIN transaction_review tr ON e.card_id = tr.reviewer
                                        WHERE tr.role = ?
                                        ORDER BY tr.date DESC
                    OFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY";
                                        $params = array($roleUnder);
                                        $resultDropdown2 = sqlsrv_query($conn, $sqlDropdown2, $params);
                                        if ($resultDropdown2) {
                                            while ($row = sqlsrv_fetch_array($resultDropdown2, SQLSRV_FETCH_ASSOC)) {
                                                // เช็คว่าข้อมูลที่ดึงมาไม่ตรงกับค่าของ $fname ก่อนที่จะแสดงใน dropdown
                                                if ($row['firstname_thai'] !== $fname && $row['firstname_thai'] . ' ' . $row['lastname_thai'] !== $nboss) {
                                                    echo "<option value='" . $row['card_id'] . "' selected>" . $row['firstname_thai'] . ' ' . $row['lastname_thai']  . "</option>";
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">

                                <h5 class="text-light-blue"> เลือกลูกค้า </h5>
                                <?php
                                $role3 = "Customer";
                                $dropdown3Disabled = ($role1 !== $role3) ? 'readonly' : '';
                                ?>
                                <select id="dropdown3" name="dropdown3" required="ture" <?php echo $dropdown3Disabled; ?> class="form-control selectpicker">
                                    <option value="">เลือกลูกค้า</option>
                                    <?php
                                    if ($role3 === $role1) {
                                        // สร้าง options สำหรับ dropdown 3
                                        $sqlDropdown3 = "SELECT e.firstname_thai,e.lastname_thai,e.card_id FROM employee e";
                                        $resultDropdown3 = sqlsrv_query($conn, $sqlDropdown3);
                                        if ($resultDropdown3) {
                                            while ($row = sqlsrv_fetch_array($resultDropdown3, SQLSRV_FETCH_ASSOC)) {
                                                // เช็คว่าข้อมูลที่ดึงมาไม่ตรงกับค่าของ $fname ก่อนที่จะแสดงใน dropdown
                                                if ($row['firstname_thai'] !== $fname && $row['firstname_thai'] . ' ' . $row['lastname_thai'] !== $nboss) {
                                                    echo "<option value='" . $row['card_id'] .  "'>" . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . "</option>";
                                                }
                                            }
                                        }
                                    } elseif ($role3 !== $role1) {
                                        $sqlDropdown3 = "SELECT e.firstname_thai,e.lastname_thai,e.card_id,tr.role
                                        FROM employee e 
                                        JOIN transaction_review tr ON e.card_id = tr.reviewer
                                        WHERE tr.role = ?
                                        ORDER BY tr.date DESC
                                        OFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY";
                                        $params = array($roleCustomer);
                                        $resultDropdown3 = sqlsrv_query($conn, $sqlDropdown3, $params);
                                        if ($resultDropdown3) {
                                            while ($row = sqlsrv_fetch_array($resultDropdown3, SQLSRV_FETCH_ASSOC)) {
                                                // เช็คว่าข้อมูลที่ดึงมาไม่ตรงกับค่าของ $fname ก่อนที่จะแสดงใน dropdown
                                                if ($row['firstname_thai'] !== $fname && $row['firstname_thai'] . ' ' . $row['lastname_thai'] !== $nboss) {
                                                    echo "<option value='" . $row['card_id'] . "' selected>" . $row['firstname_thai'] . ' ' . $row['lastname_thai']  . "</option>";
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                                <!-- select end -->
                            </div>
                            <div class="btn-approve">
                                <button type="button" onclick="window.location.href ='checkrole.php'" class='btn-cancle'>ยกเลิก</button>
                                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                <button id="submitButton" class='btn-confirm' type="submit" name="submit">
                                    ยืนยัน
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- main end -->
    </div>
    <?php include('../admin/include/scripts.php') ?>

</body>

</html>