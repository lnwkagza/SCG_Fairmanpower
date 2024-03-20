<?php
session_start();
include('../components-desktop/employee/include/header.php');
include("../database/connectdb.php");
?>
<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/head/ot-head.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/ot.css">
<?php

// ทำความสะอาดและป้องกันข้อมูล
$_SESSION['card_id'] = isset($_SESSION['card_id']) ? $_SESSION['card_id'] : '';  // ตรวจสอบว่ามีค่าหรือไม่
$_SESSION['card_id'] = trim($_SESSION['card_id']);  // ลบช่องว่างที่อาจเกิดขึ้นทั้งด้านหน้าและด้านหลังขอข้อมูล
$_SESSION['card_id'] = preg_replace('/[^a-zA-Z0-9]/', '', $_SESSION['card_id']);  // ลบทุกอักขระที่ไม่ใช่ a-z, A-Z, และ 0-9
$_SESSION['card_id'] = htmlspecialchars($_SESSION['card_id'], ENT_QUOTES, 'UTF-8');  // แปลงสัญลักษณ์ HTML เพื่อป้องกัน XSS

if (isset($_SESSION["card_id"])) {
    $select_shift_team = "SELECT line_id FROM login WHERE card_id = ?";
    $dayoff_team_stmt = sqlsrv_prepare($conn, $select_shift_team, array($_SESSION["card_id"]));
    if (sqlsrv_execute($dayoff_team_stmt)) {
        $row = sqlsrv_fetch_array($dayoff_team_stmt, SQLSRV_FETCH_ASSOC);
        $line_id = $row['line_id']; // Added a semicolon here
    } else {
        echo "Error executing query: " . print_r(sqlsrv_errors(), true);
    }
} else {
    echo "No card_id in session.";
}
?>
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
                                    <h2>OT</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor:default;">OT</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            OT
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p" id="box1">

                                <div class="row">
                                    <div class="display-menu">
                                        <div>
                                            <button type="button" onclick="window.location.href='https://scgot.online/screen/manager/infor.php?w1=<?= $line_id ?>'">
                                                <div><img src="../IMG/request-ot.png" alt=""></div>
                                                <div><span>อนุมัติ OT</span></div>
                                            </button>
                                        </div>
                                        <div>
                                            <button type="button" onclick="window.location.href='https://scgot.online/screen/manager/approveReport.php?w1=<?= $line_id ?>'">
                                                <div><img src="../IMG/check-ot.png" alt=""></div>
                                                <div><span>รายการที่อนุมัติแล้ว</span></div>
                                            </button>
                                        </div>
                                        <div>
                                            <button type="button" onclick="window.location.href='https://scgot.online/screen/manager/summarize.php?w1=<?= $line_id ?>'">
                                                <div><img src="../IMG/sum-ot.png" alt=""></div>
                                                <div><span>สรุปข้อมูล OT</span></div>
                                            </button>
                                        </div>
                                    </div>
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
                <span>โอที</span>
            </div>
        </div>

        <div class="container">
            <div class="btn-top-head">
                <div class="btn-1">
                    <div class="display-icon">
                        <button onclick="window.location.href='https://scgot.online/screen/manager/infor.php?w1=<?= $line_id ?>'"><img src="../IMG/reques1.png" alt=""></button>
                    </div>
                    <span>อนุมัติ OT</span>
                </div>
                <div class="btn-4">
                    <div class="display-icon">
                        <button onclick="window.location.href='https://scgot.online/screen/manager/approveReport.php?w1=<?= $line_id ?>'"><img src="../IMG/check.png" alt=""></button>
                    </div>
                    <span>รายการที่อนุมัติแล้ว</span>
                </div>
                <div class="btn-4">
                    <div class="display-icon">
                        <button onclick="window.location.href='https://scgot.online/screen/manager/summarize.php?w1=<?= $line_id ?>'"><img src="../IMG/sum.png" alt=""></button>
                    </div>
                    <span>สรุปข้อมูล OT</span>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include('../includes/footer.php') ?>