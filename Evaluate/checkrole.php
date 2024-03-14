<?php
session_start(); // เรียกใช้ session_start() ก่อนที่จะใช้ session

// ตรวจสอบว่ามี Session 'line_id' หรือไม่ และค่าของ 'line_id' ไม่เป็นค่าว่าง
if (isset($_SESSION['line_id']) && !empty($_SESSION['line_id'])) {
    $line_id = $_SESSION['line_id'];

    // เรียกใช้ไฟล์ config.php เพื่อเชื่อมต่อกับฐานข้อมูล SQL Server
    require_once('..\config\connection.php');

    // สร้างคำสั่ง SQL เพื่อตรวจสอบ card_id ที่ตรงกับ line_id ในตาราง test
    $sql = "SELECT li.card_id, e.prefix_thai,e.contract_type_id ,e.firstname_thai, e.lastname_thai,e.permission_id,e.cost_center_organization_id
            FROM login li
            INNER JOIN employee e ON li.card_id = e.card_id
            WHERE li.line_id = ?";
    $params = array($line_id, $line_id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if ($row) {
        // หากมีข้อมูลที่ตรงกัน
        $card_id = $row["card_id"];
        $_SESSION['card_id'] = $card_id; // เก็บค่า card_id ใน session
        $prefix = $row["prefix_thai"];
        $_SESSION['prefix_th'] = $prefix;
        $fname = $row["firstname_thai"];
        $_SESSION['firstname_thai'] = $fname;
        $lname = $row["lastname_thai"];
        $_SESSION['lastname_thai'] = $lname;
        $permission = $row['permission_id'];
        $_SESSION['permission_id'] = $permission;
        $costcenter = $row['cost_center_organization_id'];
        $_SESSION['cost_center_organization_id'] = $costcenter;
        $contract_type_id = $row['contract_type_id'];
        $_SESSION['contract_type_id'] = $contract_type_id;

            if ($permission == 4){
                header('Location: emp_main.php');
            } else if ($permission == 2){
                header('Location: boss_main.php');
            } else if ($permission == 1){
                header('Location: addmin_main.php');
            }


        echo "พบข้อมูล p_id: " . $row["card_id"];
    } else {
        // หากไม่พบข้อมูลที่ตรงกัน
        echo "ไม่พบข้อมูลที่ตรงกับ line_id: $line_id";
    }
} else {
    // หากไม่มีหรือมีค่าว่างใน Session 'line_id'
    echo "ไม่มีค่า line_id หรือค่าว่างใน Session";
}
?>