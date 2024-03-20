<?php
include('../components-desktop/head/include/header.php');
?>
<link rel="stylesheet" href="../assets/css/check-in-warning.css">
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">

<title>ข้อพึงระวัง</title>
</head>


<body>
    <div class="desktop">
        <div class="container">
            <div class="box-warning">
                <div class="btn-cross">
                    <a href="check-in-attendance-schedule-head.php"><img src="../IMG/cross-warn.png" alt=""></a>
                </div>
                <div class="top-warning">
                    <img src="../IMG/warnY.png" alt="">
                    <span>พนักงานที่กระทำความผิดวินัยตามที่ระบุไว้ให้ตัดคะแนน</span>
                    <span>ความประพฤติของพนักงานผู้นั้น ตามลักษณะความผิดดังนี้</span>
                </div>

                <div class="table-score1">
                    <table>
                        <thead>
                            <tr>
                                <th>
                                    ลักษณะของความผิด
                                </th>
                                <th>
                                    ครั้งที่ 1
                                </th>
                                <th>
                                    ครั้งที่ 2
                                </th>
                                <th>
                                    ครั้งที่ 3
                                </th>
                                <th>
                                    ครั้งที่ 4
                                </th>
                                <th>
                                    ครั้งต่อไป
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="detail">
                                    เข้างานสาย
                                </td>
                                <td>
                                    -1
                                </td>
                                <td>
                                    -2
                                </td>
                                <td>
                                    -3
                                </td>
                                <td>
                                    -4
                                </td>
                                <td>
                                    -5
                                </td>
                            </tr>
                            <tr>
                                <td class="detail">
                                    ละทิ้งหน้าที่ ขาดงานกลับก่อนเวลา <br>เลิกงานหรือออกกะเมื่อถึงเวลางาน
                                    โดยผู้รับงานกะต่อไปยังไม่มารับงาน หรือโดยหัวหน้ากะหรือผู้บังคับบัญชาไม่ได้อนุญาต
                                </td>
                                <td>
                                    -5
                                </td>
                                <td>
                                    -10
                                </td>
                                <td>
                                    -20
                                </td>
                                <td>
                                    -40
                                </td>
                                <td>
                                    -80
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bottom-warning">
                    <div class="text-warning">
                        <span>ในรอบประเมินผลการปฏิบัติงาน</span>
                        <span><br>นับแต่วันที่ 1 ตุลาคมถึงวันที่ 30 กันยายน ของปีถัดไปหากพนักงานทั่วไป</span>
                    </div>
                </div>

                <div class="label-score">
                    <div class="bg-label">
                        <div class="top-label">
                            <span>คะแนนความประพฤติ</span>
                            <img src="../IMG/60.png" alt="">
                            <span style="color: #FFCB08; margin-top: 5%;">จะไม่ได้รางวัลประจำปี</span>
                        </div>
                    </div>
                    <div class="bg-label">
                        <div class="top-label">
                            <span>คะแนนความประพฤติ</span>
                            <img src="../IMG/80.png" alt="">
                            <span
                                style="color: #F33030; text-align: center;">อาจถูกพิจารณาเลิกจ้าง<br>โดยได้รับค่าชดเชย</span>
                        </div>
                    </div>
                </div>

                <div class="display-warning">
                    <span>พนักงานที่ถูกตัดคะแนนความประพฤติ จนเป็นเหตุให้ไม่ได้รับเงินรางวัล<br></span>
                    <span>ประจำปีติดต่อกัน 3 ปี <span>อาจถูกพิจารณาเลิกจ้าง</span></span>
                </div>

                <div class="button" onclick="window.location.href='check-in-attendance-schedule.php'">ตกลง</div>
            </div>
        </div>
    </div>

    <div class="mobile">
        <div class="container">
            <div class="box-warning">
                <div class="btn-cross">
                    <a href="check-in-attendance-schedule.php"><img src="../IMG/cross-warn.png" alt=""></a>
                </div>
                <div class="top-warning">
                    <img src="../IMG/warnY.png" alt="">
                    <span>พนักงานที่กระทำความผิดวินัยตามที่ระบุไว้ให้ตัดคะแนน</span>
                    <span>ความประพฤติของพนักงานผู้นั้น ตามลักษณะความผิดดังนี้</span>
                </div>

                <div class="table-score1">
                    <table>
                        <tr>
                            <th>
                                ลักษณะของความผิด
                            </th>
                            <th>
                                ครั้ง 1
                            </th>
                            <th>
                                ครั้ง 2
                            </th>
                            <th>
                                ครั้ง 3
                            </th>
                            <th>
                                ครั้ง 4
                            </th>
                            <th>
                                ครั้งต่อไป
                            </th>
                        </tr>
                        <tr>
                            <td class="detail">
                                เข้างานสาย
                            </td>
                            <td>
                                -1
                            </td>
                            <td>
                                -2
                            </td>
                            <td>
                                -3
                            </td>
                            <td>
                                -4
                            </td>
                            <td>
                                -5
                            </td>
                        </tr>
                        <tr>
                            <td class="detail">
                                ละทิ้งหน้าที่ ขาดงานกลับก่อนเวลา <br>เลิกงานหรือออกกะเมื่อถึงเวลางาน
                                โดยผู้รับงานกะต่อไปยังไม่มารับงาน หรือโดยหัวหน้ากะหรือผู้บังคับบัญชาไม่ได้อนุญาต
                            </td>
                            <td>
                                -5
                            </td>
                            <td>
                                -10
                            </td>
                            <td>
                                -20
                            </td>
                            <td>
                                -40
                            </td>
                            <td>
                                -80
                            </td>
                        </tr>
                    </table>
                </div>


                <div class="bottom-warning">
                    <div class="text-warning">
                        <span>ในรอบประเมินผลการปฏิบัติงาน</span>
                        <span><br>นับแต่วันที่ 1 ตุลาคมถึงวันที่ 30 กันยายน ของปีถัดไปหากพนักงานทั่วไป</span>
                    </div>
                </div>

                <div class="label-score">
                    <div class="bg-label">
                        <div class="top-label">
                            <span>คะแนนความประพฤติ</span>
                            <img src="../IMG/60.png" alt="">
                            <span style="color: #FFCB08; margin-top: 5%;">จะไม่ได้รางวัลประจำปี</span>
                        </div>
                    </div>
                    <div class="bg-label">
                        <div class="top-label">
                            <span>คะแนนความประพฤติ</span>
                            <img src="../IMG/80.png" alt="">
                            <span
                                style="color: #F33030; text-align: center;">อาจถูกพิจารณาเลิกจ้าง<br>โดยได้รับค่าชดเชย</span>
                        </div>
                    </div>
                </div>

                <div class="display-warning">
                    <span>พนักงานที่ถูกตัดคะแนนความประพฤติ จนเป็นเหตุให้ไม่ได้รับเงินรางวัล<br></span>
                    <span>ประจำปีติดต่อกัน 3 ปี <span style="color: #FFCB08;">อาจถูกพิจารณาเลิกจ้าง</span></span>
                </div>

                <div class="button"><a href="check-in-attendance-schedule.php">ตกลง</a></div>
            </div>
        </div>
    </div>
</body>