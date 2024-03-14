<?php
session_start(); // เรียกใช้ session_start() ก่อนที่จะใช้ session

require_once('..\config\connection.php');

// gitupodate

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
    $contract_type_id = $_SESSION['contract_type_id'];
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

    <div class="mobile-menu-overlay"></div>
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
                        <form method="POST" action="addreviewer.php" id="addreviewer">
                            <div class="form-group">
                                <h5 class="text-light-blue">ประเมินตนเอง</h5>
                                <select id="dropdown4" disabled class="form-control">
                                    <option value="$card_id">
                                        <?php $space = ' ';
                                        echo $fname . $space . $lname  ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <h5 class="text-light-blue">หัวหน้า</h5>
                                <select id="dropdown" disabled class="form-control">
                                    <option value="$manager_card_id"><?php echo $nboss ?></option>
                                </select>
                            </div>
                            <div class="form-group">

                                <h5 class="text-light-blue"> เลือกเพื่อน </h5>
                                <select id="dropdown1" name="dropdown1" data-live-search="true" class="form-control selectpicker">
                                    <option value="" disabled selected>เลือกเพื่อน</option>
                                    <?php
                                    // สร้าง options สำหรับ dropdown 1
                                    $sqlDropdown1 = "SELECT e.firstname_thai,e.lastname_thai,e.cost_center_organization_id, e.card_id
                                    FROM employee e 
                                    WHERE e.cost_center_organization_id = ?";
                                    $params = array($e_row['cost_center_organization_id']);
                                    $resultDropdown1 = sqlsrv_query($conn, $sqlDropdown1, $params);
                                    if ($resultDropdown1) {
                                        while ($row = sqlsrv_fetch_array($resultDropdown1, SQLSRV_FETCH_ASSOC)) {
                                            // เช็คว่าข้อมูลที่ดึงมาไม่ตรงกับค่าของ $fname ก่อนที่จะแสดงใน dropdown
                                            if ($row['firstname_thai'] !== $fname && $row['firstname_thai'] . ' ' . $row['lastname_thai'] !== $nboss) {
                                                echo "<option value='" . $row['card_id'] . "'>" . $row['firstname_thai'] . ' ' . $row['lastname_thai']  . "</option>";
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php
                            // สร้าง options สำหรับ dropdown 2
                            $sqlDropdown2 = "SELECT e.firstname_thai,e.lastname_thai,e.card_id
                                    FROM  employee e
                                    INNER JOIN manager mn ON e.card_id = mn.card_id
                                    WHERE mn.manager_card_id = ?";
                            $params = array($card_id);
                            $resultDropdown2 = sqlsrv_query($conn, $sqlDropdown2, $params);

                            // ตรวจสอบว่ามีแถวข้อมูลหรือไม่
                            if ($resultDropdown2 && sqlsrv_has_rows($resultDropdown2)) {
                                echo '<h5 class="text-light-blue"> เลือกผู้ใต้บังคับบัญชา </h5>';
                                echo '<select id="dropdown2" name="dropdown2" data-live-search="true" class="form-control selectpicker">';
                                echo '<option value="" disabled selected>เลือกผู้ใต้บังคับบัญชา</option>';
                                while ($row = sqlsrv_fetch_array($resultDropdown2, SQLSRV_FETCH_ASSOC)) {
                                    $under = 'ลูกน้อง';
                                    $combinedValue = $row['card_id'] . '|' . $under;
                                    echo "<option value='"  . htmlspecialchars($combinedValue) .  "'>" . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . "</option>";
                                }
                                echo '</select>';
                            } else {
                                echo '<h5 class="text-light-blue"> เลือกเพื่อน </h5>';
                                echo '<select id="dropdown2" name="dropdown2" data-live-search="true" class="form-control selectpicker">';
                                echo '<option value="" disabled selected>เลือกเพื่อน</option>';
                                // สร้าง options สำหรับ dropdown 1
                                $sqlDropdown2 = "SELECT e.firstname_thai,e.lastname_thai,e.cost_center_organization_id,e.card_id
                                        FROM employee e 
                                        WHERE e.cost_center_organization_id = ?";
                                $params = array($e_row['cost_center_organization_id']);
                                $resultDropdown2 = sqlsrv_query($conn, $sqlDropdown2, $params);
                                if ($resultDropdown2) {
                                    while ($row = sqlsrv_fetch_array($resultDropdown2, SQLSRV_FETCH_ASSOC)) {
                                        // เช็คว่าข้อมูลที่ดึงมาไม่ตรงกับค่าของ $fname ก่อนที่จะแสดงใน dropdown
                                        if ($row['firstname_thai'] !== $fname && $row['firstname_thai'] . ' ' . $row['lastname_thai'] !== $nboss) {
                                            $under = 'Peer';
                                            $combinedValue = $row['card_id'] . '|' . $under;
                                            echo "<option value='" . htmlspecialchars($combinedValue) .  "'>" . $row['firstname_thai'] . ' ' . $row['lastname_thai']  . "</option>";
                                        }
                                    }
                                }
                                echo '</select><br>';
                            }
                            ?>
                            <br>
                            <div class="form-group">
                                <div class="serach_customer">
                                    <h5 class="text-light-blue"> เลือกลูกค้า </h5>
                                    <select id="dropdown3" name="dropdown3" required="ture" data-live-search="true" class="form-control selectpicker">
                                        <option value="" disabled selected>เลือกลูกค้า</option>
                                        <?php
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
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <!-- select end -->

                            <div class="btn-approve">
                                <button type="button" onclick="window.location.href ='checkrole.php'" class='btn-cancle'>ยกเลิก</button>
                                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                <button id="submitButton" class='btn-confirm' type="submit" name="submit" disabled onclick="showSweetAlert(event)">ยืนยัน</button>
                            </div>
                            <button id="submitform" style="display :none;" type="submit" name="submit">ปุ่มยืนยันฟอร์ม</button>
                        </form>
                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
                        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var dropdown1 = document.getElementById('dropdown1');
                                var dropdown3 = document.getElementById('dropdown3');
                                var submitButton = document.getElementById('submitButton');

                                function updateSubmitButton() {
                                    var value1 = dropdown1.value;
                                    var value2 = dropdown2.value;
                                    var value3 = dropdown3.value;

                                    if (value1 === '' || value3 === '') {
                                        submitButton.disabled = true;
                                    } else {
                                        submitButton.disabled = false;
                                        submitButton.style.backgroundColor = ''; // กำหนดสีเป็นค่าเริ่มต้น
                                    }
                                }

                                dropdown1.addEventListener('change', updateSubmitButton);
                                dropdown3.addEventListener('change', updateSubmitButton);

                                updateSubmitButton(); // เรียกใช้ฟังก์ชันเพื่อตรวจสอบค่าเริ่มต้น
                            });

                            $(document).ready(function() {
                                $('.serach_customer select').selectpicker();
                            })

                            function showSweetAlert(event) {
                                event.preventDefault();
                                console.log('showSweetAlert function is called'); // แสดงข้อความใน Console
                                const swalCustom = Swal.mixin({
                                    customClass: {
                                        confirmButton: "green-swal",
                                        cancelButton: "delete-swal"
                                    },
                                    buttonsStyling: false
                                });
                                swalCustom.fire({
                                    title: 'คำเตือน',
                                    text: 'ถ้าคุณดำเนินการแล้วจะไม่สามารถแก้ไขได้ ต้องการดำเนินการต่อหรือไม่?',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: 'ตกลง',
                                    cancelButtonText: 'ยกเลิก'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // ทำงานเมื่อผู้ใช้คลิก "ตกลง"
                                        swalCustom.fire('ดำเนินการสำเร็จ!', '', 'success').then(() => {
                                            // ส่ง form ไปยังหน้าปลายทาง
                                            document.getElementById('submitform').click(); // กำหนดค่าคลิกปุ่ม
                                        });
                                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                                        // ทำงานเมื่อผู้ใช้คลิก "ยกเลิก"
                                        swalCustom.fire('การดำเนินการถูกยกเลิก', '', 'error')
                                    }
                                });
                            }
                        </script>

                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            if (isset($_POST['submit'])) {
                                $selectedValue1 = $_POST['dropdown1'];
                                $selectedValue2 = $_POST['dropdown2'];
                                $selectedValue3 = $_POST['dropdown3'];

                                $roleValue1 = 'Peer';
                                $roleValue2 = 'Subordinate';
                                $roleValue3 = 'Customer';
                                $roleValue4 = 'Manager';
                                $roleValue5 = 'Myself';

                                $status1 = 'approve';
                                $status2 = NULL;

                                list($cardId2, $under2) = explode('|', $selectedValue2);

                                // ค่าไม่ว่าง ทำการ insert ข้อมูล
                                $sqlInsert = "
                        INSERT INTO transaction_review (review_to, reviewer, role, status, date)
                        OUTPUT INSERTED.tr_id
                        VALUES (?, ?, ?, ?, GETDATE()), (?, ?, ?, ?, GETDATE()), (?, ?, ?, ?, GETDATE()), (?, ?, ?, ?, GETDATE()), (?, ?, ?, ?, GETDATE())
                    ";
                                $params = array(
                                    $card_id, $card_id, $roleValue5, $status1,
                                    $card_id, $manager_card_id, $roleValue4, $status1,
                                    $card_id, $selectedValue1, $roleValue1, $status2,
                                    $card_id, $cardId2, $under2, $status2,
                                    $card_id, $selectedValue3, $roleValue3, $status2
                                );

                                $stmt = sqlsrv_query($conn, $sqlInsert, $params);

                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }

                                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                    $lastInsertedId = $row['tr_id'];

                                    // ลูปเพื่อทำการ insert ข้อมูลใน review_score
                                    $sqlInsertReviewScore = "INSERT INTO review_score (tr_id) VALUES (?) ";
                                    $paramsReviewScore = array($lastInsertedId);
                                    $stmtReviewScore = sqlsrv_query($conn, $sqlInsertReviewScore, $paramsReviewScore);

                                    if ($stmtReviewScore === false) {
                                        die(print_r(sqlsrv_errors(), true));
                                    }
                                }
                                echo "<script>window.location.href = 'checkrole.php';</script>";
                            }
                        }

                        ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- main end -->
    </div>
    <?php include('../admin/include/scripts.php') ?>

</body>

</html>