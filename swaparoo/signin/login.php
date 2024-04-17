<?php session_start(); include '../functions.php'; ?>
<?php $pdo = connect_mysql(); date_default_timezone_set('America/New_York'); ?>

<script>
function showAlert(message) {
    alert(message);
    window.location.href = "index.php";
}
</script>

<?php 
if ( !isset($_POST['username'], $_POST['password']) ) {
    echo '<script>showAlert("Please fill both the username and password fields!");</script>';
    exit;
}

if ($stmt = $pdo->prepare('SELECT user_id, password, credits_balance FROM Users WHERE username = ?')) {
    $stmt->bindParam(1, $_POST['username']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        if (password_verify($_POST['password'], $result['password'])) {
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['credits_balance'] = $result['credits_balance'];
            // echo '<script>alert("Welcome back, ' . htmlspecialchars($_SESSION['name'], ENT_QUOTES) . '!"); window.location.href = "../";</script>';
            echo '<script>window.location.href = "../";</script>';
        } else {
            // Incorrect password
            echo '<script>alert("Incorrect username and/or password!"); window.location.href = "index.php";</script>';
        }
    } else {
        // Incorrect username
        echo '<script>alert("Incorrect username and/or password!"); window.location.href = "index.php";</script>';
    }    

    // $stmt->close();
}
?>