var check_in_coordinates = {};
var check_out_coordinates = {};
var check_both_coordinates = {};

var btnIN_flag = Boolean;
var btnOUT_flag = Boolean;

var input_radio_coord_type = $('input[type=radio][name=gps_type]');
var input_radio_shift_time = $('input[type=radio][name=shift_time]');
var input_radio_shift_group = $('input[type=radio][name=shift_group]');

var employeesList = [];

var map = L.map('map');
map.setView([8.098955, 99.672368], 15);

var baseLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 20
});

var markersGroup = L.layerGroup();

baseLayer.addTo(map);
markersGroup.addTo(map);

//on-load webpage
jQuery(() => {
    console.info("DOM Loaded.");

    // setupMap();
    $('#map-container').hide();
    $('#shift_date-container').hide();
    $('#shift_group-container').hide();
    $('#shift_date-container').show();

    //ติ๊ก radio ให้
    $('input[name="shift_group"][value="option1"]').prop('checked', true);
    $('input[name="shift_time"][value="option2"]').prop('checked', true);
    

});

$(".datepicker").datepicker({
    dateFormat: "dd-mm-yy", // กำหนดรูปแบบวันที่
    monthNamesShort: ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"],
    dayNamesMin: ["อา.", "จ.", "อ.", "พ.", "พฤ.", "ศ.", "ส."], // กำหนดชื่อวันในสัปดาห์ (ของโค้ดนี้จะใช้ชื่อย่อ)
    changeMonth: true, // ให้เลือกเปลี่ยนเดือนได้
    changeYear: true, // ให้เลือกเปลี่ยนปีได้
    regional: "th", // ตั้งค่า locale เป็นภาษาไทย
    minDate: 0, // ไม่สามารถเลือกวันที่ผ่านมาได้

});

$('#employeesList').select2({
    minimumInputLength: 0,
    tags: false
});

$('#inspectorList').select2({
    minimumInputLength: 0,
    tags: false
});

function coords_str(coords) {
    let { lat, lng } = coords;
    let result = lat + "," + lng;
    return result;
};

function Clear_marker() {

    map.off('click');
    markersGroup.clearLayers();

    Object.keys(check_in_coordinates).forEach(key => {
        delete check_in_coordinates[key]
    })

    Object.keys(check_out_coordinates).forEach(key => {
        delete check_out_coordinates[key]
    });

    Object.keys(check_both_coordinates).forEach(key => {
        delete check_both_coordinates[key]
    });

    $('#mark-check-in-button').attr("disabled", false);
    $('#mark-check-out-button').attr("disabled", false);
    $('#mark-check-both-button').attr("disabled", false);

    $('span[name=coords_name]').empty();

    btnIN_flag = false;
    btnOUT_flag = false;

    return;
};

function scrollToElement(elementID) {
    $('html, body').animate({
        scrollTop: $('#' + elementID).offset().top
    }, 1000);
}

$('input[type=radio][name=shift_group]').on("change", function () {

    employeesList = [];
    updateEmployeeList();

    let selectedValue = $('input[type=radio][name=shift_group]:checked').val();

    function updateEmployeeList() {
        let employeeList = $('#employees');
        employeeList.empty();

        //วนแสดงชื่อพนักงาน
        $.each(employeesList, function (index, employee) {
            let listItem = $('<li class="employeeUL"><button type="button" class="btn btn-danger remove-employee" data-index="' + index + '">ลบ</button> ' + employee.scg_id + ' (' + employee.name + ') </li>');
            listItem.attr('data-employee-id', employee.card_id); // เพิ่ม data-employee-id
            employeeList.append(listItem);
        });

        //ปุ่มลบหน้าพนักงาน
        $('.remove-employee').on('click', function () {
            let index = $(this).data('index');
            employeesList.splice(index, 1);
            updateEmployeeList();
        });
    }

    function addEmployee(employeeName, employeeID, employeeShowID) {
        // เช็คว่าตัวเลือกไม่ซ้ำกัน
        var isDuplicate = employeesList.some(function (existingEmployee) {
            return existingEmployee.card_id === employeeID;
        });

        if (!isDuplicate) {
            // ปรับปรุงเพื่อเพิ่มพนักงานลงในตัวแปร employees
            var newEmployee = {
                name: employeeName,
                card_id: employeeID,
                scg_id: employeeShowID
            };
            employeesList.push(newEmployee);

            // ลบค่าใน input search
            $('#employeesList').val('เลือกผู้ตรวจสอบ').trigger('change');

            // ปรับปรุงรายการพนักงาน
            updateEmployeeList();
        } else {
            // alert('พนักงานนี้ได้ถูกเพิ่มแล้ว');

            Swal.fire({
                title: "เกิดข้อผิดพลาด",
                text: "พนักงานท่านนี้ได้ถูกเพิ่มแล้ว",
                icon: "error"
            });
        }

    }

    $('#employeesList').on('select2:select', function (e) {
        // console.log(e);

        let selectedValue = this.value;
        let valuesArray = selectedValue.split('|');
        let employee_card_id = valuesArray[0];
        let employee_scg_id = valuesArray[1];
        let selected_employeeName = e.params.data.text;

        if (employee_card_id && employee_scg_id && selected_employeeName) {
            addEmployee(selected_employeeName, employee_card_id, employee_scg_id);
        }
    });

    if (selectedValue == 'option1') {

        $('div[id=shift_group-container]').hide();
        console.log('คนเดียว');

    } else if (selectedValue == 'option2') {

        $('div[id=shift_group-container]').show();

        console.log('เป็นทีม');

        $('#employeesList').select2({
            minimumInputLength: 0,
            tags: false
        });

    };

});

$('input[type=radio][name=shift_time]').on("change", function () {

    $('#shift_date-container').show();

    let selectedValue = $('input[type=radio][name=shift_time]:checked').val();

    if (selectedValue == 'option1') {

        $('div[id=shift_time_tempo]').hide();

        // shift_time_end.attr("disabled", true);
        $('#shift_end_date').attr("disabled", true);

        // let texts = shift_time_start.text();
        // let texts = $('#shift_start_date').text();

        console.log('ระยะยาว');

    } else if (selectedValue == 'option2') {

        $('div[id=shift_time_tempo]').show();

        // shift_time_end.attr("disabled", false);
        $('#shift_end_date').attr("disabled", false);

        console.log('ชั่วคราว');

    };
});

$('input[type=radio][name=gps_type]').on("change", function () {

    let selectedValue = $('input[type=radio][name=gps_type]:checked').val();

    if (selectedValue == 'option1') {

        $("#label_coordIN").html("โปรดระบุชื่อสถานที่เข้าและออกงาน");

        $('#mark-check-both-button').show();
        $('#mark-clear-button').show();
        $('#mark-check-in-button').hide();
        $('#mark-check-out-button').hide();

        $('#label_coordOUT').hide();
        $('#coords_nameOUT').hide();

        $('#map-container').show();

        console.log('เข้าและออกจุดเดียว');

    } else if (selectedValue == 'option2') {

        $("#label_coordIN").html("โปรดระบุชื่อสถานที่เข้างาน");

        $('#mark-check-in-button').show();
        $('#mark-check-out-button').show();
        $('#mark-clear-button').show();
        $('#mark-check-both-button').hide();

        $('#label_coordOUT').show();
        $('#coords_nameOUT').show();

        $('#map-container').show();

        console.log('เข้าและออกคนละจุด');

    } else if (selectedValue == 'option3') {

        $('#mark-check-in-button').hide();
        $('#mark-check-out-button').hide();
        $('#mark-check-both-button').hide();
        $('#mark-clear-button').hide();

        $('#map-container').hide();

        console.log('เข้าและออกที่ไหนก็ได้');
    }

    Clear_marker();
    $('input[name=coords_name]').val(null);
});

$('#mark-check-in-button').on("click", function () {

    if (btnOUT_flag == true) {
        $('#mark-check-in-button').attr("disabled", true);
    } else {
        $('#mark-check-out-button').attr("disabled", true);
    }

    map.on('click', function (e) {

        map.off('click');
        let markerP = L.marker(e.latlng).addTo(markersGroup);

        let circleP = L.circle(e.latlng, {
            color: '#1E90FF',
            fillColor: '#82EEFD',
            fillOpacity: 0.4,
            radius: 500
        }).addTo(markersGroup);

        markerP.bindPopup("จุดเข้างาน.").openPopup();
        circleP.bindPopup("จุดเข้างาน.").openPopup();

        let coordinates = e.latlng;

        $("#span_showIN_lat").html('ละติจูด: ' + e.latlng.lat);
        $("#span_showIN_lng").html('ลองติจูด: ' + e.latlng.lng);

        Object.assign(check_in_coordinates, {
            coordinates
        });

        if (btnOUT_flag == true) {
            return
        } else {
            $('#mark-check-out-button').attr("disabled", false);
            $('#mark-check-in-button').attr("disabled", true);
        }
        btnIN_flag = true;

    });



    return
});

$('#mark-check-out-button').on("click", function () {

    if (btnIN_flag == true) {
        $('#mark-check-out-button').attr("disabled", true);

    } else {
        $('#mark-check-in-button').attr("disabled", true);
    }

    map.on('click', function (e) {

        map.off('click');

        let markerP = L.marker(e.latlng).addTo(markersGroup);

        let circleP = L.circle(e.latlng, {
            color: 'red',
            fillColor: '#82EEFD',
            fillOpacity: 0.4,
            radius: 500
        }).addTo(markersGroup);

        markerP.bindPopup("จุดออกงาน.").openPopup();
        circleP.bindPopup("จุดออกงาน.").openPopup();

        let coordinates = e.latlng;

        $("#span_showOUT_lat").html('ละติจูด: ' + e.latlng.lat);
        $("#span_showOUT_lng").html('ลองติจูด: ' + e.latlng.lng);

        Object.assign(check_out_coordinates, {
            coordinates
        });

        if (btnIN_flag == true) {
            return
        } else {
            $('#mark-check-in-button').attr("disabled", false);
            $('#mark-check-out-button').attr("disabled", true);
        }
        btnOUT_flag = true;
    });
    return
});

$('#mark-check-both-button').on("click", function () {

    $('#mark-check-both-button').attr("disabled", true);

    map.on('click', function (e) {

        map.off('click');

        let markerP = L.marker(e.latlng).addTo(markersGroup);

        let circleP = L.circle(e.latlng, {
            color: '#1E90FF',
            fillColor: '#82EEFD',
            fillOpacity: 0.4,
            radius: 500
        }).addTo(markersGroup);

        markerP.bindPopup("จุดเข้าและออกงาน.").openPopup();
        circleP.bindPopup("จุดเข้าออกงาน.").openPopup();

        var coordinates = e.latlng;

        $("#span_showIN_lat").html('ละติจูด: ' + e.latlng.lat);
        $("#span_showIN_lng").html('ลองติจูด: ' + e.latlng.lng);

        Object.assign(check_both_coordinates, {
            coordinates
        });

    });

});

$('#mark-clear-button').on("click", function () {
    Clear_marker();
    return;
});

$('#submit-form-button').on('click', function () {

    function coords_str(coords) {
        if (coords && coords.coordinates) {
            let { lat, lng } = coords.coordinates;
            let result = `${lat},${lng}`;
            return result;
        } else {
            // console.error("Invalid coordinates object:", coords);
            return "ไม่มีพิกัดแนบมาให้";
        }
    }

    let formData = new FormData($('#gps-check-inout')[0]);

    let GPS_checkedRadio = $('input[name="gps_type"]:checked');
    let Shift_checkedRadio = $('input[name="shift_group"]:checked');
    let TIME_checkedRadio = $('input[name="shift_time"]:checked');

    //Radio เลือกการทำงาน(ของตนเอง)
    if (Shift_checkedRadio.attr("id") === "shift_group1") {

        console.log('เลือกแบบคนเดียว: ' + personID);
        formData.append('employee', personID);

    } else if (Shift_checkedRadio.attr("id") === "shift_group2") {

        console.log('เลือกแบบทีม: ' + employeesList);
        console.log('รหัสคนส่งคำร้อง: ' + personID);
        formData.append('employeesList', JSON.stringify(employeesList));
        formData.append('employee', personID);

    }

    //ทำงานถาวร
    if (TIME_checkedRadio.attr("id") === "shift_time1") {

        console.log('ระยะทำงานแบบ: ระยะยาว');

        Swal.fire({
            title: "เกิดข้อผิดพลาด1",
            text: "คุณยังไม่ได้เลือกวันเริ่มต้น",
            icon: "error",

        }).then((result) => {
            if (result.isDismissed) {
                scrollToElement('shift_start_date');
            }
        });

        let shift_start_date = $("#shift_start_date").val();

        formData.append('shift_start_date', shift_start_date);
        // formData.append('shift_end_date', null);

        console.log('วันเริ่มต้น: ' + shift_start_date);
        console.log('ทำงานแบบถาวร');

        //ทำงานชั่วคราว
    } else if (TIME_checkedRadio.attr("id") === "shift_time2") {

        console.log('ระยะทำงานแบบ: ชั่วคราว');

        Swal.fire({
            title: "เกิดข้อผิดพลาด",
            text: "คุณยังไม่ได้เลือกวันเริ่มต้นหรือสิ้นสุด",
            icon: "error"
        }).then((result) => {
            if (result.isDismissed) {
                scrollToElement('shift_start_date');
            }
        });

        let shift_start_date = $("#shift_start_date").val();
        let shift_end_date = $("#shift_end_date").val();

        if (shift_start_date === null || shift_end_date === null) {
            console.warn('ยังไม่ได้เลือกวันเริ่มต้นทำงาน');
            return;
        }

        formData.append('shift_start_date', shift_start_date);
        formData.append('shift_end_date', shift_end_date);

        console.log('วันเริ่มต้น: ' + shift_start_date);
        console.log('วันสิ้นสุด: ' + shift_end_date);

    } else {
        console.log('ไม่ได้เลือกะยะเวลาทำงาน');
        Swal.fire({
            title: "เกิดข้อผิดพลาด",
            text: "คุณไม่ได้เลือกะยะเวลาทำงาน",
            icon: "error"
        });
        return;
    }

    //คนละจุด
    if (GPS_checkedRadio.attr("id") === "gps_type2" && (check_in_coordinates.coordinates && check_out_coordinates.coordinates)) {

        let check_in_coords_str = coords_str(check_in_coordinates);
        let check_out_coords_str = coords_str(check_out_coordinates);

        formData.append('check_in_coords_str', check_in_coords_str);
        formData.append('check_out_coords_str', check_out_coords_str);

        formData.append('coords_nameWork', $('#coords_nameWork').val());
        formData.append('coords_nameIN', $('#coords_nameIN').val());
        formData.append('coords_nameOUT', $('#coords_nameOUT').val());
        formData.append('coords_range', $('#coords_range').val());

        formData.append('gps_type', '2');

        console.log('ชื่อคำร้อง: ', $('#coords_nameWork').val());
        console.log('ชื่อสถานที่เข้างาน: ', $('#coords_nameIN').val());
        console.log('ชื่อสถานที่ออกงาน: ', $('#coords_nameOUT').val());
        console.log('รัศมีการลงชื่อ: ', $('#coords_range').val());

        console.log('Check IN: ', check_in_coords_str);
        console.log('Check OUT: ', check_out_coords_str);

        //จุดเดียว
    } else if (GPS_checkedRadio.attr("id") === "gps_type1" && (check_both_coordinates.coordinates)) {

        let check_both_coordinates_str = coords_str(check_both_coordinates);

        formData.append('check_both_coordinates_str', check_both_coordinates_str);

        formData.append('coords_nameWork', $('#coords_nameWork').val());
        formData.append('coords_nameIN', $('#coords_nameIN').val());
        formData.append('coords_range', $('#coords_range').val());

        formData.append('gps_type', '1');

        console.log('ชื่อคำร้อง: ', $('#coords_nameWork').val());
        console.log('ชื่อสถานที่เข้าและออกงาน: ', $('#coords_nameIN').val());
        console.log('รัศมีการลงชื่อ: ', $('#coords_range').val());

        console.log('Check Both: ', check_both_coordinates_str);

    } else if (GPS_checkedRadio.attr("id") === "gps_type3") {
        console.log('ที่ใดก็ได้');
        formData.append('gps_type', '3');

    } else {

        console.log('ไม่ได้เลือกประเภทการลงชื่อ');
        Swal.fire({
            title: "เกิดข้อผิดพลาด",
            text: "คุณไม่ได้เลือกประเภทการลงชื่อ",
            icon: "error"
        });
        return;

    }

    let selectedInspector = $("#inspectorList").val();

    if (selectedInspector && selectedInspector !== 'เลือกผู้ตรวจสอบ') {
        console.log('ผู้ตรวจสอบ: ' + selectedInspector);
        formData.append('inspectorID', selectedInspector);
    }

    formData.append('approverID', $('#cost-center-approval-id').val());
    console.log('ผู้อนุมัติ: ' + $('#cost-center-approval-id').val());

    Swal.fire({
        title: "ยืนยันหรือไม่?",
        text: "ยืนยันการขอสถานที่ทำงานหรือไม่",
        // text: "เวลา " + localTime + " น.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#F2CB00",
        confirmButtonText: "ลงชื่อ",
        cancelButtonColor: "#d33",
        cancelButtonText: "ยกเลิก",

    }).then((result) => {

        if (result.isConfirmed) {
            $.ajax({
                method: "POST",
                url: "../check-in/check-request-now-new.php", // ตั้งค่า URL ของไฟล์ PHP
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    console.log('[Log]: ' + response);

                    if (response === "work-shift-tempo-success") {

                        Swal.fire({
                            title: "ส่งคำร้องแล้ว",
                            text: "คุณได้ส่งคำร้องทำงานนอกโรงงานแล้ว.",
                            icon: "success",
                            timer: 2000,
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.timer) {
                                setTimeout(function () {
                                    // location.reload();
                                }, 500);
                            }
                        })

                    }
                },
                error: function (error) {
                    Swal.fire({
                        title: "เกิดข้อผิดพลาด",
                        icon: "error"
                    });
                    console.error(error);
                }
            });

        }
    })
})





