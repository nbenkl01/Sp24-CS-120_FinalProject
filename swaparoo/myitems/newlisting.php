<?php
session_start();
include '../functions.php';
shared_header('Add New Book Listing');
?>
<link rel="stylesheet" href="../styles/newitems.css">

<div class='container'>
    <h1>Add New Book Listing</h1>
    <input type='text' id='bookName' placeholder="Enter the book's name" value="Harry Potter">
    <button onclick='searchBooks()'>Search</button>
    <button onclick='manualInput()'>Manual Input</button>
    <div id='searchResults'></div>
</div>

<script src="newlisting.js"></script>

<?php
shared_footer();
?>
