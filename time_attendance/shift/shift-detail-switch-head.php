<?php
// session_start();
// session_regenerate_id(true);
//---------------------------------------------------------------------------------------
header("Cache-Control: no-cache, must-revalidate");
include("../database/connectdb.php");
include('../components-desktop/head/include/header.php')
//---------------------------------------------------------------------------------------
?>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/head/shift-detail-head.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/shift-request-detail.css">
<link rel=" stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="script/loader-normal.js"></script>

<script type="text/javascript">
    function myFunction1() {
        swal.fire({
            html: '<div style="font-weight: bold; font-size: 20px;">ยืนยันการอนุมัติหรือไม่</div><br>' +
                '<img src="../IMG/question 1.png" style="width:80px; margin-top: -10px; height:80px;"></img>',
            padding: '2em',
            confirmButtonText: 'ตกลง',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#ECCD03',
            cancelButtonColor: '#FF0000',
            showCancelButton: true,

            customClass: {
                confirmButtonText: 'swal2-confirm',
                cancelButtonText: 'swal2-cancel',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                myFunction2()
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });
    }

    function myFunction2() {
        swal.fire({
            html: '<div style="font-weight: bold; font-size: 20px;">อนุมัติสำเร็จ</div><br>' +
                '<img src="../IMG/check1.png" style="width:80px; margin-top: 2px;  height:80px;"></img>',
            padding: '2em',
            showConfirmButton: false, // ไม่แสดงปุ่มตกลง
            showCancelButton: false // ไม่แสดงปุ่มยกเลิก
        }).then((result) => {
            if (result.isConfirmed) {} else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });

    }

    function myFunction3() {
        swal.fire({
            html: '<div style="font-weight: bold; font-size: 20px;">ยืนยันการปฏิเสธขอหรือไม่</div><br>' +
                '<img src="../IMG/question 1.png" style="width:80px; margin-top: -10px; height:80px;"></img>',
            padding: '2em',
            confirmButtonText: 'ตกลง',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#ECCD03',
            cancelButtonColor: '#FF0000',
            showCancelButton: true,

            customClass: {
                confirmButtonText: 'swal2-confirm',
                cancelButtonText: 'swal2-cancel',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                myFunction4();
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });
    }


    function myFunction4() {
        swal.fire({
            html: '<div style="font-weight: bold; font-size: 20px;">ปฏิเสธคำขอสำเร็จ</div><br>' +
                '<img src="../IMG/check1.png" style="width:80px; margin-top: 2px;  height:80px;"></img>',
            padding: '2em',
            showConfirmButton: false, // ไม่แสดงปุ่มตกลง
            showCancelButton: false // ไม่แสดงปุ่มยกเลิก
        }).then((result) => {
            if (result.isConfirmed) {} else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });

    }
</script>

</head>

<body>
    <div class="desktop">
        <?php include('../components-desktop/head/include/sidebar.php'); ?>
        <?php include('../components-desktop/head/include/navbar.php'); ?>

        <div class="main-container">
            <div class="pd-ltr-20 xs-pd-20-10">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <h2>คำขออนุมัติการขอสลับกะ</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor:default;">กะการทำงาน</a>
                                        </li>
                                        <li class=" breadcrumb-item">
                                            <a href="shift-request-head.php">คำขออนุมัติ</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            คำขออนุมัติการขอสลับกะ
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class=" row">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="employee-image">
                                    <label>พนักงานคนที่ 1</label>
                                    <img src="../IMG/test.jpg" alt="" style="width:30%;">
                                </div>
                                <div class="employee-info">
                                    <label>รหัสพนักงาน :</label>
                                    <label>ชื่อ-สกุล : นายฉกาจ หนูทอง</label>
                                    <label>อีเมล : </label>
                                    <label>ตำแหน่ง : </label>
                                    <label>หน่วยงาน : </label>
                                    <label></label>
                                </div>
                            </div>
                            <div class="card-box pd-30 pt-10 height-100-p" style="margin:10px 0;">
                                <div class="employee-image">
                                    <label>พนักงานคนที่ 2</label>
                                    <img src="../IMG/test.jpg" alt="" style="width:30%;">
                                </div>
                                <div class=" employee-info">
                                    <label>00015 - นายฉกาจ หนูทอง</label>
                                    <label>หน่วยงาน : </label>
                                </div>
                            </div>
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="employee-image">
                                    <label>พนักงานคนที่ 3</label>
                                    <img src="../IMG/test.jpg" alt="" style="width:30%;">
                                </div>
                                <div class="employee-info">
                                    <label>00015 - นายฉกาจ หนูทอง</label>
                                    <label>หน่วยงาน : </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="wizard-content">
                                    <form>
                                        <section>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="date">
                                                            <label>วันที่ทำรายการ:</label>
                                                            <input class="form-control" type="text" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="employee">
                                                            <label>ชื่อพนักงาน 1:</label>
                                                            <input class="form-control" type="text" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="team">
                                                            <label>รายชื่อทีม:</label>
                                                            <input class="form-control" type="text" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="switch-shift">
                                                            <label>กะเดิม:</label>
                                                            <div class="shift-new">
                                                                <input class="form-control" type="text" readonly>
                                                                <label>กะใหม่:</label>
                                                                <input class="form-control" type="text" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="employee">
                                                            <label>ชื่อพนักงาน 2:</label>
                                                            <input class="form-control" type="text" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="team">
                                                            <label>รายชื่อทีม:</label>
                                                            <input class="form-control" type="text" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="switch-shift">
                                                            <label>กะเดิม:</label>
                                                            <div class="shift-new">
                                                                <input class="form-control" type="text" readonly>
                                                                <label>กะใหม่:</label>
                                                                <input class="form-control" type="text" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="employee">
                                                            <label>ชื่อพนักงาน 3:</label>
                                                            <input class="form-control" type="text" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="team">
                                                            <label>รายชื่อทีม:</label>
                                                            <input class="form-control" type="text" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="switch-shift">
                                                            <label>กะเดิม:</label>
                                                            <div class="shift-new">
                                                                <input class="form-control" type="text" readonly>
                                                                <label>กะใหม่:</label>
                                                                <input class="form-control" type="text" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="detail">
                                                            <label>เหตุผล:</label>
                                                            <textarea class="form-control" type="text" readonly></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <div class="form-group">
                                                        <div class="inspector">
                                                            <label>ผู้ตรวจสอบ (ถ้ามี):</label>
                                                            <input class="form-control" type="text" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-primary">อนุมัติ</button>
                                                    <button class="btn btn-primary">ปฏิเสธ</button>
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
                <span>รายละเอียดคำขออนุมัติการสลับกะ</span>
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
                    <span>ชื่อพนักงาน</span>
                    <div class="name">
                        <span>นฤมล เรืองอ่อน</span>
                    </div>
                </div>
                <div class="name-team">
                    <span>รายชื่อทีม</span>
                    <div class="team-name">
                        <span>เส้นใหญ่ไข่ปลากระป๋อง</span>
                    </div>
                </div>
                <div class="display-shift">
                    <div class="shift-old">
                        <span>กะเดิม</span>
                        <div class="old">
                            <span>กะ 1</span>
                        </div>
                    </div>
                    <div class="shift-new">
                        <span>กะใหม่</span>
                        <div class="new">
                            <span>กะ 2</span>
                        </div>
                    </div>
                </div>
                <div class="display-name">
                    <span>ชื่อพนักงาน</span>
                    <div class="name">
                        <span>นฤมล เรืองอ่อน</span>
                    </div>
                </div>
                <div class="name-team">
                    <span>รายชื่อทีม</span>
                    <div class="team-name">
                        <span>เส้นใหญ่ไข่ปลากระป๋อง</span>
                    </div>
                </div>
                <div class="display-shift">
                    <div class="shift-old">
                        <span>กะเดิม</span>
                        <div class="old">
                            <span>กะ 1</span>
                        </div>
                    </div>
                    <div class="shift-new">
                        <span>กะใหม่</span>
                        <div class="new">
                            <span>กะ 2</span>
                        </div>
                    </div>
                </div>
                <div class="display-name">
                    <span>ชื่อพนักงาน</span>
                    <div class="name">
                        <span>นฤมล เรืองอ่อน</span>
                    </div>
                </div>
                <div class="name-team">
                    <span>รายชื่อทีม</span>
                    <div class="team-name">
                        <span>เส้นใหญ่ไข่ปลากระป๋อง</span>
                    </div>
                </div>
                <div class="display-shift">
                    <div class="shift-old">
                        <span>กะเดิม</span>
                        <div class="old">
                            <span>กะ 1</span>
                        </div>
                    </div>
                    <div class="shift-new">
                        <span>กะใหม่</span>
                        <div class="new">
                            <span>กะ 2</span>
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
                <div class="btn-approve">
                    <div class="approve">
                        <button onclick="myFunction1()">อนุมัติ</button>
                    </div>
                    <div class="reject">
                        <button onclick="myFunction3()">ปฏิเสธ</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include('../includes/footer.php') ?>