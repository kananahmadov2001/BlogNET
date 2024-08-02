<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <title>Delete a Story</title>
    <link rel="stylesheet" type="text/css" href="stylesheet/stories.css">
  </head>

  <body>
    <div>
      <form action="deleteStory.php" method="post">
        <!-- <input type="hidden" name="token" value="<?php // echo $_SESSION['token']; ?>"> -->
        <input type="hidden" name="story_id" value="<?php echo htmlspecialchars($story_id); ?>">
        <input type="submit" value="Delete Story">
      </form>
    </div>

    <?php
    session_start();
    require 'database.php';

    // generating CSRF token if not already set
    // $_SESSION['token'] = bin2hex(random_bytes(32)); // generate a 32-byte random string
    // if (isset($_POST['token'])) {
    //   echo("print");
    // } else {
    //   echo ("no token");
    // }

    // handling GET request to display the edit form
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // validating the CSRF token to prevent CSRF attacks
      // if (!hash_equals($_SESSION['token'], $_POST['token'])) {
      //   die("Request forgery detected");
      // }
      
      if (isset($_SESSION['user_id']) && isset($_POST['story-id'])) {
        $story_id = $_POST['story-id'];
        $user_id = $_SESSION['user_id'];
        
        // Prepare a SQL statement to check the user ID of the story's owner
        $stmt = $mysqli->prepare("SELECT user_id FROM stories WHERE id=?");
        if (!$stmt) {
          printf("Query Prep Failed: %s\n", $mysqli->error);
          exit;
        }
        
        $stmt->bind_param('i', $story_id);
        $stmt->execute();
        $stmt->bind_result($story_user_id);
        $stmt->fetch();
        $stmt->close();
        
        // Check if the logged-in user is the owner of the story
        if ($user_id == $story_user_id) {
          // Delete the story
          $stmt = $mysqli->prepare("DELETE FROM comments WHERE story_id=?");
          
          if (!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
          }
          
          $stmt->bind_param('i', $story_id);
          $stmt->execute();
          
          $stmt = $mysqli->prepare("DELETE FROM stories WHERE id=?");
          if (!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
          }
          
          $stmt->bind_param('i', $story_id);
          if ($stmt->execute()) {
            echo "Story Deleted!";
            header("Location: main.php");
            exit();
          } else {
            echo "Error in Deleting Story: " . $stmt->error;
          }
          $stmt->close();
        } else {
          echo "No permission to delete this story!";
        }
      }
    }
    ?>
  </body>

</html>