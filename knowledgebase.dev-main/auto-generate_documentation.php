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
<script src="https://kit.fontawesome.com/1b5fdf4bb2.js" crossorigin="anonymous"></script>
<style>
:root {
  --bg-color: #FFE6B3;
  --text-color: #4A2C2A;
  --card-bg: #FFDAB3;
  --navbar-bg: #FFAD60;
  --button-bg: #FF8050;
  --button-text: #fff;
}

body {
  background-color: var(--bg-color);
  color: var(--text-color);
  font-family: 'Poppins', sans-serif;
  margin: 0;
}

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
}
.file-list { list-style:none; padding-left:0; }
.file-list li { margin-bottom:10px; font-weight:500; color: var(--text-color); transition: transform 0.3s; }
.file-list li:hover { transform: translateX(5px); color:#201714ff; }

/* Main Content */
.main-content { padding:2rem; }
textarea, select {
  background-color:#FFF3E0; border:1px solid #FFB066; border-radius:5px; padding:0.5rem 1rem;
  transition: all 0.3s; width:100%;
}
textarea:focus, select:focus { border-color:#FF5722; box-shadow:0 0 8px rgba(255,87,34,0.3); outline:none; }

button {
  background-color: var(--button-bg); color: var(--button-text);
  border-radius:50px; padding:0.6rem 2rem; font-weight:600;
  transition: all 0.3s;
}
button:hover { background-color:red; transform:translateY(-2px); box-shadow:0 5px 10px rgba(116, 16, 16, 0.2); }

.doc-box {
  margin-top: 2rem;
  background: #FFF2E0;
  padding: 1.5rem;
  border-radius: 12px;
  color: var(--text-color);
  box-shadow: 0 10px 20px rgba(0,0,0,0.08);
  white-space: pre-wrap;
}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
  <a class="navbar-brand font-weight-bold" href="#">iFAST Smart Docs <small>Documentation & Summaries</small></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarText">
    <ul class="navbar-nav ml-auto align-items-center">
      <li class="nav-item"><a class="nav-link" href="https://yourprojecthomepage.com" target="_blank">Project Homepage</a></li>
      <li class="nav-item dropdown">
        <button class="btn btn-outline-light dropdown-toggle" type="button" data-toggle="dropdown">English</button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="#">French</a>
          <a class="dropdown-item" href="#">Bulgarian</a>
        </div>
      </li>
    </ul>
  </div>
</nav>

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

        <form action="autogen.php" method="POST">
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

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
