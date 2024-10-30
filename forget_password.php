<?php
session_start();
require 'C:/xampp/htdocs/therapist/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$host = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'therapist_online';

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['send'])) {
        $email = $_POST['email'];
        $query = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $code = rand(100000, 999999); 
            $updateQuery = $conn->prepare("UPDATE users SET reset_code = ? WHERE email = ?");
            $updateQuery->bind_param("is", $code, $email);
            $updateQuery->execute();

            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'amrelamawy712@gmail.com'; 
                $mail->Password = 'nyws ezwa mxam oiis'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('your_email@gmail.com', 'Therapist Online');
                $mail->addAddress($email);
                $mail->isHTML(true); 
                $mail->Subject = 'Password Reset Code';
                $mail->Body = "Your password reset code is: <b>$code</b>";

                $mail->send();
                $message = "تم إرسال الكود بنجاح إلى بريدك الإلكتروني.";
            } catch (Exception $e) {
                $message = "حدث خطأ أثناء إرسال الكود: {$mail->ErrorInfo}";
            }
        } else {
            $message = "البريد الإلكتروني غير مسجل.";
        }
    } elseif (isset($_POST['reset'])) {
        $email = $_POST['email'];
        $code = $_POST['code'];
        $query = $conn->prepare("SELECT * FROM users WHERE email = ? AND reset_code = ?");
        $query->bind_param("si", $email, $code);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['reset_email'] = $email;
            header("Location: newpass-forget.php");
            exit();
        } else {
            $message = "الكود غير صحيح. حاول مرة أخرى.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
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
    <div class="home">
        <div class="sing-in">
            <h2>Reset Password</h2>
            <form method="post" action="forget_password.php">
                <input type="email" name="email" placeholder="Email" required>
                <input type="submit" name="send" value="Send">
                <input type="text" name="code" placeholder="Code">
                <input type="submit" name="reset" value="Reset">
                <p><?php echo $message; ?></p>
            </form>
        </div>
    </div>
    <script src="button.js"></script>
</body>
</html>
