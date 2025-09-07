<?php
$docs = [
    "GettingStarted.md",
    "API_Reference.pdf",
    "LegacySystem.txt"
];

$generatedDoc = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['topic'])) {
    $topic = htmlspecialchars(trim($_POST['topic']));
    $length = $_POST['length'] ?? 'brief';

    switch ($length) {
        case 'brief':
            $generatedDoc = "📄 Brief Summary for topic: {$topic}\nThis is a concise overview covering the essential points in 5-10 sentences.";
            break;
        case 'medium':
            $generatedDoc = "📄 Medium Summary for topic: {$topic}\nThis summary provides a detailed explanation extending roughly 1-2 pages.";
            break;
        case 'details':
            $generatedDoc = "📄 Detailed Summary for topic: {$topic}\nThis is an in-depth documentation containing key points, contextual info, and comprehensive insights.";
            break;
        default:
            $generatedDoc = "Invalid summary length selected.";
    }

    if (!in_array($topic, $docs)) {
        $docs[] = $topic;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Auto-generate Documentation | iFAST Smart Docs</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="style.css">
<script src="https://kit.fontawesome.com/1b5fdf4bb2.js" crossorigin="anonymous"></script>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container-fluid">
  <div class="row">

    <!-- Sidebar -->
    <div class="col-md-3 sidebar">
        <h5>📁 Existing Docs / Topics</h5>
        <ul class="file-list">
            <?php foreach ($docs as $doc): ?>
                <li><?= htmlspecialchars($doc) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 main-content">
        <h2>Auto-generate Documentation</h2>

        <form action="" method="POST">
            <div class="form-group">
                <label for="topic">Enter Topic / Details</label>
                <textarea name="topic" id="topic" rows="4" placeholder="Describe the topic..." required><?= isset($_POST['topic']) ? htmlspecialchars($_POST['topic']) : '' ?></textarea>
            </div>

            <div class="form-group">
                <label for="length">Select Summary Length</label>
                <select name="length" id="length">
                    <option value="brief" <?= (isset($_POST['length']) && $_POST['length']==='brief')?'selected':'' ?>>Brief (5-10 sentences)</option>
                    <option value="medium" <?= (isset($_POST['length']) && $_POST['length']==='medium')?'selected':'' ?>>Medium (1-2 Pages)</option>
                    <option value="details" <?= (isset($_POST['length']) && $_POST['length']==='details')?'selected':'' ?>>Details (Key points with context)</option>
                </select>
            </div>

            <button type="submit">Generate Documentation</button>
        </form>

        <?php if ($generatedDoc): ?>
            <div class="doc-box">
                <h5>📝 Generated Documentation</h5>
                <p><?= $generatedDoc ?></p>
            </div>
        <?php endif; ?>
    </div>

  </div>
</div>

<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>
