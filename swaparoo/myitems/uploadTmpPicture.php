<?php
$targetDir = "../../images/tmp/";
$randomString = bin2hex(random_bytes(15)); // Generate a random string for the filename
$targetFile = $targetDir . $randomString . '.webp'; // Append the .webp extension

// Ensure the target directory exists
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// Handle the file upload
if (isset($_FILES["coverImage"]["tmp_name"])) {
    if (move_uploaded_file($_FILES["coverImage"]["tmp_name"], $targetFile)) {
        // Successfully uploaded
        echo json_encode(array("success" => true, "url" => $targetFile));
    } else {
        // Error during the upload
        echo json_encode(array("success" => false, "message" => "File could not be uploaded."));
    }
} else {
    echo json_encode(array("success" => false, "message" => "No file was uploaded."));
}
?>
