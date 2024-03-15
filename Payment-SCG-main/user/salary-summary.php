<!-- สลิปเงินเดือน -->
<?php include('../admin/include/header.php') ?>

<?php include('../user/include/scripts.php') ?>

<link rel="stylesheet" href="styles/salary-summary.css">

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
            <span>ประวัติเงินเดือน</span>
        </div>
        <div class="content">
            <div class="card-box pd-5">
                <h6>
                    <div class="mt-3">สรุปข้อมูลเงินเดือนของท่าน :</div>
                </h6>
                <table class="data-table2 table stripe hover nowrap" id="myTable">
                    <thead>
                        <tr>
                            <th class="text-center col-7">รายการ</th>
                            <th class="text-center col-5">จำนวนเงิน(บาท)</th>
                        </tr>
                    </thead>
                    <thead>
                        <tr>
                            <th class="text-left col-12" colspan="2">รายรับ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- SELECT ค่า employee_payment -->
                        <?php
                        // เตรียมคำสั่ง SQL
                        $sql = "SELECT * FROM log_salary
                                    WHERE card_id = '1234';
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

                            // echo "<td class='text-center'>" . $row["salary_per_month"] . "</td>";
                            if ($row["income_amount"] !== NULL) {
                                echo "<td>" . $row["income_type"] . "</td>";
                            } else {
                                echo "<td>" . "ไม่มีรายการ" . "</td>";
                            }
                            if ($row["income_amount"] !== NULL) {
                                echo "<td class='text-right' style='padding-right: 32px;'>" . $row["income_amount"] . "</td>";
                            } else {
                                echo "<td class='text-right' style='padding-right: 32px;'>" . "0" . "</td>";
                            }
                            echo '</tr></td>';
                        }
                        ?>
                    </tbody>
                    <thead>
                        <tr>
                            <!-- <th>เงินเดือน<?php $card_id ?></th> -->
                            <th class="text-left col-12" colspan="2">รายจ่าย</th>
                            <!-- <th>รายจ่าย</th>
                        <th>จำนวนเงินรายจ่าย</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <!-- SELECT ค่า employee_payment -->
                        <?php
                        // เตรียมคำสั่ง SQL
                        $sql = "SELECT * FROM log_salary
                                    WHERE card_id = ?;
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

                            // echo "<td class='text-center'>" . $row["salary_per_month"] . "</td>";
                            if ($row["income_amount"] !== NULL) {
                                echo "<td>" . $row["income_type"] . "</td>";
                            } else {
                                echo "<td>" . "ไม่มีรายการ" . "</td>";
                            }
                            if ($row["income_amount"] !== NULL) {
                                echo "<td class='text-right' style='padding-right: 32px;'>" . $row["income_amount"] . "</td>";
                            } else {
                                echo "<td class='text-right' style='padding-right: 32px;'>" . "0" . "</td>";
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

    <?php include('../user/include/footer.php') ?>
    </div>
    <!-- js -->

</body>

</html>