<?php

/*******w******** 
    
    Name: Rylee Jennings
    Date: June 2024
    Description: Displays a list of blog posts and provides links for authenticated users to create or edit posts.

****************/

session_start();
require 'database_connect.php';

// Check to see if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Posts ordered by creation date
$sql = "SELECT * FROM Posts ORDER BY created_at DESC";

try {
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC); 
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Post actions 
if (isset($_GET['action']) && isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
    $action = $_GET['action'];

    // Delete a post
    if ($action === 'delete') {
        $sql = "DELETE FROM Posts WHERE id = :post_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':post_id', $post_id);
    } else {
        echo '<p>Invalid action.</p>';
    }

    if ($stmt->execute()) {
        header('Location: index.php');
        exit;
    } else {
        echo '<p>Error processing post action.</p>';
    }
}

// Sanitize
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

// Date format
function format_date($datetime) {
    return date('F j, Y, g:i a', strtotime($datetime));
}

// Function to truncate characters over 200
function truncate_content($content, $length = 200) {
    if (strlen($content) > $length) {
        $content = substr($content, 0, $length) . '...';
        return $content;
    }
    return $content;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Blog Posts</title>
    <link rel="stylesheet" href="main.css">
    <style>
        .details {
            display: none;
            margin-top: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
    <script>
        function toggleDetails(id) {
            var details = document.getElementById(id);
            details.style.display = details.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Blog Posts</h2>

        <?php if (empty($posts)): ?>
            <p class="no-matches">No posts found.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($posts as $post): ?>
                    <li>
                        <a href="javascript:void(0);" onclick="toggleDetails('details-<?php echo $post['id']; ?>')">
                            <?php echo htmlspecialchars($post['title']); ?> (by <?php echo htmlspecialchars($post['author']); ?>)
                        </a>
                        <div id="details-<?php echo $post['id']; ?>" class="details">
                            <p><strong>Title:</strong> <?php echo htmlspecialchars($post['title']); ?></p>
                            <p><strong>Content:</strong> 
                                <?php
                                // To view full post
                                $truncated_content = truncate_content($post['content']);
                                echo nl2br(htmlspecialchars($truncated_content));
                                ?>
                                <?php if (strlen($post['content']) > 200): ?>
                                    <a href="view_post.php?id=<?php echo htmlspecialchars($post['id']); ?>">Read Full Post</a>
                                <?php endif; ?>
                            </p>
                            <p><strong>Posted On:</strong> <?php echo format_date($post['created_at']); ?></p>

                            <p>
                                <!-- Edit or delete the post -->
                                <a href="edit.php?id=<?php echo htmlspecialchars($post['id']); ?>">Edit</a> |
                                <a href="index.php?action=delete&post_id=<?php echo htmlspecialchars($post['id']); ?>">Delete</a>
                            </p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <br>
        <!-- Add a new post -->
        <a href="post.php">Add New Post</a>
        <br><br>
    </div>
</body>
</html>
