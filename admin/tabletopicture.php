
<!DOCTYPE html>
<html lang="th">

<head>
	<!-- Basic Page Info -->
	<meta charset="utf-8">
	<title>SCG | Fair Manpower</title>

	<!-- Site favicon -->
    <link rel="icon" type="image/ico" href="../favicon.ico">

	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="../vendors/styles/core.css">
	<link rel="stylesheet" type="text/css" href="../vendors/styles/style.css">	
	<link rel="stylesheet" type="text/css" href="../src/plugins/jquery-steps/jquery.steps.css">
	<link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/responsive.bootstrap4.min.css">

	<script src="../asset/plugins/sweetalert2-11.10.1/jquery-3.7.1.min.js"></script>
	<script src="../asset/plugins/sweetalert2-11.10.1/sweetalert2.all.min.js"></script>

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

	<!-- Chagan Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Chakra+Petch&family=Inter:wght@600&family=Noto+Sans+Thai:wght@500&display=swap" rel="stylesheet">

	<style>
		.flex {
			display: flex;
		}
	</style>

</head>
<body>
<?php include('../admin/include/scripts.php') ?>

<table id="example" class="data-table table stripe hover nowrap" style="width:100%">
  <thead>
    <tr>
      <th>ชื่อ</th>
      <th>อายุ</th>
      <th>อาชีพ</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>สมชาย</td>
      <td>30</td>
      <td>นักพัฒนาซอฟต์แวร์</td>
    </tr>
    <tr>
      <td>สมหญิง</td>
      <td>25</td>
      <td>นักออกแบบกราฟิก</td>
    </tr>
    <tr>
      <td>สมศักดิ์</td>
      <td>40</td>
      <td>แพทย์</td>
    </tr>
  </tbody>
</table>

</body>
</html>
