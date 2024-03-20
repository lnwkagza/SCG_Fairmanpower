<?php
session_start();
session_regenerate_id(true);
//---------------------------------------------------------------------------------------
include("../database/connectdb.php");
include('../components-desktop/admin/include/header.php');
// include("dbconnect.php");
//---------------------------------------------------------------------------------------
?>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/admin/day-off-fix-tradition-admin.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/day-off-tradition.css">

<!-- datatable -->
<link href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.bootstrap5.min.js"></script>

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="../assets/script/loader-normal.js"></script>

<?php

date_default_timezone_set('Asia/Bangkok');
$thaiDayAbbreviations = array(
    'Monday' => 'จันทร์',
    'Tuesday' => 'อังคาร',
    'Wednesday' => 'พุธ',
    'Thursday' => 'พฤหัสบดี',
    'Friday' => 'ศุกร์',
    'Saturday' => 'เสาร์',
    'Sunday' => 'อาทิตย์'
);
function thaiMonthName($month)
{
    $months = [
        '01' => 'มกราคม',
        '02' => 'กุมภาพันธ์',
        '03' => 'มีนาคม',
        '04' => 'เมษายน',
        '05' => 'พฤษภาคม',
        '06' => 'มิถุนายน',
        '07' => 'กรกฎาคม',
        '08' => 'สิงหาคม',
        '09' => 'กันยายน',
        '10' => 'ตุลาคม',
        '11' => 'พฤศจิกายน',
        '12' => 'ธันวาคม',
    ];

    return isset($months[$month]) ? $months[$month] : '';
}

$time_stamp = date("Y");
$Y_thai = 543;
$datethai = $time_stamp + $Y_thai;

$sql = "SELECT * FROM holiday WHERE YEAR(date) = ? ORDER BY date ASC";
$params = array($time_stamp);

$holiday = sqlsrv_query($conn, $sql, $params);

$holidaydata = array();

while ($row = sqlsrv_fetch_array($holiday, SQLSRV_FETCH_ASSOC)) {
    $holidaydata[] = $row;
}
?>

<script>
    $(document).ready(function() {
        //data-table
        new DataTable('#table1', {
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
</head>

<body>
    <div id="loader"></div>
    <div style="display:none;" id="Content" class="animate-bottom">

        <div class="desktop">
            <?php include('../components-desktop/admin/include/sidebar.php'); ?>
            <?php include('../components-desktop/admin/include/navbar.php'); ?>

            <div class="main-container">
                <div class="pd-ltr-20 xs-pd-20-10">
                    <div class="min-height-200px">
                        <div class="page-header">
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="title">
                                        <h2>วันหยุดตามประเพณีประจำปี
                                            <?= $datethai; ?>
                                        </h2>
                                    </div>
                                    <nav aria-label="breadcrumb" role="navigation">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item">
                                                <a style="pointer-events:none;cursor:default;">วันหยุด</a>
                                            </li>
                                            <li class="breadcrumb-item active" aria-current="page" style="cursor:default;">
                                                วันหยุดตามประเพณี
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-5 col-md-6 col-sm-12 mb-30">
                                <div class="card-box pd-30 pt-10 height-100-p">

                                    <div class="display-calender">
                                        <div class="display-monthNow">
                                            <button id="prev-month"><i class="fa-solid fa-angles-left"></i></button>
                                            <span id="current-month-title"></span>
                                            <button id="next-month"><i class="fa-solid fa-angles-right"></i></button>
                                        </div>

                                        <div class="display-day-of-month">
                                            <div class="desktop-calendar" id="desktop-calendar"></div>
                                        </div>

                                    </div>
                                    <hr>
                                    <div class="color-detail">
                                        <div id="status1">
                                            <div>
                                                <button style="background-color:#bc3032;"></button><span>วันหยุดตามประเพณี</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-7 col-md-6 col-sm-12 mb-30">
                                <div class="card-box pd-30 pt-10 height-100-p">

                                    <div class="desktop-data-table-container">
                                        <table id="table1" class="table stripe hover nowrap">
                                            <thead>
                                                <tr>
                                                    <th>วันที่</th>
                                                    <th>วัน</th>
                                                    <th>ชื่อวันหยุด</th>
                                                    <th>การจัดการ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $desktop_currentMonth = '';
                                                foreach ($holidaydata as $row) :
                                                    $englishDay = date('l', strtotime($row['date']->format("Y-m-d")));
                                                    $thaiAbbreviation = isset($thaiDayAbbreviations[$englishDay]) ? $thaiDayAbbreviations[$englishDay] : '';
                                                    $month = (new DateTime($row['date']->format("Y-m-d")))->format("m");
                                                ?>
                                                    <tr>
                                                        <?php if ($month != $desktop_currentMonth) { ?>
                                                            <td>
                                                                <?php echo $row['date']->format("d") . " " . thaiMonthName($month) . " " . $datethai; ?>
                                                            </td>
                                                        <?php } ?>
                                                        <td>
                                                            <?php echo $thaiAbbreviation; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $row['name'] ?>
                                                        </td>
                                                        <td>
                                                            <div class="btn btn-danger" onclick="delete_dayoff(<?php echo $row['holiday_id']; ?>)">
                                                                <i class=" fa fa-trash" aria-hidden="true"></i>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <script>
                                        document.addEventListener("DOMContentLoaded", function() {
                                            const desktop_currentDate = new Date();
                                            const desktop_currentYear = desktop_currentDate.getFullYear();
                                            const desktop_currentMonth = desktop_currentDate.getMonth();
                                            desktop_updateMonthTitle(desktop_currentYear,
                                                desktop_currentMonth);
                                            desktop_generateCalendar(desktop_currentYear,
                                                desktop_currentMonth);

                                            const desktop_prevMonthButton = document.getElementById(
                                                "prev-month");
                                            desktop_prevMonthButton.addEventListener("click",
                                                desktop_showPreviousMonth);

                                            const desktop_nextMonthButton = document.getElementById(
                                                "next-month");
                                            desktop_nextMonthButton.addEventListener("click",
                                                desktop_showNextMonth);
                                        });

                                        function desktop_updateMonthTitle(year, month) {
                                            const desktop_currentMonthTitleElement = document.getElementById(
                                                "current-month-title");
                                            desktop_currentMonthTitleElement.textContent = desktop_getMonthName(
                                                    month) +
                                                " " + year;
                                        }

                                        function desktop_generateCalendar(year, month, holidayData) {
                                            const desktop_firstDayOfMonth = new Date(year, month, 1);
                                            const desktop_lastDayOfMonth = new Date(year, month + 1, 0);
                                            const desktop_daysInMonth = desktop_lastDayOfMonth.getDate();
                                            const desktop_calendarDiv = document.getElementById("desktop-calendar");
                                            let calendarHTML = "<table class='table hover nowrap'>";
                                            calendarHTML +=
                                                "<tr><th>อา.</th><th>จ.</th><th>อ.</th><th>พ.</th><th>พฤ.</th><th>ศ.</th><th>ส.</th></tr><tr>";

                                            let dayOfWeek = desktop_firstDayOfMonth.getDay();
                                            let rowCount = 1;

                                            for (let i = 0; i < dayOfWeek; i++) {
                                                calendarHTML += "<td></td>";
                                            }

                                            for (let day = 1; day <= desktop_daysInMonth; day++) {
                                                const desktop_currentDate = new Date(year, month, day);

                                                const holidayData = [];
                                                <?php
                                                foreach ($holidaydata as $row) {
                                                    $previousDay = date('Y-m-d', strtotime($row['date']->format("Y-m-d") . ' -1 day'));
                                                ?>
                                                    holidayData.push("<?php echo $previousDay; ?>");
                                                <?php } ?>


                                                const isHoliday = holidayData.includes(desktop_currentDate
                                                    .toISOString()
                                                    .split('T')[
                                                        0]);

                                                if (isHoliday) {
                                                    calendarHTML +=
                                                        `<td class="tradition"><div class="tradition-dayoff">${day}</div></td>`;
                                                } else if (dayOfWeek === 6 || dayOfWeek === 0) {
                                                    calendarHTML +=
                                                        `<td class="tradition" onclick="dayoff_compensate(${day},${month},${year},)">${day}</td>`;
                                                } else {
                                                    calendarHTML +=
                                                        `<td class="tradition" onclick="dayoff(${day},${month},${year},)">${day}</td>`;
                                                }

                                                if (dayOfWeek === 6 && day < desktop_daysInMonth) {
                                                    calendarHTML += "</tr><tr>";
                                                    rowCount++;
                                                }

                                                dayOfWeek = (dayOfWeek + 1) % 7;
                                            }

                                            const isLastDaySaturday = desktop_lastDayOfMonth.getDay() === 6;

                                            if (rowCount > 5 && !isLastDaySaturday) {
                                                calendarHTML += "</tr>";
                                            }

                                            calendarHTML += "</table>";
                                            desktop_calendarDiv.innerHTML = calendarHTML;
                                        }


                                        function desktop_showPreviousMonth() {
                                            const desktop_currentMonthTitleElement = document.getElementById(
                                                "current-month-title");
                                            let desktop_currentYear = parseInt(desktop_currentMonthTitleElement
                                                .textContent
                                                .split(
                                                    " ")[1]);
                                            let desktop_currentMonth = desktop_getMonthIndex(
                                                desktop_currentMonthTitleElement
                                                .textContent.split(
                                                    " ")[0]);
                                            desktop_currentMonth = (desktop_currentMonth - 1 + 12) % 12;

                                            if (desktop_currentMonth === 11) {
                                                desktop_currentYear -= 1;
                                            }

                                            desktop_updateMonthTitle(desktop_currentYear, desktop_currentMonth);
                                            desktop_generateCalendar(desktop_currentYear, desktop_currentMonth);
                                        }

                                        function desktop_showNextMonth() {
                                            const desktop_currentMonthTitleElement = document.getElementById(
                                                "current-month-title");
                                            let desktop_currentYear = parseInt(desktop_currentMonthTitleElement
                                                .textContent
                                                .split(
                                                    " ")[1]);
                                            let desktop_currentMonth = desktop_getMonthIndex(
                                                desktop_currentMonthTitleElement
                                                .textContent.split(
                                                    " ")[0]);
                                            desktop_currentMonth = (desktop_currentMonth + 1) % 12;

                                            if (desktop_currentMonth === 0) {
                                                desktop_currentYear += 1;
                                            }

                                            desktop_updateMonthTitle(desktop_currentYear, desktop_currentMonth);
                                            desktop_generateCalendar(desktop_currentYear, desktop_currentMonth);
                                        }

                                        function desktop_getMonthIndex(monthName) {
                                            const monthNames = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน",
                                                "พฤษภาคม",
                                                "มิถุนายน", "กรกฎาคม",
                                                "สิงหาคม",
                                                "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
                                            ];
                                            return monthNames.indexOf(monthName);
                                        }

                                        function desktop_getMonthName(monthIndex) {
                                            const monthNames = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน",
                                                "พฤษภาคม",
                                                "มิถุนายน", "กรกฎาคม",
                                                "สิงหาคม",
                                                "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
                                            ];
                                            return monthNames[monthIndex];
                                        }

                                        function dayoff(day, month, year) {
                                            swal.fire({
                                                html: '<div class="topic">วันที่ ' + day + ' ' +
                                                    desktop_getMonthName(
                                                        month) + ' ' + year +
                                                    '</div><br>' +
                                                    '<div class="content1">กำหนดให้เป็นวันหยุดตามประเพณี</div>' +
                                                    '<div class="dateFix"><input class="form-control" id="nameday" type="text"><br></div>',
                                                padding: '2em',
                                                confirmButtonText: 'ยืนยัน',
                                                cancelButtonText: 'ยกเลิก',
                                                confirmButtonColor: '#6FA803',
                                                cancelButtonColor: '#FF0000',
                                                showCancelButton: true,
                                                customClass: {
                                                    confirmButtonText: 'swal2-confirm',
                                                    cancelButtonText: 'swal2-cancel',
                                                    container: 'custom-swal-container',
                                                },
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    desktop_insertHoliday(day, month, year);
                                                } else {
                                                    swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
                                                }
                                            });
                                        }

                                        function dayoff_compensate(day, month, year) {
                                            swal.fire({
                                                html: '<div class="topic">วันที่ ' + day + ' ' +
                                                    desktop_getMonthName(
                                                        month) + ' ' + year +
                                                    '</div><br>' +
                                                    '<div class="content1">โปรดกำหนดวันหยุดชดเชย</div>' +
                                                    '<div class="dateFix"><input class="form-control" id="nameday" type="text"><br></div>',
                                                padding: '2em',
                                                confirmButtonText: 'ยืนยัน',
                                                cancelButtonText: 'ยกเลิก',
                                                confirmButtonColor: '#6FA803',
                                                cancelButtonColor: '#FF0000',
                                                showCancelButton: true,
                                                customClass: {
                                                    confirmButtonText: 'swal2-confirm',
                                                    cancelButtonText: 'swal2-cancel',
                                                    container: 'custom-swal-container',
                                                },
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    desktop_insertHoliday(day, month, year);
                                                } else {
                                                    swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
                                                }
                                            });
                                        }


                                        function dayoff_submit() {
                                            swal.fire({
                                                html: '<div style="font-weight: bold; font-size: 20px;">กำหนดวันหยุดสำเร็จ</div><br>' +
                                                    '<img src="../IMG/check1.png" style="width:80px; margin-top: 2px; height:80px;"></img>',
                                                padding: '2em',
                                                confirmButtonText: 'ตกลง',
                                                confirmButtonColor: '#6FA803',
                                                showConfirmButton: true,
                                                showCancelButton: false
                                            }).then(() => {
                                                // Reload the page after the modal is closed
                                                location.reload();
                                            });
                                        }

                                        function delete_dayoff(id) {
                                            Swal.fire({
                                                title: "<strong>ยืนยันการลบวันหยุดหรือไม่</strong>",
                                                icon: "warning",
                                                showCloseButton: true,
                                                showCancelButton: true,
                                                focusConfirm: false,
                                                confirmButtonText: `ตกลง`,
                                                cancelButtonText: `ยกเลิก`,
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    desktop_deleteHoliday(id);
                                                } else {
                                                    swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
                                                }
                                            });
                                        }

                                        function delete_dayoff_submit() {
                                            swal.fire({
                                                html: '<div style="font-weight: bold; font-size: 20px;">ลบวันหยุดสำเร็จ</div><br>' +
                                                    '<img src="../IMG/check1.png" style="width:80px; margin-top: 2px; height:80px;"></img>',
                                                padding: '2em',
                                                confirmButtonText: 'ตกลง',
                                                confirmButtonColor: '#6FA803',
                                                showConfirmButton: true,
                                                showCancelButton: false
                                            }).then(() => {
                                                // Reload the page after the modal is closed
                                                location.reload();
                                            });
                                        }

                                        function desktop_insertHoliday(day, month, year) {
                                            var formattedDate = year + '-' + (month + 1) + '-' + day;
                                            var nameday = document.getElementById('nameday').value;

                                            $.ajax({
                                                type: "POST",
                                                url: "../processing/process_dayoff_insert_holiday.php",
                                                data: {
                                                    date: formattedDate,
                                                    nameday: nameday
                                                },
                                                success: function(response) {
                                                    console.log(response);
                                                    dayoff_submit();
                                                },
                                                error: function(error) {
                                                    console.log(error);
                                                    swal("เกิดข้อผิดพลาดในการบันทึกข้อมูล");
                                                }
                                            });
                                        }

                                        function desktop_deleteHoliday(id) {
                                            $.ajax({
                                                type: "POST",
                                                url: "../processing/process_dayoff_delete_holiday.php",
                                                data: {
                                                    id
                                                },
                                                success: function(response) {
                                                    console.log(response);
                                                    delete_dayoff_submit();
                                                },
                                                error: function(error) {
                                                    console.log(error);
                                                    swal("เกิดข้อผิดพลาดในการบันทึกข้อมูล");
                                                }
                                            });
                                        }
                                    </script>
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
                    <span>วันหยุดตามประเพณีนิยม</span>
                </div>
            </div>

            <div class="color-meanDay">
                <div class="traditionDay">
                    <img src="../IMG/dot1.png" alt="">
                    <span>วันหยุดตามประเพณี</span>
                </div>
                <div class="Normal-Dayoff">
                    <img src="../IMG/dot2.png" alt="">
                    <span>วันปกติ</span>
                </div>
            </div>

            <div class="display-calender">
                <div class="display-monthNow">
                    <button id="prevMonth"><img src="../IMG/arrow1.png" alt=""></button>
                    <span id="currentMonthTitle"></span>
                    <button id="nextMonth"><img src="../IMG/arrow.png" alt=""></button>
                </div>

                <div class="display-day-of-month">
                    <div id="calendar"></div>
                </div>

            </div>

            <div class="detail-dayoff-month">
                <div class="topic-detail">
                    <span class="topic">รายละเอียดวันหยุดในปีนี้</span>
                </div>
                <?php
                $currentMonth = ''; // Variable to track the current month

                foreach ($holidaydata as $row) :
                    $englishDay = date('l', strtotime($row['date']->format("Y-m-d")));
                    $thaiAbbreviation = isset($thaiDayAbbreviations[$englishDay]) ? $thaiDayAbbreviations[$englishDay] : '';
                    $month = (new DateTime($row['date']->format("Y-m-d")))->format("m");
                    // Check if the month has changed
                    if ($month != $currentMonth) {
                        echo '<div class="show-month">';
                        echo '<span>' . thaiMonthName($month) . '</span>';
                        echo '</div>';
                        $currentMonth = $month;
                    }
                ?>
                    <div class="show-detail-day-fix">
                        <div class="container-detail-fix">
                            <span class="number">
                                <?php echo (new DateTime($row['date']->format("Y-m-d")))->format("d"); ?>
                            </span>
                            <span class="day">
                                <?php echo $thaiAbbreviation; ?>
                            </span>
                            <span class="detail" onclick="showFullText('<?php echo $row['name']; ?>')">
                                <?php echo mb_strimwidth($row['name'], 0, 25, '...'); ?>
                                <span class="popup-text">
                                    <?php echo $row['name']; ?>
                                </span>
                            </span>

                            <div id="myModal" class="modal">
                                <div class="modal-content">
                                    <span class="close" onclick="closeModal()">&times;</span>
                                    <span id="fullText"></span>
                                </div>
                            </div>
                            <script>
                                function showFullText(fullText) {
                                    document.getElementById("fullText").innerHTML = fullText;
                                    document.getElementById("myModal").style.display = "block";
                                }

                                function closeModal() {
                                    document.getElementById("myModal").style.display = "none";
                                }
                            </script>
                            <div class="dateFix">
                                <img src="../IMG/bin.png" onclick="myFunction3(<?php echo $row['holiday_id']; ?>)" />
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const currentDate = new Date();
                    const currentYear = currentDate.getFullYear();
                    const currentMonth = currentDate.getMonth();
                    updateMonthTitle(currentYear, currentMonth);
                    generateCalendar(currentYear, currentMonth);

                    const prevMonthButton = document.getElementById("prevMonth");
                    prevMonthButton.addEventListener("click", showPreviousMonth);

                    const nextMonthButton = document.getElementById("nextMonth");
                    nextMonthButton.addEventListener("click", showNextMonth);
                });

                function updateMonthTitle(year, month) {
                    const currentMonthTitleElement = document.getElementById("currentMonthTitle");
                    currentMonthTitleElement.textContent = getMonthName(month) + " " + year;
                }

                function generateCalendar(year, month, holidayData) {
                    const firstDayOfMonth = new Date(year, month, 1);
                    const lastDayOfMonth = new Date(year, month + 1, 0);
                    const daysInMonth = lastDayOfMonth.getDate();
                    const calendarDiv = document.getElementById("calendar");
                    let calendarHTML = "<table>";
                    calendarHTML +=
                        "<tr><th>อา.</th><th>จ.</th><th>อ.</th><th>พ.</th><th>พฤ.</th><th>ศ.</th><th>ส.</th></tr><tr>";

                    let dayOfWeek = firstDayOfMonth.getDay();
                    let rowCount = 1;

                    for (let i = 0; i < dayOfWeek; i++) {
                        calendarHTML += "<td></td>";
                    }

                    for (let day = 1; day <= daysInMonth; day++) {
                        const currentDate = new Date(year, month, day);

                        const holidayData = [];
                        <?php
                        foreach ($holidaydata as $row) {
                            $previousDay = date('Y-m-d', strtotime($row['date']->format("Y-m-d") . ' -1 day'));
                        ?>
                            holidayData.push("<?php echo $previousDay; ?>");
                        <?php } ?>


                        const isHoliday = holidayData.includes(currentDate.toISOString().split('T')[0]);

                        if (isHoliday) {
                            calendarHTML += `<td class="traditiondayoff">${day}</td>`;
                        } else if (dayOfWeek === 6 || dayOfWeek === 0) {
                            calendarHTML +=
                                `<td class="tradition" onclick="myFunction0(${day},${month},${year},)">${day}</td>`;
                        } else {
                            calendarHTML +=
                                `<td class="tradition" onclick="myFunction1(${day},${month},${year},)">${day}</td>`;
                        }

                        if (dayOfWeek === 6 && day < daysInMonth) {
                            calendarHTML += "</tr><tr>";
                            rowCount++;
                        }

                        dayOfWeek = (dayOfWeek + 1) % 7;
                    }

                    const isLastDaySaturday = lastDayOfMonth.getDay() === 6;

                    if (rowCount > 5 && !isLastDaySaturday) {
                        calendarHTML += "</tr>";
                    }

                    calendarHTML += "</table>";
                    calendarDiv.innerHTML = calendarHTML;
                }


                function showPreviousMonth() {
                    const currentMonthTitleElement = document.getElementById("currentMonthTitle");
                    let currentYear = parseInt(currentMonthTitleElement.textContent.split(" ")[1]);
                    let currentMonth = getMonthIndex(currentMonthTitleElement.textContent.split(" ")[0]);
                    currentMonth = (currentMonth - 1 + 12) % 12;

                    if (currentMonth === 11) {
                        currentYear -= 1;
                    }

                    updateMonthTitle(currentYear, currentMonth);
                    generateCalendar(currentYear, currentMonth);
                }

                function showNextMonth() {
                    const currentMonthTitleElement = document.getElementById("currentMonthTitle");
                    let currentYear = parseInt(currentMonthTitleElement.textContent.split(" ")[1]);
                    let currentMonth = getMonthIndex(currentMonthTitleElement.textContent.split(" ")[0]);
                    currentMonth = (currentMonth + 1) % 12;

                    if (currentMonth === 0) {
                        currentYear += 1;
                    }

                    updateMonthTitle(currentYear, currentMonth);
                    generateCalendar(currentYear, currentMonth);
                }

                function getMonthIndex(monthName) {
                    const monthNames = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
                        "กรกฎาคม",
                        "สิงหาคม",
                        "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
                    ];
                    return monthNames.indexOf(monthName);
                }

                function getMonthName(monthIndex) {
                    const monthNames = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
                        "กรกฎาคม",
                        "สิงหาคม",
                        "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
                    ];
                    return monthNames[monthIndex];
                }

                function myFunction1(day, month, year) {
                    swal.fire({
                        html: '<div class="topic">วันที่ ' + day + ' ' + getMonthName(month) + ' ' + year +
                            '</div><br>' +
                            '<div class="content1">กำหนดให้เป็นวันหยุดตามประเพณี</div>' +
                            '<div class="dateFix"><input id="nameday" type="text"><br></div>',
                        padding: '2em',
                        confirmButtonText: 'ยืนยัน',
                        confirmButtonColor: '#f1ba3d',
                        cancelButtonText: 'ยกเลิก',
                        cancelButtonColor: '#FF0000',
                        showCancelButton: true,
                        customClass: {
                            confirmButtonText: 'swal2-confirm',
                            cancelButtonText: 'swal2-cancel',
                            container: 'custom-swal-container',
                        },
                    }).then((result) => {
                        if (result.isConfirmed) {
                            insertHoliday(day, month, year);
                        } else {
                            swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
                        }
                    });
                }

                function myFunction0(day, month, year) {
                    swal.fire({
                        html: '<div class="topic">วันที่ ' + day + ' ' + getMonthName(month) + ' ' + year +
                            '</div><br>' +
                            '<div class="content1">โปรดกำหนดวันหยุดชดเชย</div>' +
                            '<div class="dateFix"><input id="nameday" type="text"><br></div>',
                        padding: '2em',
                        confirmButtonText: 'ยืนยัน',
                        confirmButtonColor: '#f1ba3d',
                        cancelButtonText: 'ยกเลิก',
                        cancelButtonColor: '#FF0000',
                        showCancelButton: true,
                        customClass: {
                            confirmButtonText: 'swal2-confirm',
                            cancelButtonText: 'swal2-cancel',
                            container: 'custom-swal-container',
                        },
                    }).then((result) => {
                        if (result.isConfirmed) {
                            insertHoliday(day, month, year);
                        } else {
                            swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
                        }
                    });
                }


                function myFunction2() {
                    swal.fire({
                        html: '<div style="font-weight: bold; font-size: 20px;">กำหนดวันหยุดสำเร็จ</div><br>' +
                            '<img class="img" src="../IMG/check1.png"></img>',
                        padding: '2em',
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#00d042',
                        showConfirmButton: true,
                        showCancelButton: false
                    }).then(() => {
                        // Reload the page after the modal is closed
                        location.reload();
                    });
                }

                function myFunction3(id) {
                    swal.fire({
                        html: '<div style="font-weight: bold; font-size: 20px;">ยืนยันการลบวันหยุด</div><br>' +
                            '<img class="img" src="../IMG/question 1.png"></img>',
                        padding: '2em',
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#f1ba3d',
                        cancelButtonText: 'ยกเลิก',
                        cancelButtonColor: '#FF0000',
                        showCancelButton: true,
                        customClass: {
                            confirmButtonText: 'swal2-confirm',
                            cancelButtonText: 'swal2-cancel',
                        },
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteHoliday(id);
                        } else {
                            swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
                        }
                    });
                }

                function myFunction4() {
                    swal.fire({
                        html: '<div style="font-weight: bold; font-size: 20px;">ลบวันหยุดสำเร็จ</div><br>' +
                            '<img class="img" src="../IMG/check1.png"></img>',
                        padding: '2em',
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#00d042',
                        showConfirmButton: true,
                        showCancelButton: false
                    }).then(() => {
                        // Reload the page after the modal is closed
                        location.reload();
                    });
                }

                function insertHoliday(day, month, year) {
                    var formattedDate = year + '-' + (month + 1) + '-' + day;
                    var nameday = document.getElementById('nameday').value;

                    $.ajax({
                        type: "POST",
                        url: "../processing/process_dayoff_insert_holiday.php",
                        data: {
                            date: formattedDate,
                            nameday: nameday
                        },
                        success: function(response) {
                            console.log(response);
                            myFunction2();
                        },
                        error: function(error) {
                            console.log(error);
                            swal("เกิดข้อผิดพลาดในการบันทึกข้อมูล");
                        }
                    });
                }

                function deleteHoliday(id) {
                    $.ajax({
                        type: "POST",
                        url: "../processing/process_dayoff_delete_holiday.php",
                        data: {
                            id
                        },
                        success: function(response) {
                            console.log(response);
                            myFunction4();
                        },
                        error: function(error) {
                            console.log(error);
                            swal("เกิดข้อผิดพลาดในการบันทึกข้อมูล");
                        }
                    });
                }
            </script>
        </div>
    </div>
</body>
<?php include('../includes/footer.php') ?>