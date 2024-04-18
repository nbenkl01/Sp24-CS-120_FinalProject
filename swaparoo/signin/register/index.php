<?php session_start(); include '../../functions.php'; shared_header('Register')?>

<?php 
if (isset($_SESSION['loggedin'])) {
    echo "<script>window.location.href = '/swaparoo/';</script>";
}
?>

<div class="register">
    <h1>Register</h1>
    <form action="register.php" method="post" autocomplete="off">
        <label for="username">
            <i class="fas fa-user"></i>
        </label>
        <input type="text" name="username" placeholder="Username" id="username" required>
        <label for="password">
            <i class="fas fa-lock"></i>
        </label>
        <input type="password" name="password" placeholder="Password" id="password" required>
        <label for="email">
            <i class="fas fa-envelope"></i>
        </label>
        <input type="email" name="email" placeholder="Email" id="email" required>
        <input type="submit" value="Register">
    </form>
    <button onclick="window.location.href = '../';">Login</button>
</div>

<?php shared_footer() ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Event listener for comment form submission
    document.querySelectorAll('.register form').forEach(function(form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            const formData = new FormData(this);
            fetch('register.php', {
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