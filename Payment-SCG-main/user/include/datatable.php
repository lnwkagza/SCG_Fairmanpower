
<script src="../vendors/scripts/core.js"></script>
<script src="../vendors/scripts/script.min.js"></script>
<script src="../vendors/scripts/process.js"></script>
<script src="../vendors/scripts/layout-settings.js"></script>
<script src="../src/plugins/datatables/js/jquery.dataTables.min.js"></script>
<script src="../src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
<script src="../src/plugins/datatables/js/dataTables.responsive.min.js"></script>
<script src="../src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
<script src="../vendors/scripts/datagraph.js"></script>

<!-- buttons for Export datatable -->
<script src="../src/plugins/datatables/js/dataTables.buttons.min.js"></script>

<script>
    // datatable
    $('document').ready(function() {
        $('.data-table').DataTable({
            scrollCollapse: true,
            autoWidth: false,
            responsive: true,
            columnDefs: [{
                targets: "datatable-nosort",
                orderable: false,
            }],
            "lengthMenu": [
                [5, 10, 15, -1],
                [5, 10, 20, "ทั้งหมด"]
            ],

            "language": {
                "info": "หน้า _START_ - _END_ จากทั้งหมด _TOTAL_ รายการ",
                "lengthMenu": "แสดง _MENU_ รายการ",
                "search": "<a style='color: #7a7a7a'><i class='fa-solid fa-magnifying-glass' ></i> ค้นหา : </a>",
                "paginate": {
                    "next": '▶',
                    "previous": '◀'
                },
                "infoEmpty": "ไม่มีรายการที่แสดง",
                "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)",
                searchPlaceholder: "ค้นหา",

            },
            dom: '<"top"lBfr<"clear">>rt<"bottom"ip<"clear">>', // ให้แสดงทุกอย่างในบรรทัดเดียวกัน
            buttons: [{
                    extend: 'copyHtml5',
                    text: 'คัดลอก',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                // {
                //     extend: 'csvHtml5',
                //     text: 'CSV',
                //     exportOptions: {
                //         columns: ':visible'
                //     }
                // },
                {
                    text: 'Excel',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    text: 'พิมพ์',
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ]
        });



        var table = $('.select-row').DataTable();
        $('.select-row tbody').on('click', 'tr', function() {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
            } else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        });

        var multipletable = $('.multiple-select-row').DataTable();
        $('.multiple-select-row tbody').on('click', 'tr', function() {
            $(this).toggleClass('selected');
        });
        var table = $('.checkbox-datatable').DataTable({
            'scrollCollapse': true,
            'autoWidth': false,
            'responsive': true,
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            "language": {
                "info": "_START_-_END_ of _TOTAL_",
                searchPlaceholder: "ค้นหา",
                paginate: {
                    next: '▶',
                    previous: '◀'
                }
            },
            'columnDefs': [{
                'targets': 0,
                'searchable': false,
                'orderable': false,
                'className': 'dt-body-center',
                'render': function(data, type, full, meta) {
                    return '<div class="dt-checkbox"><input type="checkbox" name="id[]" value="' + $('<div/>').text(data).html() + '"><span class="dt-checkbox-label"></span></div>';
                }
            }],
            'order': [
                [1, 'asc']
            ]
        });

        $('#example-select-all').on('click', function() {
            var rows = table.rows({
                'search': 'applied'
            }).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);
        });

        $('.checkbox-datatable tbody').on('change', 'input[type="checkbox"]', function() {
            if (!this.checked) {
                var el = $('#example-select-all').get(0);
                if (el && el.checked && ('indeterminate' in el)) {
                    el.indeterminate = true;
                }
            }
        });
    });
</script>