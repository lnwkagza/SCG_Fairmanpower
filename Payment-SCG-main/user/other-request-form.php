<!-- แบบฟอร์มคำร้องอื่นๆ -->
<?php include('../user/include/header.php') ?>
<link rel="stylesheet" href="styles/other-request-form.css">

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
            <span>แบบฟอร์มคำร้องอื่นๆ</span>
        </div>

        <div class="content">
            <span>ชื่อแบบฟอร์ม</span>
            <textarea class="name"></textarea>
            <span>วัตถุประสงค์ <i class="fa-solid fa-question"></i></span>
            <textarea class="objective"></textarea>
            <span>หมายเหตุ/จัดส่งที่...</span>
            <textarea class="note"></textarea>
            <div class="date">
                <div class="start">
                    <label>ช่วงวันที่เริ่มต้น :</label>
                    <input class="day" type="date">
                </div>
                <div class="end">
                    <label>สิ้นสุด :</label>
                    <input class="day" type="date">
                </div>
            </div>
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
            <button><a href="other-request-form-list.php">บันทึก</a></button>


        </div>

        <?php include('../user/include/footer.php') ?>
    </div>
    <!-- js -->

</body>

</html>