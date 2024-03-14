<!-- ขอเอกสารคำร้อง หนังสือรับรองเงินเดือน -->
<?php include('../user/include/header.php') ?>
<link rel="stylesheet" href="styles/request-document-add.css">

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
            <span>วัตถุประสงค์ <i class="fa-solid fa-question"></i></span>
            <textarea class="objective"></textarea>
            <span>หมายเหตุ/จัดส่งที่...</span>
            <textarea class="note"></textarea>
            <div class="toggle-switch">
                <div class="language">
                    <span>เลือกภาษา</span>
                    <span id="red">*</span>
                </div>
                <section>
                    <label for="toggle-1" class="toggle-1">
                        <input type="checkbox" name="toggle-1" id="toggle-1" class="toggle-1__input">
                        <span class="toggle-1__button"></span>
                    </label>
                </section>
            </div>
            <button><a href="salary-certificate.php">บันทึก</a></button>


        </div>

        <?php include('../user/include/footer.php') ?>
    </div>
    <!-- js -->

</body>

</html>