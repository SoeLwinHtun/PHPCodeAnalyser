<?php

require __DIR__ . "/vendor/autoload.php";
require_once 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Capture errors and display them
function showError($errno, $errstr, $errfile, $errline)
{
    echo "<b>Error:</b> [$errno] $errstr<br>";
    echo "Error on line $errline in $errfile<br>";
}

// Set the error handler
set_error_handler("showError");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Generate a random verification code
    $verificationCode = generateVerificationCode();

    // For simplicity, insert user data into the 'users' table (not secure)
    $sql = "INSERT INTO users (username, email, password, verification_code, is_verified) VALUES ('$username', '$email', '$password', '$verificationCode', 0)";
    if ($mysqli->query($sql) === TRUE) {
        // Send email verification
        $mail = new PHPMailer(true);

        $sender_mail = "dev.soelwinhtun@gmail.com";
        $app_password = "xxqrwekrjtrzkqnw";
        $sender_name = "Soe Lwin Htun"; 
        $mail_subject = "Verification Code";
        $mail_body = "Your verification code is ";
        $mail_body .=  $verificationCode;


        $mail->isSMTP();  //Send using SMTP
        
        //Enable SMTP debugging
        //SMTP::DEBUG_OFF = off (for production use)
        //SMTP::DEBUG_CLIENT = client messages
        //SMTP::DEBUG_SERVER = client and server messages
        $mail->SMTPDebug = SMTP::DEBUG_OFF;

        $mail->Host = 'smtp.gmail.com'; //Set the SMTP server to send through

        //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $mail->Port = 587;

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPAuth = true; //Whether to use SMTP authentication

        $mail->Username   = $sender_mail; //SMTP username
        $mail->Password   = $app_password; //SMTP password

        /** Set who the message is to be sent from
         *  For gmail, this generally needs to be the same as the user you logged * in as. */
        $mail->setFrom($sender_mail, $sender_name);


        // who will receive the email
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = $mail_subject; // Subject of the Email
        $mail->Body = $mail_body; // Mail Body

        //For Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');  // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg'); // You can specify the file name

        $mail->send();

        // Redirect to OTP verification page
        header("Location: verify_email.php");
        exit();
    } else {
        // Display SQL error
        trigger_error("Error: " . $sql . "<br>" . $mysqli->error, E_USER_ERROR);
    }

    // Close the database connection
    $mysqli->close();
}

// Function to generate a random verification code
function generateVerificationCode($length = 6)
{
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}