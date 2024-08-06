<?php

/*******w******** 
    
    Name: Rylee Jennings
    Date: June 2024
    Description: Displays the full content of a blog post.

****************/

session_start();
require 'database_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($post_id <= 0) {
    header('Location: index.php');
    exit;
}

try {
    $sql = "SELECT * FROM Posts WHERE id = :post_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':post_id', $post_id);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

function format_date($datetime) {
    return date('F j, Y, g:i a', strtotime($datetime));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Post</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <div class="container">
        <h2><?php echo htmlspecialchars($post['title']); ?></h2>
        <p><strong>Author:</strong> <?php echo htmlspecialchars($post['author']); ?></p>
        <p><strong>Content:</strong></p>
        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
        <p><strong>Posted On:</strong> <?php echo format_date($post['created_at']); ?></p>

        <br>
        <a href="index.php">Back to Posts</a>
    </div>
</body>
</html>
