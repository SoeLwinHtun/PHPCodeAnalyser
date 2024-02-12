<?php

require_once 'config.php';
session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Intentional vulnerability: No validation or sanitization, concatenating directly into the query
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";

    // Print the SQL statement for testing purposes
    //echo "SQL Query: $sql";

    //' OR 1=1 -- ( don't forget the space )

    // Execute the query (intentionally not using prepared statements)
    try {
        $result = $mysqli->query($sql);

        // Check if any rows are returned
        if ($result && $result->num_rows > 0) {
            // Fetch the user data
            $row = $result->fetch_assoc();

            // Set session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];

            // Redirect to the dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Invalid username or password.";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    // Close the database connection
    $mysqli->close();
}
?>
