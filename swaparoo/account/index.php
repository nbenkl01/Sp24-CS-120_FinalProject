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
    <h2 class="soldh2">My Sold Items</h2>
    <div class="solditems">
        <table class="transacteditemstable">
            <tr class="transactionheaderrow">
                <th class="transactionheadercell"></th>
                <th class="transactionheadercell">Title</th>
                <th class="transactionheadercell">Date Sold</th>
                <th class="transactionheadercell">Status</th>
                <th class="transactionheadercell">Price</th>
                <th class="transactionheadercell">Buyer</th>
            </tr>
        <?php transactions_items_rows('sold') ?>
        </table>
    </div>
    <h2 class="boughth2" >My Bought Items</h2>
    <div class="boughtitems">
        <table class="transacteditemstable">
            <tr class="transactionheaderrow">
                <th class="transactionheadercell"></th>
                <th class="transactionheadercell">Title</th>
                <th class="transactionheadercell">Date Bought</th>
                <th class="transactionheadercell">Status</th>
                <th class="transactionheadercell">Price</th>
                <th class="transactionheadercell">Seller</th>
            </tr>
        <?php transactions_items_rows('bought') ?>
        </table>
    </div>
</div>
</div>

<?php shared_footer() ?>