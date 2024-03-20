<?php
session_start();
include('../components-desktop/head/include/header.php');
require_once("../includes/header.php");
include("../database/connectdb.php");
$time_stamp = date("Y-m-d");

// Database queries with placeholders
$select_check_inout_Query = "SELECT manager.manager_card_id,
check_inout.card_id,
check_inout.shift_type_id,
check_inout.date,
check_inout.time_in,
check_inout.time_out,
check_inout.check_status,
shift_type.start_time,
shift_type.end_time,
employee.scg_employee_id,
employee.prefix_thai,
employee.firstname_thai,
employee.lastname_thai
FROM manager
INNER JOIN check_inout ON manager.card_id = check_inout.card_id
LEFT JOIN shift_type ON check_inout.shift_type_id = shift_type.shift_type_id
INNER JOIN employee ON check_inout.card_id = employee.card_id WHERE manager.manager_card_id  = ? AND check_inout.date = ? ORDER BY employee.scg_employee_id ASC";

$select_absence_record_Query = "SELECT *
    FROM manager 
    INNER JOIN absence_record ON manager.card_id = absence_record.card_id 
    INNER JOIN employee ON absence_record.card_id = employee.card_id
    WHERE manager.manager_card_id = ?
      AND ? BETWEEN absence_record.date_start AND absence_record.date_end;
";


// Prepare statements
$check_inout_stmt = sqlsrv_prepare($conn, $select_check_inout_Query, array(&$_SESSION["card_id"], &$time_stamp));

$sql_absencecount = sqlsrv_query($conn, $select_absence_record_Query, array($_SESSION["card_id"], $time_stamp));

sqlsrv_execute($check_inout_stmt);
sqlsrv_execute($sql_absencecount);

// Fetch data for check_inout
$check_inout_data = array();
while ($row = sqlsrv_fetch_array($check_inout_stmt, SQLSRV_FETCH_ASSOC)) {
    $check_inout_data[] = $row;
}

// Fetch data for absence_record
$absence_record_data = array();
while ($row = sqlsrv_fetch_array($sql_absencecount, SQLSRV_FETCH_ASSOC)) {
    $absence_record_data[] = $row;
}


?>
<link rel="stylesheet" href="../assets/css/approve-status-timeEm.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/navbar.css">
<title>Document</title>

<script>
    function showStatus(status) {
        // ซ่อนทั้งหมดก่อน
        document.querySelectorAll('.status-detail > div').forEach(function (div) {
            div.style.display = 'none';
        });

        // แสดงเฉพาะที่เลือก
        document.querySelector('.display-' + status).style.display = 'block';

        // แสดงเป็น modal
        selectedTable.classList.add('modal');
    }
</script>

</head>

<body>
    <div class="navbar">
        <div class="div-span">
            <span>รายงานผลการบันทึกเวลา</span>
        </div>
    </div>

    <div class="btn-status">

        <div class="btn-normal">
            <button onclick="showStatus('normal')">ปกติ</button>
        </div>
        <div class="btn-leave">
            <button  onclick="showStatus('leave')">ลา</button>
        </div>
        <div class="btn-late">
            <button  onclick="showStatus('late')">สาย</button>
        </div>
        <div class="btn-goBack">
            <button  onclick="showStatus('goBack')">ออกก่อน</button>
        </div>

        <div class="btn-missing">
            <button  onclick="showStatus('missing')">ขาด</button>
        </div>
        <div class="btn-total">
            <button onclick="showStatus('total')">ทั้งหมด</button>
        </div>

    </div>

    <div class="status-detail">
        <div class="display-normal">
            <table>
                <tr>
                    <th colspan=4 style="height: 5vh">รายการ : การเข้างานตรงเวลา</th>
                </tr>
                <tr>
                    <th>โปรไฟล์</th>
                    <th>ชื่อ - สกุล</th>
                    <th>เวลาเข้า</th>
                    <th>เวลาออก</th>
                </tr>
                <!-- Display data for check_inout -->
                <?php foreach ($check_inout_data as $row):
                    $check_status = $row["check_status"];
                    if ($check_status == "ตรงเวลา"):
                        ?>
                        <tr>
                            <td><img src="../IMG/user.png" alt="" style="width: 10vw; height: 10vw;"></td>
                            <td style="width: 50vw;"><span>
                                    <?= $row['scg_employee_id'] ?> -
                                    <?= $row['prefix_thai'] ?>
                                    <?= $row['firstname_thai'] ?>
                                    <?= $row['lastname_thai'] ?>
                                </span></td>
                            <td><span>
                                    <?= isset($row['time_in']) ? $row['time_in']->format('H:i') : ''; ?>
                                </span></td>
                            <td><span>
                                    <?= isset($row['time_out']) ? $row['time_out']->format('H:i') : ''; ?>
                                </span></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="display-late">
            <table>
                <tr>
                    <th colspan=4 style="height: 5vh">รายการ : การเข้างานสาย</th>
                </tr>

                <tr>

                    <th>โปรไฟล์</th>
                    <th>ชื่อ - สกุล</th>
                    <th>เวลาเข้า</th>
                    <th>เวลาออก</th>
                </tr>

                <?php foreach ($check_inout_data as $row):
                    $check_status = $row["check_status"];
                    if ($check_status == "มาสาย"):
                        ?>
                        <tr>
                            <td><img src="../IMG/user.png" alt="" style="width: 10vw; height: 10vw;"></td>
                            <td style="width: 50vw;"><span>
                                    <?= $row['scg_employee_id'] ?> -
                                    <?= $row['prefix_thai'] ?>
                                    <?= $row['firstname_thai'] ?>
                                    <?= $row['lastname_thai'] ?>
                                </span></td>
                            <td><span>
                                    <?= isset($row['time_in']) ? $row['time_in']->format('H:i') : ''; ?>
                                </span></td>
                            <td><span>
                                    <?= isset($row['time_out']) ? $row['time_out']->format('H:i') : ''; ?>
                                </span></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>

            </table>
        </div>
        <div class="display-goBack">
            <table>
                <tr>
                    <th colspan=4 style="height: 5vh">รายการ : การออกก่อนเวลา</th>
                </tr>
                <tr>
                    <th>โปรไฟล์</th>
                    <th>ชื่อ - สกุล</th>
                    <th>เวลาเข้า</th>
                    <th>เวลาออก</th>
                </tr>

                <?php foreach ($check_inout_data as $row):
                    $check_status = $row["check_status"];
                    if ($check_status == "กลับก่อน"):
                        ?>
                        <tr>
                            <td><img src="../IMG/user.png" alt="" style="width: 10vw; height: 10vw;"></td>
                            <td style="width: 50vw;"><span>
                                    <?= $row['scg_employee_id'] ?> -
                                    <?= $row['prefix_thai'] ?>
                                    <?= $row['firstname_thai'] ?>
                                    <?= $row['lastname_thai'] ?>
                                </span></td>
                            <td><span>
                                    <?= isset($row['time_in']) ? $row['time_in']->format('H:i') : ''; ?>
                                </span></td>
                            <td><span>
                                    <?= isset($row['time_out']) ? $row['time_out']->format('H:i') : ''; ?>
                                </span></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>

            </table>
        </div>
        <div class="display-missing">
            <table>
                <tr>
                    <th colspan=4 style="height: 5vh">รายการ : การขาดงาน</th>
                </tr>

                <tr>
                    <th>โปรไฟล์</th>
                    <th>ชื่อ - สกุล</th>
                    <th>เวลาเข้า</th>
                    <th>เวลาออก</th>
                </tr>

                <?php foreach ($check_inout_data as $row):
                    $check_status = $row["check_status"];
                    if ($check_status == "ขาดงาน"): ?>
                        <tr>
                            <td><img src="../IMG/user.png" alt="" style="width: 10vw; height: 10vw;"></td>
                            <td style="width: 50vw;"><span>
                                    <?= $row['scg_employee_id'] ?> -
                                    <?= $row['prefix_thai'] ?>
                                    <?= $row['firstname_thai'] ?>
                                    <?= $row['lastname_thai'] ?>
                                </span></td>
                            <td><span>
                                    <?= isset($row['time_in']) ? $row['time_in']->format('H:i') : ''; ?>
                                </span></td>
                            <td><span>
                                    <?= isset($row['time_out']) ? $row['time_out']->format('H:i') : ''; ?>
                                </span></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="display-total">
            <table>
                <tr>
                    <th colspan=4 style="height: 5vh">รายการ : การเข้างานทั้งหมด</th>
                </tr>

                <tr>
                    <th>โปรไฟล์</th>
                    <th>ชื่อ - สกุล</th>
                    <th>เวลาเข้า</th>
                    <th>เวลาออก</th>
                </tr>

                <?php foreach ($check_inout_data as $row): ?>
                    <tr>
                        <td><img src="../IMG/user.png" alt="" style="width: 10vw; height: 10vw;"></td>
                        <td style="width: 50vw;"><span>
                                <?= $row['scg_employee_id'] ?> -
                                <?= $row['prefix_thai'] ?>
                                <?= $row['firstname_thai'] ?>
                                <?= $row['lastname_thai'] ?>
                            </span></td>
                        <td><span>
                                <?= isset($row['time_in']) ? $row['time_in']->format('H:i') : ''; ?>
                            </span></td>
                        <td><span>
                                <?= isset($row['time_out']) ? $row['time_out']->format('H:i') : ''; ?>
                            </span></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="display-leave">
            <table>
                <tr>
                    <th colspan=4 style="height: 5vh">รายการ : การลางาน</th>
                </tr>
                <tr>
                    <th>โปรไฟล์</th>
                    <th>ชื่อ - สกุล</th>
                    <th>เริ่มต้น</th>
                    <th>สิ้นสุด</th>
                </tr>
                <!-- Display data for absence_record -->
                <?php foreach ($absence_record_data as $row): ?>
                    <tr>
                        <td><img src="../IMG/user.png" alt="" style="width: 10vw; height: 10vw; "></td>
                        <td style="width: 50vw;"><span>
                                <?= $row['scg_employee_id'] ?> -
                                <?= $row['prefix_thai'] ?>
                                <?= $row['firstname_thai'] ?>
                                <?= $row['lastname_thai'] ?>
                            </span></td>
                        <td><span>
                                <?= isset($row['date_start']) ? $row['date_start']->format('d-m-y') : ''; ?>
                            </span></td>
                        <td><span>
                                <?= isset($row['date_end']) ? $row['date_end']->format('d-m-y') : ''; ?>
                            </span></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    <?php include('../includes/footer.php'); ?>
</body>