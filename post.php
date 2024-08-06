<?php

/*******w******** 
    
    Name: Rylee Jennings
    Date: June 2024
    Description: Create a new post

****************/

require_once 'authenticate.php'; 
require 'database_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = sanitize_input($_POST['title']);
    $content = sanitize_input($_POST['content']);
    $author = sanitize_input($_POST['author']);

    if (strlen($title) > 0 && strlen($content) > 0 && !empty($author)) {
        try {
            $sql = "INSERT INTO Posts (title, content, author, created_at) VALUES (:title, :content, :author, NOW())";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':author', $author);

            if ($stmt->execute()) {
                header('Location: index.php');
                exit;
            } else {
                $error = "Error adding post.";
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = "Title and content must be at least 1 character long.";
    }
}

function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Post</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <div class="container">
        <h2>Add New Post</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" action="post.php">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required><br><br>

            <label for="content">Content:</label><br>
            <textarea id="content" name="content" rows="10" cols="50" required></textarea><br><br>

            <label for="author">Author:</label>
            <input type="text" id="author" name="author" required><br><br>

            <input type="submit" value="Add Post">
        </form>

        <br>
        <a href="index.php">Back to Posts</a>
    </div>
</body>
</html>
