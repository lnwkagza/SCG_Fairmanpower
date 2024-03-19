<?php include('../admin/include/header.php') ?>


<body>

    <?php include('../admin/include/navbar.php') ?>
    <?php include('../admin/include/sidebar.php') ?>
    <?php include('include/scripts.php') ?>

    <div class="mobile-menu-overlay"></div>


    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-100px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <h3>รายรับ/รายจ่าย : Income Deduct Payment</h3>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="employee_payment.php">จัดการเงินเดือนพนักงาน</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">รายรับ/รายจ่าย</li>
                                    <li class="breadcrumb-item"><a href="calculator_payment2.php">คำนวณเงินเดือน</a></li>
                                    <li class="breadcrumb-item"><a href="history_payment.php">ประวัติการคำนวณ</a></li>
                                    <li class="breadcrumb-item"><a href="setting_payment_income_deduct.php">ตั้งค่ารายรับรายจ่าย</a></li>
                                    <li class="breadcrumb-item"><a href="setting_payment_general.php">ตั้งค่าทั่วไป</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <button type="button" class="btn income-deduct-btn-pay">รายการเงินรับ</button>&nbsp;&nbsp;
                                    <button type="button" class="btn income-deduct-btn-pay-split" onclick="window.location.href='deduct.php'">รายการเงินจ่าย</button>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md- 12-sm-12 mt-2">
                            <div class="card-box pd-30 pt-10 height-100-p" style="box-shadow:1px 4px 8px rgba(0, 0, 0, 0.2);">
                                <div class="pd-10">
                                    <div class="title">
                                        <div class="row">
                                            <h4 class="col-7">รายการเงินรับ: Income Payment</h4>
                                            <div class="col-3"></div>
                                            <div class="text-right col-2">
                                                <button onclick="openInsert_Income_Target_Modal()" type="button" class="btn setting-btn-pay" data-dismiss="modal">
                                                    + เพิ่มรายการรับ
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pb-10">
                                    <div class="table-responsive mt-2">
                                        <table class="data-table2 table stripe hover nowrap">
                                            <thead>
                                                <tr>
                                                <th style="text-align: center;">ประเภท</th>
                                                <th style="text-align: center;">ชื่อ-นามสกุล</th>
                                                <th style="text-align: center;">จำนวนเงิน <br>(บาท)</th>
                                                <th style="text-align: center;">คำนวณเงินได้ทั้งปี </th>
                                                <th style="text-align: center;">หมายเหตุ </th>
                                                <th style="text-align: center;">เปิดใช้งาน</th>
                                                <th style="text-align: center;">การจัดการ</th>
                                                <th>
                                                    กลุ่มเป้าหมาย : Company / Division / Department /
                                                    Section / Cost Center
                                                    ประเภทพนักงาน / ระดับการทำงาน / ตำแหน่ง / รายชื่อพนักงาน
                                                </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- SELECT ค่า income_target -->
                                                <?php
                                                // เตรียมคำสั่ง SQL
                                                $sql = "SELECT employee.card_id as card_id,income_target_id,evidence_name,income_type.income_type as income_type, amount,whole_year.whole_year as whole_year,active,reason,company.name_thai as company,division.name_thai as division,department.name_thai as department,section.name_thai as section,cost_center.cost_center_code as cost_center,contract_type.name_thai as contract_type,pl.pl_name_thai as pl,position.name_thai as position , prefix_thai, firstname_thai, lastname_thai FROM income_target  
                                        INNER JOIN income_type ON income_type.income_type_id = income_target.income_type_id 
                                        INNER JOIN employee ON employee.card_id = income_target.card_id 
                                        LEFT JOIN cost_center ON cost_center.cost_center_id  = employee.cost_center_organization_id
                                        LEFT JOIN section ON section.section_id = cost_center.section_id 
                                        LEFT JOIN department ON department.department_id = section.department_id 
                                        LEFT JOIN division ON division.division_id = department.division_id 
                                        LEFT JOIN location ON location.location_id = division.location_id
                                        LEFT JOIN company ON company.company_id = location.company_id
                                        LEFT JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                        LEFT JOIN pl_info ON pl_info.card_id  = income_target.card_id
                                        LEFT JOIN pl ON pl.pl_id = pl_info.pl_id 
                                        LEFT JOIN position_info ON position_info.card_id  = income_target.card_id
                                        LEFT JOIN position ON position.position_id  = position_info.position_id
                                        LEFT JOIN whole_year ON whole_year.whole_year_id  = income_target.whole_year_id";

                                                $params = array();
                                                // ดึงข้อมูลจากฐานข้อมูล
                                                $stmt = sqlsrv_query($conn, $sql, $params);
                                                // ตรวจสอบการทำงานของคำสั่ง SQL
                                                if ($stmt === false) {
                                                    die(print_r(sqlsrv_errors(), true));
                                                }

                                                // แสดงผลลัพธ์ในรูปแบบของตาราง HTML
                                                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                                    echo "<tr>";
                                                    echo "<td class='col-md-1' style='text-align: center;''>" . $row["income_type"] . "</td>";
                                                    echo "<td class='col-md-2'>" . $row["prefix_thai"] . $row["firstname_thai"] . '  ' . $row["lastname_thai"] . "</td>";
                                                    echo "<td class='col-md-1' style='text-align: center;''>" . $row["amount"] . "</td>";
                                                    echo "<td class='col-md-1' style='text-align: center;''>" . $row["whole_year"] . "</td>";
                                                    echo "<td class='col-md-2' style='text-align: center;''>" . $row["reason"] . "</td>";
                                                    echo "<td class='col-md-1'>";
                                                    if ($row["active"] == 0) {
                                                        echo '<div class="custom-control custom-switch custom-switch-sm d-flex justify-content-center">';
                                                        echo '<input type="checkbox" class="custom-control-input toggleSwitch" id="toggleSwitch_' . $row['income_target_id'] . '" data-income-target-id="' . $row['income_target_id'] . '">';
                                                        echo '<label class="custom-control-label" for="toggleSwitch_' . $row['income_target_id'] . '"></label>';
                                                        echo '</div>';
                                                    } else if ($row["active"] == 1) {
                                                        echo '<div class="custom-control custom-switch custom-switch-sm d-flex justify-content-center">';
                                                        echo '<input type="checkbox" class="custom-control-input toggleSwitch" id="toggleSwitch_' . $row['income_target_id'] . '" data-income-target-id="' . $row['income_target_id'] . '" checked>';
                                                        echo '<label class="custom-control-label" for="toggleSwitch_' . $row['income_target_id'] . '"></label>';
                                                        echo '</div>';
                                                    }
                                                    echo "</td>";
                                                    echo "<td><div class='flex'  style='justify-content: right;'>",
                                                    '<button type="button" name="delete_income_target" class="delete-btn-pay" onclick="confirmDeleteIncome_target(\'' . $row['income_target_id'] . '\');"><i class="fa-solid fa-trash-can"></i></button> &nbsp;';
                                                    echo "<button type='button' class='edit-btn' onclick='openEdit_Income_Target_Modal(
                                                \"" . $row['income_target_id'] . "\",
                                                \"" . $row['prefix_thai'] . "\",
                                                \"" . $row['firstname_thai'] . "\",
                                                \"" . $row['lastname_thai'] . "\",
                                                \"" . $row['income_type'] . "\",
                                                \"" . $row['company'] . "\",
                                                \"" . $row['division'] . "\",
                                                \"" . $row['department'] . "\",
                                                \"" . $row['section'] . "\",
                                                \"" . $row['cost_center'] . "\",
                                                \"" . $row['contract_type'] . "\",
                                                \"" . $row['pl'] . "\",
                                                \"" . $row['position'] . "\",
                                                \"" . $row['amount'] . "\",
                                                \"" . $row['whole_year'] . "\",
                                                \"" . $row['reason'] . "\"
                                                );'>";
                                                    echo "<i class='fa-solid fa-pencil'></i>";
                                                    echo "</button> &nbsp;";
                                                    echo "<a href='flie/" . $row['evidence_name'] . "' download>";
                                                    echo "<button type='button' class='dowload-btn'>";
                                                    echo "<i class='fa-solid fa-file'></i>";
                                                    echo "</button>";
                                                    echo "</a>";
                                                    echo "<td class='col-md-4'>"  . $row["company"] . ' / ' . $row["division"] . ' / ' . $row["department"] . ' / ' . $row["section"] . ' / ' . $row["cost_center"] . ' / ' . $row["contract_type"] . ' / ' . $row["pl"] . ' / ' . $row["position"] . "</td>";
                                                    echo '</tr>';
                                                }
                                                ?>
                                                <script>
                                                    $(document).ready(function() {
                                                        $('.toggleSwitch').change(function() {
                                                            var isChecked = $(this).prop('checked');
                                                            var incomeTargetId = $(this).data('income-target-id');

                                                            // ส่งข้อมูลไปยังไฟล์ PHP ด้วย AJAX request
                                                            $.ajax({
                                                                url: 'income_update_active.php',
                                                                method: 'POST',
                                                                data: {
                                                                    isChecked: isChecked,
                                                                    incomeTargetId: incomeTargetId
                                                                },
                                                                success: function(response) {
                                                                    console.log('การอัพเดตข้อมูลสำเร็จ');
                                                                },
                                                                error: function(xhr, status, error) {
                                                                    console.error('เกิดข้อผิดพลาดในการส่งข้อมูลไปยังฐานข้อมูล');
                                                                }
                                                            });
                                                        });
                                                    });
                                                </script>

                                                <?php

                                                // -- DELETE  ค่า income_target ตาม income_target_id -->

                                                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_income_target'])) {

                                                    $income_target_id = $_POST['income_target_id'];
                                                    $sql = "DELETE FROM income_target WHERE income_target_id = ?";
                                                    $params = array($income_target_id);

                                                    $stmt = sqlsrv_prepare($conn, $sql, $params);
                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }

                                                    $result = sqlsrv_execute($stmt);
                                                    if ($result === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    } else {
                                                        echo '<script type="text/javascript">
                                                            const swalWithBootstrapButtons = Swal.mixin({
                                                                customClass: {
                                                                    confirmButton: "delete-swal",
                                                                    cancelButton: "edit-swal"
                                                                },
                                                                buttonsStyling: false
                                                            });
                                                            swalWithBootstrapButtons.fire({
                                                                icon: "success",
                                                                title: "ระบบลบข้อมูลสำเร็จ ",
                                                                text: "อีกสักครู่ ...ระบบจะทำการรีเฟส",
                                                                confirmButtonText: "ตกลง",

                                                            })
                                                        </script>';
                                                        echo "<meta http-equiv='refresh' content='2'>";
                                                        exit();
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Start -->
                    <div class="modal fade" id="insertincome_targetModal" tabindex="-1" role="dialog" aria-labelledby="insertincome_targetModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">บันทึกรายการรับ</h5>
                                </div>
                                <div class="modal-body">
                                    <!-- Form for editing data -->
                                    <form id="editForm" method="post" action="income.php" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <h6><label>ประเภทรายรับ</label></h6>
                                            <select id="income_typeDropdown" name="income_type_id" class="custom-select col-12" required="true" autocomplete="off">
                                                <option value="" disabled selected>เลือกประเภทรายรับ</option>
                                                <?php
                                                $sqlDropdown_type = "SELECT * FROM income_type";
                                                echo $sqlDropdown_type; // แสดงคำสั่ง SQL เพื่อตรวจสอบ

                                                $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type);

                                                if ($resultDropdown_type === false) {
                                                    die(print_r(sqlsrv_errors(), true));
                                                }

                                                if ($resultDropdown_type) {
                                                    while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                        echo "<option value='" . $row['income_type_id'] . "'>" . $row['income_type'] . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <h6><label for="amount">จำนวนเงิน (บาท)</label></h6>
                                            <input type="number" class="form-control" id="amount" name="amount" placeholder="กรอกจำนวนเงิน" required="true" autocomplete="off">
                                        </div>
                                        <div class="form-group">
                                            <h6><label>คำนวณเงินได้ทั้งปี</label></h6>
                                            <select type="text" class="custom-select" id="whole_yearDropdown" name="whole_year_id" required="true" autocomplete="off">
                                                <option value="" disabled selected>เลือกคำนวณเงินได้</option>
                                                <?php
                                                $sqlDropdown_type = "SELECT * FROM whole_year";
                                                echo $sqlDropdown_type; // แสดงคำสั่ง SQL เพื่อตรวจสอบ

                                                $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type);

                                                if ($resultDropdown_type === false) {
                                                    die(print_r(sqlsrv_errors(), true));
                                                }

                                                if ($resultDropdown_type) {
                                                    while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                        echo "<option value='" . $row['whole_year_id'] . "'>" . $row['whole_year'] . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <h6><label for="reason">หมายเหตุ</label></h6>
                                            <input type="text" class="form-control" id="reason" placeholder="กรอกหมายเหตุ" name="reason" autocomplete="off">
                                        </div>
                                        <div class="form-group">
                                            <h6><label for="insertfile">แนบหลักฐาน (ถ้ามี)</label></h6>
                                            <input type="file" id="insertfile" name="file" class="form-control">
                                        </div>
                                        <h6>กลุ่มเป้าหมาย</h6>
                                        <div class="mt-1">
                                            <h6><label>Organization :</label></h6>
                                        </div>
                                        <div class="row">
                                            <div class="ml-5">
                                                Company&nbsp;
                                            </div>
                                            <div class="mt-2 ml-4 col-md-7 col-sm-12">
                                                <div class="form-group">
                                                    <select id="company" name="company_id" class="custom-select" aria-label="Default select example" autocomplete="off">
                                                        <?php
                                                        if (isset($_POST['employee'])) {
                                                            // Receive the selected division from the AJAX request
                                                            $selectedEmployee = $_POST['employee'];
                                                            // Query to retrieve departments based on the selected division
                                                            $sqlDropdown_type = "SELECT company.* FROM company
                                                        INNER JOIN location ON location.company_id  = company.company_id
                                                        INNER JOIN division ON division.location_id = location.location_id
                                                        INNER JOIN department ON department.division_id = division.division_id
                                                        INNER JOIN section ON section.department_id = department.department_id
                                                        INNER JOIN cost_center ON cost_center.section_id = section.section_id
                                                        INNER JOIN employee ON employee.cost_center_organization_id = cost_center.cost_center_id 
                                                        WHERE employee.card_id =  ?";
                                                            $params = array($selectedEmployee);
                                                            $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);
                                                            $optionsCompany = '';
                                                            if ($resultDropdown_type === false) {
                                                                // Handle query errors
                                                                die(print_r(sqlsrv_errors(), true));
                                                            }
                                                            // Generate the options for the department dropdown
                                                            // $options = "<option value=''>All</option>";
                                                            while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                $optionsCompany .= "<option value='" . $row['company_id'] . "'>" . $row['name_thai'] . "</option>";
                                                            }

                                                            // Echo only the department options
                                                            echo $optionsCompany;
                                                        }
                                                        if (empty($_POST['employee'])) {
                                                            $sqlDropdown_type = "SELECT * FROM company";
                                                            $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type);
                                                            echo '<option value="" selected>All</option>';
                                                            if ($resultDropdown_type === false) {
                                                                die(print_r(sqlsrv_errors(), true));
                                                            }

                                                            while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {

                                                                echo "<option value='" . $row['company_id'] . "' >" . $row['name_thai'] . "</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="ml-5">
                                                Division&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            </div>
                                            <div class="ml-3 col-md-7 col-sm-12">
                                                <div class="form-group">
                                                    <select id="division" name="division_id" class="custom-select" aria-label="Default select example" autocomplete="off">
                                                        <?php
                                                        session_start();
                                                        if (isset($_POST['company'])) {
                                                            // Receive the selected division from the AJAX request

                                                            $selectedCompany = $_POST['company'];
                                                            $_SESSION['sessioncompany'] = $selectedCompany;

                                                            if ($selectedCompany != "") {
                                                                // Query to retrieve departments based on the selected division
                                                                $sqlDropdown_type = "SELECT division.* FROM division 
                                                            INNER JOIN location ON location.location_id = division.location_id
                                                            INNER JOIN company ON company.company_id = location.company_id
                                                            WHERE company.company_id = ?";
                                                                $params = array($selectedCompany);
                                                                $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);
                                                                $optionsDivision = '';
                                                                if ($resultDropdown_type === false) {
                                                                    // Handle query errors
                                                                    die(print_r(sqlsrv_errors(), true));
                                                                }
                                                                // Generate the options for the department dropdown
                                                                $optionsDivision = "<option value=''>All</option>";
                                                                while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                    $optionsDivision .= "<option value='" . $row['division_id'] . "'>" . $row['name_thai'] . "</option>";
                                                                }
                                                                // Echo only the department options
                                                                echo $optionsDivision;
                                                            }
                                                        }
                                                        if (isset($_POST['employee'])) {
                                                            // Receive the selected division from the AJAX request
                                                            $selectedEmployee = $_POST['employee'];
                                                            // Query to retrieve departments based on the selected division

                                                            $sqlDropdown_type = "SELECT division.* FROM division
                                                            INNER JOIN department ON department.division_id = division.division_id
                                                            INNER JOIN section ON section.department_id = department.department_id
                                                            INNER JOIN cost_center ON cost_center.section_id = section.section_id
                                                            INNER JOIN employee ON employee.cost_center_organization_id = cost_center.cost_center_id 
                                                            WHERE employee.card_id =  ?";
                                                            $params = array($selectedEmployee);
                                                            $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);
                                                            $optionsDivision = '';

                                                            if ($resultDropdown_type === false) {
                                                                // Handle query errors
                                                                die(print_r(sqlsrv_errors(), true));
                                                            }
                                                            // Generate the options for the department dropdown
                                                            while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                $optionsDivision .= "<option value='" . $row['division_id'] . "'>" . $row['name_thai'] . "</option>";
                                                            }
                                                            // Echo only the department options
                                                            echo $optionsDivision;
                                                        }
                                                        if (empty($_POST['employee'])) {
                                                            echo '<option value="" hidden>All</option>';
                                                        }
                                                        ?>
                                                    </select>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="ml-5">
                                                Department&nbsp;&nbsp;
                                            </div>
                                            <div class="col-md-7 col-sm-12">
                                                <div class="form-group">
                                                    <select id="department" name="department_id" class="custom-select" autocomplete="off">
                                                        <?php
                                                        session_start();
                                                        if (isset($_POST['company'])) {
                                                            $selectedCompany = $_POST['company'];
                                                            echo '<option value="" hidden>All</option>';
                                                        }
                                                        if (isset($_POST['division'])) {
                                                            // Receive the selected division from the AJAX request

                                                            $selectedDivision = $_POST['division'];
                                                            $_SESSION['sessiondivision'] = $selectedDivision;

                                                            if ($selectedDivision != "") {
                                                                // Query to retrieve departments based on the selected division
                                                                $sqlDropdown_type = "SELECT department.* FROM department 
                                                        INNER JOIN division ON division.division_id = department.division_id
                                                        INNER JOIN location ON location.location_id = division.location_id
                                                        INNER JOIN company ON company.company_id = location.company_id
                                                        WHERE division.division_id = ? ";
                                                                $params = array($selectedDivision);
                                                                $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);
                                                                $optionsDepartment = '';
                                                                if ($resultDropdown_type === false) {
                                                                    // Handle query errors
                                                                    die(print_r(sqlsrv_errors(), true));
                                                                }
                                                                // Generate the options for the department dropdown
                                                                $optionsDepartment = "<option value=''>All</option>";
                                                                while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                    $optionsDepartment .= "<option value='" . $row['department_id'] . "'>" . $row['name_thai'] . "</option>";
                                                                }
                                                                // Echo only the department options
                                                                echo $optionsDepartment;
                                                            }
                                                        }
                                                        if (isset($_POST['employee'])) {
                                                            // Receive the selected division from the AJAX request
                                                            $selectedEmployee = $_POST['employee'];
                                                            // Query to retrieve departments based on the selected division
                                                            $sqlDropdown_type = "SELECT department.* FROM department
                                                            INNER JOIN section ON section.department_id = department.department_id
                                                            INNER JOIN cost_center ON cost_center.section_id = section.section_id
                                                            INNER JOIN employee ON employee.cost_center_organization_id = cost_center.cost_center_id 
                                                            WHERE employee.card_id =  ?";
                                                            $params = array($selectedEmployee);
                                                            $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);
                                                            $optionsDepartment = '';

                                                            if ($resultDropdown_type === false) {
                                                                // Handle query errors
                                                                die(print_r(sqlsrv_errors(), true));
                                                            }
                                                            // Generate the options for the department dropdown
                                                            while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                $optionsDepartment .= "<option value='" . $row['department_id'] . "'>" . $row['name_thai'] . "</option>";
                                                            }
                                                            // Echo only the department options
                                                            echo $optionsDepartment;
                                                        }
                                                        if (empty($_POST['employee'])) {
                                                            echo '<option value="" hidden>All</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="ml-5">
                                                Section&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            </div>
                                            <div class="ml-2 col-md-7 col-sm-12">
                                                <div class="form-group">
                                                    <select id="section" name="section_id" class="custom-select" autocomplete="off">
                                                        <?php
                                                        session_start();
                                                        if (isset($_POST['company'])) {
                                                            $selectedCompany = $_POST['company'];
                                                            echo '<option value="" hidden>All</option>';
                                                        }
                                                        if (isset($_POST['division'])) {
                                                            $selectedDivision = $_POST['division'];
                                                            echo '<option value="" hidden>All</option>';
                                                        }
                                                        if (isset($_POST['department'])) {

                                                            // Receive the selected division from the AJAX request
                                                            $selectedDepartment = $_POST['department'];
                                                            $_SESSION['sessiondepartment'] = $selectedDepartment;

                                                            if ($selectedDepartment != "") {
                                                                // Query to retrieve departments based on the selected division
                                                                $sqlDropdown_type = "SELECT * FROM section WHERE department_id = ?";
                                                                $params = array($selectedDepartment);
                                                                $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);

                                                                if ($resultDropdown_type === false) {
                                                                    // Handle query errors
                                                                    die(print_r(sqlsrv_errors(), true));
                                                                }

                                                                // Generate the options for the department dropdown
                                                                $options = "<option value=''>All</option>";
                                                                while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                    $options .= "<option value='" . $row['section_id'] . "'>" . $row['name_thai'] . "</option>";
                                                                }

                                                                // Echo only the department options
                                                                echo $options;
                                                            }
                                                        }
                                                        if (isset($_POST['employee'])) {
                                                            // Receive the selected division from the AJAX request
                                                            $selectedEmployee = $_POST['employee'];
                                                            // Query to retrieve departments based on the selected division
                                                            $sqlDropdown_type = "SELECT section.* FROM section
                                                            INNER JOIN cost_center ON cost_center.section_id = section.section_id
                                                            INNER JOIN employee ON employee.cost_center_organization_id = cost_center.cost_center_id 
                                                            WHERE employee.card_id =  ?";
                                                            $params = array($selectedEmployee);
                                                            $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);
                                                            $optionsSection = '';

                                                            if ($resultDropdown_type === false) {
                                                                // Handle query errors
                                                                die(print_r(sqlsrv_errors(), true));
                                                            }
                                                            // Generate the options for the department dropdown
                                                            // $options = "<option value=''>All</option>";
                                                            while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                $optionsSection .= "<option value='" . $row['section_id'] . "'>" . $row['name_thai'] . "</option>";
                                                            }
                                                            // Echo only the department options
                                                            echo $optionsSection;
                                                        }
                                                        if (empty($_POST['employee'])) {
                                                            echo '<option value="" hidden>All</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="ml-5">
                                                Cost Center&nbsp;
                                            </div>
                                            <div class="ml-2 col-md-7 col-sm-12">
                                                <div class="form-group">
                                                    <select id="cost_center" name="cost_center_id" class="custom-select" autocomplete="off">
                                                        <?php
                                                        session_start();
                                                        if (isset($_POST['company'])) {
                                                            $selectedCompany = $_POST['company'];
                                                            echo '<option value="" hidden>All</option>';
                                                        }
                                                        if (isset($_POST['division'])) {
                                                            $selectedDivision = $_POST['division'];
                                                            echo '<option value="" hidden>All</option>';
                                                        }
                                                        if (isset($_POST['department'])) {
                                                            $selectedDepartment = $_POST['department'];
                                                            echo '<option value="" hidden>All</option>';
                                                        }
                                                        if (isset($_POST['section'])) {

                                                            // Receive the selected division from the AJAX request
                                                            $selectedSection = $_POST['section'];
                                                            $_SESSION['sessionsection'] = $selectedSection;

                                                            if ($selectedSection != "") {
                                                                // Query to retrieve departments based on the selected division
                                                                $sqlDropdown_type = "SELECT * FROM cost_center
                                                        WHERE section_id = ?";
                                                                $params = array($selectedSection);
                                                                $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);

                                                                if ($resultDropdown_type === false) {
                                                                    // Handle query errors
                                                                    die(print_r(sqlsrv_errors(), true));
                                                                }

                                                                // Generate the options for the department dropdown
                                                                $options = "<option value=''>All</option>";
                                                                while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                    $options .= "<option value='" . $row['cost_center_id'] . "'>" . $row['cost_center_code'] . "</option>";
                                                                }

                                                                // Echo only the department options
                                                                echo $options;
                                                            }
                                                        }
                                                        if (isset($_POST['employee'])) {
                                                            // Receive the selected division from the AJAX request
                                                            $selectedEmployee = $_POST['employee'];
                                                            // Query to retrieve departments based on the selected division
                                                            $sqlDropdown_type = "SELECT cost_center.* FROM cost_center
                                                            INNER JOIN employee ON employee.cost_center_organization_id = cost_center.cost_center_id 
                                                            WHERE employee.card_id =  ?";
                                                            $params = array($selectedEmployee);
                                                            $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);
                                                            $optionsCost_center = '';

                                                            if ($resultDropdown_type === false) {
                                                                // Handle query errors
                                                                die(print_r(sqlsrv_errors(), true));
                                                            }
                                                            // Generate the options for the department dropdown
                                                            // $options = "<option value=''>All</option>";
                                                            while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                $optionsCost_center .= "<option value='" . $row['cost_center_id'] . "'>" . $row['cost_center_code'] . "</option>";
                                                            }
                                                            // Echo only the department options
                                                            echo $optionsCost_center;
                                                        }
                                                        if (empty($_POST['employee'])) {
                                                            echo '<option value="" hidden>All</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="mt-2 ml-4">
                                                <h6>ประเภทพนักงาน :&nbsp;&nbsp;</h6>
                                            </div>
                                            <div class="ml-1 col-md-7 col-sm-12">
                                                <div class="form-group">
                                                    <select id="contract_type" name="contract_type_id" class="custom-select" autocomplete="off">
                                                        <?php
                                                        session_start();
                                                        if (isset($_POST['employee'])) {
                                                            // Receive the selected division from the AJAX request
                                                            $selectedEmployee = $_POST['employee'];
                                                            // Query to retrieve departments based on the selected division
                                                            $sqlDropdown_type = "SELECT contract_type.* FROM contract_type
                                                        INNER JOIN employee ON employee.contract_type_id  = contract_type.contract_type_id
                                                        WHERE employee.card_id = ?";
                                                            $params = array($selectedEmployee);
                                                            $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);
                                                            $optionsContract_type = '';
                                                            if ($resultDropdown_type === false) {
                                                                // Handle query errors
                                                                die(print_r(sqlsrv_errors(), true));
                                                            }

                                                            // Generate the options for the department dropdown
                                                            while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                $optionsContract_type .= "<option value='" . $row['contract_type_id'] . "'>" . $row['name_thai'] . "</option>";
                                                            }

                                                            // Echo only the department options
                                                            echo $optionsContract_type;
                                                        }
                                                        if (empty($_POST['employee'])) {
                                                            $sqlDropdown_type = "SELECT * FROM contract_type";
                                                            $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type);
                                                            echo "<option value='' >All</option>";
                                                            if ($resultDropdown_type === false) {
                                                                die(print_r(sqlsrv_errors(), true));
                                                            }
                                                            if ($resultDropdown_type) {
                                                                while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                    echo "<option value='" . $row['contract_type_id'] . "'>" . $row['name_thai'] . "</option>";
                                                                }
                                                            }
                                                        }

                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="ml-4">
                                                <h6>ระดับการทำงาน :</h6>
                                            </div>
                                            <div class="ml-3 col-md-7 col-sm-12">
                                                <div class="form-group">
                                                    <select id="pl" name="pl_id" class="custom-select" autocomplete="off">
                                                        <!-- <option value="" hidden>All</option> -->
                                                        <?php
                                                        if (isset($_POST['employee'])) {
                                                            // Receive the selected division from the AJAX request
                                                            $selectedEmployee = $_POST['employee'];
                                                            // Query to retrieve departments based on the selected division
                                                            $sqlDropdown_type = "SELECT * FROM pl
                                                        INNER JOIN pl_info ON pl_info.pl_id = pl.pl_id 
                                                        INNER JOIN employee ON employee.card_id = pl_info.card_id
                                                        WHERE employee.card_id = ?";
                                                            $params = array($selectedEmployee);
                                                            $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);
                                                            $optionsPl = '';
                                                            if ($resultDropdown_type === false) {
                                                                // Handle query errors
                                                                die(print_r(sqlsrv_errors(), true));
                                                            }

                                                            // Generate the options for the department dropdown
                                                            // $options = "<option value=''>All</option>";
                                                            while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                $optionsPl .= "<option value='" . $row['pl_id'] . "'>" . $row['pl_name_thai'] . "</option>";
                                                            }

                                                            // Echo only the department options
                                                            echo $optionsPl;
                                                        }
                                                        if (empty($_POST['employee'])) {
                                                            $sqlDropdown_type = "SELECT * FROM pl";
                                                            $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type);
                                                            echo "<option value=''>All</option>";
                                                            if ($resultDropdown_type === false) {
                                                                die(print_r(sqlsrv_errors(), true));
                                                            }
                                                            if ($resultDropdown_type) {
                                                                while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                    echo "<option value='" . $row['pl_id'] . "'>" . $row['pl_name_thai'] . "</option>";
                                                                }
                                                            }
                                                        }

                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="ml-4">
                                                <h6>ตำแหน่ง :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h6>
                                            </div>
                                            <div class="ml-3 col-md-7 col-sm-12">
                                                <div class="form-group">
                                                    <select id="position" name="position_id" class="custom-select" autocomplete="off">
                                                        <!-- <option value="" hidden>All</option> -->
                                                        <?php
                                                        if (isset($_POST['employee'])) {
                                                            // Receive the selected division from the AJAX request
                                                            $selectedEmployee = $_POST['employee'];

                                                            // Query to retrieve departments based on the selected division
                                                            $sqlDropdown_type = "SELECT DISTINCT position.* FROM position
                                                            INNER JOIN position_info ON position_info.position_id = position.position_id 
                                                            INNER JOIN employee ON employee.card_id = position_info.card_id 
                                                            WHERE employee.card_id = ?";
                                                            $params = array($selectedEmployee);
                                                            $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);
                                                            $optionsPosition = '';
                                                            if ($resultDropdown_type === false) {
                                                                // Handle query errors
                                                                die(print_r(sqlsrv_errors(), true));
                                                            }
                                                            // Generate the options for the department dropdown
                                                            // $options = "<option value=''>All</option>";
                                                            while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                $optionsPosition .= "<option value='" . $row['position_id'] . "'>" . $row['name_thai'] . "</option>";
                                                            }
                                                            // Echo only the department options
                                                            echo $optionsPosition;
                                                        }
                                                        if (empty($_POST['employee'])) {
                                                            $sqlDropdown_type = "SELECT * FROM position";
                                                            $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type);
                                                            echo "<option value=''>All</option>";
                                                            if ($resultDropdown_type === false) {
                                                                die(print_r(sqlsrv_errors(), true));
                                                            }
                                                            if ($resultDropdown_type) {
                                                                while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                    echo "<option value='" . $row['position_id'] . "'>" . $row['name_thai'] . "</option>";
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="ml-4">
                                                <h6>รายชื่อพนักงาน :</h6>
                                            </div>
                                            <div class="ml-3 col-md-7 col-sm-12">
                                                <div class="form-group">
                                                    <select id="employee" name="card_id" class="custom-select form-control " data-live-search="true" autocomplete="off">
                                                        <!-- selectpicker -->
                                                        <option value="" hidden>All</option>
                                                        <?php
                                                        session_start();
                                                        if (isset($_POST['company'])) {
                                                            // Receive the selected division from the AJAX request
                                                            $selectedCompany = $_POST['company'];
                                                            $_SESSION['previousselectedCompany'] = $selectedCompany;

                                                            if ($selectedCompany != "") {
                                                                // Query to retrieve departments based on the selected division
                                                                $sqlDropdown_type = "SELECT * FROM employee
                                                            INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                            INNER JOIN section ON section.section_id = cost_center.section_id 
                                                            INNER JOIN department ON department.department_id = section.department_id 
                                                            INNER JOIN division ON division.division_id = department.division_id 
                                                            INNER JOIN location ON location.location_id = division.location_id 
                                                            INNER JOIN company ON company.company_id = location.company_id 
                                                            WHERE company.company_id = ?";
                                                                $params = array($selectedCompany);
                                                                $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);
                                                                if ($resultDropdown_type === false) {
                                                                    // Handle query errors
                                                                    die(print_r(sqlsrv_errors(), true));
                                                                }
                                                                // Generate the options for the department dropdown
                                                                $options = "<option value=''>All</option>";
                                                                while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                    $options .= "<option value='" . $row['card_id'] . "'>" . $row['prefix_thai'] . $row['firstname_thai'] . '   ' . $row['lastname_thai'] . "</option>";
                                                                }
                                                                // Echo only the department options
                                                                echo $options;
                                                            } else {
                                                                $sqlDropdown_type = "SELECT * FROM employee";
                                                                $params = array($selectedCompany);
                                                                $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);
                                                                if ($resultDropdown_type === false) {
                                                                    // Handle query errors
                                                                    die(print_r(sqlsrv_errors(), true));
                                                                }
                                                                // Generate the options for the department dropdown
                                                                $options = "<option value=''>All</option>";
                                                                while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                    $options .= "<option value='" . $row['card_id'] . "'>" . $row['prefix_thai'] . $row['firstname_thai'] . '   ' . $row['lastname_thai'] . "</option>";
                                                                }
                                                                // Echo only the department options
                                                                echo $options;
                                                            }
                                                        } elseif (isset($_POST['division'])) {
                                                            // Receive the selected division from the AJAX request
                                                            $selectedDivision = $_POST['division'];
                                                            $_SESSION['previousselectedDivision'] = $selectedDivision;
                                                            $previousselectedCompany = isset($_SESSION['previousselectedCompany']) ? $_SESSION['previousselectedCompany'] : null;
                                                            if ($selectedDivision != "") {
                                                                // Query to retrieve departments based on the selected division
                                                                $sqlDropdown_type = "SELECT * FROM employee
                                                            INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                            INNER JOIN section ON section.section_id = cost_center.section_id 
                                                            INNER JOIN department ON department.department_id = section.department_id 
                                                            INNER JOIN division ON division.division_id = department.division_id 
                                                            WHERE division.division_id = ?";
                                                                $params = array($selectedDivision);
                                                                $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);

                                                                if ($resultDropdown_type === false) {
                                                                    // Handle query errors
                                                                    die(print_r(sqlsrv_errors(), true));
                                                                }

                                                                // Generate the options for the department dropdown
                                                                $options = "<option value=''>All</option>";
                                                                while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                    $options .= "<option value='" . $row['card_id'] . "'>" . $row['prefix_thai'] . $row['firstname_thai'] . '   ' . $row['lastname_thai'] . "</option>";
                                                                }
                                                                // Echo only the department options
                                                                echo $options;
                                                            } else {
                                                                $sqlDropdown_type = "SELECT * FROM employee
                                                            INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                            INNER JOIN section ON section.section_id = cost_center.section_id 
                                                            INNER JOIN department ON department.department_id = section.department_id 
                                                            INNER JOIN division ON division.division_id = department.division_id 
                                                            INNER JOIN location ON location.location_id = division.location_id 
                                                            INNER JOIN company ON company.company_id = location.company_id 
                                                            WHERE company.company_id = ?";
                                                                $params = array($previousselectedCompany);
                                                                $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);

                                                                if ($resultDropdown_type === false) {
                                                                    // Handle query errors
                                                                    die(print_r(sqlsrv_errors(), true));
                                                                }

                                                                // Generate the options for the department dropdown
                                                                $options = "<option value=''>All</option>";
                                                                while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                    $options .= "<option value='" . $row['card_id'] . "'>" . $row['prefix_thai'] . $row['firstname_thai'] . '   ' . $row['lastname_thai'] . "</option>";
                                                                }
                                                                // Echo only the department options
                                                                echo $options;
                                                            }
                                                        } elseif (isset($_POST['department'])) {
                                                            // Receive the selected division from the AJAX request
                                                            $selectedDepartment = $_POST['department'];
                                                            $_SESSION['previousselectedDepartment'] = $selectedDepartment;
                                                            $previousselectedDivision = isset($_SESSION['previousselectedDivision']) ? $_SESSION['previousselectedDivision'] : null;

                                                            if ($selectedDepartment != "") {
                                                                // Query to retrieve departments based on the selected division
                                                                $sqlDropdown_type = "SELECT * FROM employee
                                                            INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                            INNER JOIN section ON section.section_id = cost_center.section_id 
                                                            INNER JOIN department ON department.department_id = section.department_id 
                                                            INNER JOIN division ON division.division_id = department.division_id 
                                                            WHERE department.department_id = ?";
                                                                $params = array($selectedDepartment);
                                                                $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);

                                                                if ($resultDropdown_type === false) {
                                                                    // Handle query errors
                                                                    die(print_r(sqlsrv_errors(), true));
                                                                }

                                                                // Generate the options for the department dropdown
                                                                $options = "<option value=''>All</option>";
                                                                while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                    $options .= "<option value='" . $row['card_id'] . "'>" . $row['prefix_thai'] . $row['firstname_thai'] . '   ' . $row['lastname_thai'] . "</option>";
                                                                }

                                                                // Echo only the department options
                                                                echo $options;
                                                            } else { // Query to retrieve departments based on the selected division
                                                                $sqlDropdown_type = "SELECT * FROM employee
                                                            INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                            INNER JOIN section ON section.section_id = cost_center.section_id 
                                                            INNER JOIN department ON department.department_id = section.department_id 
                                                            INNER JOIN division ON division.division_id = department.division_id 
                                                            WHERE division.division_id = ?";
                                                                $params = array($previousselectedDivision);
                                                                $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);

                                                                if ($resultDropdown_type === false) {
                                                                    // Handle query errors
                                                                    die(print_r(sqlsrv_errors(), true));
                                                                }

                                                                // Generate the options for the department dropdown
                                                                $options = "<option value=''>All</option>";
                                                                while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                    $options .= "<option value='" . $row['card_id'] . "'>" . $row['prefix_thai'] . $row['firstname_thai'] . '   ' . $row['lastname_thai'] . "</option>";
                                                                }

                                                                // Echo only the department options
                                                                echo $options;
                                                            }
                                                        } elseif (isset($_POST['section'])) {
                                                            // Receive the selected division from the AJAX request
                                                            $selectedSection = $_POST['section'];
                                                            $_SESSION['previousselectedSection'] = $selectedSection;
                                                            $previousselectedDepartment = isset($_SESSION['previousselectedDepartment']) ? $_SESSION['previousselectedDepartment'] : null;

                                                            if ($selectedSection != "") {
                                                                // Query to retrieve departments based on the selected division
                                                                $sqlDropdown_type = "SELECT * FROM employee
                                                            INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                            INNER JOIN section ON section.section_id = cost_center.section_id 
                                                            INNER JOIN department ON department.department_id = section.department_id 
                                                            INNER JOIN division ON division.division_id = department.division_id 
                                                            WHERE section.section_id = ?";
                                                                $params = array($selectedSection);
                                                                $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);

                                                                if ($resultDropdown_type === false) {
                                                                    // Handle query errors
                                                                    die(print_r(sqlsrv_errors(), true));
                                                                }

                                                                // Generate the options for the department dropdown
                                                                $options = "<option value=''>All</option>";
                                                                while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                    $options .= "<option value='" . $row['card_id'] . "'>" . $row['prefix_thai'] . $row['firstname_thai'] . '   ' . $row['lastname_thai'] . "</option>";
                                                                }

                                                                // Echo only the department options
                                                                echo $options;
                                                            } else {
                                                                $sqlDropdown_type = "SELECT * FROM employee
                                                            INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                            INNER JOIN section ON section.section_id = cost_center.section_id 
                                                            INNER JOIN department ON department.department_id = section.department_id 
                                                            INNER JOIN division ON division.division_id = department.division_id 
                                                            WHERE department.department_id = ?";
                                                                $params = array($previousselectedDepartment);
                                                                $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);

                                                                if ($resultDropdown_type === false) {
                                                                    // Handle query errors
                                                                    die(print_r(sqlsrv_errors(), true));
                                                                }

                                                                // Generate the options for the department dropdown
                                                                $options = "<option value=''>All</option>";
                                                                while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                    $options .= "<option value='" . $row['card_id'] . "'>" . $row['prefix_thai'] . $row['firstname_thai'] . '   ' . $row['lastname_thai'] . "</option>";
                                                                }

                                                                // Echo only the department options
                                                                echo $options;
                                                            }
                                                        } elseif (isset($_POST['cost_center'])) {
                                                            // Receive the selected division from the AJAX request
                                                            $selectedCost_center = $_POST['cost_center'];
                                                            $_SESSION['previousselectedCost_center'] = $selectedCost_center;
                                                            $previousselectedSection = isset($_SESSION['previousselectedSection']) ? $_SESSION['previousselectedSection'] : null;
                                                            if ($selectedCost_center != "") {
                                                                $sqlDropdown_type = "SELECT * FROM employee
                                                            INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                            INNER JOIN section ON section.section_id = cost_center.section_id 
                                                            INNER JOIN department ON department.department_id = section.department_id 
                                                            INNER JOIN division ON division.division_id = department.division_id 
                                                            WHERE cost_center.cost_center_id = ?";
                                                                $params = array($selectedCost_center);
                                                                $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);

                                                                if ($resultDropdown_type === false) {
                                                                    // Handle query errors
                                                                    die(print_r(sqlsrv_errors(), true));
                                                                }

                                                                // Generate the options for the department dropdown
                                                                $options = "<option value=''>All</option>";
                                                                while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                    $options .= "<option value='" . $row['card_id'] . "'>" . $row['prefix_thai'] . $row['firstname_thai'] . '   ' . $row['lastname_thai'] . "</option>";
                                                                }

                                                                // Echo only the department options
                                                                echo $options;
                                                            } else {
                                                                $sqlDropdown_type = "SELECT * FROM employee
                                                            INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                            INNER JOIN section ON section.section_id = cost_center.section_id 
                                                            INNER JOIN department ON department.department_id = section.department_id 
                                                            INNER JOIN division ON division.division_id = department.division_id 
                                                            WHERE section.section_id = ?";
                                                                $params = array($previousselectedSection);
                                                                $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);
                                                                if ($resultDropdown_type === false) {
                                                                    // Handle query errors
                                                                    die(print_r(sqlsrv_errors(), true));
                                                                }
                                                                // Generate the options for the department dropdown
                                                                $options = "<option value=''>All</option>";
                                                                while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                    $options .= "<option value='" . $row['card_id'] . "'>" . $row['prefix_thai'] . $row['firstname_thai'] . '   ' . $row['lastname_thai'] . "</option>";
                                                                }
                                                                // Echo only the department options
                                                                echo $options;
                                                            }
                                                        } else {
                                                            $sqlDropdown_type = "SELECT * FROM employee";
                                                            $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type, $params);
                                                            if ($resultDropdown_type === false) {
                                                                // Handle query errors
                                                                die(print_r(sqlsrv_errors(), true));
                                                            }
                                                            // Generate the options for the department dropdown
                                                            $options = "<option value=''>All</option>";
                                                            while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                $options .= "<option value='" . $row['card_id'] . "'>" . $row['prefix_thai'] . $row['firstname_thai'] . '   ' . $row['lastname_thai'] . "</option>";
                                                            }
                                                            // Echo only the department options
                                                            echo $options;
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2 col-sm-12 text-right">
                                            <div class="dropdown">
                                                <input class="btn btn-primary" name="submit" id="submit" type="submit" value="บันทึกรายการ">
                                            </div>
                                        </div>
                                        </section>
                                    </form>
                                    <?php
                                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                        if (isset($_POST['submit'])) {
                                            $income_type_id = $_POST['income_type_id'];
                                            $amount = $_POST['amount'];
                                            $whole_year_id = $_POST['whole_year_id'];
                                            $reason = $_POST['reason'];
                                            // ข้อมูลไฟล์
                                            $targetDir = "flie/";
                                            $targetFile = $targetDir . $_FILES["file"]["name"];
                                            $file_type = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                                            $income_target_id = 10;
                                            $active = 1;

                                            $card_id = isset($_POST['card_id']) ? $_POST['card_id'] : null; // ตรวจสอบว่ามีค่า card_id หรือไม่
                                            $company_id = isset($_POST['company_id']) ? $_POST['company_id'] : null; // ตรวจสอบว่ามีค่า company_id หรือไม่
                                            $division_id = isset($_POST['division_id']) ? $_POST['division_id'] : null; // ตรวจสอบว่ามีค่า division_id หรือไม่
                                            $department_id = isset($_POST['department_id']) ? $_POST['department_id'] : null; // ตรวจสอบว่ามีค่า department_id หรือไม่
                                            $section_id = isset($_POST['section_id']) ? $_POST['section_id'] : null; // ตรวจสอบว่ามีค่า section_id หรือไม่
                                            $cost_center_id = isset($_POST['cost_center_id']) ? $_POST['cost_center_id'] : null; // ตรวจสอบว่ามีค่า cost_center_id หรือไม่

                                            $sessioncompany = isset($_SESSION['sessioncompany']) ? $_SESSION['sessioncompany'] : null;
                                            $sessiondivision = isset($_SESSION['sessiondivision']) ? $_SESSION['sessiondivision'] : null;
                                            $sessiondepartment = isset($_SESSION['sessiondepartment']) ? $_SESSION['sessiondepartment'] : null;
                                            $sessionsection = isset($_SESSION['sessionsection']) ? $_SESSION['sessionsection'] : null;
                                            $contract_type_id = $_POST['contract_type_id'] ? $_POST['contract_type_id'] : null;
                                            $pl_id = $_POST['pl_id'] ? $_POST['pl_id'] : null;
                                            $position_id = $_POST['position_id'] ? $_POST['position_id'] : null;

                                            // ตรวจสอบค่าที่รับมา
                                            if ((empty($card_id)) && (isset($contract_type_id))  && (empty($pl_id))  && (empty($position_id))) {
                                                if ((empty($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    WHERE contract_type.contract_type_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $contract_type_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    WHERE contract_type.contract_type_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $contract_type_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN location ON location.location_id = division.location_id 
                                                                    INNER JOIN company ON company.company_id = location.company_id  
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    WHERE company.company_id = ? AND contract_type.contract_type_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $company_id, $contract_type_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN location ON location.location_id = division.location_id 
                                                                    INNER JOIN company ON company.company_id = location.company_id  
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    WHERE company.company_id = ? AND contract_type.contract_type_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $company_id, $contract_type_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    WHERE division.division_id = ? AND contract_type.contract_type_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $division_id, $contract_type_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id   
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    WHERE division.division_id = ? AND contract_type.contract_type_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $division_id, $contract_type_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    WHERE department.department_id = ? AND contract_type.contract_type_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $department_id, $contract_type_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id  
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    WHERE department.department_id = ? AND contract_type.contract_type_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $department_id, $contract_type_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    WHERE section.section_id = ? AND contract_type.contract_type_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $section_id, $contract_type_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    WHERE section.section_id = ? AND contract_type.contract_type_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $section_id, $contract_type_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    WHERE cost_center.cost_center_id = ? AND contract_type.contract_type_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $cost_center_id, $contract_type_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    WHERE cost_center.cost_center_id = ? AND contract_type.contract_type_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $cost_center_id, $contract_type_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                }
                                            } else if ((empty($card_id)) && (empty($contract_type_id))  && (isset($pl_id))  && (empty($position_id))) {
                                                if ((empty($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE pl.pl_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $pl_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE pl.pl_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $pl_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN location ON location.location_id = division.location_id 
                                                                    INNER JOIN company ON company.company_id = location.company_id  
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE company.company_id = ? AND pl.pl_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $company_id, $pl_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN location ON location.location_id = division.location_id 
                                                                    INNER JOIN company ON company.company_id = location.company_id  
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE company.company_id = ? AND AND pl.pl_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $company_id, $pl_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE division.division_id = ? AND pl.pl_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $division_id, $pl_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id   
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE division.division_id = ? AND pl.pl_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $division_id, $pl_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE department.department_id = ? AND pl.pl_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $department_id, $pl_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id  
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE department.department_id = ? AND pl.pl_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $department_id, $pl_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE section.section_id = ? AND pl.pl_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $section_id, $pl_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE section.section_id = ? AND pl.pl_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $section_id, $pl_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE cost_center.cost_center_id = ? AND pl.pl_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $cost_center_id, $pl_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE cost_center.cost_center_id = ? AND pl.pl_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $cost_center_id, $pl_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                }
                                            } else if ((empty($card_id)) && (empty($contract_type_id))  && (empty($pl_id))  && (isset($position_id))) {
                                                if ((empty($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN location ON location.location_id = division.location_id 
                                                                    INNER JOIN company ON company.company_id = location.company_id  
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE company.company_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $company_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN location ON location.location_id = division.location_id 
                                                                    INNER JOIN company ON company.company_id = location.company_id  
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE company.company_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $company_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE division.division_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $division_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id   
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE division.division_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $division_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE department.department_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $department_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id  
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE department.department_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $department_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE section.section_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $section_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE section.section_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $section_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE cost_center.cost_center_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $cost_center_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE cost_center.cost_center_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $cost_center_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                }
                                            } else if ((empty($card_id)) && (isset($contract_type_id))  && (isset($pl_id))  && (empty($position_id))) {
                                                if ((empty($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE contract_type.contract_type_id = ? AND pl.pl_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $contract_type_id, $pl_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE contract_type.contract_type_id = ? AND pl.pl_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $contract_type_id, $pl_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN location ON location.location_id = division.location_id 
                                                                    INNER JOIN company ON company.company_id = location.company_id  
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE company.company_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $company_id, $contract_type_id, $pl_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN location ON location.location_id = division.location_id 
                                                                    INNER JOIN company ON company.company_id = location.company_id  
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE company.company_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $company_id, $contract_type_id, $pl_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE division.division_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $division_id, $contract_type_id, $pl_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id   
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE division.division_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $division_id, $contract_type_id, $pl_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE department.department_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $department_id, $contract_type_id, $pl_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id  
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE department.department_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $department_id, $contract_type_id, $pl_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE section.section_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $section_id, $contract_type_id, $pl_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE section.section_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $section_id, $contract_type_id, $pl_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE cost_center.cost_center_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? 
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $cost_center_id, $contract_type_id, $pl_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    WHERE cost_center.cost_center_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $cost_center_id, $contract_type_id, $pl_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                }
                                            } else if ((empty($card_id)) && (isset($contract_type_id))  && (empty($pl_id))  && (isset($position_id))) {
                                                if ((empty($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE contract_type.contract_type_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $contract_type_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE contract_type.contract_type_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $contract_type_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN location ON location.location_id = division.location_id 
                                                                    INNER JOIN company ON company.company_id = location.company_id  
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE company.company_id = ? AND contract_type.contract_type_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $company_id, $contract_type_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN location ON location.location_id = division.location_id 
                                                                    INNER JOIN company ON company.company_id = location.company_id  
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE company.company_id = ? AND contract_type.contract_type_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $company_id, $contract_type_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE division.division_id = ? AND contract_type.contract_type_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $division_id, $contract_type_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id   
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE division.division_id = ? AND contract_type.contract_type_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $division_id, $contract_type_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE department.department_id = ? AND contract_type.contract_type_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $department_id, $contract_type_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id  
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE department.department_id = ? AND contract_type.contract_type_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $department_id, $contract_type_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE section.section_id = ? AND contract_type.contract_type_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $section_id, $contract_type_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE section.section_id = ? AND contract_type.contract_type_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $section_id, $contract_type_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE cost_center.cost_center_id = ? AND contract_type.contract_type_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $cost_center_id, $contract_type_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE cost_center.cost_center_id = ? AND contract_type.contract_type_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $cost_center_id, $contract_type_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                }
                                            } else if ((empty($card_id)) && (empty($contract_type_id))  && (isset($pl_id))  && (isset($position_id))) {
                                                if ((empty($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $pl_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $pl_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN location ON location.location_id = division.location_id 
                                                                    INNER JOIN company ON company.company_id = location.company_id  
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE company.company_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $company_id, $pl_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN location ON location.location_id = division.location_id 
                                                                    INNER JOIN company ON company.company_id = location.company_id  
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE company.company_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $company_id, $pl_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE division.division_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $division_id, $pl_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id   
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE division.division_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $division_id, $pl_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE department.department_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $department_id, $pl_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id  
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE department.department_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $department_id, $pl_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE section.section_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $section_id, $pl_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE section.section_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $section_id, $pl_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE cost_center.cost_center_id = ?  AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $cost_center_id, $pl_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE cost_center.cost_center_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $cost_center_id, $pl_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                }
                                            } else if ((empty($card_id)) && (isset($contract_type_id))  && (isset($pl_id))  && (isset($position_id))) {
                                                if ((empty($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE contract_type.contract_type_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $contract_type_id, $pl_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE contract_type.contract_type_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $contract_type_id, $pl_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN location ON location.location_id = division.location_id 
                                                                    INNER JOIN company ON company.company_id = location.company_id  
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE company.company_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $company_id, $contract_type_id, $pl_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN location ON location.location_id = division.location_id 
                                                                    INNER JOIN company ON company.company_id = location.company_id  
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE company.company_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $company_id, $contract_type_id, $pl_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id  
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE division.division_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $division_id, $contract_type_id, $pl_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN division ON division.division_id = department.division_id   
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE division.division_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $division_id, $contract_type_id, $pl_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id 
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE department.department_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $department_id, $contract_type_id, $pl_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN department ON department.department_id = section.department_id  
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE department.department_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $department_id, $contract_type_id, $pl_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE section.section_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $section_id, $contract_type_id, $pl_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE section.section_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $section_id, $contract_type_id, $pl_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                } else if ((isset($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                        $filename = $_FILES["file"]["name"];
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE cost_center.cost_center_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $cost_center_id, $contract_type_id, $pl_id, $position_id);


                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    } else {
                                                        // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                        $sqlMerge = "MERGE INTO income_target AS target
                                                                USING (
                                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                    FROM employee
                                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                    INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                                    INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                                    INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                                    INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                                    INNER JOIN position ON position.position_id = position_info.position_id
                                                                    WHERE cost_center.cost_center_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                                                ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                                ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                                WHEN MATCHED THEN
                                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                                target.amount = source.amount,
                                                                                target.whole_year_id = source.whole_year_id,
                                                                                target.reason = source.reason,
                                                                                target.active = source.active,
                                                                                target.evidence_name = source.evidence_name,
                                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                    INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                    VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                        // กำหนดค่าพารามิเตอร์
                                                        $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $cost_center_id, $contract_type_id, $pl_id, $position_id);

                                                        // ทำการ Merge ข้อมูล
                                                        $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                    }
                                                }
                                            } else if ((empty($card_id)) && (empty($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                    $filename = $_FILES["file"]["name"];
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO income_target AS target
                                                            USING (
                                                                SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                FROM employee
                                                            ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                            ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                            WHEN MATCHED THEN
                                                                UPDATE SET  target.income_type_id = source.income_type_id,
                                                                            target.amount = source.amount,
                                                                            target.whole_year_id = source.whole_year_id,
                                                                            target.reason = source.reason,
                                                                            target.active = source.active,
                                                                            target.evidence_name = source.evidence_name,
                                                                            target.evidence_data = source.evidence_data
                                                            WHEN NOT MATCHED THEN
                                                                INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile);


                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO income_target AS target
                                                            USING (
                                                                SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                FROM employee
                                                            ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                            ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                            WHEN MATCHED THEN
                                                                UPDATE SET  target.income_type_id = source.income_type_id,
                                                                            target.amount = source.amount,
                                                                            target.whole_year_id = source.whole_year_id,
                                                                            target.reason = source.reason,
                                                                            target.active = source.active,
                                                                            target.evidence_name = source.evidence_name,
                                                                            target.evidence_data = source.evidence_data
                                                            WHEN NOT MATCHED THEN
                                                                INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                }
                                            } else if ((empty($card_id)) && (isset($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                    $filename = $_FILES["file"]["name"];
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO income_target AS target
                                                            USING (
                                                                SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                FROM employee
                                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                INNER JOIN department ON department.department_id = section.department_id
                                                                INNER JOIN division ON division.division_id = department.division_id 
                                                                INNER JOIN location ON location.location_id = division.location_id 
                                                                INNER JOIN company ON company.company_id = location.company_id  
                                                                WHERE company.company_id = ?
                                                            ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                            ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                            WHEN MATCHED THEN
                                                                UPDATE SET  target.income_type_id = source.income_type_id,
                                                                            target.amount = source.amount,
                                                                            target.whole_year_id = source.whole_year_id,
                                                                            target.reason = source.reason,
                                                                            target.active = source.active,
                                                                            target.evidence_name = source.evidence_name,
                                                                            target.evidence_data = source.evidence_data
                                                            WHEN NOT MATCHED THEN
                                                                INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $company_id);


                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO income_target AS target
                                                            USING (
                                                                SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                FROM employee
                                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                INNER JOIN department ON department.department_id = section.department_id 
                                                                INNER JOIN division ON division.division_id = department.division_id  
                                                                INNER JOIN location ON location.location_id = division.location_id 
                                                                INNER JOIN company ON company.company_id = location.company_id  
                                                                WHERE company.company_id = ?
                                                            ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                            ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                            WHEN MATCHED THEN
                                                                UPDATE SET  target.income_type_id = source.income_type_id,
                                                                            target.amount = source.amount,
                                                                            target.whole_year_id = source.whole_year_id,
                                                                            target.reason = source.reason,
                                                                            target.active = source.active,
                                                                            target.evidence_name = source.evidence_name,
                                                                            target.evidence_data = source.evidence_data
                                                            WHEN NOT MATCHED THEN
                                                                INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $company_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                }
                                            } else if ((empty($card_id)) && (isset($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                    $filename = $_FILES["file"]["name"];
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO income_target AS target
                                                            USING (
                                                                SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                FROM employee
                                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                INNER JOIN department ON department.department_id = section.department_id
                                                                INNER JOIN division ON division.division_id = department.division_id  
                                                                WHERE division.division_id = ?
                                                            ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                            ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                            WHEN MATCHED THEN
                                                                UPDATE SET  target.income_type_id = source.income_type_id,
                                                                            target.amount = source.amount,
                                                                            target.whole_year_id = source.whole_year_id,
                                                                            target.reason = source.reason,
                                                                            target.active = source.active,
                                                                            target.evidence_name = source.evidence_name,
                                                                            target.evidence_data = source.evidence_data
                                                            WHEN NOT MATCHED THEN
                                                                INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $division_id);


                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO income_target AS target
                                                            USING (
                                                                SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                FROM employee
                                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                INNER JOIN department ON department.department_id = section.department_id 
                                                                INNER JOIN division ON division.division_id = department.division_id  
                                                                WHERE division.division_id = ?
                                                            ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                            ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                            WHEN MATCHED THEN
                                                                UPDATE SET  target.income_type_id = source.income_type_id,
                                                                            target.amount = source.amount,
                                                                            target.whole_year_id = source.whole_year_id,
                                                                            target.reason = source.reason,
                                                                            target.active = source.active,
                                                                            target.evidence_name = source.evidence_name,
                                                                            target.evidence_data = source.evidence_data
                                                            WHEN NOT MATCHED THEN
                                                                INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $division_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                }
                                            } else if ((empty($card_id)) && (isset($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                    $filename = $_FILES["file"]["name"];
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO income_target AS target
                                                            USING (
                                                                SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                FROM employee
                                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                INNER JOIN department ON department.department_id = section.department_id 
                                                                WHERE department.department_id = ?
                                                            ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                            ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                            WHEN MATCHED THEN
                                                                UPDATE SET  target.income_type_id = source.income_type_id,
                                                                            target.amount = source.amount,
                                                                            target.whole_year_id = source.whole_year_id,
                                                                            target.reason = source.reason,
                                                                            target.active = source.active,
                                                                            target.evidence_name = source.evidence_name,
                                                                            target.evidence_data = source.evidence_data
                                                            WHEN NOT MATCHED THEN
                                                                INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $department_id);


                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO income_target AS target
                                                            USING (
                                                                SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                FROM employee
                                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                INNER JOIN department ON department.department_id = section.department_id 
                                                                WHERE department.department_id = ?
                                                            ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                            ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                            WHEN MATCHED THEN
                                                                UPDATE SET  target.income_type_id = source.income_type_id,
                                                                            target.amount = source.amount,
                                                                            target.whole_year_id = source.whole_year_id,
                                                                            target.reason = source.reason,
                                                                            target.active = source.active,
                                                                            target.evidence_name = source.evidence_name,
                                                                            target.evidence_data = source.evidence_data
                                                            WHEN NOT MATCHED THEN
                                                                INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $department_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                }
                                            } else if ((empty($card_id)) && (isset($section_id)) && (empty($cost_center_id))) {
                                                // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                    $filename = $_FILES["file"]["name"];
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO income_target AS target
                                                            USING (
                                                                SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                FROM employee
                                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                WHERE section.section_id = ?
                                                            ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                            ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                            WHEN MATCHED THEN
                                                                UPDATE SET  target.income_type_id = source.income_type_id,
                                                                            target.amount = source.amount,
                                                                            target.whole_year_id = source.whole_year_id,
                                                                            target.reason = source.reason,
                                                                            target.active = source.active,
                                                                            target.evidence_name = source.evidence_name,
                                                                            target.evidence_data = source.evidence_data
                                                            WHEN NOT MATCHED THEN
                                                                INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $section_id);


                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO income_target AS target
                                                            USING (
                                                                SELECT ?,?,?,?,?,?,?, employee.card_id
                                                                FROM employee
                                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                                WHERE section.section_id = ?
                                                            ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                            ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                            WHEN MATCHED THEN
                                                                UPDATE SET  target.income_type_id = source.income_type_id,
                                                                            target.amount = source.amount,
                                                                            target.whole_year_id = source.whole_year_id,
                                                                            target.reason = source.reason,
                                                                            target.active = source.active,
                                                                            target.evidence_name = source.evidence_name,
                                                                            target.evidence_data = source.evidence_data
                                                            WHEN NOT MATCHED THEN
                                                                INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $section_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                }
                                            } else if ((empty($card_id)) && (isset($cost_center_id))) {
                                                // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                    $filename = $_FILES["file"]["name"];
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO income_target AS target
                                            USING (
                                                SELECT ?,?,?,?,?,?,?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                WHERE cost_center.cost_center_id = ?
                                            ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                            ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                            WHEN MATCHED THEN
                                                UPDATE SET  target.income_type_id = source.income_type_id,
                                                            target.amount = source.amount,
                                                            target.whole_year_id = source.whole_year_id,
                                                            target.reason = source.reason,
                                                            target.active = source.active,
                                                            target.evidence_name = source.evidence_name,
                                                            target.evidence_data = source.evidence_data
                                            WHEN NOT MATCHED THEN
                                                INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, $filename, $targetFile, $cost_center_id);


                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO income_target AS target
                                                USING (
                                                    SELECT ?,?,?,?,?,?,?, employee.card_id
                                                    FROM employee
                                                    INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                    WHERE cost_center.cost_center_id = ? 
                                                    ) AS source (income_type_id,amount,whole_year_id,reason ,active,evidence_name,evidence_data, card_id)
                                                    ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                WHEN MATCHED THEN
                                                    UPDATE SET  target.income_type_id = source.income_type_id,
                                                                target.amount = source.amount,
                                                                target.whole_year_id = source.whole_year_id,
                                                                target.reason = source.reason,
                                                                target.active = source.active,
                                                                target.evidence_name = source.evidence_name,
                                                                target.evidence_data = source.evidence_data
                                                                WHEN NOT MATCHED THEN
                                                                INSERT (income_type_id,amount,whole_year_id,reason,active, evidence_name, evidence_data,card_id) 
                                                                VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason,source.active,source.evidence_name, source.evidence_data, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($income_type_id, $amount, $whole_year_id, $reason, $active, null, null, $cost_center_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                }
                                            } else {
                                                if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                    $filename = $_FILES["file"]["name"];
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO income_target AS target
                                                            USING (
                                                                VALUES (?,?,?,?,?,?,?,?)
                                                            ) AS source (income_type_id,amount,whole_year_id,reason, card_id ,active,evidence_name,evidence_data)
                                                            ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                            WHEN MATCHED THEN
                                                                UPDATE SET target.income_type_id = source.income_type_id,
                                                                           target.amount = source.amount,
                                                                           target.whole_year_id = source.whole_year_id,
                                                                           target.reason = source.reason,
                                                                           target.active = source.active,
                                                                           target.evidence_name = source.evidence_name,
                                                                           target.evidence_data = source.evidence_data
                                                            WHEN NOT MATCHED THEN
                                                                INSERT (income_type_id,amount,whole_year_id,reason, card_id,active, evidence_name, evidence_data) 
                                                                VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason, source.card_id,source.active,source.evidence_name, source.evidence_data);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($income_type_id, $amount, $whole_year_id, $reason, $card_id, $active, $filename, $targetFile);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO income_target AS target
                                                 USING (
                                                    VALUES (?,?,?,?,?,?,?,?)
                                                    ) AS source (income_type_id,amount,whole_year_id,reason, card_id ,active,evidence_name,evidence_data)
                                                    ON target.card_id = source.card_id AND target.income_type_id = source.income_type_id
                                                    WHEN MATCHED THEN
                                                        UPDATE SET target.income_type_id = source.income_type_id,
                                                                   target.amount = source.amount,
                                                                   target.whole_year_id = source.whole_year_id,
                                                                   target.reason = source.reason,
                                                                   target.active = source.active,
                                                                   target.evidence_name = source.evidence_name,
                                                                   target.evidence_data = source.evidence_data
                                                    WHEN NOT MATCHED THEN
                                                        INSERT (income_type_id,amount,whole_year_id,reason, card_id,active, evidence_name, evidence_data) 
                                                        VALUES (source.income_type_id, source.amount, source.whole_year_id,source.reason, source.card_id,source.active,source.evidence_name, source.evidence_data);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($income_type_id, $amount, $whole_year_id, $reason, $card_id, $active, null, null);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                }
                                            }

                                            // ส่งคำสั่ง SQL ไปที่ฐานข้อมูล
                                            $resultInsert = sqlsrv_query($conn, $sqlMerge, $params);

                                            // ตรวจสอบการดำเนินการและแสดงผล
                                            if ($resultInsert === false) {
                                                // การจัดการข้อผิดพลาดในการ Insert
                                                die(print_r(sqlsrv_errors(), true));
                                            } else {
                                                // การแจ้งเตือนผลลัพธ์สำเร็จ
                                                echo '<script type="text/javascript">
                                                const Toast = Swal.mixin({
                                                    toast: true,
                                                    position: "top-end",
                                                    showConfirmButton: false,
                                                    timer: 1500,
                                                    timerProgressBar: true,
                                                    didOpen: (toast) => {
                                                        toast.onmouseenter = Swal.stopTimer;
                                                        toast.onmouseleave = Swal.resumeTimer;
                                                    }
                                                });
                                                Toast.fire({
                                                    icon: "success",
                                                    title: "บันทึกรายการรับสำเร็จ"
                                                });            
                                                </script>';

                                                echo "<meta http-equiv='refresh' content='2'>";

                                                exit; // จบการทำงานของสคริปต์ทันทีหลังจาก redirect
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal End -->

                    <!-- Modal Start -->
                    <div class="modal fade" id="editincome_targetModal" tabindex="-1" role="dialog" aria-labelledby="editincome_targetModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">

                                <div class="modal-body">
                                    <!-- Form for editing data -->
                                    <form id="editForm" method="post" action="income.php" enctype="multipart/form-data">
                                        <input type="hidden" id="income_target_id" name="income_target_id">
                                        <div class="form-group d-flex justify-content-center">
                                            <input type="text" class="col-12" id="editcard_id" name="card_id" autocomplete="off" disabled style="border: none; background-color: transparent; font-size: 25px; font-weight: bold; text-align: center;">
                                        </div>
                                        <div class="row">
                                            <div class="form-group d-flex">
                                                <div class="mt-2 col-5">
                                                    <h6><label for="editincome_type">ประเภทรายรับ :</label></h6>
                                                </div>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type="text" id="editincome_type" class="col-12" name="income_type" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                            </div>
                                        </div>
                                        <h6><label>Organization</label></h6>
                                        <div class="row">
                                            <div class="form-group d-flex">
                                                <div class="ml-3 mt-1 col-5">
                                                    <label for="editcompany">Company :</label>
                                                </div>&nbsp;
                                                <input type="text" id="editcompany" name="company" class="col-12" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group d-flex">
                                                <div class="ml-3 col-5">
                                                    <label for="editdivision">Division :</label>
                                                </div>&nbsp;&nbsp;
                                                <input type="text" id="editdivision" class="col-12" name="division" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group d-flex">
                                                <div class="ml-3 col-5">
                                                    <label for="editdepartment">Department :</label>
                                                </div>
                                                <input type="text" id="editdepartment" class="col-12" name="department" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group d-flex">
                                                <div class="ml-3 col-5">
                                                    <label for="editsection">Section :</label>
                                                </div>&nbsp;&nbsp;&nbsp;
                                                <input type="text" id="editsection" class="col-12" name="section" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group d-flex">
                                                <div class="ml-3 col-5">
                                                    <label for="editcost_center">Cost Center :</label>
                                                </div>
                                                <input type="text" id="editcost_center" class="col-12" name="cost_center" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group d-flex">
                                                <div class="mt-1 col-5">
                                                    <h6><label for="editcontract_type">ประเภทพนักงาน :</label></h6>
                                                </div>&nbsp;&nbsp;
                                                <input type="text" id="editcontract_type" class="col-12" name="contract_type" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group d-flex">
                                                <div class="mt-1 col-5">
                                                    <h6><label for="editpl">ระดับการทำงาน :</label></h6>
                                                </div>&nbsp;&nbsp;
                                                <input type="text" id="editpl" class="col-12" name="pl" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group d-flex">
                                                <div class="mt-1 col-5">
                                                    <h6><label for="editposition">ตำแหน่ง :</label></h6>
                                                </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type="text" id="editposition" class="col-12" name="position" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <h6><label for="editamount">จำนวนเงิน (บาท)</label></h6>
                                            <input type="number" class="form-control" id="editamount" name="amount" autocomplete="off">
                                        </div>
                                        <div class="form-group">
                                            <h6><label for="editwhole_year">คำนวณเงินได้ทั้งปี</label></h6>
                                            <select type="text" class="custom-select" id="editwhole_year" name="whole_year" required="true" autocomplete="off">
                                            <option value="" selected disabled>เลือกคำนวณเงินได้</option>
                                                <?php
                                                $sqlDropdown_type = "SELECT * FROM whole_year";
                                                $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type);
                                                if ($resultDropdown_type === false) {
                                                    die(print_r(sqlsrv_errors(), true));
                                                }
                                                if ($resultDropdown_type) {
                                                    while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                        $whole_yearValue = $row['whole_year'];
                                                        echo "<option value='" . $row['whole_year_id'] . "'>" . $whole_yearValue . "</option>";
                                                    }
                                                }
                                                ?>

                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <h6><label for="editreason">หมายเหตุ</label></h6>
                                            <input type="text" class="form-control" id="editreason"  placeholder="กรอกหมายเหตุ" name="reason" autocomplete="off">
                                        </div>
                                        <div class="form-group">
                                            <div class="form-group">
                                                <h6><label for="editfile">แนบหลักฐาน (ถ้ามี)</label></h6>
                                                <input type="file" id="editfile" name="file" class="form-control">
                                            </div>
                                            <div class="text-right mt-3">
                                                <button type="submit" class="btn btn-primary" name="update_income_target">บันทึกการแก้ไข</button>
                                            </div>
                                    </form>

                                    <?php
                                    // -- UPDATE employee_payment based on employee_payment_id -->

                                    // -- UPDATE Income Type on income_id -->
                                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                        if (isset($_POST['update_income_target'])) {
                                            $income_target_id  = $_POST['income_target_id'];
                                            $amount = $_POST['amount'];
                                            $whole_year = $_POST['whole_year'];
                                            $reason = $_POST['reason'];
                                            $targetDir = "flie/";
                                            $targetFile = $targetDir . $_FILES["file"]["name"];
                                            $file_type = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));


                                            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                                                $filename = $_FILES["file"]["name"];

                                                header('Location: deduct.php');


                                                // อัปเดตค่าของฟิลด์ income_type
                                                $sqlUpdate = "UPDATE income_target SET amount = '$amount', whole_year_id = '$whole_year', reason = '$reason', evidence_name = '$filename', evidence_data = '$targetFile' WHERE income_target_id = '$income_target_id'";
                                                $stmt = sqlsrv_query($conn, $sqlUpdate);

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
                                                title: "แก้ไขข้อมูลสำเร็จ"
                                            });
                                            </script>';

                                                    echo "<meta http-equiv='refresh' content='1'>";

                                                    exit; // จบการทำงานของสคริปต์ทันทีหลังจาก redirect
                                                }
                                            } else {
                                                $sqlUpdate = "UPDATE income_target SET amount = '$amount',whole_year_id = '$whole_year',reason = '$reason' WHERE income_target_id = '$income_target_id'";
                                                $stmt = sqlsrv_query($conn, $sqlUpdate);

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
                                                title: "แก้ไขข้อมูลสำเร็จ"
                                            });
                                            </script>';

                                                    echo "<meta http-equiv='refresh' content='1'>";

                                                    exit; // จบการทำงานของสคริปต์ทันทีหลังจาก redirect
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal End -->

                </div>
            </div> <?php include('../admin/include/footer.php'); ?>
        </div>
        
</body>

</html>