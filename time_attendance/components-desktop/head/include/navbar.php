<?php

$_SESSION['card_id'] = isset($_SESSION['card_id']) ? $_SESSION['card_id'] : '';  // ตรวจสอบว่ามีค่าหรือไม่
$_SESSION['card_id'] = trim($_SESSION['card_id']);  // ลบช่องว่างที่อาจเกิดขึ้นทั้งด้านหน้าและด้านหลังของข้อมูล
$_SESSION['card_id'] = preg_replace('/[^a-zA-Z0-9]/', '', $_SESSION['card_id']);  // ลบทุกอักขระที่ไม่ใช่ a-z, A-Z, และ 0-9
$_SESSION['card_id'] = htmlspecialchars($_SESSION['card_id'], ENT_QUOTES, 'UTF-8');  // แปลงสัญลักษณ์ HTML เพื่อป้องกัน XSS
if (!empty($_SESSION['card_id'])) {
$card_id = $_SESSION["card_id"];

$qry = "SELECT e.prefix_thai, e.firstname_thai, e.lastname_thai, e.employee_image, p.permission_id, p.name FROM employee e
LEFT JOIN permission p ON p.permission_id = e.permission_id WHERE e.card_id = ?";

// Execute the query for $navbar
$array = array($card_id);
$navbar = sqlsrv_query($conn, $qry, $array);

// Fetch and display the data for $navbar
$data = sqlsrv_fetch_array($navbar, SQLSRV_FETCH_ASSOC);
$role = $data['permission_id'];
if ($role == 1) {
$role_name = "แอดมิน";
} else if ($role == 2) {
$role_name = "หัวหน้า";
} else if ($role == 3) {
$role_name = "ผู้ตรวจสอบ";
} else {
$role_name = "พนักงาน";
}

} else {
    // echo '<script>
    //         alert("คุณไม่ได้รับอนุญาต");
    //         window.location.href = "index.html";
    //   </script>';
}

?>


<div class="header">
    <div class="header-left">
        <div class="menu-icon">
            <i class="fa-solid fa-bars"></i>
        </div>
        <div class="search-toggle-icon" data-toggle="header_search"></div>

    </div>
    <div class="header-right">
        <div class="dashboard-setting user-notification">
            <div class="dropdown">
                <button class="notification-btn">
                    <i class="fa-regular fa-bell"></i>
                </button>
            </div>
        </div>
        <div class="dashboard-setting user-notification">
            <div class="dropdown">
                <button class="notification-btn">
                    <i class="fa-solid fa-comment-dots"></i>
                </button>
            </div>
        </div>
        <div class="user-info-dropdown">
            <div class="dropdown ">
                <a class="dropdown-toggle no-arrow" href="#" role="button" data-toggle="dropdown"
                    style="text-decoration: none !important;">
                    <span class="user-icon">
                        <img src="<?php echo (!empty($data['employee_image']))
                                        ? '../../admin/uploads_img/' . $data['employee_image']
                                        : '../IMG/user.png';
                                ?>" alt="" style="width: 55px; height: 55px">
                    </span>
                    <span class="user-name">
                        <label>
                            <?php echo  'คุณ' . $data['firstname_thai'] . ' ' . $data['lastname_thai'] ?><br>
                            ตำแหน่ง : <?php echo $role_name ?>
                        </label>

                    </span>

                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="profile.php"><i class="fa-regular fa-user"
                            style=" padding-right: 5px;"><br /> </i> โปรไฟล์</a>
                    <a class="dropdown-item" href="C:\xampp\htdocs\SCG_Fairmanpower\index.php"><i
                            class="fa-solid fa-arrow-right-from-bracket" style=" padding-right: 5px;"></i>
                        ออกจากระบบ</a>
                </div>
            </div>
        </div>
    </div>
</div>