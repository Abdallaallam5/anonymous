<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'therapist_online');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$post_id = $_GET['post_id'];
$content = $_POST['comment_content'];
$email = $_SESSION['email'];

$sql = "INSERT INTO comments (post_id, content, email) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $post_id, $content, $email);

if ($stmt->execute()) {
    $comment_id = $stmt->insert_id;
    echo json_encode(['success' => true, 'comment_id' => $comment_id, 'comment' => $content]);
} else {
    echo json_encode(['success' => false]);
}

$stmt->close();
$conn->close();
?>
