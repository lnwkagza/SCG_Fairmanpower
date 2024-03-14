<?php
session_start(); // เรียกใช้ session_start() ก่อนที่จะใช้ session
require_once('..\config\connection.php');
// ตรวจสอบว่ามี Session 'line_id' หรือไม่ และค่าของ 'line_id' ไม่เป็นค่าว่าง

if (
	isset($_SESSION['line_id'], $_SESSION['card_id'], $_SESSION['prefix_thai'], $_SESSION['firstname_thai'], $_SESSION['lastname_thai']) &&
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

}

?>