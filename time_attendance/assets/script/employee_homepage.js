jQuery(() => {
    var container = $(".display-status-checkin");
    var todayDiv = $("#TodayDiv");

    // กำหนดตำแหน่ง div Today เมื่อหน้าเว็บโหลดเสร็จ
    setTodayDivPosition();

    // ตรวจจับการเลื่อนหน้าจอ
    $(window).scroll(function () {
        setTodayDivPosition();
    });

    function setTodayDivPosition() {
        var windowMiddle = $(window).width() / 2;
        var todayDivMiddle = todayDiv.offset().left + todayDiv.outerWidth() / 2;

        // กำหนดตำแหน่งของ div Today ให้แสดงตรงกลางหน้าจอ
        container.scrollLeft(todayDivMiddle - windowMiddle);
    }

});

$('.show-details-btn').on('click', function () {
    // ดึงข้อมูลที่ไม่ซ้ำกันจากฐานข้อมูลโดยใช้ AJAX หรืออื่น ๆ
    var employeeId = $(this).closest('tr').find('td:first').text();

    // ตัวอย่าง AJAX โดยใช้ jQuery
    $.ajax({
        url: 'get_employee_details.php', // ตั้งค่า path ของไฟล์ PHP ที่จะดึงข้อมูล
        method: 'POST',
        data: { employee_id: employeeId },
        success: function (response) {
            // แสดงข้อมูลใน Modal
            $('#employeeDetailsBody').html(response);
            $('#employeeDetailsModal').modal('show');
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
});