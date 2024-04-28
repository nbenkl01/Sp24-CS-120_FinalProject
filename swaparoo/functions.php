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
    
    // Connect to MySQL database
    $pdo = connect_mysql();

    // Set default time zone.
    date_default_timezone_set('America/New_York');
    add_to_cart_alerts();
    if (!isset($_SESSION['loggedin'])) {
        nonuser_header($title);
    }
    else{
        user_header($title);
    }
}

function user_header($title) {
    ?>
    <!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1">
        <title><?= $title ?></title>
        <link rel="icon" href="/images/transparent_logo.png">
        <link href="/swaparoo/styles/style.css" rel="stylesheet" type="text/css">
        <link href="/swaparoo/styles/registration.css" rel="stylesheet" type="text/css">
        <link href="/swaparoo/styles/account.css" rel="stylesheet" type="text/css">
        <link href="/swaparoo/styles/myitems.css" rel="stylesheet" type="text/css">
        <link href="/swaparoo/styles/items.css" rel="stylesheet" type="text/css">
        <link href="/swaparoo/styles/cart.css" rel="stylesheet" type="text/css">
        <link href="/swaparoo/styles/contact.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
    </head>
    <?php if (isset($_SESSION['wigglecart'])) { ?>
    <script>     
    window.onload = function() {
        cartElement = document.getElementById("carticon");
        cartElement.classList.add("wiggle");

        // Remove wiggle class after animation completes
        // Must match length of css animation
        setTimeout(function() {
            cartElement.classList.remove("wiggle");
        }, 1000);
    }
    </script>
    <?php unset($_SESSION['wigglecart']); }?>
    <body>
        <header class = "userheader">
            <div class = small_device_name>
                <a href="/swaparoo/">
                    Swap-a-Roo
                </a>
            </div>
            <div class="content-wrapper">
                <h1>
                    <a href="/swaparoo/">
                        Swap-a-Roo
                    </a>
                </h1>
                <nav>
                    <a href="/swaparoo/">Home</a>
                    <a href="/swaparoo/items/">The Swap Shop</a>
                    <a href="/swaparoo/myitems/">My Items</a>
                </nav>
                <div class="link-icons">
                    <a href="/swaparoo/account/" class="accountlink">
                        <div><?=$_SESSION['name']  ?></div>
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <a href="/swaparoo/signin/logout.php"><i class="fas fa-sign-out-alt"></i></a>
                    <a href="/swaparoo/search/">
                        <i class="fas fa-search"></i>
                    </a>
                    <a href="/swaparoo/cart/" id="carticon">
                        <i class="fas fa-shopping-cart"></i>
                        <span><?=$_SESSION['num_cart_items']?></span>
                    </a>
                    <div class="headerbalance">
                        <i class="fas fa-coins"></i>
                        <div>
                            <?=number_format($_SESSION['credits_balance'])?>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <main>
<?php
}

function nonuser_header($title) {
    ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1">
        <title><?= $title ?></title>
        <link rel="icon" href="/images/transparent_logo.png">
        <link href="/swaparoo/styles/style.css" rel="stylesheet" type="text/css">
        <link href="/swaparoo/styles/registration.css" rel="stylesheet" type="text/css">
        <link href="/swaparoo/styles/account.css" rel="stylesheet" type="text/css">
        <link href="/swaparoo/styles/items.css" rel="stylesheet" type="text/css">
        <link href="/swaparoo/styles/cart.css" rel="stylesheet" type="text/css">
        <link href="/swaparoo/styles/contact.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
    </head>
    <body>
        <header class = "nonuserheader">
            <div class = small_device_name>
                <a href="/swaparoo/">
                    Swap-a-Roo
                </a>
            </div>
            <div class="content-wrapper">
                <h1>
                    <a href="/swaparoo/">
                        Swap-a-Roo
                    </a>
                </h1>
                <nav>
                    <a href="/swaparoo/items/">The Swap Shop</a>
                    <span><a href="/swaparoo/signin/">Login</a>/
                    <a href="/swaparoo/signin/register/">Register</a></span>
                </nav>
                <div class="link-icons">
                    <a href="/swaparoo/search/">
                        <i class="fas fa-search"></i>
                    </a>
                    <a href="/swaparoo/signin/">
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <a href="/swaparoo/cart/">
                        <i class="fas fa-shopping-cart"></i>
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
                <p>&copy; <?= $year ?>, Eccentric Emporiums Inc.
                <a href="/swaparoo/contact/"><i class="fas fa-address-book"></i> Contact Us</a>
                </p>
            </div>
        </footer>
    </body>
</html>
<?php
}

function saveThumbnail($url, $itemId, $directory) {
    $savePath = $directory . $itemId . ".webp";

    $imageData = file_get_contents($url);
    if ($imageData === false) {
        return ["error" => "Failed to download image from URL"];
    }

    $image = imagecreatefromstring($imageData);
    if ($image === false) {
        return ["error" => "Failed to create image from data"];
    }

    if (!imagewebp($image, $savePath)) {
        imagedestroy($image);
        return ["error" => "Failed to convert image to WebP"];
    }

    imagedestroy($image);
    return ["success" => "Image successfully converted to WebP", "path" => $savePath];
}

// Redirect to a chosen page after logging in
function require_login($previous_page) {
    if ($_SESSION['loggedin'] == FALSE) {
        $_SESSION['previous_page'] = $previous_page;
        header("Location: /swaparoo/signin/");
        exit();
    }
}

function add_to_cart_alerts() {
    if(isset($_SESSION['item_already_owned_alert'])) {
        echo '<script>alert("You already own that item!");</script>';
        unset($_SESSION['item_already_owned_alert']);
    } else if (isset($_SESSION['item_unavailable_alert'])) {
        echo '<script>alert("This item is not available for swap.");</script>';
        unset($_SESSION['item_unavailable_alert']);
    }
}
?>
