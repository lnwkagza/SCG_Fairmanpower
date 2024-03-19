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
                                <h3>ตั้งค่ารายรับ/รายจ่ายอื่นๆ</h3>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="employee_payment.php">จัดการเงินเดือนพนักงาน</a></li>
                                        <li class="breadcrumb-item"><a href="income.php">รายรับ/รายจ่าย</a></li>
                                        <li class="breadcrumb-item"><a href="calculator_payment2.php">คำนวณเงินเดือน</a></li>
                                        <li class="breadcrumb-item"><a href="history_payment.php">ประวัติการคำนวณ</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">ตั้งค่ารายรับรายจ่าย</li>
                                        <li class="breadcrumb-item"><a href="setting_payment_general.php">ตั้งค่าทั่วไป</a></li>
                                    </ol>
                                </nav>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="card-box pd-20 pt-20 height-50-p" style="min-height: 500px;">
                    <div class="col-md-10 col-sm-12">
                        <div class="form-group">
                            <div class="text-left">
                                <button class="btn setting-btn-pay" onclick="location.href='setting_payment_income.php'">ตั้งค่ารายรับ</a></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-12">
                        <div class="form-group">
                            <div class="text-left">
                                <button class="btn setting-btn-pay" onclick="location.href='setting_payment_deduct.php'">ตั้งค่ารายจ่าย</a></button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include('../admin/include/footer.php'); ?>

            </div>
            <!-- js -->

            <?php include('../admin/include/scripts.php') ?>
</body>

</html>