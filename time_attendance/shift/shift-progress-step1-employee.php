<?php
session_start();
session_regenerate_id(true);
//---------------------------------------------------------------------------------------
header("Cache-Control: no-cache, must-revalidate");
include ("../database/connectdb.php");
include ("../components-desktop/employee/include/header.php")
    //---------------------------------------------------------------------------------------
    ?>

<!-- CSS Desktop -->
<link rel="stylesheet" href="../components-desktop/employee/shift-manage-table-employee.css">

<!-- CSS Mobile-->
<link rel="stylesheet" href="../assets/css/shift-progress.css">
<link rel="stylesheet" href="../assets/css/shift-manage-table.css">
<link rel=" stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">

<link rel="stylesheet" href="../assets/css/loader.css">
<script src="../assets/script/loader-normal.js"></script>

<!-- datatables -->
<link href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.1/js/dataTables.bootstrap5.min.js"></script>

<?php
// ทำความสะอาดและป้องกันข้อมูล
$_SESSION['card_id'] = isset ($_SESSION['card_id']) ? $_SESSION['card_id'] : '';  // ตรวจสอบว่ามีค่าหรือไม่
$_SESSION['card_id'] = trim($_SESSION['card_id']);  // ลบช่องว่างที่อาจเกิดขึ้นทั้งด้านหน้าและด้านหลังของข้อมูล
$_SESSION['card_id'] = preg_replace('/[^a-zA-Z0-9]/', '', $_SESSION['card_id']);  // ลบทุกอักขระที่ไม่ใช่ a-z, A-Z, และ 0-9
$_SESSION['card_id'] = htmlspecialchars($_SESSION['card_id'], ENT_QUOTES, 'UTF-8');  // แปลงสัญลักษณ์ HTML เพื่อป้องกัน XSS

// ตรวจสอบว่า $_SESSION["card_id"] มีค่าหรือไม่
if (isset ($_SESSION["card_id"])) {

    // -----------------------------------------------------------------------------------------------------------------------------------------------
    $section = "SELECT employee.cost_center_organization_id, cost_center.cost_center_code, section.name_eng
    FROM employee
    INNER JOIN cost_center ON employee.cost_center_organization_id = cost_center.cost_center_id
    INNER JOIN section ON cost_center.section_id = section.section_id WHERE card_id = ?";
    $params2 = array($_SESSION['card_id']);
    $shiftsection = sqlsrv_query($conn, $section, $params2);
    $row2 = sqlsrv_fetch_array($shiftsection, SQLSRV_FETCH_ASSOC);

    // -----------------------------------------------------------------------------------------------------------------------------------------------
    $query = "
SELECT
    employee.card_id AS card_id,
    employee.prefix_thai AS prefix_thai,
    employee.scg_employee_id AS scg_employee_id,
    employee.firstname_thai AS firstname_thai,
    employee.lastname_thai AS lastname_thai,
    employee.employee_email AS employee_email,
    employee.employee_image AS employee_image,
    sub_team.name AS sub_team,
    position.name_thai AS position_name
FROM
	employee
LEFT JOIN
    position_info ON position_info.card_id = employee.card_id
LEFT JOIN
    position ON position.position_id = position_info.position_id
LEFT JOIN
    sub_team ON sub_team.sub_team_id = employee.sub_team_id
WHERE
	employee.card_id = ? 
	OR cost_center_organization_id IN (
	SELECT
		cost_center_organization_id 
	FROM
		employee
WHERE
	card_id = ?)";
    $params = array($_SESSION['card_id'], $_SESSION['card_id']);
    $shiftteam = sqlsrv_query($conn, $query, $params);
    $shiftteamdata = array();
    while ($row = sqlsrv_fetch_array($shiftteam, SQLSRV_FETCH_ASSOC)) {
        $shiftteamdata[] = $row;
    }
    sqlsrv_free_stmt($shiftteam);

    // -----------------------------------------------------------------------------------------------------------------------------------------------
    $additionalShiftTeamQuery = "SELECT card_id, scg_employee_id, prefix_thai, firstname_thai, lastname_thai FROM employee WHERE cost_center_organization_id = ? AND sub_team_id IS NULL ";
    $paramsAdditionalShiftTeam = array($row2["cost_center_organization_id"]);
    $shiftteamAdditional = sqlsrv_query($conn, $additionalShiftTeamQuery, $paramsAdditionalShiftTeam);
    $shiftteamuser = array();
    while ($row = sqlsrv_fetch_array($shiftteamAdditional, SQLSRV_FETCH_ASSOC)) {
        $shiftteamuser[] = $row;
    }
    sqlsrv_free_stmt($shiftteamAdditional);

    // -----------------------------------------------------------------------------------------------------------------------------------------------
    $sub_team = "SELECT sub_team_id, name, cost_center_id FROM sub_team WHERE cost_center_id = ?";
    $params_sub_team = array($row2["cost_center_organization_id"]);
    $sub_team_query = sqlsrv_query($conn, $sub_team, $params_sub_team);
    $sub_team_data = array();
    while ($row3 = sqlsrv_fetch_array($sub_team_query, SQLSRV_FETCH_ASSOC)) {
        $sub_team_data[] = $row3;
    }
    sqlsrv_free_stmt($sub_team_query);

    // -----------------------------------------------------------------------------------------------------------------------------------------------

    $sub_team_all = "
SELECT
	sub_team.name,
	e.card_id,
	scg_employee_id,
    e.prefix_thai AS prefix,
	e.firstname_thai AS firstname,
	e.lastname_thai AS lastname,
    position.name_thai AS position
FROM
	employee e
	INNER JOIN sub_team ON e.sub_team_id = sub_team.sub_team_id 
    INNER JOIN position_info ON position_info.card_id = e.card_id
    INNER JOIN position ON position.position_id = position_info.position_id
WHERE
	cost_center_id = (
	SELECT
		cost_center_organization_id 
	FROM
		employee 
WHERE
	card_id =  ? )";
    $params_sub_team_all = array($_SESSION['card_id']);
    $sub_team_all_query = sqlsrv_query($conn, $sub_team_all, $params_sub_team_all);
    $sub_team_all_data = array();
    while ($row3 = sqlsrv_fetch_array($sub_team_all_query, SQLSRV_FETCH_ASSOC)) {
        $sub_team_all_data[] = $row3;
    }
    sqlsrv_free_stmt($sub_team_all_query);

    //--------------------------------------------------------------------------------------------------------------------------------------------------
    $query = "SELECT manager_card_id,firstname_thai,lastname_thai FROM manager INNER JOIN employee ON manager.manager_card_id = employee.card_id WHERE manager.card_id = ? ";
    $params = array($_SESSION['card_id']);
    $stmt = sqlsrv_query($conn, $query, $params);
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    $approver_firstname = $row['firstname_thai'];
    $approver_lastname = $row['lastname_thai'];
    $approver_card_id = $row['manager_card_id'];
    $fullname_approver = $approver_firstname . ' ' . $approver_lastname;

    // -----------------------------------------------------------------------------------------------------------------------------------------------
    $SELECTapprover = "SELECT card_id, firstname_thai, lastname_thai FROM employee WHERE permission_id = ? and card_id != ?";
    $paramsapprover = array('2', $approver_card_id);
    $stmtapprover = sqlsrv_query($conn, $SELECTapprover, $paramsapprover);
} else {
    // กรณี $_SESSION["card_id"] ไม่มีค่า
    echo "No card_id in session.";
}

?>
<!-- --swal popup-- -->
<script type="text/javascript">
    function mobile_add_team() {
        Swal.fire({
            html: '<div class="topic">กำหนดชื่อทีมที่ต้องการเพิ่ม</div>' +
                '<div class="dateFix"><input id="nameteam" type="text" style="padding: 2.5vw;"></div>',
            padding: '2em',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#29ab29',
            cancelButtonColor: '#e1574b',
            showCancelButton: true,
            customClass: {
                confirmButtonText: 'swal2-confirm',
                cancelButtonText: 'swal2-cancel',
                container: 'custom-swal-container',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                var nameteamValue = document.getElementById('nameteam').value;
                mobile_add_team_submit(nameteamValue);
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });
    }

    function mobile_add_team_submit(nameteamValue) {
        Swal.fire({
            html: '<div style="font-weight: bold; font-size: 4vw;">ยืนยันการกำหนดทีมหรือไม่</div><br>' +
                '<img class="img" src="../IMG/question 1.png"></img>',
            padding: '2em',
            confirmButtonText: 'ตกลง',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#29ab29',
            cancelButtonColor: '#e1574b',
            showCancelButton: true,

            customClass: {
                confirmButtonText: 'swal2-confirm',
                cancelButtonText: 'swal2-cancel',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                TeamAdd(nameteamValue);
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });
    }

    function mobile_edit_team_confirm() {
        Swal.fire({
            html: '<div style="font-weight: bold; font-size: 4vw; !impotant">ยืนยันการแก้ไขทีมหรือไม่</div><br>' +
                '<img class="img" src="../IMG/question 1.png"></img>',
            padding: '2em',
            confirmButtonText: 'ตกลง',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#29ab29',
            cancelButtonColor: '#e1574b',
            showCancelButton: true,

            customClass: {
                confirmButtonText: 'swal2-confirm',
                cancelButtonText: 'swal2-cancel',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                var nameteamValue = document.getElementById('nameteam').value;
                mobile_edit_team_submit(nameteamValue)
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });
    }

    function myFunction4() {
        Swal.fire({
            html: '<div style="font-weight: bold; font-size: 4vw;">เพิ่มทีมสำเร็จ</div><br>' +
                '<img class="img src="../IMG/check1.png"></img>',
            padding: '2em',
            showConfirmButton: true, // ไม่แสดงปุ่มตกลง
            showCancelButton: false // ไม่แสดงปุ่มยกเลิก
        }).then((result) => {
            if (result.isConfirmed) { } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });

    }

    function myFunction5() {
        Swal.fire({
            html: '<div style="font-weight: bold; font-size: 4vw;">แก้ไขทีมสำเร็จ</div><br>' +
                '<img class="img" src="../IMG/check1.png"></img>',
            padding: '2em',
            confirmButtonText: 'ยืนยัน',
            confirmButtonColor: '#00d042',
            showCancelButton: false // ไม่แสดงปุ่มยกเลิก
        }).then((result) => {
            if (result.isConfirmed) {
                AddTeamEmployee();
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });

    }
    //desktop Script
    function add_team_member_submit() {
        Swal.fire({
            title: "<strong>ยืนยันการเพิ่มพนักงานในทีมหรือไม่</strong>",
            icon: "question",
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: `ตกลง`,
            cancelButtonText: `ยกเลิก`,
        }).then((result) => {
            if (result.isConfirmed) {
                add_team_member_confirm()
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });
    }

    function add_team_member_confirm() {
        Swal.fire({
            title: "<strong>เพิ่มพนักงานในทีมสำเร็จ</strong>",
            icon: "success",
            showCloseButton: true,
            focusConfirm: false,
            confirmButtonText: `ตกลง`,
        }).then((result) => {
            if (result.isConfirmed) {
                add_team_member();
            }
        });
    }

    function add_team_submit() {
        var team_name = document.getElementById('desktop-team-name').value;
        Swal.fire({
            title: "<strong>ยืนยันการเพิ่มทีมหรือไม่</strong>",
            icon: "question",
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: `ตกลง`,
            cancelButtonText: `ยกเลิก`,
        }).then((result) => {
            if (result.isConfirmed) {
                add_team_confirm(team_name);
            } else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });
    }

    function add_team_confirm(team_name) {
        Swal.fire({
            title: "<strong>เพิ่มทีมสำเร็จ</strong>",
            icon: "success",
            showCloseButton: true,
            focusConfirm: false,
            confirmButtonText: `ตกลง`,
        }).then((result) => {
            if (result.isConfirmed) {
                add_team(team_name);
            }
        });
    }
</script>

<!-- --data table-- -->
<!-- --เปลี่ยนภาษาอังกฤษของ data table-- -->
<script>
    $(document).ready(function () {
        //data-table
        new DataTable('#example', {
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
            },
            "pageLength": 5,
            "lengthChange": false

        });
    });

    //desktop
    $(document).ready(function () {
        //mange-team
        new DataTable('#table1', {
            "autowidth": false,
            "lengthMenu": [
                [5, 25, 50, -1],
                [5, 25, 50, "ทั้งหมด"]
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

<!----แสดงและซ่อนการแก้ไขรูปแบบการทำงาน---->

<script>
    function toggleEditWorkEdit() {
        var editWorkSection = document.querySelector('.display-editWork');
        if (editWorkSection.style.display === 'none' || editWorkSection.style.display === '') {
            editWorkSection.style.display = 'block';
        } else {
            editWorkSection.style.display = 'none';
        }
    }
</script>

<!-- desktop script -->
<script>
    function show_edit() {
        const add = document.getElementById("add-team");
        const edit = document.getElementById("edit-team");
        const member = document.getElementById("team-member");
        member.style.display = "none";
        add.style.display = "none";
        edit.style.display = "";
    }

    function close_edit() {
        const add = document.getElementById("add-team");
        const edit = document.getElementById("edit-team");
        const member = document.getElementById("team-member");
        if (add.style.display === "") {
            member.style.display = "none";
            edit.style.display = "none";
        } else {
            member.style.display = "";
            edit.style.display = "none";
        }
    }

    function show_add() {
        const add = document.getElementById("add-team");
        const edit = document.getElementById("edit-team");
        const member = document.getElementById("team-member");
        member.style.display = "none";
        edit.style.display = "none";
        add.style.display = "";
    }

    function close_add() {
        const add = document.getElementById("add-team");
        const edit = document.getElementById("edit-team");
        const member = document.getElementById("team-member");
        if (edit.style.display === "") {
            member.style.display = "none";
            add.style.display = "none";
        } else {
            member.style.display = "";
            add.style.display = "none";
        }
    }
</script>

</head>

<body>
    <div class="desktop">
        <?php include ('../components-desktop/employee/include/sidebar.php'); ?>
        <?php include ('../components-desktop/employee/include/navbar.php'); ?>
        <div class="main-container">
            <div class="pd-ltr-20 xs-pd-20-10">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <h2>จัดการทีม</h2>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a style="pointer-events:none;cursor: default;">กะการทำงาน</a>
                                        </li>
                                        <li class=" breadcrumb-item active" aria-current="page" style="cursor:default;">
                                            จัดการทีม
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="progress-step">
                        <div class="progress-container step-1">
                            <div class="circle active"><a href="shift-progress-step1-employee.php"><span>1</span><span
                                        class="title">จัดทีม</span></a></div>
                            <div class="circle"><a href="shift-progress-step2-employee.php">
                                    <span>2</span><span class="title">แก้ไขรูปแบบ</span></a></div>
                            <div class="circle"><a href="shift-progress-step3-employee.php">
                                    <span>3</span><span class="title">ล็อกเหลี่ยม</span></a></div>
                            <div class="circle"><a href="shift-progress-step4-employee.php">
                                    <span>4</span><span class="title">สลับกะ</span></a></div>
                            <div class="circle"><a href="shift-progress-step5-employee.php">
                                    <span>5</span><span class="title">เปลี่ยนกะ</span></a></div>
                            <div class="circle"><a href="shift-progress-step6-employee.php">
                                    <span>6</span><span class="title">เพิ่มกะ</span></a></div>
                        </div>
                    </div>
                    <div class="btn-action-step">
                        <button class="button-step" id="next"
                            onclick="location.href='shift-progress-step2-employee.php'">ถัดไป</button>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-6 col-sm-12 mb-30" id="box1">
                            <div class="card-box pd-30 pt-10 height-100-p" id="card-box1">
                                <div class="pt-3 pb-3">
                                    <div class="bar">
                                        <h2>รายชื่อพนักงานในทีม</h2>
                                    </div>
                                    <div class="cost-center pt-4">
                                        <label>
                                            <?= $row2["cost_center_code"] ?> :
                                            <?= $row2["name_eng"] ?>
                                        </label>
                                    </div>
                                    <hr>
                                    <div class="desktop-table-container">
                                        <table id="table1" class="table stripe hover nowrap">
                                            <thead>
                                                <tr>
                                                    <th class="idEm">รหัสนักงาน</th>
                                                    <th class="nameEm">ชื่อ-สกุล</th>
                                                    <th>ตำแหน่ง</th>
                                                    <th>ทีมย่อย</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($shiftteamdata as $row): ?>
                                                    <tr>
                                                        <td><b>
                                                                <?= $row["scg_employee_id"] ?>
                                                            </b></td>
                                                        <td>
                                                            <div class="row">
                                                                <div style="margin-right: 5px; margin-left:5px;">
                                                                    <img src="<?php echo (!empty ($row['employee_image'])) ? '../../admin/uploads_img/' . $row['employee_image'] : '../IMG/user.png'; ?>"
                                                                        class="border-radius-100 shadow" width="40"
                                                                        height="40" alt="">
                                                                </div>
                                                                <div>
                                                                    <b>
                                                                        <?= $row["prefix_thai"] . $row["firstname_thai"] . " " . $row["lastname_thai"] ?>
                                                                    </b><br>
                                                                    <a class="text-primary">
                                                                        <?= $row["employee_email"] ?>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?= $row['position_name'] ?>
                                                        </td>
                                                        <td>
                                                            <?= $row['sub_team'] ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-6 col-sm-12 mb-30" id="box2">
                            <div class="card-box pd-30 pt-10 height-100-p" id="card-box2">
                                <div class="pt-3 pb-3">
                                    <div class="bar-2">
                                        <h2>จัดการพนักงานภายในทีม</h2>
                                        <div class="button-bar">
                                            <button class="btn btn-primary" onclick="show_add()"><i
                                                    class="fa-solid fa-circle-plus"></i>เพิ่มทีม</button>
                                            <button class="btn btn-warning" onclick="show_edit()"><i
                                                    class="fa-solid fa-pen"></i>แก้ไขทีม</button>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="team-member" id="team-member">
                                        <?php
                                        $displayedTeams = array(); // Array to keep track of displayed teams
                                        
                                        foreach ($sub_team_all_data as $Team):
                                            if (!in_array($Team['name'], $displayedTeams)):
                                                array_push($displayedTeams, $Team['name']);
                                                ?>
                                                <div>
                                                    <label>ทีม
                                                        <?= $Team["name"] ?>
                                                    </label>
                                                    <label>พนักงานในทีม:</label>
                                                    <div>
                                                        <table class="table">
                                                            <tbody>
                                                                <?php foreach ($sub_team_all_data as $row4): ?>
                                                                    <?php if ($Team['name'] == $row4['name']): ?>
                                                                        <tr>
                                                                            <td>
                                                                                <?= $row4["scg_employee_id"]; ?>
                                                                            </td>
                                                                            <td>
                                                                                <?= $row4["prefix"] . $row4["firstname"] . " " . $row4["lastname"]; ?>
                                                                            </td>
                                                                            <td>
                                                                                <?= $row4["position"]; ?>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endif; ?>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            <?php endif;
                                        endforeach; ?>
                                    </div>

                                    <div class="add-team" id="add-team" style="display:none;">
                                        <button class="btn" onclick="close_add()"><i
                                                class="fa-solid fa-xmark"></i></button>
                                        <section>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>เพิ่มทีม:</label>
                                                        <input class="form-control" id="desktop-team-name">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-primary"
                                                        onclick="add_team_submit()">ยืนยัน</button>
                                                </div>
                                            </div>
                                        </section>
                                    </div>

                                    <div class="edit-team" id="edit-team" style="display:none;">
                                        <button class="btn" onclick="close_edit()"><i
                                                class="fa-solid fa-xmark"></i></button>
                                        <section>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label>เลือกทีม:</label>
                                                        <select class="custom-select form-control" name="desktop-team"
                                                            id="desktop-team" onchange="showTeam()">
                                                            <option value="" selected="true" disabled="disabled">
                                                                เลือกทีมที่ท่านต้องการ</option>
                                                            <?php foreach ($sub_team_data as $rowteam): ?>
                                                                <option value="<?= $rowteam["sub_team_id"] ?>">
                                                                    <?= $rowteam["name"] ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class=" form-group">
                                                        <label>เพิ่มพนักงาน:</label>
                                                        <select class="custom-select form-control"
                                                            id="desktop-employeeid" onchange="add_member()">
                                                            <option value="">เลือกสมาชิกในทีม</option>
                                                            <?php foreach ($shiftteamuser as $rowteam): ?>
                                                                <option value="<?= $rowteam["card_id"] ?>">
                                                                    <?= $rowteam["scg_employee_id"] ?>
                                                                    <?= $rowteam["prefix_thai"] . $rowteam["firstname_thai"] ?>
                                                                    <?= $rowteam["lastname_thai"] ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <div class="label-table">
                                                            <label>พนักงานในทีม:</label>
                                                            <div id="table-edit" class="table-edit"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class=" form-group">
                                                        <label>ผู้ตรวจสอบ (ถ้ามี):</label>
                                                        <select class="custom-select form-control"
                                                            name="desktop-inspector" id="desktop-inspector">
                                                            <option value="">เลือกผู้ตรวจสอบ</option>
                                                            <?php
                                                            while ($rs_emp = sqlsrv_fetch_array($stmtapprover, SQLSRV_FETCH_ASSOC)) {
                                                                echo '<option value="' . $rs_emp['card_id'] . '">' . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai'] . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class=" form-group">
                                                        <label>หัวหน้า:</label>
                                                        <input class="form-control" type="text" name="" id=""
                                                            value="<?php echo $fullname_approver; ?> " readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-primary"
                                                        onclick="add_team_member_submit()">ยืนยัน</button>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class=" mobile">
        <div class="navbar-BS">
            <div class="div-span">
                <span>จัดการทีม</span>
            </div>
        </div>
        <div class="container">
            <!-- --ส่วนของ progress bar-- -->
            <div class="container-progress">
                <div class="progress-container step-1">
                    <div class="circle active"><a href="shift-progress-step1-employee.php"><span>1</span><span
                                class="title">จัดทีม</span></a></div>
                    <div class="circle"><a href="shift-progress-step2-employee.php">
                            <span>2</span><span class="title">แก้ไขรูปแบบ</span></a>
                    </div>
                    <div class="circle"><a href="shift-progress-step3-employee.php">
                            <span>3</span><span class="title">ล็อกเหลี่ยม</span></a>
                    </div>
                    <div class="circle"><a href="shift-progress-step4-employee.php">
                            <span>4</span><span class="title">สลับกะ</span></a></div>
                    <div class="circle"><a href="shift-progress-step5-employee.php">
                            <span>5</span><span class="title">เปลี่ยนกะ</span></a></div>
                    <div class="circle"><a href="shift-progress-step6-employee.php">
                            <span>6</span><span class="title">เพิ่มกะ</span></a></div>
                </div>
            </div>
            <!-- --ส่วนของการจัดทีม-- -->
            <div class="container-manageTable">
                <div class="box-add-team">
                    <span class="topic">รายชื่อพนักงานในทีม</span>
                    <div class="display-nameEm">
                        <span class="cost-center">
                            <?= $row2["cost_center_code"] ?> :
                            <?= $row2["name_eng"] ?>
                        </span>
                        <div class="display-transaction">
                            <table id="example" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="idEm">รหัสนักงาน</th>
                                        <th class="nameEm">ชื่อ-สกุล</th>
                                        <th>ทีมย่อย</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($shiftteamdata as $row): ?>
                                        <tr>
                                            <td class="id">
                                                <?= $row["scg_employee_id"] ?>
                                            </td>
                                            <td class="name">
                                                <?= $row["firstname_thai"] ?>
                                                <?= $row["lastname_thai"] ?>
                                            </td>
                                            <td class="detail" onclick="showFullText('<?php echo $row['sub_team']; ?>')">
                                                <?php echo mb_strimwidth($row['sub_team'], 0, 10, '...'); ?>
                                                <span class="popup-text">
                                                    <?php echo $row['sub_team']; ?>
                                                </span>
                                            </td>

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
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="select-emTeam">
                    <span class="topic">จัดการพนักงานภายในทีม</span>
                    <div class="select-detail">
                        <div class="btn-action">
                            <div class="addTeam">
                                <button onclick="mobile_add_team()"><img src="../IMG/add3.png"
                                        alt="">&nbsp;เพิ่มทีม</button>
                            </div>
                            <div class="editTeam">
                                <button onclick="toggleEditWorkEdit()"><img src="../IMG/add3.png"
                                        alt="">&nbsp;แก้ไขทีม</button>
                            </div>
                        </div>
                        <div class="display-editWork">
                            <div class="selectTeam">
                                <span>เลือกทีม : </span>
                                <select name="Team" id="Team" onchange="showTeam()">
                                    <option value="">
                                        เลือกทีมที่ท่านต้องการ</option>
                                    <?php foreach ($sub_team_data as $rowteam): ?>
                                        <option value="<?= $rowteam["sub_team_id"] ?>">
                                            <?= $rowteam["name"] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="select-em">
                                <span>เพิ่มพนักงาน : </span>
                                <select id="addemployee" onchange="showEmployeeDayOff()">
                                    <option value="">เลือกสมาชิกในทีม
                                    </option>
                                    <?php foreach ($shiftteamuser as $rowteam): ?>
                                        <option value="<?= $rowteam["card_id"] ?>">
                                            <?= $rowteam["scg_employee_id"] ?>
                                            <?= $rowteam["firstname_thai"] ?>
                                            <?= $rowteam["lastname_thai"] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="detail-select-total">
                                <span>ทีม : </span>
                                <span>พนักงานในทีม : </span>
                                <div class="display-name">
                                    <span id="team_edit_employee"></span>
                                </div>
                            </div>
                            <div class="select-inspector">
                                <span>ผู้ตรวจสอบ (ถ้ามี) : </span>
                                <select name="inspector" id="inspector" class="js-example-basic-single">
                                    <option value="">เลือกผู้ตรวจสอบ
                                    </option>
                                    <?php
                                    while ($rs_emp = sqlsrv_fetch_array($stmtapprover, SQLSRV_FETCH_ASSOC)) {
                                        echo '<option value="' . $rs_emp['card_id'] . '">' . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="display-head">
                                <span>หัวหน้า : </span>
                                <div class="head">
                                    <input class="form-control-head" type="text" name="" id=""
                                        value="<?php echo $fullname_approver; ?> " readonly>
                                </div>

                            </div>
                            <div class="row-btn">
                                <div class="col-6">
                                    <input type="submit" value="ยืนยัน" onclick="mobile_edit_team_confirm()"
                                        class="btnConfirm">
                                </div>
                            </div>
                        </div>

                        <div class="display-allTeam">
                            <?php
                            $displayedTeams = array(); // Array to keep track of displayed teams
                            
                            foreach ($sub_team_all_data as $Team):
                                if (!in_array($Team['name'], $displayedTeams)):
                                    array_push($displayedTeams, $Team['name']);
                                    ?>
                                    <div class="detail-select-total-all">
                                        <span>ทีม :
                                            <?= $Team["name"] ?>
                                        </span>
                                        <span>พนักงานในทีม :</span>
                                        <div class="display-name-all">
                                            <?php foreach ($sub_team_all_data as $row4): ?>
                                                <?php if ($Team['name'] == $row4['name']): ?>
                                                    <span>
                                                        <?php echo $row4["scg_employee_id"]; ?>
                                                        -
                                                        <?php echo $row4["firstname"]; ?>
                                                        <?php echo $row4["lastname"]; ?>
                                                    </span>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif;
                            endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- --ปุ่ม action เลื่อนไปข้างหน้า เลื่อนไปข้างหลัง-- -->
            <div class="btn-action-step">
                <button class="btn" id="prev" disabled>Prev</button>
                <button class="btn" id="next" onclick="location.href='shift-progress-step2-employee.php'">Next</button>

            </div>
        </div>
    </div>
</body>
<?php include ('../includes/footer.php') ?>

<script>
    function addTeam() {
        var addTeamMoreDiv = document.querySelector('.add-team-more');
        addTeamMoreDiv.style.display = 'block';
    }

    function addTeam2() {
        var addTeamMoreDiv = document.querySelector('.add-team-more2');
        addTeamMoreDiv.style.display = 'block';
    }

    var idEditEmployeeShow = document.getElementById("id_edit_employee_show");
    var teamEditEmployee = document.getElementById("team_edit_employee");
    var employeeElementIdadd;

    function showEmployeeDayOff() {
        var employeeId = document.getElementById("addemployee").value;

        <?php foreach ($shiftteamdata as $row2): ?>
            if ("<?= $row2["card_id"] ?>" === employeeId) {
                var employeeElementId = <?= $row2["card_id"] ?>;
                teamEditEmployee.innerHTML += "<div id='" + employeeElementId + "'>" +
                    "<img src='../IMG/bin.png' alt='ลบ' onclick='deleteEmployee(" + <?= $row2["card_id"] ?> + ")'>" +
                    " <?= $row2["scg_employee_id"] ?>                     <?= $row2["firstname_thai"] ?>                     <?= $row2["lastname_thai"] ?> <br>" +
                    "</div>";
            }
        <?php endforeach; ?>
    }

    function deleteEmployee(employeeId) {
        var employeeElement = document.getElementById(employeeId);

        if (employeeElement) {
            employeeElement.remove();
            idEditEmployeeShow = document.getElementById("id_edit_employee_show");
            if (employeeElementIdadd && typeof employeeElementIdadd === 'string' && employeeElementIdadd !== '') {
                var employeeIdsArray = employeeElementIdadd.split(",");
                var index = employeeIdsArray.indexOf(employeeId.toString());
                if (index !== -1) {
                    employeeIdsArray.splice(index, 1);
                }
                employeeElementIdadd = employeeIdsArray.join(",");
            } else {
                employeeElementIdadd = '';
            }
            console.log(employeeElementIdadd);

            if (idEditEmployeeShow) {
                idEditEmployeeShow.value = idEditEmployeeShow.value.replace("," + employeeId, "").replace(
                    employeeId + ",",
                    "");
                console.log("Updated idEditEmployeeShow:", idEditEmployeeShow.value);
            }
        }
    }

    function showTeam() {
        var Team = document.getElementById("Team").value;
        console.log(Team);

    }

    function TeamAdd(nameteamValue) {
        $.ajax({
            type: "POST",
            url: "../processing/process_shift_insert_addteam.php",
            data: {
                nameteam: nameteamValue
            },
            success: function (response) {
                console.log(response);
                location.reload();
            },
            error: function (error) {
                console.log(error);
                swal("เกิดข้อผิดพลาดในการบันทึกข้อมูล");
            }
        });
    }

    function AddTeamEmployee() {
        var Team = document.getElementById("Team").value;
        var inspector = document.getElementById("inspector").value;
        var employeeIds = employeeElementIdadd; // Using the existing employee IDs variable

        $.ajax({
            type: "POST",
            url: "../processing/process_shift_insert_addTeamEmployee.php",
            data: {
                team: Team,
                employeeIds: employeeIds,
                inspectorIds: inspector
            }, // Sending team and employee IDs data
            success: function (response) {
                console.log(response);
            },
            error: function (error) {
                console.log(error);
                swal("เกิดข้อผิดพลาดในการบันทึกข้อมูล");
            }
        });
    }

    //desktop script
    function add_member() {
        var employee = document.getElementById("desktop-employeeid").value;
        var table_edit = document.getElementById("table-edit");

        <?php foreach ($shiftteamdata as $row2): ?>
            if ("<?= $row2["card_id"] ?>" === employee) {
                var div_id = <?= $row2["card_id"] ?>;
                table_edit.innerHTML +=
                    "<div id='" + div_id + "'>" +
                    "<table class='table'>" +
                    "<tbody>" +
                    "<tr>" +
                    "<td><div class='btn btn-danger' onclick='delete_member(" + <?= $row2["card_id"] ?> + ")'>" +
                    "<i class='fa-solid fa-trash-can'></i></div></td>" +
                    "<td><?= $row2["scg_employee_id"] ?></td>" +
                    "<td><?= $row2["prefix_thai"] . $row2["firstname_thai"] . " " . $row2["lastname_thai"]; ?></td>" +
                    "<td><?= $row2["position_name"] ?></td>" +
                    "</tr>" +
                    "</tbody>" +
                    "</table>" +
                    "</div>";
            }
        <?php endforeach; ?>
    }

    function delete_member(employee) {
        var delem = document.getElementById(employee);
        if (delem) {
            delem.remove();
        }
    }

    function add_team_member() {
        var des_team = document.getElementById("desktop-team").value;
        var des_inspector = document.getElementById("desktop-inspector").value;

        var des_employeeid = [];
        $('#desktop-employeeid option:selected').each(function () {
            selectedEmployeeIds.push($(this).val());
        });

        $.ajax({
            type: "POST",
            url: "../processing/process_shift_insert_addTeamEmployee.php",
            data: {
                team: des_team,
                employeeIds: des_employeeid,
                inspectorIds: des_inspector
            }, // Sending team and employee IDs data
            success: function (response) {
                console.log(response);
            },
            error: function (error) {
                console.log(error);
                swal("เกิดข้อผิดพลาดในการบันทึกข้อมูล");
            }
        });
    }

    function add_team(team_name) {
        $.ajax({
            type: "POST",
            url: "../processing/process_shift_insert_addteam.php",
            data: {
                nameteam: team_name
            },
            success: function (response) {
                console.log(response);
                location.reload();
            },
            error: function (error) {
                console.log(error);
                swal("เกิดข้อผิดพลาดในการบันทึกข้อมูล");
            }
        });
    }
</script>