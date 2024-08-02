<!DOCTYPE html>

<html lang="en">
  
  <head>
    <title>Add a New Story</title>
    <link rel="stylesheet" type="text/css" href="stylesheet/stories.css">
  </head>
  
  <body>

    <h1>Add Your Story Here:</h1>
    <div class="new-story-form-container">
      <form action="createStory.php" method="POST">
        <!-- <input type="hidden" name="token" value="<?php // echo htmlspecialchars($_SESSION['token']); ?>"> -->
        <input type="hidden" name="story_id" value="<?php echo htmlspecialchars($story_id); ?>">

        <div class="new-story-form">
          <label for="new-title">Story Title:</label>
          <input type="text" id="new-title" name="new-title">
        </div>  
        
        <div class="new-story-form">
          <label for="new-title">Story Info:</label>
          <input type="text" id="new-body" name="new-body">
        </div>

        <div class="new-story-form">
          <label for="new-title">Story Link:</label>
          <input type="text" id="new-link" name="new-link">
        </div> 

        <div class="new-story-form">
          <input class="a-button" type="submit" value="Submit">        
        </div>  

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
      //  echo ("no token");
      //}
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
      //  if (!hash_equals($_SESSION['token'], $_POST['token'])) {
        //  die("Request forgery detected");
      //  }
        
      $id = $_SESSION['user_id'];
      $title = $_POST['new-title'];
      $body = $_POST['new-body'];
      $link = $_POST['new-link'];

      // apply moderator filter to title, body, and link (not likely but there regardless) 
      $censored_title = moderate_word(explode(" ", $title));
      $censored_body = moderate_word(explode(" ", $body));
      $censored_link = moderate_word(explode(" ", $link));

      $stmt = $mysqli->prepare("insert into stories (user_id, title, body, link) 
                                values (?, ?, ?, ?)");
      if(!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
      }

      $stmt->bind_param('isss', $id, $censored_title, $censored_body, $censored_link);

      $stmt->execute();

        $stmt->close();
      
        header("Location: main.php");
        exit();
      }
    ?>

  </body>

</html>

    