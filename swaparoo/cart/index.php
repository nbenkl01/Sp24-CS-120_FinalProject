<?php session_start(); include '../functions.php'; shared_header('Cart')?>
<?php $pdo = connect_mysql(); date_default_timezone_set('America/New_York'); ?>
<?php if ($_SESSION['loggedin'] == FALSE) {
    header("Location: /swaparoo/signin/");
}
?>

<div class = "cart">
    <h1>Cart</h1>
    <?php 
    $pdo = connect_mysql();
    $user_id = $_SESSION['user_id'];

    if ($stmt = $pdo->prepare('SELECT i.item_id, i.title, i.author, i.credit_value, u.username 
    FROM Items AS i, Cart AS c, Users AS u
    WHERE i.item_id = c.item_id AND i.owner_id = u.user_id AND c.user_id = ?;')) {
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        $order_total = 0;
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $item_id = $result['item_id'];
            $title = $result['title'];
            $author = $result['author'];
            $order_total += $result['credit_value'];
            $formatted_order_total = number_format($order_total);
            $price = number_format($result['credit_value']);
            $seller = $result['username'];
            echo <<<ROW
            <div class = cartitemcontainer>
                <div class = "cartitemwrapper">
                    <div class="cartitemimage">
                        <a href="/swaparoo/items/items/?item=$item_id">    
                            <img src="../../images/items/$item_id.webp">
                        </a>
                    </div>
                    <div class="cartitemtext">        
                        <a href="/swaparoo/items/items/?item=$item_id">
                            <div class="cartitemtitletext">$title</div>
                            <div class="cartitemauthortext">$author</div>
                        </a>
                        <div class = "cartitemsellertext">Seller: $seller</div>
                    </div>
                    <div class="cartitemcreditcost">
                        <i class="fas fa-coins"></i>
                        <div>$price</div>
                    </div>
                    <div class="removefromcartbutton">
                        <form action="removefromcart.php" method="post">
                            <input type="submit" value="Remove From Cart" class="removefromcartsubmit">
                            <input type="hidden" value="$item_id" name="item_id">
                        </form>
                    </div>
                </div>    
            </div>
            ROW;
        }
        echo <<<TOTAL
        <div class = "ordertotal">Total: <i class="fas fa-coins"></i>$formatted_order_total</div>
        <form action="submitorder.php" method="post" class="submitorderform">
            <input type="submit" value="Submit My Order" class="submitorder">
            <input type="hidden" value = "$order_total" name="order_total">
        </form>
        TOTAL;

    } else {
        echo '<script>alert("Could not find user cart!"); window.location.href = "/swaparoo/";</script>';
    }
    ?>    
</div>
</div>

<?php shared_footer() ?>