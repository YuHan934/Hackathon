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
    $_SESSION['chat_history'] = []; 
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
<link rel="stylesheet" href="style.css">
<style>
:root {
  --bg-color: #FFE6B3;
  --text-color: #4A2C2A;
  --card-bg: #FFDAB3;
  --navbar-bg: #FFAD60;
  --button-bg: #FF8050;
  --button-text: #fff;
}

/* Body */
body { font-family: 'Poppins', sans-serif; background-color: var(--bg-color); color: var(--text-color); margin:0; }
.container-fluid { height: 100vh; }

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
  overflow-y: auto;
}
.preview-box {
  background-color:#FFF3E0;
  border-radius:12px;
  padding:1rem;
  margin-bottom:1rem;
  text-align:center;
  box-shadow:0 8px 20px rgba(0,0,0,0.08);
  transition: transform 0.4s ease;
}
.preview-box:hover { transform: scale(1.02); }
iframe { width:100%; height:400px; border-radius:8px; border:1px solid #FFB066; }

/* Main content */
.main-content { padding:2rem; overflow-y:auto; }
.summary-box { background: var(--card-bg); padding:1.5rem; border-radius:8px; margin-bottom:2rem; box-shadow:0 10px 20px rgba(0,0,0,0.08); width:auto; transition: all 0.4s ease; }
.summary-box:hover { transform: scale(1.01) translateY(-2px); }

/* Chat box */
.chat-box {
  background: #fff;
  border:1px solid #FFB066;
  border-radius:12px;
  padding:1rem;
  max-width:100%;
  margin-bottom:1rem;
  overflow-y:auto;
  max-height:400px;
  transition: all 0.5s ease;
}
.message-row { display:flex; align-items:flex-start; margin-bottom:1rem; opacity:0; transform: translateY(20px) scale(0.95); }
.message { padding:0.8rem 1rem; border-radius:16px; max-width:auto; transition: all 0.5s ease; }
.user { background:#d1e7ff; margin-left:auto; }
.bot { background:#e2e8f0; }
.avatar { margin-right:0.5rem; font-size:1.5rem; transform: scale(0); transition: all 0.5s ease; }

/* Inputs & buttons */
input[type="text"] { border-radius:50px; border:1px solid #FFB066; padding:0.5rem 1rem; width:100%; transition: all 0.3s ease; }
input[type="text"]:focus { outline:none; box-shadow:0 0 8px rgba(255,128,80,0.3); border-color:#FF8050; }
button { border-radius:50px; background-color: var(--button-bg); color: var(--button-text); font-weight:600; padding:0.5rem 1.5rem; transition:all 0.3s; }
button:hover { transform:translateY(-2px) scale(1.05); box-shadow:0 5px 10px rgba(0,0,0,0.2); }

/* Suggested questions buttons */
.suggested-questions button { margin:0.2rem; border-radius:50px; background:#FFAD60; color:#fff; transition: all 0.3s ease; }
.suggested-questions button:hover { background:#FF8050; transform: scale(1.05); }
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
            <div class="summary-box">
                <h5>🧠 AI-Generated Summary</h5>
                <p><strong>Document:</strong> <?= htmlspecialchars($uploadedFile) ?></p>
                <p><strong>Category:</strong> <?= htmlspecialchars($category) ?></p>
                <hr>
                <p><strong>Key Points:</strong></p>
                <ul>
                    <li>Point 1: Important concept explained.</li>
                    <li>Point 2: Another essential detail.</li>
                    <li>Point 3: Key takeaway or insight.</li>
                </ul>
                <p><strong>TL;DR:</strong> Covers main ideas, practical tips, and must-know info.</p>
            </div>

            <!-- Q&A Section -->
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
                        <button type="submit" class="btn btn-primary">Ask</button>
                    </div>
                </div>
            </form>

            <!-- Suggested questions -->
            <div class="suggested-questions mt-2">
                <form method="POST" action="result.php">
                    <input type="hidden" name="docFile" value="<?= htmlspecialchars($uploadedFile) ?>">
                    <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                    <button name="question" value="What is the main purpose?">Main purpose?</button>
                    <button name="question" value="List 3 key points">3 key points</button>
                    <button name="question" value="Explain in simple words">Simple explanation</button>
                </form>
            </div>

            <!-- Reset chat -->
            <div class="chat-controls mt-2">
                <form method="POST" action="result.php">
                    <input type="hidden" name="reset" value="1">
                    <button type="submit" class="btn btn-danger btn-sm">🗑️ Reset Chat</button>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
// Animate messages and avatars
function animateChat() {
    const chatBox = document.getElementById('chatBox');
    const rows = chatBox.querySelectorAll('.message-row');
    rows.forEach((row, i) => {
        setTimeout(() => {
            row.style.transition = 'all 0.6s cubic-bezier(0.68,-0.55,0.265,1.55)';
            row.style.opacity = 1;
            row.style.transform = 'translateY(0) scale(1)';
            const avatar = row.querySelector('.avatar');
            if(avatar){
                avatar.style.transform = 'scale(1)';
            }
        }, i * 120);
    });

    // Smooth scroll to bottom
    chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
}

document.addEventListener('DOMContentLoaded', () => {
    animateChat();

    document.getElementById('questionForm').addEventListener('submit', function(e){
        e.preventDefault();
        const formData = new FormData(this);
        fetch(this.action, { method:'POST', body: formData })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html,'text/html');
            document.getElementById('chatBox').innerHTML = doc.getElementById('chatBox').innerHTML;
            animateChat();
        });
    });
});
</script>
</body>
</html>
