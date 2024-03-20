<?php
session_start();
session_regenerate_id(true);

//---------------------------------------------------------------------------------------
include("../database/connectdb.php");
include('../components-desktop/employee/include/header.php');
?>

<!-- Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="node_modules\sweetalert2\dist\sweetalert2.css">

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/employee/leave-history-employee.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/leave-history.css">
<link rel=" stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="../assets/script/loader-normal.js"></script>

<!-- datatable -->
<link href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.bootstrap5.min.js"></script>


<?php
//---------------------------------------------------------------------------------------

// ทำความสะอาดและป้องกันข้อมูล
$_SESSION['card_id'] = isset($_SESSION['card_id']) ? $_SESSION['card_id'] : '';  // ตรวจสอบว่ามีค่าหรือไม่
$_SESSION['card_id'] = trim($_SESSION['card_id']);  // ลบช่องว่างที่อาจเกิดขึ้นทั้งด้านหน้าและด้านหลังของข้อมูล
$_SESSION['card_id'] = preg_replace('/[^a-zA-Z0-9]/', '', $_SESSION['card_id']);  // ลบทุกอักขระที่ไม่ใช่ a-z, A-Z, และ 0-9
$_SESSION['card_id'] = htmlspecialchars($_SESSION['card_id'], ENT_QUOTES, 'UTF-8');  // แปลงสัญลักษณ์ HTML เพื่อป้องกัน XSS

//---------------------------------------------------------------------------------------

if (!empty($_SESSION["card_id"])) {


    // สร้าง query เพื่อดึงข้อมูลพนักงาน
    $query = "SELECT * FROM employee WHERE card_id = ?";
    $params = array($_SESSION["card_id"]);
    $sql_card_id = sqlsrv_query($conn, $query, $params);
    if (sqlsrv_has_rows($sql_card_id)) {
        //---------------------------------------------------------------------------------------
        $sql_absence_confirm = sqlsrv_query($conn, "SELECT * FROM absence_record INNER JOIN absence_type ON absence_record.absence_type_id = absence_type.absence_type_id 
        WHERE card_id = ? AND approve_status = ?", array($_SESSION["card_id"], "confirm"));

        $absence_confirm = array();
        while ($row = sqlsrv_fetch_array($sql_absence_confirm, SQLSRV_FETCH_ASSOC)) {
            $absence_confirm[] = $row;
        }
        
        //---------------------------------------------------------------------------------------
        $sql_absence_waiting = sqlsrv_query($conn, "SELECT * FROM absence_record INNER JOIN absence_type ON absence_record.absence_type_id = absence_type.absence_type_id 
        WHERE card_id = ? AND approve_status = ?", array($_SESSION["card_id"], "waiting"));

        $absence_waiting = array();
        while ($row = sqlsrv_fetch_array($sql_absence_waiting, SQLSRV_FETCH_ASSOC)) {
            $absence_waiting[] = $row;
        }
        
        //---------------------------------------------------------------------------------------
        $sql_absence_reject = sqlsrv_query($conn, "SELECT * FROM absence_record INNER JOIN absence_type ON absence_record.absence_type_id = absence_type.absence_type_id 
        WHERE card_id = ? AND approve_status = ?", array($_SESSION["card_id"], "reject"));

        $absence_reject = array();
        while ($row = sqlsrv_fetch_array($sql_absence_reject, SQLSRV_FETCH_ASSOC)) {
            $absence_reject[] = $row;
        }
    } else {
        // แจ้งเตือนถ้ายังไม่ได้ลงทะเบียน
        echo '<script>
        alert("คุณยังไม่ได้ลงทะเบียน");
        window.location.href = "../index.html";
        </script>';
    }
} else {
    // แจ้งเตือนถ้ายังไม่ได้ลงทะเบียน
    echo '<script>
    alert("คุณยังไม่ได้ลงทะเบียน");
    window.location.href = "../index.html";
     </script>';
}

?>

<!-- --สลับตาราง--- -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnList = document.querySelector('.buttonStatus');
    const tables = document.querySelectorAll('.table-container');

    // Set initial state
    tables.forEach((table, index) => {
        if (index === 1) {
            table.style.display = 'block';
            btnList.children[index].classList.add('active');
        } else {
            table.style.display = 'none';
            btnList.children[index].classList.remove('active');
        }
    });

    btnList.addEventListener('click', function(event) {
        if (event.target.tagName === 'BUTTON') {
            const buttonIndex = Array.from(btnList.children).indexOf(event.target);

            // Hide all tables
            tables.forEach(table => table.style.display = 'none');

            // Show the selected table
            tables[buttonIndex].style.display = '';

            // Remove 'active' class from all buttons
            btnList.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));

            // Add 'active' class to the clicked button
            event.target.classList.add('active');
        }
    });
});
</script>



<!-- --data table-- -->
<!-- --เปลี่ยนภาษาอังกฤษของ data table-- -->
<script>
$(document).ready(function() {
    //data-table
    new DataTable('#mobile-table1', {
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "ทั้งหมด"]
        ],
        "language": {
            "search": "ค้นหา:",
            "lengthMenu": "แสดง _MENU_ รายการ",
            "info": "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            "paginate": {
                "first": "หน้าแรก",
                "last": "หน้าสุดท้าย",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            },
            "infoEmpty": "แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ",
            "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)"
        }
    });
    new DataTable('#mobile-table2', {
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "ทั้งหมด"]
        ],
        "language": {
            "search": "ค้นหา:",
            "lengthMenu": "แสดง _MENU_ รายการ",
            "info": "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            "paginate": {
                "first": "หน้าแรก",
                "last": "หน้าสุดท้าย",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            },
            "infoEmpty": "แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ",
            "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)"
        }
    });
    new DataTable('#mobile-table3', {
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "ทั้งหมด"]
        ],
        "language": {
            "search": "ค้นหา:",
            "lengthMenu": "แสดง _MENU_ รายการ",
            "info": "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            "paginate": {
                "first": "หน้าแรก",
                "last": "หน้าสุดท้าย",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            },
            "infoEmpty": "แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ",
            "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)"
        }
    });
});
</script>

<!-- script for desktop -->
<script>
$(document).ready(function() {
    //data-table
    new DataTable('#table1', {
        "autoWidth": false,
        "lengthMenu": [
            [10, 20, 50, -1],
            [10, 20, 50, "ทั้งหมด"]
        ],
        "language": {
            "search": "ค้นหา:",
            "lengthMenu": "แสดง _MENU_ รายการ",
            "info": "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            "paginate": {
                "first": "หน้าแรก",
                "last": "หน้าสุดท้าย",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            },
            "infoEmpty": "แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ",
            "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)"
        }
    });
});

$(document).ready(function() {
    //data-table
    new DataTable('#table2', {
        "autoWidth": false,
        "lengthMenu": [
            [10, 20, 50, -1],
            [10, 20, 50, "ทั้งหมด"]
        ],
        "language": {
            "search": "ค้นหา:",
            "lengthMenu": "แสดง _MENU_ รายการ",
            "info": "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            "paginate": {
                "first": "หน้าแรก",
                "last": "หน้าสุดท้าย",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            },
            "infoEmpty": "แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ",
            "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)"
        }
    });
});

$(document).ready(function() {
    //data-table
    new DataTable('#table3', {
        "autoWidth": false,
        "lengthMenu": [
            [10, 20, 50, -1],
            [10, 20, 50, "ทั้งหมด"]
        ],
        "language": {
            "search": "ค้นหา:",
            "lengthMenu": "แสดง _MENU_ รายการ",
            "info": "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            "paginate": {
                "first": "หน้าแรก",
                "last": "หน้าสุดท้าย",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            },
            "infoEmpty": "แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ",
            "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)"
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const btnList = document.querySelector('.button-bar');
    const tables = document.querySelectorAll('.desktop-table-container ');

    // Set initial state
    tables.forEach((table, index) => {
        if (index === 1) {
            table.style.display = 'block';
            btnList.children[index].classList.add('active');
        } else {
            table.style.display = 'none';
            btnList.children[index].classList.remove('active');
        }
    });

    btnList.addEventListener('click', function(event) {
        if (event.target.tagName === 'BUTTON') {
            const buttonIndex = Array.from(btnList.children).indexOf(event.target);

            // Hide all tables
            tables.forEach(table => table.style.display = 'none');

            // Show the selected table
            tables[buttonIndex].style.display = '';

            // Remove 'active' class from all buttons
            btnList.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));

            // Add 'active' class to the clicked button
            event.target.classList.add('active');
        }
    });
});
</script>

</head>

<body>
    <div id="loader"></div>
    <div style="display:none;" id="Content" class="animate-bottom">

        <div class="desktop">
            <?php include('../components-desktop/employee/include/sidebar.php'); ?>
            <?php include('../components-desktop/employee/include/navbar.php'); ?>

            <div class="main-container">
                <div class="pd-ltr-20 xs-pd-20-10">
                    <div class="min-height-200px">
                        <div class="page-header">
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="title">
                                        <h2>ประวัติการลา : Leave History</h2>
                                    </div>
                                    <nav aria-label="breadcrumb" role="navigation">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item">
                                                <a style="pointer-events:none;cursor:default;">การลา</a>
                                            </li>
                                            <li class="breadcrumb-item active" aria-current="page"
                                                style="cursor:default;">
                                                ประวัติการลา
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                                <div class="card-box pd-30 pt-10 height-100-p">
                                    <div class="button-bar bar">
                                        <button class="desktop-btn" id="btn1">
                                            อนุมัติแล้ว
                                        </button>
                                        <button class="desktop-btn" id="btn2">
                                            รออนุมัติ
                                        </button>
                                        <button class="desktop-btn" id="btn3">
                                            ปฏิเสธ
                                        </button>
                                    </div>

                                    <div class="desktop-table-container table1">
                                        <table id="table1" class="table table-striped" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>วันที่ขอ</th>
                                                    <th>วันที่ลา</th>
                                                    <th>ประเภทการลา</th>
                                                    <th>สถานะ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($absence_confirm as $row) { ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $row['input_timestamp']->format('d-m-Y') ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row['date_start']->format('d-m-Y') . " ถึง " . $row['date_end']->format('d-m-Y') ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row['name'] ?>
                                                    </td>
                                                    <td style="color: green !important;font-weight: 600;">อนุมัติแล้ว
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="desktop-table-container table2">
                                        <table id="table2" class="table table-striped" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>วันที่ขอ</th>
                                                    <th>วันที่ลา</th>
                                                    <th>ประเภทการลา</th>
                                                    <th>สถานะ</th>
                                                    <th>แก้ไข</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($absence_waiting as $row) { ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $row['input_timestamp']->format('d-m-Y') ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row['date_start']->format('d-m-Y') . " ถึง " . $row['date_end']->format('d-m-Y') ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row['name'] ?>
                                                    </td>
                                                    <td style="color: orange !important;font-weight: 600;">รออนุมัติ
                                                    </td>
                                                    <td><a href="leave-edit-employee.php?id='<?php echo $row['absence_record_id'] ?>'"
                                                            title="แก้ไข"><img src=" ../IMG/edit.png"
                                                                style="width: 30px;" alt="แก้ไข"></a></td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="desktop-table-container table3">
                                        <table id="table3" class="table table-striped" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>วันที่ขอ</th>
                                                    <th>วันที่ลา</th>
                                                    <th>ประเภทการลา</th>
                                                    <th>สถานะ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($absence_reject as $row) { ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $row['input_timestamp']->format('d-m-Y') ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row['date_start']->format('d-m-Y') . " ถึง " . $row['date_end']->format('d-m-Y') ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row['name'] ?>
                                                    </td>
                                                    <td style="color: red !important;font-weight: 600;">ปฏิเสธ</td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class=" mobile">
            <div class="navbar">
                <div class="div-span">
                    <span>ประวัติการลา</span>
                </div>
            </div>

            <div class="buttonStatus">
                <button class="buttonl" id="btn1">
                    อนุมัติแล้ว
                </button>
                <button class="buttonl active" id="btn1">
                    รออนุมัติ
                </button>
                <button class="buttonl" id="btn3">
                    ปฏิเสธ
                </button>
            </div>

            <div class="table-container table1">
                <table id="mobile-table1" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>วันที่ขอ</th>
                            <th>วันที่ลา</th>
                            <th>ประเภท</th>
                            <th>สถานะ</th>
                            <th>แก้ไข</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($absence_confirm as $row2) { ?>
                        <tr>
                            <td><?= $row2['input_timestamp']->format('d-m-Y') ?></td>
                            <td><?= $row2['date_start']->format('d-m-Y') . " <br>ถึง " . $row2['date_end']->format('d-m-Y') ?>
                            </td>
                            <td><?= $row2['name'] ?></td>
                            <td style="color:green;">อนุมัติ</td>
                            <td style="text-align:center !important"><a
                                    href="leave-edit-employee.php?id='<?php echo $row2['absence_record_id'] ?>'"><img
                                        src="../IMG/edit.png" style="width: 5vw;"></a></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="table-container table2">
                <table id="mobile-table2" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>วันที่ขอ</th>
                            <th>วันที่ลา</th>
                            <th>ประเภท</th>
                            <th>สถานะ</th>
                            <th>แก้ไข</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($absence_waiting as $row2) { ?>
                        <tr>
                            <td><?= $row2['input_timestamp']->format('d-m-Y') ?></td>
                            <td><?= $row2['date_start']->format('d-m-Y') . " <br>ถึง " . $row2['date_end']->format('d-m-Y') ?>
                            </td>
                            <td><?= $row2['name'] ?></td>
                            <td style="color:orange;">รออนุมัติ</td>
                            <td style="text-align:center !important"><a
                                    href="leave-edit-employee.php?id='<?php echo $row2['absence_record_id'] ?>'"><img
                                        src="../IMG/edit.png" style="width: 5vw;"></a></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="table-container table3">
                <table id="mobile-table3" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>วันที่ขอ</th>
                            <th>วันที่ลา</th>
                            <th>ประเภท</th>
                            <th>สถานะ</th>
                            <th>แก้ไข</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($absence_reject as $row2) { ?>
                        <tr>
                            <td><?= $row2['input_timestamp']->format('d-m-Y') ?></td>
                            <td><?= $row2['date_start']->format('d-m-Y') . " <br>ถึง " . $row2['date_end']->format('d-m-Y') ?>
                            </td>
                            <td><?= $row2['name'] ?></td>
                            <td style="color:red;">ปฏิเสธ</td>
                            <td style="text-align:center !important"><a
                                    href="leave-edit-employee.php?id='<?php echo $row2['absence_record_id'] ?>'"><img
                                        src="../IMG/edit.png" style="width: 5vw;"></a></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
</body>
<?php include('../includes/footer.php'); ?>