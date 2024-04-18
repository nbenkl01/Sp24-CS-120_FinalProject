<?php session_start(); include '../../functions.php'; ?>
<?php $pdo = connect_mysql(); date_default_timezone_set('America/New_York'); ?>

<?php 
// Check if the data was submitted.
if (!isset($_POST['username'], $_POST['password'], $_POST['email'])) {
    echo '"Please complete the registration form!"';
    exit;
}

// Make sure submitted registration values are not empty.
if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
    echo '"Please complete the registration form!"';
    exit;
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    echo '"Email is not valid!"';
    exit;
}

if (preg_match('/^[a-zA-Z0-9]+$/', $_POST['username']) == 0) {
    echo '"Username is not valid!"';
    exit;
}

if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 2) {
    echo '"Password must be between 2 and 20 characters long!"';
    exit;
}

if ($stmt = $pdo->prepare('SELECT user_id, password FROM Users WHERE username = ?')) {
    $stmt->bindParam(1, $_POST['username']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        echo '"Username exists, please choose another!"';
    } else {
        if ($stmt = $pdo->prepare('INSERT INTO Users (username, password, email) VALUES (?, ?, ?)')) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt->bindParam(1, $_POST['username']);
            $stmt->bindParam(2, $password);
            $stmt->bindParam(3, $_POST['email']);
            $stmt->execute();
            echo '"You have successfully registered! You can now login!"';
        } else {
            echo '"Could not prepare statement!"';
        }
    }
} else {
    echo '"Could not prepare statement!"';
}
?>