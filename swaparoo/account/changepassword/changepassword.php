<?php session_start(); include '../../functions.php'; ?>
<?php $pdo = connect_mysql(); date_default_timezone_set('America/New_York'); ?>

<?php 
if ($_POST['newpassword'] != $_POST['retypepassword']) {
    echo '<script>alert("Entered passwords do not match!"); window.location.href = "index.php"; </script>';
    exit;
}

if (strlen($_POST['newpassword']) > 20 || strlen($_POST['newpassword']) < 2) {
    echo '<script>alert("Password must be between 2 and 20 characters long!"); window.location.href = "index.php";</script>';
    exit;
}

if ($stmt = $pdo->prepare('UPDATE Users SET password = ? WHERE user_id = ?;')) {
    $password = password_hash($_POST['newpassword'], PASSWORD_DEFAULT);
    $stmt->bindParam(1, $password);
    $stmt->bindParam(2, $_SESSION['user_id']);
    $status = $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($status) {
        echo '<script>alert("Password successfully changed!"); window.location.href = "/swaparoo/signin/logout.php";</script>';
    } else {
        // Query failed for some reason
        echo '<script>alert("Could not update password, please try again later!"); window.location.href = "index.php";</script>';
    }    
}
?>

