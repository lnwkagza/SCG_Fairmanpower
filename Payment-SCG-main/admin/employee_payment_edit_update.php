<?php include('../admin/include/header.php') ?>
<?php include('../admin/include/scripts.php') ?>


<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $card_id = $_POST['card_id'];
    $employee_payment_id = $_POST['employee_payment_id'];
    $salary_per_month = $_POST['salary_per_month'];
    $formatted_salary_per_month = number_format($salary_per_month, 2);
    $salary_per_day = $_POST['salary_per_day'];
    $formatted_salary_per_day = number_format($salary_per_day, 2);
    $salary_per_hour = $_POST['salary_per_hour'];
    $formatted_salary_per_hour = number_format($salary_per_hour, 2);
    $comment = $_POST['comment'];
    $targetDir = "flie/";
    $targetFile = $targetDir . $_FILES["file"]["name"];
    $file_type = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $income_target_id = 10;

    $sqlGet = "SELECT employee_payment_id, card_id, salary_per_month, salary_per_day, salary_per_hour, comment, time_set
    FROM fairman.dbo.employee_payment
    WHERE employee_payment_id=$employee_payment_id;";
    $stmt = sqlsrv_query($conn, $sqlGet);
    if (empty($stmt)) {
        $sqlInsert = "INSERT INTO employee_payment
            (card_id, salary_per_month, salary_per_day, salary_per_hour, comment, time_set)
            VALUES( '$card_id', '$salary_per_month', '$salary_per_day', '$salary_per_hour','$comment', GETDATE());";
        $stmt = sqlsrv_query($conn, $sqlInsert);
    } else {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
            $filename = $_FILES["file"]["name"];
            $sqlUpdate = "UPDATE employee_payment SET salary_per_month = '$salary_per_month',
            salary_per_day = '$salary_per_day', salary_per_hour = '$salary_per_hour', comment = '$comment' ,
            evidence_name = '$filename' , evidence_data = '$targetFile', time_set = GETDATE()
            WHERE employee_payment_id = '$employee_payment_id'";
            $stmt = sqlsrv_query($conn, $sqlUpdate);
        } else {
            $sqlUpdate1 = "UPDATE employee_payment SET salary_per_month = '$salary_per_month',
            salary_per_day = '$salary_per_day', salary_per_hour = '$salary_per_hour', comment = '$comment',     
            evidence_name = null , evidence_data = null,time_set = GETDATE()
            WHERE employee_payment_id = '$employee_payment_id'";
            $stmt = sqlsrv_query($conn, $sqlUpdate1);
        }
    }


    // เพิ่ม log ลงในตาราง log_salary_comment
    $sqlLog = "INSERT INTO log_payment (employee_payment_id, old_salary_per_month, new_salary_per_month, old_salary_per_day, new_salary_per_day, old_salary_per_hour, new_salary_per_hour,old_comment, new_comment,old_evidence_name,new_evidence_name)
                VALUES (@employee_payment_id, @old_salary_per_month, @new_salary_per_month, @old_salary_per_day, @new_salary_per_day, @old_salary_per_hour, @new_salary_per_hour, @old_comment, @new_comment,@old_evidence_name,@new_evidence_name);";
    $stmtLog = sqlsrv_query($conn, $sqlLog);



    if (sqlsrv_execute($stmt)) {
        echo "error";
        exit; // จบการทำงานของสคริปต์ทันทีหลังจาก redirect
    } else {
        echo '<script type="text/javascript">
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 950,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    Toast.fire({
                        icon: "success",
                        title: "แก้ไขข้อมูล Employee Payment สำเร็จ"
                    });
                    setTimeout(function() {
                        window.location.href = "employee_payment.php";
                    }, 950);
                    </script>';
    }
}

?>