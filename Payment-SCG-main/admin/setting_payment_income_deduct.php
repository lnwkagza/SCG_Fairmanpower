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
                                <ol class="breadcrumb">
           
                                    <li class="breadcrumb-item"><a href="setting_payment_general.php">ตั้งค่าทั่วไป</a></li>
                                    <li class="breadcrumb-item active" aria-current="page.php">ตั้งค่ารายรับ/รายจ่ายอื่นๆ</li>                                  
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="card-box pd-20 pt-20 height-50-p">
                <div class="col-md-10 col-sm-12">
                    <div class="form-group">
                        <div class="text-left">
                            <button class="btn setting-btn-pay"  onclick="location.href='setting_payment_income.php'">ตั้งค่ารายรับ</a></button>
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
            <?php include('../admin/include/footer.php'); ?>
        
    </div>
    <!-- js -->

    <?php include('../admin/include/scripts.php') ?>
</body>

</html>