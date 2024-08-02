<!DOCTYPE html>

<html lang="en">

  <head>
    <meta charset="UTF-8">
    <title>Edit Comment</title>
    <link rel="stylesheet" type="text/css" href="stylesheet/comments.css?ts=<?=time()?>">
  </head>
  
  <body>
    
    <form action="editComments.php?id=<?php echo(intval($_GET['id']))?>" method="POST">
      <!-- <input type="hidden" name="token" value="<?php // echo $_SESSION['token']; ?>"> -->
      <input type="hidden" name="new_comment_id" value="<?php echo($_POST['comment-id']); ?>" >
      Change Comment: <input type="text" name="new_comment" required>
      <input type="submit" value="Edit Comment">
    </form>
    
    <?php
    session_start();
    require 'database.php';
    include 'moderator.php'; 
    
    // generating CSRF token if not already set
    // $_SESSION['token'] = bin2hex(random_bytes(32)); // generate a 32-byte random string

    // generating CSRF token if not already set
   // if (isset($_POST['token'])) {
    //  echo("print");
    //} else {
    //  echo ("no token");
   // }

    // handling GET request to display the edit form
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // validating the CSRF token to prevent CSRF attacks
     // if (!hash_equals($_SESSION['token'], $_POST['token'])) {
      //  die("Request forgery detected");
     // }

      if (isset($_SESSION['user_id']) && isset($_POST['new_comment'])) {
        $comment_id = $_POST['new_comment_id'];
        $story_id_url = intval($_GET['id']);
        $user_id = $_SESSION['user_id'];        

        // preparing a SQL statement for the user ID of the comment's owner
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
        
        // checking if the logged-in user is the owner of the comment
        if ($user_id == $comment_user_id) {

          $censored_words = moderate_word(explode(" ", $_POST['new_comment']));

          // Update the comment
          $stmt = $mysqli->prepare("UPDATE comments SET comment=? WHERE id=?");
          if (!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
          }
          
          $stmt->bind_param('si', $censored_words, $comment_id);
          if ($stmt->execute()) {
            echo "Comment Updated!";
            
            header("Location: viewComments.php?id=$story_id_url");
            exit();
          } else {
            echo "Error in Updating Commment: " . $stmt->error;
          }
          $stmt->close();
        
        } else {
          echo "No permission to edit this comment!";
        }
      }
    }
    ?>
  </body>
</html>