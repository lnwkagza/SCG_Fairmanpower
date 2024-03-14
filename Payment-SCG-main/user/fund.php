<!-- กองทุน -->
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
            <span>กองทุน</span>
        </div>

        <div class="content">
            <div class="btn-blue">
                <a href="provident-fund.php">
                    <span>กองทุนสำรองเลี้ยงชีพ (อัตรา นโยบาย ผู้รับผลประโยชน์)</span>
                    <i class="fa-solid fa-caret-right"></i>
                </a>
            </div>
            <div class="btn-blue">
                <a href="social-security-fund.php">
                    <span>กองทุนสำรองเลี้ยงชีพ (เงินสะสมและผลตอบแทน)</span>
                    <i class="fa-solid fa-caret-right"></i>
                </a>
            </div>
        </div>

        <?php include('../user/include/footer.php') ?>
    </div>
    <!-- js -->

</body>

</html>