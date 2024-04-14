<?php session_start(); include '../../functions.php'; ?>
<?php $pdo = connect_mysql(); date_default_timezone_set('America/New_York'); ?>

<script>
function showAlert(message) {
    alert(message);
    window.location.href = "index.php";
}
</script>

<?php 
if (strtolower($_POST['newemail']) != strtolower($_POST['retypeemail'])) {
    echo '<script>alert("Entered emails do not match!"); </script>';
    exit;
}

if (!filter_var($_POST['newemail'], FILTER_VALIDATE_EMAIL)) {
    echo '<script>alert("Email is not valid!");';
    exit;
}

if ($stmt = $pdo->prepare('UPDATE Users SET email = ? WHERE user_id = ?;')) {
    $stmt->bindParam(1, strtolower($_POST['newemail']));
    $stmt->bindParam(2, $_SESSION['user_id']);
    $status = $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($status) {
        echo '<script>alert("Email successfully changed!"); window.location.href = "/swaparoo/account/account";</script>';
    } else {
        // Could not find user id
        echo '<script>alert("Could not update email, please try again later!"); window.location.href = "index.php";</script>';
    }    
}
?>