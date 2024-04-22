<?php session_start(); include '../functions.php'; shared_header('Items')?>
<?php $pdo = connect_mysql(); date_default_timezone_set('America/New_York'); ?>

<?php
// The number of items to show on each page
$num_items_on_each_page = 4;
// The current page
$current_page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;

$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'item_id';
$valid_columns = ['title', 'credit_value', 'item_condition','author', 'publisher', 'availability']; // Define valid sorting columns
$order = isset($_GET['order']) && in_array($_GET['order'], ['asc', 'desc']) ? $_GET['order'] : 'asc';
if (!in_array($sort_by, $valid_columns)) {
    $sort_by = 'item_id'; // Default to date_added if invalid column provided
}

// Execute a query to retrieve the authors
$authorStmt = $pdo->query('SELECT DISTINCT author FROM Items ORDER BY author');
// Fetch all authors as an associative array
$authors = $authorStmt->fetchAll(PDO::FETCH_ASSOC);

// Encode authors' data as JSON
$authorsJson = json_encode($authors);

// Execute queries to retrieve the options for other filtering sections
$conditionStmt = $pdo->query('SELECT DISTINCT item_condition FROM Items ORDER BY item_condition');
$conditions = $conditionStmt->fetchAll(PDO::FETCH_ASSOC);

$availabilityStmt = $pdo->query('SELECT DISTINCT available FROM Items ORDER BY available DESC');
$availabilities = $availabilityStmt->fetchAll(PDO::FETCH_ASSOC);

$conditionFilters = isset($_GET['conditionFilter']) ? explode(',', $_GET['conditionFilter']) : [];
$authorFilters = isset($_GET['authorFilter']) ? explode(',', $_GET['authorFilter']) : [];
$availabilityFilters = isset($_GET['availabilityFilter']) ? explode(',', $_GET['availabilityFilter']) : [];
$minCreditValue = isset($_GET['minCreditValueFilter']) ? $_GET['minCreditValueFilter'] : null;
$maxCreditValue = isset($_GET['maxCreditValueFilter']) ? $_GET['maxCreditValueFilter'] : null;

// // Check if filters are set, if not, use default values
// Build the WHERE clause for the SQL query based on selected filters
$whereClause = '';
$params = [];
if (!empty($conditionFilters) && $conditionFilters[0] !== '') {
    $whereClause .= 'item_condition IN (' . rtrim(str_repeat('?,', count($conditionFilters)), ',') . ') AND ';
    $params = array_merge($params, $conditionFilters);
}
if (!empty($authorFilters) && $authorFilters[0] !== '') {
    $whereClause .= 'author IN (' . rtrim(str_repeat('?,', count($authorFilters)), ',') . ') AND ';
    $params = array_merge($params, $authorFilters);
}
if (!empty($availabilityFilters) && $availabilityFilters[0] !== '') {
    $whereClause .= 'available IN (' . rtrim(str_repeat('?,', count($availabilityFilters)), ',') . ') AND ';
    $params = array_merge($params, $availabilityFilters);
}
if ($minCreditValue !== null && $minCreditValue !== '') {
    $whereClause .= 'credit_value >= ? AND ';
    $params[] = $minCreditValue;
}
if ($maxCreditValue !== null && $maxCreditValue !== '') {
    $whereClause .= 'credit_value <= ? AND ';
    $params[] = $maxCreditValue;
}

// Remove trailing 'AND' from the WHERE clause
$whereClause = rtrim($whereClause, 'AND ');

// Prepare and execute the SQL query with selected filters
$sql = "SELECT * FROM Items";
$countsql ="SELECT COUNT(*) FROM Items";
if (!empty($whereClause)) {
    $sql .= " WHERE $whereClause";
    $countsql .= " WHERE $whereClause";
}

// Append sorting to the SQL query
$sql .= " ORDER BY $sort_by $order LIMIT ?,?";

// Prepare and execute the SQL query with selected filters
$stmt = $pdo->prepare($sql);

// Bind parameters
for ($i = 0; $i < count($params); $i++) {
    $stmt->bindValue($i + 1, $params[$i]);
}

// Bind LIMIT parameters
$stmt->bindValue(count($params) + 1, ($current_page - 1) * $num_items_on_each_page, PDO::PARAM_INT);
$stmt->bindValue(count($params) + 2, $num_items_on_each_page, PDO::PARAM_INT);

// Execute the statement
$stmt->execute();

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare and execute the SQL query with selected filters
$stmt = $pdo->prepare($countsql);

// Bind parameters
for ($i = 0; $i < count($params); $i++) {
    $stmt->bindValue($i + 1, $params[$i]);
}

// Execute the statement
$stmt->execute();

$total_items = $stmt->fetchColumn();
// echo '<pre>'; print_r($params); echo '</pre>';

if (isset($_SERVER['QUERY_STRING'])) {
    $urlParams = $_SERVER['QUERY_STRING'];
    $urlParamParts = explode("&", $urlParams);

    // echo json_encode($urlParamParts);
    // Filter out array slices that don't start with "?", "sort_by", or "order"
    $filteredParts = array_filter($urlParamParts, function($part) {
        return !preg_match('/^(?:\?|p=|sort_by=|order=)/', $part);
    });

    // echo json_encode($filteredParts);
    // Re-index the filtered array
    $filteredParts = array_values($filteredParts);

    // Add "&" at the beginning only if filteredParts is not empty
    if (count($filteredParts) > 0) {
        // Re-index the filtered array
        $filteredParts = array_values($filteredParts);

        // Imploding the filtered array
        $urlParamParts = "&" . implode("&", $filteredParts);
    } else {
        $urlParamParts = '';
    }
    // echo $urlParamParts;
} else {
    $urlParamParts = '';
}
?>


<div class="items content-wrapper">
    <h1>The Swap Shop</h1>
    
    <div class="sort-wrapper">
        <div class="total-items">
            <?=$total_items?> items
        </div>
        <div class="sort-options">
            <select id="sort_order" name="sort_order">
                <option value="title_asc" <?= $sort_by === 'title' && $order === 'asc' ? 'selected' : '' ?>>Sort by: Title (A-Z)</option>
                <option value="title_desc" <?= $sort_by === 'title' && $order === 'desc' ? 'selected' : '' ?>>Sort by: Title (Z-A)</option>
                <option value="credit_value_asc" <?= $sort_by === 'credit_value' && $order === 'asc' ? 'selected' : '' ?>>Sort by: Cost (Low to High)</option>
                <option value="credit_value_desc" <?= $sort_by === 'credit_value' && $order === 'desc' ? 'selected' : '' ?>>Sort by: Cost (High to Low)</option>
            </select>
        </div>
    </div>

    <div class="data-filtering">
        <!-- <h2>Filter</h2> -->
        <form id="filterForm">
            <div class = "scrollable">
                <div class="filterSection" id="conditionFilter">
                    <label>Condition:</label>
                    <ul class="checkbox-list">
                        <?php foreach ($conditions as $condition): ?>
                            <li>
                                <input type="checkbox" id="<?= $condition['item_condition'] ?>Filter" name="conditionFilter" value="<?= $condition['item_condition'] ?>">
                                <label for="<?= $condition['item_condition'] ?>Filter"><?= $condition['item_condition'] ?></label>
                            </li>
                        <?php endforeach; ?>
                        <!-- <li>
                            <input type="checkbox" id="selectAllCondition">
                            <label for="selectAllCondition">Select All</label>
                        </li> -->
                    </ul>
                </div>
                <div class="filterSection">
                    <label for="authorFilter">Author:</label>
                    <select id="authorFilter" name="authorFilter[]" class="author-select">
                        <option value="" style="color: grey;">Filter by Author(s)</option>
                        <?php foreach ($authors as $author): ?>
                            <option value="<?= $author['author'] ?>"><?= $author['author'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div id="selectedAuthors" class="selectedAuthors"></div> 
                </div>

                <div class="filterSection" id = "availabilityFilter">
                <label>Availability:</label>
                    <ul class="checkbox-list">
                        <?php foreach ($availabilities as $availability): ?>
                            <li>
                                <input type="checkbox" id="<?= $availability['available'] ?>Filter" name="availabilityFilter" value="<?= $availability['available'] ?>">
                                <label for="<?= $availability['available'] ?>Filter"><?= $availability['available'] ? 'Available' : 'Unavailable' ?></label>
                            </li>
                        <?php endforeach; ?>
                        <!-- <li>
                            <input type="checkbox" id="selectAllAvailability">
                            <label for="selectAllAvailability">Select All</label>
                        </li> -->
                    </ul>
                </div>
                <div class="filterSection last" id="creditFilter">
                    <label for="creditRangeFilter">Credit Value:</label></br>
                    <input type="number" id="minCreditValueFilter" name="minCreditValueFilter" placeholder="Min" value = null style="width: 60px;">
                    <span style="margin: 0 5px;">to</span>
                    <input type="number" id="maxCreditValueFilter" name="maxCreditValueFilter" placeholder="Max" value = null style="width: 60px;">
                </div>
            </div>
            <!-- <div class = "buttons"> -->
            <button type="submit">Apply</button>
            <button type="reset">Clear</button>
            <!-- </div> -->
        </form>
    </div>

    <div class="items-wrapper">
        <?php foreach ($items as $item): ?>
            <div class="item">
                <a href="/swaparoo/items/items/?item=<?=$item['item_id']?>">
                    <img src="../../images/items/<?=$item['item_id']?>.webp" width="200" height="200" alt="<?=$item['title']?>">
                    <div class="item-info">
                        <?php
                            // Check if title width exceeds container width
                            $nameClass = (strlen($item['title'])) > 20 ? 'name marquee' : 'name';
                        ?>
                        <span class="<?=$nameClass?>"><?=$item['title']?></span>
                        <div class=creditcost>
                            <i class="fas fa-coins"></i>
                            <?=$item['credit_value']?>
                        </div>    
                    </div>    
                </a>
                <form class="add-to-cart-form" action="/swaparoo/cart/addtocart.php" method="post">
                    <input type="hidden" name="item_id" value="<?=$item['item_id']?>">
                    <input type="submit" style = "float: right;" class="add-to-cart-btn" value="Put in Pouch">
                </form>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="buttons">
        <div>
            <!-- <a href="/swaparoo/cart/">Go to Pouch</a> -->
        </div>
        <div>
            <!-- <script type="text/javascript">
                const urlParams = new URLSearchParams(window.location.search);
                let parts = String(urlParams).split("&");
                let secondOnward = parts.slice(2).join("&");
            </script> -->
            <?php if ($current_page > 1): ?>
                <a href="/swaparoo/items/?p=<?=$current_page-1?>&sort_by=<?=$sort_by?>&order=<?=$order?><?=$urlParamParts?>">Prev</a>
            <?php else: ?>
                <a class="disabled">Prev</a>
            <?php endif; ?>
            <select id="pageSelect">
                <?php for ($i = 1; $i <= ceil($total_items / $num_items_on_each_page); $i++): ?>
                    <option value="<?=$i?>" <?= $i === $current_page ? 'selected' : '' ?>><?=$i?></option>
                <?php endfor; ?>
            </select>
            / <?= ceil($total_items / $num_items_on_each_page) ?>
            <?php if ($total_items > ($current_page * $num_items_on_each_page) - $num_items_on_each_page + count($items)): ?>
                <a href="/swaparoo/items/?p=<?=$current_page+1?>&sort_by=<?=$sort_by?>&order=<?=$order?><?=$urlParamParts?>">Next</a>
            <?php else: ?>
                <a class="disabled">Next</a>
            <?php endif; ?>
        </div>

    </div>
</div>

<?=shared_footer()?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const authors = <?= $authorsJson ?>;

        // Event listeners for sorting
        setupSortListeners();

        // Event listener for adding items to cart
        setupAddToCartListeners();

        // Event listener for collapsing filter sections
        setupFilterSectionListeners();

        // Event listener for selecting authors
        setupAuthorSelectionListeners();

        // Update form items based on URL parameters
        updateFormItemsFromUrl();

        // Event listener for submitting the filter form
        setupFilterFormSubmitListener();

        // Event listener for clearing the filter form
        setupClearButtonListener();
    });

    // Function to set up event listeners for sorting
    function setupSortListeners() {
        const sortSelect = document.getElementById('sort_order');
        sortSelect.addEventListener('change', function() {
            const sortOrder = this.value;
            const { sort_by, order } = parseSortOrder(sortOrder);
            updateUrlWithSortOrder(sort_by, order);
        });

        const pageSelect = document.getElementById('pageSelect');
        pageSelect.addEventListener('change', function() {
            const sortOrder = sortSelect.value;
            const selectedPage = this.value;
            const { sort_by, order } = parseSortOrder(sortOrder);
            updateUrlWithSortOrder(sort_by, order, selectedPage);
        });
    }

    // Function to parse sort order
    function parseSortOrder(sortOrder = '') {
        const parts = sortOrder.split('_');
        const order = parts.pop(); // Remove the last part (order)
        const sort_by = parts.join('_'); // Join the remaining parts to get sort_by
        return { sort_by, order };
    }

    // Function to update URL with sort order
    function updateUrlWithSortOrder(sort_by, order, page = null) {
        const url = `/swaparoo/items/?p=${page || <?=$current_page?>}&sort_by=${sort_by}&order=${order}<?=$urlParamParts?>`;
        window.location.href = url;
    }

    // Function to set up event listeners for adding items to cart
    function setupAddToCartListeners() {
        const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const form = button.closest('.add-to-cart-form');
                const formData = new FormData(form);
                addToCart(formData);
            });
        });
    }

    // Function to add item to cart
    function addToCart(formData) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/swaparoo/cart/', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                // Cart updated successfully
                location.reload();
            } else {
                // Handle errors
                alert('Failed to add item to cart.');
            }
        };
        xhr.send(formData);
    }

    // Function to set up event listeners for collapsing filter sections
    function setupFilterSectionListeners() {
        const filterSections = document.querySelectorAll('.filterSection');
        filterSections.forEach(section => {
            const label = section.querySelector('label');
            label.addEventListener('click', function() {
                section.classList.toggle('collapsed');
            });
        });
    }

    // Function to set up event listeners for selecting authors
    function setupAuthorSelectionListeners() {
        const selectedAuthorsContainer = document.getElementById('selectedAuthors');
        const authorFilter = document.getElementById('authorFilter');
        authorFilter.addEventListener('change', function() {
            selectAuthor(this, selectedAuthorsContainer);
        });
    }

    // Function to handle author selection
    function selectAuthor(selectElement, container) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        if (selectedOption.value !== '') {
            moveSelectedAuthorToContainer(selectedOption, container, selectElement);
            sortSelectOptions(selectElement);
            selectElement.selectedIndex = 0;
        }
    }

    // Function to move selected author to container
    function moveSelectedAuthorToContainer(option, container, selectElement) {
        console.log(option.textContent)
        if (option.value !== '') {
            const selectedAuthorItem = document.createElement('li');
            selectedAuthorItem.textContent = option.textContent;
            container.appendChild(selectedAuthorItem);
            selectedAuthorItem.addEventListener('click', function() {
                container.removeChild(selectedAuthorItem);
                option.selected = false;
                selectElement.appendChild(option);
                sortSelectOptions(option.parentElement);
            });
            option.remove();
        }
    }

    // Function to update form items based on URL parameters
    function updateFormItemsFromUrl() {
        const selectedAuthorsContainer = document.getElementById('selectedAuthors');
        const authorFilter = document.getElementById('authorFilter');
        const urlParams = parseUrlParams();

        // Update condition filter checkboxes
        const conditionFilters = urlParams['conditionFilter'] || [];
        conditionFilters.forEach(filter => {
            const checkbox = document.getElementById(`${filter}Filter`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });

        // Update author filter select options
        const authorFilters = urlParams['authorFilter'] || [];
        authorFilters.forEach(filter => {
            const option = document.querySelector(`select[name="authorFilter[]"] option[value="${filter}"]`);
            if (option) {
                moveSelectedAuthorToContainer(option, selectedAuthorsContainer, authorFilters);
            }
        });

        // Update availability filter checkboxes
        const availabilityFilters = urlParams['availabilityFilter'] || [];
        availabilityFilters.forEach(filter => {
            const checkbox = document.getElementById(`${filter}Filter`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });

        // Update min and max credit value inputs
        const minCreditValue = urlParams['minCreditValueFilter'] ? urlParams['minCreditValueFilter'][0] : '';
        const maxCreditValue = urlParams['maxCreditValueFilter'] ? urlParams['maxCreditValueFilter'][0] : '';
        document.getElementById('minCreditValueFilter').value = minCreditValue;
        document.getElementById('maxCreditValueFilter').value = maxCreditValue;
    }

    // Function to parse URL parameters
    function parseUrlParams() {
        const urlParams = new URLSearchParams(window.location.search);
        const params = {};
        for (const [key, value] of urlParams) {
            params[key] = value.split(',');
        }
        return params;
    }

    // Function to set up event listener for submitting the filter form
    function setupFilterFormSubmitListener() {
        const filterForm = document.getElementById('filterForm');
        filterForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent form submission
            const sortSelect = document.getElementById('sort_order');
            const sortOrder = sortSelect.value;
            const { sort_by, order } = parseSortOrder(sortOrder);
            const conditionFilters = Array.from(document.querySelectorAll('input[name="conditionFilter"]:checked')).map(input => input.value);
            const availabilityFilters = Array.from(document.querySelectorAll('input[name="availabilityFilter"]:checked')).map(input => input.value);
            const minCreditValue = document.getElementById('minCreditValueFilter').value;
            const maxCreditValue = document.getElementById('maxCreditValueFilter').value;
            const selectedAuthors = Array.from(document.getElementById('selectedAuthors').querySelectorAll('li')).map(li => li.textContent);
            const url = `/swaparoo/items/?sort_by=${sort_by}&order=${order}&conditionFilter=${conditionFilters.join(',')}&authorFilter=${selectedAuthors.join(',')}&availabilityFilter=${availabilityFilters.join(',')}&minCreditValueFilter=${minCreditValue}&maxCreditValueFilter=${maxCreditValue}`;
            window.location.href = url;
        });
    }

    // Function to set up event listener for clearing the filter form
    function setupClearButtonListener() {
        const clearButton = document.querySelector('button[type="reset"]');
        clearButton.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent the form from being cleared by default
            clearForm();
        });
    }

    // Function to clear the filter form
    function clearForm() {
        // Clear condition checkboxes
        document.querySelectorAll('input[name="conditionFilter"]:checked').forEach(checkbox => {
            checkbox.checked = false;
        });

        // Clear author select and reset selected authors
        document.getElementById('selectedAuthors').innerHTML = '';
        const authorFilter = document.getElementById('authorFilter');
        authorFilter.querySelectorAll('option').forEach(option => {
            if (option.value !== '') {
                authorFilter.appendChild(option);
            }
        });

        // Clear availability checkboxes
        document.querySelectorAll('input[name="availabilityFilter"]:checked').forEach(checkbox => {
            checkbox.checked = false;
        });

        // Clear credit value inputs
        document.getElementById('minCreditValueFilter').value = '';
        document.getElementById('maxCreditValueFilter').value = '';
    }

    // Function to sort select options
    function sortSelectOptions(selectElement) {
        const options = Array.from(selectElement.options);

        // Move "Filter by Author(s)" to the beginning
        options.sort((a, b) => {
            if (a.value === '') {
                return -1; // "Filter by Author(s)" comes first
            } else if (b.value === '') {
                return 1;
            } else {
                return a.text.localeCompare(b.text);
            }
        });

        selectElement.innerHTML = '';
        options.forEach(option => selectElement.add(option));
    }

</script>

<!-- <script>
    const authors = <?= $authorsJson ?>;
    document.addEventListener('DOMContentLoaded', function() {
        const sortSelect = document.getElementById('sort_order');
        sortSelect.addEventListener('change', function() {
            const sortOrder = this.value;
            const parts = sortOrder.split('_');
            const order = parts.pop(); // Remove the last part (order)
            const sort_by = parts.join('_'); // Join the remaining parts to get sort_by
            window.location.href = `/swaparoo/items/?p=<?=$current_page?>&sort_by=${sort_by}&order=${order}<?=$urlParamParts?>`;
        });

        const pageSelect = document.getElementById('pageSelect');
        pageSelect.addEventListener('change', function() {
            const selectedPage = this.value;
            window.location.href = `/swaparoo/items/?p=${selectedPage}&sort_by=<?=$sort_by?>&order=<?=$order?><?=$urlParamParts?>`;
        });

        const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const form = button.closest('.add-to-cart-form');
                const formData = new FormData(form);
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/swaparoo/cart/', true);
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        // Cart updated successfully
                        location.reload();
                    } else {
                        // Handle errors
                        alert('Failed to add item to cart.');
                    }
                };
                xhr.send(formData);
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Function to toggle the selection of all checkboxes in a category
        function toggleSelectAll(checkboxId, category) {
            const selectAllCheckbox = document.getElementById(checkboxId);
            const checkboxes = document.querySelectorAll(`input[name^=${category}]`);
            selectAllCheckbox.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
        }

        // Toggle select all for Condition checkboxes
        toggleSelectAll('selectAllCondition', 'conditionFilter');
        
        // Toggle select all for Availability checkboxes
        toggleSelectAll('selectAllAvailability', 'availabilityFilter');
    });

    document.addEventListener('DOMContentLoaded', function() {
        const filterSections = document.querySelectorAll('.filterSection');

        filterSections.forEach(section => {
            const label = section.querySelector('label');
            label.addEventListener('click', function() {
                const div = this.parentElement;
                div.classList.toggle('collapsed');
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const selectedAuthorsContainer = document.getElementById('selectedAuthors');
        const authorFilter = document.getElementById('authorFilter');
        
        authorFilter.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value !== '') {
                // Remove selected author from options list
                selectedOption.remove();
                // Create a list item to display selected author
                const selectedAuthorItem = document.createElement('li');
                selectedAuthorItem.textContent = selectedOption.textContent;
                // Append selected author item to the container
                selectedAuthorsContainer.appendChild(selectedAuthorItem);
                
                // Sort authorFilter options
                sortSelectOptions(authorFilter);
                
                // Reselect the default option
                authorFilter.selectedIndex = 0;
                
                // Add click event listener to remove the selected author when clicked
                selectedAuthorItem.addEventListener('click', function() {
                    // Remove the selected author item
                    selectedAuthorItem.remove();
                    // Add the removed option back to the options list
                    authorFilter.appendChild(selectedOption);
                    
                    // Sort authorFilter options again
                    sortSelectOptions(authorFilter);
                    
                    // Reselect the default option
                    authorFilter.selectedIndex = 0;
                });
            }
        });
        
        // Function to sort select options
        function sortSelectOptions(selectElement) {
            const options = Array.from(selectElement.options);
            
            // Move "Filter by Author(s)" to the beginning
            options.sort((a, b) => {
                if (a.value === '') {
                    return -1; // "Filter by Author(s)" comes first
                } else if (b.value === '') {
                    return 1;
                } else {
                    return a.text.localeCompare(b.text);
                }
            });
            
            selectElement.innerHTML = '';
            options.forEach(option => selectElement.add(option));
        }
    });


    document.addEventListener('DOMContentLoaded', function() {
        const selectedAuthorsContainer = document.getElementById('selectedAuthors');
        const authorFilter = document.getElementById('authorFilter');
        
        // Function to parse URL parameters
        function parseUrlParams() {
            const urlParams = new URLSearchParams(window.location.search);
            const params = {};
            for (const [key, value] of urlParams) {
                params[key] = value.split(',');
            }
            return params;
        }

        // Function to update form items based on URL parameters
        function updateFormItems() {
            const urlParams = parseUrlParams();

            // Update condition filter checkboxes
            const conditionFilters = urlParams['conditionFilter'] || [];
            conditionFilters.forEach(filter => {
                const checkbox = document.getElementById(`${filter}Filter`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });

            // Update author filter select options
            const authorFilters = urlParams['authorFilter'] || [];
            authorFilters.forEach(filter => {
                const option = document.querySelector(`select[name="authorFilter[]"] option[value="${filter}"]`);
                if (option) {
                    option.selected = true;
                    
                    // Create a list item to display selected author
                    const selectedAuthorItem = document.createElement('li');
                    selectedAuthorItem.textContent = option.textContent;
                    // Append selected author item to the container
                    selectedAuthorsContainer.appendChild(selectedAuthorItem);

                    // Sort authorFilter options
                    sortSelectOptions(authorFilter);
                    
                    // Reselect the default option
                    authorFilter.selectedIndex = 0;
                    
                    // Add click event listener to remove the selected author when clicked
                    selectedAuthorItem.addEventListener('click', function() {
                        // Remove the selected author item
                        selectedAuthorItem.remove();
                        
                        // Add the removed option back to the options list
                        authorFilter.appendChild(option);

                        // Reselect the default option
                        option.selected = false;
                        // Sort authorFilter options again
                        sortSelectOptions(authorFilter);
                    });
                    // Remove the selected option from the author filter
                    option.remove();
                }

                // authorFilter.selectedIndex = 0;
            });

            // Update availability filter checkboxes
            const availabilityFilters = urlParams['availabilityFilter'] || [];
            availabilityFilters.forEach(filter => {
                const checkbox = document.getElementById(`${filter}Filter`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });

            // Update min and max credit value inputs
            const minCreditValue = urlParams['minCreditValueFilter'] ? urlParams['minCreditValueFilter'][0] : '';
            const maxCreditValue = urlParams['maxCreditValueFilter'] ? urlParams['maxCreditValueFilter'][0] : '';
            document.getElementById('minCreditValueFilter').value = minCreditValue;
            document.getElementById('maxCreditValueFilter').value = maxCreditValue;
        }

        // Function to sort select options
        function sortSelectOptions(selectElement) {
            const options = Array.from(selectElement.options);
            
            // Move "Filter by Author(s)" to the beginning
            options.sort((a, b) => {
                if (a.value === '') {
                    return -1; // "Filter by Author(s)" comes first
                } else if (b.value === '') {
                    return 1;
                } else {
                    return a.text.localeCompare(b.text);
                }
            });
            
            selectElement.innerHTML = '';
            options.forEach(option => selectElement.add(option));
        }

        // Update form items on page load if form already submitted
        updateFormItems();
    });

    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('filterForm');
        const authorFilter = document.getElementById('authorFilter');
        const selectedAuthorsContainer = document.getElementById('selectedAuthors');
        
        filterForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent form submission
            
            // Gather selected filter values
            const conditionFilters = Array.from(document.querySelectorAll('input[name="conditionFilter"]:checked')).map(input => input.value);
            const availabilityFilters = Array.from(document.querySelectorAll('input[name="availabilityFilter"]:checked')).map(input => input.value);
            const minCreditValue = document.getElementById('minCreditValueFilter').value;
            const maxCreditValue = document.getElementById('maxCreditValueFilter').value;
            
            // Construct authorFilters from the selectedAuthors div
            const selectedAuthors = Array.from(selectedAuthorsContainer.querySelectorAll('li')).map(li => li.textContent);
            
            // Construct URL with filter values
            const url = `/swaparoo/items/?sort_by=<?=$sort_by?>&order=<?=$order?>&conditionFilter=${conditionFilters.join(',')}&authorFilter=${selectedAuthors.join(',')}&availabilityFilter=${availabilityFilters.join(',')}&minCreditValueFilter=${minCreditValue}&maxCreditValueFilter=${maxCreditValue}`;
            
            // Redirect to the constructed URL
            window.location.href = url;
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const selectedAuthorsContainer = document.getElementById('selectedAuthors');
        const authorFilter = document.getElementById('authorFilter');

        function clearForm() {
            // Clear condition checkboxes
            document.querySelectorAll('input[name="conditionFilter"]:checked').forEach(checkbox => {
                checkbox.checked = false;
            });

            // Clear author select and reset selected authors
            selectedAuthorsContainer.innerHTML = '';
            authorFilter.querySelectorAll('option').forEach(option => {
                if (option.value !== '') {
                    authorFilter.appendChild(option);
                }
            });

            // Clear availability checkboxes
            document.querySelectorAll('input[name="availabilityFilter"]:checked').forEach(checkbox => {
                checkbox.checked = false;
            });

            // Clear credit value inputs
            document.getElementById('minCreditValueFilter').value = '';
            document.getElementById('maxCreditValueFilter').value = '';

            // Add cleared authors back to authorFilter list if not already present
            authors.forEach(author => {
                // Check if author already exists in authorFilter list
                const existingOption = authorFilter.querySelector(`option[value="${author['author']}"]`);
                if (!existingOption) {
                    const option = document.createElement('option');
                    option.value = author['author'];
                    option.textContent = author['author'];
                    authorFilter.appendChild(option);
                }
            });

            // Sort the author filter after adding authors
            sortSelectOptions(authorFilter);
        }

        // Add event listener to the clear button
        const clearButton = document.querySelector('button[type="reset"]');
        clearButton.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent the form from being cleared by default
            clearForm(); // Call the function to clear the form
        });

        // Function to sort select options
        function sortSelectOptions(selectElement) {
            const options = Array.from(selectElement.options);

            // Move "Filter by Author(s)" to the beginning
            options.sort((a, b) => {
                if (a.value === '') {
                    return -1; // "Filter by Author(s)" comes first
                } else if (b.value === '') {
                    return 1;
                } else {
                    return a.text.localeCompare(b.text);
                }
            });

            selectElement.innerHTML = '';
            options.forEach(option => selectElement.add(option));
        }
    });
</script> -->