<?php session_start(); include '../../functions.php'; include '/swaparoo/account/changepassword.php';shared_header('Change Password')?>
<?php $pdo = connect_mysql(); date_default_timezone_set('America/New_York'); ?>
<?php if ($_SESSION['loggedin'] == FALSE) {
    header("Location: /swaparoo/signin/");
}
?>

<div class = "changepassword">
    <h1>Change Password</h1>
    <form action="changepassword.php" method="post" class="changeform">
        <input type="password" name="newpassword" placeholder="New Password" id="newpassword" required>
        <input type="password" name="retypepassword" placeholder="Retype New Password" id="retype" required>
        <input type="submit" value="Submit" class="formsubmit">
    </form>
</div>

<?php shared_footer() ?>