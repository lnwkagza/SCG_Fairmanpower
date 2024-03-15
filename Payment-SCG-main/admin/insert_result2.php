<?php

require_once('..\..\config\connection.php');

session_start();

$splitId = 2;
$_SESSION['splitId'] = $splitId;

$split = '2';

// คำสั่ง SQL INSERT INTO VALUES สำหรับการเพิ่มข้อมูลในตารางใหม่
$sql_insert = "INSERT INTO log_salary (card_id, salary_per_month, income_type, income_amount, deduct_type, deduct_amount,datetime,split)
               VALUES (?, ?, ?, ?, ?, ?, GETDATE(),$split)";

// เตรียมคำสั่ง SQL
$sql = "SELECT 
            employee.card_id,
            (CASE WHEN (row_number() OVER (PARTITION BY employee.card_id ORDER BY employee.card_id) = 1) OR (row_number() OVER (PARTITION BY itt.income_type ORDER BY itt.income_type) = 1)
                        THEN itt.income_type
                        END) AS 'income_type',
            (CASE WHEN (row_number() OVER (PARTITION BY employee.card_id ORDER BY employee.card_id) = 1) OR (row_number() OVER (PARTITION BY deduct_type.deduct_type ORDER BY deduct_type.deduct_type) = 1)
                        THEN deduct_type.deduct_type
                        END) AS 'deduct_type',
            (CASE WHEN (row_number() OVER (PARTITION BY employee.card_id ORDER BY employee.card_id) = 1) OR (row_number() OVER (PARTITION BY it.amount ORDER BY it.amount) = 1)
                        THEN it.amount
                        END) AS 'itamount',
            (CASE WHEN (row_number() OVER (PARTITION BY employee.card_id ORDER BY employee.card_id) = 1) OR (row_number() OVER (PARTITION BY dt.amount ORDER BY dt.amount) = 1)
                        THEN dt.amount
                        END) AS 'dtamount',
            (CASE WHEN row_number() OVER (PARTITION BY employee.card_id ORDER BY employee.card_id) = 1
                        THEN ep.salary_per_month
                        END) AS 'salary_amount'
                                                                                        
            FROM 
            employee
            LEFT JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id
            LEFT JOIN section ON section.section_id = cost_center.section_id 
            LEFT JOIN department ON department.department_id = section.department_id 
            LEFT JOIN division ON division.division_id = department.division_id 
            LEFT JOIN location ON location.location_id = division.location_id
            LEFT JOIN company ON company.company_id = location.company_id
            LEFT JOIN position_info ON position_info.card_id = employee.card_id
            LEFT JOIN position ON position.position_id = position_info.position_id
            LEFT JOIN employee_payment ep ON ep.card_id = employee.card_id
            LEFT JOIN income_target it ON it.card_id = employee.card_id
            LEFT JOIN income_type itt ON itt.income_type_id = it.income_type_id
            LEFT JOIN deduct_target dt ON dt.card_id = employee.card_id
            LEFT JOIN deduct_type ON dt.deduct_type_id = deduct_type.deduct_type_id
            LEFT JOIN split ON split.card_id = employee.card_id 
            WHERE 
            split.split_set_id = '2' AND ( it.active = '1' OR dt.active = '1')";

// แทนค่าพารามิเตอร์ในคำสั่ง SQL
$params = array();

// ดึงข้อมูลจากฐานข้อมูล
$stmt = sqlsrv_query($conn, $sql, $params);

// ตรวจสอบการทำงานของคำสั่ง SQL
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Loop ผ่านผลลัพธ์ที่ได้จากคำสั่ง SQL SELECT
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    // Bind ข้อมูลในแต่ละคอลัมของผลลัพธ์ไปยัง parameters ในคำสั่ง SQL INSERT
    $params_insert = array(
        $row["card_id"],
        $row["salary_amount"],
        $row["income_type"],
        $row["itamount"],
        $row["deduct_type"],
        $row["dtamount"]
    );

    // Execute คำสั่ง SQL INSERT
    $stmt_insert = sqlsrv_query($conn, $sql_insert, $params_insert);

    // ตรวจสอบการทำงานของคำสั่ง SQL INSERT
    if ($stmt_insert === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // ปิด statement ของคำสั่ง SQL INSERT
    sqlsrv_free_stmt($stmt_insert);
}
// สร้างคำสั่ง SQL INSERT INTO VALUES
$sql_insert2 = "INSERT INTO log_sum_salary (card_id, total_income, total_deduct, date, split) 
                VALUES (?, ?, ?, GETDATE(), $split)";

$sql_select2 = "SELECT 
  e.card_id,
  it.total_income,
  dt.total_deduction,
  split.split_set_id
FROM 
  employee e
  LEFT JOIN (SELECT card_id, SUM(amount) AS total_income FROM income_target WHERE active = '1' GROUP BY card_id) it ON e.card_id = it.card_id
  LEFT JOIN (SELECT card_id, SUM(amount) AS total_deduction FROM deduct_target WHERE active = '1' GROUP BY card_id) dt ON e.card_id = dt.card_id
  LEFT JOIN split ON split.card_id = e.card_id 
WHERE
  split.split_set_id = '2'";

// ส่งคำสั่ง SQL ไปยังฐานข้อมูล
$stmt2 = sqlsrv_query($conn, $sql_select2);

// ตรวจสอบการทำงานของคำสั่ง SQL SELECT
if ($stmt2 === false) {
    die(print_r(sqlsrv_errors(), true));
}
// Loop ผ่านผลลัพธ์ที่ได้จากคำสั่ง SQL SELECT
while ($row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
    // สร้าง array ของ parameters เพื่อ bind ค่าให้กับคำสั่ง SQL INSERT INTO VALUES
    $params_insert2 = array(
        $row['card_id'],
        $row['total_income'],
        $row['total_deduction']
    );

    // ส่งคำสั่ง SQL INSERT INTO VALUES พร้อมกับ parameters ไปยังฐานข้อมูล
    $stmt_insert2 = sqlsrv_query($conn, $sql_insert2, $params_insert2);

    // ตรวจสอบการทำงานของคำสั่ง SQL INSERT INTO VALUES
    if ($stmt_insert2 === false) {
        die(print_r(sqlsrv_errors(), true));
    }
}
// ปิด statement ของคำสั่ง SQL SELECT
sqlsrv_free_stmt($stmt);

// ปิดการเชื่อมต่อฐานข้อมูล
sqlsrv_close($conn);

// ส่งผู้ใช้ไปยังหน้าอื่นหลังจาก insert เสร็จสิ้น
header("Location: s.php");

exit();
