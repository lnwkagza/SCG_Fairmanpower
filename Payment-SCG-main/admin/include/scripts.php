<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../vendors/scripts/core.js"></script>
<script src="../vendors/scripts/script.min.js"></script>
<script src="../vendors/scripts/process.js"></script>
<script src="../vendors/scripts/layout-settings.js"></script>
<script src="../src/plugins/apexcharts/apexcharts.min.js"></script>
<script src="../src/plugins/datatables/js/jquery.dataTables.min.js"></script>
<script src="../src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
<script src="../src/plugins/datatables/js/dataTables.responsive.min.js"></script>
<script src="../src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
<script src="../vendors/scripts/datagraph.js"></script>

<!-- buttons for Export datatable -->
<script src="../src/plugins/datatables/js/dataTables.buttons.min.js"></script>
<script src="../src/plugins/datatables/js/buttons.bootstrap4.min.js"></script>
<script src="../src/plugins/datatables/js/buttons.print.min.js"></script>
<script src="../src/plugins/datatables/js/buttons.html5.min.js"></script>
<script src="../src/plugins/datatables/js/buttons.flash.min.js"></script>
<script src="../src/plugins/datatables/js/pdfmake.min.js"></script>
<script src="../src/plugins/datatables/js/vfs_fonts.js"></script>
<script src="../vendors/scripts/advanced-components.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable with custom options
        var dataTable = $('.data-table2').DataTable({

            "lengthMenu": [[5, 10, 50, -1 ],["5 รายการ","10 รายการ","50 รายการ","ทั้งหมด"]], // เลือกจำนวนแถวที่แสดง
            "pageLength": 5, // จำนวนแถวที่แสดงต่อหน้าเริ่มต้น
            "dom": '<"d-flex justify-content-between"lf>rt<"d-flex justify-content-between"ip><"clear">', // ตำแหน่งของ elements
            "language": {
                "infoEmpty": "ไม่มีข้อมูลที่แสดง",
                "infoFiltered": "(กรองจากทั้งหมด)",
                "search": "ค้นหา:",
                "lengthMenu": 'เลือกจำนวนข้อมูลที่จะแสดง _MENU_',
                "paginate": {
                    "first": "หน้าแรก",
                    "last": "หน้าสุดท้าย",
                    "next": "ถัดไป",
                    "previous": "ย้อนกลับ"
                }
            }
        });

        // Add Bootstrap styling to length dropdown and search input
        $('select[name="dataTables_length"]').addClass('form-control form-control-lg');
        $('input[type="search"]').addClass('form-control form-control-lg ');

        // Trigger DataTables redraw on select change
        $('select[name="dataTables_length"]').change(function() {
            dataTable.draw();
        });

        // Trigger DataTables search on input change
        $('input[type="search"]').on('input', function() {
            dataTable.search(this.value).draw();
        });
    });


    function openInsert_Employee_Payment_Modal(card_id, employee_payment_id, prefix_thai, firstname_thai, lastname_thai, company, division, department, section, cost_center, contract_type, pl, position, salary_per_month, salary_per_day, salary_per_hour, comment) {
        document.getElementById('card_id').value = card_id;
        document.getElementById('name').value = prefix_thai + "" + firstname_thai + "  " + lastname_thai;
        document.getElementById('edit_employee_payment_id').value = employee_payment_id;
        document.getElementById('edit_company').value = company;
        document.getElementById('edit_division').value = division;
        document.getElementById('edit_department').value = department;
        document.getElementById('edit_section').value = section;
        document.getElementById('edit_cost_center').value = cost_center;
        document.getElementById('edit_contract_type').value = contract_type;
        document.getElementById('edit_pl').value = pl;
        document.getElementById('edit_position').value = position;
        document.getElementById('editsalary_per_month').value = salary_per_month;
        document.getElementById('editsalary_per_day').value = salary_per_day;
        document.getElementById('editsalary_per_hour').value = salary_per_hour;
        document.getElementById('editcomment').value = "";
        $('#editemployeeModal').modal('show');
        console.log(openEdit_Employee_Payment_Modal);
    }

    // ฟังขั่นส่งค่า tr_id ไปหน้า do_assessment.php
    function openEdit_Employee_Payment(employee_payment_id, card_id) {
        window.location.href = 'reason_payment_edit.php?id=' + employee_payment_id + '&card_id=' + card_id;
    }

    function openEdit_Income_Type_Modal(income_type_id, income_type) {
        document.getElementById('edit_income_type_id').value = income_type_id;
        document.getElementById('edit_income_type').value = income_type;
        $('#editincome_typeModal').modal('show');
    }

    function openInsert_Income_Target_Modal() {
        $('#insertincome_targetModal').modal('show');
    }

    function openEdit_Income_Target_Modal(income_target_id, prefix_thai, firstname_thai, lastname_thai, income_type, company, division, department, section, cost_center, contract_type, pl, position, amount, whole_year, reason) {
        document.getElementById('income_target_id').value = income_target_id;
        document.getElementById('editcard_id').value = prefix_thai + "" + firstname_thai + "  " + lastname_thai;
        document.getElementById('editincome_type').value = income_type;
        document.getElementById('editcompany').value = company;
        document.getElementById('editdivision').value = division;
        document.getElementById('editdepartment').value = department;
        document.getElementById('editsection').value = section;
        document.getElementById('editcost_center').value = cost_center;
        document.getElementById('editcontract_type').value = contract_type;
        document.getElementById('editpl').value = pl;
        document.getElementById('editposition').value = position;
        document.getElementById('editamount').value = amount;
        document.getElementById('editwhole_year').value = "";
        document.getElementById('editreason').value = "";
        $('#editincome_targetModal').modal('show');
    }

    function confirmDeleteIncome_target(income_target_id) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "delete-swal",
                cancelButton: "edit-swal"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: 'คุณต้องการลบ รายการรับ นี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบ!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // ส่ง request ไปยังไฟล์ PHP ที่ทำการลบข้อมูล
                $.ajax({
                    type: 'POST',
                    url: 'income_delete.php',
                    data: {
                        delete_income_target: true,
                        income_target_id: income_target_id
                    },
                    success: function(response) {
                        // ตรวจสอบคำตอบที่ได้จาก PHP
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            swalWithBootstrapButtons.fire({
                                icon: 'success',
                                title: 'ลบข้อมูลสำเร็จ!',
                                text: 'ข้อมูล รายการรับ ถูกลบออกจากระบบแล้ว',
                            }).then(() => {
                                // ทำการรีเฟรชหน้าหลังจากลบสำเร็จ
                                location.reload();
                            });
                        } else {
                            swalWithBootstrapButtons.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: 'ไม่สามารถลบข้อมูลได้',
                            });
                        }
                    },
                    error: function() {
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


    function openEdit_Deduct_Type_Modal(deduct_type_id, deduct_type) {
        document.getElementById('edit_deduct_type_id').value = deduct_type_id;
        document.getElementById('edit_deduct_type').value = deduct_type;
        $('#editdeduct_typeModal').modal('show');
    }

    function openInsert_Deduct_Target_Modal() {
        $('#insertdeduct_targetModal').modal('show');
    }

    function openEdit_Deduct_Target_Modal(deduct_target_id, prefix_thai, firstname_thai, lastname_thai, deduct_type, company, division, department, section, cost_center, contract_type, pl, position, amount, whole_year, reason) {
        document.getElementById('deduct_target_id').value = deduct_target_id;
        document.getElementById('editcard_id').value = prefix_thai + "" + firstname_thai + "  " + lastname_thai;
        document.getElementById('editdeduct_type').value = deduct_type;
        document.getElementById('editcompany').value = company;
        document.getElementById('editdivision').value = division;
        document.getElementById('editdepartment').value = department;
        document.getElementById('editsection').value = section;
        document.getElementById('editcost_center').value = cost_center;
        document.getElementById('editcontract_type').value = contract_type;
        document.getElementById('editpl').value = pl;
        document.getElementById('editposition').value = position;
        document.getElementById('editamount').value = amount;
        document.getElementById('editwhole_year').value = "";
        document.getElementById('editreason').value = "";
        $('#editdeduct_targetModal').modal('show');
    }

    function confirmDeleteDeduct_target(deduct_target_id) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "delete-swal",
                cancelButton: "edit-swal"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: 'คุณต้องการลบ รายการจ่าย นี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบ!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // ส่ง request ไปยังไฟล์ PHP ที่ทำการลบข้อมูล
                $.ajax({
                    type: 'POST',
                    url: 'deduct_delete.php',
                    data: {
                        delete_deduct_target: true,
                        deduct_target_id: deduct_target_id
                    },
                    success: function(response) {
                        // ตรวจสอบคำตอบที่ได้จาก PHP
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            swalWithBootstrapButtons.fire({
                                icon: 'success',
                                title: 'ลบข้อมูลสำเร็จ!',
                                text: 'ข้อมูล รายการจ่าย ถูกลบออกจากระบบแล้ว',
                            }).then(() => {
                                // ทำการรีเฟรชหน้าหลังจากลบสำเร็จ
                                location.reload();
                            });
                        } else {
                            swalWithBootstrapButtons.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: 'ไม่สามารถลบข้อมูลได้',
                            });
                        }
                    },
                    error: function() {
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




    function openCircle_Setting_Modal() {
        $('#insertcircleModal').modal('show');
    }

    function openEdit_Circle_Setting_Modal(circle_id, card_id, circle, prefix_thai, firstname_thai, lastname_thai, company, division, department, section, cost_center, contract_type, pl, position) {
        // กำหนดค่าใน input fields
        document.getElementById('circle_id').value = circle_id;
        document.getElementById('card_id').value = card_id;
        document.getElementById('circle_set_id').value = "";
        document.getElementById('name').value = prefix_thai + "" + firstname_thai + "  " + lastname_thai;
        document.getElementById('editcompany').value = company;
        document.getElementById('editdivision').value = division;
        document.getElementById('editdepartment').value = department;
        document.getElementById('editsection').value = section;
        document.getElementById('editcost_center').value = cost_center;
        document.getElementById('editcontract_type').value = contract_type;
        document.getElementById('editpl').value = pl;
        document.getElementById('editposition').value = position;
        // เปิด modal form
        $('#editcircleModal').modal('show');
        console.log(position);
    }

    function openSplit_Setting_Modal() {
        $('#insertsplitModal').modal('show');
    }

    function openEdit_Split_Setting_Modal(split_id, card_id, split, prefix_thai, firstname_thai, lastname_thai, company, division, department, section, cost_center, contract_type, pl, position) {
        // กำหนดค่าใน input fields
        document.getElementById('split_id').value = split_id;
        document.getElementById('card_id').value = card_id;
        document.getElementById('split_set_id').value = "";
        document.getElementById('name').value = prefix_thai + "" + firstname_thai + "  " + lastname_thai;
        document.getElementById('editcompany').value = company;
        document.getElementById('editdivision').value = division;
        document.getElementById('editdepartment').value = department;
        document.getElementById('editsection').value = section;
        document.getElementById('editcost_center').value = cost_center;
        document.getElementById('editcontract_type').value = contract_type;
        document.getElementById('editpl').value = pl;
        document.getElementById('editposition').value = position;
        // เปิด modal form
        $('#editsplitModal').modal('show');
        console.log(split);
    }

    function openClosing_date_Setting_Modal() {
        $('#insertclosing_dateModal').modal('show');
    }

    function openEdit_Closing_date_Setting_Modal(closing_date_id, card_id, closing_date, prefix_thai, firstname_thai, lastname_thai, company, division, department, section, cost_center, contract_type, pl, position) {
        // กำหนดค่าใน input fields
        document.getElementById('closing_date_id').value = closing_date_id;
        document.getElementById('card_id').value = card_id;
        document.getElementById('closing_date_set_id').value = "";
        document.getElementById('name').value = prefix_thai + "" + firstname_thai + "  " + lastname_thai;
        document.getElementById('editcompany').value = company;
        document.getElementById('editdivision').value = division;
        document.getElementById('editdepartment').value = department;
        document.getElementById('editsection').value = section;
        document.getElementById('editcost_center').value = cost_center;
        document.getElementById('editcontract_type').value = contract_type;
        document.getElementById('editpl').value = pl;
        document.getElementById('editposition').value = position;
        // เปิด modal form
        $('#editclosing_dateModal').modal('show');
        console.log(openEdit_Closing_date_Setting_Modal);
    }

    function openPay_holiday_Setting_Modal() {
        $('#insertpay_holidayModal').modal('show');
    }

    function openEdit_Pay_holiday_Setting_Modal(pay_holiday_id, card_id, pay_holiday, prefix_thai, firstname_thai, lastname_thai, company, division, department, section, cost_center, contract_type, pl, position) {
        // กำหนดค่าใน input fields
        document.getElementById('pay_holiday_id').value = pay_holiday_id;
        document.getElementById('card_id').value = card_id;
        document.getElementById('pay_holiday_set_id').value = "";
        document.getElementById('name').value = prefix_thai + "" + firstname_thai + "  " + lastname_thai;
        document.getElementById('editcompany').value = company;
        document.getElementById('editdivision').value = division;
        document.getElementById('editdepartment').value = department;
        document.getElementById('editsection').value = section;
        document.getElementById('editcost_center').value = cost_center;
        document.getElementById('editcontract_type').value = contract_type;
        document.getElementById('editpl').value = pl;
        document.getElementById('editposition').value = position;
        // เปิด modal form
        $('#editpay_holidayModal').modal('show');
        console.log(pay_holiday);
    }

    function openSocial_securityy_Setting_Modal() {
        $('#insertsocial_securityModal').modal('show');
    }

    function openEdit_Social_security_Setting_Modal(social_security_id, card_id, social_security, prefix_thai, firstname_thai, lastname_thai, company, division, department, section, cost_center, contract_type, pl, position) {
        // กำหนดค่าใน input fields
        document.getElementById('social_security_id').value = social_security_id;
        document.getElementById('card_id').value = card_id;
        document.getElementById('editsocial_security').value = social_security;
        document.getElementById('name').value = prefix_thai + "" + firstname_thai + "  " + lastname_thai;
        document.getElementById('editcompany').value = company;
        document.getElementById('editdivision').value = division;
        document.getElementById('editdepartment').value = department;
        document.getElementById('editsection').value = section;
        document.getElementById('editcost_center').value = cost_center;
        document.getElementById('editcontract_type').value = contract_type;
        document.getElementById('editpl').value = pl;
        document.getElementById('editposition').value = position;
        // เปิด modal form
        $('#editsocial_securityModal').modal('show');
        console.log(social_security);
    }

    function openForm_Setting_Modal() {
        $('#insertformModal').modal('show');
    }

    function openEdit_Form_Setting_Modal(form_id, card_id, form, prefix_thai, firstname_thai, lastname_thai, company, division, department, section, cost_center, contract_type, pl, position) {
        // กำหนดค่าใน input fields
        document.getElementById('form_id').value = form_id;
        document.getElementById('card_id').value = card_id;
        document.getElementById('form_set_id').value = "";
        document.getElementById('name').value = prefix_thai + "" + firstname_thai + "  " + lastname_thai;
        document.getElementById('editcompany').value = company;
        document.getElementById('editdivision').value = division;
        document.getElementById('editdepartment').value = department;
        document.getElementById('editsection').value = section;
        document.getElementById('editcost_center').value = cost_center;
        document.getElementById('editcontract_type').value = contract_type;
        document.getElementById('editpl').value = pl;
        document.getElementById('editposition').value = position;
        // เปิด modal form
        $('#editformModal').modal('show');
        console.log(form);
    }

    function openNotification_Setting_Modal() {
        $('#insertnotificationModal').modal('show');
    }

    function openEdit_Notification_Setting_Modal(notification_id, card_id, notification_certificate, notification_medical, prefix_thai, firstname_thai, lastname_thai, company, division, department, section, cost_center, contract_type, pl, position) {
        // กำหนดค่าใน input fields
        document.getElementById('notification_id').value = notification_id;
        document.getElementById('card_id').value = card_id;
        document.getElementById('notification_certificate_id').value = "";
        document.getElementById('notification_medical_id').value = "";
        document.getElementById('name').value = prefix_thai + "" + firstname_thai + "  " + lastname_thai;
        document.getElementById('editcompany').value = company;
        document.getElementById('editdivision').value = division;
        document.getElementById('editdepartment').value = department;
        document.getElementById('editsection').value = section;
        document.getElementById('editcost_center').value = cost_center;
        document.getElementById('editcontract_type').value = contract_type;
        document.getElementById('editpl').value = pl;
        document.getElementById('editposition').value = position;
        // เปิด modal form
        $('#editnotificationModal').modal('show');
        console.log(notification);
    }


    //////--------------------------------------------------------------///////
    function confirmDeleteCircle(circle_id) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "delete-swal",
                cancelButton: "edit-swal"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: 'คุณต้องการลบ รอบวันที่ นี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบ!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // ส่ง request ไปยังไฟล์ PHP ที่ทำการลบข้อมูล
                $.ajax({
                    type: 'POST',
                    url: 'setting_payment_circle_delete.php',
                    data: {
                        delete_circle: true,
                        circle_id: circle_id
                    },
                    success: function(response) {
                        // ตรวจสอบคำตอบที่ได้จาก PHP
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            swalWithBootstrapButtons.fire({
                                icon: 'success',
                                title: 'ลบข้อมูลสำเร็จ!',
                                text: 'ข้อมูล รอบวันที่ ถูกลบออกจากระบบแล้ว',
                            }).then(() => {
                                // ทำการรีเฟรชหน้าหลังจากลบสำเร็จ
                                location.reload();
                            });
                        } else {
                            swalWithBootstrapButtons.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: 'ไม่สามารถลบข้อมูลได้',
                            });
                        }
                    },
                    error: function() {
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

    function confirmDeleteSplit(split_id) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "delete-swal",
                cancelButton: "edit-swal"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: 'คุณต้องการลบ งวดการจ่าย นี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบ!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // ส่ง request ไปยังไฟล์ PHP ที่ทำการลบข้อมูล
                $.ajax({
                    type: 'POST',
                    url: 'setting_payment_split_delete.php',
                    data: {
                        delete_split: true,
                        split_id: split_id
                    },
                    success: function(response) {
                        // ตรวจสอบคำตอบที่ได้จาก PHP
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            swalWithBootstrapButtons.fire({
                                icon: 'success',
                                title: 'ลบข้อมูลสำเร็จ!',
                                text: 'ข้อมูล งวดการจ่าย ถูกลบออกจากระบบแล้ว',
                            }).then(() => {
                                // ทำการรีเฟรชหน้าหลังจากลบสำเร็จ
                                location.reload();
                            });
                        } else {
                            swalWithBootstrapButtons.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: 'ไม่สามารถลบข้อมูลได้',
                            });
                        }
                    },
                    error: function() {
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

    function confirmDeleteClosing_date(closing_date_id) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "delete-swal",
                cancelButton: "edit-swal"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: 'คุณต้องการลบ วันปิดงวด นี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบ!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // ส่ง request ไปยังไฟล์ PHP ที่ทำการลบข้อมูล
                $.ajax({
                    type: 'POST',
                    url: 'setting_payment_set_closing_date_delete.php',
                    data: {
                        delete_closing_date: true,
                        closing_date_id: closing_date_id
                    },
                    success: function(response) {
                        // ตรวจสอบคำตอบที่ได้จาก PHP
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            swalWithBootstrapButtons.fire({
                                icon: 'success',
                                title: 'ลบข้อมูลสำเร็จ!',
                                text: 'ข้อมูล วันปิดงวด ถูกลบออกจากระบบแล้ว',
                            }).then(() => {
                                // ทำการรีเฟรชหน้าหลังจากลบสำเร็จ
                                location.reload();
                            });
                        } else {
                            swalWithBootstrapButtons.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: 'ไม่สามารถลบข้อมูลได้',
                            });
                        }
                    },
                    error: function() {
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

    function confirmDeletePay_holiday(pay_holiday_id) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "delete-swal",
                cancelButton: "edit-swal"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: 'คุณต้องการลบ รายได้พนักงานในวันหยุด นี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบ!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // ส่ง request ไปยังไฟล์ PHP ที่ทำการลบข้อมูล
                $.ajax({
                    type: 'POST',
                    url: 'setting_payment_holidays_delete.php',
                    data: {
                        delete_pay_holiday: true,
                        pay_holiday_id: pay_holiday_id
                    },
                    success: function(response) {
                        // ตรวจสอบคำตอบที่ได้จาก PHP
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            swalWithBootstrapButtons.fire({
                                icon: 'success',
                                title: 'ลบข้อมูลสำเร็จ!',
                                text: 'ข้อมูล รายได้พนักงานในวันหยุด ถูกลบออกจากระบบแล้ว',
                            }).then(() => {
                                // ทำการรีเฟรชหน้าหลังจากลบสำเร็จ
                                location.reload();
                            });
                        } else {
                            swalWithBootstrapButtons.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: 'ไม่สามารถลบข้อมูลได้',
                            });
                        }
                    },
                    error: function() {
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

    function confirmDeleteSocial_security(social_security_id) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "delete-swal",
                cancelButton: "edit-swal"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: 'คุณต้องการลบ ประกันสังคม นี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบ!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // ส่ง request ไปยังไฟล์ PHP ที่ทำการลบข้อมูล
                $.ajax({
                    type: 'POST',
                    url: 'setting_payment_social_security_delete.php',
                    data: {
                        delete_social_security: true,
                        social_security_id: social_security_id
                    },
                    success: function(response) {
                        // ตรวจสอบคำตอบที่ได้จาก PHP
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            swalWithBootstrapButtons.fire({
                                icon: 'success',
                                title: 'ลบข้อมูลสำเร็จ!',
                                text: 'ข้อมูล ประกันสังคม ถูกลบออกจากระบบแล้ว',
                            }).then(() => {
                                // ทำการรีเฟรชหน้าหลังจากลบสำเร็จ
                                location.reload();
                            });
                        } else {
                            swalWithBootstrapButtons.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: 'ไม่สามารถลบข้อมูลได้',
                            });
                        }
                    },
                    error: function() {
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

    function confirmDeleteForm(form_id) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "delete-swal",
                cancelButton: "edit-swal"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: 'คุณต้องการลบ แบบฟอร์ม นี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบ!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // ส่ง request ไปยังไฟล์ PHP ที่ทำการลบข้อมูล
                $.ajax({
                    type: 'POST',
                    url: 'setting_payment_form_delete.php',
                    data: {
                        delete_form: true,
                        form_id: form_id
                    },
                    success: function(response) {
                        // ตรวจสอบคำตอบที่ได้จาก PHP
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            swalWithBootstrapButtons.fire({
                                icon: 'success',
                                title: 'ลบข้อมูลสำเร็จ!',
                                text: 'ข้อมูล แบบฟอร์ม ถูกลบออกจากระบบแล้ว',
                            }).then(() => {
                                // ทำการรีเฟรชหน้าหลังจากลบสำเร็จ
                                location.reload();
                            });
                        } else {
                            swalWithBootstrapButtons.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: 'ไม่สามารถลบข้อมูลได้',
                            });
                        }
                    },
                    error: function() {
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

    function confirmDeleteNotification(notification_id) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "delete-swal",
                cancelButton: "edit-swal"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: 'คุณต้องการลบ การแจ้งเตือน นี้หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ลบ!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // ส่ง request ไปยังไฟล์ PHP ที่ทำการลบข้อมูล
                $.ajax({
                    type: 'POST',
                    url: 'setting_payment_notification_delete.php',
                    data: {
                        delete_notification: true,
                        notification_id: notification_id
                    },
                    success: function(response) {
                        // ตรวจสอบคำตอบที่ได้จาก PHP
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            swalWithBootstrapButtons.fire({
                                icon: 'success',
                                title: 'ลบข้อมูลสำเร็จ!',
                                text: 'ข้อมูล การแจ้งเตือน ถูกลบออกจากระบบแล้ว',
                            }).then(() => {
                                // ทำการรีเฟรชหน้าหลังจากลบสำเร็จ
                                location.reload();
                            });
                        } else {
                            swalWithBootstrapButtons.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: 'ไม่สามารถลบข้อมูลได้',
                            });
                        }
                    },
                    error: function() {
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

    //--------------------------------------------------------------//
    function swalAddAlert1() {
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        Toast.fire({
            icon: "success",
            title: "บันทึกข้อมูลสำเร็จ"
        });
    };
</script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        var companySelect = document.getElementById("company");
        var divisionSelect = document.getElementById("division");
        var departmentSelect = document.getElementById("department");
        var sectionSelect = document.getElementById("section");
        var cost_centerSelect = document.getElementById("cost_center");
        var contract_typeSelect = document.getElementById("contract_type");
        var plSelect = document.getElementById("pl");
        var positionSelect = document.getElementById("position");
        var employeeSelect = document.getElementById("employee");

        companySelect.addEventListener("change", function() {
            var selectedCompany = companySelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('division').innerHTML;

                    // Update departmentSelect with the new options
                    divisionSelect.innerHTML = newOptions;
                    console.log(selectedCompany)
                }
            };

            // Send selectedDivision as POST data
            xhr.send("company=" + selectedCompany);
        });

        companySelect.addEventListener("change", function() {
            var selectedCompany = companySelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('department').innerHTML;

                    // Update departmentSelect with the new options
                    departmentSelect.innerHTML = newOptions;
                    // console.log(newOptions)
                }
            };

            // Send selectedDivision as POST data
            xhr.send("company=" + selectedCompany);
        });

        companySelect.addEventListener("change", function() {
            var selectedCompany = companySelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('section').innerHTML;

                    // Update departmentSelect with the new options
                    sectionSelect.innerHTML = newOptions;
                    // console.log(newOptions)
                }
            };

            // Send selectedDivision as POST data
            xhr.send("company=" + selectedCompany);
        });

        companySelect.addEventListener("change", function() {
            var selectedCompany = companySelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('cost_center').innerHTML;

                    // Update departmentSelect with the new options
                    cost_centerSelect.innerHTML = newOptions;
                    // console.log(newOptions)
                }
            };

            // Send selectedDivision as POST data
            xhr.send("company=" + selectedCompany);
        });


        companySelect.addEventListener("change", function() {
            var selectedCompany = companySelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('employee').innerHTML;

                    // Update departmentSelect with the new options
                    employeeSelect.innerHTML = newOptions;
                    console.log(selectedCompany)

                }
            };

            // Send selectedDivision as POST data
            xhr.send("company=" + selectedCompany);
        });



        // -------------------------------------------------------------------------------------------
        divisionSelect.addEventListener("change", function() {
            var selectedDivision = divisionSelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('department').innerHTML;

                    // Update departmentSelect with the new options
                    departmentSelect.innerHTML = newOptions;
                    console.log(selectedDivision)
                }
            };

            // Send selectedDivision as POST data
            xhr.send("division=" + selectedDivision);
        });

        divisionSelect.addEventListener("change", function() {
            var selectedDivision = divisionSelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('section').innerHTML;

                    // Update departmentSelect with the new options
                    sectionSelect.innerHTML = newOptions;
                    // console.log(newOptions)
                }
            };

            // Send selectedDivision as POST data
            xhr.send("division=" + selectedDivision);
        });

        divisionSelect.addEventListener("change", function() {
            var selectedDivision = divisionSelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('cost_center').innerHTML;

                    // Update departmentSelect with the new options
                    cost_centerSelect.innerHTML = newOptions;
                    // console.log(newOptions)
                }
            };

            // Send selectedDivision as POST data
            xhr.send("division=" + selectedDivision);
        });


        divisionSelect.addEventListener("change", function() {
            var selectedDivision = divisionSelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('employee').innerHTML;

                    // Update departmentSelect with the new options
                    employeeSelect.innerHTML = newOptions;
                    // console.log(newOptions)
                }
            };

            // Send selectedDivision as POST data
            xhr.send("division=" + selectedDivision);
        });


        // -------------------------------------------------------------------------------------------
        departmentSelect.addEventListener("change", function() {
            var selectedDepartment = departmentSelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('section').innerHTML;
                    // console.log(newOptions)

                    // Update departmentSelect with the new options
                    sectionSelect.innerHTML = newOptions;
                }
            };

            // Send selectedDivision as POST data
            xhr.send("department=" + selectedDepartment);
        });

        departmentSelect.addEventListener("change", function() {
            var selectedDepartment = departmentSelect.value;
            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('cost_center').innerHTML;
                    // console.log(newOptions)

                    // Update departmentSelect with the new options
                    cost_centerSelect.innerHTML = newOptions;
                }
            };
            // Send selectedDivision as POST data
            xhr.send("department=" + selectedDepartment);
        });

        departmentSelect.addEventListener("change", function() {
            var selectedDepartment = departmentSelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('employee').innerHTML;
                    // console.log(newOptions)

                    // Update departmentSelect with the new options
                    employeeSelect.innerHTML = newOptions;
                }
            };
            // Send selectedDivision as POST data
            xhr.send("department=" + selectedDepartment);
            // console.log(selectedDepartment)
        });


        // -------------------------------------------------------------------------------------------       
        sectionSelect.addEventListener("change", function() {
            var selectedSection = sectionSelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('cost_center').innerHTML;

                    // Update departmentSelect with the new options
                    cost_centerSelect.innerHTML = newOptions;
                    // console.log(newOptions)
                }
            };

            // Send selectedDivision as POST data
            xhr.send("section=" + selectedSection);
            // console.log(selectedSection)
        });

        sectionSelect.addEventListener("change", function() {
            var selectedSection = sectionSelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('employee').innerHTML;

                    // Update departmentSelect with the new options
                    employeeSelect.innerHTML = newOptions;
                    // console.log(newOptions)
                }
            };

            // Send selectedDivision as POST data
            xhr.send("section=" + selectedSection);
            // console.log(selectedSection)
        });


        // -------------------------------------------------------------------------------------------

        cost_centerSelect.addEventListener("change", function() {
            var selectedCost_center = cost_centerSelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('employee').innerHTML;

                    // Update departmentSelect with the new options
                    employeeSelect.innerHTML = newOptions;
                    // console.log(newOptions)
                }
            };

            // Send selectedDivision as POST data
            xhr.send("cost_center=" + selectedCost_center);
            // console.log(selectedCost_center)
        });



        // -------------------------------------------------------------------------------------------
        employeeSelect.addEventListener("change", function() {
            var selectedEmployee = employeeSelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('company').innerHTML;

                    // Update departmentSelect with the new options
                    companySelect.innerHTML = newOptions;
                    console.log(newOptions)
                }
            };

            // Send selectedDivision as POST data
            xhr.send("employee=" + selectedEmployee);
        });

        employeeSelect.addEventListener("change", function() {
            var selectedEmployee = employeeSelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('division').innerHTML;

                    // Update departmentSelect with the new options
                    divisionSelect.innerHTML = newOptions;
                    // console.log(newOptions)
                }
            };

            // Send selectedDivision as POST data
            xhr.send("employee=" + selectedEmployee);
        });

        employeeSelect.addEventListener("change", function() {
            var selectedEmployee = employeeSelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('department').innerHTML;

                    // Update departmentSelect with the new options
                    departmentSelect.innerHTML = newOptions;
                    // console.log(newOptions)
                }
            };

            // Send selectedDivision as POST data
            xhr.send("employee=" + selectedEmployee);
        });

        employeeSelect.addEventListener("change", function() {
            var selectedEmployee = employeeSelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('section').innerHTML;

                    // Update departmentSelect with the new options
                    sectionSelect.innerHTML = newOptions;
                    // console.log(newOptions)
                }
            };

            // Send selectedDivision as POST data
            xhr.send("employee=" + selectedEmployee);
        });

        employeeSelect.addEventListener("change", function() {
            var selectedEmployee = employeeSelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('cost_center').innerHTML;

                    // Update departmentSelect with the new options
                    cost_centerSelect.innerHTML = newOptions;
                    // console.log(newOptions)
                }
            };

            // Send selectedDivision as POST data
            xhr.send("employee=" + selectedEmployee);
        });

        employeeSelect.addEventListener("change", function() {
            var selectedEmployee = employeeSelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('contract_type').innerHTML;

                    // Update departmentSelect with the new options
                    contract_typeSelect.innerHTML = newOptions;
                    // console.log(newOptions)
                }
            };

            // Send selectedDivision as POST data
            xhr.send("employee=" + selectedEmployee);
        });

        employeeSelect.addEventListener("change", function() {
            var selectedEmployee = employeeSelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('pl').innerHTML;

                    // Update departmentSelect with the new options
                    plSelect.innerHTML = newOptions;
                    // console.log(newOptions)
                }
            };

            // Send selectedDivision as POST data
            xhr.send("employee=" + selectedEmployee);
        });

        employeeSelect.addEventListener("change", function() {
            var selectedEmployee = employeeSelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the response as HTML
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');

                    // Get the new department options
                    var newOptions = doc.getElementById('position').innerHTML;

                    // Update departmentSelect with the new options
                    positionSelect.innerHTML = newOptions;
                    // console.log(newOptions)
                }
            };

            // Send selectedDivision as POST data
            xhr.send("employee=" + selectedEmployee);
        });
    });

    // เพิ่มเหตุการณ์ onChange สำหรับ Dropdown
    employeeSelect.addEventListener("change", function() {
        var selectedPositionId = employeeSelect.value;

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "setting_payment_circle.php", true); // เปลี่ยนเป็น URL ของไฟล์ PHP ที่ใช้เก็บค่า position_id ใน Session
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // รับการตอบกลับจากเซิร์ฟเวอร์ (ถ้ามี)
                console.log(xhr.responseText);
            }
        };

        // ส่งค่า position_id ที่เลือกใน Dropdown ไปยังเซิร์ฟเวอร์
        xhr.send("selectedPositionId=" + selectedPositionId);
    });


    $(document).ready(function() {
        // เมื่อคลิกปุ่ม "อัพโหลดไฟล์"
        $('#uploadBtn').click(function() {
            // เรียกใช้ input type="file" ที่ซ่อนไว้
            $('#edit_file').click();
        });

        // เมื่อมีการเลือกไฟล์
        $('#edit_file').change(function() {
            var fileName = $(this).val().split('\\').pop(); // ดึงชื่อไฟล์ที่ถูกเลือก
            // แสดงชื่อไฟล์ที่เลือก
            $(this).next('.custom-file-label').html(fileName);
        });
    });
</script>



</html>