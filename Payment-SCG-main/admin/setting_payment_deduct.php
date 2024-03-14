<?php include('../admin/include/header.php') ?>


<body>

    <?php include('../admin/include/navbar.php') ?>
    <?php include('../admin/include/sidebar.php') ?>
    <?php include('../admin/include/scripts.php') ?>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <h3>ตั้งค่ารายจ่าย</h3>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="setting_payment_income.php">ตั้งค่ารายรับ</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">ตั้งค่ารายจ่าย</li>
                                </ol>

                            </nav>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 col-md-8 col-sm-8">
                        <div class="card-box pd-20 pt-10 height-100-p">
                            <div class="pd-5">
                                <div class="title">
                                    <h2 class="h3 mb-0 text-blue">รายการจ่ายทั้งหมด</h2>
                                    <p class="text-danger">* หมายเหตุ : หากต้องการลบรายการ จะต้องลบข้อมูลในรายการนั้นให้หมดก่อน</p>
                                </div>

                            </div>
                            <table class="data-table2 table stripe hover nowrap">
                                <tread>
                                    <tr>
                                        <th class="datatable-nosort">ชื่อรายการ</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th class="datatable-nosort">&nbsp;การจัดการ</th>

                                    </tr>
                                </tread>
                                <tbody>
                                    <!-- SELECT ค่า deduct -->
                                    <?php
                                    // เตรียมคำสั่ง SQL
                                    $sql = "SELECT * FROM deduct_type";
                                    $params = array();
                                    // ดึงข้อมูลจากฐานข้อมูล
                                    $stmt = sqlsrv_query($conn, $sql, $params);
                                    // ตรวจสอบการทำงานของคำสั่ง SQL
                                    if ($stmt === false) {
                                        die(print_r(sqlsrv_errors(), true));
                                    }

                                    // แสดงผลลัพธ์ในรูปแบบของตาราง HTML
                                    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                        echo "<tr>";
                                        echo "<td class='col-md-8'>" . $row["deduct_type"] . "</td>";
                                        echo "<td></td>";
                                        echo "<td></td>";
                                        echo "<td></td>";
                                        echo "<td></td>";
                                        echo "<td></td>";
                                        echo "<td></td>";
                                        echo "<td></td>";
                                        echo "<td><div class='flex'>",
                                        '<form method="post" action="setting_payment_deduct.php">',
                                        '<input type="hidden" name="deduct_type_id" value="' . $row['deduct_type_id'] . '">',
                                        '<button type="submit" name="delete_deduct_type" class="delete-btn-pay" ><i class="fa-solid fa-trash-can"></i></button>',
                                        '</form>&nbsp;';
                                        echo "<button type='button' class='edit-btn-pay' onclick='openEdit_Deduct_Type_Modal(
                                            \"" . $row['deduct_type_id'] . "\",
                                            \"" . $row['deduct_type'] . "\");'>";
                                        echo "<i class='fa-solid fa-pencil'></i>";
                                        echo "</button>";
                                    }
                                    // ปิดการเชื่อมต่อ
                                    ?>
                                    <?php

                                    // -- DELETE  ค่า deduct ตาม deduct_id -->

                                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_deduct_type'])) {

                                        $deduct_type_id = $_POST['deduct_type_id'];
                                        $sql = "DELETE FROM deduct_type WHERE deduct_type_id = ?";
                                        $params = array($deduct_type_id);

                                        $stmt = sqlsrv_prepare($conn, $sql, $params);
                                        if ($stmt === false) {
                                            die(print_r(sqlsrv_errors(), true));
                                        }

                                        $result = sqlsrv_execute($stmt);
                                        if ($result === false) {
                                            die(print_r(sqlsrv_errors(), true));
                                        } else {
                                            echo '<script type="text/javascript">
                                                    const swalWithBootstrapButtons = Swal.mixin({
                                                        customClass: {
                                                            confirmButton: "delete-swal",
                                                            cancelButton: "edit-swal"
                                                        },
                                                        buttonsStyling: false
                                                    });
                                                    swalWithBootstrapButtons.fire({
                                                        icon: "success",
                                                        title: "ระบบลบข้อมูลสำเร็จ ",
                                                        text: "อีกสักครู่ ...ระบบจะทำการรีเฟส",
                                                        confirmButtonText: "ตกลง",

                                                    })
                                                </script>';
                                            echo "<meta http-equiv='refresh' content='2'>";
                                            exit();
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <div class="row">
                                <div class="mt-5 col-md-5 col-sm-5">
                                    <div class="form-group">
                                        <label style="font-size:24px;"><b></b></label>
                                        <div class="justify-content-left">
                                            <button style="font-size:20px;" onclick="location.href='setting_payment_income_deduct.php'" type="button" class="btn btn-default" data-dismiss="modal"><i class="fa-solid fa-circle-left"> </i> ย้อนกลับ</button>
                                            <!-- color:#AAAAAA -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Start -->
                    <div class="modal fade" id="editdeduct_typeModal" tabindex="-1" role="dialog" aria-labelledby="editdeduct_typeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">แก้ไขชื่อรายการจ่าย</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <!-- Form for editing data -->
                                    <form id="editForm" method="post" action="setting_payment_deduct.php">
                                        <input id="edit_deduct_type_id" name="deduct_type_id" type="hidden">
                                        <div class="form-group">
                                            <label for="edit_deduct_type">ชื่อรายการ</label>
                                            <input type="text" class="form-control" id="edit_deduct_type" name="deduct_type" required autocomplete="off">
                                        </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary" name="update_deduct_type">บันทึกการแก้ไข</button>
                                        </div>
                                    </form>
                                    
                                    <?php
                                    // -- UPDATE deduct Type on deduct_id -->
                                    if (isset($_POST['update_deduct_type'])) {
                                        $deduct_type_id = $_POST['deduct_type_id'];
                                        
                                        $deduct_type = $_POST['deduct_type'];


                                        // อัปเดตค่าของฟิลด์ deduct_type
                                        $sqlUpdate = "UPDATE deduct_type SET deduct_type = '$deduct_type' WHERE deduct_type_id = '$deduct_type_id'";
                                        $stmt = sqlsrv_query($conn, $sqlUpdate);

                                        if ($stmt === false) {
                                            die(print_r(sqlsrv_errors(), true));
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
                                                        title: "แก้ไขข้อมูลสำเร็จ"
                                                    });
                                                    </script>';

                                            echo "<meta http-equiv='refresh' content='1'>";

                                            exit; // จบการทำงานของสคริปต์ทันทีหลังจาก redirect
                                        }
                                    }

                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal End -->

                    <div class="col-lg-4 col-md-4 col-sm-4 mb-30">
                        <div class="card-box pd-30 pt-10 height-50-p">
                            <h2 class="mb-30 h4"></h2>
                            <section>
                                <form name="save" method="post" action="setting_payment_deduct.php">
                                    <div class="form-group text-light-green">
                                        <h3><label>+ เพิ่มรายการจ่าย </label></h3>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>กรอกชื่อรายการ</label>
                                                <input name="deduct_type" type="text" class="form-control" required="true" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 text-right">
                                        <div class="dropdown">
                                            <input class="btn btn-primary" type="submit" value="บันทึก" name="submit">
                                        </div>
                                    </div>
                                </form>
                                <?php

                                // -------- INSERT  ค่า deduct ตาม deduct_id PK-->

                                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                    if (isset($_POST['submit'])) {

                                        $deduct_type = $_POST['deduct_type'];


                                        // ค่าไม่ว่าง ทำการ insert ข้อมูล
                                        $sqlInsert = "INSERT INTO deduct_type (deduct_type) VALUES ('$deduct_type')";
                                        // $params = array($selectedValue1, $nameTH, $nameENG);
                                        $stmt = sqlsrv_query($conn, $sqlInsert);

                                        if ($stmt === false) {
                                            die(print_r(sqlsrv_errors(), true));
                                        } else {
                                            echo '<script type="text/javascript">
                                            const Toast = Swal.mixin({
                                                toast: true,
                                                position: "top-end",
                                                showConfirmButton: false,
                                                timer: 1500,
                                                timerProgressBar: true,
                                                didOpen: (toast) => {
                                                    toast.onmouseenter = Swal.stopTimer;
                                                    toast.onmouseleave = Swal.resumeTimer;
                                                }
                                            });
                                            Toast.fire({
                                                icon: "success",
                                                title: "บันทึกข้อมูลรายการจ่ายสำเร็จ"
                                            });            
                                            </script>';

                                            echo "<meta http-equiv='refresh' content='2'>";

                                            exit; // จบการทำงานของสคริปต์ทันทีหลังจาก redirect
                                        }
                                    }
                                }
                                ?>
                            </section>
                        </div>
                    </div>
                </div>

            </div>
            <?php include('../admin/include/footer.php'); ?>
        </div>
    </div>
    <!-- js -->


</body>

</html>