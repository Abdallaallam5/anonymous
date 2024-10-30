<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'therapist_online');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['post_id']) && isset($_SESSION['email'])) {
    $postId = intval($_GET['post_id']);
    $email = $_SESSION['email'];

    
    $stmt = $conn->prepare("SELECT * FROM views WHERE post_id = ? AND email = ?");
    $stmt->bind_param("is", $postId, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO views (post_id, email) VALUES (?, ?)");
        $stmt->bind_param("is", $postId, $email);
        $stmt->execute();
        $stmt->close();
        echo "View recorded";
    } else {
        echo "View already recorded";
    }
} else {
    echo "Invalid request";
}

$conn->close();
?>
