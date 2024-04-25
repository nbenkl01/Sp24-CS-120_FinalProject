<?php
include '../functions.php';

header('Content-Type: application/json');

$pdo = connect_mysql();

$category = $_GET['category'] ?? '';
$keywords = $_GET['keywords'] ?? '';

// prepare the search query based on the category user wants to search
switch ($category) {
    case 'title':
        $searchField = "title";
        break;
    case 'author':
        $searchField = "author";
        break;
    case 'isbn':
        $searchField = "isbn";
        break;
    default:
        echo json_encode(["error" => "Invalid search category"]);
        exit;
}

$query = "
    SELECT Items.item_id, Items.title, Items.author, Items.isbn, Items.item_condition,
           MIN(Items.credit_value) AS lowest_price, Users.username AS owner_username
    FROM Items
    JOIN Users ON Items.owner_id = Users.user_id
    WHERE Items.$searchField LIKE :keywords
    GROUP BY Items.item_id
    ORDER BY lowest_price ASC
";

// Use prepared statements to prevent SQL injection
$stmt = $pdo->prepare($query);
$stmt->bindValue(':keywords', '%' . $keywords . '%');
$stmt->execute();

$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($books);
