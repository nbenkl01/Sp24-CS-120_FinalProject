<?php
header('Content-Type: application/json');
session_start();
include '../functions.php';
// fixed path definition for image saving
$imagesPath = "../../images/items/";  

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403);
    echo json_encode(["error" => "User not authenticated"]);
    exit;
}

$pdo = connect_mysql();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? null;
    $description = $_POST['description'] ?? null;
    $category = "Books"; // category is always set to "Books"
    $condition = $_POST['condition'] ?? null;
    $credit_value = $_POST['credit_value'] ?? null;
    $owner_id = $_SESSION['user_id'] ?? null;
    $title = $_POST['title'] ?? null;
    $author = $_POST['author'] ?? null;
    $isbn = $_POST['isbn'] ?? null;
    $thumbnailUrl = $_POST['thumbnail'] ?? null;

    $missing = [];
    foreach (['name', 'description', 'condition', 'credit_value', 'owner_id', 'title', 'author', 'isbn'] as $field) {
        if (empty($$field)) {
            $missing[] = $field;
        }
    }

    if (!empty($missing)) {
        http_response_code(400);
        echo json_encode(["error" => "Missing item information: " . implode(', ', $missing)]);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO Items (name, description, category, item_condition, owner_id, credit_value, title, author, isbn) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $description, $category, $condition, $owner_id, $credit_value, $title, $author, $isbn])) {
        $item_id = $pdo->lastInsertId();
        $result = saveThumbnail($thumbnailUrl, $item_id, $imagesPath);
        
        if (isset($result['error'])) {
            echo json_encode(["error" => $result['error']]);
        } else {
            echo json_encode(["success" => "Book added successfully", "item_id" => $item_id, "image_info" => $result]);

            // // Cleanup thumbnail from temporary storage if needed
            // if ($thumbnailUrl && strpos($thumbnailUrl) !== false) {
            //     // Construct the correct file path based on the relative location
            //     $filePath = basename($thumbnailUrl);
            //     if (file_exists($filePath)) {
            //         unlink($filePath); // Attempt to delete the file
            //     } else {
            //         // Optional: Log an error or send back a response if the file was not found
            //         echo json_encode(["error" => "File not found for deletion: " . $filePath]);
            //     }
            // }
        }
        
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to add book: " . implode(' ', $stmt->errorInfo())]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}
?>
