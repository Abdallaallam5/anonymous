<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: pages/login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'therapist_online');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_SESSION['email'];


$sql = "SELECT posts.*, 
               IFNULL(COUNT(views.id), 0) AS views_count, 
               (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS likes_count,
               (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id AND likes.email = ?) AS user_liked
        FROM posts 
        LEFT JOIN views ON posts.id = views.post_id 
        GROUP BY posts.id 
        ORDER BY RAND()";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die("Error: " . $conn->error);
}


$comments = [];
$comments_sql = "SELECT * FROM comments";
$comments_result = $conn->query($comments_sql);
if ($comments_result) {
    while ($comment = $comments_result->fetch_assoc()) {
        $comments[$comment['post_id']][] = $comment;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://unpkg.com/typeit@8.7.1/dist/index.umd.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Therapist</title>
</head>
<body>
    <div class="header">
        <div class="profile">
            <img src="images/userimage.jpg" alt="">
            <ul class="links">
                <li><a href="index.php">home</a></li>
                <li><a href="pages/profile.php">profile</a></li>
                <li id="menu"><a href="#">setting</a>
                    <ul class="small-menu">
                        <li><a href="pages/reset_password.php">change password</a></li>
                        <li><a href="pages/logout.php">logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="logo">
            <img src="images/logo.png" alt="">
        </div>
    </div>
    <div class="home">
        <div class="container">
            <div class="main-box">
                <h2 id="quote"></h2>
            </div>
            <div class="new-post-box">
                <form action="pages/add_post.php" method="POST">
                    <textarea id="postInput" name="content" placeholder="say everything" rows="1" required></textarea>
                    <input type="submit" value="post" id="submitPost" disabled>
                </form>
            </div>
            <div class="posts-container">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="post-box" data-post-id="<?php echo $row['id']; ?>">
                        <div class="user">
                            <img src="images/userimage.jpg" alt="">
                            <span>anonymous</span>
                        </div>
                        <div class="content">
                            <h2><?php echo htmlentities($row['content']); ?></h2>
                        </div>
                        <div class="box-footer">
                            <div class="likes">
                                <i class="fa-<?php echo $row['user_liked'] ? 'solid' : 'regular'; ?> fa-heart"></i>
                                <p><span><?php echo $row['likes_count']; ?></span> like</p>
                            </div>
                            <div class="info">
                                <i class="fa-solid fa-eye"></i>
                                <p><span><?php echo $row['views_count']; ?></span> view</p>
                            </div>
                        </div>
                        <hr>
                        <div class="comment">
                            <div class="comment-box">
                            <?php 
                                if (isset($comments[$row['id']])) {
                                    foreach ($comments[$row['id']] as $comment) {
                                        echo "<div class='comment-text' data-comment-id='" . $comment['id'] . "'>";
                                        echo "<p><span>anonymous: </span>" . htmlentities($comment['content']) . "</p>";
                                        if ($comment['email'] === $email) {
                                            echo " <i class='fa-regular fa-circle-xmark delete-comment' style='cursor:pointer;'></i>";
                                        }
                                        echo "</div>";
                                    }
                                }
                            ?>
                            </div>
                            <div class="post-comment"> 
                                <form class="comment-form" data-post-id="<?php echo $row['id']; ?>" action="javascript:void(0);">
                                    <input type="text" name="comment_content" placeholder="Write your comment" required>
                                    <input type="submit" value="comment">
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    <script>
        new TypeIt("#quote", {
            strings: "You're here to share everything that's on your heart.",
            speed: 95,
            loop: true,
        }).go();
    </script>
<script src="main.js"></script>
</body>
</html>
