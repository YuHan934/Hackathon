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

    // Simulate documentation generation based on length option
    switch ($length) {
        case 'brief':
            $generatedDoc = <<<DOC
📄 <strong>Brief Summary</strong> for topic: <strong>{$topic}</strong><br><br>
This is a concise overview of <em>{$topic}</em>, covering the essential points in 5-10 sentences.
DOC;
            break;

        case 'medium':
            $generatedDoc = <<<DOC
📄 <strong>Medium Summary</strong> for topic: <strong>{$topic}</strong><br><br>
This summary provides a detailed explanation of <em>{$topic}</em> extending roughly 1-2 pages. It includes background, core concepts, and examples to help understand the topic thoroughly.
DOC;
            break;

        case 'details':
            $generatedDoc = <<<DOC
📄 <strong>Detailed Summary</strong> for topic: <strong>{$topic}</strong><br><br>
This is an in-depth documentation containing key points, contextual information, and comprehensive insights about <em>{$topic}</em>. It covers all critical aspects, definitions, use cases, and best practices.
DOC;
            break;

        default:
            $generatedDoc = "Invalid summary length selected.";
    }

    // For demo: add topic as a "file" in docs list if not present
    if (!in_array($topic, $docs)) {
        $docs[] = $topic;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Auto-generate Documentation | iFAST Smart Docs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <style>
        body {
            background-color: #f7f9fc;
            font-family: 'Segoe UI', sans-serif;
        }
        .sidebar {
            background-color: #fff;
            padding: 1.5rem;
            height: 100vh;
            border-right: 1px solid #dee2e6;
            overflow-y: auto;
        }
        .file-list {
            list-style: none;
            padding-left: 0;
        }
        .file-list li {
            margin-bottom: 10px;
            font-weight: 500;
            color: #2d3748;
            cursor: default;
        }
        .main-content {
            padding: 2rem;
        }
        .form-group label {
            font-weight: 600;
        }
        .doc-box {
            margin-top: 2rem;
            background: #edf2f7;
            padding: 1.5rem;
            border-radius: 8px;
            color: #2d3748;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 sidebar">
            <h5 class="mb-4">📁 Existing Docs / Topics</h5>
            <ul class="file-list">
                <?php foreach ($docs as $doc): ?>
                    <li><?= htmlspecialchars($doc) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Main content -->
        <div class="col-md-9 main-content">
            <h2 class="mb-4">Auto-generate Documentation</h2>

            <form action="autogen.php" method="POST">
                <div class="form-group">
                    <label for="topic">Enter Topic / Details for Documentation</label>
                    <textarea name="topic" id="topic" class="form-control" rows="4" placeholder="Describe the topic or details you want to generate documentation for..." required><?= isset($_POST['topic']) ? htmlspecialchars($_POST['topic']) : '' ?></textarea>
                </div>

                <div class="form-group">
                    <label for="length">Select Summary Length</label>
                    <select name="length" id="length" class="form-control">
                        <option value="brief" <?= (isset($_POST['length']) && $_POST['length'] === 'brief') ? 'selected' : '' ?>>Brief (5-10 sentences)</option>
                        <option value="medium" <?= (isset($_POST['length']) && $_POST['length'] === 'medium') ? 'selected' : '' ?>>Medium (1-2 Pages)</option>
                        <option value="details" <?= (isset($_POST['length']) && $_POST['length'] === 'details') ? 'selected' : '' ?>>Details (Key points with context)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Generate Documentation</button>
            </form>

            <?php if ($generatedDoc): ?>
                <div class="doc-box mt-4">
                    <h5>📝 Generated Documentation</h5>
                    <p><?= $generatedDoc ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
