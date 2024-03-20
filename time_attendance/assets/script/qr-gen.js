const crypto = require('crypto');

function generateUniqueToken(length) {
    const token = crypto.randomBytes(Math.ceil(length / 2))
        .toString('hex')
        .slice(0, length);
    return token;
}

function generateAndPrintToken() {
    const uniqueToken = generateUniqueToken(16); // สร้างโทเคนที่มีความยาว 16 ตัวอักษร
    console.log(uniqueToken);
}

// เริ่มต้นสร้างโทเคนทุก 10 วินาที
setInterval(generateAndPrintToken, 10000);
