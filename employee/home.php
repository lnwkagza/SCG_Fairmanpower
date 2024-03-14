<?php
require_once "../config/connection.php";
?>

<?php include('../employee/include/header.php') ?>

<body>

    <?php include('../employee/include/navbar.php') ?>
    <?php include('../employee/include/sidebar.php') ?>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="title pb-20">
                <h2 class="h3 mb-0">Darshboard</h2>
            </div>
            <div class="card-box pd-20 height-50-p mb-30">
                <h4 class="font-20 weight-500 mb-10 text-capitalize">
                    SCG : Fair Manpower ยินดีให้บริการ <h4 class="weight-600 font-15 text-primary">คุณ <?php echo $fname . ' ' . $lname ?></h4>
                </h4>
                <p class="font-18 max-width-1000">* หมายเหตุ ทางผู้พัฒนาได้ปรับปรุงส่วน <a href="listemployee_Edit.php"> ข้อมูลพนักงาน </a>ณ วันที่ 5 - 14 กุมภาพันธ์ 2567
                <p class="font-18 max-width-800 text-danger"> จึง สุขสันปีใหม่ 2567 มา ณ ที่นี้</p>
                </p>
            </div>
        </div>


        <?php include('../admin/include/footer.php'); ?>
    </div>
    </div>

    <!-- js -->
    <?php include('../employee/include/scripts.php') ?>

</body>

</html>