<?php
session_start();

// Initialize history if not exists
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

// Get uploaded file and category
$uploadedFile = $_POST['docFile'] ?? "Demo_Document.pdf";
$category = $_POST['category'] ?? "General";

// Predefined fun answers
$answers = [
    "🤖 This document is basically about <strong>$uploadedFile</strong>. Super important stuff! 🚀",
    "🎯 The main point? Stay organized and never miss deadlines! 📅",
    "🧠 If I explain it to a 5-year-old: it's like magic scheduling ✨",
    "📄 Think of this as a roadmap for your tasks, nice and simple.",
    "🤓 Key takeaway: Always check your notes twice, trust me 😅"
];

// Handle new question
$question = $_POST['question'] ?? null;
$botAnswer = null;
if ($question) {
    $question = htmlspecialchars(trim($question));
    $botAnswer = $answers[array_rand($answers)];

    // Add to session history
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
    <title>Smart Summaries Result | iFAST Smart Docs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f7f9fc;
            font-family: 'Segoe UI', sans-serif;
        }
        .container-fluid {
            height: 100vh;
        }
        .sidebar {
            background-color: #fff;
            padding: 1.5rem;
            height: 100vh;
            border-right: 1px solid #dee2e6;
            overflow-y: auto;
        }
        .preview-box {
            background: #f1f5f9;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        .main-content {
            padding: 2rem;
            overflow-y: auto;
        }
        .summary-box {
            background: #edf2f7;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .qa-box {
            background: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        iframe {
            width: 100%;
            height: 400px;
            border-radius: 8px;
            border: 1px solid #cbd5e0;
        }
        .chat-box { 
            background: #fff; 
            border: 1px solid #dee2e6; 
            border-radius: 12px; 
            padding: 1rem; 
            max-width: 1000px; 
            margin: 2rem auto; 
        }
        .message-row {
            display: flex; 
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        .avatar { 
            margin-right: 0.5rem;
            font-size: 1.5rem;
        }
        .message { 
            padding: 0.8rem 1rem; 
            border-radius: 16px; 
            max-width: 70%; 
        }
        .user { 
            background: #d1e7ff; 
            margin-left: auto; 
        }
        .bot { 
            background: #e2e8f0; 
        }
        .suggested-questions { 
            margin-top: 1rem;
         }
        .suggested-questions button { 
            margin: 0.2rem; 
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- LEFT SIDE: Document Preview -->
        <div class="col-md-4 sidebar">
            <h5 class="mb-4">📄 Document Preview</h5>
            <div class="preview-box">
                <strong><?= htmlspecialchars($uploadedFile) ?></strong><br>
                <small>Category: <?= htmlspecialchars($category) ?></small>
            </div>

            <!-- Document preview (currently using demo PDF.js viewer) -->
            <iframe src="https://mozilla.github.io/pdf.js/web/viewer.html?file=sample.pdf"></iframe>
            <p class="mt-2 text-muted">⚠️Demo preview (replace with uploaded file path)</p>
        </div>

        <!-- RIGHT SIDE: Summary + Q&A -->
        <div class="col-md-8 main-content">
            <h2 class="mb-4">Smart Summaries & Q&A</h2>

            <!-- Summary Section -->
            <div class="summary-box">
                <h5>🧠 AI-Generated Summary</h5>
                <p><?= $summary ?></p>
            </div>

            <!-- Q&A Section -->
             <div class="chat-box">
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
                            <p class="text-muted">Ask me anything about this document!</p>
                            <?php endif; ?>

                            <!-- Input box -->
                             <form method="POST" action="result.php">
                                <input type="hidden" name="docFile" value="<?= htmlspecialchars($uploadedFile) ?>">
                                <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                                <div class="input-group mt-2">
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
                        </div>
                    </div>
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const chatBox = document.querySelector('.chat-box');
            // Only animate messages with class 'new-message'
            const newMessages = chatBox.querySelectorAll('.message-row.new-message');
            newMessages.forEach((msg, i) => {
                msg.style.opacity = 0;
                msg.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    msg.style.transition = 'all 0.6s ease';
                    msg.style.opacity = 1;
                    msg.style.transform = 'translateY(0)';
                    // Remove 'new-message' class after animation so it won't animate again
                    msg.classList.remove('new-message');
                }, i * 100);
            });
            // Always scroll to bottom
            chatBox.scrollTop = chatBox.scrollHeight;
            });
            </script>
</body>
</html>
