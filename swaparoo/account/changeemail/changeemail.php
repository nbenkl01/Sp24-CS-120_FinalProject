<?php session_start(); include '../../functions.php'; ?>
<?php $pdo = connect_mysql(); date_default_timezone_set('America/New_York'); ?>

<?php 
if (strtolower($_POST['newemail']) != strtolower($_POST['retypeemail'])) {
    echo '<script>alert("Entered emails do not match!"); window.location.href = "index.php";</script>';
    exit;
}

if (!filter_var($_POST['newemail'], FILTER_VALIDATE_EMAIL)) {
    echo '<script>alert("Email is not valid!"); window.location.href = "index.php";</script>';
    exit;
}

if ($stmt = $pdo->prepare('UPDATE Users SET email = ? WHERE user_id = ?;')) {
    $stmt->bindParam(1, strtolower($_POST['newemail']));
    $stmt->bindParam(2, $_SESSION['user_id']);
    $status = $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($status) {
        echo '<script>alert("Email successfully changed!"); window.location.href = "/swaparoo/account/";</script>';
    } else {
        // Query failed for some reason
        echo '<script>alert("Could not update email, please try again later!"); window.location.href = "index.php";</script>';
    }    
}
?>