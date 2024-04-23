document.getElementById('bookName').addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        event.preventDefault(); // Prevent default form submission on Enter
        searchBooks();
    }
});

// object to store books data indexed by unique ID
let booksData = {}; 

function searchBooks() {
    const bookName = document.getElementById('bookName').value;
    const resultsContainer = document.getElementById('searchResults');
    // clear previous results
    resultsContainer.innerHTML = ''; 

    fetch(`../search/googlebooksearch.php?q=${encodeURIComponent(bookName)}&n=12`)
        .then(response => response.json())
        .then(data => {
            // reset the books data before new data is added
            booksData = {}; 
            data.books.forEach((book, index) => {
                // unique id for each book displayed
                const bookID = 'book-' + index; 
                booksData[bookID] = book;

                const bookDiv = document.createElement('div');
                bookDiv.className = 'book-result';
                bookDiv.innerHTML = `
                    <img src="${book.smallThumbnail}" alt="${book.title} thumbnail">
                    <h3>${book.title}</h3>
                    <p>Author: ${book.authors.join(', ')}</p>
                    <button id="${bookID}">Select</button>
                `;
                resultsContainer.appendChild(bookDiv);

                // attach event listener to the button within the bookDiv
                document.getElementById(bookID).addEventListener('click', () => {
                    selectBook(bookID);
                });
            });
        })
        .catch(error => console.error('Error fetching data:', error));
}

function selectBook(bookID) {
    const book = booksData[bookID];
    showBookDetailsModal(book, bookID);
}

function showBookDetailsModal(book, bookID) {
    console.log('Book details:', book);
    const overlay = document.createElement('div');
    overlay.id = 'overlay';
    overlay.innerHTML = `
        <div class="popup">
            <h2>${book.title}</h2>
            <p>Author: ${book.authors.join(', ')}</p>
            <p>Enter additional information for your book listing:</p>
            <label for="condition-${bookID}">Condition:</label>
            <select id="condition-${bookID}" name="condition">
                <option value="">Select Condition</option>
                <option value="New">New</option>
                <option value="Used">Used</option>
            </select>
            <label for="credit_value-${bookID}">Credit Value:</label>
            <input type="number" id="credit_value-${bookID}" name="credit_value" min="1">
            <button type="button" onclick="addBookToListing('${bookID}')">Add to my list</button>
            <button type="button" onclick="closeOverlay()">Cancel</button>
        </div>
    `;
    document.body.appendChild(overlay);
}

function addBookToListing(bookID) {
    const book = booksData[bookID];
    const condition = document.getElementById(`condition-${bookID}`).value;
    const creditValue = document.getElementById(`credit_value-${bookID}`).value;

    fetch('../myitems/addbook.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `name=${encodeURIComponent(book.title)}&description=${encodeURIComponent(book.description || '')}&category=Books&condition=${encodeURIComponent(condition)}&credit_value=${encodeURIComponent(creditValue)}&title=${encodeURIComponent(book.title)}&author=${encodeURIComponent(book.authors.join(', '))}&isbn=${encodeURIComponent(book.ISBN || '')}&thumbnail=${encodeURIComponent(book.smallThumbnail)}`
    })
    .then(response => response.json())
    .then(data => {
        console.log('Server Response:', data); // print the server response, including item_id and thumbnailUrl
        showPopupMessage(data.success || data.error);
        closeOverlay(); // close the overlay on success or display a message
    })
    .catch(error => {
        console.error('Error adding book:', error);
        showPopupMessage('Error adding book: ' + error.message);
    });
}

function closeOverlay() {
    const overlay = document.getElementById('overlay');
    if (overlay) {
        document.body.removeChild(overlay);
    }
}

function showPopupMessage(message) {
    const messageBox = document.createElement('div');
    messageBox.textContent = message;
    messageBox.style.position = 'fixed';
    messageBox.style.top = '50%';
    messageBox.style.left = '50%';
    messageBox.style.transform = 'translate(-50%, -50%)';
    messageBox.style.backgroundColor = 'lightgrey';
    messageBox.style.padding = '10px 20px';
    messageBox.style.borderRadius = '5px';
    messageBox.style.zIndex = '1050'; // Ensure it's above other content
    messageBox.style.textAlign = 'center'; // Center-align the text
    messageBox.style.fontSize = '1.2rem'; // Optional: Enhance font size for better readability
    messageBox.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)'; // Optional: Add shadow for better visibility

    document.body.appendChild(messageBox);

    // Make the message disappear after 5 seconds
    setTimeout(() => {
        document.body.removeChild(messageBox);
    }, 5000);
}

document.addEventListener('DOMContentLoaded', function() {
    const manualButton = document.querySelector('button[onclick="manualInput()"]');
    manualButton.addEventListener('click', manualInput);
    document.getElementById('manualCoverImage').addEventListener('change', checkFile);
});

document.getElementById('manualEntryForm').addEventListener('submit', function(event) {
    if (!validateManualEntry()) {
        event.preventDefault(); // Prevent form submission if validation fails
    }
});

// Function to open manual input modal
function manualInput() {
    const manualInputModal = document.getElementById('manualInputModal');
    
    // Adding a cancel button if it does not exist
    if (!document.getElementById('cancelManualInput')) {
        const cancelButton = document.createElement('button');
        cancelButton.textContent = 'Cancel';
        cancelButton.id = 'cancelManualInput';
        cancelButton.type = 'button';
        cancelButton.onclick = closeModal; // Bind the closeModal function to the click event
        manualInputModal.appendChild(cancelButton); // Append the cancel button to the modal
    }
    
    manualInputModal.style.display = 'block';
}

// Function to validate manual entry form
function validateManualEntry() {
    let isValid = true;
    const isbn = document.getElementById('manualISBN').value; // Correct ID for ISBN
    const creditValue = document.getElementById('manualCreditValue').value; // Correct ID for Credit Value
    const bookTitle = document.getElementById('manualName').value; // Correct ID for Book Title
    const author = document.getElementById('manualAuthor').value; // Correct ID for Author
    const description = document.getElementById('manualDescription').value; // Correct ID for Description

    // Clear any previous error messages
    clearErrorMessages();

    // Check if all fields are filled
    if (!bookTitle.trim() || !description.trim() || !author.trim() || !isbn.trim() || !creditValue.trim()) {
        showError('All fields must be filled');
        isValid = false;
    }

    // Validate ISBN (if it needs to be a specific format)
    if (!/^\d{10}(\d{3})?$/.test(isbn)) { // Adjusted regex for ISBN-10 or ISBN-13
        showError('ISBN must be 10 or 13 digits with no symbols');
        isValid = false;
    }

    // Validate credit value
    if (isNaN(creditValue) || parseInt(creditValue) <= 0) {
        showError('Credit value must be greater than 0');
        isValid = false;
    }

    return isValid;
}

function showError(message) {
    const errorDiv = document.getElementById('error-message');
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'block'; // Make the error message visible
    }
}

function clearErrorMessages() {
    const errorDiv = document.getElementById('error-message');
    if (errorDiv) {
        errorDiv.style.display = 'none'; // Hide the error message
    }
}

function submitManualEntry() {
    if (!validateManualEntry()) {
        return;
    }

    const coverImageFile = document.getElementById('manualCoverImage').files[0];
    if (coverImageFile) {
        const formData = new FormData();
        formData.append('coverImage', coverImageFile);

        // upload the image first
        fetch('uploadTmpPicture.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // continue with submitting the rest of the form
                completeBookSubmission(data.url);
            } else {
                showPopupMessage(data.message);
            }
        })
        .catch(error => {
            console.error('Error uploading image:', error);
            showPopupMessage('Error uploading image: ' + error.message);
        });
    } else {
        // No image file selected, proceed without image URL
        // this need to be changed, no picture no upload
        completeBookSubmission();
    }
}

function completeBookSubmission(imageUrl = '') {
    const formData = new FormData(document.getElementById('manualEntryForm')); // This grabs all input data
    formData.append('thumbnail', imageUrl); // Adding the thumbnail

    // check if title needs to be added separately
    if (!formData.has('title')) {
        const title = document.getElementById('manualName').value; // Assuming 'manualName' is your title input
        formData.append('title', title);
    }

    // Now send this data to addbook.php
    fetch('addbook.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showPopupMessage('Book added successfully');
            closeModal();
            // resetFormFields();  // Call this function to clear all input fields
        } else {
            showPopupMessage(data.error);
        }
    })
    .catch(error => {
        console.error('Error adding book:', error);
        showPopupMessage('Error adding book manually: ' + error.message);
    });
}

// Function to close the modal
function closeModal() {
    document.getElementById('manualInputModal').style.display = 'none';
}

// Function to reset all form fields
function resetFormFields() {
    document.getElementById('manualEntryForm').reset();
}

function checkFile() {
    const fileInput = document.getElementById('manualCoverImage');
    const file = fileInput.files[0];
    
    if (!file) {
        showPopupMessage('No file selected. Please upload a book cover.');
        return false;
    }

    if (file.type !== 'image/webp' || file.size > 70 * 1024) {
        showPopupMessage('Please upload a .webp image with a size less than 70KB');
        fileInput.value = '';
        return false;
    }

    booksData['coverImage'] = file;
    return true;
}

function generateRandomString(length) {
    let result = '';
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    const charactersLength = characters.length;
    for (let i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}