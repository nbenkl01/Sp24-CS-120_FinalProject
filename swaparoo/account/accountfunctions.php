<?php
function profile_info() {
    $pdo = connect_mysql();
    $name = $_SESSION['name'];
    $user_id = $_SESSION['user_id'];

    if ($stmt = $pdo->prepare('SELECT credits_balance, email FROM Users WHERE user_id = ?')) {
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $balance = number_format($result['credits_balance']);
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
                <a href="/swaparoo/account/changepassword/">
                    Edit <i class="fas fa-edit"></i>
                </a>
            </div>
        </div>
    </div>
<?php
}

function transactions_items_rows($transaction_types) {
    $pdo = connect_mysql();
    $user_id = $_SESSION['user_id'];
    if ($transaction_types == 'sold') {
        $query = 'SELECT t.item_id, t.transaction_timestamp, i.title, i.author, t.status, t.price, u.username AS other_party FROM Transactions AS t, Items AS i, Users AS u WHERE t.seller_id = ? AND t.item_id = i.item_id AND t.buyer_id = u.user_id ORDER BY t.transaction_timestamp DESC;';
    } elseif ($transaction_types == 'bought') {
        $query = 'SELECT t.item_id, t.transaction_timestamp, i.title, i.author, t.status, t.price, u.username AS other_party FROM Transactions AS t, Items AS i, Users AS u WHERE t.buyer_id = ? AND t.item_id = i.item_id AND t.seller_id = u.user_id ORDER BY t.transaction_timestamp DESC;';
    }
    if ($stmt = $pdo->prepare($query)) {
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $item_id = $result['item_id'];
            $timestamp = $result['transaction_timestamp'];
            $date_str = date('F j, Y', strtotime($timestamp));
            $title = $result['title'];
            $author = $result['author'];
            $order_status = $result['status'];
            $price = number_format($result['price']);
            $other_party = $result['other_party'];
            echo <<<ROW
            <tr class="transactionrow">
            <td class="transactioncell">
            <a href="/swaparoo/items/items/?item=$item_id">    
            <img src="../../images/items/$item_id.webp">
            </a>
            </td>
            <td class="transactioncell transactionitemname" >
            <a href="/swaparoo/items/items/?item=$item_id">
            <div>
            <div class="transactiontitletext">$title</div>
            <div class="transactionauthortext">$author</div>
            </div>
            </a>
            </td>
            <td class="transactioncell">$date_str</td>
            <td class="transactioncell">$order_status</td>
            <td class="transactioncell">
            <div class=creditcost>
            <i class="fas fa-coins"></i>
            <div>$price</div>
            </div>    
            </td>
            <td class="transactioncell">$other_party</td>
            </tr>
            ROW;
        }
    } else {
        echo '<script>alert("Could not find user!"); window.location.href = "/swaparoo/";</script>';
    }
}
?>
