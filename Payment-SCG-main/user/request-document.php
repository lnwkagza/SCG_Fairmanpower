<!-- เอกสารคำร้อง -->
<?php include('../user/include/header.php') ?>
<link rel="stylesheet" href="styles/fund.css">

<body>
    <div class="main-container">
        <!-- แถบบนสุด -->
        <div class="navbar">
            <!-- รูปปุ่มย้อนกลับไปหน้าก่อนหน้า -->
            <div>
                <a href="index.php">
                    <img id="backIcon" src="bw.png">
                </a>
            </div>
            <span>เอกสารคำร้อง</span>
        </div>

        <div class="content">
            <div class="btn-blue">
                <a href="salary-certificate.php">
                    <span>หนังสือรับรองเงินเดือน</span>
                    <i class="fa-solid fa-caret-right"></i>
                </a>
            </div>
            <div class="btn-blue">
                <a href="work-certificate.php">
                    <span>หนังสือรับรองการทำงาน</span>
                    <i class="fa-solid fa-caret-right"></i>
                </a>
            </div>
            <div class="btn-blue">
                <a href="medical-expenses.php">
                    <span>เบิกค่ารักษาพยาบาล</span>
                    <i class="fa-solid fa-caret-right"></i>
                </a>
            </div>
            <div class="btn-blue">
                <a href="letter-medical.php">
                    <span>ใบส่งตัวเข้ารักษาพยาบาล</span>
                    <i class="fa-solid fa-caret-right"></i>
                </a>
            </div>
            <div class="btn-blue">
                <a href="other-request-form-list.php">
                    <span>แบบฟอร์มคำร้องอื่นๆ</span>
                    <i class="fa-solid fa-caret-right"></i>
                </a>
            </div>
        </div>

        <?php include('../user/include/footer.php') ?>
    </div>
    <!-- js -->

</body>

</html>