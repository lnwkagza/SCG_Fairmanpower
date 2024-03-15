<?php include('../employee/include/header.php') ?>
<link rel="stylesheet" href="../vendors/styles/resultsSummary.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script> -->

<body>

    <?php include('../employee/include/navbar.php') ?>
    <?php include('../employee/include/sidebar.php') ?>
    <?php include('../admin/include/scripts.php') ?>
    <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

    <?php
    $currentDateTime = date('Y-m-d'); // วันที่และเวลาปัจจุบันในรูปแบบ Y-m-d H:i:s
    $month = $_GET['month'];
    $year = $_GET['year'];
    ?>

<div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <h3>สลิปเงินเดือนย้อนหลัง : History Pay Slip</h3>
                            </div>
                            <a href="summary_slip.pdf" class="btn btn-outline-info" style="float: right;">ดาวน์โหลดเอกสาร <i class="fa-solid fa-file-pdf"></i></a>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="slip.php">สลิปเงินเดือน</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">ประวัติเงินเดือน</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
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
                <div class="row">
                    <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                        <div class="card-box pd-30 pt-20" >
                            <div class="row">
                                <span style="font-size: 25px; font-weight: bold;">บริษัท แฟร์ แมนพาวเวอร์ จำกัด (มหาชน)</span>
                                <div style="display: flex; flex-direction: column; text-align: end; margin-bottom: 5px; margin-left: auto;">
                                    <span style="font-size: 20px; font-weight: bold;">สลิปเงินเดือน</span>
                                    <span style="font-size: 20px; font-weight: bold;">Pay Slip</span>
                                </div>
                            </div>
                            <div class="info" style="display: flex; justify-content: space-between;">
                                <div class="info-l" style="display: flex; gap: 15px;">
                                <div class="section" style="display: flex; flex-direction: column; font-size: 14px;">
                                        <span style="font-size: 14px;">ชื่อ : <?php echo $prefix . ' ' . $fname . ' ' . $lname ?> <br></span>
                                        <span style="font-size: 14px;">รหัสประจำตัวผู้เสียภาษี : <?php echo $row['tax_id'] ?> <br></span>
                                        <span style="font-size: 14px;">รหัสพนักงาน : <?php echo $row['scg_employee_id'] ?> <br></span>
                                        <span style="font-size: 14px;">หน่วยงาน : <?php echo $row['section'] ?> <br></span>
                                        <span style="font-size: 14px;">ตำแหน่ง : <?php echo $row['permission'] ?> <br></span>
                                        <span style="font-size: 14px;">วันที่เริ่มงาน : <?php echo $row['scg_hiring_date']->format('D, d M Y') ?></span>
                                    </div>

                                </div>
                            </div>
                            <div class="row pt-2">
                                <div class="col-lg-12 col-md-8 col-sm-8">
                                    <div class="tb">
                                        <table class="data-table2 table stripe hover nowrap" id="myTable" style="border-collapse: collapse; width: 100%; background-color: white;">
                                            <thead>
                                                <tr>
                                                    <th style="width: 35%; border: 1px solid #d0cdcd;">รายรับ</th>
                                                    <th style="border: 1px solid #d0cdcd;">จำนวนเงิน (บาท)</th>
                                                    <th style="width: 35%; border: 1px solid #d0cdcd;">รายจ่าย</th>
                                                    <th style="border: 1px solid #d0cdcd;">จำนวนเงิน (บาท)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- SELECT ค่า employee_payment -->
                                                <?php
                                                // เตรียมคำสั่ง SQL
                                                $sql = "SELECT * FROM log_salary
                                                            WHERE card_id = ?
                                                            AND MONTH(datetime) = $month
                                                            AND YEAR(datetime) = $year;
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
                                                if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                                    echo "<tr>";
                                                    echo "<td style='text-align: left; padding-left: 10px; border-left: 1px solid #d0cdcd;'>" . 'เงินเดือน' . "</td>";
                                                    echo "<td style='text-align: right; padding-right: 10px;  border-left: 1px solid #d0cdcd;'>"  . number_format($row["salary_per_month"], 2) . "</td>";
                                                    echo "<td style='text-align: left; padding-left: 10px; border-left: 1px solid #d0cdcd;'>" . 'ประกันสังคม' . "</td>";
                                                    echo "<td style='text-align: right; right; padding-right: 10px;  border-left: 1px solid #d0cdcd; border-right: 1px solid #d0cdcd;'>" . number_format($row["salary_per_month"] * 0.05, 2) . "</td>";
                                                    echo "</tr>";
                                                }
                                                ?>
                                                <?php
                                                // เตรียมคำสั่ง SQL
                                                $sql = "SELECT * FROM log_salary
                                                            WHERE card_id = ?
                                                            AND MONTH(datetime) = $month
                                                            AND YEAR(datetime) = $year;
                                                    ";
                                                // เพิ่มเงื่อนไขค้นหา
                                                $params = array($card_id);

                                                // ดึงข้อมูลจากฐานข้อมูล
                                                $stmt = sqlsrv_query($conn, $sql, $params);
                                                // ตรวจสอบการทำงานของคำสั่ง SQL
                                                if ($stmt === false) {
                                                    die(print_r(sqlsrv_errors(), true));
                                                }
                                                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                                    echo "<tr>";
                                                    if ($row["income_amount"] !== NULL) {
                                                        echo "<td style='text-align: left; padding-left: 10px; border-left: 1px solid #d0cdcd;'>" . $row["income_type"] . "</td>";
                                                        echo "<td style='text-align: right; right; padding-right: 10px; border-left: 1px solid #d0cdcd;'>" . number_format($row["income_amount"], 2) . "</td>";
                                                    } else {
                                                        echo "<td style='text-align: left; border-left: 1px solid #d0cdcd;'></td>";
                                                        echo "<td style='text-align: left; border-left: 1px solid #d0cdcd;'></td>";
                                                    }
                                                    if ($row["deduct_amount"] !== NULL) {
                                                        echo "<td style='text-align: left; padding-left: 10px; border-left: 1px solid #d0cdcd;'>" . $row["deduct_type"] . "</td>";
                                                        echo "<td style='text-align: right; padding-right: 10px; border-left: 1px solid #d0cdcd; border-right: 1px solid #d0cdcd;'>" .  number_format($row["deduct_amount"], 2) . "</td>";
                                                    } else {
                                                        echo "<td style='text-align: left; border-left: 1px solid #d0cdcd;'></td>";
                                                        echo "<td style='text-align: left; border-left: 1px solid #d0cdcd;'></td>";
                                                    }
                                                    echo '</tr>';
                                                }
                                                ?>
                                                <?php
                                                // เตรียมคำสั่ง SQL
                                                $sql = "SELECT * FROM log_sum_salary
                                                            WHERE log_sum_salary.card_id = ?
                                                            AND MONTH(date) = $month
                                                            AND YEAR(date) = $year;
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
                                                if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                                    echo "<tr>";
                                                    echo "<td style='text-align: left; padding-left: 10px; border: 1px solid #d0cdcd; border-bottom: none;'>" . 'รวมรายรับทั้งหมด' . "</td>";
                                                    echo "<td style='text-align: right; right; padding-right: 10px; border: 1px solid #d0cdcd; border-bottom: none;'>" . number_format($row["salary_per_month"] + $row["total_income"], 2) . "</td>";
                                                    echo "<td style='text-align: left; padding-left: 10px; border: 1px solid #d0cdcd; border-bottom: none;'>" . 'รวมรายจ่ายทั้งหมด' . "</td>";
                                                    echo "<td style='text-align: right; right; padding-right: 10px; border: 1px solid #d0cdcd; border-bottom: none;'>" . number_format($row["salary_per_month"] * 0.05 + $row["total_deduct"], 2) . "</td>";
                                                    echo '</tr></td>';
                                                }
                                                ?>
                                                <?php
                                                // เตรียมคำสั่ง SQL
                                                $sql = "SELECT * FROM log_sum_salary
                                                            WHERE log_sum_salary.card_id = ?
                                                            AND MONTH(date) = $month
                                                            AND YEAR(date) = $year;
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
                                                if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                                    $total_salary = ($row["salary_per_month"] + $row["total_income"]) - ($row["salary_per_month"] * 0.05 + $row["total_deduct"]);
                                                    echo "<tr>";
                                                    echo "<td colspan='3' style='text-align: center; border: 1px solid #d0cdcd; font-weight: bold;'>" . 'รายได้สุทธิ' . "</td>";
                                                    echo "<td style='text-align: right; padding-right: 10px; border: 1px solid #d0cdcd; font-weight: bold;'>" . number_format($total_salary, 2) . "</td>";
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
                    <?php
                    $html = ob_get_contents();
                    $mpdf->WriteHTML($html);
                    $mpdf->Output("summary_slip.pdf");
                    ob_end_flush();
                    ?>
                </div>
            </div>
            <?php include('../employee/include/scripts.php') ?>
        </div>
    </div>
    </div>
    </div>
</body>

</html>