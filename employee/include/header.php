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


	if ($permission_id == 4) {

	}
	else {
		header('location: ../checkrole.php');
	}
	// ส่วนการค้นหา manager ที่มี $card_id เป็นลูกน้องอยู่แล้วในฐานข้อมูล
	$msql = "SELECT m.manager_id as m_id,  
		m.manager_card_id as em_id,                                                                       
		m.edit_time,                                                                         
		m.edit_detail as em_detail,                                                                         
		m.card_id as e_id,                                                                        
		em.prefix_thai as em_pre,                                                                                                            
		em.firstname_thai as em_fname,                                                                       
		em.lastname_thai as em_lname,                                                                       
		em.scg_employee_id as em_scg_id,                                                                        
		em.employee_image as em_img,                                                                         
		em.employee_email as em_email,																		
		em.phone_number as em_phone,
		cost_center.cost_center_code as em_cost,																																																																		
		section.name_thai as section, 
		department.name_thai as department, 																		
		pm.permission_id as pm_id,                                    									
		pm.name as pm_name                                                                       
		FROM manager m                                                                        
		INNER JOIN employee e ON m.card_id = e.card_id                                                                       
		INNER JOIN employee em ON m.manager_card_id = em.card_id
		INNER JOIN cost_center ON cost_center.cost_center_id = em.cost_center_organization_id
		INNER JOIN section ON section.section_id = cost_center.section_id
		INNER JOIN department ON department.department_id = section.department_id
		INNER JOIN permission p ON p.permission_id = e.permission_id
		INNER JOIN permission pm ON pm.permission_id = em.permission_id
		WHERE m.card_id = ? ";
	$mparams = array($card_id);
	$mstmt = sqlsrv_query($conn, $msql, $mparams);

	if ($mstmt === false) {
		die(print_r(sqlsrv_errors(), true));
	}
	$manger = sqlsrv_fetch_array($mstmt, SQLSRV_FETCH_ASSOC);
    if ($manger) {
        $nboss = $manger["em_fname"]. ' '. $manger["em_lname"];
        $_SESSION['nboss'] = $nboss;
        $manager_card_id = $manger['em_id'];
        $_SESSION['manager_card_id'] = $manager_card_id;
    } 
	// ส่วนการค้นหา report-to ที่มี $card_id เป็นลูกน้องอยู่แล้วในฐานข้อมูล
	$r_sql = "SELECT m.report_to_id as m_id,  
			m.report_to_card_id as em_id,                                                                       
			m.edit_time,                                                                         
			m.edit_detail as em_detail,                                                                         
			m.card_id as e_id,                                                                        
			em.prefix_thai as em_pre,                                                                                                            
			em.firstname_thai as em_fname,                                                                       
			em.lastname_thai as em_lname,                                                                       
			em.scg_employee_id as em_scg_id,                                                                        
			em.employee_image as em_img,                                                                         
			em.employee_email as em_email,																		
			em.phone_number as em_phone,
			cost_center.cost_center_code as em_cost,																																																																		
			section.name_thai as section, 
			department.name_thai as department, 																		
			pm.permission_id as pm_id,                                    									
			pm.name as pm_name                                                                       
			FROM report_to m                                                                        
			INNER JOIN employee e ON m.card_id = e.card_id                                                                       
			INNER JOIN employee em ON m.report_to_card_id = em.card_id
			INNER JOIN cost_center ON cost_center.cost_center_id = em.cost_center_organization_id
			INNER JOIN section ON section.section_id = cost_center.section_id
			INNER JOIN department ON department.department_id = section.department_id
			INNER JOIN permission p ON p.permission_id = e.permission_id
			INNER JOIN permission pm ON pm.permission_id = em.permission_id
			WHERE m.card_id = ? ";
	$r_params = array($card_id);
	$r_stmt = sqlsrv_query($conn, $r_sql, $r_params);

	if ($r_stmt === false) {
		die(print_r(sqlsrv_errors(), true));
	}

	// ตรวจสอบว่ามีข้อมูลหรือไม่
	if (sqlsrv_has_rows($r_stmt)) {
		$r_port = sqlsrv_fetch_array($r_stmt, SQLSRV_FETCH_ASSOC);
		// ประมวลผลข้อมูลที่ได้รับ
	} else {
		// กรณีไม่พบข้อมูล
		$r_port = array();  // กำหนดค่าเป็น array ว่างหรือตามที่คุณต้องการ
	}

	// ส่วนคำสั่ง SQL ควรตรงกับโครงสร้างของตารางในฐานข้อมูล
	$sql2 = "SELECT *,
	permission.name as permission, permission.permission_id as permissionID, contract_type.name_eng as contracts, contract_type.name_thai as contract_th,
	section.name_thai as section, department.name_thai as department 
	
	FROM employee
	LEFT JOIN cost_center ON cost_center.cost_center_id = employee.cost_center_organization_id
	LEFT JOIN section ON section.section_id = cost_center.section_id
	LEFT JOIN department ON department.department_id = section.department_id
	LEFT JOIN permission ON permission.permission_id = employee.permission_id
	LEFT JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id WHERE employee.card_id = ?";

	$params = array($card_id);
	$stmt = sqlsrv_query($conn, $sql2, $params);

	if ($stmt === false) {
		die(print_r(sqlsrv_errors(), true));
	}

	$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
	if ($row) {
	} else {
		// หากไม่พบข้อมูลที่ตรงกัน
		echo "ไม่พบข้อมูลที่ตรงกับ line_id: $line_id";
	}

	// ตรวจสอบว่ามีข้อมูลในตาราง employee_info หรือไม่
	$check_employee_info_sql = "SELECT * FROM employee_info WHERE card_id = ?";
	$check_employee_info_params = array($card_id);
	$check_employee_info_stmt = sqlsrv_query($conn, $check_employee_info_sql, $check_employee_info_params);

	if ($check_employee_info_stmt === false) {
		die(print_r(sqlsrv_errors(), true));
	}

	$has_employee_info = sqlsrv_has_rows($check_employee_info_stmt);

	// ถ้าไม่มีข้อมูลในตาราง employee_info ให้ทำการ INSERT
	if (!$has_employee_info) {
		$insert_employee_info_sql = "INSERT INTO employee_info (card_id) VALUES (?)";
		$insert_employee_info_params = array($card_id); // แทนค่า $value1, $value2, ... ด้วยค่าที่ต้องการใส่
		$insert_employee_info_stmt = sqlsrv_query($conn, $insert_employee_info_sql, $insert_employee_info_params);

		if ($insert_employee_info_stmt === false) {
			die(print_r(sqlsrv_errors(), true));
		}

		echo "ข้อมูลถูกเพิ่มลงในตาราง employee_info";
	} else {
		echo "มีข้อมูลในตาราง employee_info แล้ว | ";
	}

	// ตรวจสอบว่ามีข้อมูลในตาราง education_info หรือไม่
	$check_education_info_sql = "SELECT * FROM education_info WHERE card_id = ?";
	$check_education_info_params = array($card_id);
	$check_education_info_stmt = sqlsrv_query($conn, $check_education_info_sql, $check_education_info_params);

	if ($check_education_info_stmt === false) {
		die(print_r(sqlsrv_errors(), true));
	}

	$has_education_info = sqlsrv_has_rows($check_education_info_stmt);

	// ถ้าไม่มีข้อมูลในตาราง education_info ให้ทำการ INSERT
	if (!$has_education_info) {
		$insert_education_info_sql = "INSERT INTO education_info (card_id) VALUES (?)";
		$insert_education_info_params = array($card_id); // แทนค่า $value1, $value2, ... ด้วยค่าที่ต้องการใส่
		$insert_education_info_stmt = sqlsrv_query($conn, $insert_education_info_sql, $insert_education_info_params);

		if ($insert_education_info_stmt === false) {
			die(print_r(sqlsrv_errors(), true));
		}

		echo "ข้อมูลถูกเพิ่มลงในตาราง education_info";
	} else {
		echo "มีข้อมูลในตาราง education_info แล้ว ";
	}

	// ส่วนการค้นหา employee_info ที่มี $card_id อยู่แล้วในฐานข้อมูล
	$sql_info = "SELECT *
	FROM employee_info e_info
	INNER JOIN employee e ON e.card_id = e_info.card_id
    WHERE e_info.card_id = ?";

	$e_params = array($card_id);
	$e_stmt = sqlsrv_query($conn, $sql_info, $e_params);

	if ($e_stmt === false) {
		die(print_r(sqlsrv_errors(), true));
	}

	$e_info = sqlsrv_fetch_array($e_stmt, SQLSRV_FETCH_ASSOC);
	if ($e_info) {
	} else {
		// หากไม่พบข้อมูลที่ตรงกัน
		echo "ไม่พบข้อมูลที่ตรงกับ card_id: $card_id บน employee_info";
	}

	// ส่วนการค้นหา education_info ที่มี $card_id อยู่แล้วในฐานข้อมูล
	$sql_edu = "SELECT *
		FROM education_info e_edu
		INNER JOIN employee e ON e.card_id = e_edu.card_id
		WHERE e_edu.card_id = ?";

	$e_edu_params = array($card_id);
	$e_edu_stmt = sqlsrv_query($conn, $sql_edu, $e_edu_params);

	if ($e_edu_stmt === false) {
		die(print_r(sqlsrv_errors(), true));
	}

	$e_edu = sqlsrv_fetch_array($e_edu_stmt, SQLSRV_FETCH_ASSOC);
	if ($e_edu) {
	} else {
		// หากไม่พบข้อมูลที่ตรงกัน
		echo "ไม่พบข้อมูลที่ตรงกับ card_id: $card_id";
	}
}
$date2 = new DateTime();
$date2->setTimezone(new DateTimeZone('Asia/Bangkok'));
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

	<!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="../vendors/styles/core.css">
	<link rel="stylesheet" type="text/css" href="../vendors/styles/style.css">	
	<link rel="stylesheet" type="text/css" href="../src/plugins/jquery-steps/jquery.steps.css">
	<link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/responsive.bootstrap4.min.css">

	<script src="../asset/plugins/sweetalert2-11.10.1/jquery-3.7.1.min.js"></script>
	<script src="../asset/plugins/sweetalert2-11.10.1/sweetalert2.all.min.js"></script>

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />


	<style>
		.flex {
			display: flex;
		}

		input[list]+datalist {
			width: 100%;
		}
	</style>

</head>