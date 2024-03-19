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
                                        <li class="breadcrumb-item"><a href="employee_payment.php">จัดการเงินเดือนพนักงาน</a></li>
                                        <li class="breadcrumb-item"><a href="income.php">รายรับ/รายจ่าย</a></li>
                                        <li class="breadcrumb-item"><a href="calculator_payment2.php">คำนวณเงินเดือน</a></li>
                                        <li class="breadcrumb-item"><a href="history_payment.php">ประวัติการคำนวณ</a></li>
                                        <li class="breadcrumb-item"><a href="setting_payment_income_deduct.php">ตั้งค่ารายรับรายจ่าย</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">ตั้งค่าทั่วไป</li>
                                    </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="card-box pd-20 pt-20">
                    <div class="col-md-10 col-sm-12">
                        <div class="form-group">
                            <div class="text-left">
                                <button class="btn setting-btn-pay" onclick="location.href='setting_payment_circle.php'"><i class="fa-solid fa-arrows-spin"></i> ตั้งค่างวดเดือน </a></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-12">
                        <div class="form-group">
                            <div class="text-left">
                                <button disabled class="btn setting-btn-pay" onclick="location.href='setting_payment_split.php'"><i class="fa-solid fa-table-columns"></i> ตั้งค่าแบ่งงวดจ่าย </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-12">
                        <div class="form-group">
                            <div class="text-left">
                                <button disabled class="btn setting-btn-pay" onclick="location.href='setting_payment_set_closing_date.php'"><i class="fa-solid fa-file-invoice-dollar"></i> ตั้งค่าวันปิดงวด </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-12">
                        <div class="form-group">
                            <div class="text-left">
                                <button disabled class="btn setting-btn-pay" onclick="location.href='setting_payment_holidays.php'"><i class="fa-solid fa-money-check-dollar"></i> ตั้งค่ารายได้พนักงานในวันหยุดนักขัตฤกษ์ </a></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-12">
                        <div class="form-group">
                            <div class="text-left">
                                <button disabled class="btn setting-btn-pay" onclick="location.href='setting_payment_social_security.php'"><i class="fa-solid fa-people-arrows"></i> ตั้งค่าประกันสังคม </a></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-12">
                        <div class="form-group">
                            <div class="text-left">
                                <button disabled class="btn setting-btn-pay" onclick="location.href='setting_payment_form.php'"><i class="fa-solid fa-file-pen"></i> ตั้งค่าแบบฟอร์ม </a></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-12">
                        <div class="form-group">
                            <div class="text-left">
                                <button disabled class="btn setting-btn-pay" onclick="location.href='setting_payment_notification.php'"><i class="fa-solid fa-bell"></i> ตั้งค่าการแจ้งเตือน </a></button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mt-5 col-md-3 col-sm-1">
                            <div class="form-group">
                                <label style="font-size:24px;"><b></b></label>
                                
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