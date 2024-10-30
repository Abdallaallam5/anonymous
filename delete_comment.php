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


$comment_id = $_GET['id'];


$email = $_SESSION['email'];
$sql = "DELETE FROM comments WHERE id = ? AND email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $comment_id, $email);


header('Content-Type: application/json'); 
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]); 
}

$stmt->close();
$conn->close();
?>
