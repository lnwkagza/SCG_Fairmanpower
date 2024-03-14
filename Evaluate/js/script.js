const navItems = document.querySelectorAll('.nav-item');

navItems.forEach(item => {
  item.addEventListener('click', function(e) {
    e.preventDefault();

    // Remove 'active' class from all items
    navItems.forEach(navItem => {
      navItem.classList.remove('active');
    });

    // Add 'active' class to the clicked item
    this.classList.add('active');
  });
});


// ฟังก์ชันสำหรับเพิ่มคำถามและคำตอบ
function addQuestion() {
  var question = document.getElementById('question').value;
  var answer = document.getElementById('answer').value;

  var questionElement = document.createElement('div');
  questionElement.innerHTML = '<strong>คำถาม:</strong> ' + question + '<br><strong>คำตอบ:</strong> ' + answer + '<hr>';

  var questionList = document.getElementById('questionList');
  questionList.appendChild(questionElement);

  document.getElementById('question').value = '';
  document.getElementById('answer').value = '';
}

// เชื่อมต่อฟังก์ชันกับปุ่มเพิ่มคำถามและคำตอบ
document.getElementById('addQuestionButton').addEventListener('click', addQuestion);


// ฟังขั่นส่งค่า tr_id ไปหน้า do_assessment.php
function redirectTodoassessment(tr_id,contract_type_id) {
  window.location.href = 'do_assessment.php?id=' + tr_id + '&emp=' + contract_type_id;
}

// ฟังขั่นส่งค่า tr_id, review_to, role ไปหน้า edit_reviewer.php
function redirectToAssessment(tr_id, review_to, role) {
  window.location.href = 'edit_reviewer.php?id=' + tr_id + '&rt=' + review_to + '&role=' + role;
}

// ฟังชั่น Modal เหตุผลถูกปฏิเสธ หน้า main
function showDetail(encodedDetail) {

      var detail = decodeURIComponent(encodedDetail);

  // ตรวจสอบว่าค่าเป็น NULL หรือไม่
  if (detail !== null) {
      // ถ้าไม่เป็น NULL ให้แสดงค่า
      document.getElementById('detailIdInput').innerText = detail;
  } else {
      // ถ้าเป็น NULL ให้แสดงข้อความที่ต้องการ
      document.getElementById('detailIdInput').innerText = 'No data available';
  }

  // เรียกใช้ Modal
  $('#editModal').modal('show');
}