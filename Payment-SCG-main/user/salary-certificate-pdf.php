<!-- หนังสือรับรองเงินเดือน pdf -->
<?php include('../user/include/header.php') ?>
<link rel="stylesheet" href="styles/salary-certificate-pdf.css">

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
            <span>หนังสือรับรองเงินเดือน</span>
        </div>

        <div class="content">
            <div class="btn">
                <button id="download"> <i class="fa-solid fa-download"></i> download</button>
                <button id="print"> <i class="fa-solid fa-print"></i> print</button>
            </div>

        </div>

        <?php include('../user/include/footer.php') ?>
    </div>
    <!-- js -->

</body>

</html>