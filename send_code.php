<?php
session_start();
require 'C:/xampp/htdocs/therapist/vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = new mysqli('localhost', 'root', '', 'therapist_online');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $verification_code = rand(100000, 999999);

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['message-up'] = "البريد الإلكتروني موجود مسبقًا";
    } else {
        
        $mail = new PHPMailer(true); 
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'amrelamawy712@gmail.com'; 
        $mail->Password = 'nyws ezwa mxam oiis';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('no-reply@therapist.com', 'therapist');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Verification Code';
        $mail->Body    = "Your verification code is $verification_code";

        try {
            $mail->send();
            $_SESSION['verification_code'] = $verification_code;
            $_SESSION['email'] = $email;
            $_SESSION['message-up'] = "تم إرسال الكود بنجاح";
        } catch (Exception $e) {
            $_SESSION['message-up'] = "خطأ في إرسال البريد الإلكتروني: " . $mail->ErrorInfo;
        }
    }

    header("Location: login.php");
    exit;
}

$conn->close();
?>
