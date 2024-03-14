<!-- การจ่ายเงินเดือน งวดเต็ม -->
<?php include('../admin/include/header.php') ?>
<link rel="stylesheet" href="../vendors/styles/slip.css">
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
                                <h3>สถานะเงินเดือน : Status Payment</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                        <div class="card-box pd-30 pt-10 height-100-p">
                            <div class="row1">
                                <div class="row1-l">
                                    <span>สลิปเงินเดือนพนักงาน</span>
                                    <span class="f1">BL10221</span>
                                    <span>เลขที่</span>
                                    <span class="f1">PAY25112023</span>
                                </div>
                                <div class="row1-r">
                                    <button id="btn1"> <i class="fa-solid fa-download"></i> download</button>
                                    <button id="btn2"> <i class="fa-solid fa-print"></i> print</button>
                                </div>
                            </div>

                            <div class="row2">
                                <div class="row2-l">
                                    <span class="row2-l1">นาย ทดสอบ ระบบ (Tax ID : 21518949856)</span>
                                    <span class="row2-l1">ข้อมูลพนักงาน</span>
                                    <div class="row2-l2">
                                        <div class="left">
                                            <span>ชื่อ - สกุล :</span>
                                            <span>รหัสพนักงาน :</span>
                                            <span>แผนก :</span>
                                            <span>ตำแแหน่ง :</span>
                                            <span>รับเงินโดย :</span>
                                        </div>
                                        <div class="right">
                                            <span>นาย ทดสอบ ระบบ (Tax ID : 21518949856)</span>
                                            <span>BL10221</span>
                                            <span>บัญชี</span>
                                            <span>หัวหน้า</span>
                                            <span>ธ.กสิกรไทย 0481587812</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row2-r">
                                    <span class="row2-r1">สลิปเงินเดือน</span>
                                    <span class="row2-r1">รวมเงินเดือน</span>
                                    <span>ตั้งแต่วันที่ : 01/11/2023</span>
                                    <span>ถึงวันที่ : 15/11/2023</span>
                                    <span>วันที่ชำระเงินเดือน : 25/11/2023</span>
                                </div>
                            </div>
                            <hr>

                            <div class="tb">
                                <span class="row2-l1">รายการเงินเดือน 11/2023</span>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>รายได้</th>
                                            <th class="ct">จำนวนเงิน (บาท)</th>
                                            <th>รายหัก</th>
                                            <th class="ct">จำนวนเงิน (บาท)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>เงินเดือน</td>
                                            <td>15,000.00</td>
                                            <td>ประกันสังคม</td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td>ค่าที่พัก</td>
                                            <td>-</td>
                                            <td>ขาด/สาย</td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td>ค่าล่วงเวลา</td>
                                            <td>1,300.00</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>เบี้ยขยัน</td>
                                            <td>-</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr class="tb1">
                                            <td>รายได้รวม</td>
                                            <td>16,300.00</td>
                                            <td>รายได้หัก</td>
                                            <td>00.00</td>
                                        </tr>
                                        <tr class="tb1">
                                            <td class="ct" colspan="2">หนึ่งหมื่นหกพันสามร้อยบาทถ้วน</td>
                                            <td colspan="2">
                                                <div class="tb2">
                                                    <span>เงินรับสุทธิ</span>
                                                    <span>16,300.00</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="tb-b">
                                <div id="sp">รวมรายได้ทั้งปี 2023 :</div>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>เงินได้สะสม (บาท)</th>
                                            <th>ภาษีหัก ณ ที่จ่ายสะสม (บาท)</th>
                                            <th>เงินประกันสังคมสะสม (บาท)</th>
                                            <th>เงินกองทุนสำรองสะสม (บาท)</th>
                                            <th>ลายเซ็นผู้รับเงิน</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>16,300.00</td>
                                            <td></td>
                                            <td>00.00</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="backward">
                            <button>ย้อนกลับ</button>
                        </div>
                    </div>
                </div>

            </div>

            <?php include('../admin/include/footer.php'); ?>
        </div>
    </div>
    <!-- js -->

    <!-- เมื่อกดปุ่มย้อนกลับให้เด้งไปหน้าก่อนหน้า -->
    <script>
        document.querySelector('.backward button').addEventListener('click', function() {
            history.go(-1); // นำผู้ใช้ไปยังหน้าก่อนหน้า
        });
    </script>

    <?php include('../admin/include/scripts.php') ?>
</body>

</html>