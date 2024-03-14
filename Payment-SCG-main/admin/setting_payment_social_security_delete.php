<?php

// ... โค้ดที่มีอยู่ในไฟล์
require_once('..\config\connection.php');

// -- DELETE  ค่า income ตาม income_id -->
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_social_security'])) {
        $social_security_id = $_POST['social_security_id'];

        // ทำการลบข้อมูลจากตาราง business โดยใช้ business_id
        $sqlDelete = "DELETE FROM social_security WHERE social_security_id = '$social_security_id'";
        $stmtDelete = sqlsrv_query($conn, $sqlDelete);

        if ($stmtDelete === false) {
            die(print_r(sqlsrv_errors(), true));
        } else {
            // ส่งค่ากลับเพื่อให้ JavaScript ทำงาน (ถูกใช้ใน SweetAlert2)
            echo json_encode(array('status' => 'success'));
            exit();
        }
    }
}
