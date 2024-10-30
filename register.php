<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'therapist_online');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['code'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($conn->real_escape_string($_POST['password']), PASSWORD_DEFAULT);
    $code = $conn->real_escape_string($_POST['code']);

    if ($_SESSION['email'] === $email && $_SESSION['verification_code'] == $code) {
        $sql = "INSERT INTO users (email, password, is_verified) VALUES ('$email', '$password', TRUE)";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['message-up'] = "تم التسجيل بنجاح";
        } else {
            $_SESSION['message-up'] = "خطأ في التسجيل: " . $conn->error;
        }
    } else {
        $_SESSION['message-up'] = "رمز التحقق غير صحيح";
    }

    header("Location: login.php");
    exit;
}

$conn->close();
?>
