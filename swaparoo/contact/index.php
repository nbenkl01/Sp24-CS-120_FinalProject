<?php session_start(); include '../functions.php'; shared_header('My Items')?>
<?php $pdo = connect_mysql(); date_default_timezone_set('America/New_York'); ?>

<?php
$responses = [];
if (isset($_POST['email'], $_POST['subject'], $_POST['name'], $_POST['msg'])) {
	if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$responses[] = 'Email is not valid!';
	}
	if (empty($_POST['email']) || empty($_POST['subject']) || empty($_POST['name']) || empty($_POST['msg'])) {
		$responses[] = 'Please complete all fields!';
	} 
	if (!$responses) {
		$to      = 'contact@example.com';
		$from = 'noreply@example.com';
		$subject = $_POST['subject'];
		$message = $_POST['msg'];
		$headers = 'From: ' . $from . "\r\n" . 'Reply-To: ' . $_POST['email'] . "\r\n" . 'X-Mailer: PHP/' . phpversion();
		if (mail($to, $subject, $message, $headers)) {
			$responses[] = 'Message sent!';		
		} else {
			$responses[] = 'Message could not be sent! Please check your mail server settings!';
		}
	}
}
?>

    <form class="contact" method="post" action="">
        <h1>Contact Us</h1>
        <div class="fields">
            <label for="email">
                <i class="fas fa-envelope"></i>
                <input id="email" type="email" name="email" placeholder="Your Email" required>
            </label>
            <label for="name">
                <i class="fas fa-user"></i>
                <input type="text" name="name" placeholder="Your Name" required>
            <label>
            <input type="text" name="subject" placeholder="Subject" required>
            <textarea name="msg" placeholder="Message" required></textarea>
        </div>
        <?php if ($responses): ?>
        <p class="responses"><?php echo implode('<br>', $responses); ?></p>
        <?php endif; ?>
        <input type="submit">
    </form>

<?=shared_footer()?>
