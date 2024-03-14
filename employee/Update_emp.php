<?php
require_once('..\config\connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $card_id = $_POST['card_id']; // 
    $person_id = $_POST['person_id']; // 23 input
    $personnel_number = $_POST['personnel_number'];
    $prefix_thai = $_POST['prefix_thai'];
    $firstname_thai = $_POST['firstname_thai'];
    $lastname_thai = $_POST['lastname_thai'];
    $nickname_thai = $_POST['nickname_thai'];
    $prefix_eng = $_POST['prefix_eng'];
    $firstname_eng = $_POST['firstname_eng'];
    $lastname_eng = $_POST['lastname_eng'];
    $nickname_eng = $_POST['nickname_eng'];
    $gender = $_POST['gender'];
    $birth_date = $_POST['birth_date'];
    $blood_type = $_POST['blood_type'];
    $marital_status = $_POST['marital_status'];
    $employee_email = $_POST['employee_email'];
    $phone_number = $_POST['phone_number'];
    $social_security_id = $_POST['social_security_id'];
    $nation = $_POST['nation'];
    $ethnicity = $_POST['ethnicity'];
    $religion = $_POST['religion'];
    $tax_id = $_POST['tax_id'];
    $bank_name = $_POST['bank_name'];
    $bank_branch_name = $_POST['bank_branch_name'];
    $back_account_id = $_POST['back_account_id'];
    $work_per_day = $_POST['work_per_day'];
    $outside_equivalent_year = $_POST['outside_equivalent_year'];
    $outside_equivalent_month = $_POST['outside_equivalent_month'];
    $position_id = isset($_POST['position']) ? $_POST['position'] : null;
    $pl_id = isset($_POST['pl']) ? $_POST['pl'] : null;
    $currentDateTime = date("Y-m-d");

    // คำสั่ง SQL ในรูปแบบของ prepared statement
    $sqlUpdate = "UPDATE employee SET 
                    person_id = ?, 
                    personnel_number = ?,
                    prefix_thai = ?, 
                    firstname_thai = ?, 
                    lastname_thai = ?, 
                    nickname_thai = ?,
                    prefix_eng = ?, 
                    firstname_eng = ?, 
                    lastname_eng = ?, 
                    nickname_eng = ?,  
                    gender = ?, 
                    birth_date = ?, 
                    blood_type = ?, 
                    marital_status = ?, 
                    nation = ?, 
                    ethnicity = ?, 
                    religion = ?, 
                    tax_id = ?,
                    employee_email = ?, 
                    phone_number = ?, 
                    social_security_id = ?,
                    bank_name = ?,
                    bank_branch_name =?,
                    back_account_id = ?,
                    work_per_day = ?,
                    outside_equivalent_year = ?, 
                    outside_equivalent_month = ?
                    WHERE card_id = ?";

    // เตรียม prepared statement
    $stmt = sqlsrv_prepare($conn, $sqlUpdate, array(
        &$person_id,
        &$personnel_number,
        &$prefix_thai,
        &$firstname_thai,
        &$lastname_thai,
        &$nickname_thai,
        &$prefix_eng,
        &$firstname_eng,
        &$lastname_eng,
        &$nickname_eng,
        &$gender,
        &$birth_date,
        &$blood_type,
        &$marital_status,
        &$nation,
        &$ethnicity,
        &$religion,
        &$tax_id,
        &$employee_email,
        &$phone_number,                   
        &$social_security_id,
        &$bank_name,
        &$bank_branch_name,
        &$back_account_id,
        &$work_per_day,
        &$outside_equivalent_year,
        &$outside_equivalent_month,
        &$card_id
    ));

    // ทำการ execute prepared statement
    $result = sqlsrv_execute($stmt);
    
    $sql1 = "UPDATE position_info SET position_id = ? WHERE card_id = ?";
    $stmt1 = sqlsrv_prepare($conn, $sql1, array(
        &$position_id,
        &$card_id
        ));
    $result1 = sqlsrv_execute($stmt1);

    $sql2 = "UPDATE pl_info SET pl_id = ?,start_date = ? WHERE card_id = ?";
    $stmt2 = sqlsrv_prepare($conn, $sql2, array(
        &$pl_id,
        &$currentDateTime,&$card_id));
    $result2 = sqlsrv_execute($stmt2);

    // ตรวจสอบสถานะการ execute
    if ($result === false & $result1 === false & $result2 === false) {
        $errors = sqlsrv_errors();
        echo json_encode(array('status' => 'error', 'message' => 'Database error: ' . $errors[0]['message']));
        exit();
    } else {
        echo json_encode(array('status' => 'success'));
        exit();
    }
}
