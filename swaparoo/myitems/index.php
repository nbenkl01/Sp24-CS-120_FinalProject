<?php session_start(); include '../functions.php'; include 'myitemsfunctions.php';shared_header('My Items')?>
<?php $pdo = connect_mysql(); date_default_timezone_set('America/New_York'); ?>
<?php if ($_SESSION['loggedin'] == FALSE) {
    header("Location: /swaparoo/signin/");
}
?>
<?php

?>

<!-- Lists items that are owned vs. listed - either as one or two tables -->
<!-- Listed items have button to remove from listing -->
<!-- Owned Items have button to list the item-->

<!-- Listed Items: Image, Title/author, Condition, Status(Available for Swap/Sale Pending), Price, Remove Listing Button -->
<!-- Owned Items: Image, Title/author, Condition (with edit button?), Credit Value, Add to listing  -->

<div class = "myitems">
<h1>My Items</h1>
<h2 class="listedh2">Listed Items</h2>
    <div class="listeditems">
        <table class="itemstable">
            <tr class="entryheaderrow">
                <th class="entryheadercell"></th>
                <th class="entryheadercell">Title</th>
                <th class="entryheadercell">Condition</th>
                <th class="entryheadercell">Status</th>
                <th class="entryheadercell">Credit Value</th>
                <th class="entryheadercell"></th>
            </tr>
        <?php listed_items_rows() ?>
        </table>
    </div>
    <h2 class="ownedh2" >Owned Items</h2>
    <div class="owneditems">
        <table class="itemstable">
            <tr class="entryheaderrow">
                <th class="entryheadercell"></th>
                <th class="entryheadercell">Title</th>
                <th class="entryheadercell">Condition</th>
                <th class="entryheadercell">Credit Value</th>
                <th class="entryheadercell"></th>
            </tr>
        <?php owned_items_rows() ?>
        </table>
    </div>
<h2></h2>
<h2></h2>
</div>
</div>

<?php shared_footer() ?>