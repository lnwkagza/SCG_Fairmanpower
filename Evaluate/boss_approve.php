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
    $contract_type_id = $_SESSION['contract_type_id'];

    $permission_id = $_SESSION['permission_id'];
	if ($permission_id == 2) {

	}
	else {
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
    <title>Approve</title>
    <link rel="icon" href="../favicon.ico" type="image/png">

    <!-- Mobile Responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="css/index.css">
    <!-- <link rel="stylesheet" href="css/allmain.css"> -->
    <link rel="stylesheet" href="css/Navbar.css">
    <link rel="stylesheet" href="css/bottomnav.css">
    <link rel="stylesheet" href="css/boss_approve.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <link rel="stylesheet" type="text/css" href="../vendors/styles/core.css">
    <link rel="stylesheet" type="text/css" href="../vendors/styles/style.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/jquery-steps/jquery.steps.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/responsive.bootstrap4.min.css">


</head>

<body>
    <!-- Navbar start -->
    <?php include('../Evaluate/include/head_navbar.php') ?>
    <?php include('../Evaluate/include/head_sidebar.php') ?>
    <!-- Navbar end -->

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-30">
            <div class="profile-tab  pt-10">
                <div class="tab height-50-p">
                    <div class="nav nav-tabs customtab" role="tablist">
                        <a class="nav-link" href='boss_main.php'><img src="img/review.png" width="40" height="40"></a>
                        <a class="nav-link active" href='boss_approve.php'><img src="img/approve.png" width="40" height="40"></a>
                        <a class="nav-link" href='boss_detail.php'><img src="img/emp_profile.png" width="40" height="40"></a>
                    </div>
                </div>
            </div>
            <div class="pd-20 card-box">

                <div class="search_allapprove">
                    <form action="" method="GET">
                        <div class="searchbar">
                            <div class="inputsearch">
                                <div class="section">
                                    <span class="section">พิจารณาอนุมัติ</span>
                                    <input class="input1" type="text" name="search" placeholder="Search..." autocomplete="off">
                                    <button class="btn-search"><img src="img/search.png" class="img-search"></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <br>
                <form class="form_insert" action="insert_all.php" method="post">
                    <div class="btn-approve">
                        <button class="btn-doassessment" type="submit" name="save">ยอมรับทั้งหมดที่เลือก</button>
                    </div>

                    <table id="table_update">

                </form>
                <table class="multiple-select-row table stripe hover nowrap">
                    <thead>
                        <tr>
                            <th>
                                <div class="selectall">
                                    <input type="checkbox" id="checkAll" class="checkbox" onclick="selectAllCheckboxes()">
                                </div>
                            </th>
                            <th>ชื่อ</th>
                            <th>ชื่อคนประเมิน</th>
                            <th>บทบาท</th>
                            <th>section</th>
                            <th>ยอมรับ</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $search_query = isset($_GET['search']) ? $_GET['search'] : '';
                        $sql = "SELECT s.name_thai,tr.review_to,tr.tr_id,tr.reviewer,tr.role,e.firstname_thai,e.lastname_thai,
                                   er.firstname_thai AS er_firstname_thai ,er.lastname_thai AS er_lastname_thai
                    FROM employee e
                    JOIN transaction_review tr ON tr.review_to = e.card_id
                    JOIN employee AS er ON tr.reviewer = er.card_id
                    JOIN manager mn ON e.card_id = mn.card_id
                    JOIN cost_center c ON e.cost_center_organization_id = c.cost_center_id
                    JOIN section s ON c.section_id = s.section_id
                    WHERE mn.manager_card_id = ? AND (tr.status IS NULL OR tr.status = 'edit')
                    AND (s.name_thai LIKE '%$search_query%' OR e.firstname_thai LIKE '%$search_query%' 
                    OR e.lastname_thai LIKE '%$search_query%' OR er.firstname_thai LIKE '%$search_query%' 
                    OR er.lastname_thai LIKE '%$search_query%' OR tr.role LIKE '%$search_query%')
                    ";
                        $params = array($card_id);
                        // ดึงข้อมูลจากฐานข้อมูล
                        $stmt = sqlsrv_query($conn, $sql, $params);

                        // ตรวจสอบการทำงานของคำสั่ง SQL
                        if ($stmt === false) {
                            die(print_r(sqlsrv_errors(), true));
                        }

                        // แสดงผลลัพธ์ในรูปแบบของตาราง HTML
                        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

                            echo "<tr>";
                            echo '<td><input class="checkbox" type="checkbox" name="myCheckbox[]" value="' . $row['tr_id'] . '"></td>';
                            echo "<td>" . $row["firstname_thai"] . " " . $row["lastname_thai"] . "</td>";
                            echo "<td>" . $row["er_firstname_thai"] . " " . $row["er_lastname_thai"] . "</td>";
                            echo "<td>" . $row["role"] . "</td>";
                            echo "<td>" . $row["name_thai"] . "</td>";
                            echo "<td>  <button class='accept-btn' onclick='updateStatus(\"approve\", " . $row['tr_id'] . ")'>
                                    <span class='checkmark'>&#10004;</span> 
                                    </button>
                   <span>&nbsp;</span>
                                    <button class='dissent-btn' onclick='openPopup(event, \"reject\", " . $row['tr_id'] . ")'>
                                        <span class='checkmark'>&#10008;</span>
                                    </button>
                             </td>";
                            echo "</tr>";
                        }
                        ?>

                        <script>
                            function updateStatus(status, trId) {
                                var xhr = new XMLHttpRequest();
                                var url = 'update_status.php'; // ตั้งชื่อไฟล์ PHP ที่จะใช้ในการอัปเดตฐานข้อมูล

                                xhr.open('POST', url, true);
                                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                                xhr.onreadystatechange = function() {
                                    if (xhr.readyState === XMLHttpRequest.DONE) {
                                        if (xhr.status === 200) {
                                            // ส่วนที่คุณสามารถจัดการตอนที่รับค่า response จากฝั่งเซิร์ฟเวอร์ได้
                                            console.log('Status updated successfully!');
                                            window.location.reload(); // หรือ location.reload();
                                        } else {
                                            console.error('Error occurred: ' + xhr.status);
                                        }
                                    }
                                };

                                var params = 'status=' + encodeURIComponent(status) + '&tr_id=' + encodeURIComponent(trId);
                                xhr.send(params);
                            }

                            function openPopup(event, status, trId) {
                                // ป้องกันการทำงานของ form
                                event.preventDefault();

                                // SweetAlert2 Popup
                                Swal.fire({
                                    title: 'เหตุผลที่ปฏิเสธ',
                                    input: 'text',
                                    showCancelButton: true,
                                    confirmButtonText: 'Save',
                                    showLoaderOnConfirm: true,
                                    preConfirm: (text) => {
                                        // ใช้ AJAX เพื่อส่งข้อความไปบันทึกในฐานข้อมูล
                                        return fetch('update_reject_status.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/x-www-form-urlencoded',
                                                },
                                                body: 'text=' + encodeURIComponent(text) + '&status=' + encodeURIComponent(status) + '&tr_id=' + encodeURIComponent(trId),
                                            })
                                            .then(response => {
                                                if (!response.ok) {
                                                    throw new Error('Server error');
                                                }
                                                return response.json();
                                            })
                                            .catch(error => {
                                                Swal.showValidationMessage(`Request failed: ${error}`);
                                            });
                                    },
                                    allowOutsideClick: () => !Swal.isLoading(),
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        Swal.fire({
                                            title: 'Success!',
                                            text: 'ระบบทำการบันทึกข้อมูลเรียบร้อย',
                                            icon: 'success',
                                        }).then(() => {
                                            location.reload(); // รีโหลดหน้า
                                        });
                                    }

                                });
                            }

                            function selectAllCheckboxes() {
                                // หาทุก input element ที่มี type เป็น checkbox ในตาราง
                                const checkboxes = document.querySelectorAll('table input[type="checkbox"]');

                                // วนลูปเพื่อเลือกทุก checkbox
                                checkboxes.forEach(checkbox => {
                                    checkbox.checked = true;
                                });
                            }
                        </script>
                    </tbody>
                </table>
            </div>

            <script src="js/script.js"></script>
        </div>

        <!-- main end -->
        <!-- bottom Nav start -->
        <!-- <div class="bottom-navigation" id="bottomNav">
            <button onclick="window.location.href = 'boss_main.php'" class="nav-item button-48"><img src="img/review.png" class="icon"> </button>
            <button onclick="window.location.href = 'boss_approve.php'" class="nav-item button-48 active"><img src="img/approve.png" class="icon"> </button>
            <button onclick="window.location.href = 'boss_detail.php'" class="nav-item button-48"><img src="img/emp_profile.png" class="icon"></button>
        </div> -->


        <!-- bottom Nav end -->
    </div>
    <?php include('../admin/include/scripts.php') ?>

</body>

</html>