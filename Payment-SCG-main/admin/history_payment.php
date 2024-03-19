
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
                                    <li class="breadcrumb-item"><a href="employee_payment.php">จัดการเงินเดือนพนักงาน</a></li>
                                    <li class="breadcrumb-item"><a href="income.php">รายรับ/รายจ่าย</a></li>
                                    <li class="breadcrumb-item"><a href="calculator_payment2.php">คำนวณเงินเดือน</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">ประวัติการคำนวณ</li>
                                    <li class="breadcrumb-item"><a href="setting_payment_income_deduct.php">ตั้งค่ารายรับรายจ่าย</a></li>
                                    <li class="breadcrumb-item"><a href="setting_payment_general.php">ตั้งค่าทั่วไป</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                        <div class="card-box-2 pd-30 pt-10">
                            <div class="card-box pd-30 pt-20 mt-3" style="box-shadow: 1px 4px 8px rgba(0, 0, 0, 0.2);">
                                <div class="row" style="display: flex; justify-content: space-between;">
                                    <div class="text-left">
                                        <h3>รายละเอียดรายรับ / รายจ่ายทั้งหมด</h3>
                                    </div>
                                    <div>
                                        <button id="excel" onclick="exportToExcel()">EXCEL <i class="fa-solid fa-file-excel"></i></button>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <p style="text-align: right; color: #dc3545;">
                                        หมายเหตุ : หากต้องการจะ export ข้อมูล จะได้รับข้อมูลที่ตามที่ท่านเลือกจำนวนข้อมูลที่แสดงเท่านั้น
                                    </p>
                                </div>
                                <div class="table-responsive mt-2">
                                    <table class="data-table2 table stripe hover nowrap" id="myTable">
                                        <thead>
                                            <tr>
                                            <th>รหัส</th>
                                            <th>ชื่อ-สกุล</th>
                                            <th>บริษัท</th>
                                            <th>ส่วนงาน</th>
                                            <th>แผนก</th>
                                            <th>หน่วยงาน</th>
                                            <th>Cost Center</th>
                                            <th>ประเภทพนักงาน</th>
                                            <th>ระดับปฏิบัติการ</th>
                                            <th>ตำแหน่ง</th>
                                            <th>มาทำงาน</th>
                                            <th>วันทำงาน</th>
                                            <th>เงินเดือน</th>
                                            <th>รายรับ</th>
                                            <th>จำนวนเงินรายรับ</th>
                                            <th>รายจ่าย</th>
                                            <th>จำนวนเงินรายจ่าย</th>
                                            <th>จำนวนงวด</th>
                                            <th>วันปิดงวด</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- SELECT ค่า employee_payment -->
                                            <?php
                                            // เตรียมคำสั่ง SQL
                                            $sql = "SELECT e.scg_employee_id as scg_employee_id,e.card_id as card_id,e.prefix_thai as prefix_thai,e.firstname_thai as firstname_thai,e.lastname_thai as lastname_thai,
                                            c.name_thai as company,d2.name_thai as division,d.name_thai as department,s.name_thai as section,cc.cost_center_code as cost_center,ct.name_thai as contract_type,pl.pl_name_thai as pl,position.name_thai as position,
                                            log.salary_per_month,log.income_type,log.income_amount ,log.deduct_type ,log.deduct_amount ,log.[datetime] ,log.split  FROM log_salary log
                                            LEFT JOIN employee e ON e.card_id = log.card_id  
                                            LEFT JOIN cost_center cc ON cc.cost_center_id = e.cost_center_organization_id 
                                            LEFT JOIN section s ON s.section_id = cc.section_id 
                                            LEFT JOIN department d ON d.department_id = s.department_id 
                                            LEFT JOIN division d2 ON d2.division_id = d.division_id 
                                            LEFT JOIN location l ON l.location_id = d2.location_id 
                                            LEFT JOIN company c ON c.company_id = l.company_id 
                                            LEFT JOIN contract_type ct ON ct.contract_type_id = e.contract_type_id 
                                            LEFT JOIN pl_info On pl_info.card_id = e.card_id 
                                            LEFT JOIN pl ON pl.pl_id = pl_info.pl_id 
                                            LEFT JOIN position_info ON position_info.card_id = e.card_id 
                                            LEFT JOIN position ON position.position_id = position_info.position_id";
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
                                                $time_start  = $row["datetime"]; // สร้างวัตถุ DateTime
                                                $formattedDateStart = $time_start->format('d-m-Y');

                                                echo "<tr >";
                                                echo "<td >" . $row["scg_employee_id"] . "</td>";
                                                echo "<td >" . $row["prefix_thai"] . '' . $row["firstname_thai"] . ' ' . $row["lastname_thai"] . "</td>";
                                                echo "<td >" . $row["company"] . "</td>";
                                                echo "<td >" . $row["division"] . "</td>";
                                                echo "<td >" . $row["department"] . "</td>";
                                                echo "<td >" . $row["section"] . "</td>";
                                                echo "<td >" . $row["cost_center"] . "</td>";
                                                echo "<td >" . $row["contract_type"] . "</td>";
                                                echo "<td >" . $row["pl"] . "</td>";
                                                echo "<td >" . $row["position"] . "</td>";
                                                echo "<td ></td>";
                                                echo "<td ></td>";
                                                echo "<td >" . $row["salary_per_month"] . "</td>";
                                                echo "<td >" . $row["income_type"] . "</td>";
                                                echo "<td >" . $row["income_amount"] . "</td>";
                                                echo "<td >" . $row["deduct_type"] . "</td>";
                                                echo "<td >" . $row["deduct_amount"] . "</td>";
                                                echo "<td >" . $row["split"] . "</td>";
                                                echo "<td >" . $formattedDateStart . "</td>";
                                                echo '</tr></td>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row pt-1">
                                <div class="col-lg-12 col-md-8 col-sm-8">
                                    <div class="card-box pd-30 pt-20 mt-5" style="box-shadow: 1px 4px 8px rgba(0, 0, 0, 0.2);">
                                        <div class="row" style="display: flex; justify-content: space-between;">
                                            <div class="text-left">
                                                <h3>สรุปผลการคำนวณทั้งหมด</h3>
                                            </div>
                                            <div>
                                                <button id="excel" onclick="exportToExcel2()">EXCEL <i class="fa-solid fa-file-excel"></i></button>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <p style="text-align: right; color: #dc3545;">
                                                หมายเหตุ : หากต้องการจะ export ข้อมูล จะได้รับข้อมูลที่ตามที่ท่านเลือกจำนวนข้อมูลที่แสดงเท่านั้น
                                            </p>
                                        </div>
                                        <div class="table-responsive mt-2">
                                            <table class="data-table2 table stripe hover nowrap" id="myTable2">
                                                <thead>
                                                    <tr>
                                                        <th>ดูสลิป</th>
                                                        <th>รหัส</th>
                                                        <th>ชื่อ-สกุล</th>
                                                        <th>บริษัท</th>
                                                        <th>ส่วนงาน</th>
                                                        <th>แผนก</th>
                                                        <th>หน่วยงาน</th>
                                                        <th>Cost Center</th>
                                                        <th>ประเภทพนักงาน</th>
                                                        <th>ระดับปฏิบัติการ</th>
                                                        <th>ตำแหน่ง</th>
                                                        <th>เงินเดือน</th>
                                                        <th>รายรับทั้งหมด</th>
                                                        <th>รายจ่ายทั้งหมด</th>
                                                        <th>เงินที่ต้องจ่าย</th>
                                                        <th>จำนวนงวด</th>
                                                        <th>วันปิดงวด</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- SELECT ค่า employee_payment -->
                                                    <?php
                                                    // เตรียมคำสั่ง SQL
                                                    $sql = "SELECT scg_employee_id,prefix_thai,firstname_thai,lastname_thai,company.name_thai as company
                                                            ,division.name_thai as division,department.name_thai as department,section.name_thai as section,
                                                            cost_center.cost_center_code as cost_center,contract_type.name_thai as contract_type,pl.pl_name_thai as pl,
                                                            position.name_thai as position ,ep.salary_per_month as salary_per_month ,total_income,total_deduct ,
                                                            split_set.split as split ,date FROM log_sum_salary
                                                    LEFT JOIN employee e ON e.card_id = log_sum_salary.card_id 
                                                    LEFT JOIN cost_center ON cost_center.cost_center_id = e.cost_center_organization_id
                                                    LEFT JOIN section ON section.section_id = cost_center.section_id 
                                                    LEFT JOIN department ON department.department_id = section.department_id 
                                                    LEFT JOIN division ON division.division_id = department.division_id 
                                                    LEFT JOIN location ON location.location_id = division.location_id
                                                    LEFT JOIN company ON company.company_id = location.company_id
                                                    LEFT JOIN contract_type ON contract_type.contract_type_id = e.contract_type_id
                                                    LEFT JOIN pl_info ON pl_info.card_id = e.card_id
                                                    LEFT JOIN pl ON pl.pl_id = pl_info.pl_id
                                                    LEFT JOIN position_info ON position_info.card_id = e.card_id
                                                    LEFT JOIN position ON position.position_id = position_info.position_id
                                                    LEFT JOIN split ON split.card_id = e.card_id
                                                    LEFT JOIN split_set ON split_set.split_set_id = split.split_set_id
                                                    LEFT JOIN employee_payment ep ON ep.card_id = e.card_id";
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
                                                        $time_start  = $row["date"]; // สร้างวัตถุ DateTime
                                                        $formattedDateStart = $time_start->format('d-m-Y');
                                                        echo "<tr>";
                                                        echo "<td><a href='check_split.php?scg_employee_id=" . $row['scg_employee_id'] . "&formattedDateStart=" . $formattedDateStart . "&split=" . $row["split"] ."'>สลิป</a></td>";

                                                        echo "<td>" . $row["scg_employee_id"] . "</td>";
                                                        echo "<td>" . $row["prefix_thai"] . '' . $row["firstname_thai"] . ' ' . $row["lastname_thai"] . "</td>";
                                                        echo "<td>" . $row["company"] . "</td>";
                                                        echo "<td>" . $row["division"] . "</td>";
                                                        echo "<td>" . $row["department"] . "</td>";
                                                        echo "<td>" . $row["section"] . "</td>";
                                                        echo "<td>" . $row["cost_center"] . "</td>";
                                                        echo "<td>" . $row["contract_type"] . "</td>";
                                                        echo "<td>" . $row["pl"] . "</td>";
                                                        echo "<td>" . $row["position"] . "</td>";
                                                        echo "<td class='text-center'>" . $row["salary_per_month"] . "</td>";
                                                        echo "<td>" . $row["total_income"] . "</td>";
                                                        echo "<td>" . $row["total_deduct"] . "</td>";
                                                        $sum_payment = $row["salary_per_month"] + $row["total_income"] - $row["total_deduct"];
                                                        echo "<td class='text-center'>$sum_payment </td>";
                                                        echo "<td >" . $row["split"] . "</td>";
                                                        echo "<td >" . $formattedDateStart . "</td>";
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