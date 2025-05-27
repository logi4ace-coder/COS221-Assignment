<?php session_start()?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/view.css">
    <link href="https://fonts.googleapis.com/css2?family=Barlow&display=swap" rel="stylesheet">
</head>
<body>
        <?php include "header.php"; ?>
<div id="product-container"></div>
              <img src="/Assignment/images/loading.gif" alt="Loading..." class="loader-img" />

<form id="review-form">



    
    <label>Rating:</label>
<div id="star-rating" style="display: flex; gap: 5px; cursor: pointer;"></div>
<input type="hidden" id="rating" required><br>

    <label>Review Text:</label>
    <textarea id="review_text" required></textarea><br>

    <button type="submit">Submit Review</button>
</form>

<div id="review-container" class="p-4"></div>

<div id="review-container" class="p-4"></div>
<script src="/Assignment/scripts/view.js"></script>
</body>
</html>