<?php
session_start();
include('../components-desktop/head/include/header.php');
include("../database/connectdb.php");
?>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/head/report-head.css">
<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/report.css">
<link rel="stylesheet" href="../assets/css/check-in-detail-edit.css">
<link rel="stylesheet" href="..\node_modules\sweetalert2\dist\sweetalert2.css">

<script type="text/javascript">
    function myFunction() {
        swal.fire({
            html:
                // '<img class="d-flex; align-items: center; justify-content-center" src="../../images/warningyall.png" style="width:45%px;  height:95%;"></img>' +
                '<div style="font-weight: bold; font-size: 20px;">ยืนยันการแก้ไข</div><br>' +
                '<img src="../IMG/question 1.png" style="width:80px; margin-top: -10px;  height:80px;"></img>',
            padding: '2em',
            confirmButtonText: 'ตกลง',
            confirmButtonColor: '#95E302',
            showCancelButton: true,
            cancelButtonText: 'ยกเลิก',
            cancelButtonColor: '#FF5643',
            customClass: {
                confirmButtonText: 'swal2-confirm',
                cancelButtonText: 'swal2-cancel',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "check-in-attendance-schedule-edit.php?transaction=";
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });
    }

    function submit_back() {
        window.history.back();
    }

    function submit() {
        Swal.fire({
            title: "<strong>ยืนยันการแจ้งปัญหา</strong>",
            icon: "question",
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: `ตกลง`,
            cancelButtonText: `ยกเลิก`,
        }).then((result) => {
            if (result.isConfirmed) {
                confirm(result);
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });

    }

    function confirm() {
        Swal.fire({
            title: "<strong>แจ้งปัญหาสำเร็จ</strong>",
            icon: "success",
            showCloseButton: true,
            focusConfirm: false,
            confirmButtonText: `ตกลง`,
        }).then((result) => {
            if (result.isConfirmed) {
                submit_form();
            }
        });
    }

    function submit_form() {
        let form = document.getElementById("desktop-form");
        form.action = '../processing/process_report.php';
        form.method = 'POST';
        form.submit();
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
                                    <h2>แจ้งปัญหาการใช้งาน</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p" id="box1">
                                <div class="wizard-content">
                                    <section>
                                        <form id="desktop-form" enctype="multipart/form-data">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label for="employeeid">ประเภทปัญหา</label>
                                                        <select class="custom-select form-control">
                                                            <option value="" disabled selected>เลือกประเภทปัญหา
                                                            <option value="">เลือกประเภทปัญหา</option>
                                                            <option value="แจ้งปัญหาการเข้างาน-ออกงาน">
                                                                แจ้งปัญหาการเข้างาน-ออกงาน</option>
                                                            <option value="แจ้งปัญหาของวันลา">แจ้งปัญหาของวันลา</option>
                                                            <option value="จ้งปัญหาของวันหยุด">แจ้งปัญหาของวันหยุด
                                                            </option>
                                                            <option value="แจ้งปัญหาของกะการทำงาน">
                                                                แจ้งปัญหาของกะการทำงาน</option>
                                                            <option value="แจ้งปัญหาของ OT">แจ้งปัญหาของ OT</option>
                                                            <option value="แจ้งปัญหาของระบบ">แจ้งปัญหาของระบบ</option>
                                                            <option value="แจ้งปัญหาอื่น ๆ">แจ้งปัญหาอื่น ๆ</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label>รายละเอียดคำขอลา</label>
                                                        <textarea class="form-control" name="details" id="details" rows="4" cols="50"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label>เลือกไฟล์</label>
                                                        <label for="selectFile">
                                                            <img src="../IMG/camera.png" alt="Image Button" onclick="selectFiles('display-file-name')">
                                                            <input type="file" id="selectFile" name="imageAttachment" onchange="selectFiles('display-file-name')" accept="image/*" style="display: none;">
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <div id="display-file-name"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <script>
                                                function selectFiles(file) {
                                                    const fileSelect = event.target;
                                                    const fileName = document.getElementById(file);
                                                    const filesLength = fileSelect.files;

                                                    if (filesLength.length > 0) {
                                                        fileName.textContent = `ไฟล์ที่เลือก: ${filesLength[0].name}`;
                                                    } else {
                                                        fileName.textContent = "ไม่มีไฟล์ที่เลือก";
                                                    }
                                                }

                                                function cancelFile(inputId, file) {
                                                    const fileInput = document.getElementById(inputId);
                                                    const fileInfo = document.getElementById(file);

                                                    fileInput.value = null;
                                                    fileInfo.textContent = "ไฟล์ถูกยกเลิก";

                                                }
                                            </script>
                                        </form>
                                        <div class="row">
                                            <div class="col-md-6 col-sm-12" style="display:flex;justify-content:flex-end;margin-top:20px">
                                                <div class="form-group">
                                                    <input type="button" value="ยืนยัน" class="btn-primary" onclick="submit()">
                                                </div>
                                            </div>
                                        </div>
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
                <span>แจ้งปัญหาการใช้งาน</span>
            </div>
        </div>

        <div class="display-report">
            <form action="../processing/process_report.php" method="post" enctype="multipart/form-data">
                <div class="type-problem">
                    <span>ประเภทปัญหา</span>
                    <select name="" id="">
                        <option value="">เลือกประเภทปัญหา</option>
                        <option value="แจ้งปัญหาการเข้างาน-ออกงาน">แจ้งปัญหาการเข้างาน-ออกงาน</option>
                        <option value="แจ้งปัญหาของวันลา">แจ้งปัญหาของวันลา</option>
                        <option value="จ้งปัญหาของวันหยุด">แจ้งปัญหาของวันหยุด</option>
                        <option value="แจ้งปัญหาของกะการทำงาน">แจ้งปัญหาของกะการทำงาน</option>
                        <option value="แจ้งปัญหาของ OT">แจ้งปัญหาของ OT</option>
                        <option value="แจ้งปัญหาของระบบ">แจ้งปัญหาของระบบ</option>
                        <option value="แจ้งปัญหาอื่น ๆ">แจ้งปัญหาอื่น ๆ</option>
                    </select>
                </div>
                <div class="Reason-edit-1">
                    <label>รายละเอียดปัญหา</label>
                    <textarea name="details" id="details" rows="4" cols="50" required></textarea>
                </div>

                <div class="select-img-file">
                    <span>เลือกไฟล์ :</span>
                    <div class="file-label" id="file1">
                        <label for="imageAttachment">
                            <img src="../IMG/camera.png" alt="Image Button" onclick="displayFileName('image-file-info')">
                            <input type="file" id="imageAttachment" name="imageAttachment" onchange="displayFileName('image-file-info')" accept="image/*" style="display: none;">
                        </label>
                    </div>
                </div>
                <div id="image-file-info"></div>

                <script>
                    function displayFileName(infoId) {
                        const fileInput = event.target;
                        const fileInfo = document.getElementById(infoId);
                        const files = fileInput.files;

                        if (files.length > 0) {
                            fileInfo.textContent = `ไฟล์ที่เลือก: ${files[0].name}`;
                        } else {
                            fileInfo.textContent = "ไม่มีไฟล์ที่เลือก";
                        }
                    }

                    function cancelFile(inputId, infoId) {
                        const fileInput = document.getElementById(inputId);
                        const fileInfo = document.getElementById(infoId);

                        fileInput.value = null;
                        fileInfo.textContent = "ไฟล์ถูกยกเลิก";

                    }

                    $(document).ready(function() {
                        $('.js-example-basic-single').select2({
                            minimumInputLength: 0,
                            tags: false
                        });
                    });
                </script>


            </form>
        </div>
        <div class="btn-submit-edit">
            <input type="submit" value="ยืนยัน" class="btnConfirm">
        </div>
    </div>
    <?php include('../includes/footer.php'); ?>
</body>


</html>