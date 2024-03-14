<!-- หนังสือรับรองเงินเดือน -->
<?php include('../user/include/header.php') ?>
<link rel="stylesheet" href="styles/salary-certificate.css">

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
            <span>หนังสือรับรองเงินเดือน</span>
        </div>

        <div class="content">
            <div class="btn">
                <a href="request-document-add.php"><span><i class="fa-solid fa-plus"></i> ขอเอกสารคำร้อง</span></a>
            </div>
            <div class="tb">
                <table>
                    <thead>
                        <tr>
                            <th>รายละเอียด</th>
                            <th>สถานะ</th>
                            <th>รายละเอียด</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="data-l">
                                    <span class="bold">ยื่นขอ "เอกสารรับรองเงินเดือน"</span>
                                    <span>ยื่นขอวันที่ 18/01/2024</span>
                                    <span>วัตถุประสงค์ : Apply for a loan</span>
                                    <span>หมายเหตุ/จัดส่งที่ : Email</span>
                                </div>
                            </td>
                            <td class="status">
                                <div class="data-r">
                                    <span class="waiting">รออนุมัติ...</span>
                                    <span><i class="fa-solid fa-calendar-days"></i> 18/01/2024</span>
                                    <span><i class="fa-solid fa-clock"></i> 11:48</span>
                                </div>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="data-l">
                                    <span class="bold">ยื่นขอ "เอกสารรับรองเงินเดือน"</span>
                                    <span>ยื่นขอวันที่ 18/01/2024</span>
                                    <span>วัตถุประสงค์ : Apply for a loan</span>
                                    <span>หมายเหตุ/จัดส่งที่ : Email</span>
                                </div>
                            </td>
                            <td class="status">
                                <div class="data-r">
                                    <span class="approv">อนุมัติแล้ว</span>
                                    <span class="bold">โดย...ทวีชัย โพธิ์ดี</span>
                                    <span><i class="fa-solid fa-calendar-days"></i> 18/01/2024</span>
                                    <span><i class="fa-solid fa-clock"></i> 11:48</span>
                                </div>
                            </td>
                            <td>
                                <a href="salary-certificate-pdf.php"><i class="fa-solid fa-file-pdf"></i> </a>
                            </td>
                        </tr>
                        
                    </tbody>

                </table>
            </div>


        </div>

        <?php include('../user/include/footer.php') ?>
    </div>
    <!-- js -->

</body>

</html>