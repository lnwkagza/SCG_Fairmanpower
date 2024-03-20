<?php
include("dbconnect.php");
$employee_id = isset($_GET["employee_id"]) ? $_GET["employee_id"] : '';
// Validate and sanitize $employee_id if needed
$employee_id = trim($employee_id);
$employee_id = htmlspecialchars($employee_id, ENT_QUOTES, 'UTF-8');

?>
<script>
function submit_back() {
    window.history.back();
}
</script>

<!DOCTYPE html>
<html>

<head>
</head>

<body>
    <button onclick="submit_back()"> ย้อนกลับ </button>
    <center>
        <h2>ปฎิทินการลาทีม</h2>
    </center>
</body>

</html>