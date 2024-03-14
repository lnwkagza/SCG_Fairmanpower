<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>pictue upload</title>
</head>
<body>
    <form action="upload_pictue.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="file">
        <button type="submit" name="submit" >ปุ่ม</button>
        <br>
        <?php
        require_once('C:\xampp\htdocs\Payment\config\connection.php');

                    // เตรียมคำสั่ง SQL
                    $sql = "SELECT * FROM income_target ";
                    // ดึงข้อมูลจากฐานข้อมูล
                    $stmt = sqlsrv_query($conn, $sql);

                    // ตรวจสอบการทำงานของคำสั่ง SQL
                    if ($stmt === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }
                    // แสดงผลลัพธ์ในรูปแบบของตาราง HTML
                    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

                        echo "<tr>";
                        echo "<td>" . $row['evidence_name'] . "<td>";
                        echo "<td><a href='img/" . $row['evidence_name'] . "' download>Download</a></td>";
                        echo "</tr>";
                    }

                    ?>
</form>
</body>
</html>