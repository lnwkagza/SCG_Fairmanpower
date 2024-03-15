<!-- หน้าสรุปผลการคำนวณ  -->
<?php include('../admin/include/header.php') ?>
<link rel="stylesheet" href="../vendors/styles/resultsSummary.css">
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
    $month = $_GET['month'];
    $year = $_GET['year'];
    ?>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <h3>ประวัติเงินเดือน : History Payment</h3>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="slip.php">สลิปเงินเดือน</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">ประวัติเงินเดือน</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                        <div class="card-box-2 pd-30 pt-10">
                            <div class="bar d-flex justify-content-center align-items-center">
                                <a style="color:#338ac6;">ประวัติเงินเดือน <i class="fa-regular fa-file"></i></a>
                            </div>

                            <div class="row pt-3">
                                <div class="col-lg-12 col-md-8 col-sm-8">
                                    <div class="card-box pd-30 pt-20" style="box-shadow: 1px 4px 8px rgba(0, 0, 0, 0.2);">
                                        <div class="text-left">

                                            <h3>ประวัติรายรัย / รายจ่าย</h3>
                                        </div>
                                        <div class="table-responsive mt-2">
                                            <table class="data-table2 table stripe hover nowrap" id="myTable1" style="border-collapse: collapse; width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>เงินเดือน </th>
                                                        <th>รายรับ</th>
                                                        <th>จำนวนเงินรายรับ</th>
                                                        <th>รายจ่าย</th>
                                                        <th>จำนวนเงินรายจ่าย</th>
                                                        <th>งวด</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- SELECT ค่า employee_payment -->
                                                    <?php
                                                    // เตรียมคำสั่ง SQL
                                                    $sql = "SELECT * FROM log_salary
                                                            WHERE card_id = ?
                                                            AND MONTH(datetime) = $month
                                                            AND YEAR(datetime) = $year
                                                            ;";
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
                                                        $time  = $row["datetime"]; // สร้างวัตถุ DateTime
                                                        $formattedDate = $time->format('d-m-Y');

                                                        echo "<tr>";

                                                        echo "<td class='text-center' style='text-align: center;'>" . $row["salary_per_month"] . "</td>";
                                                        if ($row["income_amount"] !== NULL) {
                                                            echo "<td class='text-center' style='text-align: center;'>" . $row["income_type"] . "</td>";
                                                        } else {
                                                            echo "<td class='text-center' style='text-align: center;'>" . "ไม่มีรายการ" . "</td>";
                                                        }
                                                        if ($row["income_amount"] !== NULL) {
                                                            echo "<td class='text-center' style='text-align: center;'>" . $row["income_amount"] . "</td>";
                                                        } else {
                                                            echo "<td class='text-center' style='text-align: center;'>" . "0" . "</td>";
                                                        }
                                                        if ($row["deduct_amount"] !== NULL) {
                                                            echo "<td class='text-center' style='text-align: center;'>" . $row["deduct_type"] . "</td>";
                                                        } else {
                                                            echo "<td class='text-center' style='text-align: center;'>" . "ไม่มีรายการ" . "</td>";
                                                        }
                                                        if ($row["deduct_amount"] !== NULL) {
                                                            echo "<td class='text-center' style='text-align: center;'>" . $row["deduct_amount"] . "</td>";
                                                        } else {
                                                            echo "<td class='text-center' style='text-align: center;'>" . "0" . "</td>";
                                                        }
                                                        echo "<td class='text-center' style='text-align: center;'>" . $formattedDate  . "</td>";
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
                                            <h3>ประวัติเงินที่ได้รับ</h3>
                                        </div>
                                        <div class="table-responsive mt-2">
                                            <table class="data-table2 table stripe hover nowrap" id="myTable" style="border-collapse: collapse; width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>รายรับทั้งหมด</th>
                                                        <th>รายจ่ายทั้งหมด</th>
                                                        <th>จำนวนเงินที่ได้รับ</th>
                                                        <th>งวด</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- SELECT ค่า employee_payment -->
                                                    <?php
                                                    // เตรียมคำสั่ง SQL
                                                    $sql = "SELECT * FROM log_sum_salary
                                                            WHERE log_sum_salary.card_id = ?
                                                            AND MONTH(date) = $month
                                                            AND YEAR(date) = $year
                                                            ;";
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
                                                        $time  = $row["date"]; // สร้างวัตถุ DateTime
                                                        $formattedDate = $time->format('d-m-Y');

                                                        // เงินได้ที่เดือนนี้
                                                        $total_salary = ($row["salary_per_month"] + $row["total_income"]) - $row["total_deduct"];


                                                        echo "<tr>";
                                                        echo "<td class='text-center' style='text-align: center;'>" . $row["total_income"] . "</td>";
                                                        echo "<td class='text-center' style='text-align: center;'>" . $row["total_deduct"] . "</td>";
                                                        echo "<td class='text-center' style='text-align: center;'>" . $total_salary . "</td>";
                                                        echo "<td class='text-center' style='text-align: center;'>" . $formattedDate  . "</td>";
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
                        <a href='history_slip.php?month=<?php echo $month ?>&year= <?php echo $year ?>' class="btn btn-primary">สลิปเงินเดือน</a>
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