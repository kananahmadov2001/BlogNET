<!DOCTYPE html>

<html lang="en">

  <head>
    <meta charset="UTF-8">
    <title>Edit Story</title>
    <link rel="stylesheet" type="text/css" href="stylesheet/stories.css">
  </head>
  
  <body>
    <h1>Edit Your Story Here:</h1>
    <div class="edit-story-form-container">
      <form class="edit-story-form" action="editStory.php" method="POST">
        <input type="hidden" name="new_story_id" value="<?php echo htmlspecialchars($_POST['story-id']); ?>">
        <!-- <input type="hidden" name="token" value="<?php // echo htmlspecialchars($_SESSION['token']); ?>"> -->
        Title: <input type="text" name="new_title" required>
        Body:  <input type="text" name="new_body" required>
        Link:  <input type="text" name="new_link" required>
        <!-- <input type="hidden" name="token" value="<?php // echo $_SESSION['token']; ?>"> -->
        <input type="submit" value="Edit Story">
      </form>
    </div>
    
    <?php
    session_start();
    require 'database.php';
    include 'moderator.php';
    // generating CSRF token if not already set
    //if (isset($_POST['token'])) {
    //  echo("print");
    //} else {
     // echo ("no token");
    //}

    // handling GET request to display the edit form
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // validating the CSRF token to prevent CSRF attacks
      //if (!hash_equals($_SESSION['token'], $_POST['token'])) {
       // die("Request forgery detected");
      //}

      if (isset($_SESSION['user_id']) && isset($_POST['new_story_id']) && isset($_POST['new_title']) && isset($_POST['new_body']) && isset($_POST['new_link'])) {
        $story_id = $_POST['new_story_id'];
        $new_title = $_POST['new_title'];
        $new_body = $_POST['new_body'];
        $new_link = $_POST['new_link'];
        $user_id = $_SESSION['user_id'];
        
        // preparing a SQL statement for the user ID of the story's owner
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
        
        // checking if the logged-in user is the owner of the story
        if ($user_id == $story_user_id) {

          // apply moderator filter to title, body, and link (not likely but there regardless) 
          $censored_title = moderate_word(explode(" ", $new_title));
          $censored_body = moderate_word(explode(" ", $new_body));
          $censored_link = moderate_word(explode(" ", $new_link));

          // Update the story
          $stmt = $mysqli->prepare("UPDATE stories SET title=?, body=?, link=? WHERE id=?");
          if (!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
          }
          
          $stmt->bind_param('sssi', $censored_title, $censored_body, $censored_link, $story_id);
          if ($stmt->execute()) {
            echo "Story Updated!";
            header("Location: main.php");
            exit();
          } else {
            echo "Error in Updating Story: " . $stmt->error;
          }
          $stmt->close();
        
        } else {
          echo "No permission to edit this story!";
        }
      }
    }
    ?>
  </body>
</html>