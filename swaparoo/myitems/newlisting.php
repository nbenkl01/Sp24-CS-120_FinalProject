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

<!-- Manual Input Modal Structure -->
<div id='manualInputModal' class='modal'>
    <div class='modal-content'>
        <span class='close' onclick='closeModal()'>&times;</span>
        <h2>Manual Book Entry</h2>
        <div id="error-message" style="display: none; color: red; margin-bottom: 10px;"></div>
        <form id="manualEntryForm">
            <input type='text' id='manualName' name='name' placeholder="Book Title" value="Harry Potter and the Cursed Child" required>
            <textarea id='manualDescription' name='description' placeholder="Description" value="As an overworked employee of the Ministry of Magic, a husband, and a father, Harry Potter struggles with a past that refuses to stay where it belongs while his youngest son, Albus, finds the weight of the family legacy difficult to bear." ></textarea>
            <input type='text' id='manualAuthor' name='author' placeholder="Author" value="J. K. Rowling" required>
            <input type='text' id='manualISBN' name='isbn' placeholder="ISBN" value="0751565369" required>
            <select id='manualCondition' name='condition' required>
                <option value=''>Select Condition</option>
                <option value='New'>New</option>
                <option value='Used'>Used</option>
            </select>
            <input type='number' id='manualCreditValue' name='credit_value' placeholder="Credit Value" min='1' required>
            <label for='manualCoverImage'>Upload Book Cover (Max 70KB, .webp format):</label>
            <input type='file' id='manualCoverImage' name='coverImage' accept=".webp" onchange="checkFile()">
            <button type='button' id="manualSubmitButton" onclick='submitManualEntry()'>Add the book!</button>
            <button type='button' id='cancelManualInput' class='cancel-button' onclick='closeModal()'>Cancel</button>
        </form>
    </div>
</div>


<script src="newlisting.js"></script>

<?php
shared_footer();
?>
