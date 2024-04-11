<?php session_start(); include '../../functions.php'; ?>
<?php $pdo = connect_mysql(); date_default_timezone_set('America/New_York'); ?>

<script>
function showAlert(message, href = "index.php") {
    alert(message);
    window.location.href = href;
}
</script>

<?php 
// Check if the data was submitted.
if (!isset($_POST['username'], $_POST['password'], $_POST['email'])) {
    echo '<script>alert("Please complete the registration form!"); window.location.href = "index.php";</script>';
    exit;
}

// Make sure submitted registration values are not empty.
if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
    echo '<script>alert("Please complete the registration form!"); window.location.href = "index.php";</script>';
    exit;
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    echo '<script>alert("Email is not valid!"); window.location.href = "index.php";</script>';
    exit;
}

if (preg_match('/^[a-zA-Z0-9]+$/', $_POST['username']) == 0) {
    echo '<script>alert("Username is not valid!"); window.location.href = "index.php";</script>';
    exit;
}

if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 2) {
    echo '<script>alert("Password must be between 2 and 20 characters long!"); window.location.href = "index.php";</script>';
    exit;
}

if ($stmt = $pdo->prepare('SELECT user_id, password FROM Users WHERE username = ?')) {
    $stmt->bindParam(1, $_POST['username']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        echo '<script>alert("Username exists, please choose another!"); window.location.href = "index.php";</script>';
    } else {
        if ($stmt = $pdo->prepare('INSERT INTO Users (username, password, email) VALUES (?, ?, ?)')) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt->bindParam(1, $_POST['username']);
            $stmt->bindParam(2, $password);
            $stmt->bindParam(3, $_POST['email']);
            $stmt->execute();
            echo '<script>alert("You have successfully registered! You can now login!"); window.location.href = "../index.php";</script>';
        } else {
            echo '<script>alert("Could not prepare statement!"); window.location.href = "index.php";</script>';
        }
    }
} else {
    echo '<script>alert("Could not prepare statement!"); window.location.href = "index.php";</script>';
}
?>