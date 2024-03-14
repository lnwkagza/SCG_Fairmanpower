<?php include('../admin/include/header.php') ?>


<body>

    <?php include('../admin/include/navbar.php') ?>
    <?php include('../admin/include/sidebar.php') ?>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <div class="row" style="justify-content: space-between; align-items: center;">
                                    <div class="col-md-3 col-sm-1 text-left">
                                    </div>
                                    <div class="col-md-3 col-sm-1 text-right">
                                        <a class="btn-back" href='org3_Organizaion.php'>
                                            <i class="fa-solid fa-circle-left fa-xl">
                                            </i>
                                        </a>
                                        <a class="btn-back" href='org5_Location.php'>
                                            <i class="fa-solid fa-circle-right fa-xl"></i>
                                        </a>
                                    </div>
                                </div>
                                <h3>ข้อมูลโครงสร้างองค์กร : Company (บริษัท)</h3>
                                <p class="text-primary">โครงสร้างทั้ง 9 ลำดับขั้นจะเริ่มเรียงจากซ้าย-ขวาเสมอ

                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item "><a href="org1_Business_unit.php">Business Unit</a></li>
                                    <li class="breadcrumb-item"><a href="org2_Sub_Business_unit.php">Sub-business-unit</a></li>
                                    <li class="breadcrumb-item"><a href="org3_Organizaion.php">Organization-ID</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Company</li>
                                    <li class="breadcrumb-item"><a href="org5_Location.php">Location</a></li>
                                    <li class="breadcrumb-item"><a href="org6_Division.php">Division</a></li>
                                    <li class="breadcrumb-item"><a href="org7_Department.php">Department</a></li>
                                    <li class="breadcrumb-item"><a href="org8_Section.php">Section</a></li>
                                    <li class="breadcrumb-item"><a href="org9_Costcenter.php">Cost-Center</a></li>
                                    <li class="breadcrumb-item"><a href="org10_Position.php">Position</a></li>

                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-9 col-md-6 col-sm-12 mb-30">
                        <div class="card-box pd-30 pt-10 height-100-p">
                            <h2 class="h4 text-blue">รายการ บริษัททั้งหมดในระบบ</h2>
                            <p class="text-danger">* หมายเหตุ : หากต้องการลบชื่อ Company (บริษัท) จะต้องลบ Location (สำนักงาน) ที่เกี่ยวข้องก่อนเสมอ</p>

                            <div class="pb-20">
                                <table class="data-table table stripe hover nowrap">
                                    <thead>
                                        <tr>
                                            <th>ลำดับ</th>
                                            <th>Organization ID</th>
                                            <th>ชื่อ Company (TH)</th>
                                            <th>ชื่อ Company (ENG)</th>
                                            <th class="datatable-nosort">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // เตรียมคำสั่ง SQL
                                        $sql4 = "SELECT * FROM company";
                                        $params4 = array();
                                        $i = 1;
                                        // ดึงข้อมูลจากฐานข้อมูล
                                        $stmt4 = sqlsrv_query($conn, $sql4, $params4);
                                        // ตรวจสอบการทำงานของคำสั่ง SQL
                                        if ($stmt4 === false) {
                                            die(print_r(sqlsrv_errors(), true));
                                        }

                                        // แสดงผลลัพธ์ในรูปแบบของตาราง HTML
                                        while ($row = sqlsrv_fetch_array($stmt4, SQLSRV_FETCH_ASSOC)) {
                                            echo "<tr>";
                                            echo "<td>" . $i++ . "</td>";
                                            echo "<td>" . $row["organization_id"] . "</td>";
                                            echo "<td>" . $row["name_thai"] . "</td>";
                                            echo "<td>" . $row["name_eng"] . "</td>";
                                            echo '<td><div class="flex">',
                                            '<button type="button" name="delete_company" class="delete-btn_Org" onclick="confirmDelete_Company(\'' . $row['company_id'] . '\');"><i class="fa-solid fa-trash-can"></i></button>';

                                            echo '<button type="button" class="edit-btn_Org" onclick="openEdit_Company_Modal(\'' . $row['company_id'] . '\', \'' . $row['organization_id'] . '\', \'' . $row['name_thai'] . '\', \'' . $row['name_eng'] . '\');">',
                                            '<i class="fa-solid fa-pencil"></i>',
                                            '</button></div></td>';
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-30">
                        <div class="card-box pd-30 pt-10 height-50-p">
                            <form method="post" id="insert_company">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>หมายเลข Organization ID</label>
                                            <select name="organization_id" class="form-control selectpicker" data-live-search="true" required="true" autocomplete="off">
                                                <option value="" disabled selected> ระบุหมายเลข Organization ID </option>
                                                <?php
                                                // สร้าง options สำหรับ dropdown 2

                                                $sqlDropdown4 = "SELECT org.organization_id as org_id, org.sub_business_id, sub.name_thai as sub_th, sub.name_eng as sub_eng
                                                FROM organization org
                                                INNER JOIN sub_business sub ON sub.sub_business_id = org.sub_business_id";

                                                $resultDropdown4 = sqlsrv_query($conn, $sqlDropdown4);

                                                // เช็ค error
                                                if ($resultDropdown4 === false) {
                                                    die(print_r(sqlsrv_errors(), true));
                                                }

                                                if ($resultDropdown4) {
                                                    while ($row = sqlsrv_fetch_array($resultDropdown4, SQLSRV_FETCH_ASSOC)) {
                                                        echo "<option value='"  . $row['org_id'] . "'>" . $row['org_id'] . ' ' . $row['sub_th'] . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>รหัส Company</label>
                                            <input name="company_id" type="number" maxlength="4" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" class="form-control" required="true" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>ชื่อ Company (TH)</label>
                                            <input name="name_thai" type="text" class="form-control" required="true" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>ชื่อ Company (ENG)</label>
                                            <input name="name_eng" type="text" class="form-control" required="true" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-primary" onclick="insert_company_Form(event);">เพิ่ม Company (บริษัท)</button>
                                    </div>
                                </div>
                        </div>
                        </form>
                        <!-- script -->
                        <script>
                            function insert_company_Form(event) {
                                event.preventDefault();
                                console.log("INSERT company Form send!");
                                const swalWithBootstrapButtons = Swal.mixin({
                                    customClass: {
                                        confirmButton: "green-swal",
                                        cancelButton: "delete-swal"
                                    },
                                    buttonsStyling: false
                                });
                                swalWithBootstrapButtons.fire({
                                    title: 'ยืนยันการเพิ่ม Company (บริษัท)',
                                    text: 'Company (บริษัท) จะถูกเพิ่มลงฐานข้อมูลในระบบ',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: 'ใช่ ,ยืนยัน',
                                    cancelButtonText: 'ยกเลิก',
                                }).then((response) => {
                                    if (response.isConfirmed) {
                                        var formData = $('#insert_company').serialize();
                                        console.log("Form Data: ", formData); // Log ค่า FormData ที่จะส่งไป

                                        $.ajax({
                                            type: "POST",
                                            url: "Back_End_ajax/Org/company_add.php",
                                            data: formData,
                                            dataType: "json", // ระบุว่าต้องการรับข้อมูลเป็น JSON
                                            success: function(response) {
                                                console.log(response);
                                                if (response.status === 'success') {
                                                    const Toast = Swal.mixin({
                                                        toast: true,
                                                        position: "top-end",
                                                        showConfirmButton: false,
                                                        timer: 1200,
                                                        timerProgressBar: true,
                                                        didOpen: (toast) => {
                                                            toast.onmouseenter = Swal.stopTimer;
                                                            toast.onmouseleave = Swal.resumeTimer;
                                                        }
                                                    });
                                                    Toast.fire({
                                                        icon: "success",
                                                        title: "เพิ่ม Company (บริษัท) สำเร็จ"
                                                    }).then(() => {
                                                        location.href = 'org5_Location.php';
                                                    });

                                                } else {
                                                    // แสดงข้อความ error ที่ได้จาก server
                                                    swalWithBootstrapButtons.fire({
                                                        icon: 'error',
                                                        title: 'เกิดข้อผิดพลาด!',
                                                        text: response.message || 'ไม่สามารถบันทึกข้อมูลได้',
                                                    });
                                                }
                                            },
                                            error: function(xhr, textStatus, errorThrown) {
                                                console.log(xhr, textStatus, errorThrown);
                                                // แสดงข้อความ error ที่ได้จาก AJAX request
                                                swalWithBootstrapButtons.fire({
                                                    icon: 'error',
                                                    title: 'เกิดข้อผิดพลาด!',
                                                    text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                                                });
                                            }
                                        });
                                    }
                                });
                            }
                        </script>
                    </div>
                </div>

                <!-- Modal Start -->
                <div class="modal fade" id="editCompanyModal" tabindex="-1" role="dialog" aria-labelledby="editCompanyModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editCompanyModalLabel">แก้ไขข้อมูลบริษัท</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Form for editing Company data -->
                                <form id="update_company" method="post">
                                    <input name="company_id" type="hidden" id="editCompanyIdInput">
                                    <div class="form-group">
                                        <label for="editOrganizationId">หมายเลข Organization-ID</label>
                                        <select name="organization_id" class="form-control" data-live-search="true" required="true" autocomplete="off" id="editOrganizationId">
                                            <?php
                                            // สร้าง options สำหรับ dropdown 2
                                            $sqlDropdown = "SELECT org.organization_id as org_id, org.sub_business_id, sub.name_thai as sub_th, sub.name_eng as sub_eng
                                            FROM organization org
                                            INNER JOIN sub_business sub ON sub.sub_business_id = org.sub_business_id";
                                            $resultDropdown = sqlsrv_query($conn, $sqlDropdown);

                                            // เช็ค error
                                            if ($resultDropdown === false) {
                                                die(print_r(sqlsrv_errors(), true));
                                            }

                                            if ($resultDropdown) {
                                                while ($row = sqlsrv_fetch_array($resultDropdown, SQLSRV_FETCH_ASSOC)) {
                                                    echo "<option value='"  . $row['org_id'] . "'>" . $row['org_id'] . ' ' . $row['sub_th'] . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="editNameThai">ชื่อ Company (TH)</label>
                                        <input type="text" class="form-control" id="editNameThai" name="name_thai" required autocomplete="off">
                                    </div>
                                    <div class="form-group">
                                        <label for="editNameEng">ชื่อ Company (ENG)</label>
                                        <input type="text" class="form-control" id="editNameEng" name="name_eng" required autocomplete="off">
                                    </div>
                                    <div class="text-right">
                                        <button class="btn btn-primary" onclick="update_companyForm(event);">บันทึกการแก้ไข</button>
                                    </div>
                                </form>
                                <!-- script -->
                                <script>
                                    function update_companyForm(event) {
                                        event.preventDefault();
                                        console.log("UPDATE company Form send!");
                                        const swalWithBootstrapButtons = Swal.mixin({
                                            customClass: {
                                                confirmButton: "green-swal",
                                                cancelButton: "delete-swal"
                                            },
                                            buttonsStyling: false
                                        });
                                        swalWithBootstrapButtons.fire({
                                            title: 'ยืนยันแก้ไข Company (บริษัท)',
                                            text: 'Company (บริษัท) จะถูกแก้ไขใหม่ตามที่ระบุ',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonText: 'ใช่ ,ยืนยัน',
                                            cancelButtonText: 'ยกเลิก',
                                        }).then((response) => {
                                            if (response.isConfirmed) {
                                                var formData = $('#update_company').serialize();
                                                console.log("Form Data: ", formData); // Log ค่า FormData ที่จะส่งไป

                                                $.ajax({
                                                    type: "POST",
                                                    url: "Back_End_ajax/Org/company_update.php",
                                                    data: formData,
                                                    dataType: "json", // ระบุว่าต้องการรับข้อมูลเป็น JSON
                                                    success: function(response) {
                                                        console.log(response);
                                                        if (response.status === 'success') {
                                                            swalWithBootstrapButtons.fire({
                                                                icon: 'success',
                                                                title: 'แก้ไข Company (บริษัท) สำเร็จ!',
                                                                text: 'ข้อมูล Company (บริษัท) ถูกแก้ไขเรียบร้อย',
                                                            }).then(() => {
                                                                location.reload();
                                                            });

                                                        } else {
                                                            // แสดงข้อความ error ที่ได้จาก server
                                                            swalWithBootstrapButtons.fire({
                                                                icon: 'error',
                                                                title: 'เกิดข้อผิดพลาด!',
                                                                text: response.message || 'ไม่สามารถบันทึกข้อมูลได้',
                                                            });
                                                        }
                                                    },
                                                    error: function(xhr, textStatus, errorThrown) {
                                                        console.log(xhr, textStatus, errorThrown);
                                                        // แสดงข้อความ error ที่ได้จาก AJAX request
                                                        swalWithBootstrapButtons.fire({
                                                            icon: 'error',
                                                            title: 'เกิดข้อผิดพลาด!',
                                                            text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                                                        });
                                                    }
                                                });
                                            }
                                        });
                                    }
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal End -->
    </div>
    <?php include('../admin/include/footer.php'); ?>

    <?php include('../admin/include/scripts.php') ?>
</body>

</html>