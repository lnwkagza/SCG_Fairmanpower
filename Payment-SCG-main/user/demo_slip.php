<!DOCTYPE html>
<html>
<head>
    <title>Generate Salary Slip</title>
    <script>
        function generateSalarySlip() {
            var canvas = document.createElement('canvas');
            canvas.width = 800;
            canvas.height = 600;
            var ctx = canvas.getContext('2d');

            // ตั้งพื้นหลังให้เป็นสีขาว
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // เพิ่มข้อความลงบนรูปภาพ
            ctx.fillStyle = '#000000';
            ctx.font = '20px Arial';
            ctx.fillText('สลิปเงินเดือนของคุณ', 300, 50);

            // สร้างตาราง
            drawTable(ctx, 50, 100, 700, 400, 5, 5);

            document.body.appendChild(canvas);

            // สร้างลิงก์สำหรับดาวน์โหลดรูปภาพ
            var link = document.createElement('a');
            link.download = 'salary_slip.png';
            link.href = canvas.toDataURL('image/png');
            link.innerHTML = 'ดาวน์โหลดรูปภาพ';
            document.body.appendChild(link);
        }

        function drawTable(ctx, x, y, width, height, rows, columns) {
            var cellWidth = width / columns;
            var cellHeight = height / rows;

            for (var i = 0; i <= rows; i++) {
                ctx.moveTo(x, y + i * cellHeight);
                ctx.lineTo(x + width, y + i * cellHeight);
            }

            for (var i = 0; i <= columns; i++) {
                ctx.moveTo(x + i * cellWidth, y);
                ctx.lineTo(x + i * cellWidth, y + height);
            }

            ctx.strokeStyle = '#000';
            ctx.stroke();


        }
    </script>
</head>
<body>
    <button onclick="generateSalarySlip()">สร้างสลิปเงินเดือน</button>
</body>
</html>
