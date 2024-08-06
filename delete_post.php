<?php

/*******w******** 
    
    Name: Rylee Jennings
    Date: June 2024
    Description: Deletes a blog post.

****************/
require_once 'authenticate.php'; 

require 'database_connect.php';

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($post_id <= 0) {
    header('Location: index.php');
    exit;
}

try {
    $sql = "DELETE FROM Posts WHERE id = :post_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':post_id', $post_id);

    if ($stmt->execute()) {
        header('Location: index.php');
        exit;
    } else {
        echo '<p>Error deleting post.</p>';
    }
} catch (PDOException $e) {
    echo '<p>Error: ' . $e->getMessage() . '</p>';
}
?>
