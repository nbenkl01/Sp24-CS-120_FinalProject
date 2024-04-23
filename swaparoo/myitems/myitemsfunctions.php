<?php



function listed_items_rows() {
    $pdo = connect_mysql();
    $user_id = $_SESSION['user_id'];
    $query = 'SELECT i.item_id, i.title, i.author, i.item_condition, i.credit_value, "Sale Pending" AS status
    FROM Items AS i, Transactions AS t
    WHERE t.status = "Pending" AND t.item_id = i.item_id AND t.seller_id = ?
    
    UNION ALL
    
    SELECT i.item_id, i.title, i.author, i.item_condition, i.credit_value, "Listed" AS status
    FROM Items AS i
    WHERE i.available = 1 AND i.owner_id = ?;';

    if ($stmt = $pdo->prepare($query)) {
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $user_id);
        $stmt->execute();
        $last_list_status = "";
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $item_id = $result['item_id'];
            $title = $result['title'];
            $author = $result['author'];
            $list_status = $result['status'];
            $price = number_format($result['credit_value']);
            $item_condition = $result['item_condition'];
            if($list_status == "Listed") {
                $cellclass = "";
                $titletextclass = "";
                $authortextclass = "";
                if ($last_list_status == 'Sale Pending') {
                    $rowclass = "firstrowlisted";
                } else {
                    $rowclass = "";
                }

            } else {
                $rowclass = "";
                $cellclass = "entrycellpending";
                $titletextclass = "titletextpending";
                $authortextclass = "authortextpending";
            }
            $last_list_status = $list_status;
            echo <<<ROW
            <tr class="$rowclass entryrow">
            <td class="entrycell $cellclass">
            <a href="/swaparoo/items/items/?item=$item_id">    
            <img src="../../images/items/$item_id.webp">
            </a>
            </td>
            <td class="entrycell $cellclass entryitemname" >
            <a href="/swaparoo/items/items/?item=$item_id">
            <div>
            <div class="entrytitletext $titletextclass">$title</div>
            <div class="entryauthortext $authortextclass">$author</div>
            </div>
            </a>
            </td>
            <td class="entrycell $cellclass">$item_condition</td>
            <td class="entrycell $cellclass">$list_status</td>
            <td class="entrycell $cellclass">
            <div class=creditcost>
            <i class="fas fa-coins"></i>
            <div>$price</div>
            </div>    
            </td>
            <td class="entrycell $cellclass">
            ROW;
            if($list_status == "Listed") {
                echo <<<UNLISTBTN
                <form action="updatelisting.php" method="post">
                <input type="submit" value="Unlist" class="listingsubmit">
                <input type="hidden" value="$item_id" name="item_id">
                <input type="hidden" value="unlist" name="action">
                </form>
                UNLISTBTN;
            }
            echo <<<ROW
            </td>
            </tr>
            ROW;
        }
    } else {
        echo '<script>alert("Could not find user!"); window.location.href = "/swaparoo/";</script>';
    }
    
}


function owned_items_rows() {
    $pdo = connect_mysql();
    $user_id = $_SESSION['user_id'];
    $query = 'SELECT i.item_id, i.title, i.author, i.item_condition, i.credit_value
    FROM Items AS i
    WHERE i.available = 0 AND i.owner_id = ?;';

    if ($stmt = $pdo->prepare($query)) {
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $item_id = $result['item_id'];
            $title = $result['title'];
            $author = $result['author'];
            $price = number_format($result['credit_value']);
            $item_condition = $result['item_condition'];
            echo <<<ROW
            <tr class="entryrow">
            <td class="entrycell">
            <a href="/swaparoo/items/items/?item=$item_id">    
            <img src="../../images/items/$item_id.webp">
            </a>
            </td>
            <td class="entrycell entryitemname" >
            <a href="/swaparoo/items/items/?item=$item_id">
            <div>
            <div class="entrytitletext">$title</div>
            <div class="entryauthortext">$author</div>
            </div>
            </a>
            </td>
            <td class="entrycell">$item_condition</td>
            <td class="entrycell">
            <div class=creditcost>
            <i class="fas fa-coins"></i>
            <div>$price</div>
            </div>    
            </td>
            <td class="entrycell">

            <form action="updatelisting.php" method="post">
            <input type="submit" value="List" class="listingsubmit">
            <input type="hidden" value="$item_id" name="item_id">
            <input type="hidden" value="list" name="action">
            </form>

            </td>
            </tr>
            ROW;
        }
    } else {
        echo '<script>alert("Could not find user!"); window.location.href = "/swaparoo/";</script>';
    }
    
}

?>
