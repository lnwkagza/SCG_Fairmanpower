<?php
session_start(); // เรียกใช้ session_start() ก่อนที่จะใช้ session

require_once('..\config\connection.php');

// ตรวจสอบว่ามี Session 'line_id' หรือไม่ และค่าของ 'line_id' ไม่เป็นค่าว่าง
if (
    isset($_SESSION['line_id'], $_SESSION['card_id'], $_SESSION['prefix_thai'], $_SESSION['firstname_thai'], $_SESSION['lastname_thai'], $_SESSION['permission_id']) &&
    !empty($_SESSION['line_id']) && !empty($_SESSION['card_id']) && !empty($_SESSION['prefix_thai']) &&
    !empty($_SESSION['firstname_thai']) && !empty($_SESSION['lastname_thai'])
) {
    $line_id = $_SESSION['line_id'];
    $card_id = $_SESSION['card_id'];
    $prefix = $_SESSION['prefix_thai'];
    $fname = $_SESSION['firstname_thai'];
    $lname = $_SESSION['lastname_thai'];
    $costcenter = $_SESSION['cost_center_organization_id'];

    $permission_id = $_SESSION['permission_id'];
    if ($permission_id == 1) {
    } else {
        header('location: ../checkrole.php');
    }

    // ส่วนคำสั่ง SQL ควรตรงกับโครงสร้างของตารางในฐานข้อมูล
    $e_sql = "SELECT *,
        permission.name as permission, permission.permission_id as permissionID, contract_type.name_eng as contracts, contract_type.name_thai as contract_th,
        section.name_thai as section, department.name_thai as department 
        
        FROM employee
        INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id
        INNER JOIN section ON section.section_id = cost_center.section_id
        INNER JOIN department ON department.department_id = section.department_id
        INNER JOIN permission ON permission.permission_id = employee.permission_id
        INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id WHERE employee.card_id = ?";

    $params = array($card_id);
    $stmt = sqlsrv_query($conn, $e_sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $e_row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ReviewModule</title>
    <link rel="icon" href="../favicon.ico" type="image/png">

    <!-- Mobile Responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- <link rel="stylesheet" href="css/index.css"> -->
    <!-- <link rel="stylesheet" href="css/allmain.css"> -->
    <!-- <link rel="stylesheet" href="css/boss_approve.css">
    <link rel="stylesheet" href="css/Navbar.css">
    <link rel="stylesheet" href="css/bottomnav.css"> -->

    <link rel="stylesheet" type="text/css" href="../vendors/styles/core.css">
    <link rel="stylesheet" type="text/css" href="../vendors/styles/style.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/jquery-steps/jquery.steps.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/responsive.bootstrap4.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>
    <!-- Navbar start -->
    <?php include('../Evaluate/include/admin_navbar.php') ?>
    <?php include('../Evaluate/include/admin_sidebar.php') ?>
    <!-- Navbar end -->

    <div class="main-container">
        <div class="pd-30">

            <div class="row">
                <div class="col-xl-3 col-lg-2 col-md-6 mb-10 title pb-20">
                    <h2 class="h3 mb-0 "><i class="fa-solid fa-clipboard fa-lg"></i> แบบประเมินทั้งหมด</h2>
                </div>
                <div class="col-xl-9 col-lg-2 col-md-6 mb-10">
                    <div class="text-right">
                        <button onclick="window.location.href = 'addquiz.php'" class='btn btn-primary'>เพิ่มแบบประเมิน</button>
                    </div>
                </div>
            </div>

            <!-- main start -->
            <div class="card-box pd-20 height-100-p mb-30">
                <table class="data-table table stripe hover nowrap">
                    <thead>
                        <tr>
                            <th>ชื่อแบบประเมิน</th>
                            <th>ประเภทพนักงาน</th>
                            <th>วันที่เริ่มประเมิน</th>
                            <th>วันที่จบประเมิน</th>
                            <th class="datatable-nosort">แก้ไข/ลบ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // เตรียมคำสั่ง SQL
                        $sql = "SELECT * FROM assessment asm
                            INNER JOIN contract_type ctt ON ctt.contract_type_id = asm.contract_type_id
                        ";
                        // ดึงข้อมูลจากฐานข้อมูล
                        $stmt = sqlsrv_query($conn, $sql);

                        // ตรวจสอบการทำงานของคำสั่ง SQL
                        if ($stmt === false) {
                            die(print_r(sqlsrv_errors(), true));
                        }
                        // แสดงผลลัพธ์ในรูปแบบของตาราง HTML
                        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

                            $time_start  = $row["date_start"]; // สร้างวัตถุ DateTime
                            $formattedDateStart = $time_start->format('Y-m-d');

                            $time_end  = $row["date_end"]; // สร้างวัตถุ DateTime
                            $formattedDateEnd =  $time_end->format('Y-m-d');
                            echo "<tr>";
                            echo "<td>" . $row["name"] . "</td>";
                            echo "<td>" . $row["name_thai"] . "</td>";
                            echo "<td>" . $formattedDateStart . "</td>";
                            echo "<td>"  . $formattedDateEnd .  "</td>";
                            echo "<td ><button class='edit-btn_Org' onclick='editAssessment( " . $row['assessment_id'] . ")'><span class='checkmark'>✎</span></button>                    
                        <button id='deletebtn' style='display :none;' onclick='deleteRecord( " . $row['assessment_id'] . ")'>
                             </button> 
                        <button class='delete-btn_Org' onclick='showSweetAlert()'>
                        <span class='checkmark'>&#10008;</span>
                    </button> </td>";

                            echo "</tr>";
                        }

                        ?>
                    </tbody>
                </table>
            </div>
            <script>
                // SweetAlert2 Popup
                function showSweetAlert() {
                    console.log('showSweetAlert function is called'); // แสดงข้อความใน Console
                    const swalWithBootstrapButtons = Swal.mixin({
                        customClass: {
                            confirmButton: "green-swal",
                            cancelButton: "delete-swal"
                        },
                        buttonsStyling: false
                    });
                    swalWithBootstrapButtons.fire({
                        title: 'คำเตือน',
                        text: 'ถ้าคุณดำเนินการแล้วจะไม่สามารถแก้ไขได้ ต้องการดำเนินการต่อหรือไม่?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'ตกลง',
                        cancelButtonText: 'ยกเลิก'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // ทำงานเมื่อผู้ใช้คลิก "ตกลง"
                            swalWithBootstrapButtons.fire('ดำเนินการสำเร็จ!', '', 'success').then(() => {
                                // ส่ง form ไปยังหน้าปลายทาง
                                document.getElementById('deletebtn').click(); // กำหนดค่าคลิกปุ่ม
                            });
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            // ทำงานเมื่อผู้ใช้คลิก "ยกเลิก"
                            swalWithBootstrapButtons.fire('การดำเนินการถูกยกเลิก', '', 'error')
                        }
                    });
                }

                function deleteRecord(assessmentId) {
                    // ส่ง request ไปยัง server เพื่อทำการลบข้อมูล
                    var xhr = new XMLHttpRequest();

                    // กำหนด method และ url ที่จะส่ง request ไป
                    xhr.open('POST', 'delete_assessment.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                    // กำหนด callback function สำหรับการตอบสนองจาก server
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            // ทำตามขั้นตอนที่คุณต้องการหลังจากลบข้อมูลสำเร็จ
                            alert('Record deleted successfully!');
                            // ตัวอย่าง: รีโหลดหน้าหลังจากลบข้อมูล
                            window.location.reload();
                        }
                    };

                    // ส่ง request พร้อมกับข้อมูลที่ต้องการส่งไปยัง server
                    xhr.send('action=delete&assessment_id=' + assessmentId);
                }

                // ฟังขั่นส่งค่า tr_id ไปหน้า do_assessment.php
                function editAssessment(assessment_id) {
                    window.location.href = 'edit_assessment.php?id=' + assessment_id;
                }

            </script>
            <div class="profile-tab  pt-10">
                <div class="tab height-50-p">
                    <div class="nav nav-tabs customtab" role="tablist">
                        <a class="nav-link active" href='addmin_main.php'><img src="../asset/img/evaluate/pencil.png" width="60" height="60"></a>
                        <a class="nav-link" href='KPI.php'><img src="../asset/img/evaluate/kpi.png" width="60" height="60"></a>
                    </div>
                </div>
            </div>

            <script src="js/script.js"></script>
            <!-- bottom Nav end -->

        </div>
        <?php include('../admin/include/footer.php') ?>
    </div>
    <?php include('../admin/include/scripts.php') ?>

</body>

</html>