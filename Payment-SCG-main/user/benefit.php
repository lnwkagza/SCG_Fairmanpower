<!-- สวัสดิการ -->
<?php include('../user/include/header.php') ?>
<link rel="stylesheet" href="styles/fund.css">
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
            <span>สวัสดิการ</span>
        </div>

        <div class="content">
            <div class="btn-blue">
                <a href="benefit-1.php">
                    <span>สรุปข้อมูลการรักษาพยาบาล</span>
                    <i class="fa-solid fa-caret-right"></i>
                </a>
            </div>
            <div class="btn-blue">
                <a href="benefit-2.php">
                    <span>ประกันอุบัติเหตุส่วนเพิ่ม</span>
                    <i class="fa-solid fa-caret-right"></i>
                </a>
            </div>
            <div class="btn-blue">
                <a href="benefit-3.php">
                    <span>เปลี่ยนแปลงผู้รับผลประโยชน์ประกันกลุ่ม</span>
                    <i class="fa-solid fa-caret-right"></i>
                </a>
            </div>
            <div class="btn-blue">
                <a href="benefit-4.php">
                    <span>เปลี่ยนแปลงผู้รับผลประโยชน์ประกันสังคม</span>
                    <i class="fa-solid fa-caret-right"></i>
                </a>
            </div>
        </div>

        <?php include('../user/include/footer.php') ?>
    </div>
    <!-- js -->

</body>

</html>