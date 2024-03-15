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
                            <span>ชื่อ
                                <span style="font-size: 4px;">(Name)</span> :
                            </span>
                            <span>รหัสพนักงาน
                                <span style="font-size: 4px;">(Code)</span> :
                            </span>
                            <span>หน่วยงาน
                                <span style="font-size: 4px;">(Department)</span> :
                            </span>
                            <span>ตำแหน่ง
                                <span style="font-size: 4px;">(Position)</span> :
                            </span>
                            <span>วันที่เริ่มงาน
                                <span style="font-size: 4px;">(Employeed Date)</span> :
                            </span>

                            <!-- <span>รหัสพนักงาน :</span>
                            <span class="font-small">(Code)</span>
                            <span>หน่วยงาน :</span>
                            <span class="font-small">(Department)</span>
                            <span>ตำแหน่ง :</span>
                            <span class="font-small">(Position)</span>
                            <span>วันที่เริ่มงาน :</span>
                            <span class="font-small">(Employeed Date)</span> -->
                        </div>
                        <div class="data">
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
                    <div class="sp">รายรับ / จ่าย งวดนี้ :</div>
                    <table class="data-table2 table stripe hover nowrap" id="myTable">
                        <thead>
                            <tr>
                                <th>เงินเดือน<?php $card_id ?></th>
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
                                    AND DAY(datetime) > 15;
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
                <div class="tb-b">
                    <div class="sp">รายได้งวดนี้ :</div>
                    <table class="data-table2 table stripe hover nowrap" id="myTable">
                        <thead>
                            <tr>
                                <th>รายรับทั้งหมด</th>
                                <th>รายจ่ายทั้งหมด</th>
                                <th>จำนวนเงินที่ได้รับในงวดนี้</th>
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
                                    AND DAY(date) > 15;
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
                                $total_salary = ($row["salary_per_month"] + $row["total_income"]) - $row["total_deduct"]  ;
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

    <?php include('../user/include/footer.php') ?>
    </div>
    <!-- js -->

</body>

</html>