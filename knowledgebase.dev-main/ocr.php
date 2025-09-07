<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Image Upload & Paste with Fake OCR | iFAST Smart Docs</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
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
      z-index: 10;
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
      white-space: pre-wrap;
      text-align: left;
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
    /* Style for Q&A section */
    #qaSection {
      margin-top: 2rem;
      text-align: left;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }
    #questionInput {
      width: 100%;
      height: 60px;
      padding: 0.5rem;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 6px;
      resize: vertical;
    }
    #askBtn {
      margin-top: 0.5rem;
      background-color: #4caf50;
      color: white;
      border: none;
      padding: 0.6rem 1.2rem;
      border-radius: 6px;
      cursor: pointer;
    }
    #answer {
      margin-top: 1rem;
      background: #e9f7ef;
      padding: 1rem;
      border-radius: 6px;
      min-height: 50px;
      white-space: pre-wrap;
    }
  </style>
</head>
<body>

<div class="container">
  <h2 class="mb-4">📋 Upload or Paste Screenshot for Fake OCR</h2>

  <div id="dropArea" tabindex="0">
    <p><strong>Paste a screenshot here (Ctrl+V)</strong> or <strong>click to select an image</strong></p>
    
    <input type="file" id="fileInput" accept="image/*" />
    <label for="fileInput" id="uploadLabel">Choose Image</label>

    <div id="previewWrapper" style="display: none;">
      <button id="deleteBtn" aria-label="Delete image">&times;</button>
      <img id="preview" alt="Uploaded preview" />
    </div>

    <pre id="ocrResult" style="display: none;">Fake OCR result will appear here...</pre>

    <!-- Contextual Q&A Section -->
    <div id="qaSection">
      <h4>🤖 Ask about the doc</h4>
      <textarea id="questionInput" placeholder="Type your question about the document here..."></textarea>
      <button id="askBtn">Ask</button>
      <div id="answer"></div>
    </div>
  </div>
</div>

<script>
  const dropArea = document.getElementById('dropArea');
  const fileInput = document.getElementById('fileInput');
  const preview = document.getElementById('preview');
  const previewWrapper = document.getElementById('previewWrapper');
  const ocrResult = document.getElementById('ocrResult');
  const deleteBtn = document.getElementById('deleteBtn');

  const questionInput = document.getElementById('questionInput');
  const askBtn = document.getElementById('askBtn');
  const answerDiv = document.getElementById('answer');

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

  // Click drop area to trigger file input (except on interactive elements)
  dropArea.addEventListener('click', (e) => {
    const ignoredElements = ['INPUT', 'BUTTON', 'LABEL', 'PRE', 'IMG', 'TEXTAREA', 'DIV'];
    if (!ignoredElements.includes(e.target.tagName)) {
      fileInput.click();
    }
  });

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

      // Clear previous answer and question on new image load
      questionInput.value = '';
      answerDiv.textContent = '';
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
    questionInput.value = '';
    answerDiv.textContent = '';
  });

  // Fake Q&A functionality: respond with canned answer referencing OCR text
  askBtn.addEventListener('click', () => {
    const question = questionInput.value.trim();
    if (!question) {
      answerDiv.textContent = 'Please enter a question first.';
      return;
    }

    // Simple fake context Q&A response using OCR text
    const ocrText = ocrResult.textContent || '';
    let response = '';

    if (/date/i.test(question)) {
      response = 'The detected date on the document is 2023-09-07.';
    } else if (/invoice/i.test(question)) {
      response = 'The invoice number is #123456.';
    } else if (/total/i.test(question)) {
      response = 'The total due amount is RM299.00.';
    } else if (/status/i.test(question)) {
      response = 'The payment status is PAID.';
    } else {
      response = "Sorry, I don't have an answer for that question based on the document.";
    }

    answerDiv.textContent = response;
  });
</script>

</body>
</html>
