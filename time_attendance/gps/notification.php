<?php
include("../dbconnect.php");

$user_name = $_GET['user_id'];
session_start();
$user_id = $_SESSION["user_id"];
echo $user_id;
$sql = mysqli_query($con, "SELECT a.line_token,a.department_id FROM employee a, approver b WHERE a.department_id = b.department_id AND a.employee_id = '$user_id'");
$rs = $sql->fetch_object();
$token = $rs->line_token;
$department_id = $rs->department_id;


$sql1 = mysqli_query($con, "SELECT * FROM approver a WHERE a.department_id = '$department_id'");
if ($sql1) {
    $rs1 = $sql1->fetch_object();
    $token1 = $rs1->line_token;
    echo ($token);

    $url        = 'https://notify-api.line.me/api/notify';
    $token      = $token1;
    $headers    = [
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Bearer ' . $token1
    ];
    $fields     = 'message= มีการขออนุมัติ OT ใหม่จากคุณ' . $user_name . ' กรุณาโปรดทำรายการอนุมัติผ่านลิ้ง https://lin.ee/RPd2a0c';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);

    var_dump($result);
    $result = json_decode($result, TRUE);
}

// header("Location: ../sendMail.php");
