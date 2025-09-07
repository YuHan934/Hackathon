<?php
session_start();

// Ensure upload directory exists
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Handle file upload (if coming back here directly)
if (isset($_FILES['docFile']) && $_FILES['docFile']['error'] === 0) {
    $uploadedFile = $_FILES['docFile']['name'];
    $tmpName = $_FILES['docFile']['tmp_name'];
    $category = $_POST['category'] ?? 'General';
    $destination = $uploadDir . basename($uploadedFile);

    if (move_uploaded_file($tmpName, $destination)) {
        $_SESSION['uploadedFile'] = $uploadedFile;
        $_SESSION['category'] = $category;
    }
}

// Always read from session
$uploadedFile = $_SESSION['uploadedFile'] ?? "NoFile.pdf";
$category = $_SESSION['category'] ?? "Uncategorized";

// Initialize chat history
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

// Predefined demo answers
$answers = [
    "🤖 This document is about <strong>$uploadedFile</strong>. Very important! 🚀",
    "🎯 Key point: Stay organized and never miss deadlines! 📅",
    "🧠 Explained simply: It's like magic scheduling ✨",
    "📄 A roadmap for your tasks, simple and clear.",
    "🤓 Takeaway: Always double-check your notes 😅"
];

// Handle new question
$question = $_POST['question'] ?? null;
if ($question) {
    $question = htmlspecialchars(trim($question));
    $botAnswer = $answers[array_rand($answers)];

    // Add to history
    $_SESSION['chat_history'][] = [
        'question' => $question,
        'answer' => $botAnswer
    ];
}

// Reset Chat
if (isset($_POST['reset'])) {
    $_SESSION['chat_history'] = [];
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Smart Summaries & Q&A | iFAST Smart Docs</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
<style>
/* --- Styling --- */
:root {
  --bg-color: #FFE6B3;
  --text-color: #4A2C2A;
  --card-bg: #FFDAB3;
  --navbar-bg: #FFAD60;
  --button-bg: #FF8050;
  --button-text: #fff;
}
body {
  font-family: 'Poppins', sans-serif;
  background-color: var(--bg-color);
  color: var(--text-color);
  margin: 0;
}
.container-fluid {
  height: 100vh;
}
.sidebar {
  background-color: var(--card-bg);
  padding: 1.5rem;
  border-radius: 0 20px 20px 0;
  height: 100vh;
  overflow-y: auto;
}
.preview-box {
  background-color:#FFF3E0;
  border-radius:12px;
  padding:1rem;
  margin-bottom:1rem;
  text-align:center;
  box-shadow:0 8px 20px rgba(0,0,0,0.08);
}
iframe, embed {
  width:100%;
  height:500px;
  border-radius:8px;
  border:1px solid #FFB066;
}
.main-content {
  padding:2rem;
  overflow-y:auto;
}
.summary-box {
  background: var(--card-bg);
  padding:1.5rem;
  border-radius:8px;
  margin-bottom:2rem;
  box-shadow:0 10px 20px rgba(0,0,0,0.08);
}
.chat-box {
  background: #fff;
  border:1px solid #FFB066;
  border-radius:12px;
  padding:1rem;
  max-height:400px;
  overflow-y:auto;
}
.message-row {
  display:flex;
  align-items:flex-start;
  margin-bottom:1rem;
}
.message {
  padding:0.8rem 1rem;
  border-radius:16px;
  max-width:auto;
}
.user {
  background:#d1e7ff;
  margin-left:auto;
}
.bot {
  background:#e2e8f0;
}
.avatar {
  margin: 0 0.5rem;
  font-size: 1.5rem;
}
input[type="text"] {
  border-radius:50px;
  border:1px solid #FFB066;
  padding:0.5rem 1rem;
  width:100%;
}
button {
  border-radius:50px;
  background-color: var(--button-bg);
  color: var(--button-text);
  font-weight:600;
  padding:0.5rem 1.5rem;
  border:none;
}
button.btn-sm {
  border-radius: 12px;
  padding: 0.3rem 0.8rem;
  font-size: 0.85rem;
}
</style>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- LEFT: Document Preview -->
        <div class="col-md-8 sidebar">
            <h5 class="mb-4">📄 Document Preview</h5>
            <div class="preview-box">
                <strong><?= htmlspecialchars($uploadedFile) ?></strong><br>
                <small>Category: <?= htmlspecialchars($category) ?></small><br><br>

                <?php
                $filePath = $uploadDir . $uploadedFile;
                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                if ($ext === 'pdf') {
                    echo '<embed src="' . htmlspecialchars($filePath) . '" type="application/pdf">';
                } elseif ($ext === 'txt') {
                    echo '<div class="text-left p-3 border rounded" style="height: 300px; overflow-y:auto; background:#fff;">';
                    echo nl2br(htmlspecialchars(file_get_contents($filePath)));
                    echo '</div>';
                } elseif (in_array($ext, ['doc', 'docx'])) {
                    // For local testing, Office viewer may not work - show fallback message
                    $publicURL = "http://yourdomain.com/" . $filePath; // Replace with your real domain or hosting URL
                    echo '<iframe src="https://view.officeapps.live.com/op/embed.aspx?src=' . urlencode($publicURL) . '" frameborder="0"></iframe>';
                    echo '<p class="text-muted">⚠️ Word preview works only with public URLs.</p>';
                } else {
                    echo '<p class="text-danger">Preview not available for this file type.</p>';
                }
                ?>
            </div>
        </div>

        <!-- RIGHT: Summary + Q&A -->
        <div class="col-md-4 main-content">
            <h3>Smart Summaries & Q&A</h3>

            <!-- Summary -->
            <div class="summary-box">
                <h5>🧠 AI-Generated Summary</h5>
                <p><strong>Document:</strong> <?= htmlspecialchars($uploadedFile) ?></p>
                <p><strong>Category:</strong> <?= htmlspecialchars($category) ?></p>
                <hr>
                <ul>
                    <li>Point 1: Important concept explained.</li>
                    <li>Point 2: Another essential detail.</li>
                    <li>Point 3: Key takeaway or insight.</li>
                </ul>
                <p><strong>TL;DR:</strong> Covers main ideas, practical tips, and must-know info.</p>
            </div>

            <!-- Chat History -->
            <div class="chat-box" id="chatBox">
                <h5>💬 Q&A with Your Document</h5>
                <?php if (!empty($_SESSION['chat_history'])): ?>
                    <?php foreach ($_SESSION['chat_history'] as $msg): ?>
                        <div class="message-row">
                            <div class="message user"><?= $msg['question'] ?> ❓</div>
                            <span class="avatar">🤔</span>
                        </div>
                        <div class="message-row">
                            <span class="avatar">🤖</span>
                            <div class="message bot"><?= $msg['answer'] ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No questions asked yet. Try asking something!</p>
                <?php endif; ?>
            </div>

            <!-- Ask a Question -->
            <form id="questionForm" method="POST" action="result.php" class="mt-3">
                <input type="hidden" name="docFile" value="<?= htmlspecialchars($uploadedFile) ?>">
                <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                <div class="input-group">
                    <input type="text" name="question" class="form-control" placeholder="Ask your question..." required>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Ask</button>
                    </div>
                </div>
            </form>

            <!-- Suggested questions -->
            <div class="mt-3">
                <form method="POST" action="result.php">
                    <input type="hidden" name="docFile" value="<?= htmlspecialchars($uploadedFile) ?>">
                    <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                    <button name="question" value="What is the main purpose?" class="btn btn-sm btn-warning">Main purpose?</button>
                    <button name="question" value="List 3 key points" class="btn btn-sm btn-warning">3 key points</button>
                    <button name="question" value="Explain in simple words" class="btn btn-sm btn-warning">Simple explanation</button>
                </form>
            </div>

            <!-- Reset chat -->
            <div class="mt-3">
                <form method="POST" action="result.php">
                    <input type="hidden" name="reset" value="1" />
                    <button type="submit" class="btn btn-danger btn-sm">🗑️ Reset Chat</button>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const chatBox = document.getElementById('chatBox');
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    // Optional: AJAX submit for question form
    const questionForm = document.getElementById('questionForm');
    if(questionForm) {
        questionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch(this.action, { method: 'POST', body: formData })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    document.getElementById('chatBox').innerHTML = doc.getElementById('chatBox').innerHTML;
                    // Clear input
                    this.querySelector('input[name="question"]').value = '';
                    chatBox.scrollTop = chatBox.scrollHeight;
                });
        });
    }
});
</script>

</body>
</html>
