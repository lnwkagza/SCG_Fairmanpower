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
                                    <h2>รายละเอียดคำขออนุมัติการขอจัดการทีม</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor:default;">กะการทำงาน</a>
                                        </li>
                                        <li class=" breadcrumb-item">
                                            <a href="shift-reques/-admin.php">คำขออนุมัติ</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            รายละเอียดคำขออนุมัติการขอจัดการทีม
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
                                                            <label>วันที่ทำรายการ:</label>
                                                            <input class="form-control" type="text" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="employee">
                                                            <label>ชื่อผู้ทำรายการ:</label>
                                                            <input class="form-control" type="text" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="team">
                                                            <label>รายชื่อทีม:</label>
                                                            <input class="form-control" type="text" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="team-member">
                                                            <label>สมาชิกในทีม:</label>
                                                            <textarea class="form-control" type="text" readonly></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="work-format">
                                                            <label>ชุดการทำงาน:</label>
                                                            <input class="form-control" type="text" readonly>
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
                    <span>วันที่ทำรายการ</span>
                    <div class="date">
                        <span>10/10/67</span>
                    </div>
                </div>
                <div class="display-name">
                    <span>ชื่อผู้ทำรายการ</span>
                    <div class="name">
                        <span>นฤมล เรืองอ่อน</span>
                    </div>

                </div>
                <div class="display-team">
                    <div class="name-team">
                        <span>รายชื่อทีม</span>
                        <div class="team-name">
                            <span>เส้นใหญ่ไข่ปลากระป๋อง</span>
                        </div>

                    </div>
                    <div class="member-team">
                        <div class="topic-member">
                            <span>สมาชิกในทีม</span>
                        </div>
                        <div class="member-all-team">
                            <span>00001 นฤมล เรืองอ่อน</span>
                            <span>00008 นพดล กุลบุตร</span>
                            <span>00018 รณชัย บุญสนอง</span>
                            <span>00038 พีรพัฒ มณีมัย</span>
                        </div>
                    </div>
                    <div class="display-workSet">
                        <span>ชุดการทำงาน</span>
                        <div class="set">
                            <span>ชุด10001</span>
                        </div>
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