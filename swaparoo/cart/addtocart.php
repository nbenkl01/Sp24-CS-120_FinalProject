
<?php session_start(); include '../functions.php'; ?>
<?php $pdo = connect_mysql(); date_default_timezone_set('America/New_York'); ?>
<?php if ($_SESSION['loggedin'] == FALSE) {
    header("Location: /swaparoo/signin/");
    exit();
}
?>

<?php
$stmt = $pdo->prepare('INSERT IGNORE INTO Cart (user_id, item_id) VALUES (?, ?);');
$stmt->bindParam(1, $_SESSION['user_id']);
$stmt->bindParam(2, $_POST['item_id']);
$status = $stmt->execute();
if (!$status) {
    echo '<script>alert("Could not update cart, please try again later!"); window.location.href = "index.php";</script>';
}
// If item is already in cart, no rows will be affected
if ($stmt->rowCount() != 0) {
    $_SESSION['num_cart_items'] += 1;
    $_SESSION['wigglecart'] = TRUE;
}
header("Location: " . $_SERVER['HTTP_REFERER']);
?>

