<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Image Upload & Paste with Fake OCR | iFAST Smart Docs</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      padding: 2rem;
      background: #fffaf0;
      font-family: 'Segoe UI', sans-serif;
    }
    #dropArea {
      border: 2px dashed #aaa;
      border-radius: 10px;
      padding: 2rem;
      text-align: center;
      color: #666;
      background: #fff;
      cursor: pointer;
      position: relative;
    }
    #previewWrapper {
      position: relative;
      display: inline-block;
      margin-top: 1rem;
    }
    #deleteBtn {
      position: absolute;
      top: -10px;
      right: -10px;
      background: #ff4d4d;
      border: none;
      color: #fff;
      border-radius: 50%;
      width: 30px;
      height: 30px;
      font-weight: bold;
      cursor: pointer;
      display: none;
    }
    img {
      max-width: 100%;
      border-radius: 8px;
    }
    pre {
      background: #f7f7f7;
      padding: 1rem;
      margin-top: 1rem;
      border-radius: 6px;
    }
    input[type="file"] {
      display: none;
    }
    #uploadLabel {
      display: inline-block;
      padding: 0.5rem 1rem;
      background-color: #ff8050;
      color: #fff;
      border-radius: 30px;
      cursor: pointer;
      margin-top: 1rem;
    }
  </style>
</head>
<body>

<div class="container">
  <h2 class="mb-4">📋 Upload or Paste Screenshot for Fake OCR</h2>

  <div id="dropArea" tabindex="0">
    <p><strong>Paste a screenshot here (Ctrl+V)</strong> or <strong>click to select an image</strong></p>
    
    <input type="file" id="fileInput" accept="image/*">
    <label for="fileInput" id="uploadLabel">Choose Image</label>

    <div id="previewWrapper" style="display: none;">
      <button id="deleteBtn">&times;</button>
      <img id="preview" />
    </div>

    <pre id="ocrResult" style="display: none;">Fake OCR result will appear here...</pre>
  </div>
</div>

<script>
const dropArea = document.getElementById('dropArea');
const fileInput = document.getElementById('fileInput');
const preview = document.getElementById('preview');
const previewWrapper = document.getElementById('previewWrapper');
const ocrResult = document.getElementById('ocrResult');
const deleteBtn = document.getElementById('deleteBtn');

// Handle pasted image
dropArea.addEventListener('paste', function (e) {
  const items = e.clipboardData.items;
  for (let i = 0; i < items.length; i++) {
    const item = items[i];
    if (item.type.indexOf("image") !== -1) {
      const blob = item.getAsFile();
      displayImage(blob);
    }
  }
});

// Handle file input
fileInput.addEventListener('change', function () {
  const file = this.files[0];
  if (file && file.type.startsWith('image/')) {
    displayImage(file);
  }
});

// Click drop area to trigger file input
dropArea.addEventListener('click', () => fileInput.click());

// Show image + fake OCR
function displayImage(blob) {
  const reader = new FileReader();
  reader.onload = function (event) {
    preview.src = event.target.result;
    previewWrapper.style.display = "inline-block";
    deleteBtn.style.display = "block";

    // Fake OCR output
    ocrResult.textContent = `🧾 Fake OCR Output:

- Detected Date: 2023-09-07
- Invoice No: #123456
- Total Due: RM299.00
- Status: PAID ✅

(This is a placeholder OCR output for demo purposes.)
    `;
    ocrResult.style.display = "block";
  };
  reader.readAsDataURL(blob);
}

// Handle delete/reset
deleteBtn.addEventListener('click', function (e) {
  e.stopPropagation();
  preview.src = '';
  previewWrapper.style.display = "none";
  deleteBtn.style.display = "none";
  ocrResult.style.display = "none";
  fileInput.value = '';
});
</script>

</body>
</html>
