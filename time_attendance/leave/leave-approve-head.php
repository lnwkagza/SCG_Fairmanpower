<?php
session_start();
session_regenerate_id(true);

//---------------------------------------------------------------------------------------
include ("../database/connectdb.php");
include ('../components-desktop/head/include/header.php')
    ?>

<!-- Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="node_modules\sweetalert2\dist\sweetalert2.css">

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/head/leave-approve-head.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/leave-approve.css">
<link rel=" stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="../assets/script/loader-normal.js"></script>

<!-- datatable -->
<link href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.bootstrap5.min.js"></script>

<?php

$sql_absence_confirm = sqlsrv_query($conn, "SELECT *
FROM absence_record 
RIGHT JOIN absence_type ON absence_record.absence_type_id = absence_type.absence_type_id
LEFT JOIN employee ON absence_record.card_id = employee.card_id
WHERE absence_record.approver = ? AND absence_record.approve_status = ?;
", array($_SESSION["card_id"], "confirm"));

$absence_confirm = array();

while ($row = sqlsrv_fetch_array($sql_absence_confirm, SQLSRV_FETCH_ASSOC)) {
    $absence_confirm[] = $row;
}

$sql_absence_confirm_inspector = sqlsrv_query($conn, "SELECT *
FROM absence_record 
RIGHT JOIN absence_type ON absence_record.absence_type_id = absence_type.absence_type_id
LEFT JOIN employee ON absence_record.card_id = employee.card_id
WHERE absence_record.inspector = ? AND absence_record.inspector_status = ?;
", array($_SESSION["card_id"], "confirm"));

$absence_confirm_inspector = array();

while ($row = sqlsrv_fetch_array($sql_absence_confirm_inspector, SQLSRV_FETCH_ASSOC)) {
    $absence_confirm_inspector[] = $row;
}
//-----------------------------------------------------------------------------------------------------------
$sql_absence_reject = sqlsrv_query($conn, "SELECT *
FROM  absence_record 
RIGHT JOIN absence_type ON absence_record.absence_type_id = absence_type.absence_type_id
LEFT JOIN employee ON absence_record.card_id = employee.card_id
WHERE absence_record.approver = ? AND absence_record.approve_status = ?;
", array($_SESSION["card_id"], "reject"));
$absence_reject = array();

while ($row = sqlsrv_fetch_array($sql_absence_reject, SQLSRV_FETCH_ASSOC)) {
    $absence_reject[] = $row;
}

$sql_absence_reject_inspector = sqlsrv_query($conn, "SELECT *
FROM  absence_record 
RIGHT JOIN absence_type ON absence_record.absence_type_id = absence_type.absence_type_id
LEFT JOIN employee ON absence_record.card_id = employee.card_id
WHERE absence_record.inspector = ? AND absence_record.inspector_status = ?;
", array($_SESSION["card_id"], "reject"));

$absence_reject_inspector = array();

while ($row = sqlsrv_fetch_array($sql_absence_reject_inspector, SQLSRV_FETCH_ASSOC)) {
    $absence_reject_inspector[] = $row;
}

// Assuming $conn is your SQL Server connection

//-----------------------------------------------------------------------------------------------------------
$sql_absence_waiting = sqlsrv_query(
    $conn,
    "SELECT *
    FROM absence_record
    RIGHT JOIN absence_type ON absence_record.absence_type_id = absence_type.absence_type_id
    LEFT JOIN employee ON absence_record.card_id = employee.card_id
    WHERE absence_record.approver = ? AND
    absence_record.approve_status = ?",
    array($_SESSION["card_id"], "waiting")
);

$absence_waiting = array();

while ($row = sqlsrv_fetch_array($sql_absence_waiting, SQLSRV_FETCH_ASSOC)) {
    $absence_waiting[] = $row;
}

// Second SQL Query
$sql_absence_waiting_inspector = sqlsrv_query(
    $conn,
    "SELECT *
    FROM absence_record 
    RIGHT JOIN absence_type ON absence_record.absence_type_id = absence_type.absence_type_id
    LEFT JOIN employee ON absence_record.card_id = employee.card_id
    WHERE absence_record.inspector = ? AND absence_record.approve_status = ?",
    array($_SESSION["card_id"], "waiting")
);

$absence_waiting_inspector = array();

while ($row = sqlsrv_fetch_array($sql_absence_waiting_inspector, SQLSRV_FETCH_ASSOC)) {
    $absence_waiting_inspector[] = $row;
}


// URLs for Approve and Reject Actions
$approve_inspector_url = "process_Leave_approve_inspector.php";
$reject_inspector_url = "process_Leave_reject_inspector.php";
$approve_url = "process_Leave_approve.php";
$reject_url = "process_Leave_reject.php";

//-----------------------------------------------------------------------------------------------------------

?>

<!-- --สลับตาราง--- -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
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

        btnList.addEventListener('click', function (event) {
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
    $(document).ready(function () {
        //data-table
        new DataTable('#mobile-table1', {
            "autoWidth": false,
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
            "autoWidth": false,
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
            "autoWidth": false,
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
    $(document).ready(function () {
        //data-table
        new DataTable('#table1', {
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
        new DataTable('#table2', {
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
        new DataTable('#table3', {
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

    document.addEventListener('DOMContentLoaded', function () {
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

        btnList.addEventListener('click', function (event) {
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
    <div class="desktop">
        <?php include ('../components-desktop/head/include/sidebar.php'); ?>
        <?php include ('../components-desktop/head/include/navbar.php'); ?>

        <div class=" main-container">
            <div class="pd-ltr-20 xs-pd-20-10">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <h2>คำขอการลาพนักงาน</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor:default;">การลา</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            คำขอการลาพนักงาน
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
                                                <th>รหัสพนักงาน</th>
                                                <th>ผู้ยื่นอนุมัติ</th>
                                                <th>วันที่</th>
                                                <th>ประเภทการลา</th>
                                                <th>สถานะ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php

                                            // Display rows for the first query
                                            foreach ($absence_confirm as $row) {
                                                // Check conditions before displaying rows
                                                if ($row['inspector'] == "" && $row['inspector_status'] == "") {
                                                    displayRows_confirm($row);
                                                }
                                            }

                                            // Display rows for the second query
                                            foreach ($absence_confirm_inspector as $row) {
                                                // Check conditions before displaying rows
                                                if ($row['inspector'] != "" && $row['inspector_status'] == "confirm") {
                                                    displayRows_confirm($row);
                                                }
                                            }

                                            function displayRows_confirm($row)
                                            {
                                                echo "<tr>";
                                                echo "<td>{$row['scg_employee_id']}</td>";
                                                echo "<td>{$row['firstname_thai']} {$row['lastname_thai']}</td>";
                                                echo "<td>{$row['date_start']->format('d-m-Y')}</td>";
                                                echo "<td>{$row['name']}</td>";
                                                echo "<td style='color: green !important;font-weight:600'>อนุมัติ</td>";
                                                echo "</tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="desktop-table-container table2">
                                    <table id="table2" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>รหัสพนักงาน</th>
                                                <th>ผู้ยื่นอนุมัติ</th>
                                                <th>วันที่</th>
                                                <th>ประเภทการลา</th>
                                                <th>สถานะ</th>
                                                <th>ดำเนินการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php

                                            // Display rows for the first query
                                            foreach ($absence_waiting as $row) {
                                                // Check conditions before displaying rows
                                                if ($row['inspector'] == "" && $row['inspector_status'] == "") {
                                                    displayRows($row, $approve_url, $reject_url);
                                                }
                                            }

                                            // Display rows for the second query
                                            foreach ($absence_waiting_inspector as $row) {
                                                // Check conditions before displaying rows
                                                if ($row['inspector'] != "" && $row['inspector_status'] == "waiting") {
                                                    displayRows($row, $approve_inspector_url, $reject_inspector_url);
                                                } elseif ($row['inspector'] != "" && $row['inspector_status'] == "confirm") {
                                                    displayRows($row, $approve_url, $reject_url);
                                                } elseif ($row['inspector'] == "" && $row['inspector_status'] == "") {
                                                    displayRows($row, $approve_url, $reject_url);
                                                }
                                            }

                                            function displayRows($row, $approve_url, $reject_url)
                                            {
                                                echo "<tr>";
                                                echo "<td>{$row['scg_employee_id']}</td>";
                                                echo "<td>{$row['firstname_thai']} {$row['lastname_thai']}</td>";
                                                echo "<td>{$row['date_start']->format('d-m-Y')}</td>";
                                                echo "<td>{$row['name']}</td>";
                                                echo "<td style='color: orange !important;font-weight:600;'>รออนุมัติ</td>";
                                                echo "<td class='action'>
                                                <a class='btn-info' href='leave-detail-head.php?id={$row['absence_record_id']}'>รายละเอียด</a>
                                            </td>";
                                                echo "</tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="desktop-table-container table3">
                                    <table id="table3" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>รหัสพนักงาน</th>
                                                <th>ผู้ยื่นอนุมัติ</th>
                                                <th>วันที่</th>
                                                <th>ประเภทการลา</th>
                                                <th>สถานะ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php

                                            // Display rows for the first query
                                            foreach ($absence_reject as $row) {
                                                // Check conditions before displaying rows
                                                if ($row['inspector'] == "" && $row['inspector_status'] == "") {
                                                    displayRows_reject($row);
                                                }
                                            }

                                            // Display rows for the second query
                                            foreach ($absence_reject_inspector as $row) {
                                                // Check conditions before displaying rows
                                                if ($row['inspector'] != "" && $row['inspector_status'] == "confirm") {
                                                    displayRows_reject($row);
                                                }
                                            }

                                            function displayRows_reject($row)
                                            {
                                                echo "<tr>";
                                                echo "<td>{$row['scg_employee_id']}</td>";
                                                echo "<td>{$row['firstname_thai']} {$row['lastname_thai']}</td>";
                                                echo "<td>{$row['date_start']->format('d-m-Y')}</td>";
                                                echo "<td>{$row['name']}</td>";
                                                echo "<td style='color: red !important;font-weight:600'>ไม่อนุมัติ</td>";
                                                echo "</tr>";
                                            }
                                            ?>
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

    <div class="mobile">
        <div class="navbar">
            <div class="div-span">
                <span>คำขอลาพนักงาน</span>
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
                        <!-- <th>รหัส</th> -->
                        <th>ชื่อ-สกุล</th>
                        <th>วันที่</th>
                        <th>ประเภท</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    // Display rows for the first query
                    foreach ($absence_confirm as $row) {
                        // Check conditions before displaying rows
                        if ($row['inspector'] == "" && $row['inspector_status'] == "") {
                            displayRows_confirm1($row);
                        }
                    }

                    // Display rows for the second query
                    foreach ($absence_confirm_inspector as $row) {
                        // Check conditions before displaying rows
                        if ($row['inspector'] != "" && $row['inspector_status'] == "confirm") {
                            displayRows_confirm1($row);
                        }
                    }

                    function displayRows_confirm1($row)
                    {
                        echo "<tr>";
                        // echo "<td>{$row['scg_employee_id']}</td>";
                        echo "<td>{$row['firstname_thai']} {$row['lastname_thai']}</td>";
                        echo "<td>{$row['date_start']->format('d-m-Y')}</td>";
                        echo "<td>{$row['name']}</td>";
                        echo "<td style='color: green !important;font-weight:600;'>อนุมัติ</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="table-container table2">
            <table id="mobile-table2" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <!-- <th>รหัส</th> -->
                        <th>ชื่อ-สกุล</th>
                        <th>วันที่</th>
                        <th>ประเภท</th>
                        <th>สถานะ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    // Display rows for the first query
                    foreach ($absence_waiting as $row) {
                        // Check conditions before displaying rows
                        if ($row['inspector'] == "" && $row['inspector_status'] == "") {
                            displayRows1($row, $approve_url, $reject_url);
                        }
                    }

                    // Display rows for the second query
                    foreach ($absence_waiting_inspector as $row) {
                        // Check conditions before displaying rows
                        if ($row['inspector'] != "" && $row['inspector_status'] == "waiting") {
                            displayRows1($row, $approve_inspector_url, $reject_inspector_url);
                        } elseif ($row['inspector'] != "" && $row['inspector_status'] == "confirm") {
                            displayRows1($row, $approve_url, $reject_url);
                        } elseif ($row['inspector'] == "" && $row['inspector_status'] == "") {
                            displayRows1($row, $approve_url, $reject_url);
                        }
                    }

                    function displayRows1($row, $approve_url, $reject_url)
                    {
                        echo "<tr>";
                        // echo "<td>{$row['scg_employee_id']}</td>";
                        echo "<td>{$row['firstname_thai']} {$row['lastname_thai']}</td>";
                        echo "<td>{$row['date_start']->format('d-m-Y')}</td>";
                        echo "<td>{$row['name']}</td>";
                        echo "<td style='color: orange !important;font-weight:600;'>รออนุมัติ</td>";
                        echo "<td class='btn-detail'>
                                <button onclick='window.location.href=\"leave-detail-head.php?id=" . $row['absence_record_id'] . "\"'>
                                    <img src='../IMG/approve1.png' alt=''>
                                </button>
                             </td>";

                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="table-container table3">
            <table id="mobile-table3" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <!-- <th>รหัส</th> -->
                        <th>ชื่อ-สกุล</th>
                        <th>วันที่</th>
                        <th>ประเภท</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Display rows for the first query
                    foreach ($absence_reject as $row) {
                        // Check conditions before displaying rows
                        if ($row['inspector'] == "" && $row['inspector_status'] == "") {
                            displayRows_reject1($row);
                        }
                    }

                    // Display rows for the second query
                    foreach ($absence_reject_inspector as $row) {
                        // Check conditions before displaying rows
                        if ($row['inspector'] != "" && $row['inspector_status'] == "confirm") {
                            displayRows_reject1($row);
                        }
                    }

                    function displayRows_reject1($row)
                    {
                        echo "<tr>";
                        // echo "<td>{$row['scg_employee_id']}</td>";
                        echo "<td>{$row['firstname_thai']} {$row['lastname_thai']}</td>";
                        echo "<td>{$row['date_start']->format('d-m-Y')}</td>";
                        echo "<td>{$row['name']}</td>";
                        echo "<td style='color: red !important;font-weight:600;'>ไม่อนุมัติ</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
<?php include ('../includes/footer.php'); ?>