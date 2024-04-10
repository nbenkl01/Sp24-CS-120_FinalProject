<?php
function connect_mysql() {
    // MySQL database details
    $DATABASE_HOST = 'noamb.sgedu.site';
    $DATABASE_USER = 'utbw5koa0qclk';
    $DATABASE_PASS = '@16@f1crsy^2';
    $DATABASE_NAME = 'dbnggjboof0sqv';
    try {
        return new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
    } catch (PDOException $exception) {
        // If there is an error with the connection, stop the script and display the error.
        exit('Failed to connect to database!');
    }
}

// Standard header
function shared_header($title) {
    $num_items_in_cart = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
    ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?= $title ?></title>
        <link href="style.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
    </head>
    <body>
        <header>
            <div class="content-wrapper">
                <h1>
                    <a href="index.php">
                        <i class="fas fa-tents"></i>
                        Swap-a-Roo
                        <i class="fas fa-person-circle-question"></i>
                    </a>
                </h1>
                <nav>
                    <a href="index.php">Home</a>
                    <a href="index.php?page=items">The Swap Shop</a>
                    <a href="index.php?page=register">Register</a>
                </nav>
                <div class="link-icons">
                    <a href="index.php?page=search">
                        <i class="fas fa-search"></i>
                    </a>
                    <a href="index.php?page=account">
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <a href="index.php?page=cart">
                        <i class="fas fa-shopping-cart"></i>
                        <span><?= $num_items_in_cart ?></span>
                    </a>
                </div>
            </div>
        </header>
        <main>
<?php
}

// Standard footer
function shared_footer() {
    $year = date('Y');
    ?>
        </main>
        <footer>
            <div class="content-wrapper">
                <p>&copy; <?= $year ?>, Eccentric Emporiums Inc.</p>
            </div>
        </footer>
    </body>
</html>
<?php
}
?>