<!DOCTYPE html>

<html lang="en">
  
  <head>
    <title>Add a New Comment</title>
    <link rel="stylesheet" type="text/css" href="stylesheet/stories.css?ts=<?=time()?>">
  </head>
  
  <body>

    <?php
        session_start();
        require 'database.php';
        include 'moderator.php'; 
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_SESSION['user_id'];
            $story_id = intval($_GET['id']);
            $comment = $_POST['new-comment'];
            
            $censored_words = moderate_word(explode(" ",$comment));

            $stmt = $mysqli->prepare("insert into comments (story_id, user_id, comment) 
                                        values (?, ?, ?)");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }

            $stmt->bind_param('iis', $story_id, $user_id, $censored_words);

            $stmt->execute();

            $stmt->close();
       
            header("Location: viewComments.php?id=$story_id");
            exit();
        }
    ?>

  </body>

</html>

    