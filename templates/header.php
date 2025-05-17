<?php
/* templates/header.php */
if (!isset($page_title)) { $page_title = 'Fitness Site'; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($page_title) ?></title>

  <!-- Local Bootstrap -->
  <link href="/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Your extra styling (optional) -->
  <link href="/assets/css/custom.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="/">Fitness</a>

    <!-- Mobile hamburger -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div id="mainNav" class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="/pages/plans/list.php">Plans</a></li>
        <li class="nav-item"><a class="nav-link" href="/pages/classes/list.php">Classes</a></li>
        <li class="nav-item"><a class="nav-link" href="/pages/progress/list.php">Progress</a></li>
        <li class="nav-item"><a class="nav-link" href="/pages/posts/list.php">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="/pages/testimonials/list.php">Stories</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4"><!-- page-specific content starts here -->
