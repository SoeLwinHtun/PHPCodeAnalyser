<!-- dashboard.php -->

<?php
session_start(); // Start the session

// Check if the user is not logged in, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Function to display user details
function displayUserDetails() {
    echo "<div class='user-details'>";
    echo "<p>Username: " . $_SESSION['username'] . "</p>";
    echo "<p>Email: " . $_SESSION['email'] . "</p>";
    echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";

    // Check if the user has a profile picture
    $profilePicturePath = "uploads/" . $_SESSION['user_id'] . ".jpg";
    if (file_exists($profilePicturePath)) {
        echo "<img src='$profilePicturePath' alt='Profile Picture'>";
    } else {
        echo "<p>No profile picture yet.</p>";
    }

    echo "</div>";
}

// Function to display articles
function displayArticles() {
    echo "<div class='article'>";
    echo "<h2>Article 1</h2>";
    // Intentional vulnerability: Reflecting unsanitized URL parameter in the content
    $articleContent = $_GET['content'] ?? '';
    echo "<p>$articleContent</p>";
    echo "</div>";

    echo "<div class='article'>";
    echo "<h2>Article 2</h2>";
    echo "</div>";
    // Add more articles as needed
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard-style.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to the Dashboard</h1>

        <?php
        // Display user details
        displayUserDetails();
        ?>

        <?php
        // Display articles
        displayArticles();
        ?>

        <!-- Profile Picture Upload Form -->
        <form method="post" action="upload_profile_picture.php" enctype="multipart/form-data">
            <label for="profilePicture">Change Profile Picture:</label>
            <input type='file' name="profilePicture" required>
            <input type="submit" value="Upload">
        </form>

        <div class="user-options">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
