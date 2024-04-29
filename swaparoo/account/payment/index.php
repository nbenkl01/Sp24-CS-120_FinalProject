<?php
session_start();
include '../../functions.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../login.php');
    exit;
}

$pdo = connect_mysql();
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT credits_balance FROM Users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

shared_header('Load Credits');

?>

<link rel="stylesheet" href="../../styles/payment.css">

<div class="container">
    <h1>Load Credits</h1>
    <p>Your current balance: <strong><?php echo $user['credits_balance'] ?? '0'; ?> credits</strong></p>
    
    <div class="payment-form">
        <form id="paymentForm" method="post" action="charge.php">
            <div class="form-group">
                <label for="creditAmount">Amount to load (USD)</label>
                <input type="number" id="creditAmount" name="credit_amount" min="1" required>
            </div>
            <div class="form-group">
                <label for="cardNumber">Card Number</label>
                <input type="text" id="cardNumber" name="card_number" minlength="16" maxlength="16" pattern="\d*" required>
            </div>
            <div class="form-group">
                <label for="cardExpiry">Expiration Date (MM/YY)</label>
                <input type="text" id="cardExpiry" name="card_expiry" pattern="\d{2}/\d{2}" placeholder="MM/YY" required>
            </div>
            <div class="form-group">
                <label for="cardCVC">CVC</label>
                <input type="text" id="cardCVC" name="card_cvc" minlength="3" maxlength="4" pattern="\d*" required>
            </div>
            <button type="submit" class="btn-pay">Pay Now</button>
        </form>
    </div>
</div>

<?php
shared_footer();
?>
