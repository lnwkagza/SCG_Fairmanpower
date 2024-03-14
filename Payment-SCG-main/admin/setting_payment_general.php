<?php include('../admin/include/header.php') ?>


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
                                <h3>ตั้งค่าทั่วไป</h3>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">ตั้งค่าทั่วไป</li>
                                    <li class="breadcrumb-item"><a href="setting_payment_income_deduct.php">ตั้งค่ารายรับ/รายจ่ายอื่นๆ</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="card-box pd-20 pt-20">
                    <div class="col-md-10 col-sm-12">
                        <div class="form-group">
                            <div class="text-left">
                                <button class="btn setting-btn-pay" onclick="location.href='setting_payment_circle.php'">ตั้งค่ารอบเดือน <i class="fa-solid fa-arrows-spin"></i></a></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-12">
                        <div class="form-group">
                            <div class="text-left">
                                <button class="btn setting-btn-pay" onclick="location.href='setting_payment_split.php'">ตั้งค่าแบ่งงวดจ่าย <i class="fa-solid fa-table-columns"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-12">
                        <div class="form-group">
                            <div class="text-left">
                                <button class="btn setting-btn-pay" onclick="location.href='setting_payment_set_closing_date.php'">ตั้งค่าวันปิดงวด <i class="fa-solid fa-file-invoice-dollar"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-12">
                        <div class="form-group">
                            <div class="text-left">
                                <button class="btn setting-btn-pay" onclick="location.href='setting_payment_holidays.php'">ตั้งค่ารายได้พนักงานในวันหยุดนักขัตฤกษ์ <i class="fa-solid fa-money-check-dollar"></i></a></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-12">
                        <div class="form-group">
                            <div class="text-left">
                                <button class="btn setting-btn-pay" onclick="location.href='setting_payment_social_security.php'">ตั้งค่าประกันสังคม <i class="fa-solid fa-people-arrows"></i></a></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-12">
                        <div class="form-group">
                            <div class="text-left">
                                <button class="btn setting-btn-pay" onclick="location.href='setting_payment_form.php'">ตั้งค่าแบบฟอร์ม <i class="fa-solid fa-file-pen"></i></a></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-12">
                        <div class="form-group">
                            <div class="text-left">
                                <button class="btn setting-btn-pay" onclick="location.href='setting_payment_notification.php'">ตั้งค่าการแจ้งเตือน <i class="fa-solid fa-bell"></i></i></a></button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mt-5 col-md-3 col-sm-1">
                            <div class="form-group">
                                <label style="font-size:24px;"><b></b></label>
                                <div class="justify-content-left">
                                    <button style="font-size:20px;" onclick="location.href='setting_payment.php'" type="button" class="btn btn-default" data-dismiss="modal"><i class="fa-solid fa-circle-left"> </i> ย้อนกลับ</button>
                                    <!-- color:#AAAAAA -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <?php include('../admin/include/footer.php'); ?>
        </div>
    </div>
    <!-- js -->

    <?php include('../admin/include/scripts.php') ?>
</body>

</html>