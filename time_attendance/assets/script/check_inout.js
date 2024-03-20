// Destructuring Assignment
var [coords_obj, check_type, Cur_Position, Dis_Position, Cal_distance, gps_rangeKM] = [null];

var Cur_coords = {};
var rearCameraId;

var modal

var map = L.map('mockup-map');

var base_Layer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    // attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
});

map.addLayer(base_Layer);

jQuery(() => {

    getCurrentGPS();
    showThaiDateTime();
    setInterval(showThaiDateTime, 1000);

    setTimeout(function () {

        //พิกัด เข้างาน ทั้งหมด
        $('.check-in-lat').html(Dis_Position.lat);
        $('.check-in-lng').html(Dis_Position.lng);

        $('.check-out-lat').html(Dis_Position.lat);
        $('.check-out-lng').html(Dis_Position.lng);

        $('.check-in-range').html(gps_rangeKM);
        
    }, 750);

});

//ปุ่มแสดงแผนที่
$('.open-modal-btn').on('click', function () {

    // Extract latitude and longitude from button's data attributes
    let lat = $(this).data('lat');
    let lng = $(this).data('lng');
    let coords_range = $(this).data('range');

    let lat_lng_str = lat + "," + lng;

    let coords_obj = CoordsTo_Object(lat_lng_str);

    console.log('Lat: ' + lat);
    console.log('Lng: ' + lng);

    console.log('Coords: ' + coords_obj);

    let popupMap =
        '<div style="font-weight: bold; font-size: 15px;">พิกัดสถานที่</div><br>' +
        '<div id="map"></div><br>' +

        '<div class="row modal-dialog modal-dialog-scrollable">' +

        '<div class="col-md-6">' +

        '<div class="form-group">' +
        '<label for="work_in_lat">ละติจูด</label>' +
        '<input id="work_in_lat" type="text" name="work_in_lat" class="form-control" value="" readonly>' +
        '</div>' +

        '<div class="form-group">' +
        '<label for="work_in_lng">ลองติจูด</label>' +
        '<input id="work_in_lng" type="text" name="work_in_lng" class="form-control" value="" readonly>' +
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

            if (map) {
                map.remove();
            }

            let map = L.map('map').setView(coords_obj, 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            new L.marker(coords_obj).addTo(map);

            console.log("พิกัดเดียวกัน.");

            let circleP = L.circle(coords_obj, {
                color: '#1E90FF',
                fillColor: '#82EEFD',
                fillOpacity: 0.4,
                radius: coords_range
            }).addTo(map);

            circleP.bindPopup("จุดเข้าและออกงาน.").openPopup();


        }


    })



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

function formatThaiDateTime(dateObject) {
    let thaiMonths = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
    ];

    let thaiYear = dateObject.getFullYear() + 543;
    let thaiMonth = thaiMonths[dateObject.getMonth()];
    let thaiDay = dateObject.getDate();

    let hours = dateObject.getHours();
    let minutes = dateObject.getMinutes();
    let seconds = dateObject.getSeconds();

    // รูปแบบเวลาให้เป็น 2 หลัก
    minutes = ('0' + minutes).slice(-2);
    seconds = ('0' + seconds).slice(-2);

    let thaiTime = hours + ':' + minutes + ':' + seconds + ' น.';

    return 'วันที่: ' + thaiDay + ' ' + thaiMonth + ' ' + thaiYear + ' เวลา: ' + thaiTime;
}

function showThaiDateTime() {
    // สร้าง Object Date สำหรับเวลาปัจจุบัน
    let now = new Date();

    // แสดงผลวันที่และเวลาแบบปี พ.ศ.
    let thaiDateTime = formatThaiDateTime(now);

    // แสดงผลวันที่และเวลา
    $('.Display_datetime').text(thaiDateTime);
}

function getDistance() {
    let distance = calculateDistance(Cur_Position, Dis_Position);
    distance.bold();
    Cal_distance = distance;
}

function getClientCoords(callback) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                let latitude = position.coords.latitude;
                let longitude = position.coords.longitude;
                // ส่งค่าตัวแปรแยกกันออกไปที่ callback
                callback(latitude, longitude);
            },
            function (error) {
                console.error('Error getting user location:', error.message);
                // ส่งค่า null ถ้าเกิดข้อผิดพลาด
                callback(null, null);
            }
        );
    } else {
        console.error('Geolocation is not supported by this browser.');
        callback(null, null);
    }
}

//calculate the distance between 2 marker
function calculateDistance(latA, latB) {
    if (latA !== undefined && latB !== undefined) {
        let dis = latA.distanceTo(latB);
        let distanceConversion = dis.toFixed(0);
        let distanceKm = distanceConversion;
        return distanceKm || 0;
    }
    else {
        console.error("Calculation error.");
        return 0;
    }
}

function haversineDistance(lat1, lon1, lat2, lon2) {
    // คำนวณความห่างระหว่างละติจูด (latitude) และลองจิจูด (longitude) ในรูปแบบ radian
    const dLat = toRadians(lat2 - lat1);
    const dLon = toRadians(lon2 - lon1);

    // คำนวณ Haversine สำหรับทั้ง lat และ lon
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2)) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);

    // คำนวณความห่างในระยะหน่วย radius ของโลก (รัศมีของโลกในหน่วยเมตร)
    const radiusOfEarth = 6371000; // รัศมีของโลกในหน่วยเมตร
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const distance = radiusOfEarth * c;

    // ใช้ Math.round() เพื่อปัดเป็นจำนวนเต็ม
    return Math.round(distance);
}

function toRadians(degrees) {
    return degrees * (Math.PI / 180);
}

function showPosition(position) {

    coords_obj = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
    };

    map.setView(coords_obj, 14);

    gps_rangeKM = meterToKilometer(gps_range);

    let Cur_Position_marker = new L.marker(coords_obj);
    let Dis_Position_marker = new L.marker(default_coords);

    Cur_Position = Cur_Position_marker._latlng;
    Dis_Position = Dis_Position_marker._latlng;

    let Dis_Position_Circle = L.circle(default_coords, {
        color: '#1E90FF',
        fillColor: '#82EEFD',
        fillOpacity: 0.4,
        radius: gps_range
    });

    getDistance();

    //delete obj val foreach kry
    Object.keys(coords_obj).forEach(key => {
        delete coords_obj[key];
    });

    // map.addLayer(base_Layer);
    map.addLayer(Dis_Position_marker);
    map.addLayer(Dis_Position_Circle);
    map.addLayer(Cur_Position_marker);

    setTimeout(function () {
        map.remove();
        console.log('map remove!');
    }, 200);

}

function getCurrentGPS() {

    const options = {
        enableHighAccuracy: true,
        timeout: 10000,
    };

    function showError(error) {
        let errorMessage = "";
        switch (error.code) {
            case error.PERMISSION_DENIED:
                errorMessage = "ผู้ใช้ปฏิเสธคำขอตำแหน่งทางภูมิศาสตร์.";
                break;
            case error.POSITION_UNAVAILABLE:
                errorMessage = "ไม่มีข้อมูลตำแหน่ง.";
                break;
            case error.TIMEOUT:
                errorMessage = "คำขอรับตำแหน่งของผู้ใช้หมดเวลา.";
                break;
            default:
                errorMessage = "An unknown error occurred.";
                console.warn(`ERROR(${error.code}): ${error.message}`);
                break;
        }
        $('#location').html(errorMessage);
    }

    navigator.geolocation.getCurrentPosition(showPosition, showError, options);
}

function show_outside_range(distance, range) {
    let result = (distance - range) + 3;
    return result
}

function showMap(latitude, longitude) {

    let map = L.map('map-container').setView([latitude, longitude], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);
    L.marker([latitude, longitude]).addTo(map)
        .bindPopup('ตำแหน่งปัจจุบัน')
        .openPopup();
}

function coords_str(coords) {
    let { lat, lng } = coords;
    let result = lat + "," + lng;
    return result;
}

function combineCoords(latitude, longitude) {
    return latitude + ',' + longitude;
}

function getCurrentLocalTime() {
    const time_now = new Date();
    const options = {
        timeZone: 'Asia/Bangkok',
        hour12: false,
        hour: '2-digit',
        minute: '2-digit'
    };
    return time_now.toLocaleTimeString('en-US', options);
}

function meterToKilometer(meter) {
    return meter / 1000;
}

//GPS Modal CheckIN
$('#check-in-Modal-gps').on('show.bs.modal', function () {

    let map = L.map('modal-checkIN-map');
    // map.setView(coords_obj, 14);


    map.on('click', function () {
        map.invalidateSize();
        console.log('map reload!');
    });
    setTimeout(function () {
        map.invalidateSize();
        console.log('map reload!');
    }, 500);

    $('.check-in-lat').html(Dis_Position.lat);
    $('.check-in-lng').html(Dis_Position.lng);
    $('.check-in-range').html(gps_rangeKM);

    let outside_range_result = show_outside_range(Cal_distance, gps_range);
    let distance_result = Cal_distance - gps_range;

    if (distance_result >= 0) {
        $('#location-in').html("อีก " + outside_range_result + " เมตร ถึงจะลงชื่อได้.");
    } else if (distance_result < 0) {
        $('#location-in').html("ท่านอยู่ในรัศมีพิกัดที่กำหนด.");
    }

    $("#check-in-Modal-gps").on('click', '#check-in-button-gps', function () {

        console.log('Modal ลงชื่อเข้า');

        check_in_str = coords_str(Cur_Position);

        const localTime = getCurrentLocalTime();

        let distance_result = Cal_distance - gps_range;

        if (distance_result < 0) {
            Swal.fire({
                title: '<span style="font-size: 4vw;">ยืนยันลงชื่อเข้างานหรือไม่?</span>',
                text: "เวลา " + localTime + " น.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#29ab29",
                confirmButtonText: "ลงชื่อ",
                cancelButtonColor: "#e1574b",
                cancelButtonText: "ยกเลิก",

            }).then((result) => {
                if (result.isConfirmed) {

                    $('#gps-in-coords').val(check_in_str);
                    let formData = $("#form-check-gps").serialize();

                    $.ajax({
                        type: "POST",
                        url: "../processing/process_check_in.php", // ตั้งค่า URL ของไฟล์ PHP
                        data: formData,
                        success: function (response) {
                            if (response === 'check-in-complete') {
                                Swal.fire({
                                    title: "ลงชื่อแล้ว",
                                    text: "คุณได้ลงชื่อเข้างานแล้ว.",
                                    icon: "success",
                                    timer: 2000,

                                }).then((result) => {
                                    if (result.dismiss === Swal.DismissReason.timer) {
                                        setTimeout(function () {
                                            location.reload();
                                        }, 500);
                                    }
                                }).finally(() => {
                                    setTimeout(function () {
                                        location.reload();
                                    }, 500);
                                });
                                console.log(response);
                            } else if (response === 'already-checked-in') {
                                Swal.fire({
                                    title: "เข้างานไปแล้ว",
                                    text: "คุณได้ลงชื่อเข้างานไปแล้ว.",
                                    icon: "warning",
                                });
                                console.log('คุณได้ลงชื่อเข้างานไปแล้ว.');
                            } else if (response === 'already-request-leave') {
                                console.log('คุณได้ลางานไว้แล้ว.');
                            } else if (response === 'no-data-in-record') {
                                console.log('ไม่พบข้อมูล.');
                            } else if (response === 'not-check-in') {
                                console.log('ยังไม่ได้ลงชื่อเข้างาน.');
                            } else {
                                console.log('ERROR = ', response);
                            }
                            $('#gps-in-coords').val('');
                        },
                        error: function (error) {
                            Swal.fire({
                                title: "เกิดข้อผิดพลาด",
                                text: "ไม่สามารถลงชื่อได้.",
                                icon: "error"
                            });
                            console.error(error);
                        }
                    });
                }
                return;
            });

        } else if (distance_result >= 0) {
            $('#location').html("อยู่ห่างเกินไป.")
        }


    });

});

//GPS Modal CheckOUT
$('#check-out-Modal-gps').on('show.bs.modal', function () {

    coords_obj = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
    };

    let map = L.map('modal-checkIN-map').setView(coords_obj, 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);
    L.marker(coords_obj).addTo(map)
        .bindPopup('ตำแหน่งปัจจุบัน')
        .openPopup();







    map.on('click', function () {
        map.invalidateSize();
        console.log('map reload!');
    });
    setTimeout(function () {
        map.invalidateSize();
        console.log('map reload!');
    }, 500);

    $('.check-in-lat').html(Dis_Position.lat);
    $('.check-in-lng').html(Dis_Position.lng);
    $('.check-in-range').html(gps_rangeKM);

    let outside_range_result = show_outside_range(Cal_distance, gps_range);
    let distance_result = Cal_distance - gps_range;

    if (distance_result >= 0) {
        $('#location-out').html("อีก " + outside_range_result + " เมตร ถึงจะลงชื่อได้.");
    } else if (distance_result < 0) {
        $('#location-out').html("ท่านอยู่ในรัศมีพิกัดที่กำหนด.");
    }

    $("#check-out-Modal-gps").on('click', '#check-out-button-gps', function () {
        console.log('Modal ลงชื่อออก');

        check_out_str = coords_str(Dis_Position);
        const localTime = getCurrentLocalTime();

        let distance_result = Cal_distance - gps_range;

        if (distance_result < 0) {
            Swal.fire({
                title: "ยืนยันลงชื่อออกงานหรือไม่?",
                text: "เวลา " + localTime + " น.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#29ab29",
                confirmButtonText: "ลงชื่อ",
                cancelButtonColor: "#e1574b",
                cancelButtonText: "ยกเลิก",

            }).then((result) => {
                if (result.isConfirmed) {

                    $('#gps-out-coords').val(check_out_str);

                    let formData = $("#form-check-gps").serialize();

                    $.ajax({
                        type: "POST",
                        url: "../processing/process_check_in.php", // ตั้งค่า URL ของไฟล์ PHP
                        data: formData,

                        success: function (response) {
                            if (response === 'check-out-complete') {
                                Swal.fire({
                                    title: "ลงชื่อแล้ว",
                                    text: "คุณได้ลงชื่อออกงานแล้ว.",
                                    icon: "success",
                                }).then((result) => {
                                    if (result.dismiss === Swal.DismissReason.timer) {
                                        setTimeout(function () {
                                            location.reload();
                                        }, 500);
                                    }
                                }).finally(() => {
                                    setTimeout(function () {
                                        location.reload();
                                    }, 500);
                                });
                                console.log(response);
                            } else if (response === 'already-checked-out') {
                                Swal.fire({
                                    title: "ออกงานไปแล้ว",
                                    text: "คุณได้ลงชื่อออกงานไปแล้ว.",
                                    icon: "warning",
                                });
                                console.log('คุณได้ลงชื่อออกงานไปแล้ว.');
                            } else if (response === 'not-check-in') {
                                Swal.fire({
                                    title: "ยังไม่ได้เข้างาน",
                                    text: "คุณยังไม่ได้ลงชื่อเข้างาน.",
                                    icon: "warning",
                                });
                                console.log('คุณไม่ได้ลงชื่อเข้างาน.');
                            } else if (response === 'already-request-leave') {
                                console.log('คุณได้ลางานไว้แล้ว.');
                            } else if (response === 'no-data-in-record') {
                                console.log('ไม่พบข้อมูล.');
                            } else {
                                console.log('ERROR = ', response);
                            }
                            $('#gps-out-coords').val('');
                        },
                        error: function (error) {
                            Swal.fire({
                                title: "เกิดข้อผิดพลาด",
                                text: "ไม่สามารถลงชื่อได้.",
                                icon: "error"
                            });
                            console.error(error);
                        }
                    });
                }
                return;
            });

        } else if (distance_result >= 0) {
            $('#location').html("อยู่ห่างเกินไป.")
        }
    });

});

//WIFI Modal
$('#check-inout-Modal-wifi').on('show.bs.modal', function () {

    console.log('wifi modal')
    const localTime = getCurrentLocalTime();

    function handleCheckINResponse(formData) {
        $.ajax({
            type: 'POST',
            url: '../processing/process_check_in.php',
            data: formData,
            success: function (result) {
                if (result === 'check-in-completes') {
                    Swal.fire({
                        title: "ลงชื่อแล้ว",
                        text: "คุณได้ลงชื่อเข้างานแล้ว.",
                        icon: "success",
                        timer: 2000,
                        confirmButtonColor: "#29ab29",

                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.timer) {
                            setTimeout(function () {
                                location.reload();
                            }, 500);
                        }
                    }).finally(() => {
                        setTimeout(function () {
                            location.reload();
                        }, 500);
                    });

                } else if (result === 'already-checked-in') {
                    Swal.fire({
                        title: "เข้างานไปแล้ว",
                        text: "คุณได้ลงชื่อเข้างานไปแล้ว.",
                        icon: "warning",
                        confirmButtonColor: "#29ab29",
                    });
                    console.log('คุณได้ลงชื่อเข้างานไปแล้ว.');
                } else if (result === 'already-checked-out') {
                    Swal.fire({
                        title: "ออกงานไปแล้ว",
                        text: "คุณได้ลงชื่อออกงานไปแล้ว.",
                        icon: "warning",
                        confirmButtonColor: "#29ab29",
                    });
                    console.log('คุณได้ลงชื่อออกงานไปแล้ว.');
                } else if (result === 'Invalid-subnet') {
                    Swal.fire({
                        title: "ไม่สามารถลงชื่อได้",
                        text: "คุณไม่ได้เชื่อมต่อเครือข่ายของโรงงาน.",
                        icon: "warning",
                        confirmButtonColor: "#29ab29",
                    });
                    console.log('คุณไม่ได้เชื่อมต่อเครือข่ายของโรงงาน.');
                }
                console.log(result);
            },
            error: function () {
                console.error('Error occurred during login.');
            }
        });
    }

    function handleCheckOUTResponse(formData) {
        $.ajax({
            type: 'POST',
            url: '../processing/process_check_in.php',
            data: formData,
            success: function (result) {
                if (result === 'check-out-completes') {
                    Swal.fire({
                        title: "ลงชื่อแล้ว",
                        text: "คุณได้ลงชื่อออกงานแล้ว.",
                        icon: "success",
                        timer: 2000,

                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.timer) {
                            setTimeout(function () {
                                location.reload();
                            }, 500);
                        }
                    }).finally(() => {
                        setTimeout(function () {
                            location.reload();
                        }, 500);
                    });

                } else if (result === 'not-check-in') {
                    Swal.fire({
                        title: "ยังไม่ได้เข้างาน",
                        text: "คุณยังไม่ได้ลงชื่อเข้างาน.",
                        icon: "warning",
                    });
                    console.log('คุณได้ลงชื่อออกงานไปแล้ว.');
                } else if (result === 'already-checked-out') {
                    Swal.fire({
                        title: "ออกงานไปแล้ว",
                        text: "คุณได้ลงชื่อออกงานไปแล้ว.",
                        icon: "warning",
                    });
                    console.log('คุณได้ลงชื่อเข้างานไปแล้ว.');
                } else if (result === 'Invalid-subnet') {
                    Swal.fire({
                        title: "ไม่สามารถลงชื่อได้",
                        text: "คุณไม่ได้เชื่อมต่อเครือข่ายของโรงงาน.",
                        icon: "warning",
                    });
                    console.log('คุณไม่ได้เชื่อมต่อเครือข่ายของโรงงาน.');
                }
                console.log(result);
            },
            error: function () {
                console.error('Error occurred during login.');
            }
        });
        return;
    }

    $("#check-inout-Modal-wifi").on('click', '#check-in-button-wifi', function () {

        console.log('check-in-btn-wifi');

        Swal.fire({
            title: "ยืนยันลงชื่อเข้างานหรือไม่?",
            text: "เวลา " + localTime + " น.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#29ab29",
            confirmButtonText: "ลงชื่อ",
            cancelButtonColor: "#e1574b",
            cancelButtonText: "ยกเลิก",
            // customClass: {
            //     container: 'custom-swal-container-class',  // คลาส CSS สำหรับ container ของ SweetAlert
            //     popup: 'custom-swal-popup-class',          // คลาส CSS สำหรับ popup ของ SweetAlert
            //     header: 'custom-swal-header-class',        // คลาส CSS สำหรับ header ของ SweetAlert
            //     title: 'custom-swal-title-class',          // คลาส CSS สำหรับ title ของ SweetAlert
            //     closeButton: 'custom-swal-close-button-class',  // คลาส CSS สำหรับปุ่มปิด (close button) ของ SweetAlert
            //     icon: 'custom-swal-icon-class',            // คลาส CSS สำหรับ icon ของ SweetAlert
            //     image: 'custom-swal-image-class',          // คลาส CSS สำหรับรูปภาพ ณ ตำแหน่งของรูปภาพ (image) ของ SweetAlert
            //     content: 'custom-swal-content-class',      // คลาส CSS สำหรับ content ของ SweetAlert
            //     input: 'custom-swal-input-class',          // คลาส CSS สำหรับ input ของ SweetAlert
            //     actions: 'custom-swal-actions-class',      // คลาส CSS สำหรับ actions ของ SweetAlert
            //     confirmButton: 'custom-swal-confirm-button-class', // คลาส CSS สำหรับปุ่ม Confirm ของ SweetAlert
            //     cancelButton: 'custom-swal-cancel-button-class', // คลาส CSS สำหรับปุ่ม Cancel ของ SweetAlert
            //     footer: 'custom-swal-footer-class'         // คลาส CSS สำหรับ footer ของ SweetAlert
            // }

        }).then((result) => {
            if (result.isConfirmed) {

                let formData = $("#form-check-wifi").serialize();

                getClientCoords(function (latitude, longitude) {
                    if (latitude !== null && longitude !== null) {
                        let clientCoords = combineCoords(latitude, longitude)
                        $('#client-coords').val(clientCoords);

                        handleCheckINResponse(formData);
                    } else {
                        handleCheckINResponse(formData);
                    }
                });
            }
        });
    });

    $("#check-inout-Modal-wifi").on('click', '#check-out-button-wifi', function () {

        console.log('check-out-btn-wifi');

        Swal.fire({
            title: "ยืนยันลงชื่อออกงานหรือไม่?",
            text: "เวลา " + localTime + " น.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "ลงชื่อ",
            confirmButtonColor: " #29ab29", // สีของปุ่ม confirm
            cancelButtonColor: "#e1574b" // สีของปุ่ม cancel
            //     
            // customClass: {
            //     container: 'custom-swal-container-class',  // คลาส CSS สำหรับ container ของ SweetAlert
            //     popup: 'custom-swal-popup-class',          // คลาส CSS สำหรับ popup ของ SweetAlert
            //     header: 'custom-swal-header-class',        // คลาส CSS สำหรับ header ของ SweetAlert
            //     title: 'custom-swal-title-class',          // คลาส CSS สำหรับ title ของ SweetAlert
            //     closeButton: 'custom-swal-close-button-class',  // คลาส CSS สำหรับปุ่มปิด (close button) ของ SweetAlert
            //     icon: 'custom-swal-icon-class',            // คลาส CSS สำหรับ icon ของ SweetAlert
            //     image: 'custom-swal-image-class',          // คลาส CSS สำหรับรูปภาพ ณ ตำแหน่งของรูปภาพ (image) ของ SweetAlert
            //     content: 'custom-swal-content-class',      // คลาส CSS สำหรับ content ของ SweetAlert
            //     input: 'custom-swal-input-class',          // คลาส CSS สำหรับ input ของ SweetAlert
            //     actions: 'custom-swal-actions-class',      // คลาส CSS สำหรับ actions ของ SweetAlert
            //     confirmButton: 'custom-swal-confirm-button-class', // คลาส CSS สำหรับปุ่ม Confirm ของ SweetAlert
            //     confirmButtonText: "ลงชื่อ",
            //     cancelButton: 'custom-swal-cancel-button-class', // คลาส CSS สำหรับปุ่ม Cancel ของ SweetAlert
            //     cancelButtonText: "ยกเลิก",    
            //     footer: 'custom-swal-footer-class'         // คลาส CSS สำหรับ footer ของ SweetAlert
            // }

        }).then((result) => {
            if (result.isConfirmed) {

                let formData = $("#form-check-wifi").serialize();

                getClientCoords(function (latitude, longitude) {
                    if (latitude !== null && longitude !== null) {

                        let clientCoords = combineCoords(latitude, longitude)
                        $('#client-coords').val(clientCoords);

                        handleCheckOUTResponse(formData);
                    } else {
                        handleCheckOUTResponse(formData);
                    }
                });
            }
        });
    });

});

//QR Modal check-in
$('#check-in-Modal-qr').on('show.bs.modal', function () {

    console.log('check-in-Modal-qr ' + Cur_Position);

    checkRearCamera().then(rearCameraId => {

        let selectedDeviceId;
        const codeReader = new ZXing.BrowserQRCodeReader();
        console.log('Camera initialized');

        codeReader.getVideoInputDevices()
            .then(() => {
                selectedDeviceId = rearCameraId;
            })
            .catch((err) => {
                console.error(err);
            });

        $('#startButton').on('click', function () {
            console.log('สแกน check-in');
            decodeOnce(codeReader, selectedDeviceId);
        });

        $('#resetButton').on('click', function () {
            codeReader.reset();
            $('#result').text('');
            console.log('Reset.');
        });

    }).catch(error => {
        console.error(error);
    });

    function checkRearCamera() {
        return new Promise((resolve, reject) => {
            // Constraints ที่กำหนดการใช้งานของกล้อง
            const constraints = {
                video: true,
                audio: false
            };

            // ขออนุญาตใช้งานกล้อง
            navigator.mediaDevices.getUserMedia(constraints)
                .then((stream) => {
                    // ขอให้กล้องปิดทันทีหลังจากได้รับอนุญาต
                    stream.getTracks().forEach(track => track.stop());

                    // ใช้ enumerateDevices เพื่อดึงข้อมูลทั้งหมดของอุปกรณ์
                    navigator.mediaDevices.enumerateDevices()
                        .then(devices => {
                            const videoDevices = devices.filter(device => device.kind === 'videoinput');
                            const rearCamera = videoDevices.find(device => device.label.toLowerCase().includes('back'));

                            // console.log('videoDevices คือ: ', videoDevices);

                            if (rearCamera) {
                                const rearCameraId = rearCamera.deviceId;
                                // console.log('Device ID ของกล้องหลังคือ:', rearCameraId);
                                // $('#cam').text("มีกล้องหลัง");
                                resolve(rearCameraId);
                            } else {
                                console.log('ไม่พบกล้องหลัง');
                                // $('#cam').text("ไม่พบกล้องหลัง");
                                reject(new Error('ไม่พบกล้องหลัง'));
                            }
                        })
                        .catch(error => {
                            console.error('เกิดข้อผิดพลาดในการดึงข้อมูลอุปกรณ์วีดีโอ:', error);
                            reject(error);
                        });
                })
                .catch((error) => {
                    // ถ้าไม่ได้รับอนุญาตหรือมีปัญหาอื่น ๆ
                    console.error('เกิดข้อผิดพลาดในการขออนุญาตเข้าถึงกล้อง:', error);
                    reject(error);
                });
        });
    }

    function decryptGPS(token, key) {
        const decryptedMessageGPS = CryptoJS.AES.decrypt(token, key).toString(CryptoJS.enc.Utf8);
        return decryptedMessageGPS;
    }

    function decodeOnce(codeReader, selectedDeviceId) {
        codeReader.decodeFromInputVideoDevice(selectedDeviceId, 'video')
            .then((result) => {
                // console.log(result);
                handleResultIN(result);
            }).catch((err) => {
                console.error(err);
                $('#result').text(err);
            });
    }

    function createLocationObject(lat, lng) {
        return { lat, lng };
    }

    function handleResultIN(result) {

        // $('#qr-distance-gps').html('handleResultIN(result)');

        if (result) {

            const resultText = result.text;

            // $('#qr-distance-gps').html(resultText);
            console.log('Found QR code!', resultText);

            var resultQR = decryptGPS(resultText, secretKey);

            const QRstr = resultQR.split(",");
            console.log('QRstr: ', resultQR);
            const [qr_lat, qr_lng, qr_range] = QRstr;//เอาอาเรย์มาเก็บแยกแต่ละตัว

            console.group('พิกัดของ QR');

            //พิกัด ใน QR-Code
            console.log('==========================================================');
            console.log('QR_lat ' + qr_lat);
            console.log('QR_lng ' + qr_lng);
            console.log('==========================================================');

            console.groupEnd();

            if (resultQR) {

                console.log('QR Code matched!');

                getClientCoords(function (latitude, longitude) { // พิกัด Client

                    if (latitude !== null && longitude !== null) {

                        let distance_between = haversineDistance(qr_lat, qr_lng, latitude, longitude)

                        console.log('ห่างจากจุดเป็นระยะทาง ' + distance_between + ' เมตร');

                        let distance_result = distance_between - qr_range;

                        console.log('ลบกับ qr_range จะเหลือระยะทาง ' + distance_result + ' เมตร');

                        let qr_client_coords = combineCoords(latitude, longitude);

                        console.log(qr_client_coords);

                        let formData = new FormData($('#form-check-qr')[0]);
                        formData.append('qr-card-id', personID);
                        formData.append('qr-coords', qr_client_coords);

                        if (distance_result >= 0) {

                            Swal.fire({
                                icon: "error",
                                title: "คุณไม่ได้อยู่ในโรงงาน",
                                text: "คุณสามารถลงชื่อได้เฉพาะในเขตโรงงานเท่านั้น",
                                timer: 5000
                            }).then(() => {
                                return
                            });


                        } else if (distance_result < 0) {

                            // decodeOnce(codeReader, selectedDeviceId);
                            Swal.fire({
                                title: 'ยืนยันการลงชื่อ',
                                text: 'คุณต้องการยืนยันลงชื่อหรือไม่?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'ยืนยัน',
                                cancelButtonText: 'ยังก่อน'

                            }).then((result) => {
                                if (result.isConfirmed) {

                                    // let formData = $("#form-check-qr").serialize();
                                    // $('#qr-coords').val(qr_client_coords);

                                    $.ajax({
                                        method: "POST",
                                        url: "../processing/process_check_in.php", // ตั้งค่า URL ของไฟล์ PHP
                                        data: formData,
                                        contentType: false,
                                        processData: false,

                                        success: function (response) {
                                            if (response === 'check-in-complete') {
                                                Swal.fire({
                                                    title: "ลงชื่อแล้ว",
                                                    text: "คุณได้ลงชื่อเข้างานแล้ว.",
                                                    icon: "success",
                                                    timer: 2000,

                                                }).then((result) => {
                                                    if (result.dismiss === Swal.DismissReason.timer) {
                                                        setTimeout(function () {
                                                            location.reload();
                                                        }, 500);
                                                    }
                                                }).finally(() => {
                                                    setTimeout(function () {
                                                        location.reload();
                                                    }, 500);
                                                });
                                                console.log(response);
                                            } else if (response === 'check-out-complete') {
                                                Swal.fire({
                                                    title: "ลงชื่อแล้ว",
                                                    text: "คุณได้ลงชื่อออกงานแล้ว.",
                                                    icon: "success",
                                                    timer: 2000,

                                                }).then((result) => {
                                                    if (result.dismiss === Swal.DismissReason.timer) {
                                                        setTimeout(function () {
                                                            location.reload();
                                                        }, 500);
                                                    }
                                                }).finally(() => {
                                                    setTimeout(function () {
                                                        location.reload();
                                                    }, 500);
                                                });
                                                console.log(response);
                                            } else {
                                                console.log('ERROR = ', response);
                                            }
                                            $('#qr-coords').val('');
                                        },
                                        error: function (error) {
                                            Swal.fire({
                                                title: "เกิดข้อผิดพลาด",
                                                text: "ไม่สามารถลงชื่อได้.",
                                                icon: "error"
                                            });
                                            console.error(error);
                                        }
                                    });

                                    return;

                                } else if (result.dismiss) {
                                    // User clicked 'No' or closed the popup
                                    Swal.fire({
                                        icon: "error",
                                        title: "ยกเลิก",
                                        text: "ยกเลิกการลงชื่อ"
                                    });
                                }
                            });
                        }

                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "คุณไม่ได้เปิด GPS",
                        });
                        console.warn('คุณไม่ได้เปิด GPS')
                    }
                })
            } else {
                console.log('QR Code not matched.');
                $('#result').text("False");
            }

        } else {
            $('#qr-distance-gps').html('ไม่เจอ QR');
            // Swal.fire({
            //     title: "ไม่พบ QR-Code",
            //     timer: '2000'
            // });
        }

    }

});

//QR Modal check-out
$('#check-out-Modal-qr').on('show.bs.modal', function () {

    console.log('check-in-Modal-qr ' + Cur_Position);

    checkRearCamera().then(rearCameraId => {

        let selectedDeviceId;
        const codeReader = new ZXing.BrowserQRCodeReader();
        console.log('ZXing code reader initialized');

        codeReader.getVideoInputDevices()
            .then(() => {
                selectedDeviceId = rearCameraId;
            })
            .catch((err) => {
                console.error(err);
            });

        $('#startButton').on('click', function () {
            console.log('สแกน check-in');
            decodeOnce(codeReader, selectedDeviceId);
        });

        $('#resetButton').on('click', function () {
            codeReader.reset();
            $('#result').text('');
            console.log('Reset.');
        });

    }).catch(error => {
        console.error(error);
    });

    function checkRearCamera() {
        return new Promise((resolve, reject) => {
            // Constraints ที่กำหนดการใช้งานของกล้อง
            const constraints = {
                video: true,
                audio: false
            };

            // ขออนุญาตใช้งานกล้อง
            navigator.mediaDevices.getUserMedia(constraints)
                .then((stream) => {
                    // ขอให้กล้องปิดทันทีหลังจากได้รับอนุญาต
                    stream.getTracks().forEach(track => track.stop());

                    // ใช้ enumerateDevices เพื่อดึงข้อมูลทั้งหมดของอุปกรณ์
                    navigator.mediaDevices.enumerateDevices()
                        .then(devices => {
                            const videoDevices = devices.filter(device => device.kind === 'videoinput');
                            const rearCamera = videoDevices.find(device => device.label.toLowerCase().includes('back'));

                            // console.log('videoDevices คือ: ', videoDevices);

                            if (rearCamera) {
                                const rearCameraId = rearCamera.deviceId;
                                // console.log('Device ID ของกล้องหลังคือ:', rearCameraId);
                                // $('#cam').text("มีกล้องหลัง");
                                resolve(rearCameraId);
                            } else {
                                console.log('ไม่พบกล้องหลัง');
                                // $('#cam').text("ไม่พบกล้องหลัง");
                                reject(new Error('ไม่พบกล้องหลัง'));
                            }
                        })
                        .catch(error => {
                            console.error('เกิดข้อผิดพลาดในการดึงข้อมูลอุปกรณ์วีดีโอ:', error);
                            reject(error);
                        });
                })
                .catch((error) => {
                    // ถ้าไม่ได้รับอนุญาตหรือมีปัญหาอื่น ๆ
                    console.error('เกิดข้อผิดพลาดในการขออนุญาตเข้าถึงกล้อง:', error);
                    reject(error);
                });
        });
    }

    function decryptGPS(token, key) {
        const decryptedMessageGPS = CryptoJS.AES.decrypt(token, key).toString(CryptoJS.enc.Utf8);
        return decryptedMessageGPS;
    }

    function decodeOnce(codeReader, selectedDeviceId) {
        codeReader.decodeFromInputVideoDevice(selectedDeviceId, 'video')
            .then((result) => {
                // console.log(result);
                handleResultIN(result);
            }).catch((err) => {
                console.error(err);
                $('#result').text(err);
            });
    }

    function createLocationObject(lat, lng) {
        return { lat, lng };
    }

    function handleResultIN(result) {

        // $('#qr-distance-gps').html('handleResultIN(result)');

        if (result) {

            const resultText = result.text;

            // $('#qr-distance-gps').html(resultText);
            console.log('Found QR code!', resultText);

            var resultQR = decryptGPS(resultText, secretKey);

            const QRstr = resultQR.split(",");
            const [qr_lat, qr_lng, qr_range] = QRstr;//เอาอาเรย์มาเก็บแยกแต่ละตัว

            console.group('พิกัดของ QR');

            //พิกัด ใน QR-Code
            console.log('==========================================================');
            console.log('QR_lat ' + qr_lat);
            console.log('QR_lng ' + qr_lng);
            console.log('==========================================================');

            console.groupEnd();

            if (resultQR) {

                console.log('QR Code matched!');

                getClientCoords(function (latitude, longitude) { // พิกัด Client

                    if (latitude !== null && longitude !== null) {

                        let distance_between = haversineDistance(qr_lat, qr_lng, latitude, longitude)

                        console.log('ห่างจากจุดเป็นระยะทาง ' + distance_between + ' เมตร');

                        let distance_result = distance_between - qr_range;

                        console.log('ลบกับ qr_range จะเหลือระยะทาง ' + distance_result + ' เมตร');

                        let qr_client_coords = combineCoords(latitude, longitude);

                        if (distance_result >= 0) {

                            Swal.fire({
                                icon: "error",
                                title: "คุณไม่ได้อยู่ในโรงงาน",
                                text: "คุณสามารถลงชื่อได้เฉพาะในเขตโรงงานเท่านั้น",
                                timer: 5000
                            }).then(() => {
                                return
                            });


                        } else if (distance_result < 0) {

                            // decodeOnce(codeReader, selectedDeviceId);
                            Swal.fire({
                                title: 'ยืนยันการเข้างาน',
                                text: 'คุณต้องการยืนยันลงชื่อเข้างานหรือไม่?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'ยืนยัน',
                                cancelButtonText: 'ยังก่อน'

                            }).then((result) => {
                                if (result.isConfirmed) {

                                    let formData = $("#form-check-qr").serialize();
                                    $('#qr-coords').val(qr_client_coords);

                                    $.ajax({
                                        type: "POST",
                                        url: "../processing/process_check_in.php", // ตั้งค่า URL ของไฟล์ PHP
                                        data: formData,

                                        success: function (response) {
                                            if (response === 'check-in-complete') {
                                                Swal.fire({
                                                    title: "ลงชื่อแล้ว",
                                                    text: "คุณได้ลงชื่อเข้างานแล้ว.",
                                                    icon: "success",
                                                    timer: 2000,

                                                }).then((result) => {
                                                    if (result.dismiss === Swal.DismissReason.timer) {
                                                        setTimeout(function () {
                                                            location.reload();
                                                        }, 500);
                                                    }
                                                }).finally(() => {
                                                    setTimeout(function () {
                                                        location.reload();
                                                    }, 500);
                                                });
                                                console.log(response);
                                            } else if (response === 'check-out-complete') {
                                                Swal.fire({
                                                    title: "ลงชื่อแล้ว",
                                                    text: "คุณได้ลงชื่อออกงานแล้ว.",
                                                    icon: "success",
                                                    timer: 2000,

                                                }).then((result) => {
                                                    if (result.dismiss === Swal.DismissReason.timer) {
                                                        setTimeout(function () {
                                                            location.reload();
                                                        }, 500);
                                                    }
                                                }).finally(() => {
                                                    setTimeout(function () {
                                                        location.reload();
                                                    }, 500);
                                                });
                                                console.log(response);
                                            } else {
                                                console.log('ERROR = ', response);
                                            }
                                            $('#qr-coords').val('');
                                        },
                                        error: function (error) {
                                            Swal.fire({
                                                title: "เกิดข้อผิดพลาด",
                                                text: "ไม่สามารถลงชื่อได้.",
                                                icon: "error"
                                            });
                                            console.error(error);
                                        }
                                    });

                                    return;

                                } else if (result.dismiss) {
                                    // User clicked 'No' or closed the popup
                                    Swal.fire({
                                        icon: "error",
                                        title: "ยกเลิก",
                                        text: "ยกเลิกการลงชื่อ"
                                    });
                                }
                            });
                        }

                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "คุณไม่ได้เปิด GPS",
                        });
                        console.warn('คุณไม่ได้เปิด GPS')
                    }
                })
            } else {
                console.log('QR Code not matched.');
                $('#result').text("False");
            }

        } else {
            $('#qr-distance-gps').html('ไม่เจอ QR');
            // Swal.fire({
            //     title: "ไม่พบ QR-Code",
            //     timer: '2000'
            // });
        }

    }

});

//remove backdrop of modal bootstrp5
$('#check-in-Modal-qr, #check-out-Modal-qr').on('hidden.bs.modal', function () {
    // Remove the backdrop when either modal is hidden
    $('.modal-backdrop').remove();
});


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