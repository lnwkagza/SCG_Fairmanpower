<?php include('../admin/include/header.php') ?>

<body>

    <?php include('../admin/include/navbar.php') ?>
    <?php include('../admin/include/sidebar.php') ?>
    <?php include('../admin/include/scripts.php') ?>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-100px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <h3>จัดการเงินเดือนพนักงาน : Employee Payment</h3>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">จัดการเงินเดือนพนักงาน</li>
                                    <li class="breadcrumb-item"><a href="income.php">รายรับ/รายจ่าย</a></li>
                                    <li class="breadcrumb-item"><a href="calculator_payment2.php">คำนวณเงินเดือน</a></li>
                                    <li class="breadcrumb-item"><a href="history_payment.php">ประวัติการคำนวณ</a></li>
                                    <li class="breadcrumb-item"><a href="setting_payment_income_deduct.php">ตั้งค่ารายรับรายจ่าย</a></li>
                                    <li class="breadcrumb-item"><a href="setting_payment_general.php">ตั้งค่าทั่วไป</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card-box pd-30 pt-10 height-100-p">
                        <div class="title">
                            <h2 class="h3 mb-0 text-blue mt-2">จัดการเงินเดือนพนักงานทั้งหมด</h2>
                            <div class="pb-20 mt-2 ">
                                <div class="table-responsive mt-2">
                                    <table class="data-table2 table stripe hover nowrap">
                                        <thead>
                                            <tr>
                                                <th>เลขที่</th>
                                                <th>ชื่อ-สกุล</th>
                                                <th>Company / Division / Department / Section / Cost Center / ประเภทพนักงาน / ระดับการทำงาน / ตำแหน่ง</th>
                                                <th style="text-align: center;">จัดการเงินเดือน </th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <!-- SELECT ค่า employee_payment -->
                                            <?php
                                            // เตรียมคำสั่ง SQL
                                            $sql = "SELECT employee_payment_id,employee.card_id as card_id, scg_employee_id, prefix_thai,firstname_thai,lastname_thai,nickname_thai,evidence_name,company.name_thai as company,division.name_thai as division,department.name_thai as department,section.name_thai as section,cost_center.cost_center_code as cost_center,contract_type.name_thai as contract_type,pl.pl_name_thai as pl,position.name_thai as position ,salary_per_month,salary_per_day,salary_per_hour,comment,time_set FROM employee
                                        LEFT JOIN cost_center ON cost_center.cost_center_id  = employee.cost_center_organization_id
                                        LEFT JOIN section ON section.section_id = cost_center.section_id 
                                        LEFT JOIN department ON department.department_id = section.department_id 
                                        LEFT JOIN division ON division.division_id = department.division_id 
                                        LEFT JOIN location ON location.location_id = division.location_id
                                        LEFT JOIN company ON company.company_id = location.company_id
                                        LEFT JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                        LEFT JOIN pl_info ON pl_info.card_id  = employee.card_id
                                        LEFT JOIN pl ON pl.pl_id = pl_info.pl_id 
                                        LEFT JOIN position_info ON position_info.card_id  = employee.card_id
                                        LEFT JOIN position ON position.position_id  = position_info.position_id
                                        LEFT JOIN employee_payment ON employee_payment.card_id = employee.card_id";
                                            // เพิ่มเงื่อนไขค้นหา
                                            $params = array();
                                            // ดึงข้อมูลจากฐานข้อมูล
                                            $stmt = sqlsrv_query($conn, $sql, $params);
                                            // ตรวจสอบการทำงานของคำสั่ง SQL
                                            if ($stmt === false) {
                                                die(print_r(sqlsrv_errors(), true));
                                            }
                                            // แสดงผลลัพธ์ในรูปแบบของตาราง HTML
                                            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                                echo "<tr >";
                                                echo "<td >" . $row["scg_employee_id"] . "</td>";
                                                echo "<td >" . $row["prefix_thai"] . '' . $row["firstname_thai"] . ' ' . $row["lastname_thai"] . ' (' . $row["nickname_thai"] . ')' . "</td>";
                                                echo "<td >"  . $row["company"] . ' / ' . $row["division"] . ' / ' . $row["department"] . ' / ' . $row["section"] . ' / ' . $row["cost_center"] . '<br>' . $row["contract_type"] . ' / ' . $row["pl"] . ' / ' . $row["position"] . "</td>";

                                                if ($row['salary_per_month'] or $row['salary_per_day'] or $row['salary_per_hour'] !== NULL) {
                                                    echo "<td class='d-flex justify-content-center table-sm'><button type='button' class='add-btn-disabled' disabled>";
                                                } else {
                                                    echo "<td class='d-flex justify-content-center table-sm'><button type='button' class='add-btn' onclick='openInsert_Employee_Payment_Modal(
                                                \"" . $row['card_id'] . "\",
                                                \"" . $row['employee_payment_id'] . "\",
                                                \"" . $row['prefix_thai'] . "\",
                                                \"" . $row['firstname_thai'] . "\",
                                                \"" . $row['lastname_thai'] . "\",
                                                \"" . $row['company'] . "\",
                                                \"" . $row['division'] . "\",
                                                \"" . $row['department'] . "\",
                                                \"" . $row['section'] . "\",
                                                \"" . $row['cost_center'] . "\",
                                                \"" . $row['contract_type'] . "\",
                                                \"" . $row['pl'] . "\",
                                                \"" . $row['position'] . "\",
                                                \"" . $row['salary_per_month'] . "\",
                                                \"" . $row['salary_per_day'] . "\",
                                                \"" . $row['salary_per_hour'] . "\",
                                                \"" . $row['comment'] . "\",
                                                );'>";
                                                }
                                                echo "<i class='fa-solid fa-plus'></i>";
                                                echo "</button>&nbsp;";
                                                echo "<button type='button' class='edit-btn' onclick='openEdit_Employee_Payment(\"" . urlencode($row['employee_payment_id']) . "\", \"" . urlencode($row["card_id"]) . "\");'>";
                                                echo "<i class='fa-solid fa-pencil'></i>";
                                                echo "</button>&nbsp;";
                                                if ($row['evidence_name'] !== NULL) {
                                                    echo "<a href='flie/" . $row['evidence_name'] . "' download>";
                                                    echo "<button type='button' class='dowload-btn'>";
                                                    echo "<i class='fa-solid fa-file'></i>";
                                                    echo "</button>";
                                                    echo "</a>";
                                                } else {
                                                    echo "<button type='button' class='dowload-btn-disabled'>";
                                                    echo "<i class='fa-solid fa-file'></i>";
                                                    echo "</button>";
                                                }
                                                echo '</tr></td>';
                                            }
                                            ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Start -->
                <div class="modal fade" id="editemployeeModal" tabindex="-1" role="dialog" aria-labelledby="editemployeeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">แก้ไขเงินเดือนพนักงาน</h5>
                            </div>
                            <div class="modal-body">
                                <!-- Form for editing data -->
                                <form id="editForm" method="post" action="employee_payment.php">
                                    <input type="hidden" id="card_id" name="card_id">
                                    <input type="hidden" id="edit_employee_payment_id" name="employee_payment_id">
                                    <div class="form-group d-flex justify-content-center">
                                        <input type="text" class="col-12" id="name" name="first_name_thai" autocomplete="off" disabled style="border: none; background-color: transparent; font-size: 25px; font-weight: bold; text-align: center;">
                                    </div>
                                    <div class="mt-3">
                                        <h6><label>Organization :</label></h6>
                                    </div>
                                    <div class="row">
                                        <div class="form-group d-flex">
                                            <div class="ml-3 mt-2 col-5">
                                                <label for="edit_company">Company :</label>
                                            </div>&nbsp;
                                            <input type="text" class="col-10 mt-1" id="edit_company" name="company" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group d-flex">
                                            <div class="ml-3 mt-2 col-5">
                                                <label for="edit_division">Division :</label>
                                            </div>&nbsp;&nbsp;
                                            <input type="text" class="col-10 mt-1" id="edit_division" name="division" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group d-flex">
                                            <div class="ml-3 mt-2 col-5">
                                                <label for="edit_department">Department :</label>
                                            </div>
                                            <input type="text" class="col-10 mt-1" id="edit_department" name="department" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group d-flex">
                                            <div class="ml-3 mt-2 col-5">
                                                <label for="edit_section">Section :</label>
                                            </div>&nbsp;&nbsp;&nbsp;
                                            <input type="text" class="col-10 mt-1" id="edit_section" name="section" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group d-flex">
                                            <div class="ml-3 mt-2 col-5">
                                                <label for="edit_cost_center">Cost Center :</label>
                                            </div>&nbsp;
                                            <input type="text" class="col-10 mt-1 " id="edit_cost_center" name="cost_center" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group d-flex">
                                            <div class="mt-2 col-5">
                                                <h6><label for="edit_contract_type">ประเภทพนักงาน :</label></h6>
                                            </div>
                                            <input type="text" class="col-10 mt-1 ml-3" id="edit_contract_type" name="contract_type" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group d-flex">
                                            <div class="mt-2 col-5">
                                                <h6><label for="edit_pl">ระดับการทำงาน :</label></h6>
                                            </div>
                                            <input type="text" class="col-10 mt-1 ml-3" id="edit_pl" name="pl" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group d-flex">
                                            <div class="mt-2 col-5">
                                                <h6><label for="edit_position">ตำแหน่ง :</label></h6>
                                            </div>&nbsp;&nbsp;&nbsp;
                                            <input type="text" class="col-10 mt-1 ml-3" id="edit_position" name="position" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                        </div>
                                    </div>
                                    <div class="row ml-1">
                                        <div class="form-group">
                                            <h6><label for="editsalary_per_month">ค่าแรง/เดือน</label>
                                                <input type="number" class="form-control" id="editsalary_per_month" placeholder="กรอกเงินเดือน" name="salary_per_month" required autocomplete="off">
                                        </div>
                                        <div class="form-group ml-3">
                                            <h6><label for="editsalary_per_day">ค่าแรง/วัน</label></h6>
                                            <input type="number" class="form-control" id="editsalary_per_day" placeholder="รายเดือนหารด้วย30วัน" name="salary_per_day" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="row ml-1">
                                        <div class="form-group ">
                                            <h6><label for="editsalary_per_hour">ค่าแรง/ชั่วโมง</label></h6>
                                            <input type="number" class="form-control" id="editsalary_per_hour" placeholder="รายวันหารด้วย8ชั่วโมง" name="salary_per_hour" required autocomplete="off">
                                        </div>
                                        <div class="form-group ml-3">
                                            <h6><label for="editcomment">หมายเหตุ</label></h6>
                                            <input type="text" class="form-control" id="editcomment" placeholder="กรุณากรอกหมายเหตุ" name="comment" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <h6><label for="editfile">แนบหลักฐาน (ถ้ามี)</label></h6>
                                        <input type="file" id="editfile" name="file" class="form-control">
                                    </div>
                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary" name="update_employee_payment">บันทึกการแก้ไข</button>
                                    </div>
                                </form>

                                <?php
                                // -- UPDATE employee_payment based on employee_payment_id -->

                                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_employee_payment'])) {
                                    $card_id = $_POST['card_id'];
                                    $employee_payment_id = $_POST['employee_payment_id'];
                                    $salary_per_month = $_POST['salary_per_month'];
                                    $formatted_salary_per_month = number_format($salary_per_month, 2);
                                    $salary_per_day = $_POST['salary_per_day'];
                                    $formatted_salary_per_day = number_format($salary_per_day, 2);
                                    $salary_per_hour = $_POST['salary_per_hour'];
                                    $formatted_salary_per_hour = number_format($salary_per_hour, 2);
                                    $comment = $_POST['comment'];

                                    $sqlGet = "SELECT employee_payment_id, card_id, salary_per_month, salary_per_day, salary_per_hour, comment, time_set
                                    FROM fairman.dbo.employee_payment
                                    WHERE employee_payment_id=$employee_payment_id;";
                                    $stmt = sqlsrv_query($conn, $sqlGet);
                                    if (empty($stmt)) {
                                        $sqlInsert = "INSERT INTO employee_payment
                                    (card_id, salary_per_month, salary_per_day, salary_per_hour, comment)
                                    VALUES( '$card_id', '$salary_per_month', '$salary_per_day', '$salary_per_hour','$comment');";
                                        $stmt = sqlsrv_query($conn, $sqlInsert);
                                    } else {
                                        $sqlUpdate = "UPDATE employee_payment SET salary_per_month = '$salary_per_month', salary_per_day = '$salary_per_day', salary_per_hour = '$salary_per_hour', comment = '$comment' WHERE employee_payment_id = '$employee_payment_id'";
                                        $stmt = sqlsrv_query($conn, $sqlUpdate);
                                    }
                                    // เพิ่ม log ลงในตาราง log_salary_comment
                                    $sqlLog = "INSERT INTO log_payment (employee_payment_id, old_salary_per_month, new_salary_per_month, old_salary_per_day, new_salary_per_day, old_salary_per_hour, new_salary_per_hour,old_comment, new_comment)
                                    VALUES (@employee_payment_id, @old_salary_per_month, @new_salary_per_month, @old_salary_per_day, @new_salary_per_day, @old_salary_per_hour, @new_salary_per_hour, @old_comment, @new_comment);";
                                    $stmtLog = sqlsrv_query($conn, $sqlLog);

                                    // อัปเดตค่าของฟิลด์ salary และ comment

                                    if ($stmt === false) {
                                        die(print_r(sqlsrv_errors(), true));
                                    } else {
                                        echo '<script type="text/javascript">
                                                    const Toast = Swal.mixin({
                                                        toast: true,
                                                        position: "top-end",
                                                        showConfirmButton: false,
                                                        timer: 950,
                                                        timerProgressBar: true,
                                                        didOpen: (toast) => {
                                                            toast.onmouseenter = Swal.stopTimer;
                                                            toast.onmouseleave = Swal.resumeTimer;
                                                        }
                                                    });
                                                    Toast.fire({
                                                        icon: "success",
                                                        title: "แก้ไขข้อมูล Employee Payment สำเร็จ"
                                                    });
                                                    </script>';

                                        echo "<meta http-equiv='refresh' content='1'>";

                                        exit; // จบการทำงานของสคริปต์ทันทีหลังจาก redirect
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal End -->

            </div>
            <?php include('../admin/include/footer.php'); ?>
</body>

</html>