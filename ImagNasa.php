<?php
// index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>NASA</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .card img {
      object-fit: cover;
      height: 200px;
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <h1 class="mb-4 text-center">ค้นหาภาพถ่าย</h1>

    <!-- ช่องกรอกข้อความและปุ่มค้นหา -->
    <div class="input-group mb-4">
      <!-- <input type="text" id="query" class="form-control" placeholder="กรุณากรอกชื่อภาพถ่าย" /> -->
      <input type="text" id="query" class="form-control" placeholder="กรุณากรอกวันที่ (YYYY-MM-DD)" />

      <button class="btn btn-primary" onclick="searchRecipes()">ค้นหา</button>
    </div>

    <!-- ส่วนแสดงเมนูแนะนำ -->
    <h4 class="mb-3">ภาพแนะนำ</h4>
    <div id="results" class="row g-4"></div>
  </div>

  <!-- Modal สำหรับแสดงรายละเอียดเมนู -->
  <div class="modal fade" id="recipeModal" tabindex="-1" aria-labelledby="recipeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="recipeModalLabel">รายละเอียดภาพ</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
        </div>
        <div class="modal-body" id="modal-body-content">
          <!-- รายละเอียดจะถูกแสดงที่นี่ -->
        </div>
      </div>
    </div>
  </div>

  <script>
    const apiKey = 'E5BLnhhuj9K0iqmbyeQFkatXp74UiK0HNNyBLIQ2';

    // ตัวแปรเก็บข้อมูลภาพ APOD ไว้ใช้งานแสดงรายละเอียด
    window.recipes = [];

    // โหลดภาพ APOD 8 วันล่าสุด
    async function fetchRecommendedRecipes() {
      const resultsDiv = document.getElementById('results');
      resultsDiv.innerHTML = '<p class="text-center">กำลังโหลดเมนูแนะนำ...</p>';

      try {
        const recipes = [];
        const today = new Date();
        for (let i = 0; i < 20; i++) {
          const date = new Date(today);
          date.setDate(today.getDate() - i);
          const dateString = date.toISOString().split('T')[0];

          const response = await fetch(
            `https://api.nasa.gov/planetary/apod?api_key=${apiKey}&date=${dateString}`
          );
          const data = await response.json();

          // ตรวจสอบว่าเป็นภาพ (image) เท่านั้น บางวันอาจเป็นวิดีโอ
          if (data.media_type === 'image') {
            recipes.push({
              id: i,
              title: data.title,
              image: data.url,
              explanation: data.explanation,
            });
          }
        }

        window.recipes = recipes; // เก็บข้อมูลไว้ global
        displayRecipes(recipes);
      } catch (error) {
        resultsDiv.innerHTML = `<p class="text-danger text-center">เกิดข้อผิดพลาด: ${error.message}</p>`;
      }
    }

    // แสดงการ์ดภาพ APOD
    function displayRecipes(recipes) {
      const resultsDiv = document.getElementById('results');
      resultsDiv.innerHTML = '';

      recipes.forEach((recipe) => {
        const col = document.createElement('div');
        col.className = 'col-md-3';
        col.innerHTML = `
          <div class="card h-100 shadow-sm">
            <img src="${recipe.image}" class="card-img-top" alt="${recipe.title}" />
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">${recipe.title}</h5>
              <button class="btn btn-outline-primary mt-auto" onclick="showRecipeDetails(${recipe.id})">
                ดูรายละเอียด
              </button>
            </div>
          </div>
        `;
        resultsDiv.appendChild(col);
      });
    }

    // แสดงรายละเอียดภาพใน modal
    function showRecipeDetails(id) {
      const modalBody = document.getElementById('modal-body-content');
      const recipe = window.recipes.find((r) => r.id === id);

      if (!recipe) {
        modalBody.innerHTML = `<p class="text-danger text-center">ไม่พบข้อมูลรายละเอียด</p>`;
      } else {
        modalBody.innerHTML = `
          <h2>${recipe.title}</h2>
          <img src="${recipe.image}" class="img-fluid my-3 rounded" />
          <p><strong>รายละเอียด:</strong><br>${recipe.explanation}</p>
        `;
      }

      const modal = new bootstrap.Modal(document.getElementById('recipeModal'));
      modal.show();
    }

    // ฟังก์ชัน placeholder สำหรับ searchRecipes (ถ้าต้องการทำ search จริงๆ ต้องแก้เพิ่มเติม)
    async function searchRecipes() {
      const resultsDiv = document.getElementById('results');
      resultsDiv.innerHTML = '<p class="text-center">ฟีเจอร์ค้นหายังไม่พร้อมใช้งาน</p>';
    }

    // โหลดข้อมูลตอนเปิดหน้าเว็บ
    window.addEventListener('DOMContentLoaded', fetchRecommendedRecipes);
    async function searchRecipes() {
  const resultsDiv = document.getElementById('results');
  const query = document.getElementById('query').value.trim();

  // ตรวจสอบรูปแบบวันที่ YYYY-MM-DD ง่ายๆ ด้วย regex
  const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
  if (!dateRegex.test(query)) {
    resultsDiv.innerHTML = '<p class="text-danger text-center">กรุณากรอกวันที่ในรูปแบบ YYYY-MM-DD เท่านั้น</p>';
    return;
  }

  resultsDiv.innerHTML = '<p class="text-center">กำลังค้นหาภาพ...</p>';

  try {
    const response = await fetch(
      `https://api.nasa.gov/planetary/apod?api_key=${apiKey}&date=${query}`
    );

    if (!response.ok) {
      throw new Error(`ไม่พบภาพในวันที่ ${query}`);
    }

    const data = await response.json();

    if (data.media_type !== 'image') {
      resultsDiv.innerHTML = `<p class="text-center">วันที่ ${query} ไม่มีภาพ (อาจเป็นวิดีโอ)</p>`;
      return;
    }

    // แสดงภาพเดียวในรูปแบบการ์ดเหมือนภาพแนะนำ
    const recipe = {
      id: 0,
      title: data.title,
      image: data.url,
      explanation: data.explanation,
    };

    // เก็บไว้ใน window.recipes เผื่อกดดูรายละเอียดได้
    window.recipes = [recipe];

    displayRecipes([recipe]);
  } catch (error) {
    resultsDiv.innerHTML = `<p class="text-danger text-center">${error.message}</p>`;
  }
}

  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
