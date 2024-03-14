<!-- เอกสารรอการอนุมัติ ลางาน -->
<?php include('../admin/include/header.php') ?>
<link rel="stylesheet" href="../vendors/styles/doc-waitingApprov.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>


<body>

    <?php include('../admin/include/navbar.php') ?>
    <?php include('../admin/include/sidebar.php') ?>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <h3>คำนวณเงินเดือน : Calculator Payment</h3>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">คำนวณเงินเดือน</li>
                                    <li class="breadcrumb-item "><a href="closeThePeriod.php">ปิดงวด</a></li>
                                    <li class="breadcrumb-item"><a href="saveAccount.php">บันทึกบัญชี</a></li>
                                    <li class="breadcrumb-item"><a href="">สรุปผลรายเดือน</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                        <div class="card-box pd-30 pt-10 height-100-p">
                            <div class="bar">
                                <a href="calculator_payment.php">คำนวณเงินเดือนพนักงาน <i class="fa-regular fa-user"></i></a>
                                <a style="color:#338ac6 ;">เอกสารรอการอนุมัติ <i class="fa-solid fa-file"></i></a>
                                <a href="resultsSummary.php">สรุปผลการคำนวณ <i class="fa-regular fa-file"></i></a>
                            </div>
                            <div class="approv">
                                <button id="approv1"><i class="fa-solid fa-check"></i>อนุมัติทั้งหมด</button>
                                <button id="approv2"><i class="fa-solid fa-xmark"></i>ไม่อนุมัติทั้งหมด</button>
                            </div>
                            <div class="t-tb">
                                <div class="program">
                                    <a href="doc-waitingApprov.php">ทั้งหมด <div class="bug">11</div></a>
                                    <a href="doc-waitingApprov-OT.php">โอที<div class="bug" style="top: 39px;">9</div></a>
                                    <a style="color:#338ac6;font-weight: bolder;">ลางาน<div class="bug" style="top: 73px;">7</div></a>
                                    <a href="doc-waitingApprov-WS.php">กะการทำงาน<div class="bug" style="top: 107px;">5</div></a>
                                    <a href="doc-waitingApprov-DO.php">วันหยุด<div class="bug" style="top: 141px;">3</div></a>
                                </div>

                                <table class="data-table table stripe hover nowrap">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="checkAll"></th>
                                            <th>สถานะ</th>
                                            <th>ลำดับ</th>
                                            <th>รหัสพนักงาน</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="feth" style="background-color: #7ab0d5;">
                                            <td><input type="checkbox"></td>
                                            <td colspan="3" style="font-size: 18px; color: #ffff;">SCG120819 : นางสาววิจิตรา แซ่เอีย</td>
                                        </tr>
                                        <tr>
                                            <td><input type="checkbox"></td>
                                            <td>ยื่นขอ "ลากิจได้รับค่าจ้าง"</td>
                                            <td>รออนุมัติ...</td>
                                            <td id="approv-child">
                                                <button id="approv1-child"><i class="fa-solid fa-check"></i>อนุมัติ</button>
                                                <button id="approv2-child"><i class="fa-solid fa-xmark"></i>ไม่อนุมัติ</button>
                                            </td>
                                        </tr>
                                        <tr class="feth" style="background-color: #7ab0d5;">
                                            <td><input type="checkbox"></td>
                                            <td colspan="3" style="font-size: 18px; color: #ffff;">SCG234963 : นางสาวภัทราวดี ละมูลสุข</td>
                                        </tr>
                                        <tr>
                                            <td><input type="checkbox"></td>
                                            <td>ยื่นขอ "ลากิจได้รับค่าจ้าง"</td>
                                            <td>รออนุมัติ...</td>
                                            <td>
                                                <div id="approv-child">
                                                    <button id="approv1-child"><i class="fa-solid fa-check"></i>อนุมัติ</button>
                                                    <button id="approv2-child"><i class="fa-solid fa-xmark"></i>ไม่อนุมัติ</button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>

                <?php include('../admin/include/footer.php'); ?>
            </div>
        </div>
        <!-- js -->
        <script>
    // เลือกทุก element ที่มี class "bug"
    var bugElements = document.querySelectorAll('.bug');

    // วนลูปทุกรายการเพื่อตรวจสอบค่า
    bugElements.forEach(function(bugElement) {
        // ตรวจสอบค่าใน bugElement
        var bugValue = parseInt(bugElement.textContent);

        // ถ้าค่าเป็น 0 ให้ซ่อน bugElement
        if (bugValue === 0) {
            bugElement.style.display = 'none';
        }
    });
</script>



        <?php include('../admin/include/scripts.php') ?>
</body>

</html>