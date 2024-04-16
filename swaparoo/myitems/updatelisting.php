<?php session_start(); include '../functions.php'; ?>
<?php $pdo = connect_mysql(); date_default_timezone_set('America/New_York'); ?>

<?php 
if ($_POST['action'] == 'unlist') {
    $new_availability = 0;
} else {
    $new_availability = 1;
}
if ($stmt = $pdo->prepare('UPDATE Items SET available = ? WHERE item_id = ?;')) {
    $password = password_hash($_POST['newpassword'], PASSWORD_DEFAULT);
    $stmt->bindParam(1, $new_availability);
    $stmt->bindParam(2, $_POST['item_id']);
    $status = $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$status) {
        echo '<script>alert("Could not upadte listing, please try again later!"); window.location.href = "index.php";</script>';
    }
}
header("Location: /swaparoo/myitems/");
?>

