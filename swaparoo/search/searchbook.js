let fetchedResults = [];
const resultsContainer = document.getElementById('search-results');

document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();

        fetch(`searchbook.php?${params}`, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            fetchedResults = data;
            displayResults(fetchedResults);
        })
        .catch(error => console.error('Error:', error));
    });

    document.getElementById('filter-results').addEventListener('click', function() {
        const selectedCondition = document.querySelector('input[name="condition"]:checked').value;
        filterResults(selectedCondition);
    });
});

function displayResults(data) {
    resultsContainer.innerHTML = '';

    if (data.error) {
        resultsContainer.innerHTML = `<p class="error">${data.error}</p>`;
    } else if (data.length === 0) {
        resultsContainer.innerHTML = '<p>No results found.</p>';
    } else {
        data.forEach(book => {
            const bookElement = document.createElement('div');
            bookElement.className = 'book-result';
            bookElement.innerHTML = `
                <a href="../items/items/?item=${book.item_id}" class="book-link">
                    <img src="../../images/items/${book.item_id}.webp" alt="${book.title}" onerror="this.onerror=null;this.src='../images/default-cover.webp';">
                    <div class="book-info">
                        <h3>${book.title}</h3>
                        <p>Author: ${book.author}</p>
                        <p>ISBN: ${book.isbn}</p>
                        <p>Condition: ${book.item_condition}</p>
                        <p>Lowest Price: ${book.lowest_price} Credits</p>
                        <p>Seller: ${book.owner_username}</p>
                    </div>
                </a>
            `;
            resultsContainer.appendChild(bookElement);
        });
    }
}


function filterResults(condition) {
    if (condition === 'all') {
        displayResults(fetchedResults);
        return;
    }

    const filteredResults = fetchedResults.filter(book => book.item_condition.toLowerCase() === condition);
    displayResults(filteredResults);
}
