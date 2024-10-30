<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'therapist_online');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['content']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $email = $_SESSION['email'] ?? null;
    if ($email) {
        $content = $conn->real_escape_string($_POST['content']);

        $stmt = $conn->prepare("INSERT INTO posts (email, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $content);

        if ($stmt->execute()) {
            header("Location: ../index.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error: Email is missing in session.";
    }
} else {
    echo "Invalid request or session.";
}

$conn->close();
?>
