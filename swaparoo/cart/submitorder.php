<?php session_start(); include '../functions.php'; ?>
<?php $pdo = connect_mysql(); date_default_timezone_set('America/New_York'); ?>

<?php 
if ($_POST['order_total'] > $_SESSION['credits_balance']) {
    echo '<script>alert("You do not have sufficient funds to make this purchase."); window.location.href = "/swaparoo/cart/";</script>';
} else {
    // Get All Items in cart
    // $pdo1 = connect_mysql();
    $user_id = $_SESSION['user_id'];
    $stmt_get_cart = $pdo->prepare('SELECT c.item_id, i.owner_id, i.credit_value FROM Cart AS c, Items AS i WHERE i.item_id = c.item_id AND user_id = ?;');
    $stmt_get_cart->bindParam(1, $user_id);
    $stmt_get_cart->execute();
    // Need a new pdo object while we use the other 
    $pdo_each = connect_mysql();
    $results = $stmt_get_cart->fetchAll(PDO::FETCH_ASSOC);
    foreach($results as $row) {
        // Add each item to transaction history and update owner in items table
        $stmt_each = $pdo_each->prepare('INSERT INTO Transactions (transaction_id, buyer_id, seller_id, item_id, transaction_timestamp, status, price) VALUES (NULL, ?, ?, ?, NOW(), "Pending", ?); UPDATE Items SET owner_id = ? WHERE item_id = ?;');
        $stmt_each->bindParam(1, $user_id);
        $stmt_each->bindParam(2, $row['owner_id']);
        $stmt_each->bindParam(3, $row['item_id']);
        $stmt_each->bindParam(4, $row['credit_value']);
        $stmt_each->bindParam(5, $user_id);
        $stmt_each->bindParam(6, $row['item_id']);
        $status = $stmt_each->execute();
        if (!$status) {
            echo '<script>alert("Could not update transaction history, please try again later!"); window.location.href = "index.php";</script>';
        }
    }
    // Need a new connection after loop for some reason
    $pdo_finally = connect_mysql();
    $new_balance = $_SESSION['credits_balance'] - $_POST['order_total'];
    $_SESSION['credits_balance'] = $new_balance;

    // Remove items from cart, and update users credit balance
    $stmt_finally = $pdo_finally->prepare('DELETE FROM Cart WHERE user_id = ?; UPDATE Users SET credits_balance = ? WHERE user_id = ?;');
    $stmt_finally->bindParam(1, $user_id);
    $stmt_finally->bindParam(2, $new_balance);
    $stmt_finally->bindParam(3, $user_id);
    $status = $stmt_finally->execute();
    if (!$status) {
        echo '<script>alert("Could not remove items from cart, please try again later!"); window.location.href = "index.php";</script>';
    }
    $_SESSION['num_cart_items'] = 0;
    $_SESSION['wigglecart'] = TRUE;
}
header("Location: /swaparoo/cart/thankyou/?" . http_build_query(
    array(
        'order_total' => $_POST['order_total']
    )));
?>

