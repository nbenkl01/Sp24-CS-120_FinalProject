<?php session_start(); include '../functions.php'; include 'cartfunctions.php';shared_header('Cart')?>
<?php $pdo = connect_mysql(); date_default_timezone_set('America/New_York'); ?>
<?php if ($_SESSION['loggedin'] == FALSE) {
    header("Location: /swaparoo/signin/");
}
?>

<div class = "cart">
    <h1>Cart</h1>
    <div class = cartitemcontainer>
        <div class = "cartitemwrapper">
            <div class="cartitemimage">
                <a href="/swaparoo/items/items/?item=1">    
                    <img src="../../images/items/1.webp">
                </a>
            </div>
            <div class="cartitemtext">        
                <a href="/swaparoo/items/items/?item=1">
                    <div class="cartitemtitletext">The Catcher in the Rye</div>
                    <div class="cartitemauthortext">J.D. Salinger</div>
                </a>
                <div class = "cartitemsellertext">Seller: nbenkel</div>
            </div>
            <div class="cartitemcreditcost">
                <i class="fas fa-coins"></i>
                <div>50</div>
            </div>
            <div class="removefromcartbutton">
                <form action="removefromcart.php" method="post">
                    <input type="submit" value="Remove From Cart" class="removefromcartsubmit">
                    <input type="hidden" value="1" name="item_id">
                </form>
            </div>
        </div>    
    </div>
    
</div>
</div>

<?php shared_footer() ?>