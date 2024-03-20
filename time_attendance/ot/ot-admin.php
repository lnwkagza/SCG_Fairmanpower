<?php
include("dbconnect.php");
$employee_id = $_GET["employee_id"];

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
    <h2>OT</h2>
    </center>
</body>
</html>
