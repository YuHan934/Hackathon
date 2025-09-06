<?php
session_start();

// Ensure upload directory exists
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Handle file upload
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

// Predefined fun answers (demo)
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

    // Add new message to history
    $_SESSION['chat_history'][] = [
        'question' => $question,
        'answer' => $botAnswer
    ];
}

// Handle Reset Chat
if (isset($_POST['reset'])) {
    $_SESSION['chat_history'] = []; // Only clear chat
    // Keep uploadedFile and category intact
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Smart Summaries & Q&A | iFAST Smart Docs</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<style>
body { font-family: 'Segoe UI', sans-serif; background: #f7f9fc; }
.container-fluid { height: 100vh; }
.sidebar { background: #fff; padding: 1.5rem; height: 100vh; border-right: 1px solid #dee2e6; overflow-y: auto; }
.preview-box { background: #f1f5f9; border-radius: 8px; padding: 10px; margin-bottom: 1rem; text-align: center; }
iframe { width: 100%; height: 400px; border-radius: 8px; border: 1px solid #cbd5e0; }
.main-content { padding: 2rem; overflow-y: auto; }
.summary-box { background: #edf2f7; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; }
.chat-box { background: #fff; border: 1px solid #dee2e6; border-radius: 12px; padding: 1rem; max-width: auto; margin: 2rem; overflow-y: auto; max-height: 400px; }
.message-row { display: flex; align-items: flex-start; margin-bottom: 1rem; }
.avatar { margin-right: 0.5rem; font-size: 1.5rem; }
.message { padding: 0.8rem 1rem; border-radius: 16px; max-width: auto; }
.user { background: #d1e7ff; margin-left: auto; }
.bot { background: #e2e8f0; }
.suggested-questions { margin-top: 1rem; }
.suggested-questions button { margin: 0.2rem; }
.chat-controls { margin-top: 1rem; }
</style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- LEFT SIDE: Document Preview -->
        <div class="col-md-8 sidebar">
            <h5 class="mb-5">📄 Document Preview</h5>
            <div class="preview-box">
                <strong><?= htmlspecialchars($uploadedFile) ?></strong><br>
                <small>Category: <?= htmlspecialchars($category) ?></small>
                <?php $filePath = $uploadDir . $uploadedFile; ?>
            <iframe src="https://mozilla.github.io/pdf.js/web/viewer.html?file=<?= urlencode($filePath) ?>"></iframe>
            <p class="mt-2 text-muted">⚠️Demo preview (replace with uploaded file path)</p>

            </div>
        </div>

        <!-- RIGHT SIDE: Summary + Q&A -->
        <div class="col-md-4 main-content">
            <h3 class="mb-4">Smart Summaries & Q&A</h3>

            <!-- Summary Section -->
            <div class="summary-box p-1 mb-3 shadow-sm rounded">
                <h5 class="mb-3">🧠 AI-Generated Summary</h5>
                <div class="summary-content" style="line-height:1.2; color:#2C3E50;">
                    <p><strong>Document:</strong> <?= htmlspecialchars($uploadedFile) ?></p>
                    <p><strong>Category:</strong> <?= htmlspecialchars($category) ?></p>
                    <hr>
                    <p><strong>Key Points:</strong></p>
                    <ul>
                        <li>Point 1: Important concept explained.</li>
                        <li>Point 2: Another essential detail.</li>
                        <li>Point 3: Key takeaway or insight.</li>
                    </ul>
                    <p><strong>TL;DR:</strong> Covers main ideas, practical tips, and must-know info for quick understanding.</p>
                </div>
            </div>

            <!-- Q&A Section -->
            <div class="chat-box" id="chatBox">
                <h5>💬 Q&A with Your Document</h5>
                <?php if (!empty($_SESSION['chat_history'])): ?>
                    <?php foreach ($_SESSION['chat_history'] as $msg): ?>
                        <div class="message-row new-message">
                            <div class="message user"><?= $msg['question'] ?> ❓</div>
                            <span class="avatar">🤔</span>
                        </div>
                        <div class="message-row new-message">
                            <span class="avatar">🤖</span>
                            <div class="message bot"><?= $msg['answer'] ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">Ask me anything about this document!</p>
                <?php endif; ?>
            </div>

            <!-- Input box -->
             <form id="questionForm" method="POST" action="result.php" class="mt-3">
                <input type="hidden" name="docFile" value="<?= htmlspecialchars($uploadedFile) ?>">
                <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                <div class="input-group">
                    <input type="text" name="question" class="form-control" placeholder="Type your question..." required>
                    <div class="input-group-append">
                        <button class="btn btn-primary">Ask</button>
                    </div>
                </div>
            </form>

            <!-- Suggested questions -->
            <div class="suggested-questions mt-2">
                <form method="POST" action="result.php">
                    <input type="hidden" name="docFile" value="<?= htmlspecialchars($uploadedFile) ?>">
                    <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                    <button name="question" value="What is the main purpose?" class="btn btn-outline-secondary btn-sm">Main purpose?</button>
                    <button name="question" value="List 3 key points" class="btn btn-outline-secondary btn-sm">3 key points</button>
                    <button name="question" value="Explain in simple words" class="btn btn-outline-secondary btn-sm">Simple explanation</button>
                </form>

            <!-- Reset chat -->
            <div class="chat-controls">
                <form method="POST" action="result.php">
                    <input type="hidden" name="reset" value="1">
                    <button type="submit" class="btn btn-danger btn-sm">🗑️ Reset Chat</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Animation for new messages -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const chatBox = document.querySelector('.chat-box');
    const newMessages = chatBox.querySelectorAll('.message-row.new-message');

    newMessages.forEach((msg, i) => {
        msg.style.opacity = 0;
        msg.style.transform = 'translateY(20px)';
        setTimeout(() => {
            msg.style.transition = 'all 0.9s ease';
            msg.style.opacity = 1;
            msg.style.transform = 'translateY(0)';
            msg.classList.remove('new-message'); // prevent re-animation
        }, i * 100);
    });

    // Scroll to bottom
    chatBox.scrollTop = chatBox.scrollHeight;

    //Add AJAX JavaScript to handle form submission
    document.getElementById('questionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(html => {
        // Extract the updated chat box from the response
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newChatBox = doc.getElementById('chatBox');
        document.getElementById('chatBox').innerHTML = newChatBox.innerHTML;
    });
});
});
</script>
</body>
</html>
