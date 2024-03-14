<?php
session_start(); // เรียกใช้ session_start() ก่อนที่จะใช้ session

// ตรวจสอบว่ามี Session 'line_id' หรือไม่ และค่าของ 'line_id' ไม่เป็นค่าว่าง
if (isset($_SESSION['line_id']) && !empty($_SESSION['line_id'])) {
    $line_id = $_SESSION['line_id'];

    // เรียกใช้ไฟล์ config.php เพื่อเชื่อมต่อกับฐานข้อมูล SQL Server
    require_once('config\connection.php');
    
    // สร้างคำสั่ง SQL เพื่อตรวจสอบ card_id ที่ตรงกับ line_id ในตาราง test
    $sql = "SELECT scg_employee_id,li.card_id, e.person_id, e.prefix_thai, e.firstname_thai, e.lastname_thai, e.permission_id ,e.cost_center_organization_id, e.contract_type_id, s.split_set_id,company.name_thai as company
    ,division.name_thai as division,department.name_thai as department,section.name_thai as section,
    cost_center.cost_center_code as cost_center,contract_type.name_thai as contract_type,pl.pl_name_thai as pl,
    position.name_thai as position
            FROM login li
            INNER JOIN employee e ON li.card_id = e.card_id
            LEFT JOIN cost_center ON cost_center.cost_center_id  = e.cost_center_organization_id
            LEFT JOIN section ON section.section_id = cost_center.section_id 
            LEFT JOIN department ON department.department_id = section.department_id 
            LEFT JOIN division ON division.division_id = department.division_id 
            LEFT JOIN location ON location.location_id = division.location_id
            LEFT JOIN company ON company.company_id = location.company_id
            LEFT JOIN contract_type ON contract_type.contract_type_id = e.contract_type_id
            LEFT JOIN pl_info ON pl_info.card_id = e.card_id
            LEFT JOIN pl ON pl.pl_id = pl_info.pl_id
            LEFT JOIN position_info ON position_info.card_id  = e.card_id
            LEFT JOIN position ON position.position_id  = position_info.position_id
            JOIN split s ON s.card_id = e.card_id
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

        // CHAKAT
        $user_id = $row["person_id"];
        $_SESSION['person_id'] = $user_id; 

        $prefix = $row["prefix_thai"];
        $_SESSION['prefix_thai'] = $prefix;

        $fname = $row["firstname_thai"];
        $_SESSION['firstname_thai'] = $fname;

        $lname = $row["lastname_thai"];
        $_SESSION['lastname_thai'] = $lname;

        $permission = $row['permission_id'];  // ประกาศ permmission เข้าถึงข้อมูล
        $_SESSION['permission_id'] = $permission;

        $costcenter = $row['cost_center_organization_id'];
        $_SESSION['cost_center_organization_id'] = $costcenter;
        
        $contract_type_id = $row['contract_type_id'];
        $_SESSION['contract_type_id'] = $contract_type_id;
        
        $prefix = $row["prefix_thai"];
        $_SESSION['prefix_thai'] = $prefix;

        $emp_split = $row["split_set_id"];
        $_SESSION['split_set_id'] = $emp_split;

        if ($permission == 1) {
            header('Location: admin/dashboard.php');
            $_SESSION['admin_login'] = $user_id; 

        } else if ($permission == 2) {
            header('Location: head/listemployee.php');
            $_SESSION['user_login'] = $user_id; 

        } else if ($permission == 3) {
            header('Location: Inspector/home.php');
            $_SESSION['user_login'] = $user_id; 

        } else if ($permission == 4) {
            header('Location: employee/home.php');
            $_SESSION['user_login'] = $user_id; 

        }
        echo "พบข้อมูล บัตรประชาชน : " . $row["card_id"];
    } else {
        // หากไม่พบข้อมูลที่ตรงกัน
        echo "ไม่พบข้อมูลที่ตรงกับ line_id: $line_id";
    }
} else {
    // หากไม่มีหรือมีค่าว่างใน Session 'line_id'
    echo "ไม่มีค่า line_id หรือค่าว่างใน Session";
}


// เช็ค ก่อน Insert 