<?php
session_start();

// Initialize chat history
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

// Get uploaded file and category
$uploadedFile = $_POST['docFile'] ?? "NoFile.pdf";
$category = $_POST['category'] ?? "Uncategorized";

// Predefined fun answers (demo)
$answers = [
    "🤖 This document is about <strong>$uploadedFile</strong>. Very important! 🚀",
    "🎯 Key point: Stay organized and never miss deadlines! 📅",
    "🧠 Explained simply: It's like magic scheduling ✨",
    "📄 A roadmap for your tasks, simple and clear.",
    "🤓 Takeaway: Always double-check your notes 😅"
];

// Handle Reset Chat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
    $_SESSION['chat_history'] = [];
    header("Location: result.php");
    exit;
}

// Handle new question
$question = $_POST['question'] ?? null;
$botAnswer = null;
if ($question) {
    $question = htmlspecialchars(trim($question));
    $botAnswer = $answers[array_rand($answers)];

    // Add new message to history
    $_SESSION['chat_history'][] = [
        'question' => $question,
        'answer' => $botAnswer
    ];
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
.main-content { padding: 2rem; overflow-y: auto; }
.summary-box { background: #edf2f7; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; }
.chat-box { background: #fff; border: 1px solid #dee2e6; border-radius: 12px; padding: 1rem; max-width: 1000px; margin: 2rem auto; overflow-y: auto; max-height: 400px; }
.message-row { display: flex; align-items: flex-start; margin-bottom: 1rem; }
.avatar { margin-right: 0.5rem; font-size: 1.5rem; }
.message { padding: 0.8rem 1rem; border-radius: 16px; max-width: 70%; }
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
            <h5 class="mb-7">📄 Document Preview</h5>
            <div class="preview-box">
                <strong><?= htmlspecialchars($uploadedFile) ?></strong><br>
                <small>Category: <?= htmlspecialchars($category) ?></small>
            </div>

            <!-- Document preview (currently using demo PDF.js viewer) -->
            <iframe src="https://mozilla.github.io/pdf.js/web/viewer.html?file=sample.pdf" width=950px></iframe>
            <p class="mt-2 text-muted">⚠️Demo preview (replace with uploaded file path)</p>
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
            <div class="chat-box">
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
            <form method="POST" action="result.php" class="mt-3">
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
            </div>

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
});
</script>

</body>
</html>
