<?php include('../admin/include/header.php') ?>


<?php

if (isset($_SESSION['id'])) {
    $employee_payment_id = $_SESSION['id'];
    // ตอนนี้คุณสามารถใช้ค่า $employee_payment_id ได้ตามต้องการ
} else {
    echo "ไม่พบค่า employee_payment_id ใน session";
}
?>

<div>
    <?php include('../admin/include/navbar.php') ?>
    <?php include('../admin/include/sidebar.php') ?>
    <?php include('../admin/include/scripts.php') ?>

    <?php
    $sql = "SELECT employee_payment.employee_payment_id,employee.card_id as card_id, scg_employee_id, prefix_thai,firstname_thai,lastname_thai,nickname_thai,evidence_name,company.name_thai as company,division.name_thai as division,department.name_thai as department,section.name_thai as section,cost_center.cost_center_code as cost_center,contract_type.name_thai as contract_type,pl.pl_name_thai as pl,position.name_thai as position ,salary_per_month,salary_per_day,salary_per_hour,comment FROM employee
    LEFT JOIN cost_center ON cost_center.cost_center_id  = employee.cost_center_organization_id
    LEFT JOIN section ON section.section_id = cost_center.section_id 
    LEFT JOIN department ON department.department_id = section.department_id 
    LEFT JOIN division ON division.division_id = department.division_id 
    LEFT JOIN location ON location.location_id = division.location_id
    LEFT JOIN company ON company.company_id = location.company_id
    LEFT JOIN contract_type ON contract_type.contract_type_id = employee.contract_type_id
    LEFT JOIN pl_info ON pl_info.card_id  = employee.card_id
    LEFT JOIN pl ON pl.pl_id = pl_info.pl_id 
    LEFT JOIN position_info ON position_info.card_id  = employee.card_id
    LEFT JOIN position ON position.position_id  = position_info.position_id
    LEFT JOIN employee_payment ON employee_payment.card_id = employee.card_id
    WHERE employee_payment.employee_payment_id = ?";
    $params = array($employee_payment_id);
    $result = sqlsrv_query($conn, $sql, $params);
    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    $firstAssessment = true;
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    ?>
        <div class="main-container">
            <div class="pd-ltr-20 xs-pd-20-10">
                <div class="min-height-100px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <h3>เงินเดือนพนักงาน : Employee Payment</h3>
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">เงินเดือนพนักงาน</li>
                                    <li class="breadcrumb-item"><a href="income.php">รายรับ/รายจ่าย</a></li>
                                    <li class="breadcrumb-item"><a href="calculator_payment2.php">คำนวณเงินเดือน</a></li>
                                    <li class="breadcrumb-item"><a href="history_payment.php">ประวัติการคำนวณ</a></li>
                                    <li class="breadcrumb-item"><a href="setting_payment.php">ตั้งค่า</a></li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-8">
                    <div class="card-box pd-30 pt-20" style="box-shadow: 1px 4px 8px rgba(0, 0, 0, 0.2);">
                        <div class="title">
                            <h4>แก้ไขเงินเดือนพนักงาน</h4>
                            <form id="editForm" method="post" action="employee_payment_edit_update.php" enctype="multipart/form-data">
                                <input type="hidden" id="card_id" name="card_id" value="<?php echo $row['card_id']; ?>">
                                <input type="hidden" id="edit_employee_payment_id" name="employee_payment_id" value="<?php echo $row['employee_payment_id']; ?>">
                                <div class="form-group d-flex mt-3">
                                    <input type="text" class="col-12" id="name" name="first_name_thai" autocomplete="off" disabled style="border: none; background-color: transparent; font-size: 30px; font-weight: bold; text-align: center;" readonly value="<?php echo $row['prefix_thai'], ' ', $row['firstname_thai'], ' ', $row['lastname_thai'], ' (', $row['nickname_thai'], ')'; ?>">
                                </div>
                                <div class="row ml-1 mt-3">
                                    <div class="form-group col-4">
                                        <h6><label for="editsalary_per_month">ค่าแรง/เดือน</label></h6>
                                        <input type="number" class="form-control" id="editsalary_per_month" placeholder="กรอกเงินเดือน" name="salary_per_month" required autocomplete="off" value="<?php echo $row['salary_per_month']; ?>">
                                    </div>
                                    <div class="form-group col-4">
                                        <h6><label for="editsalary_per_day">ค่าแรง/วัน</label></h6>
                                        <input type="number" class="form-control" id="editsalary_per_day" placeholder="รายเดือนหารด้วย30วัน" name="salary_per_day" required autocomplete="off" value="<?php echo $row['salary_per_day']; ?>">
                                    </div>
                                    <div class="form-group col-4">
                                        <h6><label for="editsalary_per_hour">ค่าแรง/ชั่วโมง</label></h6>
                                        <input type="number" class="form-control" id="editsalary_per_hour" placeholder="รายวันหารด้วย8ชั่วโมง" name="salary_per_hour" required autocomplete="off" value="<?php echo $row['salary_per_hour']; ?>">
                                    </div>
                                </div>
                                <div class="form-group col-11">
                                    <h6><label for="editcomment">หมายเหตุ</label></h6>
                                    <input type="text" class="form-control" id="editcomment" placeholder="กรุณากรอกหมายเหตุ" name="comment" required autocomplete="off">
                                </div>
                                <div class="form-group col-11">
                                    <h6><label>แนบหลักฐาน (ถ้ามี)</label></h6>
                                    <input type="file" name="file" class="form-control">
                                </div>
                        </div>
                        <div class="row mt-3 d-flex align-items-center">
                            <div class="mt-2 col-6 d-flex align-items-center">
                                <h6 class="mb-0 mr-2">Company :</h6>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="text" id="company" class="col-8 form-control" name="company" autocomplete="off" readonly value="<?php echo $row['company']; ?>" style="border: none; background-color: transparent; color: #606060;" disabled>
                            </div>
                            <div class="mt-2 col-6 d-flex align-items-center">
                                <h6 class="mb-0 mr-2">Division :</h6>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="text" id="division" class="col-8 form-control" name="division" autocomplete="off" readonly value="<?php echo $row['division']; ?>" style="border: none; background-color: transparent; color: #606060;" disabled>
                            </div>
                            <div class="mt-2 col-6 d-flex align-items-center">
                                <h6 class="mb-0 mr-2">Department :</h6>&nbsp;
                                <input type="text" id="department" class="col-8 form-control" name="department" autocomplete="off" readonly value="<?php echo $row['department']; ?>" style="border: none; background-color: transparent; color: #606060;" disabled>
                            </div>
                            <div class="mt-2 col-6 d-flex align-items-center">
                                <h6 class="mb-0 mr-2">Section :</h6>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="text" id="section" class="col-8 form-control" name="section" autocomplete="off" readonly value="<?php echo $row['section']; ?>" style="border: none; background-color: transparent; color: #606060;" disabled>
                            </div>
                            <div class="mt-2 col-6 d-flex align-items-center">
                                <h6 class="mb-0 mr-2">Cost Center :</h6>&nbsp;&nbsp;
                                <input type="text" id="cost_center" class="col-8 form-control" name="cost_center" autocomplete="off" readonly value="<?php echo $row['cost_center']; ?>" style="border: none; background-color: transparent; color: #606060;" disabled>
                            </div>
                            <div class="mt-2 col-6 d-flex align-items-center">
                                <h6 class="mb-0 mr-2">ประเภทพนักงาน :</h6>&nbsp;&nbsp;
                                <input type="text" id="contract_type" class="col-8 form-control" name="contract_type" autocomplete="off" readonly value="<?php echo $row['contract_type']; ?>" style="border: none; background-color: transparent; color: #606060;" disabled>
                            </div>
                            <div class="mt-2 col-6 d-flex align-items-center">
                                <h6 class="mb-0 mr-2">ระดับการทำงาน :</h6>
                                <input type="text" id="pl" class="col-8 form-control" name="pl" autocomplete="off" readonly value="<?php echo $row['pl']; ?>" style="border: none; background-color: transparent; color: #606060;" disabled>
                            </div>
                            <div class="mt-2 col-6 d-flex align-items-center">
                                <h6 class="mb-0 mr-2">ตำแหน่ง :</h6>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="text" id="position" class="col-8 form-control" name="position" autocomplete="off" readonly value="<?php echo $row['position']; ?>" style="border: none; background-color: transparent; color: #606060;" disabled>
                            </div>
                        </div>
                        <div class="text-right mt-4">
                            <button type="submit" class="btn btn-primary" name="update_employee_payment" name="submit">บันทึกการแก้ไข</button>
                        </div>
                    </div>
                </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                    <div class="card-box pd-30 pt-20" style="box-shadow: 1px 4px 8px rgba(0, 0, 0, 0.2);">
                        <div class="text-right mr-3">
                            <h5><i class="fa-solid fa-clock-rotate-left"></i>&nbsp;&nbsp;ประวัติการแก้ไข </h5>
                        </div>
                        <?php
                        $sql = "SELECT * FROM log_payment
                                LEFT JOIN employee_payment ON employee_payment.employee_payment_id = log_payment.employee_payment_id 
                                WHERE employee_payment.employee_payment_id = ?
                                ORDER BY log_payment.update_time DESC ";
                        $params = array($employee_payment_id);
                        $result = sqlsrv_query($conn, $sql, $params);
                        if ($result === false) {
                            die(print_r(sqlsrv_errors(), true));
                        }
                        $firstAssessment = true;
                        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                            $date_time  = $row["update_time"]; // สร้างวัตถุ DateTime
                            $date_time = $date_time->format('d-m-Y');
                            $time_start  = $row["update_time"]; // สร้างวัตถุ DateTime
                            $time_start = $time_start->format('H:i:s');

                        ?>
                            <div class="text-right ">
                                <input class="form-control col-12 text-right mt-4" style="font-weight: bold; color: rgb(111, 112, 112); border: none; background-color: transparent; " disabled value="<?php echo $row['new_comment'], ' วันที่ ', $date_time, '  เวลา ', $time_start, ' น.'; ?>">
                                <input class="form-control text-right" style="color: rgb(111, 112, 112) ; border: none; background-color: transparent;" disabled value="<?php echo 'เงินเดือนล่าสุด :  ', $row['new_salary_per_month'], ' บาท'; ?>">
                                <input class="form-control text-right" style="color: rgb(111, 112, 112) ; border: none; background-color: transparent;" disabled value="<?php echo 'เงินเดือนก่อนหน้า :  ', $row['old_salary_per_month'], ' บาท'; ?>">
                                <?php echo "<a href='flie/" . $row['new_evidence_name'] . "' download>";
                                echo "<button type='button' class='download-btn-edit-payment mt-2 mr-2'>";
                                echo "ดาวน์โหลดเอกสาร";
                                echo "  <i class='fa-solid fa-file-arrow-down'></i>";
                                echo "</button>";
                                echo "</a>"; ?>
                            </div>
                        <?php
                        }
                        // -- UPDATE employee_payment based on employee_payment_id -->
                        ?>
                    </div>
                    </div>
                    </form>
                
                </div>
            </div>
        </div>
</div>
<?php include('../admin/include/footer.php'); ?>
</div>
</div>
<?php
    }
    // -- UPDATE employee_payment based on employee_payment_id -->
?>


</body>

</html>