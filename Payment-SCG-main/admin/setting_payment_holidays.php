<?php include('../admin/include/header.php') ?>


<body>

    <?php include('../admin/include/navbar.php') ?>
    <?php include('../admin/include/sidebar.php') ?>
    <?php include('../admin/include/scripts.php') ?>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <h3>ตั้งค่ารายได้พนักงานนักขัตฤกษ์</h3>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="setting_payment_circle.php">ตั้งค่ารอบวันที่</a></li>
                                    <li class="breadcrumb-item"><a href="setting_payment_split.php">ตั้งค่างวดการจ่าย</a></li>
                                    <li class="breadcrumb-item"><a href="setting_payment_set_closing_date.php">ตั้งค่าวันปิดงวด</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">ตั้งค่ารายได้พนักงานนักขัตฤกษ์</li>
                                    <li class="breadcrumb-item"><a href="setting_payment_social_security.php">ตั้งค่าประกันสังคม</a></li>
                                    <li class="breadcrumb-item"><a href="setting_payment_form.php">ตั้งค่าแบบฟอร์ม</a></li>
                                    <li class="breadcrumb-item"><a href="setting_payment_notification.php">ตั้งค่าการแจ้งเตือน</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12-sm-12">
                        <div class="card-box pd-20 pt-10 height-100-p">
                            <div class="pd-10">
                                <div class="title ">
                                    <div class="row">
                                        <h4 class="col-7">รายละเอียดทั้งหมด</h4>
                                        <div class="col-1"></div>
                                        <div class="text-right col-4">
                                            <button onclick="openPay_holiday_Setting_Modal()" type="button" class="btn insert-btn-pay" data-dismiss="modal">
                                                + เพิ่มการตั้งค่ารายได้พนักงานในวันหยุดนักขัตฤกษ์
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
                                                <th style="text-align: center;">รายได้พนักงาน</th>
                                                <th style="text-align: center;">รหัสพนักงาน </th>
                                                <th style="text-align: center;">ชื่อ-สกุล </th>
                                                <th style="text-align: cemter;">การจัดการ</th>
                                                <th>
                                                    Company / Division / Department / Section / Cost Center / ประเภทพนักงาน / ระดับการทำงาน / ตำแหน่ง
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- SELECT ค่า cicle -->
                                            <?php
                                            // เตรียมคำสั่ง SQL
                                            $sql = "SELECT pay_holiday_id,pay_holiday_set.pay_holiday as pay_holiday,scg_employee_id, employee.card_id as card_id, company.name_thai as company,division.name_thai as division,department.name_thai as department,section.name_thai as section,cost_center.cost_center_code as cost_center,contract_type.name_thai as contract_type,pl.pl_name_thai as pl,position.name_thai as position , prefix_thai, firstname_thai, lastname_thai FROM pay_holiday  
                                        INNER JOIN pay_holiday_set ON pay_holiday_set.pay_holiday_set_id  = pay_holiday.pay_holiday_set_id 
                                        INNER JOIN employee ON employee.card_id = pay_holiday.card_id  
                                        LEFT JOIN cost_center ON cost_center.cost_center_id  = employee.cost_center_organization_id
                                        LEFT JOIN section ON section.section_id = cost_center.section_id 
                                        LEFT JOIN department ON department.department_id = section.department_id 
                                        LEFT JOIN division ON division.division_id = department.division_id 
                                        LEFT JOIN location ON location.location_id = division.location_id
                                        LEFT JOIN company ON company.company_id = location.company_id
                                        LEFT JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                        LEFT JOIN pl_info ON pl_info.card_id  = pay_holiday.card_id
                                        LEFT JOIN pl ON pl.pl_id = pl_info.pl_id 
                                        LEFT JOIN position_info ON position_info.card_id  = pay_holiday.card_id
                                        LEFT JOIN position ON position.position_id  = position_info.position_id";
                                            $params = array();
                                            // ดึงข้อมูลจากฐานข้อมูล
                                            $stmt = sqlsrv_query($conn, $sql, $params);
                                            // ตรวจสอบการทำงานของคำสั่ง SQL
                                            if ($stmt === false) {
                                                die(print_r(sqlsrv_errors(), true));
                                            }

                                            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                                echo "<tr>";
                                                $displayHoliday = ($row["pay_holiday"] === 0) ? "ได้รับค่าแรง" : (($row["pay_holiday"] === 1) ? "ไม่ได้รับค่าแรง" : $row["pay_holiday"]);
                                                echo "<td  style='text-align: center;''>" . $displayHoliday . "</td>";
                                                echo "<td >" . $row["scg_employee_id"] . "</td>";
                                                echo "<td>" . $row["prefix_thai"] . $row["firstname_thai"] . '  ' . $row["lastname_thai"] . "</td>";
                                                echo "<td><div class='flex'  style='justify-content: right; padding-right: 35px;'>",
                                                '<button type="button" name="delete_pay_holiday" class="delete-btn-pay" onclick="confirmDeletePay_holiday(\'' . $row['pay_holiday_id'] . '\');"><i class="fa-solid fa-trash-can"></i></button> &nbsp;';
                                                echo "<button type='button' class='edit-btn-pay' onclick='openEdit_Pay_holiday_Setting_Modal
                                            (
                                            \"" . $row['pay_holiday_id'] . "\",
                                            \"" . $row['card_id'] . "\",
                                            \"" . $row['pay_holiday'] . "\",
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
                                            \"" . $row['position'] . "\"
                                            
                                            );'>";
                                                echo "<div ><i class='fa-solid fa-pencil'></i>";
                                                echo "</button>";
                                                echo "<td>" . $row["company"] . ' / ' . $row["division"] . ' / ' . $row["department"] . ' / ' . $row["section"] . ' / ' . $row["cost_center"] . ' / ' . $row["contract_type"] . ' / ' . $row["pl"] . ' / ' . $row["position"] . "</td>";
                                                echo "</tr>";
                                            }
                                            // ปิดการเชื่อมต่อ
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5 col-sm-5">
                                    <div class="form-group">
                                        <label style="font-size:24px;"><b></b></label>
                                        <div class="justify-content-left">
                                            <button style="font-size:20px;" onclick="location.href='setting_payment_general.php'" type="button" class="btn btn-default" data-dismiss="modal"><i class="fa-solid fa-circle-left"> </i> ย้อนกลับ</button>
                                            <!-- color:#AAAAAA -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Start -->
                    <div class="modal fade" id="insertpay_holidayModal" tabindex="-1" role="dialog" aria-labelledby="insertpay_holidayModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <div class="text-light-green">
                                        <h4><label>+ เพิ่มการตั้งค่ารายได้พนักงาน </label></h4>
                                    </div>
                                </div>
                                <div class="modal-body">
                                    <h6>รายได้พนักงาน</h6>
                                    <form method="post" action="setting_payment_holidays.php">
                                        <section>
                                            <div class="col-md-7 col-sm-7">
                                                <div class="form-group pt-1">
                                                    <select id="pay_holiday_setDropdown" name="pay_holiday_set_id" class="custom-select" required="true" autocomplete="off">
                                                        <option value="" disabled selected>เลือกรายได้พนักงาน</option>
                                                        <?php
                                                        $sqlDropdown_type = "SELECT * FROM pay_holiday_set";
                                                        $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type);

                                                        if ($resultDropdown_type === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                        if ($resultDropdown_type) {
                                                            while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                                $pay_holiday_setValue = ($row['pay_holiday'] == 0) ? 'ได้รับค่าแรง' : 'ไม่ได้รับค่าแรง';
                                                                echo "<option value='" . $row['pay_holiday_set_id'] . "'>" . $pay_holiday_setValue . "</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <h6>กลุ่มเป้าหมาย</h6>
                                            <div class="mt-2 ml-2">
                                                <h6>Organization :</h6>
                                            </div>

                                            <div class="row">
                                                <div class="mt-2 ml-5">
                                                    Company&nbsp;
                                                </div>
                                                <div class="ml-4 col-md-7 col-sm-12">
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
                                                <div class="mt-2 ml-5">
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
                                                <div class="mt-2 ml-5">
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
                                                <div class="mt-2 ml-5">
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
                                                <div class="mt-2 ml-5">
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
                                                <div class="mt-2 ml-4">
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
                                                <div class="mt-2 ml-4">
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
                                                <div class="mt-2 ml-4">
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
                                            <div class="mt-2 text-right">
                                                <div class="dropdown">
                                                    <input class="btn btn-primary" name="submit" id="submit" type="submit" value="บันทึกรายการ" name="submit">
                                                </div>
                                            </div>
                                        </section>
                                    </form>
                                    <?php

                                    // -------- INSERT ค่า Employee ตาม card_id PK-->

                                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                        if (isset($_POST['submit'])) {
                                            $pay_holiday_set_id = $_POST['pay_holiday_set_id'];
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
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee 
                                                INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                WHERE contract_type.contract_type_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $contract_type_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                INNER JOIN department ON department.department_id = section.department_id 
                                                INNER JOIN division ON division.division_id = department.division_id 
                                                INNER JOIN location ON location.location_id = division.location_id 
                                                INNER JOIN company ON company.company_id = location.company_id 
                                                INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                WHERE company.company_id = ? AND contract_type.contract_type_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessioncompany, $contract_type_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                INNER JOIN department ON department.department_id = section.department_id 
                                                INNER JOIN division ON division.division_id = department.division_id 
                                                INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                WHERE division.division_id = ?  AND contract_type.contract_type_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessiondivision, $contract_type_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                INNER JOIN department ON department.department_id = section.department_id 
                                                INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                WHERE department.department_id = ? AND contract_type.contract_type_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessiondepartment, $contract_type_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                WHERE section.section_id = ? AND contract_type.contract_type_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessionsection, $contract_type_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                WHERE cost_center.cost_center_id = ? AND contract_type.contract_type_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $cost_center_id, $contract_type_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                }
                                            } else if ((empty($card_id)) && (empty($contract_type_id))  && (isset($pl_id))  && (empty($position_id))) {
                                                if ((empty($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee 
                                                INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                WHERE pl.pl_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $pl_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
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
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessioncompany, $pl_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                INNER JOIN department ON department.department_id = section.department_id 
                                                INNER JOIN division ON division.division_id = department.division_id 
                                                INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                WHERE division.division_id = ?  AND pl.pl_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessiondivision,  $pl_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                INNER JOIN department ON department.department_id = section.department_id 
                                                INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                WHERE department.department_id = ? AND pl.pl_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessiondepartment,  $pl_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                WHERE section.section_id = ? AND pl.pl_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessionsection, $pl_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                WHERE cost_center.cost_center_id = ? AND pl.pl_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $cost_center_id, $pl_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                }
                                            } else if ((empty($card_id)) && (empty($contract_type_id))  && (empty($pl_id))  && (isset($position_id))) {
                                                if ((empty($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee 
                                                INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                INNER JOIN position ON position.position_id = position_info.position_id
                                                WHERE position.position_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
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
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessioncompany, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                INNER JOIN department ON department.department_id = section.department_id 
                                                INNER JOIN division ON division.division_id = department.division_id 
                                                INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                INNER JOIN position ON position.position_id = position_info.position_id
                                                WHERE division.division_id = ?  AND position.position_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessiondivision,  $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                INNER JOIN department ON department.department_id = section.department_id 
                                                INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                INNER JOIN position ON position.position_id = position_info.position_id
                                                WHERE department.department_id = ? AND position.position_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessiondepartment,  $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                INNER JOIN position ON position.position_id = position_info.position_id
                                                WHERE section.section_id = ? AND position.position_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessionsection, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                INNER JOIN position ON position.position_id = position_info.position_id
                                                WHERE cost_center.cost_center_id = ? AND position.position_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $cost_center_id, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                }
                                            } else if ((empty($card_id)) && (isset($contract_type_id))  && (isset($pl_id))  && (empty($position_id))) {
                                                if ((empty($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee 
                                                INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                WHERE contract_type.contract_type_id = ? AND pl.pl_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $contract_type_id, $pl_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
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
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessioncompany, $contract_type_id, $pl_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                INNER JOIN department ON department.department_id = section.department_id 
                                                INNER JOIN division ON division.division_id = department.division_id 
                                                INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                WHERE division.division_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? 
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessiondivision, $contract_type_id, $pl_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                INNER JOIN department ON department.department_id = section.department_id 
                                                INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                WHERE department.department_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessiondepartment, $contract_type_id, $pl_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                WHERE section.section_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? 
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessionsection, $contract_type_id, $pl_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                WHERE cost_center.cost_center_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $cost_center_id, $contract_type_id, $pl_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                }
                                            } else if ((empty($card_id)) && (isset($contract_type_id))  && (empty($pl_id))  && (isset($position_id))) {
                                                if ((empty($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee 
                                                INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id

                                                INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                INNER JOIN position ON position.position_id = position_info.position_id
                                                WHERE contract_type.contract_type_id = ? AND position.position_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $contract_type_id, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
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
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessioncompany, $contract_type_id, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                INNER JOIN department ON department.department_id = section.department_id 
                                                INNER JOIN division ON division.division_id = department.division_id 
                                                INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                INNER JOIN position ON position.position_id = position_info.position_id
                                                WHERE division.division_id = ? AND contract_type.contract_type_id = ? AND position.position_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessiondivision, $contract_type_id, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                INNER JOIN department ON department.department_id = section.department_id 
                                                INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                INNER JOIN position ON position.position_id = position_info.position_id
                                                WHERE department.department_id = ? AND contract_type.contract_type_id = ? AND position.position_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessiondepartment, $contract_type_id, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                INNER JOIN position ON position.position_id = position_info.position_id
                                                WHERE section.section_id = ? AND contract_type.contract_type_id = ? AND position.position_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessionsection, $contract_type_id, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                INNER JOIN position ON position.position_id = position_info.position_id
                                                WHERE cost_center.cost_center_id = ? AND contract_type.contract_type_id = ? AND position.position_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $cost_center_id, $contract_type_id, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                }
                                            } else if ((empty($card_id)) && (empty($contract_type_id))  && (isset($pl_id))  && (isset($position_id))) {
                                                if ((empty($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee 
                                                INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                INNER JOIN position ON position.position_id = position_info.position_id
                                                WHERE pl.pl_id = ? AND position.position_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $pl_id, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
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
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessioncompany, $pl_id, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                INNER JOIN department ON department.department_id = section.department_id 
                                                INNER JOIN division ON division.division_id = department.division_id 
                                                INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                INNER JOIN position ON position.position_id = position_info.position_id
                                                WHERE division.division_id = ?  AND pl.pl_id = ? AND position.position_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessiondivision, $pl_id, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                INNER JOIN department ON department.department_id = section.department_id 
                                                INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                INNER JOIN position ON position.position_id = position_info.position_id
                                                WHERE department.department_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessiondepartment, $pl_id, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                INNER JOIN position ON position.position_id = position_info.position_id
                                                WHERE section.section_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessionsection, $pl_id, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                INNER JOIN position ON position.position_id = position_info.position_id
                                                WHERE cost_center.cost_center_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $cost_center_id, $pl_id, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                }
                                            } else if ((empty($card_id)) && (isset($contract_type_id))  && (isset($pl_id))  && (isset($position_id))) {
                                                if ((empty($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee 
                                                INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                INNER JOIN position ON position.position_id = position_info.position_id
                                                WHERE contract_type.contract_type_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $contract_type_id, $pl_id, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
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
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessioncompany, $contract_type_id, $pl_id, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
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
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessiondivision, $contract_type_id, $pl_id, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
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
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessiondepartment, $contract_type_id, $pl_id, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($section_id)) && (empty($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN section ON section.section_id = cost_center.section_id 
                                                INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                INNER JOIN position ON position.position_id = position_info.position_id
                                                WHERE section.section_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $sessionsection, $contract_type_id, $pl_id, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                } else if ((isset($cost_center_id))) {
                                                    // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                    $sqlMerge = "MERGE INTO pay_holiday AS target
                                            USING (
                                                SELECT ?, employee.card_id
                                                FROM employee
                                                INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                                INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                INNER JOIN pl_info ON pl_info.card_id = employee.card_id
                                                INNER JOIN pl ON pl.pl_id = pl_info.pl_id
                                                INNER JOIN position_info ON position_info.card_id = employee.card_id
                                                INNER JOIN position ON position.position_id = position_info.position_id
                                                WHERE cost_center.cost_center_id = ? AND contract_type.contract_type_id = ? AND pl.pl_id = ? AND position.position_id = ?
                                            ) AS source (pay_holiday_set_id, card_id)
                                            ON target.card_id = source.card_id
                                            WHEN MATCHED THEN
                                                UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                            WHEN NOT MATCHED THEN
                                                INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                    // กำหนดค่าพารามิเตอร์
                                                    $params = array($pay_holiday_set_id, $cost_center_id, $contract_type_id, $pl_id, $position_id);

                                                    // ทำการ Merge ข้อมูล
                                                    $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                    if ($stmt === false) {
                                                        die(print_r(sqlsrv_errors(), true));
                                                    }
                                                }
                                            } else if ((empty($card_id)) && (empty($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                $sqlMerge = "MERGE INTO pay_holiday AS target
                                        USING (
                                            SELECT ?, employee.card_id
                                            FROM employee
                                        ) AS source (pay_holiday_set_id, card_id)
                                        ON target.card_id = source.card_id
                                        WHEN MATCHED THEN
                                            UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                        WHEN NOT MATCHED THEN
                                            INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                // กำหนดค่าพารามิเตอร์
                                                $params = array($pay_holiday_set_id);

                                                // ทำการ Merge ข้อมูล
                                                $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                if ($stmt === false) {
                                                    die(print_r(sqlsrv_errors(), true));
                                                }
                                            } else if ((empty($card_id)) && (isset($company_id)) && (empty($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                $sqlMerge = "MERGE INTO pay_holiday AS target
                                        USING (
                                            SELECT ?, employee.card_id
                                            FROM employee
                                            INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                            INNER JOIN section ON section.section_id = cost_center.section_id 
                                            INNER JOIN department ON department.department_id = section.department_id 
                                            INNER JOIN division ON division.division_id = department.division_id 
                                            INNER JOIN location ON location.location_id = division.location_id 
                                            INNER JOIN company ON company.company_id = location.company_id 
                                            WHERE company.company_id = ?
                                        ) AS source (pay_holiday_set_id, card_id)
                                        ON target.card_id = source.card_id
                                        WHEN MATCHED THEN
                                            UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                        WHEN NOT MATCHED THEN
                                            INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                // กำหนดค่าพารามิเตอร์
                                                $params = array($pay_holiday_set_id, $sessioncompany);

                                                // ทำการ Merge ข้อมูล
                                                $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                if ($stmt === false) {
                                                    die(print_r(sqlsrv_errors(), true));
                                                }
                                            } else if ((empty($card_id)) && (isset($division_id)) && (empty($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                $sqlMerge = "MERGE INTO pay_holiday AS target
                                        USING (
                                            SELECT ?, employee.card_id
                                            FROM employee
                                            INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                            INNER JOIN section ON section.section_id = cost_center.section_id 
                                            INNER JOIN department ON department.department_id = section.department_id 
                                            INNER JOIN division ON division.division_id = department.division_id 
                                            WHERE division.division_id = ?
                                        ) AS source (pay_holiday_set_id, card_id)
                                        ON target.card_id = source.card_id
                                        WHEN MATCHED THEN
                                            UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                        WHEN NOT MATCHED THEN
                                            INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                // กำหนดค่าพารามิเตอร์
                                                $params = array($pay_holiday_set_id, $sessiondivision);

                                                // ทำการ Merge ข้อมูล
                                                $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                if ($stmt === false) {
                                                    die(print_r(sqlsrv_errors(), true));
                                                }
                                            } else if ((empty($card_id)) && (isset($department_id)) && (empty($section_id)) && (empty($cost_center_id))) {
                                                // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                $sqlMerge = "MERGE INTO pay_holiday AS target
                                         USING (
                                             SELECT ?, employee.card_id
                                             FROM employee
                                             INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                             INNER JOIN section ON section.section_id = cost_center.section_id 
                                             INNER JOIN department ON department.department_id = section.department_id 
                                             WHERE department.department_id = ?
                                         ) AS source (pay_holiday_set_id, card_id)
                                         ON target.card_id = source.card_id
                                         WHEN MATCHED THEN
                                             UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                         WHEN NOT MATCHED THEN
                                             INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                // กำหนดค่าพารามิเตอร์
                                                $params = array($pay_holiday_set_id, $sessiondepartment);

                                                // ทำการ Merge ข้อมูล
                                                $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                if ($stmt === false) {
                                                    die(print_r(sqlsrv_errors(), true));
                                                }
                                            } else if ((empty($card_id)) && (isset($section_id)) && (empty($cost_center_id))) {
                                                // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                $sqlMerge = "MERGE INTO pay_holiday AS target
                                        USING (
                                            SELECT ?, employee.card_id
                                            FROM employee
                                            INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                            INNER JOIN section ON section.section_id = cost_center.section_id 
                                            WHERE section.section_id = ?
                                        ) AS source (pay_holiday_set_id, card_id)
                                        ON target.card_id = source.card_id
                                        WHEN MATCHED THEN
                                            UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                        WHEN NOT MATCHED THEN
                                            INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                // กำหนดค่าพารามิเตอร์
                                                $params = array($pay_holiday_set_id, $sessionsection);

                                                // ทำการ Merge ข้อมูล
                                                $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                if ($stmt === false) {
                                                    die(print_r(sqlsrv_errors(), true));
                                                }
                                            } else if ((empty($card_id)) && (isset($cost_center_id))) {
                                                // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                $sqlMerge = "MERGE INTO pay_holiday AS target
                                        USING (
                                            SELECT ?, employee.card_id
                                            FROM employee
                                            INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id 
                                            WHERE cost_center.cost_center_id = ?
                                        ) AS source (pay_holiday_set_id, card_id)
                                        ON target.card_id = source.card_id
                                        WHEN MATCHED THEN
                                            UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                        WHEN NOT MATCHED THEN
                                            INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                // กำหนดค่าพารามิเตอร์
                                                $params = array($pay_holiday_set_id, $cost_center_id);

                                                // ทำการ Merge ข้อมูล
                                                $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                if ($stmt === false) {
                                                    die(print_r(sqlsrv_errors(), true));
                                                }
                                            } else {
                                                // คำสั่ง SQL สำหรับ Insert ข้อมูลเมื่อ card_id และ ทั้งหมด ว่าง
                                                $sqlMerge = "MERGE INTO pay_holiday AS target
                                         USING (
                                            VALUES (?, ?)
                                         ) AS source (pay_holiday_set_id, card_id)
                                         ON target.card_id = source.card_id
                                         WHEN MATCHED THEN
                                             UPDATE SET target.pay_holiday_set_id = source.pay_holiday_set_id
                                         WHEN NOT MATCHED THEN
                                             INSERT (pay_holiday_set_id, card_id) VALUES (source.pay_holiday_set_id, source.card_id);";

                                                // กำหนดค่าพารามิเตอร์
                                                $params = array($pay_holiday_set_id, $card_id);

                                                // ทำการ Merge ข้อมูล
                                                $stmt = sqlsrv_query($conn, $sqlMerge, $params);

                                                if ($stmt === false) {
                                                    die(print_r(sqlsrv_errors(), true));
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
                                                title: "บันทึกข้อมูลรายได้พนักงานสำเร็จ"
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
                    <!-- Modal Start -->
                    <div class="modal fade" id="editpay_holidayModal" tabindex="-1" role="dialog" aria-labelledby="editpay_holidayModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">แก้ไขรายได้ในวันหยุด</h5>
                                </div>
                                <div class="modal-body">
                                    <!-- Form for editing data -->
                                    <form id="editForm" method="post" action="setting_payment_holidays.php">
                                        <input type="hidden" id="pay_holiday_id" name="pay_holiday_id">
                                        <input type="hidden" id="card_id" name="card_id">
                                        <div class="form-group d-flex justify-content-center">
                                            <input type="text" class="col-12" id="name" name="first_name_thai" autocomplete="off" disabled style="border: none; background-color: transparent; font-size: 25px; font-weight: bold; text-align: center;">
                                        </div>
                                        <h6><label for="pay_holiday_set_id">รายได้ในวันหยุด</label></h6>
                                        <select id="pay_holiday_set_id" name="pay_holiday_set_id" class="custom-select" required="true" autocomplete="off">
                                        <option value="" selected disabled>เลือกรายได้ในวันหยุด</option>
                                            <?php
                                            $sqlDropdown_type = "SELECT * FROM pay_holiday_set";
                                            $resultDropdown_type = sqlsrv_query($conn, $sqlDropdown_type);

                                            if ($resultDropdown_type === false) {
                                                die(print_r(sqlsrv_errors(), true));
                                            }
                                            if ($resultDropdown_type) {
                                                while ($row = sqlsrv_fetch_array($resultDropdown_type, SQLSRV_FETCH_ASSOC)) {
                                                    $pay_holiday_setValue = ($row['pay_holiday'] == 0) ? 'ได้รับค่าแรง' : 'ไม่ได้รับค่าแรง';
                                                    echo "<option value='" . $row['pay_holiday_set_id'] . "'>" . $pay_holiday_setValue . "</option>";
                                                }
                                            }
                                            ?>

                                        </select>
                                        <div class="mt-3">
                                            <h6><label>Organization :</label></h6>
                                        </div>
                                        <div class="row">
                                            <div class="form-group d-flex">
                                                <div class="ml-3 mt-2 col-5">
                                                    <label for="editcompany">Company :</label>
                                                </div>&nbsp;
                                                <input type="text" class="col-10 mt-1" id="editcompany" name="company" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group d-flex">
                                                <div class="ml-3 mt-2 col-5">
                                                    <label for="editdivision">Division :</label>
                                                </div>&nbsp;&nbsp;
                                                <input type="text" class="col-10 mt-1" id="editdivision" name="division" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group d-flex">
                                                <div class="ml-3 mt-2 col-5">
                                                    <label for="editdepartment">Department :</label>
                                                </div>
                                                <input type="text" class="col-10 mt-1" id="editdepartment" name="department" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group d-flex">
                                                <div class="ml-3 mt-2 col-5">
                                                    <label for="editsection">Section :</label>
                                                </div>&nbsp;&nbsp;&nbsp;
                                                <input type="text" class="col-10 mt-1" id="editsection" name="section" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group d-flex">
                                                <div class="ml-3 mt-2 col-5">
                                                    <label for="editcost_center">Cost Center :</label>
                                                </div>&nbsp;
                                                <input type="text" class="col-10 mt-1 " id="editcost_center" name="cost_center" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group d-flex">
                                                <div class="mt-2 col-5">
                                                    <h6><label for="editcontract_type">ประเภทพนักงาน :</label></h6>
                                                </div>
                                                <input type="text" class="col-10 mt-1 ml-3" id="editcontract_type" name="contract_type" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group d-flex">
                                                <div class="mt-2 col-5">
                                                    <h6><label for="editpl">ระดับการทำงาน :</label></h6>
                                                </div>
                                                <input type="text" class="col-10 mt-1 ml-3" id="editpl" name="pl" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group d-flex">
                                                <div class="mt-2 col-5">
                                                    <h6><label for="editposition">ตำแหน่ง :</label></h6>
                                                </div>&nbsp;&nbsp;&nbsp;
                                                <input type="text" class="col-10 mt-1 ml-3" id="editposition" name="position" autocomplete="off" disabled style="border: none; background-color: transparent;">
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary" name="update_pay_holiday">บันทึกการแก้ไข</button>
                                        </div>
                                    </form>
                                    <?php
                                    // -- UPDATE employee_payment based on employee_payment_id -->

                                    // -- UPDATE Income Type on income_id -->
                                    if (isset($_POST['update_pay_holiday'])) {
                                        $pay_holiday_id  = $_POST['pay_holiday_id'];
                                        $pay_holiday_set_id = $_POST['pay_holiday_set_id'];

                                        // อัปเดตค่าของฟิลด์ income_type
                                        $sqlUpdate = "UPDATE pay_holiday SET pay_holiday_set_id = '$pay_holiday_set_id' WHERE pay_holiday_id = '$pay_holiday_id'";
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
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal End -->
                </div>
            </div>
            <?php include('../admin/include/footer.php'); ?>
        </div>
    </div>
</body>

</html>