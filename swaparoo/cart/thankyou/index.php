<?php session_start(); include '../../functions.php'; shared_header('Thank you')?>


<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
<script> confetti({particleCount: 300, spread: 90})</script>


<div class="orderconfirmation">
    <h1>Order Confirmation</h1>
    <div>Thank you for your order!</div>
    <div class="shiptext"> Your items are expected to ship on <div class = "shipdate"><?=date("F d, Y", strtotime('+2 days'))?></div></div>
    <div> Order Total: <i class="fas fa-coins"></i><?=$_GET['order_total']?></div>
</div>
</div>

<?php shared_footer() ?>