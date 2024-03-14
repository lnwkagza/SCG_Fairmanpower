<?php include('../admin/include/header.php') ?>

<body>

    <?php include('../admin/include/navbar.php') ?>
    <?php include('../admin/include/sidebar.php') ?>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="title pb-20">
                <h2 class="h3 mb-0">Darshboard Data Analytics</h2>
            </div>
            <div class="card-box pd-20 height-50-p mb-30">
                <h4 class="font-20 weight-500 mb-10 text-capitalize">
                    SCG : Fair Manpower ยินดีให้บริการ <h4 class="weight-600 font-15 text-primary"></h4>
                </h4>
                <p class="font-18 max-width-1000">* หมายเหตุ ทางผู้พัฒนาได้ปรับปรุงส่วน <a href="listemployee_Manager.php"> ข้อมูลผู้จัดการ / report-to </a>ณ วันที่ <?php echo $date2->format("D, d M Y") ?>
                <p class="font-18 max-width-800 text-danger"> จึง สุขสันปีใหม่ 2567 มา ณ ที่นี้</p>
                </p>
                <button class="font-20 weight-500 mb-10 text-capitalize btn btn-secondary" onclick="location.href='analytics/dashboard.php'">
                    <i class="fa-solid fa-chart-line fa-lg"></i> Demo Analytics <h4 class="weight-600 font-15 text-primary"></h4>
                </button>
            </div>
        </div>
        <?php include('../admin/include/footer.php') ?>
    </div>

    <!-- js -->
    <?php include('../admin/include/scripts.php') ?>
    
</body>

</html>