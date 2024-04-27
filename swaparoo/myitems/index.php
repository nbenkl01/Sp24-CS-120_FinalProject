<?php session_start(); include '../functions.php'; include 'myitemsfunctions.php';shared_header('My Items')?>
<?php $pdo = connect_mysql(); date_default_timezone_set('America/New_York'); ?>
<?php require_login('/swaparoo/myitems/'); ?>

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
                <th class="entryheadercell">Value</th>
                <th class="entryheadercell"></th>
            </tr>
        <?php listed_items_rows() ?>
        </table>
    </div>
    <form method="post" action="newlisting.php">
        <input type="submit" value="Add New Book" class="addnewbook">
    </form>
    <h2 class="ownedh2" >Owned Items</h2>
    <div class="owneditems">
        <table class="itemstable">
            <tr class="entryheaderrow">
                <th class="entryheadercell"></th>
                <th class="entryheadercell">Title</th>
                <th class="entryheadercell">Condition</th>
                <th class="entryheadercell">Value</th>
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