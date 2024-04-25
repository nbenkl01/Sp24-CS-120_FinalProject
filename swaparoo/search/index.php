<?php
session_start();
include '../functions.php';
shared_header('Book Search');
?>
<link rel="stylesheet" href="../styles/search.css">

<div class="container">
    <h1>Book Search</h1>
    <form id="searchForm">
        <div class="radio-group">
            <label>
                <input type="radio" name="category" value="title" checked>
                Title
            </label>
            <label>
                <input type="radio" name="category" value="author">
                Author
            </label>
            <label>
                <input type="radio" name="category" value="isbn">
                ISBN
            </label>
        </div>
        <input type="text" name="keywords" placeholder="Enter search keywords" value="1984" required>
        <input type="submit" value="Search">
    </form>
    <div id="search-results"></div>
</div>

<script src="searchbook.js"></script>

<?php
shared_footer();
?>
