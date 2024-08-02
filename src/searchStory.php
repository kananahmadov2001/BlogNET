<!DOCTYPE html>

<html lang="en">

  <head>
    <meta charset="UTF-8">
    <title>Search for A Story</title>
    <link rel="stylesheet" type="text/css" href="stylesheet/stories.css?ts=<?=time()?>">
  </head>
  
  <body>
    <h1>Search Here:</h1>
    <div class="edit-story-form-container">
      <form class="edit-story-form" action="searchStory.php" method="POST">
        <!-- <input type="hidden" name="token" value="<?php // echo $_SESSION['token']; ?>"> -->
        <!-- search params -->
        Title: <input type="text" name="search_title">
        Author: <input type="text" name="search_author">
        <input type="submit" value="Search">
      </form>
    </div>
    
    <h3>Results Below:</h3>
    <!-- Display the newest stories uploaded -->
    <div class="reads-container">
        <?php
        session_start();
        require 'database.php';
        $is_loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'];
            
        // generating CSRF token if not already set
        // $_SESSION['token'] = bin2hex(random_bytes(32)); // generate a 32-byte random string

        // handling GET request to display the edit form
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // validating the CSRF token to prevent CSRF attacks
        // if (!hash_equals($_SESSION['token'], $_POST['token'])) {
        //   die("Request forgery detected");
        // }    

            // if the search is empty, output all
            if(!isset($_POST["search_title"]) || !isset($_POST["search_author"])){
                $stmt = $mysqli->prepare("SELECT title, body, link, users.username, user_id, stories.id FROM stories 
                                        JOIN users ON (stories.user_id=users.id) ORDER BY stories.id;");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
            } else {
                $stmt = $mysqli->prepare("SELECT title, body, link, users.username, user_id, stories.id FROM stories 
                                        JOIN users ON (stories.user_id=users.id) WHERE title LIKE ? AND username LIKE ?
                                        ORDER BY stories.id");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                
                $term_title = '%'.$_POST["search_title"].'%';
                $term_author = '%'.$_POST["search_author"].'%';
                $stmt->bind_param('ss', $term_title, $term_author);
            }
                $stmt->execute();
                $result = $stmt->get_result();

                // view story title, author, comment section and edit (if it's your own story)
                while($row = $result->fetch_assoc()){
                    echo('<div class="reads">');
                        // title and author
                        echo('<div> 
                                <a class="title" href="'.htmlspecialchars($row["link"]).'">'.htmlspecialchars($row["title"]).'</a>
                                <span class="author">posted by '.htmlspecialchars($row["username"]).'</span>
                            </div>');
                        // comment
                        echo('<div>
                                <form action="viewComments.php?id='.$row["id"].'" method="POST">
                                    <input type="hidden" name="comment-user-id"  value="'.(int) ($row["user_id"]).'">
                                    <input type="submit" class="link-button" value="view full post and comments">
                                </form>
                            </div>');
                        // edit
                        if ($is_loggedin && $row["user_id"] === $_SESSION['user_id']){
                            echo('<div>
                                    <form action="editStory.php" method="POST">
                                        <input type="hidden" name="story-id" value="'.(int) $row["id"].'">
                                        <input type="submit" formaction="editStory.php" class="link-button" value="edit">
                                        <input type="submit" formaction="deleteStory.php" class="link-button" value="delete">
                                    </form>
                                </div>');
                        }
                    echo('</div>');
                }
        }
        ?>    
    </div>

  </body>
</html>