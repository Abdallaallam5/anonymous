<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'therapist_online');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_SESSION['email'];


$sql = "SELECT posts.*, COUNT(views.id) AS views_count, COUNT(likes.id) AS likes_count 
        FROM posts 
        LEFT JOIN views ON posts.id = views.post_id 
        LEFT JOIN likes ON posts.id = likes.post_id 
        WHERE posts.email = '$email' 
        GROUP BY posts.id 
        ORDER BY post_date DESC";
$result = $conn->query($sql);

if ($result === false) {
    die("Error: " . $conn->error);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post_id'])) {
    $post_id = $_POST['delete_post_id'];

    
    $stmt = $conn->prepare("DELETE FROM comments WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->close();


    $stmt = $conn->prepare("DELETE FROM views WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->close();

    
    $stmt = $conn->prepare("DELETE FROM likes WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->close();

   
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND email = ?");
    $stmt->bind_param("is", $post_id, $email);
    if ($stmt->execute()) {
        header("Location: profile.php");
        exit();
    } else {
        echo "Error deleting post";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="profile-style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo+Play:wght@200..1000&family=Cairo:wght@200..1000&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <title>therapist</title>
</head>
<body>
    <div class="header">
        <div class="profile">
            <img src="../images/userimage.jpg" alt="">
            <ul class="links">
                <li><a href="../index.php">home</a></li>
                <li><a href="profile.php">profile</a></li>
                <li id="menu"><a href="#">setting</a>
                    <ul class="small-menu">
                        <li><a href="reset_password.php">change password</a></li>
                        <li><a href="logout.php">logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="logo">
            <img src="../images/logo.png" alt="">
        </div>
    </div>
    <div class="container">
        <div class="home">
            <?php if ($result->num_rows === 0): ?>
                <h2 class="notice-message">no posts yet</h2>
            <?php else: ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="post-box" data-post-id="<?php echo $row['id']; ?>">
                        <div class="user">
                            <img src="../images/userimage.jpg" alt="">
                            <span>anonymous</span>
                        </div>
                        <div class="content">
                            <h2><?php echo htmlentities($row['content']); ?></h2>
                        </div>
                        <div class="details">
                            <form method="POST">
                                <input type="hidden" name="delete_post_id" value="<?php echo $row['id']; ?>">
                                <button type="submit">delete</button>
                            </form>
                            <div class="box-footer">
                                <div class="likes">
                                    <p><span><?php echo $row['likes_count']; ?></span> like</p>
                                </div>
                                <div class="info">
                                    <i class="fa-solid fa-eye"></i>
                                    <p><span><?php echo $row['views_count']; ?></span> view</p>
                                </div>
                            </div>
                        </div>
                        <div class="comment">
                            <div class="comment-box">
                                <?php 
                                // get comments
                                $comments_sql = "SELECT * FROM comments WHERE post_id = ?";
                                $comments_stmt = $conn->prepare($comments_sql);
                                $comments_stmt->bind_param("i", $row['id']);
                                $comments_stmt->execute();
                                $comments_result = $comments_stmt->get_result();

                                // show comments
                                while ($comment = $comments_result->fetch_assoc()) {
                                    echo "<div class='comment-text'><p><span>Anonymous: </span>" . htmlentities($comment['content']) . "</p></div>";
                                }
                                $comments_stmt->close();
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
    <script src="../main.js"></script>
</body>
</html>

<?php
$conn->close();
?>
