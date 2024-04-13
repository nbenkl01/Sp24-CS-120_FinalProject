<?php
function profile_info() {
    $pdo = connect_mysql();
    $name = $_SESSION['name'];
    $user_id = $_SESSION['user_id'];

    if ($stmt = $pdo->prepare('SELECT credits_balance, email FROM Users WHERE user_id = ?')) {
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $balance = $result['credits_balance'];
        $email = $result['email'];
    } else {
        echo '<script>alert("Could not find user!"); window.location.href = "/swaparoo/";</script>';
    }
    ?>
    <div class=balance>
        <i class="fas fa-coins"></i>
        <div><?= $balance ?></div>
    </div>
    <h2>Security Settings</h2>
    <div class="securitysettings">
        <div class="profilerow">
            <div class="profilecell profilecellheader">Username</div>
            <div class="profilecell"><?= $name ?></div>
            <div class="profilecell">
                <a href="/swaparoo/account/changeusername/">
                    Edit <i class="fas fa-edit"></i>
                </a>
            </div>
        </div>
        <div class="profilerow">
            <div class="profilecell profilecellheader">Email Address</div>
            <div class="profilecell"><?= $email ?></div>
            <div class="profilecell">
                <a href="/swaparoo/account/changeemail/">
                    Edit <i class="fas fa-edit"></i>
                </a>
            </div>
        </div>
        <div class="profilerow">
            <div class="profilecell profilecellheader">Password</div>
            <div class="profilecell">**********</div>
            <div class="profilecell">
                <a href="/swaparoo/account/changeemail/">
                    Edit <i class="fas fa-edit"></i>
                </a>
            </div>
        </div>
    </div>
<?php
}
?>