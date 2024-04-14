<?php session_start(); include '../functions.php'; include 'accountfunctions.php';shared_header('Account')?>
<?php $pdo = connect_mysql(); date_default_timezone_set('America/New_York'); ?>
<?php if ($_SESSION['loggedin'] == FALSE) {
    header("Location: /swaparoo/signin/");
}
?>
<!-- Headers: Profile -->
<!-- Show Credit Balance -->
<!-- Show username, email, password (as stars), Account Settings(link)-->

<!-- Account Settings -->

<!-- My Items -->
<!-- item id (link to item page), item name, date acquired, time owned -->

<!-- My Swap Story -->
<!-- My Sold Items -->
<!-- Show item, desciription, Transction date, Status, # Credits, Buyer (user_id) -->

<!-- My Bought Items -->
<!-- item id (link to item page), item name, Transction date, Status, # Credits, Seller (user_id) -->



<div class = "account">
    <h1>Profile</h1>
    <?php profile_info() ?>
    <h2>My Swap Story</h2>
</div>
</div>

<?php shared_footer() ?>