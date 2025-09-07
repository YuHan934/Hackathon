<?php
// index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>iFAST Smart Docs</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
  <script src="https://kit.fontawesome.com/1b5fdf4bb2.js" crossorigin="anonymous"></script>
</head>
<body>

<?php include 'navbar.php'; ?>
<!-- Boba circles -->
<div class="boba-bg" id="bobaBg">
  <div class="boba-circle" style="width:80px;height:80px;top:20%;left:15%;"></div>
  <div class="boba-circle" style="width:50px;height:50px;top:60%;left:70%;"></div>
  <div class="boba-circle" style="width:100px;height:100px;top:40%;left:50%;"></div>
  <div class="boba-circle" style="width:60px;height:60px;top:75%;left:30%;"></div>
  <div class="boba-circle" style="width:90px;height:90px;top:10%;left:80%;"></div>
</div>

<section class="hero mx-auto">
  <h1>Fix the Docs</h1>
  <p>Smarter, Faster, Maintainable Documentation for the Real World</p>
  <form class="search-form" action="/">
    <div class="input-group search justify-content-center">
      <input type="text" name="q" placeholder="Search your docs or ask AI...">
      <button><i class="fa fa-search"></i></button>
    </div>
  </form>
</section>

<div class="container my-5 features-container">
  <div class="feature-card-wrapper" id="featureWrapper">
    <a href="auto-generate_documentation.php" class="feature-card">
      <i class="fas fa-code feature-icon"></i>
      <h3 class="feature-title">Auto-generate Documentation</h3>
      <p class="feature-desc">Paste your code or upload files to quickly create clean, professional documentation with AI assistance.</p>
    </a>
    <a href="summarize.php" class="feature-card">
      <i class="fas fa-file-alt feature-icon"></i>
      <h3 class="feature-title">Smart Summaries</h3>
      <p class="feature-desc">Get concise, clear summaries of your documentation to speed up reading and onboarding.</p>
    </a>
  </div>
  <div class="carousel-dots" id="carouselDots"></div>
</div>

<div class="container my-5">
  <div class="row">
    <div class="col-md-6">
      <h2>FAQs</h2>
      <ul class="homepage-listing">
        <li><a href="#">How do I sign up?</a></li>
        <li><a href="#">How can I get a new password?</a></li>
        <li><a href="#">What is the proper use of the product?</a></li>
      </ul>
    </div>
    <div class="col-md-6">
      <h2>Trending Articles</h2>
      <ul class="homepage-listing">
        <li><a href="#">Getting started with our product</a></li>
        <li><a href="#">Making great use of this thing</a></li>
        <li><a href="#">Use-cases of the knowledge base template</a></li>
      </ul>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>
