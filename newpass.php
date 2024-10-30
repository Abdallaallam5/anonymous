<?php
session_start();
if (!isset($_SESSION['reset_email'])) {
    header("Location: reset_password.php");
    exit("لم يتم التحقق من البريد الإلكتروني. الرجاء طلب كود التحقق أولاً.");
}


$conn = new mysqli('localhost', 'root', '', 'therapist_online');


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $_SESSION['reset_email']);

        if ($stmt->execute()) {
            $message = "تم تحديث كلمة المرور بنجاح.";
            unset($_SESSION['reset_email']);
            header("refresh:3;url=logout.php");
        } else {
            $message = "حدث خطأ أثناء تحديث كلمة المرور.";
        }

        $stmt->close();
    } else {
        $message = "كلمة المرور غير متطابقة.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="login-style.css">
    <title>therapist</title>
</head>
<body>
<div class="header" style="justify-content: space-between;">
        <div class="profile">
            <img src="../images/userimage.jpg" alt="">
            <ul class="links">
                <li><a href="../index.php">home</a></li>
                <li><a href="profile.php">profile</a></li>
                <li id="menu"><a href="#">setting</a>
                    <ul class="small-menu">
                        <li><a href="#">change password</a></li>
                        <li><a href="logout.php">logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="logo">
            <img src="../images/logo.png" alt="">
        </div>
    </div>

    <div class="home">
        <div class="sing-in">
            <h2>Reset Password</h2>
            <form method="post" action="newpass.php">
                <input type="password" name="new_password" placeholder="New password" required>
                <input type="password" name="confirm_password" placeholder="New password again" required>
                <input type="submit" value="Update Password">
                <p><?php echo $message; ?></p>
            </form>
        </div>
    </div>
    <script src="button.js"></script>
</body>
</html>
