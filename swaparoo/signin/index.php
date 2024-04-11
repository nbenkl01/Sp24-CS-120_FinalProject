<?php session_start(); include '../functions.php'; shared_header('Sign In')?>

<div class="login">
    <h1>Login</h1>
    <form action="login.php" method="post">
        <label for="username">
            <i class="fas fa-user"></i>
        </label>
        <input type="text" name="username" placeholder="Username" id="username" required>
        <label for="password">
            <i class="fas fa-lock"></i>
        </label>
        <input type="password" name="password" placeholder="Password" id="password" required>
        <input type="submit" value="Login">
    </form>
    <button onclick="window.location.href = 'register/';">Register</button>
</div>

<?php shared_footer() ?>