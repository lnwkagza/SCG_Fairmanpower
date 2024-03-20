jQuery(() => {
    console.log("ผู้ตรวจสอบคือ: " + cardID);

});

$('table.displayTB').DataTable({
    autoWidth: true,
    scrollX: true,
    responsive: true,
    language: {
        search: "ค้นหา:",
        info: "กำลังแสดงหน้าที่ _PAGE_ จาก _PAGES_ หน้า",
        lengthMenu: "แสดง _MENU_ รายการ",
        paginate: {
            first: "หน้าแรก",
            previous: "ก่อนหน้า",
            next: "ถัดไป",
            last: "หน้าสุดท้าย"
        },
        zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
        infoEmpty: "ไม่มีข้อมูลที่แสดง",
        infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)"
    },
    columnDefs: [
        { responsivePriority: 1, targets: 0 },
        { responsivePriority: 2, targets: -1 }
    ]
});

$('.approval_status_selection').on('change', function () {
    let selectedValue = $(this).val();
    let closestTr = $(this).closest('tr');
    let closestSelected = closestTr.find('.approval_status_selection').val();
    let cardId = closestTr.find('td:first').text();

    let shift_id = closestTr.find('.tb_shift_id').text();

    let employeeName = closestTr.find('.tb_employee_name').text();
    let start_date = closestTr.find('.tb_start_date').text();
    let end_date = closestTr.find('.tb_end_date').text();

    console.log('shift_id: ',shift_id.trim());
    console.log(cardId);

    if (closestSelected === '2') {
        console.log('ไม่อนุมัติ');

        Swal.fire({
            title: 'ไม่อนุมัติ',
            input: 'textarea',
            inputLabel: 'กรุณากรอกรายละเอียดการไม่อนุมัติ',
            inputPlaceholder: 'กรอกรายละเอียด...',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก',
            allowOutsideClick: true,
            showLoaderOnConfirm: true,
            preConfirm: (reason) => {
                if (!reason) {
                    Swal.showValidationMessage('กรุณากรอกรายละเอียดการไม่อนุมัติ');
                } else {
                    return reason;
                }
            }

        }).then((result) => {
            // หากผู้ใช้กด "ยืนยัน"
            if (result.isConfirmed) {
                $.ajax({
                    url: '../gps/gps_update_approval.php',
                    method: 'POST',
                    data: {
                        shift_id: shift_id,
                        card_id: cardId,
                        approval_id: selectedValue,
                        start_date: start_date,
                        end_date: end_date,
                        approvarID: cardID,
                        reason: result.value
                    },
                    success: function (response) {
                        console.log(response);
                        Swal.fire({
                            icon: "success",
                            title: "เปลี่ยนสถานะแล้ว",
                            text: "คำร้องของ: " + employeeName,
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
                        // ทำการอัปเดต <select> ใน DOM หลังจากเปลี่ยนสถานะแล้ว
                        closestTr.find('.approval_status_selection').val(selectedValue);
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            } else if (result.isDismissed) {
                // หากผู้ใช้กด "ยกเลิก" ให้ทำการเปลี่ยน select เป็น "รอการตรวจสอบ"
                closestTr.find('.approval_status_selection').val(5);
            }
        });
    } else {
        // กรณีที่ไม่ใช่[ไม่อนุมัติ]
        $.ajax({
            url: '../gps/gps_update_approval.php',
            method: 'POST',
            data: {
                shift_id: shift_id,
                card_id: cardId,
                approval_id: selectedValue,
                start_date: start_date,
                end_date: end_date,
                approvarID: cardID

            },
            success: function (response) {
                console.log(response);
                Swal.fire({
                    icon: "success",
                    title: "เปลี่ยนสถานะแล้ว",
                    text: "คำร้องของ: " + employeeName,
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
                // ทำการอัปเดต <select> ใน DOM หลังจากเปลี่ยนสถานะแล้ว
                closestTr.find('.approval_status_selection').val(selectedValue);
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }
});

$('.btn-show-details').on('click', function () {
    let employeeID = $(this).closest('tr').find('.tb_employee_id').text();
    console.log('TEST BTN');
    // ส่งข้อมูลไปยัง Modal ด้วย Ajax
    $('#employeeDetailsModal').modal('show');
});

function CoordsTo_Object(coordinatesString) {
    // แยกค่าพิกัดตาม comma (,)
    const [latitude, longitude] = coordinatesString.split(',');

    // สร้างอ็อบเจ็กต์ที่เก็บค่าพิกัด
    const coordinatesObject = {
        lat: parseFloat(latitude),
        lng: parseFloat(longitude),
    };

    return coordinatesObject;
}

$('button[type=button][name=checkIN_coords_btn]').on('click', function () {

    // หา modal ที่ใกล้ที่สุดในลำดับของปุ่ม "Check In"
    let modal = $(this).closest(".modal");

    // ใน modal นั้น หาปุ่มที่มี name เป็น "checkIN_coords"
    let checkIN_Coords = modal.find("input[name='checkIN_coords']").val();
    let checkOUT_Coords = modal.find("input[name='checkOUT_coords']").val();

    let checkIN_Coords_array = checkIN_Coords.split(',');
    let checkOUT_Coords_array = checkOUT_Coords.split(',');

    let checkIN_Name = modal.find("input[name='coords_in_name']").val();
    let checkOUT_Name = modal.find("input[name='coords_out_name']").val();

    let coords_range = modal.find("input[name='coords_range']").val();

    let check_in_obj = CoordsTo_Object(checkIN_Coords);
    let check_out_obj = CoordsTo_Object(checkOUT_Coords);

    //เอาไว้ใช้แยกรัศมีของพิกัดกัน
    let check_in_json = JSON.stringify(check_in_obj);
    let check_out_json = JSON.stringify(check_out_obj);

    let popupMap =
        '<div style="font-weight: bold; font-size: 15px;">พิกัดสถานที่เข้าและออกงาน</div><br>' +
        '<div class="center" id="display-map"></div><br>' +

        '<div class="row modal-dialog modal-dialog-scrollable">' +

        '<div class="col-md-6">' +

        '<div class="form-group">' +
        '<label for="work_in_location">สถานที่เข้างาน</label>' +
        '<input id="work_in_location" type="text" name="work_in_location" class="form-control" value="" readonly>' +
        '</div>' +

        '<div class="form-group">' +
        '<label for="work_in_lat">ละติจูด</label>' +
        '<input id="work_in_lat" type="text" name="work_in_lat" class="form-control" value="" readonly>' +
        '</div>' +

        '<div class="form-group">' +
        '<label for="work_in_lng">ลองติจูด</label>' +
        '<input id="work_in_lng" type="text" name="work_in_lng" class="form-control" value="" readonly>' +
        '</div>' +

        '</div>' +

        '<div class="col-md-6">' +

        '<div class="form-group">' +
        '<label for="work_out_location">สถานที่ออกงาน</label>' +
        '<input id="work_out_location" type="text" name="work_out_location" class="form-control" value="" readonly>' +
        '</div>' +

        '<div class="form-group">' +
        '<label for="work_out_lat">ละติจูด</label>' +
        '<input id="work_out_lat" type="text" name="work_out_lat" class="form-control" value="" readonly>' +
        '</div>' +

        '<div class="form-group">' +
        '<label for="work_out_lng">ลองติจูด</label>' +
        '<input id="work_out_lng" type="text" name="work_out_lng" class="form-control" value="" readonly>' +
        '</div>' +

        '</div>' +

        '</div>'

    Swal.fire({
        html: popupMap,
        padding: '2em',
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: 'ปิด',
        cancelButtonColor: '#5c636a',
        customClass: {
            cancelButtonText: 'swal2-cancel'
        },
        didOpen: () => {

            let map = L.map('display-map').setView(check_in_obj, 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            if (check_in_json === check_out_json) {

                console.log("พิกัดเดียวกัน.");

                let circleP = L.circle(check_in_obj, {
                    color: '#1E90FF',
                    fillColor: '#82EEFD',
                    fillOpacity: 0.4,
                    radius: coords_range
                }).addTo(map);

                circleP.bindPopup("จุดเข้าและออกงาน.").openPopup();

            } else {

                console.log("พิกัดแยกกัน.");

                let circleP_in = L.circle(check_in_obj, {
                    color: '#1E90FF',
                    fillColor: '#82EEFD',
                    fillOpacity: 0.4,
                    radius: coords_range
                }).addTo(map);

                let circleP_out = L.circle(check_out_obj, {
                    color: '#1E90FF',
                    fillColor: '#82EEFD',
                    fillOpacity: 0.4,
                    radius: coords_range
                }).addTo(map);

                circleP_in.bindPopup("จุดเข้างาน.").openPopup();
                circleP_out.bindPopup("จุดออกงาน.").openPopup();


            }

            //ชื่อสถานที่เข้าและออก
            $("input[name='work_in_location']").val(checkIN_Name);
            $("input[name='work_out_location']").val(checkOUT_Name);

            //ละติจูดและลองติจูดเข้างาน
            $("input[name='work_in_lat']").val(checkIN_Coords_array[0]);
            $("input[name='work_in_lng']").val(checkIN_Coords_array[1]);

            //ละติจูดและลองติจูดเข้างาน
            $("input[name='work_out_lat']").val(checkOUT_Coords_array[0]);
            $("input[name='work_out_lng']").val(checkOUT_Coords_array[1]);

        }


    })

    
});

$('button[type=button][name=checkOUT_coords_btn]').on('click', function () {
    // หา modal ที่ใกล้ที่สุดในลำดับของปุ่ม "Check In"
    let modal = $(this).closest(".modal");

    // ใน modal นั้น หาปุ่มที่มี name เป็น "checkIN_coords"
    let checkINButton = modal.find("input[name='coords_str']").val();

    // ทำสิ่งที่คุณต้องการกับปุ่มนี้ เช่นแสดงข้อมูลหรืออื่น ๆ
    console.log("Check Out Button:", checkINButton);
});
