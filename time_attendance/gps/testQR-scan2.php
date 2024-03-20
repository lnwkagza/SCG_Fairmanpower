<table id="employee_request_gps" class="table dataTable no-footer" role="grid"
    aria-describedby="employee_request_gps_info">
    <thead>
        <tr role="row">
            <th class="nameEm sorting_asc" tabindex="0" aria-controls="employee_request_gps" rowspan="1" colspan="1"
                aria-sort="ascending" aria-label="ชื่อ-สกุล: activate to sort column descending" style="width: 81.8px;">
                ชื่อ-สกุล</th>
            <th class="sorting" tabindex="0" aria-controls="employee_request_gps" rowspan="1" colspan="1"
                aria-label="วันเริ่มทำงาน: activate to sort column ascending" style="width: 92.1875px;">วันเริ่มทำงาน
            </th>
            <th class="sorting" tabindex="0" aria-controls="employee_request_gps" rowspan="1" colspan="1"
                aria-label="วันสิ้นสุดทำงาน: activate to sort column ascending" style="width: 110.012px;">
                วันสิ้นสุดทำงาน</th>
            <th class="sorting" tabindex="0" aria-controls="employee_request_gps" rowspan="1" colspan="1"
                aria-label="พิกัดทำงาน: activate to sort column ascending" style="width: 117.137px;">พิกัดทำงาน</th>
            <th class="sorting" tabindex="0" aria-controls="employee_request_gps" rowspan="1" colspan="1"
                aria-label="สถานะการอนุมัติ: activate to sort column ascending" style="width: 118.65px;">สถานะการอนุมัติ
            </th>
            <th class="sorting" tabindex="0" aria-controls="employee_request_gps" rowspan="1" colspan="1"
                aria-label="จัดการ: activate to sort column ascending" style="width: 140.4px;">จัดการ</th>
        </tr>
    </thead>
    <tbody>
        <tr role="row" class="odd">
            <!-- <td>1949999999904</td> -->
            <td class="sorting_1">นพดล กุลบุตร</td>
            <td>25/02/2024</td>
            <td>09/03/2024</td>
            <td>โรงงานปูนดาวอังคาร</td>
            <td>
                <button type="button">ดูรายละเอียด</button>
            </td>
            <td>
                <!-- ยังไม่อนุมัติ -->
                <select id="approval_status_selection">
                    <option value="1">ยังไม่อนุมัติ</option>
                    <option value="2">ไม่อนุมัติ</option>
                    <option value="3">อนุมัติแล้ว</option>
                    <option value="4">สิ้นสุดการทำงานแล้ว</option>
                </select>
                <div class="selectedValueDisplay">Selected Value: 2</div>
            </td>
        </tr>
    </tbody>
    <tbody>
        <tr>
            <!-- <td>1949999999903</td> -->
            <td>พีรพัฒน์ มณีมัย</td>
            <td>27/02/2024</td>
            <td>07/03/2024</td>
            <td>โรงบ้านบ้านพูน</td>
            <td>
                <button type="button">ดูรายละเอียด</button>
            </td>
            <td>
                <!-- อนุมัติแล้ว -->
                <select id="approval_status_selection">
                    <option value="3">อนุมัติแล้ว</option>
                    <option value="1">ยังไม่อนุมัติ</option>
                    <option value="2">ไม่อนุมัติ</option>
                    <option value="4">สิ้นสุดการทำงานแล้ว</option>
                </select>
                <div class="selectedValueDisplay"></div>
            </td>
        </tr>
    </tbody>
</table>