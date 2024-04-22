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
    messageBox.style.bottom = '20px';
    messageBox.style.left = '20px';
    messageBox.style.backgroundColor = 'lightgrey';
    messageBox.style.padding = '10px';
    messageBox.style.borderRadius = '5px';
    document.body.appendChild(messageBox);

    // message will disappear after 5 seconds
    setTimeout(() => {
        document.body.removeChild(messageBox);
    }, 5000);
}
