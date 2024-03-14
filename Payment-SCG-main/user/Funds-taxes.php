<!-- กองทุนและภาษี -->
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
            <span>กองทุนและภาษี</span>
        </div>

        <div class="content">
            <div class="btn-blue">
                <a href="fund.php">
                    <span>กองทุน</span>
                    <i class="fa-solid fa-caret-right"></i>
                </a>
            </div>
            <div class="btn-blue">
                <a href="taxes.php">
                    <span>ภาษี</span>
                    <i class="fa-solid fa-caret-right"></i>
                </a>
            </div>
        </div>

        <?php include('../user/include/footer.php') ?>
    </div>
    <!-- js -->

</body>

</html>