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

    // Save file to uploads directory
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $destination = $uploadDir . basename($uploadedFile);

    if (move_uploaded_file($_FILES['docFile']['tmp_name'], $destination)) {
        $_SESSION['uploadedFile'] = $uploadedFile;
        $_SESSION['category'] = $category;

        // Redirect to result.php after upload
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
<style>
body { background-color: #f7f9fc; font-family: 'Segoe UI', sans-serif; }
.sidebar { background-color: #fff; padding: 1.5rem; height: 100vh; border-right: 1px solid #dee2e6; }
.file-list { list-style: none; padding-left: 0; }
.file-list li { margin-bottom: 10px; font-weight: 500; color: #2d3748; }
.file-list small { color: #718096; }
.main-content { padding: 2rem; }
.summary-box { margin-top: 2rem; background: #edf2f7; padding: 1.5rem; border-radius: 8px; color: #2d3748; }
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
    <form action="Summarize.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>📂 Select or Create Folder</label>
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
    <button type="submit" class="btn btn-primary">Upload & Summarize</button>
    </form>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
</div>

</div>
</div>
</body>
</html>
