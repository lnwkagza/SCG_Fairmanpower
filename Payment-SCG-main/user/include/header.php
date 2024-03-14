<?php
session_start(); // เรียกใช้ session_start() ก่อนที่จะใช้ session
require_once('..\config\connection.php');
// ตรวจสอบว่ามี Session 'line_id' หรือไม่ และค่าของ 'line_id' ไม่เป็นค่าว่าง

if (
	isset($_SESSION['line_id'], $_SESSION['card_id'], $_SESSION['prefix_thai'], $_SESSION['firstname_thai'], $_SESSION['lastname_thai'], $_SESSION['split_set_id']) &&
	!empty($_SESSION['line_id']) && !empty($_SESSION['card_id']) && !empty($_SESSION['prefix_thai']) &&
	!empty($_SESSION['firstname_thai']) && !empty($_SESSION['lastname_thai']) && !empty($_SESSION['split_set_id'])
) {
	$line_id = $_SESSION['line_id'];
	$card_id = $_SESSION['card_id'];
	$prefix = $_SESSION['prefix_thai'];
	$fname = $_SESSION['firstname_thai'];
	$lname = $_SESSION['lastname_thai'];
	$emp_split = $_SESSION['split_set_id'];

	// $tax_id = $_SESSION['tax_id'];


	// ส่วนคำสั่ง SQL ควรตรงกับโครงสร้างของตารางในฐานข้อมูล
	$sql = "SELECT *,
	permission.name as permission, permission.permission_id as permissionID, contract_type.name_eng as contracts, contract_type.name_thai as contract_th,
	section.name_thai as section, department.name_thai as department 
	
	FROM employee
	INNER JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id
	INNER JOIN section ON section.section_id = cost_center.section_id
	INNER JOIN department ON department.department_id = section.department_id
	INNER JOIN permission ON permission.permission_id = employee.permission_id
	INNER JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id WHERE employee.card_id = ?";	$params = array($card_id);
	$stmt = sqlsrv_query($conn, $sql, $params);

	if ($stmt === false) {
		die(print_r(sqlsrv_errors(), true));
	}

	$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
	if ($row) {
	} else {
		// หากไม่พบข้อมูลที่ตรงกัน
		echo "ไม่พบข้อมูลที่ตรงกับ line_id: $line_id";
	}
}
?>

<!DOCTYPE html>
<html>

<head>
	<!-- Basic Page Info -->
	<meta charset="utf-8">
	<title>SCG | Fair Manpower</title>

	<!-- Site favicon -->
	<link rel="icon" type="image/ico" href="../favicon.ico">


	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">



	<!-- font -->
	
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" type="text/css" href="../src/plugins/jquery-steps/jquery.steps.css">
	<link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300&display=swap" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/responsive.bootstrap4.min.css">



	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			font-family: 'Chakra Petch', sans-serif;
		}
		input,
		select,
		select option:checked,
		option,
		button {
			font-family: 'Chakra Petch', sans-serif;
		}
	</style>

	<!-- เด้งไปหน้าก่อนหน้า -->
	<script>
		function goBack() {
			window.history.back();
		}
	</script>

</head>