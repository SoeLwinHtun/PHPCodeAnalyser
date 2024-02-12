<?php
session_start(); // Start the session

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Directory to upload files (make sure it's writable)
$uploadDir = __DIR__ . '/uploads/'; // Update the path based on your file structure

// Check if the file is uploaded without errors
if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
    $fileName = $_FILES['profilePicture']['name'];
    $tempFilePath = $_FILES['profilePicture']['tmp_name'];

    // Save the file with the user's ID as the filename (overwrite existing file)
    $destination = $uploadDir . $_SESSION['user_id'] . '.jpg';
    if (move_uploaded_file($tempFilePath, $destination)) {
        // Redirect back to the dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        // Display an error message if file move failed
        echo "Error moving file to destination.";
    }
} else {
    // Display an error message if file upload failed
    echo "Error uploading file. Error code: " . $_FILES['profilePicture']['error'];
}
?>
