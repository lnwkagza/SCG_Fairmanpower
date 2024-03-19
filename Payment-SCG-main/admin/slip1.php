<!-- หน้าสรุปผลการคำนวณ  -->
<?php include('../admin/include/header.php') ?>
<link rel="stylesheet" href="../vendors/styles/resultsSummary.css">
<link rel="stylesheet" href="../vendors/styles/slip12.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script> -->


<body>

    <?php include('../admin/include/navbar.php') ?>
    <?php include('../admin/include/sidebar.php') ?>
    <?php include('../admin/include/scripts.php') ?>
    <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>


    <?php
    $currentDateTime = date('Y-m-d'); // วันที่และเวลาปัจจุบันในรูปแบบ Y-m-d H:i:s
    echo $currentDateTime;
    ?>
    <?php 
    $scg_employee_id = $_SESSION['scg_employee_id'] ;
    $formattedDateStart = $_SESSION['formattedDateStart'] ;
    $sql1 = "SELECT *,
	permission.name as permission, permission.permission_id as permissionID, contract_type.name_eng as contracts, contract_type.name_thai as contract_th,
	section.name_thai as section, department.name_thai as department 
	
	FROM employee
	INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id
	INNER JOIN section ON section.section_id = cost_center.section_id
	INNER JOIN department ON department.department_id = section.department_id
	INNER JOIN permission ON permission.permission_id = employee.permission_id
	INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id WHERE employee.scg_employee_id = ? ";
    $params1 = array($scg_employee_id);
    $stmt1 = sqlsrv_query($conn, $sql1, $params1);
    $row1 = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC);
    $employee_card_id = $row1['card_id'];
    
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
                                <h3>สลิปเงินเดือน : slip Payment</h3>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">สลิปเงินเดือน</a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><a href="salary_summary.php">ประวัติเงินเดือน</li></a>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                        <div class="card-box pd-30 pt-10">
                            <div class="pay-slip" style="display: flex; flex-direction: column; text-align: end; margin-bottom: 5px;">
                                <span style="font-size: 20px; font-weight: bold;">สลิปเงินเดือน</span>
                                <span style="font-size: 20px; font-weight: bold;">Pay Slip</span>
                            </div>
                            <div class="info" style="display: flex; justify-content: space-between;">
                                <div class="info-l" style="display: flex; gap: 15px;">
                                    <div class="section" style="display: flex; flex-direction: column; font-size: 14px;">
                                        <span>ชื่อ :</span>
                                        <span>รหัสประจำตัวผู้เสียภาษี :</span>
                                        <span>รหัสพนักงาน :</span>
                                        <span>หน่วยงาน :</span>
                                        <span>ตำแหน่ง :</span>
                                        <span>วันที่เริ่มงาน :</span>
                                    </div>

                                    <div class="data" style="display: flex; flex-direction: column; font-size: 14px;">
                                        <span style="font-size: 14px;"><?php echo $row1['prefix_thai'] . ' ' . $row1['firstname_thai'] . ' ' . $row1['lastname_thai'] ?></span>
                                        <span style="font-size: 14px;"><?php echo $row1['tax_id']?></span>
                                        <span style="font-size: 14px;"><?php echo $row1['scg_employee_id'] ?></span>
                                        <span style="font-size: 14px;"><?php echo $row1['section'] ?></span>
                                        <span style="font-size: 14px;"><?php echo $row1['permission'] ?></span>
                                        <?php
                                        $date = $row1['scg_hiring_date'];

                                        $thai_days = array(
                                            'Monday' => 'จันทร์',
                                            'Tuesday' => 'อังคาร',
                                            'Wednesday' => 'พุธ',
                                            'Thursday' => 'พฤหัสบดี',
                                            'Friday' => 'ศุกร์',
                                            'Saturday' => 'เสาร์',
                                            'Sunday' => 'อาทิตย์'
                                        );

                                        $thai_months = array(
                                            'January' => 'มกราคม',
                                            'February' => 'กุมภาพันธ์',
                                            'March' => 'มีนาคม',
                                            'April' => 'เมษายน',
                                            'May' => 'พฤษภาคม',
                                            'June' => 'มิถุนายน',
                                            'July' => 'กรกฎาคม',
                                            'August' => 'สิงหาคม',
                                            'September' => 'กันยายน',
                                            'October' => 'ตุลาคม',
                                            'November' => 'พฤศจิกายน',
                                            'December' => 'ธันวาคม'
                                        );

                                        // แปลงชื่อวันและเดือนในรูปแบบภาษาไทย
                                        $thai_day = $thai_days[$date->format('l')];
                                        $thai_month = $thai_months[$date->format('F')];
                                        
                                        $thai_year = (int)$date->format('Y') + 543;


                                        // สร้างรูปแบบข้อความใหม่
                                        $formatted_date = str_replace($date->format('l'), $thai_day, $date->format('l, d F Y'));
                                        $formatted_date = str_replace($date->format('F'), $thai_month, $formatted_date);
                                        $formatted_date = str_replace($date->format('Y'), $thai_year, $formatted_date);

                                        ?>

                                        <span style="font-size: 14px;">วันที่เริ่มงาน : <?php echo $formatted_date?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row pt-3">
                            <div class="col-lg-12 col-md-8 col-sm-8">
                                <div class="card-box pd-30 pt-20" style="box-shadow: 1px 4px 8px rgba(0, 0, 0, 0.2);">
                                    <div class="row1 pb-3">
                                        <ol>
                                            <button type="button" class="btn income-deduct-btn-pay-split" onclick="window.location.href='slip2.php'">งวด 2 (1-15)</button>
                                            <button type="button" class="btn income-deduct-btn-pay">งวด 1 (16-30)</button>
                                        </ol>
                                    </div>
                                    <?php

                                    require_once __DIR__ . '/vendor/autoload.php';

                                    $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
                                    $fontDirs = $defaultConfig['fontDir'];

                                    $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
                                    $fontData = $defaultFontConfig['fontdata'];

                                    $mpdf = new \Mpdf\Mpdf([
                                        'fontDir' => array_merge($fontDirs, [
                                            __DIR__ . '/tmp',
                                        ]),
                                        'fontdata' => $fontData + [
                                            'sarabun' => [
                                                'R' => 'THSarabunNew.ttf',
                                                'I' => 'THSarabunNew Italic.ttf',
                                                'B' => 'THSarabunNew Bold.ttf',
                                                'BI' => 'THSarabunNew BoldItalic.ttf'
                                            ]
                                        ],
                                        'default_font' => 'sarabun'
                                    ]);

                                    ob_start();
                                    ?>
                                    <div class="text-left">
                                        <h3>รายรับ / รายจ่าย</h3>
                                    </div>
                                    <div class="table-responsive mt-2">
                                        <table class="data-table2 table stripe hover nowrap" id="myTable1" style="border-collapse: collapse; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th style="border: solid ">เงินเดือน</th>
                                                    <th style="border: solid ;">รายรับ</th>
                                                    <th style="border: solid ;">จำนวนเงินรายรับ</th>
                                                    <th style="border: solid ;">รายจ่าย</th>
                                                    <th style="border: solid ;">จำนวนเงินรายจ่าย</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- SELECT ค่า employee_payment -->
                                                <?php
                                                // เตรียมคำสั่ง SQL
                                                $sql = "SELECT * FROM log_salary
                                                            WHERE card_id = ?                                    
                                                            AND MONTH(datetime) = MONTH(GETDATE())
                                                            AND YEAR(datetime) = YEAR(GETDATE())
                                                            AND DAY(datetime) > 15;
                                                    ";
                                                // เพิ่มเงื่อนไขค้นหา
                                                $params = array($employee_card_id);

                                                // ดึงข้อมูลจากฐานข้อมูล
                                                $stmt = sqlsrv_query($conn, $sql, $params);
                                                // ตรวจสอบการทำงานของคำสั่ง SQL
                                                if ($stmt === false) {
                                                    die(print_r(sqlsrv_errors(), true));
                                                }
                                                // แสดงผลลัพธ์ในรูปแบบของตาราง HTML
                                                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

                                                    echo "<tr>";

                                                    echo "<td class='text-center' style='text-align: center;'>" . $row["salary_per_month"] . "</td>";
                                                    echo "<td class='text-center' style='text-align: center;'>" . $row["income_type"] . "</td>";
                                                    echo "<td class='text-center' style='text-align: center;'>" . $row["income_amount"] . "</td>";
                                                    echo "<td class='text-center' style='text-align: center;'>" . $row["deduct_type"] . "</td>";
                                                    echo "<td class='text-center' style='text-align: center;'>" . $row["deduct_amount"] . "</td>";
                                                    echo '</tr></td>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row pt-3">
                            <div class="col-lg-12 col-md-8 col-sm-8">
                                <div class="card-box pd-30 pt-20" style="box-shadow: 1px 4px 8px rgba(0, 0, 0, 0.2);">
                                    <div class="text-left">
                                        <h3>เงินที่ได้รับงวดนี้</h3>
                                    </div>
                                    <div class="table-responsive mt-2">
                                        <table class="data-table2 table stripe hover nowrap" id="myTable" style="border-collapse: collapse; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th style="border: solid ">รายรับทั้งหมด</th>
                                                    <th style="border: solid ">รายจ่ายทั้งหมด</th>
                                                    <th style="border: solid ">จำนวนเงินที่ได้รับ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- SELECT ค่า employee_payment -->
                                                <?php
                                                // เตรียมคำสั่ง SQL
                                                $sqlsalary = "SELECT * FROM log_sum_salary
                                                            WHERE log_sum_salary.card_id = ?
                                                            AND MONTH(date) = MONTH(GETDATE())
                                                            AND YEAR(date) = YEAR(GETDATE())
                                                            AND DAY(date) > 15;
                                                    ";
                                                // เพิ่มเงื่อนไขค้นหา
                                                $paramssalary = array($employee_card_id);

                                                // ดึงข้อมูลจากฐานข้อมูล
                                                $stmtsalary = sqlsrv_query($conn, $sqlsalary, $paramssalary);
                                                // ตรวจสอบการทำงานของคำสั่ง SQL
                                                if ($stmtsalary === false) {
                                                    die(print_r(sqlsrv_errors(), true));
                                                }
                                                // แสดงผลลัพธ์ในรูปแบบของตาราง HTML
                                                while ($rowsalary = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

                                                    // เงินได้ที่เดือนนี้
                                                    $vat = $row["salary_per_month"] * 0.05 ;
                                                    // echo $vat ;
                                                    $total_salary = ($row["salary_per_month"] + $rowsalary["total_income"]) - $vat - $rowsalary["total_deduct"];
                                                    // echo $total_salary;
                                                    // echo ' ' . $row["salary_per_month"] . ' ';
                                                    // echo $rowsalary["total_income"]  . ' ' ;
                                                    // echo $rowsalary["total_deduct"] . ' ' ;

                                                    echo "<tr>";
                                                    echo "<td class='text-center' style='text-align: center;'>" . $rowsalary["total_income"] . "</td>";
                                                    echo "<td class='text-center' style='text-align: center;'>" . $rowsalary["total_deduct"] . "</td>";
                                                    echo "<td class='text-center' style='text-align: center;'>" . $total_salary . "</td>";
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
                    <?php include('../employee/include/footer.php'); ?>
                </div>
            </div>
            <?php
            $html = ob_get_contents();
            $mpdf->WriteHTML($html);
            $mpdf->Output("slip1.pdf");
            ob_end_flush();
            ?>
            <a href="slip1.pdf" class="btn btn-primary">โหลดผลการเรียน (pdf)</a>
            <?php include('../employee/include/scripts.php') ?>
        </div>
    </div>
    </div>
    </div>
</body>

</html>