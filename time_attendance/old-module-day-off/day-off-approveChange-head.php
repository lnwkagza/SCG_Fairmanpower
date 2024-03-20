<?php
include('header.php');
include("../database/connectdb.php");

//---------------------------------------------------------------------------------------

session_start();
session_regenerate_id(true);

//---------------------------------------------------------------------------------------

// ทำความสะอาดและป้องกันข้อมูล
$_SESSION['card_id'] = isset($_SESSION['card_id']) ? $_SESSION['card_id'] : '';  // ตรวจสอบว่ามีค่าหรือไม่
$_SESSION['card_id'] = trim($_SESSION['card_id']);  // ลบช่องว่างที่อาจเกิดขึ้นทั้งด้านหน้าและด้านหลังของข้อมูล
$_SESSION['card_id'] = preg_replace('/[^a-zA-Z0-9]/', '', $_SESSION['card_id']);  // ลบทุกอักขระที่ไม่ใช่ a-z, A-Z, และ 0-9
$_SESSION['card_id'] = htmlspecialchars($_SESSION['card_id'], ENT_QUOTES, 'UTF-8');  // แปลงสัญลักษณ์ HTML เพื่อป้องกัน XSS

//---------------------------------------------------------------------------------------

if (!empty($_SESSION["card_id"])) {
    // สร้าง query เพื่อดึงข้อมูลพนักงาน
    $query = "SELECT * FROM employee INNER JOIN work_format ON employee.work_format_code = work_format.work_format_code WHERE card_id = ?";
    $params = array($_SESSION["card_id"]);
    $sql_card_id = sqlsrv_query($conn, $query, $params);
    $rowg = sqlsrv_fetch_array($sql_card_id);
    if (sqlsrv_has_rows($sql_card_id)) {
        $sql_day_off_request_confirm = sqlsrv_query($conn, "SELECT *
            FROM manager
            INNER JOIN day_off_request ON manager.card_id = day_off_request.card_id
            INNER JOIN work_format ON day_off_request.edit_work_format_code = work_format.work_format_code
            LEFT JOIN employee ON day_off_request.card_id = employee.card_id
            WHERE manager.manager_card_id = ? AND day_off_request.approve_status = ?;
            ", array($_SESSION["card_id"], "confirm"));

        $sql_day_off_request_waiting = sqlsrv_query($conn, "SELECT *
            FROM manager
            INNER JOIN day_off_request ON manager.card_id = day_off_request.card_id
            INNER JOIN work_format ON day_off_request.edit_work_format_code = work_format.work_format_code
            LEFT JOIN employee ON day_off_request.card_id = employee.card_id
            WHERE manager.manager_card_id = ? AND day_off_request.approve_status = ?;
            ", array($_SESSION["card_id"], "waiting"));

        $sql_day_off_request_reject = sqlsrv_query($conn, "SELECT *
            FROM manager
            INNER JOIN day_off_request ON manager.card_id = day_off_request.card_id
            INNER JOIN work_format ON day_off_request.edit_work_format_code = work_format.work_format_code
            LEFT JOIN employee ON day_off_request.card_id = employee.card_id
            WHERE manager.manager_card_id = ? AND day_off_request.approve_status = ?;
            ", array($_SESSION["card_id"], "reject"));
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
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/day-off-change-history.css">
<title>Document</title>
<script>
window.addEventListener('DOMContentLoaded', (event) => {
    const button1 = document.getElementById('button1');
    const button2 = document.getElementById('button2');
    const button3 = document.getElementById('button3');
    const nametable1 = document.getElementById('nametable1');
    const nametable2 = document.getElementById('nametable2');
    const nametable3 = document.getElementById('nametable3');

    // Initial state: Show nametable2
    button2.classList.add('active');
    nametable2.style.display = 'block';
    nametable1.style.display = 'none';
    nametable3.style.display = 'none';

    // Button 2 click event
    button2.addEventListener('click', () => {
        // Toggle button active state
        button2.classList.add('active');
        button1.classList.remove('active');
        button3.classList.remove('active');

        // Toggle table visibility
        nametable2.style.display = 'block';
        nametable1.style.display = 'none';
        nametable3.style.display = 'none';
    });

    // Button 1 click event
    button1.addEventListener('click', () => {
        // Toggle button active state
        button1.classList.add('active');
        button2.classList.remove('active');
        button3.classList.remove('active');

        // Toggle table visibility
        nametable1.style.display = 'block';
        nametable2.style.display = 'none';
        nametable3.style.display = 'none';
    });

    // Button 3 click event
    button3.addEventListener('click', () => {
        // Toggle button active state
        button3.classList.add('active');
        button1.classList.remove('active');
        button2.classList.remove('active');

        // Toggle table visibility
        nametable3.style.display = 'block';
        nametable1.style.display = 'none';
        nametable2.style.display = 'none';
    });
});
</script>
</head>

<body>
    <div class="navbar">
        <div class="div-span">
            <span>คำขอเปลี่ยนวันหยุด</span>
        </div>
    </div>
    <div class="boxFirst">
        <div class="text-left">
            <span>วันเริ่มต้น</span>
        </div>
        <span style="font-size: 16px; font-weight: bold;">ถึง</span>
        <div class="text-right">
            <span>วันสิ้นสุด</span>
        </div>
    </div>
    <div class="buttonStatus">
        <button class="buttonl" id="button1">
            <span id="text1">อนุมัติแล้ว</span>
        </button>
        <button class="buttonl active" id="button2">
            <span id="text2">รออนุมัติ</span>
        </button>
        <button class="buttonl" id="button3">
            <span id="text3">ปฏิเสธ</span>
        </button>
    </div>

    <div class="table">
        <table id="nametable1">
            <?php
            echo '<tr>';
            echo '<th>รหัส</th>';
            echo '<th>ชื่อ - สกุล</th>';
            echo '<th>เดิม</th>';
            echo '<th>ใหม่</th>';
            echo '<th>สถานะ</th>';
            echo '</tr>';

            while ($row = sqlsrv_fetch_array($sql_day_off_request_confirm, SQLSRV_FETCH_ASSOC)) {
                echo '<tr>';
                echo '<td>' . $row['scg_employee_id'] . '</td>';
                echo '<td>' . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . '</td>';
                echo '<td>' . $rowg['day_off1'] . "-" . $rowg['day_off2'] . '</td>';
                echo '<td>' . $row['day_off1'] . "-" . $row['day_off2'] . '</td>';
                echo '<td style="color:green;">อนุมัติ</td>';
                echo '</tr>';
            }
            // End the HTML table
            ?>
        </table>

        <table id="nametable2">
            <!-- Table content for 'waiting' status -->
            <?php
            echo '<tr>';
            echo '<th>รหัส</th>';
            echo '<th>ชื่อ - สกุล</th>';
            echo '<th>เดิม</th>';
            echo '<th>ใหม่</th>';
            echo '<th>สถานะ</th>';
            echo '</tr>';

            while ($row = sqlsrv_fetch_array($sql_day_off_request_waiting, SQLSRV_FETCH_ASSOC)) {
                echo '<tr>';
                echo '<td>' . $row['scg_employee_id'] . '</td>';
                echo '<td>' . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . '</td>';
                echo '<td>' . $rowg['day_off1'] . "-" . $rowg['day_off2'] . '</td>';
                echo '<td>' . $row['day_off1'] . "-" . $row['day_off2'] . '</td>';
                echo '<td class="status">
                        <button class="approve" onclick="window.location.href=\'process_dayoff_approve.php?id=' . $row['day_off_req_id'] . '\'">อนุมัติ</button>
                        <button class="reject" onclick="window.location.href=\'process_dayoff_reject.php?id=' . $row['day_off_req_id'] . '\'">ปฎิเสธ</button>
                    </td>';
                echo '</tr>';
            }
            ?>
        </table>

        <table id="nametable3">
            <!-- Table content for 'reject' status -->
            <?php
            echo '<tr>';
            echo '<th>รหัส</th>';
            echo '<th>ชื่อ - สกุล</th>';
            echo '<th>เดิม</th>';
            echo '<th>ใหม่</th>';
            echo '<th>สถานะ</th>';
            echo '</tr>';

            // Iterate through the fetched data and populate the table rows
            while ($row = sqlsrv_fetch_array($sql_day_off_request_reject, SQLSRV_FETCH_ASSOC)) {
                echo '<tr>';
                echo '<td>' . $row['scg_employee_id'] . '</td>';
                echo '<td>' . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . '</td>';
                echo '<td>' . $rowg['day_off1'] . "-" . $rowg['day_off2'] . '</td>';
                echo '<td>' . $row['day_off1'] . "-" . $row['day_off2'] . '</td>';
                echo '<td style="color:red;">ไม่อนุมัติ</td>';
                echo '</tr>';
            }
            // End the HTML table
            ?>
        </table>
    </div>

</body>
<?php include('../includes/footer.php') ?>

</html>