<?php session_start(); include '../functions.php'; ?>
<?php $pdo = connect_mysql(); date_default_timezone_set('America/New_York'); ?>

<script>
function showAlert(message) {
    alert(message);
    window.location.href = "index.php";
}
</script>

<?php 
// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if ( !isset($_POST['username'], $_POST['password']) ) {
    echo '<script>showAlert("Please fill both the username and password fields!");</script>';
    exit;
}

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $pdo->prepare('SELECT user_id, password FROM Users WHERE username = ?')) {
    // Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
    $stmt->bindParam(1, $_POST['username']);
    $stmt->execute();
    // Store the result so we can check if the account exists in the database.
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        // Account exists, now we verify the password.
        // Note: remember to use password_hash in your registration file to store the hashed passwords.
        if (password_verify($_POST['password'], $result['password'])) {
            // Verification success! User has logged-in!
            // Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $result['id'];
            echo '<script>alert("Welcome back, ' . htmlspecialchars($_SESSION['name'], ENT_QUOTES) . '!"); window.location.href = "../";</script>';
        } else {
            // Incorrect password
            echo '<script>alert("Incorrect username and/or password!"); window.location.href = "index.php";</script>';
        }
    } else {
        // Incorrect username
        echo '<script>alert("Incorrect username and/or password!"); window.location.href = "index.php";</script>';
    }    

    $stmt->close();
}
$pdo = null; // Close connection
?>