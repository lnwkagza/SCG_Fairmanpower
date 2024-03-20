<?php
// session_start();
// session_regenerate_id(true);
//---------------------------------------------------------------------------------------
header("Cache-Control: no-cache, must-revalidate");
include("../database/connectdb.php");
include('../components-desktop/admin/include/header.php')
//---------------------------------------------------------------------------------------
?>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/admin/shift-detail-admin.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/shift-request-detail.css">
<link rel=" stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="script/loader-normal.js"></script>

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
                                    <h2>รายละเอียดคำขออนุมัติการล็อกเหลี่ยม</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor:default;">กะการทำงาน</a>
                                        </li>
                                        <li class=" breadcrumb-item">
                                            <a href="shift-request-admin.php">คำขออนุมัติ/</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            รายละเอียดคำขออนุมัติการล็อกเหลี่ยม
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class=" row">
                        <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="wizard-content">
                                    <form>
                                        <section>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="date">
                                                            <label>วันล็อกเหลี่ยม:</label>
                                                            <input class="form-control" type="text" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="employee">
                                                            <label>ชื่อพนักงาน:</label>
                                                            <input class="form-control" type="text" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="shift">
                                                            <label>กะการทำงาน:</label>
                                                            <input class="form-control" type="text" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="detail">
                                                            <label>เหตุผล:</label>
                                                            <textarea class="form-control" type="text" readonly></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="inspector">
                                                            <label>ผู้ตรวจสอบ (ถ้ามี):</label>
                                                            <input class="form-control" type="text" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="head">
                                                            <label>หัวหน้า:</label>
                                                            <input class="form-control" type="text" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="display-status">
                                                            <label>สถานะ:</label>
                                                            <!-- <span class="approve">อนุมัติแล้ว</span> -->
                                                            <!-- <span class="wait">รออนุมัติ</span> -->
                                                            <input class="form-control reject" value="ปฏิเสธ" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    </form>
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
                <span>รายละเอียดคำขออนุมัติ</span>
            </div>
        </div>

        <div class="container">
            <div class="display-topic">
                <span>รายละเอียดคำขออนุมัติการจัดการทีม</span>
            </div>
            <div class="display-detail">

                <div class="display-date">
                    <span>วันล็อกเหลี่ยม</span>
                    <div class="date">
                        <span>10/10/67</span>
                    </div>
                </div>
                <div class="display-name">
                    <span>ชื่อพนักงาน</span>
                    <div class="name">
                        <span>นฤมล เรืองอ่อน</span>
                    </div>
                </div>
                <div class="display-shift">
                    <div class="shift-add">
                        <span>กะการทำงาน</span>
                        <div class="add">
                            <span>กะ 1</span>
                        </div>
                    </div>
                </div>
                <div class="reason">
                    <span>เหตุผล</span>
                    <div class="reason-req">
                        <span>เหนื่อยล้าเต็มที</span>
                    </div>
                </div>
                <div class="display-inspector">
                    <span>ผู้ตรวจสอบ (ถ้ามี):</span>
                    <div class="inspector">
                        <span>ผมปาล์มกาเกดใน</span>
                    </div>
                </div>
                <div class="display-head">
                    <span>หัวหน้า</span>
                    <div class="head">
                        <span>นฤมล คนสวย</span>
                    </div>
                </div>
                <div class="display-status">
                    <!-- <span class="approve">อนุมัติแล้ว</span> -->
                    <!-- <span class="wait">รออนุมัติ</span> -->
                    <span class="reject">ปฏิเสธ</span>
                </div>
            </div>
        </div>
    </div>

</body>
<?php include('../includes/footer.php') ?>