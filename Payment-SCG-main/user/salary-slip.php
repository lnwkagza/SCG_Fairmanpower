<!-- สลิปเงินเดือน -->
<?php include('../user/include/header.php') ?>

<link rel="stylesheet" href="styles/salary-slip.css">

<body>
    <div class="main-container">
        <!-- แถบบนสุด -->
        <div class="navbar">
            <!-- รูปปุ่มย้อนกลับไปหน้าก่อนหน้า -->
            <div>
                <a href="#" onclick="goBack()">
                    <img id="backIcon" src="bw.png">
                </a>
            </div>
            <span>สลิปเงินเดือน</span>
        </div>

        <div class="content">
            <div class="head-span"> <!-- ข้อความหลักบนสุด (สลิปเงินเดือน+เลขที่) -->
                <div class="h1">
                    <span>สลิปเงินเดือนพนักงาน</span>
                    <span class="font-blue">BL10221</span>
                </div>
            </div>
            <div class="box-slip">
                <div class="pay-slip">
                    <span>สลิปเงินเดือน</span>
                    <span>Pay Slip</span>
                </div>
                <div class="info">
                    <div class="info-l">
                        <div class="section">
                            <span>ชื่อ :</span>
                            <span>รหัสพนักงาน :</span>
                            <span>หน่วยงาน :</span>
                            <span>ตำแหน่ง :</span>
                            <span>วันที่เริ่มงาน :</span>
                        </div>
                        <div class="section">
                            <span>นาย ทดสอบ ระบบ (Tax ID : 21518949856)</span>
                            <span>BL10221</span>
                            <span>Digital Transformation</span>
                            <span>หัวหน้า</span>
                            <span>18 January 2023</span>
                        </div>
                    </div>
                    <div class="info-r">
                        <span>วันที่ชำระเงินเดือน : 18/01/2024</span>
                    </div>
                </div>
                <div class="tb">
                    <table class="data-table2 table stripe hover nowrap" id="myTable" style="border-bottom: 1px solid #ddd;">
                        <thead>
                            <tr>
                                <th class="income-deduct-column">รายรับ</th>
                                <th>จำนวนเงิน (บาท)</th>
                                <th class="income-deduct-column">รายจ่าย</th>
                                <th>จำนวนเงิน (บาท)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- SELECT ค่า employee_payment -->
                            <?php
                            // เตรียมคำสั่ง SQL
                            $sql = "SELECT * FROM log_salary
                                    WHERE card_id = ?
                                    AND MONTH(datetime) = MONTH(GETDATE())
                                    AND YEAR(datetime) = YEAR(GETDATE());
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
                                echo "<td class='text-center'>" . 'เงินเดือน' . "</td>";
                                echo "<td class='text-center'>" . number_format($row["salary_per_month"], 2) . "</td>";
                                echo "<td class='text-center'>" . 'ประกันสังคม' . "</td>";
                                echo "<td class='text-center'>" . number_format($row["salary_per_month"] * 0.05, 2) . "</td>";
                                echo "</tr>";
                            }
                            ?>
                            <?php
                            // เตรียมคำสั่ง SQL
                            $sql = "SELECT * FROM log_salary
                                    WHERE card_id = ?
                                    AND MONTH(datetime) = MONTH(GETDATE())
                                    AND YEAR(datetime) = YEAR(GETDATE());
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
                                    echo "<td class='text-center'>" . $row["income_type"] . "</td>";
                                    echo "<td class='text-center'>" . number_format($row["income_amount"], 2) . "</td>";
                                } else {
                                    echo "<td></td>";
                                    echo "<td></td>";
                                }
                                if ($row["deduct_amount"] !== NULL) {
                                    echo "<td class='text-center'>" . $row["deduct_type"] . "</td>";
                                    echo "<td class='text-center'>" .  number_format($row["deduct_amount"], 2) . "</td>";
                                } else {
                                    echo "<td></td>";
                                    echo "<td></td>";
                                }
                                echo '</tr>';
                            }
                            ?>
                            <?php
                            // เตรียมคำสั่ง SQL
                            $sql = "SELECT * FROM log_sum_salary
                                    WHERE log_sum_salary.card_id = ?
                                    AND MONTH(date) = MONTH(GETDATE())
                                    AND YEAR(date) = YEAR(GETDATE());
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
                                echo "<td class='text-center'>" . 'รวมรายรับทั้งหมด' . "</td>";
                                echo "<td class='text-center'>" . number_format($row["salary_per_month"] + $row["total_income"], 2) . "</td>";
                                echo "<td class='text-center'>" . 'รวมรายจ่ายทั้งหมด' . "</td>";
                                echo "<td class='text-center'>" . number_format($row["salary_per_month"] * 0.05 + $row["total_deduct"], 2) . "</td>";
                                echo '</tr></td>';
                            }
                            ?>
                            <?php
                            // เตรียมคำสั่ง SQL
                            $sql = "SELECT * FROM log_sum_salary
                                    WHERE log_sum_salary.card_id = ?
                                    AND MONTH(date) = MONTH(GETDATE())
                                    AND YEAR(date) = YEAR(GETDATE());
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
                                echo "<td class='text-center' colspan='3' style='border: 1px solid #d0cdcd;'>" . 'รายได้สุทธิ' . "</td>";
                                echo "<td class='text-center' style='border: 1px solid #d0cdcd;'>" . number_format($total_salary, 2) . "</td>";
                                echo '</tr></td>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php include('../user/include/footer.php') ?>
    </div>
    <!-- js -->
</body>

</html>