<?php
session_start();
include('../components-desktop/admin/include/header.php');
include("../database/connectdb.php");
?>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/admin/report-admin.css">

<!-- CSS Mobile -->
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/report-admin.css">

</head>

<body>
    <div class="desktop">
        <?php include('../components-desktop/admin/include/sidebar.php'); ?>
        <?php include('../components-desktop/admin/include/navbar.php'); ?>
        <div class="main-container">
            <div class="pd-ltr-20 xs-pd-20-10">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <h2>รายละเอียดคำขอแจ้งปัญหาการใช้งาน</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="report-admin.php">คำขอแจ้งปัญหาการใช้งาน</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            รายละเอียดคำขอแจ้งปัญหาการใช้งาน
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p" id="box1">
                                <div>
                                    <section>
                                        <form>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <div class="form-group">
                                                        <div>
                                                            <label>ผู้แจ้งปัญหา:</label>
                                                            <input class="form-control" value="เอกพงศ์ มีสุข" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <div class="form-group">
                                                        <div>
                                                            <label>วัน/เวลา ที่แจ้งปัญหา:</label>
                                                            <input class="form-control" value="2024-01-16 : 14.46.29" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <div class="form-group">
                                                        <div>
                                                            <label>ประเภทปัญหา:</label>
                                                            <input class="form-control" value="แจ้งปัญหาของกะการทำงาน" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <div class="form-group">
                                                        <div>
                                                            <label>รายละเอียดปัญหา:</label>
                                                            <textarea class="form-control" readonly> กะการทำงานไม่สามารถใช้งานได้ </textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </section>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p" id="box2">
                                <div>
                                    <section>
                                        <form>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div>
                                                            <label>ไฟล์แนบ:</label>
                                                            <div>
                                                                <img src="../IMG/minmin.jpg" alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mobile">
        <div class="navbar">
            <div class="div-span">
                <span>รายละเอียดการแจ้งปัญหา</span>
            </div>
        </div>
        <div class="container">
            <div class="display-topic">
                <span>รายละเอียดปัญหาการใช้งาน</span>
            </div>
            <div class="box-detail">
                <div class="display-name">
                    <span>ชื่อ - สกุล :</span>
                    <span class="name">เอกพงศ์ มีสุข</span>
                </div>
                <div class="display-date">
                    <span>วันเวลาที่แจ้งปัญหา : </span>
                    <span class="date">2024-01-16 : 14.46.29</span>
                </div>
                <div class="display-type">
                    <span>ประเภทปัญหา : </span>
                    <span class="date">แจ้งปัญหาของกะการทำงาน</span>
                </div>
                <div class="display-detail-problem">
                    <span>รายละเอียดปัญหา :</span>
                    <span class="detail">เมื่อคืนหนูหลับตา แล้วรู้สึกมืดพี่</span>
                </div>
                <div class="display-file">
                    <span>ไฟล์แนบ :</span>
                    <div class="boximg">
                        <img src="../IMG/minmin.jpg" alt="">
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
<?php include('../includes/footer.php') ?>