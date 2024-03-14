<!-- ขอเอกสารคำร้อง ฟอร์มขอเบิกค่ารักษาพยาบาล -->
<?php include('../user/include/header.php') ?>
<link rel="stylesheet" href="styles/medical-expenses-add.css">

<body>
    <div class="main-container">
        <!-- แถบบนสุด -->
        <div class="navbar">
            <!-- รูปปุ่มย้อนกลับไปหน้าก่อนหน้า -->
            <div>
                <a href="request-document.php" >
                    <img id="backIcon" src="bw.png">
                </a>
            </div>
            <span>เบิกค่ารักษาพยาบาล</span>
        </div>

        <div class="content">
            <div class="row1">
                <span>สิทธิค่ารักษาพยาบาลคงเหลือ</span>
                <span>10,000.00</span>
            </div>
            <div class="row2">
                <button>รายละเอียดค่ารักษาพยาบาล</button>
            </div>
            <div class="row3">
                <span>โรคที่รักษาและรายละเอียด</span>
                <textarea></textarea>
            </div>
            <div class="row4">
                <span>โรงพยาบาลที่เข้ารับการรักษา</span>
                <input type="text">
            </div>
            <div class="row5">
                <span>วันที่เริ่มต้นรับการรักษา</span>
                <input type="date">
            </div>
            <div class="row6">
                <span>วันที่สิ้นสุดรับการรักษา</span>
                <input type="date">
            </div>
            <div class="row7">
                <span>จำนวนขอเบิก</span>
                <input type="text">
            </div>

            <div class="row8">
                <span>เลขที่ใบเสร็จ</span>
                <input type="text">
            </div>
            <div class="row9">
                <span>วันที่ใบเสร็จ</span>
                <input type="text">
            </div>
            <div class="row10">
                <span>แทรกหลักฐาน</span>
                <div class="row10-1">
                    <input type="file" accept="image/*,.pdf" />
                    <button>อัปโหลด</button>
                </div>
            </div>
            <button id="save"><a href="medical-expenses.php">บันทึก</a></button>
        </div>



    </div>

    <?php include('../user/include/footer.php') ?>
    </div>
    <!-- js -->

</body>

</html>