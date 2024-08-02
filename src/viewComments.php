<!DOCTYPE html>

<html lang="en">
  
  <head>
    <title>View Comments</title>
    <link rel="stylesheet" type="text/css" href="stylesheet/comments.css?ts=<?=time()?>">
  </head>

  <body>
    <?php
        session_start();
        require 'database.php';

        $is_loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'];

        // output the full story post
        if (isset($_GET["id"])){
            $story_id_url = intval($_GET["id"]);
            
            $stmt = $mysqli->prepare("SELECT title, body, link FROM stories 
                                    WHERE id=?; ");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt->bind_param('i', $story_id_url) ;
            $stmt->execute();
            $result = $stmt->get_result();

            while($row = $result->fetch_assoc()){
                echo('<h1>'.htmlspecialchars_decode(($row['title'])).'</h1>');
                echo('<div class="full-post-container">');
                    echo('<p>'.htmlspecialchars_decode(($row['body'])).'</p>');
                    echo('<a href="'.htmlspecialchars_decode(($row['link'])).'">Article Link</a>');
                echo('</div>');
            }
        }
        
        // add a comment
        if ($is_loggedin){
            echo('<div>
                    <form action="createComments.php?id='.(int) $story_id_url.'" class="add-comment" method="POST">
                        
                        <label for="tbox">New Comment:</label><textarea type="text" class="tbox" name="new-comment" required></textarea>
                        <input type="submit" class="a-button" value="Add">
                    </form>
                  </div>');
        }
    
        // show comments
        echo('<h3>Comments</h3>');
        
        if (isset($_GET["id"])){
            $stmt = $mysqli->prepare("SELECT comment, users.username, user_id, comments.id, story_id FROM comments 
                                    JOIN users ON (comments.user_id=users.id) 
                                    WHERE story_id=? ORDER BY comments.id DESC; ");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt->bind_param('i', $story_id_url) ;
            $stmt->execute();
            $result = $stmt->get_result();

            while($row = $result->fetch_assoc()){
                echo('<div class="full-post-container">
                        <p class="show-comment-user">'.htmlspecialchars($row["username"]).': </p>
                        <span class="show-comment-text">'.htmlspecialchars($row["comment"]).' </span>');

                if ($is_loggedin && $row["user_id"] == $_SESSION['user_id']){
                    echo('<div>
                            <form action="editComments.php?id='.(int) $story_id_url.'" method="POST">
                                <input type="hidden" name="comment-id" value="'.(int) $row["id"].'">
                                <input type="submit" formaction="editComments.php?id='.(int) $story_id_url.'" class="link-button" value="edit">
                                <input type="submit" formaction="deleteComments.php?id='.(int) $story_id_url.'" class="link-button" value="delete">
                            </form>
                        </div>');
                }
            }
        }
    ?>

  </body>

</html>