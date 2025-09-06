<?php
$files = [
    "Tutorial_03.pdf",
    "Lecture_Notes.docx",
    "LegacySystem.txt"
];


// Handle file upload (basic version)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['docFile'])) {
    $uploadedFile = $_FILES['docFile']['name'];
    $category = $_POST['category'] ?? 'Uncategorized';

    // Save file (simulated)
    // In real app: move_uploaded_file($_FILES['docFile']['tmp_name'], "uploads/$uploadedFile");

    $summary = "📄 Summary of <strong>$uploadedFile</strong> under <em>$category</em> (simulated)...";

    // You would normally save file + category data here
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Summarize Files | iFAST Smart Docs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f7f9fc;
            font-family: 'Segoe UI', sans-serif;
        }
        .sidebar {
            background-color: #ffffff;
            padding: 1.5rem;
            height: 100vh;
            border-right: 1px solid #dee2e6;
        }
        .file-list {
            list-style: none;
            padding-left: 0;
        }
        .file-list li {
            margin-bottom: 10px;
            font-weight: 500;
            color: #2d3748;
        }
        .file-list small {
            color: #718096;
        }
        .main-content {
            padding: 2rem;
        }
        .form-group label {
            font-weight: 600;
        }
        .summary-box {
            margin-top: 2rem;
            background: #edf2f7;
            padding: 1.5rem;
            border-radius: 8px;
            color: #2d3748;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 sidebar">
            <h5 class="mb-4">📁 Uploaded Files</h5>
            <ul class="file-list">
    <?php foreach ($files as $file): ?>
    <li><?= htmlspecialchars($file) ?></li>
<?php endforeach; ?>

</ul>


            <hr>

            <form action="summarize.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="category">📂 Select or Create Folders</label>
                    <input type="text" name="category" class="form-control" placeholder="+ New Folders" required>
                </div>
        </div>

        <!-- Main content -->
        <div class="col-md-9 main-content">
            <h2 class="mb-4">Summarize Documentation</h2>

                <div class="form-group">
                    <label for="docFile">Upload File</label>
                    <input type="file" name="docFile" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Upload & Summarize</button>
            </form>

            <?php if (isset($summary)): ?>
                <div class="summary-box mt-4">
                    <h5>🧠 AI-Generated Summary</h5>
                    <p><?= $summary ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
