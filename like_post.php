<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'therapist_online');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['post_id']) && isset($_SESSION['email'])) {
    $post_id = intval($_GET['post_id']);
    $email = $_SESSION['email'];

    $stmt = $conn->prepare("SELECT * FROM likes WHERE post_id = ? AND email = ?");
    $stmt->bind_param("is", $post_id, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO likes (post_id, email) VALUES (?, ?)");
        $stmt->bind_param("is", $post_id, $email);
        $stmt->execute();
        $stmt->close();
        echo "Like recorded";
    } else {
        $stmt = $conn->prepare("DELETE FROM likes WHERE post_id = ? AND email = ?");
        $stmt->bind_param("is", $post_id, $email);
        $stmt->execute();
        $stmt->close();
        echo "Like removed";
    }
} else {
    echo "Invalid request";
}

$conn->close();
?>
