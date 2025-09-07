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
<link rel="stylesheet" href="style.css">
<script src="https://kit.fontawesome.com/1b5fdf4bb2.js" crossorigin="anonymous"></script>

</head>
<body>

<!-- Navbar -->
<?php include 'navbar.php'; ?>

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
        <button type="submit">Upload & Summarize</button>
        </form>
        <?php if(isset($error)): ?>
          <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </div>
  </div>
</div>

<!-- Footer -->
<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="script.js"></script>
</body>
</html>
