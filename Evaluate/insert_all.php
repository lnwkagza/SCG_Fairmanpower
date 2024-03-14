<?php 
require_once('..\config\connection.php');

if (isset($_POST['save'])) {
    if(isset($_POST['myCheckbox'])) {
        $trid = $_POST['myCheckbox'];


        foreach($trid as $tr_id) {
            $status = 'approve';
            // เตรียมคำสั่ง SQL เพื่อ insert ข้อมูลลงในฐานข้อมูล
            $sqlInsert = "UPDATE transaction_review SET status = ? WHERE tr_id = ? " ;
            $paramsInsert = array( $status,$tr_id);
            $stmtInsert = sqlsrv_query($conn, $sqlInsert,$paramsInsert );

            if ($stmtInsert === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        }

        echo "บันทึกข้อมูลเรียบร้อยแล้ว";
    } else {
        echo "ไม่มีข้อมูลที่จะบันทึก";
    }
}
?>