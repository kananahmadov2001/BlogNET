<?php
session_start();
require 'database.php';

// generating CSRF token if not already set
// $_SESSION['token'] = bin2hex(random_bytes(32)); // generate a 32-byte random string

// handling GET request to display the edit form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // validating the CSRF token to prevent CSRF attacks
  //if (!hash_equals($_SESSION['token'], $_POST['token'])) {
   // die("Request forgery detected");
  //}
  
  if (isset($_SESSION['user_id']) && isset($_POST['comment-id'])) {
    $comment_id = $_POST['comment-id'];
    $user_id = $_SESSION['user_id'];
    $story_id_url = intval($_GET['id']);

    // Prepare a SQL statement to check the user ID of the comments's owner
    $stmt = $mysqli->prepare("SELECT user_id FROM comments WHERE id=?");
    if (!$stmt) {
      printf("Query Prep Failed: %s\n", $mysqli->error);
      exit;
    }
    
    $stmt->bind_param('i', $comment_id);
    $stmt->execute();
    $stmt->bind_result($comment_user_id);
    $stmt->fetch();
    $stmt->close();
    
    // Check if the logged-in user is the owner of the story
    if ($user_id == $comment_user_id) {
      // Delete the story
      $stmt = $mysqli->prepare("DELETE FROM comments WHERE id=?");
      if (!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
      }
      
      $stmt->bind_param('i', $comment_id);
      if ($stmt->execute()) {
        echo "Comment Deleted!";
        header("Location: viewComments.php?id=$story_id_url");
        exit();
      } else {
        echo "Error in Deleting Comment: " . $stmt->error;
      }
      $stmt->close();
    } else {
      echo "No permission to delete this comment!";
    }
  }
}
?>