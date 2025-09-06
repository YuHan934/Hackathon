<?php
session_start();

// Example files
$files = [
    "Tutorial_03.pdf",
    "Lecture_Notes.docx",
    "LegacySystem.txt"
];

// Handle file upload
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Summarize | iFAST Smart Docs</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://kit.fontawesome.com/1b5fdf4bb2.js" crossorigin="anonymous"></script>

<style>
:root {
  --bg-color: #FFE6B3;
  --text-color: #4A2C2A;
  --card-bg: #FFDAB3;
  --navbar-bg: #FFAD60;
  --button-bg: #FF8050;
  --button-text: #fff;

  --blue-bg: #7fa3c7ff;
  --blue-text: #1e2e5aff;
  --blue-card: #E0F2FF;
  --blue-navbar: #2C3E50;
  --blue-button: #2a6a94ff;
  --blue-button-text: #fff;
}

body {
  background-color: var(--bg-color);
  color: var(--text-color);
  font-family: 'Poppins', sans-serif;
  transition: all 0.5s ease;
  margin:0;
}

/* Navbar */
.navbar {
  background-color: var(--navbar-bg);
  border-radius: 0 0 20px 20px;
  padding: 1rem 2rem;
  box-shadow: 0 6px 15px rgba(0,0,0,0.1);
}
.navbar-brand { font-weight:700; font-size:1.8rem; color:#fff !important; }
.navbar-brand small { font-weight:400; font-size:0.9rem; color:#FFF3E0; }
.nav-link { color:#fff !important; }
.nav-link:hover { color:#FFDAB3 !important; }

/* Sidebar */
.sidebar {
  background-color: var(--card-bg);
  padding: 1.5rem;
  border-radius: 0 20px 20px 0;
  height: 100vh;
}
.file-list { list-style:none; padding-left:0; }
.file-list li { margin-bottom:10px; font-weight:500; color: var(--text-color); transition: transform 0.3s; }
.file-list li:hover { transform: translateX(5px); color:#201714ff; }

.folder-icon { display:inline-block; animation: bounce 1.2s infinite ease-in-out; margin-right:6px; }
@keyframes bounce { 0%,100%{transform:translateY(0);} 50%{transform:translateY(-6px);} }

/* Main Content */
.main-content { padding:2rem; }
input[type="text"], input[type="file"] {
  background-color:#FFF3E0; border:1px solid #FFB066; border-radius:50px; padding:0.2rem 1rem;
  transition: all 0.3s;
}
input:focus { border-color:#FF5722; box-shadow:0 0 8px rgba(255,87,34,0.3); outline:none; }

button {
  background-color: var(--button-bg); color: var(--button-text);
  border-radius:50px; padding:0.6rem 2rem; font-weight:600;
  transition: all 0.3s;
}
button:hover { background-color:#c5bebdff; transform:translateY(-2px); box-shadow:0 5px 10px rgba(0,0,0,0.2); }

/* Blue Theme */
body.blue-theme {
  --bg-color: var(--blue-bg);
  --text-color: var(--blue-text);
  --card-bg: var(--blue-card);
  --navbar-bg: var(--blue-navbar);
  --button-bg: var(--blue-button);
  --button-text: var(--blue-button-text);
}
</style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
  <a class="navbar-brand font-weight-bold" href="#">iFAST Smart Docs <small>Documentation & Summaries</small></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarText">
    <ul class="navbar-nav ml-auto align-items-center">
      <li class="nav-item">
        <a class="nav-link" href="https://yourprojecthomepage.com" target="_blank">Project Homepage</a>
      </li>
      <li class="nav-item dropdown">
        <button class="btn btn-outline-light dropdown-toggle" type="button" data-toggle="dropdown">
          English
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="#">French</a>
          <a class="dropdown-item" href="#">Bulgarian</a>
        </div>
      </li>
    </ul>
  </div>
</nav>

<div class="container-fluid">
  <div class="row">

    <!-- Sidebar -->
    <div class="col-md-3 sidebar">
        <h5>📁 Uploaded Files</h5>
        <ul class="file-list">
            <?php foreach($files as $file): ?>
            <li><?= htmlspecialchars($file) ?></li>
            <?php endforeach; ?>
        </ul>
        <hr>
        <form action="result.php" method="POST" enctype="multipart/form-data">
          <div class="form-group" style="display:flex; align-items:center;">
            <span class="folder-icon">📂</span>
            <input type="text" name="category" class="form-control" placeholder="+ New Folder" required>
          </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 main-content">
        <h2>Upload & Summarize Documentation</h2>
        <div class="form-group">
          <input type="file" name="docFile" class="form-control" required>
        </div>
        <button type="submit" class="btn">Upload & Summarize</button>
        </form>
        <?php if(isset($error)): ?>
          <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
