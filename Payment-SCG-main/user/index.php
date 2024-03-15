<!-- โปรไฟล์ -->
<?php include('../user/include/header.php') ?>
<link rel="stylesheet" href="styles/index.css">



<body>
    <div class="main-container">
        <div class="inform-box"> <!-- กล่องข้อมูล -->
            <div class="l"> <!-- ชื่อมุมบนซ้าย -->
                <!-- <img id="em" src="4.png"> -->
                <img src="<?php echo (!empty($row['employee_image'])) ? '../../admin/uploads_img/' . $row['employee_image'] : '../asset/img/admin.png'; ?>" class="border-radius-100" width="120" height="120">

                <div class="ct">
                    <span><?php echo $scg_employee_id ?></span>
                    <span><?php echo $prefix . ' ' . $fname . ' ' . $lname ?></span>
                </div>
            </div>
            <div class="c"> <!-- วันที่ตรงกลาง -->
                <i class="fa-solid fa-comments-dollar"></i>
                <span><?php echo date('d F Y'); ?></span>
                <!-- popup -->
                <button type="submit" class="btn" onclick="openPopup()">สลิปเงินเดือน</button>
                <div class="popup" id="popup">
                    <img id="x" src="16.png" type="button" onclick="closePopup()"> <br> <!-- กดปิด -->
                    <img src="ohh.png"> <br> <!-- รูปเครื่องหมายตกใจสีเหลือง -->
                    <span>สรุปเงินเดือน</span> <br>
                    <span>คุณจำเป็นต้องกรอกรหัสผ่านเพื่อดูรายละเอียด</span><br>
                    <input id="password" type="password">
                    <div class="ppAlink">
                        <button id="submit" type="button" onclick="openPage()">ยืนยัน</button> <!-- กดปุ่มออก จะเด้งไปหน้าดูสลิปเงินเดือน -->
                    </div>
                </div>
            </div>
            <div class="b">
                <span>รหัสพนักงาน : <?php echo $scg_employee_id?></span>
                <span>ชื่อ : <?php echo $prefix . ' '. $fname. ' ' . $lname?></span>
                <span>ตำแหน่ง : <?php echo $position?></span>
                <span>แผนก : <?php echo $department?></span>
                <span>Division : <?php echo $division?></span>
            </div>
        </div>

    </div>

    <div class="card">
        <div class="top">
            <div class="picSpan">
                <i class="fa-solid fa-circle-check"></i>
                <div class="span">
                    <span>อนุมัติแล้ว : เอกสารรับรองเงินเดือน</span>
                    <div class="date">
                        <span><i class="fa-solid fa-calendar-days"></i> 17/01/2024</span>
                        <span><i class="fa-solid fa-clock"></i> 10.30</span>
                    </div>
                </div>
            </div>
            <span id="inCompany">ภายในบริษัท</span>
        </div>
        <hr>
        <div class="center">
            <img id="ep" src="ep.png">
            <div class="info">
                <span><?php echo $prefix . ' '. $fname. ' ' . $lname?> ยื่นขอ "เอกสารรับรองเงินเดือน"</span>
                <span>ขอเอกสารรับรองเงินเดือน ยื่นขอวันที่ 17/01/2024</span>
                <span>วัตถุประสงค์ : Apply for a home loan</span>
                <span>หมายเหตุ/จัดส่งที่ : Email</span>
                <div>
                    <span>สถานะ :</span>
                    <span id="change-color">อนุมัติ</span>
                </div>
            </div>
        </div>
        <hr>
        <div class="bottom">
            <span><i class="fa-solid fa-question"></i> รายละเอียด</span>
        </div>
    </div>

    <div class="alink">
        <div class="salary-summary">
            <a href="#" onclick="openPopup1()">
                <i class="fa-solid fa-chart-simple"></i>
                <span>สรุปเงินเดือน</span>
            </a>
            <div class="popup" id="popup1">
                <img id="x" src="16.png" type="button" onclick="closePopup1()"> <br> <!-- กดปิด -->
                <img src="ohh.png"> <br> <!-- รูปเครื่องหมายตกใจสีเหลือง -->
                <span style="font-size: 12px;">สรุปเงินเดือน</span> <br>
                <span style="font-size: 12px;">คุณจำเป็นต้องกรอกรหัสผ่านเพื่อดูรายละเอียด</span><br>
                <input id="password" type="password">
                <div class="ppAlink">
                    <button id="send" type="button" onclick="sumPage()">ยืนยัน</button> <!-- กดปุ่มออก จะเด้งไปหน้าดูสลิปเงินเดือน -->
                </div>
            </div>
        </div>
        <div class="fund">
            <a href="Funds-taxes.php">
                <i class="fa-solid fa-money-bill-trend-up"></i>
                <span>กองทุนและภาษี</span>
            </a>
        </div>
        <div class="benefit">
            <a href="benefit.php">
                <i class="fa-solid fa-shield-halved"></i>
                <span>สวัสดิการ</span>
            </a>
        </div>

        <div class="request-documents">
            <a href="request-document.php">
                <i class="fa-solid fa-file-invoice"></i>
                <span>เอกสารคำร้อง</span>
            </a>
        </div>
    </div>
    </div>
    <?php include('../user/include/footer.php') ?>
    </div>
    <!-- js -->
    <!-- ป๊อปอัพเมื่อกดสลิปเงินเดือน -->
    <script>
        let popup = document.getElementById("popup");
        //เปิดป้อปอัพ
        function openPopup() {
            popup.classList.add("open-popup");
        }
        // ปิดป้อปอัพ ปุ่มยกเลิก
        function closePopup() {
            popup.classList.remove("open-popup");
        }
        // นำทางไปยังหน้าสลิป ปุ่มดูสลิปเงินเดือน
        function openPage() {
            window.location.href = "salary-slip.php";
        }
    </script>

    <script>
        let popup1 = document.getElementById("popup1");
        //เปิดป้อปอัพ
        function openPopup1() {
            popup1.classList.add("open-popup");
        }
        // ปิดป้อปอัพ ปุ่มยกเลิก
        function closePopup1() {
            popup1.classList.remove("open-popup");
        }
        // นำทางไปยังหน้าสรุปเงินเดือน ปุ่มสรุปเงินเดือน
        function sumPage() {
            window.location.href = "salary-summary.php";
        }
    </script>

</body>

</html>