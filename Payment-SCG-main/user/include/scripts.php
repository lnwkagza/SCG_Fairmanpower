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
            "dom": '<"d-flex justify-content-between"lf>rt<"d-flex justify-content-between"p><"clear">', // ตำแหน่งของ elements
            "language": {
                "infoEmpty": "ไม่มีข้อมูลที่แสดง",
                "infoFiltered": "(กรองจากทั้งหมด)",
                "search": "ค้นหา:",
                "lengthMenu": 'แสดงข้อมูล _MENU_',
                "paginate": {
                    "first": "หน้าแรก",
                    "last": "หน้าสุดท้าย",
                    "next": ">",
                    "previous": "<"
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
</script>
</html>