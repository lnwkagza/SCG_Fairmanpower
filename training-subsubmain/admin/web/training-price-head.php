<?php
session_start();
require_once '../../connect/connect.php';
if(!isset($_SESSION['user_login']) && !isset($_SESSION['admin_login'])){
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location:  ../../../../linelogin/index.html');
    exit();
}

// ถ้าผู้ใช้กด Logout
if (isset($_GET['logout']) && isset($_SESSION['user_login'])) {
    // ลบ session และส่งผู้ใช้กลับไปยังหน้าล็อกอิน
    unset($_SESSION['user_login']);
    $_SESSION['error'] = 'คุณได้ออกจากระบบแล้ว';
    header('location:  ../../../../linelogin/index.html');
    exit();
}

?>

<?php include('../../components/head.php') ?>
<link rel="stylesheet" href="../../plugin/bootstrap-icons-1.11.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../plugin/boxicons-2.1.4/css/boxicons.min.css">
<link rel="stylesheet" href="../css/training-price-head.css">
<title>AdminPage</title>

<body>
    <?php include('../../components/navbar.php') ?>
    <?php include('../../components/sidebar.php') ?>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pl-10 pt-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <span id="head">การฝึกอบรม</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                        <div class="card-box pd-10 pt-10 height-100-p">
                            <div class="bar">
                                <div class="mainclass">
                                    <div id="add">เพิ่มข้อมูลการฝึกอบรม <label style="color: red;">*</label></div>
                                    <div class="data-t">
                                        <div class="cost-center">
                                            <label for="">Cost Center</label>
                                            <input type="text" autocomplete="off">
                                        </div>
                                        <div class="internal-order">
                                            <label for="">Internal Order</label>
                                            <input type="text" autocomplete="off">
                                        </div>
                                        <div class="cost-element">
                                            <label for="">Cost Element</label>
                                            <input type="text" autocomplete="off">
                                        </div>
                                    </div>
                                    <div id="add">งบประมาณ (แสดงผลรวมในหน้าเพิ่มข้อมูล)</div>
                                    <div class="data-b">
                                        <div class="row1">
                                            <input type="checkbox">
                                            <span id="name">ค่าหลักสูตร</span>
                                            <input type="number" placeholder="กรุณาระบุจำนวนเงิน" autocomplete="off">
                                            <span class="to">ต่อ</span>
                                            <input type="number" autocomplete="off">
                                            <span>คน</span>
                                            <input type="number" placeholder="กรุณาระบุจำนวนเงินรวม" autocomplete="off">
                                            <span>บาท</span>
                                        </div>
                                        <div class="row2">
                                            <input  type="checkbox">
                                            <span id="name">ค่าตั๋วเดินทาง</span>
                                            <input type="number" placeholder="กรุณาระบุจำนวนเงิน" autocomplete="off">
                                            <span class="to">ต่อ</span>
                                            <input type="number" autocomplete="off">
                                            <span>คน</span>
                                            <input type="number" placeholder="กรุณาระบุจำนวนเงินรวม" autocomplete="off">
                                            <span>บาท</span>
                                        </div>
                                        <div class="row3">
                                            <input  type="checkbox">
                                            <span id="name">ค่าเช่ารถ</span>
                                            <input type="number" placeholder="กรุณาระบุจำนวนเงิน" autocomplete="off">
                                            <span class="to">ต่อ</span>
                                            <input type="number" autocomplete="off">
                                            <span>คน</span>
                                            <input type="number" placeholder="กรุณาระบุจำนวนเงินรวม" autocomplete="off">
                                            <span>บาท</span>
                                        </div>
                                        <div class="row4">
                                            <input  type="checkbox">
                                            <span id="name">ค่าโรงแรม</span>
                                            <input type="number" placeholder="กรุณาระบุจำนวนเงิน" autocomplete="off">
                                            <span class="to">ต่อ</span>
                                            <input type="number" autocomplete="off">
                                            <span>คน</span>
                                            <input type="number" placeholder="กรุณาระบุจำนวนเงินรวม" autocomplete="off">
                                            <span>บาท</span>
                                        </div>
                                        <div class="row5">
                                            <input  type="checkbox">
                                            <span id="name">ค่าใช้จ่ายอื่นๆ</span>
                                            <input type="number" placeholder="กรุณาระบุจำนวนเงิน" autocomplete="off">
                                            <span class="to">ต่อ</span>
                                            <input type="number" autocomplete="off">
                                            <span>คน</span>
                                            <input type="number" placeholder="กรุณาระบุจำนวนเงินรวม" autocomplete="off">
                                            <span>บาท</span>
                                        </div>
                                    </div>
                                    <a class="save" href="training-add.php">บันทึก</a>

                                </div>
                            </div>
                        </div>

                    </div>


                </div>

            </div>
        </div>
    </div>

    <?php include('../../components/script.php') ?>
</body>

</html>