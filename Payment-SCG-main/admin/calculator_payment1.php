<!-- คำนวณเงินเดือนพนักงาน -->
<?php include('../admin/include/header.php') ?>
<link rel="stylesheet" href="../vendors/styles/calculator_payment.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>


<body>

    <?php include('../admin/include/navbar.php') ?>
    <?php include('../admin/include/sidebar.php') ?>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <h3>คำนวณเงินเดือน : Calculator Payment (เต็มงวด)</h3>
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
                        <div class="card-box pd-30 pt-10 height-100-p">
                            <div class="bar">
                                <a style="color:#338ac6 ;">คำนวณเงินเดือนพนักงาน <i class="fa-regular fa-user"></i></a>
                                <a href="doc-waitingApprov.php">เอกสารรอการอนุมัติ <i class="fa-solid fa-file"></i>
                                    <div class="bug">1</div> <!-- วงกลมแสดงจำนวนรายการค้างอนุมัติ -->
                                </a>
                            </div>

                            <div class="row1">
                                <ol>
                                    <button class="split" onclick="window.location.href='calculator_payment2.php'">แบ่งงวด</button>
                                    <button class="move-split" disabled>เต็มงวด</button>
                                </ol>
                                <div class="push">
                                    <button  id="cal"  onclick="window.location.href='reason_salary.php'">คำนวณ <i class="fa-solid fa-calculator"></i></button>
                                    <button id="reset" onclick="window.location.href=window.location.href">รีเซต <i class="fa-solid fa-rotate-right"></i></button>
                                </div>
                            </div>
                            <div class="pb-10">
                                <div class="table-responsive mt-2">
                                    <table class="data-table2 table stripe hover nowrap">
                                        <thead>
                                            <tr>
                                                <th><input class="ctd" type="checkbox" id="checkAll" disabled></th>
                                                <th>สถานะ</th>
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
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- SELECT ค่า employee_payment -->
                                            <?php
                                            $splitId = 1;
                                            $_SESSION['splitId'] = $splitId;

                                            // เตรียมคำสั่ง SQL
                                            $sql = "SELECT  scg_employee_id, prefix_thai,firstname_thai,lastname_thai,nickname_thai,
                                                        company.name_thai as company
                                                        ,division.name_thai as division,department.name_thai as department,section.name_thai as section,
                                                        cost_center.cost_center_code as cost_center,contract_type.name_thai as contract_type,pl.pl_name_thai as pl,
                                                        position.name_thai as position FROM employee
                                                    LEFT JOIN cost_center ON cost_center.cost_center_id  = employee.cost_center_organization_id
                                                    LEFT JOIN section ON section.section_id = cost_center.section_id 
                                                    LEFT JOIN department ON department.department_id = section.department_id 
                                                    LEFT JOIN division ON division.division_id = department.division_id 
                                                    LEFT JOIN location ON location.location_id = division.location_id
                                                    LEFT JOIN company ON company.company_id = location.company_id
                                                    LEFT JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
                                                    LEFT JOIN pl_info ON pl_info.card_id = employee.card_id
                                                    LEFT JOIN pl ON pl.pl_id = pl_info.pl_id
                                                    LEFT JOIN position_info ON position_info.card_id  = employee.card_id
                                                    LEFT JOIN position ON position.position_id  = position_info.position_id
                                                    LEFT JOIN employee_payment ON employee_payment.card_id = employee.card_id
                                                    LEFT JOIN split ON split.card_id = employee.card_id 
                                                WHERE split.split_set_id = '1'";
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
                                                echo "<tr >";
                                                echo "<td><input class='ctd' type='checkbox' disabled></td>";
                                                echo "<td><button class='ctd' style='background-color:#38b33a;'></button></td>";
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
                                                echo '</tr></td>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="detail">
                                    <div id="status1">
                                        <button style="background-color:#38b33a;"></button><span>พร้อมคำนวณ</span>
                                    </div>
                                    <div id="status2">
                                        <button style="background-color:#e99532;"></button><span>มีเอกสารรอการอนุมัติ</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <?php include('../admin/include/footer.php'); ?>
            </div>
        </div>
        <!-- js -->
        <!-- ปุ่มแสดงสถานะ -->
        <script>
            $(document).ready(function() {
                // คลิกปุ่มเพื่อตรวจสอบสีและดำเนินการตามเงื่อนไข
                $('table.data-table tbody').on('click', 'button', function() {
                    // ดึงสีพื้นหลังของปุ่ม
                    var buttonColor = $(this).css('background-color');

                    // เช็คสีและดำเนินการตามเงื่อนไข
                    if (buttonColor === 'rgb(233, 149, 50)') {
                        // สีส้ม - ลิงค์ไปที่ doc-waitingApprov.php
                        window.location.href = 'doc-waitingApprov.php';
                    } else if (buttonColor === 'rgb(56, 179, 58)') {
                        // สีเขียว - ไม่ต้องทำอะไร
                        console.log('ไม่ต้องทำอะไร');
                    }
                });
            });
        </script>

        <!-- ซ่อนแจ้งเตือน -->
        <script>
            // เลือก element ที่มี class "bug"
            var bugElement = document.querySelector('.bug');

            // ตรวจสอบค่าใน element
            var bugValue = parseInt(bugElement.textContent);

            // ถ้าค่าเป็น 0 ให้ซ่อน element
            if (bugValue === 0) {
                bugElement.style.display = 'none';
            }
        </script>

        <!-- กดเลือกทุกช่องแล้วกดคำนวณ เด้งไปหน้าสรุปผลการคำนวณ -->
        <!-- <script>
        $(document).ready(function() {
            // คลิกที่ปุ่มคำนวณ
            $('#cal').click(function() {
                // ตรวจสอบว่าทุกช่อง checkbox ถูกเลือกหรือไม่
                var allChecked = $('.data-table tbody input:checkbox:checked').length === $('.data-table tbody input:checkbox').length;

                // ถ้าทุกช่อง checkbox ถูกเลือก
                if (allChecked) {
                    // ลิงค์ไปที่ resultsSummary.php
                    window.location.href = 'resultsSummary.php';
                } else {
                    // แสดงข้อความ
                    console.log('กรุณาเลือกทุกช่อง checkbox ก่อน');
                }
            });
        });
    </script> -->
        <?php include('../admin/include/scripts.php') ?>
</body>

</html>