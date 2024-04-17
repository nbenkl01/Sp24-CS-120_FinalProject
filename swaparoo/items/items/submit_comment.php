<?php
session_start();
include '../../functions.php';
$pdo = connect_mysql();
date_default_timezone_set('America/New_York');

// Check if the required POST data exists
if (isset($_POST['item_id'], $_POST['parent_id'], $_POST['user_id'], $_POST['comment_text'])) {
    // Prepare and execute the SQL statement to insert the comment into the database
    $stmt = $pdo->prepare('INSERT INTO Comments (item_id, parent_id, user_id, comment_text, comment_timestamp) VALUES (?, ?, ?, ?, NOW())');
    $stmt->execute([$_POST['item_id'], $_POST['parent_id'], $_POST['user_id'], $_POST['comment_text']]);

    // You can respond with a success message or any other relevant information
    // echo '<script>alert("Your comment has been submitted!"); window.location.href = "./?item='.$_POST['item_id'].'";</script>';
    echo 'Your comment has been submitted!';
} else {
    // Respond with an error message if the required POST data is missing
    http_response_code(400);
    // echo '<script>alert("Error: Required data is missing."); window.location.href = "./?item='.$_POST['item_id'].'";</script>';
}
?>