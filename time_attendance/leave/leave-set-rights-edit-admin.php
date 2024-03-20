<?php
include('header.php');
?>
<link rel="stylesheet" href="../assets/css/leave-set-rights.css">
<link rel="stylesheet" href="../components-desktop/side-menu-admin.css">
<link rel="stylesheet" href="../components-desktop/navbar-profile.css">
<link rel=" stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<title>จัดการสิทธิการลา</title>
</head>

<body>
    <div class="grid-container">

        <div class="column-left">
            <?php include('../components-desktop/side-menu-admin.php'); ?>
        </div>

        <div class="column-right">
            <?php include('../components-desktop/navbar-profile.php'); ?>

            <div class=" title">
                <div class="titlename">จัดการสิทธิการลา</div>
            </div>

            <div class="main-class">
                <div class="navbar">
                    <div class="div-span">
                        <span>จัดการสิทธิการลา</span>
                    </div>
                </div>

                <div class="searchEm">
                    <input type="text" placeholder="ค้นหาพนักงานเพื่อเช็คสิทธิ">
                    <a href="leave-set-rights-edit-admin.php"><img src="../IMG/search.png"></a>
                    <a href="leave-set-rights-admin.php"><img src="../IMG/cross.png"></a>
                </div>

                <div class="dataEm">
                    <div class="id-nameEm">
                        <span>รหัส-ชื่อพนักงาน : <input type="text"></span>
                    </div>
                    <div class="level-ageWork">
                        <span>ระดับพนักงาน : <input type="text"></span>
                        <span>อายุงาน : <input type="text"></span>
                    </div>
                </div>

                <div class="buttonSave">
                    <div class="save">
                        <a href="leave-set-rights-admin.php">บันทึกข้อมูล</a>
                    </div>
                </div>

                <div class="break">
                    <div class="box">
                        <div class="dataDate">
                            <div class="imgDate">
                                <img src="../IMG/hammock1.png">
                            </div>
                            <div class="topic-text">
                                <span>วันหยุดพักผ่อนประจำปี </span>
                                <span>ระบุจำนวนวัน </span>
                            </div>
                            <div class="boxRight">
                                <div class="sub-text">
                                    <input type="text">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <div class="dataDate">
                            <div class="imgDate">
                                <img src="../IMG/plane.png">
                            </div>
                            <div class="topic-text">
                                <span>วันหยุดพักผ่อนประจำปีสะสม </span>
                                <span>ระบุจำนวนวัน </span>
                            </div>
                            <div class="boxRight">
                                <div class="sub-text">
                                    <input type="text">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <div class="dataDate">
                            <div class="imgDate">
                                <img src="../IMG/work1.png">
                            </div>
                            <div class="topic-text">
                                <span>ลากิจ </span>
                                <span>ระบุจำนวนวัน </span>
                            </div>
                            <div class="boxRight">
                                <div class="sub-text">
                                    <input type="text">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <div class="dataDate">
                            <div class="imgDate">
                                <img src="../IMG/work1_1.png">
                            </div>
                            <div class="topic-text">
                                <span>ลากิจ แบบไม่จ่าย </span>
                                <span>ระบุจำนวนวัน </span>
                            </div>
                            <div class="boxRight">
                                <div class="sub-text">
                                    <input type="text">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <div class="dataDate">
                            <div class="imgDate">
                                <img src="../IMG/sick.png">
                            </div>
                            <div class="topic-text">
                                <span>ลาป่วย </span>
                                <span>ระบุจำนวนวัน </span>
                            </div>
                            <div class="boxRight">
                                <div class="sub-text">
                                    <input type="text">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <div class="dataDate">
                            <div class="imgDate">
                                <img src="../IMG/Pregnant.png">
                            </div>
                            <div class="topic-text">
                                <span>ลาก่อนคลอด </span>
                                <span>ระบุจำนวนวัน </span>
                            </div>
                            <div class="boxRight">
                                <div class="sub-text">
                                    <input type="text">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <div class="dataDate">
                            <div class="imgDate">
                                <img src="../IMG/birth.png">
                            </div>
                            <div class="topic-text">
                                <span>ลาคลอด </span>
                                <span>ระบุจำนวนวัน </span>
                            </div>
                            <div class="boxRight">
                                <div class="sub-text">
                                    <input type="text">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <div class="dataDate">
                            <div class="imgDate">
                                <img src="../IMG/don't birth.png">
                            </div>
                            <div class="topic-text">
                                <span>ลาคลอด ไม่จ่าย </span>
                                <span>ระบุจำนวนวัน </span>
                            </div>
                            <div class="boxRight">
                                <div class="sub-text">
                                    <input type="text">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <div class="dataDate">
                            <div class="imgDate">
                                <img src="../IMG/holiday.png">
                            </div>
                            <div class="topic-text">
                                <span>ลาพักร้อน </span>
                                <span>ระบุจำนวนวัน </span>
                            </div>
                            <div class="boxRight">
                                <div class="sub-text">
                                    <input type="text">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <div class="dataDate">
                            <div class="imgDate">
                                <img src="../IMG/training.png">
                            </div>
                            <div class="topic-text">
                                <span>ลาอบรม </span>
                                <span>ระบุจำนวนวัน </span>
                            </div>
                            <div class="boxRight">
                                <div class="sub-text">
                                    <input type="text">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <div class="dataDate">
                            <div class="imgDate">
                                <img src="../IMG/father.png">
                            </div>
                            <div class="topic-text">
                                <span>ลาความเป็นพ่อ </span>
                                <span>ระบุจำนวนวัน </span>
                            </div>
                            <div class="boxRight">
                                <div class="sub-text">
                                    <input type="text">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <div class="dataDate">
                            <div class="imgDate">
                                <img src="../IMG/father1.png">
                            </div>
                            <div class="topic-text">
                                <span>ลาความเป็นพ่อ ไม่จ่าย </span>
                                <span>ระบุจำนวนวัน </span>
                            </div>
                            <div class="boxRight">
                                <div class="sub-text">
                                    <input type="text">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <div class="dataDate">
                            <div class="imgDate">
                                <img src="../IMG/religion.png">
                            </div>
                            <div class="topic-text">
                                <span>ลาศาสนา </span>
                                <span>ระบุจำนวนวัน </span>
                            </div>
                            <div class="boxRight">
                                <div class="sub-text">
                                    <input type="text">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <div class="dataDate">
                            <div class="imgDate">
                                <img src="../IMG/religion1.png">
                            </div>
                            <div class="topic-text">
                                <span>ลาศาสนา ไม่จ่าย </span>
                                <span>ระบุจำนวนวัน </span>
                            </div>
                            <div class="boxRight">
                                <div class="sub-text">
                                    <input type="text">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <div class="dataDate">
                            <div class="imgDate">
                                <img src="../IMG/another.png">
                            </div>
                            <div class="topic-text">
                                <span>ลาอื่น ๆ </span>
                                <span>ระบุจำนวนวัน </span>
                            </div>
                            <div class="boxRight">
                                <div class="sub-text">
                                    <input type="text">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <?php include('../includes/footer.php'); ?>
</body>