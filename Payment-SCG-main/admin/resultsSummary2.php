<!-- หน้าสรุปผลการคำนวณ  -->
<?php include('../admin/include/header.php') ?>
<link rel="stylesheet" href="../vendors/styles/resultsSummary.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script> -->


<body>

    <?php include('../admin/include/navbar.php') ?>
    <?php include('../admin/include/sidebar.php') ?>
    <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>


    <?php
    $currentDateTime = date('Y-m-d'); // วันที่และเวลาปัจจุบันในรูปแบบ Y-m-d H:i:s
    echo $currentDateTime;
    ?>
   <script>
        function exportToExcel() {
            // รับตารางทั้งหมด
            var table = document.getElementById("myTable");

            // สร้าง Workbook และ Worksheet ในไฟล์ Excel
            var wb = XLSX.utils.table_to_book(table);
            var ws = wb.Sheets["Sheet1"];

            // บันทึกไฟล์ Excel
            XLSX.writeFile(wb, "Payment <?php echo $currentDateTime; ?>.xlsx");
        }

        function exportToExcel2() {
            // รับตารางทั้งหมด
            var table = document.getElementById("myTable2");

            // สร้าง Workbook และ Worksheet ในไฟล์ Excel
            var wb = XLSX.utils.table_to_book(table);
            var ws = wb.Sheets["Sheet1"];

            // บันทึกไฟล์ Excel
            XLSX.writeFile(wb, "Payment <?php echo $currentDateTime; ?>.xlsx");
        }

        function exportToPdf() {
            var doc = new jsPDF();
            doc.autoTable({
                html: '#myTable'
            });
            doc.save("Payment_<?php echo $currentDateTime; ?>.pdf");
        }

        function exportToPdf2() {
            var doc = new jsPDF();
            doc.autoTable({
                html: '#myTable2'
            });
            doc.save("Payment_<?php echo $currentDateTime; ?>.pdf");
        }
    </script>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <h3>คำนวณเงินเดือน : Calculator Payment</h3>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="employee_payment.php">เงินเดือนพนักงาน</a></li>
                                    <li class="breadcrumb-item"><a href="income.php">รายรับ/รายจ่าย</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">คำนวณเงินเดือน</li>
                                    <li class="breadcrumb-item"><a href="history_payment.php">ประวัติการคำนวณ</a></li>
                                    <li class="breadcrumb-item"><a href="setting_payment.php">ตั้งค่า</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                        <div class="card-box-2 pd-30 pt-10 height-100-p">
                            <div class="bar d-flex justify-content-center align-items-center">
                                <a style="color:#338ac6;">สรุปผลการคำนวณ <i class="fa-regular fa-file"></i></a>
                            </div>
                            <div class="row pt-2 justify-content-end pr-3">
                                <button type="submit" id="closeSetBtn" onclick="window.location.href='insert_result2.php'" class="ml-auto">ทำการปิดงวด</button>
                            </div>
                            <div class="row pt-2">
                                <div class="col-lg-12 col-md-8 col-sm-8">
                                    <div class="card-box pd-30 pt-20" style="box-shadow: 1px 4px 8px rgba(0, 0, 0, 0.2);">
                                        <div class="row" style="display: flex; justify-content: space-between;">
                                            <div class="text-left">
                                                <h3>รายละเอียดรายรับ / รายจ่าย</h3>
                                            </div>
                                            <div>
                                                <button id="pdf" onclick="exportToPdf()">PDF <i class="fa-solid fa-file-pdf"></i></button>
                                                <button id="excel" onclick="exportToExcel()">EXCEL <i class="fa-solid fa-file-excel"></i></button>
                                            </div>
                                        </div>
                                        <div class="table-responsive mt-2">
                                            <p style="text-align: right; color: #dc3545;">
                                                หมายเหตุ : หากต้องการ export excel จะได้รับข้อมูลที่ตามที่ท่านเลือกจำนวนข้อมูลที่แสดงเท่านั้น
                                            </p>
                                            <table class="data-table2 table stripe hover nowrap" id="myTable">
                                                <thead>
                                                    <tr>
                                                        <th>รหัส</th>
                                                        <th>ชื่อ-สกุล</th>
                                                        <th>บริษัท</th>
                                                        <th>แผนก</th>
                                                        <th>ตำแหน่ง</th>
                                                        <th>มาทำงาน</th>
                                                        <th>วันทำงาน</th>
                                                        <th>เงินเดือน</th>
                                                        <th>รายรับ</th>
                                                        <th>จำนวนเงินรายรับ</th>
                                                        <th>รายจ่าย</th>
                                                        <th>จำนวนเงินรายจ่าย</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- SELECT ค่า employee_payment -->
                                                    <?php
                                                    // เตรียมคำสั่ง SQL
                                                    $sql = "SELECT 
                                                    scg_employee_id,
                                                    prefix_thai,
                                                    firstname_thai,
                                                    lastname_thai,
                                                    nickname_thai,
                                                    company.name_thai AS company,
                                                    department.name_thai AS department,
                                                    position.name_thai AS position,
                                                    salary_per_month,
                                                    (CASE WHEN (row_number() OVER (PARTITION BY employee.card_id ORDER BY employee.card_id) = 1) OR (row_number() OVER (PARTITION BY itt.income_type ORDER BY itt.income_type) = 1)
                                                                THEN itt.income_type
                                                                END) AS 'income_type',
                                                    (CASE WHEN (row_number() OVER (PARTITION BY employee.card_id ORDER BY employee.card_id) = 1) OR (row_number() OVER (PARTITION BY deduct_type.deduct_type ORDER BY deduct_type.deduct_type) = 1)
                                                                THEN deduct_type.deduct_type
                                                                END) AS 'deduct_type',
                                                    (CASE WHEN (row_number() OVER (PARTITION BY employee.card_id ORDER BY employee.card_id) = 1) OR (row_number() OVER (PARTITION BY it.amount ORDER BY it.amount) = 1)
                                                                THEN it.amount
                                                                END) AS 'itamount',
                                                    (CASE WHEN (row_number() OVER (PARTITION BY employee.card_id ORDER BY employee.card_id) = 1) OR (row_number() OVER (PARTITION BY dt.amount ORDER BY dt.amount) = 1)
                                                                THEN dt.amount
                                                                END) AS 'dtamount',
                                                    (CASE WHEN row_number() OVER (PARTITION BY employee.card_id ORDER BY employee.card_id) = 1
                                                                THEN ep.salary_per_month
                                                                END) AS 'salary_amount'
                                                                                                                                
                                                FROM 
                                                    employee
                                                    LEFT JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id
                                                    LEFT JOIN section ON section.section_id = cost_center.section_id 
                                                    LEFT JOIN department ON department.department_id = section.department_id 
                                                    LEFT JOIN division ON division.division_id = department.division_id 
                                                    LEFT JOIN location ON location.location_id = division.location_id
                                                    LEFT JOIN company ON company.company_id = location.company_id
                                                    LEFT JOIN position_info ON position_info.card_id = employee.card_id
                                                    LEFT JOIN position ON position.position_id = position_info.position_id
                                                    LEFT JOIN employee_payment ep ON ep.card_id = employee.card_id
                                                    LEFT JOIN income_target it ON it.card_id = employee.card_id
                                                    LEFT JOIN income_type itt ON itt.income_type_id = it.income_type_id
                                                    LEFT JOIN deduct_target dt ON dt.card_id = employee.card_id
                                                    LEFT JOIN deduct_type ON dt.deduct_type_id = deduct_type.deduct_type_id
                                                    LEFT JOIN split ON split.card_id = employee.card_id 
                                                WHERE 
                                                    split.split_set_id = '2' AND ( it.active = '1' OR dt.active = '1')";
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
                                                        echo "<tr>";
                                                        echo "<td>" . $row["scg_employee_id"] . "</td>";
                                                        echo "<td>" . $row["prefix_thai"] . '' . $row["firstname_thai"] . ' ' . $row["lastname_thai"] . "</td>";
                                                        echo "<td>" . $row["company"] . "</td>";
                                                        echo "<td>" . $row["department"] . "</td>";
                                                        echo "<td>" . $row["position"] . "</td>";
                                                        echo "<td>" . "จำนวนกี่วัน" . "</td>";
                                                        echo "<td>" . "จำนวนกี่วัน"  . "</td>";
                                                        echo "<td class='text-center'>" . number_format($row["salary_amount"],2) . "</td>";
                                                        if ($row["itamount"] !== NULL) {
                                                            echo "<td class='text-center'>" . $row["income_type"] . "</td>";
                                                        } else {
                                                            echo "<td class='text-center'></td>";
                                                        }
                                                        if ($row["itamount"] !== NULL) {
                                                            echo "<td class='text-center'>" . number_format($row["itamount"], 2) . "</td>";
                                                        } else {
                                                            echo "<td class='text-center'></td>";
                                                        }
                                                        if ($row["dtamount"] !== NULL) {
                                                            echo "<td class='text-center'>" . $row["deduct_type"] . "</td>";
                                                        } else {
                                                            echo "<td class='text-center'></td>";
                                                        }
                                                        if ($row["dtamount"] !== NULL) {
                                                            echo "<td class='text-center'>" .  number_format($row["dtamount"],2) . "</td>";
                                                        } else {
                                                            echo "<td class='text-center'></td>";
                                                        }
                                                        echo '</tr></td>';
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row pt-5">
                                <div class="col-lg-12 col-md-8 col-sm-8">
                                    <div class="card-box pd-30 pt-20" style="box-shadow: 1px 4px 8px rgba(0, 0, 0, 0.2);">
                                        <div class="row" style="display: flex; justify-content: space-between;">
                                            <div class="text-left">
                                                <h3>สรุปผลการคำนวน</h3>
                                            </div>
                                            <div>
                                                <button id="pdf" onclick="exportToPdf()">PDF <i class="fa-solid fa-file-pdf"></i></button>
                                                <button id="excel" onclick="exportToExcel2()">EXCEL <i class="fa-solid fa-file-excel"></i></button>
                                            </div>
                                        </div>
                                        <div class="table-responsive mt-2">
                                            <p style="text-align: right; color: #dc3545;">
                                                หมายเหตุ : หากต้องการ export excel จะได้รับข้อมูลที่ตามที่ท่านเลือกจำนวนข้อมูลที่แสดงเท่านั้น
                                            </p>
                                            <table class="data-table2 table stripe hover nowrap" id="myTable2">
                                                <thead>
                                                    <tr>
                                                        <th>รหัส</th>
                                                        <th>ชื่อ-สกุล</th>
                                                        <th>บริษัท</th>
                                                        <th>แผนก</th>
                                                        <th>ตำแหน่ง</th>
                                                        <th>เงินเดือน</th>
                                                        <th>รายรับทั้งหมด</th>
                                                        <th>รายจ่ายทั้งหมด</th>
                                                        <th>เงินที่ต้องจ่าย</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- SELECT ค่า employee_payment -->
                                                    <?php
                                                    // เตรียมคำสั่ง SQL
                                                    $sql = "SELECT 
                                                    scg_employee_id,
                                                    prefix_thai,
                                                    firstname_thai,
                                                    lastname_thai,
                                                    nickname_thai,
                                                    company.name_thai AS company,
                                                    department.name_thai AS department,
                                                    position.name_thai AS position,
                                                    salary_per_month,                                        
                                                    it.total_income,
                                                    dt.total_deduction,
                                                    split.split_set_id
                                      FROM 
                                        employee e
                                        LEFT JOIN cost_center ON cost_center.cost_center_id = e.cost_center_organization_id
                                        LEFT JOIN section ON section.section_id = cost_center.section_id 
                                        LEFT JOIN department ON department.department_id = section.department_id 
                                        LEFT JOIN division ON division.division_id = department.division_id 
                                        LEFT JOIN location ON location.location_id = division.location_id
                                        LEFT JOIN company ON company.company_id = location.company_id
                                        LEFT JOIN position_info ON position_info.card_id = e.card_id
                                        LEFT JOIN position ON position.position_id = position_info.position_id
                                        LEFT JOIN (SELECT card_id, SUM(amount) AS total_income FROM income_target WHERE active = '1' GROUP BY card_id) it ON e.card_id = it.card_id
                                        LEFT JOIN (SELECT card_id, SUM(amount) AS total_deduction FROM deduct_target WHERE active = '1' GROUP BY card_id) dt ON e.card_id = dt.card_id
                                        LEFT JOIN split ON split.card_id = e.card_id 
                                        LEFT JOIN employee_payment ep ON ep.card_id = e.card_id
                                      WHERE
                                        split.split_set_id = '2';
                                      ";
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
                                                        echo "<tr>";
                                                        echo "<td>" . $row["scg_employee_id"] . "</td>";
                                                        echo "<td>" . $row["prefix_thai"] . '' . $row["firstname_thai"] . ' ' . $row["lastname_thai"] . "</td>";
                                                        echo "<td>" . $row["company"] . "</td>";
                                                        echo "<td>" . $row["department"] . "</td>";
                                                        echo "<td>" . $row["position"] . "</td>";
                                                        echo "<td class='text-center'>" .  number_format($row["salary_per_month"],2) . "</td>";
                                                        if ($row["total_income"] !== NULL) {
                                                            echo "<td class='text-center'>" .  number_format($row["total_income"],2) . "</td>";
                                                        } else {
                                                            echo "<td class='text-center'>" . 0 . "</td>";
                                                        }
                                                        if ($row["total_deduction"] !== NULL) {
                                                            echo "<td class='text-center'>" .  number_format($row["total_deduction"],2) . "</td>";
                                                        } else {
                                                            echo "<td class='text-center'>" . 0 . "</td>";
                                                        }
                                                        $sum_payment = $row["salary_per_month"] + $row["total_income"] - $row["total_deduction"];
                                                        echo "<td class='text-center'>" . number_format($sum_payment,2)."</td>";
                                                        echo '</tr></td>';
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php include('../admin/include/footer.php'); ?>
                    </div>
                </div>

                <?php include('../admin/include/scripts.php') ?>
            </div>
        </div>
    </div>
    </div>
</body>

</html>