<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'therapist_online');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $user['email'];

            setcookie("email", $user['email'], time() + (86400 * 30), "/"); 
            $_SESSION['logged_in_email'] = $email; 
            header("Location: ../index.php");
            exit();
        } else {
            $_SESSION['message-in'] = "كلمة المرور غير صحيحة";
        }
    } else {
        $_SESSION['message-in'] = "البريد الإلكتروني غير موجود";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="login-style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo+Play:wght@200..1000&family=Cairo:wght@200..1000&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <title>therapist</title>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="../images/logo.png" alt="">
        </div>
    </div>
    <div class="home">
        <div class="sing-in">
            <h2>تسجيل الدخول</h2>
            <form action="login.php" method="POST">
                <input type="email" name="email" placeholder="البريد الإلكتروني" required>
                <input type="password" name="password" placeholder="كلمة المرور" required>
                <div class="details">
                    <a href="forget_password.php">forget passwrod</a>
                </div>
                <input type="submit" value="تسجيل الدخول">
            </form>
            <p id="message-in"><?php if(isset($_SESSION['message-in'])) { echo $_SESSION['message-in']; unset($_SESSION['message-in']); } ?></p>
        </div>
        <div class="sing-up">
            <h2>تسجيل جديد</h2>
            <form action="send_code.php" method="POST">
                <input type="email" name="email" placeholder="example@gmail.com" required>
                <input type="submit" value="send code">
            </form>
            <form action="register.php" method="POST">
                <input type="email" name="email" placeholder="البريد الإلكتروني" required>
                <input type="text" name="code" placeholder="code" required>
                <input type="password" name="password" placeholder="كلمة المرور" required>
                <input type="submit" value="تسجيل">
            </form>
            <p id="message-up"><?php if(isset($_SESSION['message-up'])) { echo $_SESSION['message-up']; unset($_SESSION['message-up']); } ?></p>
        </div>
    </div>
    <script src="main.js"></script>
</body>
</html>
