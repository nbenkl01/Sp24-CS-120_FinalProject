<?php session_start(); include 'functions.php'; shared_header('Home') ?>
<?php $pdo = connect_mysql(); date_default_timezone_set('America/New_York'); ?>

<?php 
$stmt = $pdo->prepare('SELECT * FROM Items ORDER BY item_id DESC LIMIT 4');
$stmt->execute();
$recently_added_items = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $pdo->prepare('
    SELECT i.*, COUNT(t.item_id) AS transaction_count 
    FROM Items i 
    LEFT JOIN Transactions t ON i.item_id = t.item_id 
    GROUP BY i.item_id 
    ORDER BY transaction_count DESC 
    LIMIT 4
');
$stmt->execute();
$popular_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT * FROM Items ORDER BY credit_value ASC LIMIT 4');
$stmt->execute();
$frugal_finds = $stmt->fetchAll(PDO::FETCH_ASSOC);

function displayItems($items) {
    $html = '';
    foreach ($items as $item) {
        $nameClass = (strlen($item['title'])) > 20 ? 'name marquee' : 'name';
        $html .= '
            <div class="item">
                <a href="/swaparoo/items/items/?item=' . $item['item_id'] . '">
                    <img src="../../images/items/' . $item['item_id'] . '.webp" width="200" height="200" alt="' . $item['title'] . '">
                    <div class="item-info">
                        <span class="' . $nameClass . '">' . $item['title'] . '</span>
                        <div class="creditcost">
                            <i class="fas fa-coins"></i>' . $item['credit_value'] . '
                        </div>
                    </div>
                </a>
            </div>';
    }
    return $html;
}
?>

<div class="featured">
    <img src="../../images/logo.webp" alt="Swap-a-Roo Logo">
    <h2>Swap-a-Roo</h2>
    <p>Where Treasures Hop from Pocket to Pocket!</p>
</div>
<div class="items content-wrapper">
    <div class="navigation-links">
        <a href="#recently-added" class="internal-nav-link active">Recently Added Items</a>
        <a href="#storied-swaps" class="internal-nav-link">Storied Swaps</a>
        <a href="#frugal-finds" class="internal-nav-link">Frugal Finds</a> 
    </div>
    
    <div class="content-section" id="recently-added">
        <div class="items-wrapper">
            <?= displayItems($recently_added_items) ?>
        </div>
    </div>

    <div class="content-section" id="storied-swaps" style="display: none;">
        <div class="items-wrapper">
            <?= displayItems($popular_items) ?>
        </div>
    </div>

    <div class="content-section" id="frugal-finds" style="display: none;">
        <div class="items-wrapper">
            <?= displayItems($frugal_finds) ?>
        </div>
    </div>
</div>

<?php shared_footer() ?>

<script>
    // JavaScript to handle navigation link clicks
    document.addEventListener('DOMContentLoaded', function() {
    // Get all navigation links
    var navLinks = document.querySelectorAll('.internal-nav-link');
    var lastActiveTab = document.querySelector('.internal-nav-link.active');

    // Add mouseover event listener to each navigation link
    navLinks.forEach(function(navLink) {
        navLink.addEventListener('mouseover', function(event) {
            // Remove 'active' class from all navigation links
            navLinks.forEach(function(link) {
                link.classList.remove('active');
            });
        });
    });

    // Add mouseout event listener to revert to last active tab
    navLinks.forEach(function(navLink) {
        navLink.addEventListener('mouseout', function(event) {
            if (lastActiveTab) {
                lastActiveTab.classList.add('active');
            }
        });
    });

    // Add click event listener to each navigation link
    navLinks.forEach(function(navLink) {
        navLink.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default link behavior
            
            // Remove 'active' class from all navigation links
            navLinks.forEach(function(link) {
                link.classList.remove('active');
            });
            
            // Add 'active' class to the clicked navigation link
            this.classList.add('active');
            
            // Store reference to the last active tab
            lastActiveTab = this;

            // Get the target section ID from the href attribute
            var targetId = this.getAttribute('href');
            
            // Hide all content sections
            var contentSections = document.querySelectorAll('.content-section');
            contentSections.forEach(function(section) {
                section.style.display = 'none';
            });
            
            // Show the target content section
            document.querySelector(targetId).style.display = 'block';
        });
    });
});
</script>