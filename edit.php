<?php

/*******w******** 
    
    Name: Rylee Jennings
    Date: June 2024
    Description: Edit an existing post

****************/

session_start();
require_once 'authenticate.php'; 
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = sanitize_input($_POST['title']);
    $content = sanitize_input($_POST['content']);
    $author = sanitize_input($_POST['author']);

    if (strlen($title) > 0 && strlen($content) > 0 && !empty($author)) {
        try {
            $sql = "UPDATE Posts SET title = :title, content = :content, author = :author WHERE id = :post_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':author', $author);
            $stmt->bindParam(':post_id', $post_id);

            if ($stmt->execute()) {
                header('Location: index.php');
                exit;
            } else {
                $error = "Error updating post.";
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = "Title and content must be at least 1 character long.";
    }
} else {
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
        $error = "Error: " . $e->getMessage();
    }
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
    <title>Edit Post</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <div class="container">
        <h2>Edit Post</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" action="edit.php?id=<?php echo htmlspecialchars($post_id); ?>">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required><br><br>

            <label for="content">Content:</label><br>
            <textarea id="content" name="content" rows="10" cols="50" required><?php echo htmlspecialchars($post['content']); ?></textarea><br><br>

            <label for="author">Author:</label>
            <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($post['author']); ?>" required><br><br>

            <input type="submit" value="Update Post">
        </form>

        <br>
        <a href="index.php">Back to Posts</a>
    </div>
</body>
</html>

