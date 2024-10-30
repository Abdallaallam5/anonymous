<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'therapist_online');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['post_id']) && isset($_SESSION['email'])) {
    $post_id = $_GET['post_id'];
    $email = $_SESSION['email'];

    
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND email = ?");
    $stmt->bind_param("is", $post_id, $email);
    if ($stmt->execute()) {
        echo "Post deleted";
    } else {
        echo "Error deleting post";
    }

    $stmt->close();
} else {
    echo "Invalid request";
}

$conn->close();
?>
