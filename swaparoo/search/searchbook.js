document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const resultsContainer = document.getElementById('search-results');

    form.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent the default form submission

        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();

        fetch(`searchbook.php?${params}`, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            displayResults(data);
        })
        .catch(error => console.error('Error:', error));
    });

    function displayResults(data) {
        resultsContainer.innerHTML = ''; // Clear previous results
    
        if (data.error) {
            resultsContainer.innerHTML = `<p class="error">${data.error}</p>`;
        } else if (data.length === 0) {
            resultsContainer.innerHTML = '<p>No results found.</p>';
        } else {
            data.forEach(book => {
                const bookElement = document.createElement('div');
                bookElement.className = 'book-result';
                bookElement.innerHTML = `
                    <img src="../../images/items/${book.item_id}.webp" alt="${book.title}" onerror="this.onerror=null;this.src='../images/default-cover.webp';">
                    <h3>${book.title}</h3>
                    <p>Author: ${book.author}</p>
                    <p>ISBN: ${book.isbn}</p>
                    <p>Condition: ${book.item_condition}</p>
                    <p>Lowest Price: ${book.lowest_price} Credits</p>
                    <p>Seller: ${book.owner_username}</p>
                `;
                resultsContainer.appendChild(bookElement);
            });
        }
    }
    
});
