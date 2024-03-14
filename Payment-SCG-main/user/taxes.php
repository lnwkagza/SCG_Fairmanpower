<!-- ภาษี -->
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
            <span>ภาษี</span>
        </div>

        <div class="content">
            <div class="btn-blue">
                <a href="taxes-1.php">
                    <span>แบบแจ้งรายการเพื่อหักลดหย่อนภาษี (ล.ย.01)</span>
                    <i class="fa-solid fa-caret-right"></i>
                </a>
            </div>
            <div class="btn-blue">
                <a href="taxes-2.php">
                    <span>แบบคำนวณภาษีเงินได้ ภงด.91</span>
                    <i class="fa-solid fa-caret-right"></i>
                </a>
            </div>
            <div class="btn-blue">
                <a href="taxes-3.php">
                    <span>หนังสือรับรองการหัก ณ ที่จ่าย 50 ทวิ</span>
                    <i class="fa-solid fa-caret-right"></i>
                </a>
            </div>
            <div class="btn-blue">
                <a href="taxes-4.php">
                    <span>หนังสือรับรองการหักลดหย่อนค่าอุปการะเลี้ยงดูบิดา-มารดา</span>
                    <i class="fa-solid fa-caret-right"></i>
                </a>
            </div>
        </div>

        <?php include('../user/include/footer.php') ?>
    </div>
    <!-- js -->

</body>

</html>