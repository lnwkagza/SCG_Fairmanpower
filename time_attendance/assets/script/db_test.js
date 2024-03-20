const sql = require('mssql');

// กำหนดการเชื่อมต่อกับ SQL Server
const config = {
    user: 'follow',
    password: 'Follow@2022',
    server: '52.139.193.40',
    database: 'fairman',
    port: 3511,
    options: {
        encrypt: false, // หากคุณใช้การเชื่อมต่อผ่าน SSL/TLS
    },
};

// ฟังก์ชันสำหรับทำการ query ข้อมูล
async function queryDatabase() {
    try {
      // เปิดการเชื่อมต่อกับ SQL Server
      await sql.connect(config);
  
      // ทำ query ตามความต้องการ
      const result = await sql.query`SELECT * FROM location_coords_default WHERE coords_id = 1`;
  
      // ตรวจสอบว่ามีข้อมูลหรือไม่
      if (result.recordset.length > 0) {
        
        const row = result.recordset[0];
        const name = row.coords_name;
        const check_in = row.coords_out_lat_lng;
        const check_out = row.coords_out_lat_lng;
  
        console.log(`Name: ${name}`);
        console.log(`Check IN GPS: ${check_in}`);
        console.log(`Check OUT GPS: ${check_out}`);

      } else {
        console.log('ไม่พบข้อมูล');
      }
    } catch (err) {
      console.error(err);
    } finally {
      // ปิดการเชื่อมต่อ
      await sql.close();
    }
  }

// เรียกใช้ฟังก์ชัน
queryDatabase(1949900414333);
