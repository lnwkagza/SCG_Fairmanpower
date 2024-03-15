<?php include('../employee/include/header.php') ?>


<style>
    .form-group {
        margin-bottom: 15px;
    }

    .form-control {
        border-radius: 4px;
        border-color: #ddd;
        height: 40px;
        padding: 0 10px;
    }

    .form-control:focus {
        border-color: #999;
        outline: none;
    }
</style>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>เลือกเดือนที่ต้องการดู</title>
</head>

<body>

    <?php include('../employee/include/navbar.php') ?>
    <?php include('../employee/include/sidebar.php') ?>
    <?php include('../admin/include/scripts.php') ?>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                    <div class="form-group">
                        <label for="month">เดือน:</label>
                        <select class="form-control" id="month">
                            <option value="" disabled selected>-- เลือกเดือน --</option>
                            <option value="1">มกราคม</option>
                            <option value="2">กุมภาพันธ์</option>
                            <option value="3">มีนาคม</option>
                            <option value="4">เมษายน</option>
                            <option value="5">พฤษภาคม</option>
                            <option value="6">มิถุนายน</option>
                            <option value="7">กรกฎาคม</option>
                            <option value="8">สิงหาคม</option>
                            <option value="9">กันยายน</option>
                            <option value="10">ตุลาคม</option>
                            <option value="11">พฤศจิกายน</option>
                            <option value="12">ธันวาคม</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="year">ปี:</label>
                        <select class="form-control" id="year">
                        <option value="" disabled selected>-- เลือกปี --</option>
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                            <option value="2027">2027</option>
                            <option value="2028">2028</option>
                            <option value="2029">2029</option>
                            <option value="2030">2030</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="ยืนยัน" class="btn btn-primary">
                    </div>
            </div>
        </div>
    </div>
</body>

<script>
    const month = document.getElementById("month");
    const year = document.getElementById("year");

    document.querySelector(".btn-primary").addEventListener("click", () => {
        window.location.href = `salary_summary.php?month=${month.value}&year=${year.value}`;
    });
</script>

</html>