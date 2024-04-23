<?php session_start(); include '../functions.php'; shared_header('Sign In')?>

<?php 
if (isset($_SESSION['loggedin'])) {
    if (isset($_SESSION['previous_page'])) {
        echo "<script>window.location.href = '" . $_SESSION['previous_page'] . "';</script>";
        unset($_SESSION['previous_page']);
    } else {
        echo "<script>window.location.href = '/swaparoo/';</script>";
    }
}
?>

<div class="login">
    <h1>Login</h1>
    <!-- <form action="login.php" method="post"> -->
    <form method="post" action="">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Event listener for comment form submission
    document.querySelectorAll('.login form').forEach(function(form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            const formData = new FormData(this);
            fetch('login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data); // Display success message or handle response
                window.location.reload();
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>