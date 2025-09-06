<?php
session_start();

// Predefined example files
$files = [
    "Tutorial_03.pdf",
    "Lecture_Notes.docx",
    "LegacySystem.txt"
];

// Handle file upload & redirect
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['docFile'])) {
    $uploadedFile = $_FILES['docFile']['name'];
    $category = $_POST['category'] ?? 'Uncategorized';

    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $destination = $uploadDir . basename($uploadedFile);

    if (move_uploaded_file($_FILES['docFile']['tmp_name'], $destination)) {
        $_SESSION['uploadedFile'] = $uploadedFile;
        $_SESSION['category'] = $category;

        header("Location: result.php");
        exit;
    } else {
        $error = "Failed to upload file.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Summarize Files | iFAST Smart Docs</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://kit.fontawesome.com/1b5fdf4bb2.js" crossorigin="anonymous"></script>
<style>
:root {
  --bg-color: #FFE6B3;      /* warm boba orange */
  --text-color: #4A2C2A;
  --card-bg: #FFDAB3;
  --button-bg: #FF8050;
  --button-text: #fff;
  --input-bg: #FFF3E0;
  --input-border: #FFB066;
  --blue-bg: #7fa3c7ff;       /* soft blue theme */
  --blue-text: #1e2e5aff;
}

body {
  background-color: var(--bg-color);
  font-family: 'Segoe UI', sans-serif;
  transition: all 0.5s ease;
}

.sidebar {
  background-color: var(--card-bg);
  padding: 1.5rem;
  height: 100vh;
  border-right: 1px solid transparent;
  border-radius: 0 20px 20px 0;
  transition: all 0.4s ease;
}

.file-list {
  list-style: none;
  padding-left: 0;
}

.file-list li {
  margin-bottom: 12px;
  font-weight: 500;
  color: var(--text-color);
  transition: transform 0.3s ease;
}
.file-list li:hover {
  transform: translateX(5px);
  color: #201714ff;
}

.file-list small {
  color: #7F4F24;
}

.main-content {
  padding: 2rem;
}

.summary-box {
  margin-top: 2rem;
  background: #FFF2E0;
  padding: 1.5rem;
  border-radius: 12px;
  color: var(--text-color);
  box-shadow: 0 10px 20px rgba(0,0,0,0.08);
  transition: transform 0.4s ease, box-shadow 0.4s ease;
}
.summary-box:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 25px rgba(0,0,0,0.12);
}

input[type="text"], input[type="file"] {
  background-color: var(--input-bg);
  border: 1px solid var(--input-border);
  border-radius: 50px;
  padding: 0.2rem 1rem;
  transition: all 0.3s ease;
}
input[type="text"]:focus, input[type="file"]:focus {
  border-color: #FF5722;
  box-shadow: 0 0 8px rgba(255,87,34,0.3);
  outline: none;
}

button {
  background-color: var(--button-bg);
  color: var(--button-text);
  border-radius: 50px;
  padding: 0.6rem 2rem;
  font-weight: 600;
  transition: all 0.3s ease;
}
button:hover {
  background-color: #c5bebdff;
  transform: translateY(-2px);
  box-shadow: 0 5px 10px rgba(0,0,0,0.2);
}

/* Blue Theme */
body.blue-theme {
  --bg-color: var(--blue-bg);
  --text-color: var(--blue-text);
  --card-bg: #E0F2FF;
  --button-bg: #2a6a94ff;
  --button-text: #fff;
  --input-bg: #F0F8FF;
  --input-border: #5d758dff;
}

/* Smooth transitions */
* {
  transition: all 0.3s ease;
}

/* Folder icon animation */
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
}

.folder-icon {
    animation: bounce 1.2s infinite ease-in-out;
    display: inline-block;
}
</style>
</head>
<body>

<div class="container-fluid">
  <div class="row">

    <!-- Sidebar: List of example files -->
    <div class="col-md-3 sidebar">
        <h5 class="mb-4">📁 Uploaded Files</h5>
        <ul class="file-list">
            <?php foreach ($files as $file): ?>
                <li><?= htmlspecialchars($file) ?></li>
            <?php endforeach; ?>
        </ul>
        <hr>

       <!-- Upload form -->
        <form action="result.php" method="POST" enctype="multipart/form-data">
            <div class="form-group" style="position: relative;">
                <label style="display: flex; align-items: center; font-weight: 600; font-size: 1.1rem;">
                    <span class="folder-icon" style="display: inline-block; margin-right: 8px;">📂</span>
                    Select or Create Folder
                </label>
                <input type="text" name="category" class="form-control" placeholder="+ New Folder" required>
            </div>
    </div>

    <!-- Main content -->
    <div class="col-md-9 main-content">
        <h2 class="mb-4">Summarize Documentation</h2>
        <div class="form-group">
            <label>Upload File</label>
            <input type="file" name="docFile" class="form-control" required>
        </div>
        <button type="submit" class="btn">Upload & Summarize</button>
        </form>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <br>
        <!-- Theme toggle button -->
        <button id="themeToggle" class="btn mt-4">🔄 Change Theme</button>
    </div>
  </div>
</form>
</div>

<script>
const themeToggle = document.getElementById('themeToggle');
themeToggle.addEventListener('click', () => {
    document.body.classList.toggle('blue-theme');
    // Store preference
    if(document.body.classList.contains('blue-theme')){
        localStorage.setItem('theme', 'blue');
    } else {
        localStorage.setItem('theme', 'boba');
    }
});

// Load previous theme
if(localStorage.getItem('theme') === 'blue') {
    document.body.classList.add('blue-theme');
}
</script>
</body>
</html>
