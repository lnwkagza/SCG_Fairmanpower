<!-- หน้าสรุปผลการคำนวณ  -->
<?php include('../employee/include/header.php') ?>
<link rel="stylesheet" href="../vendors/styles/resultsSummary.css">
<link rel="stylesheet" href="../vendors/styles/slip12.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script> -->


<body>

    <?php include('../employee/include/navbar.php') ?>
    <?php include('../employee/include/sidebar.php') ?>
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
                                        <span style="font-size: 14px;"><?php echo $prefix . ' ' . $fname . ' ' . $lname ?></span>
                                        <span style="font-size: 14px;"><?php echo $row['tax_id']?></span>
                                        <span style="font-size: 14px;"><?php echo $row['scg_employee_id'] ?></span>
                                        <span style="font-size: 14px;"><?php echo $row['section'] ?></span>
                                        <span style="font-size: 14px;"><?php echo $row['permission'] ?></span>
                                        <span style="font-size: 14px;"><?php echo $row['scg_hiring_date']->format('D, d M Y') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row pt-3">
                            <div class="col-lg-12 col-md-8 col-sm-8">
                                <div class="card-box pd-30 pt-20" style="box-shadow: 1px 4px 8px rgba(0, 0, 0, 0.2);">
                                    <div class="row1 pb-3">
                                        <ol>
                                            <button type="button" class="move-split">2 งวด</button>
                                            <button type="button" class="split" onclick="window.location.href='slip1.php'">1 งวด</button>
                                        </ol>
                                    </div>
                                    <div class="text-left">
                                        <h3>รายรับ / รายจ่าย</h3>
                                    </div>
                                    <div class="table-responsive mt-2">
                                        <table class="data-table2 table stripe hover nowrap" id="myTable1">
                                            <thead>
                                                <tr>
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
                                                $sql = "SELECT * FROM log_salary
                                                            WHERE card_id = ?                                    
                                                            AND MONTH(datetime) = MONTH(GETDATE())
                                                            AND YEAR(datetime) = YEAR(GETDATE())
                                                            AND DAY(datetime) >= 1 AND DAY(datetime) <= 15;
                                                    ";
                                                // เพิ่มเงื่อนไขค้นหา
                                                $params = array($card_id);

                                                // ดึงข้อมูลจากฐานข้อมูล
                                                $stmt = sqlsrv_query($conn, $sql, $params);
                                                // ตรวจสอบการทำงานของคำสั่ง SQL
                                                if ($stmt === false) {
                                                    die(print_r(sqlsrv_errors(), true));
                                                }
                                                // แสดงผลลัพธ์ในรูปแบบของตาราง HTML
                                                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

                                                    echo "<tr>";

                                                    echo "<td class='text-center'>" . $row["salary_per_month"] . "</td>";
                                                    if ($row["income_amount"] !== NULL) {
                                                        echo "<td class='text-center'>" . $row["income_type"] . "</td>";
                                                    } else {
                                                        echo "<td class='text-center'>" . "ไม่มีรายการ" . "</td>";
                                                    }
                                                    if ($row["income_amount"] !== NULL) {
                                                        echo "<td class='text-center'>" . $row["income_amount"] . "</td>";
                                                    } else {
                                                        echo "<td class='text-center'>" . "0" . "</td>";
                                                    }
                                                    if ($row["deduct_amount"] !== NULL) {
                                                        echo "<td class='text-center'>" . $row["deduct_type"] . "</td>";
                                                    } else {
                                                        echo "<td class='text-center'>" . "ไม่มีรายการ" . "</td>";
                                                    }
                                                    if ($row["deduct_amount"] !== NULL) {
                                                        echo "<td class='text-center'>" . $row["deduct_amount"] . "</td>";
                                                    } else {
                                                        echo "<td class='text-center'>" . "0" . "</td>";
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

                        <div class="row pt-3">
                            <div class="col-lg-12 col-md-8 col-sm-8">
                                <div class="card-box pd-30 pt-20" style="box-shadow: 1px 4px 8px rgba(0, 0, 0, 0.2);">
                                    <div class="text-left">
                                        <h3>เงินที่ได้รับงวดนี้</h3>
                                    </div>
                                    <div class="table-responsive mt-2">
                                        <table class="data-table2 table stripe hover nowrap" id="myTable">
                                            <thead>
                                                <tr>
                                                    <th>รายรับทั้งหมด</th>
                                                    <th>รายจ่ายทั้งหมด</th>
                                                    <th>จำนวนเงินที่ได้รับ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- SELECT ค่า employee_payment -->
                                                <?php
                                                // เตรียมคำสั่ง SQL
                                                $sql = "SELECT * FROM log_sum_salary
                                                            WHERE log_sum_salary.card_id = ?
                                                            AND MONTH(date) = MONTH(GETDATE())
                                                            AND YEAR(date) = YEAR(GETDATE())
                                                            AND DAY(date) >= 1 AND DAY(date) <= 15;
                                                    ";
                                                // เพิ่มเงื่อนไขค้นหา
                                                $params = array($card_id);

                                                // ดึงข้อมูลจากฐานข้อมูล
                                                $stmt = sqlsrv_query($conn, $sql, $params);
                                                // ตรวจสอบการทำงานของคำสั่ง SQL
                                                if ($stmt === false) {
                                                    die(print_r(sqlsrv_errors(), true));
                                                }
                                                // แสดงผลลัพธ์ในรูปแบบของตาราง HTML
                                                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

                                                    // เงินได้ที่เดือนนี้
                                                    $total_salary = ($row["salary_per_month"] + $row["total_income"]) - $row["total_deduct"];

                                                    echo "<tr>";
                                                    echo "<td class='text-center'>" . $row["total_income"] . "</td>";
                                                    echo "<td class='text-center'>" . $row["total_deduct"] . "</td>";
                                                    echo "<td class='text-center'>" . $total_salary . "</td>";
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

            <?php include('../employee/include/scripts.php') ?>
        </div>
    </div>
    </div>
    </div>
</body>

</html>