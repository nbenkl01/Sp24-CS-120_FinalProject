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

// Select items ordered by the date added
// $stmt = $pdo->prepare('SELECT * FROM Items LIMIT ?,?');
$stmt = $pdo->prepare("SELECT * FROM Items ORDER BY $sort_by $order LIMIT ?,?");
// $stmt = $pdo->prepare('SELECT * FROM Items');
$stmt->bindValue(1, ($current_page - 1) * $num_items_on_each_page, PDO::PARAM_INT);
$stmt->bindValue(2, $num_items_on_each_page, PDO::PARAM_INT);
$stmt->execute();
// Fetch the items from the database
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Get the total number of items
$total_items = $pdo->query('SELECT COUNT(*) FROM Items')->fetchColumn();
?>

<div class="items content-wrapper">
    <h1>The Swap Shop</h1>
    <div class="sort-wrapper">
        <div class="total-items">
            <?=$total_items?> items
        </div>
        <div class="sort-options">
            <label for="sort_by">Sort:</label>
            <select id="sort_by" name="sort_by">
                <option value="title" <?= $sort_by === 'title' ? 'selected' : '' ?>>Title</option>
                <option value="credit_value" <?= $sort_by === 'credit_value' ? 'selected' : '' ?>>Credit Value</option>
            </select>
            <label for="order">Order:</label>
            <select id="order" name="order">
                <option value="asc" <?= $order === 'asc' ? 'selected' : '' ?>>Low to High</option>
                <option value="desc" <?= $order === 'desc' ? 'selected' : '' ?>>High to Low</option>
            </select>
            <button onclick="sortItems()">Sort</button>
        </div>
    </div>
    <div class="items-wrapper">
        <?php foreach ($items as $item): ?>
            <div class="item">
                <a href="/swaparoo/items/items/?item=<?=$item['item_id']?>">
                    <img src="../../images/items/<?=$item['item_id']?>.webp" width="200" height="200" alt="<?=$item['title']?>">
                    <div class="item-info">
                        <?php
                            // Check if title width exceeds container width
                            $nameClass = (strlen($item['title'])) > 22 ? 'name marquee' : 'name';
                        ?>
                        <span class="<?=$nameClass?>"><?=$item['title']?></span>
                        <div class=creditcost>
                            <i class="fas fa-coins"></i>
                            <?=$item['credit_value']?>
                        </div>    
                    </div>    
                </a>
                <form class="add-to-cart-form">
                    <input type="hidden" name="item_id" value="<?=$item['item_id']?>">
                    <input type="button" style = "float: right;" class="add-to-cart-btn" value="Put in Pouch">
                </form>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="buttons">
        <a href="/swaparoo/cart/" style="float: left;">Go to Pouch</a>
        <?php if ($current_page > 1): ?>
        <a href="/swaparoo/items/?p=<?=$current_page-1?>&sort_by=<?=$sort_by?>&order=<?=$order?>">Prev</a>
        <?php endif; ?>
        <?php if ($total_items > ($current_page * $num_items_on_each_page) - $num_items_on_each_page + count($items)): ?>
        <a href="/swaparoo/items/?p=<?=$current_page+1?>&sort_by=<?=$sort_by?>&order=<?=$order?>">Next</a>
        <?php endif; ?>
    </div>
</div>

<?=shared_footer()?>

<script>
    function sortItems() {
        const sortBy = document.getElementById('sort_by').value;
        const order = document.getElementById('order').value;
        window.location.href = `/swaparoo/items/?p=<?=$current_page?>&sort_by=${sortBy}&order=${order}`;
    }

    document.addEventListener("DOMContentLoaded", function() {
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

    // document.addEventListener("DOMContentLoaded", function() {
    // const items = document.querySelectorAll('.items-wrapper .item');
    // items.forEach(item => {
    //     const name = item.querySelector('.name');
        
    //     // Check if title width exceeds container width
    //     if (name.offsetWidth > item.offsetWidth) {
    //         name.classList.add('marquee'); // Apply animation
    //     }
    // });
    // });
</script>