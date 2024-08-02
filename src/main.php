<!DOCTYPE html>

<html lang="en">
    
    <head>
        <title>News Website</title>
        <link rel="stylesheet" type="text/css" href="stylesheet/main.css?ts=<?=time()?>">
    </head>
    
    <body>
        <?php 
        session_start();
        require 'database.php';
        // due to mutliple usages, declared in variable here:
        $is_loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'];
        ?>
        
        <!-- Change user status bar based on logged in status -->
        <div class="user-info">
            <div class="username"> 
                <?php
                if (isset($_SESSION['username'])){
                    echo('<h3> Welcome, <a href="profile.php">'.htmlspecialchars($_SESSION['username']).'</a>!</h3>'); 
                } else {
                    echo('<h5> Log in to comment and submit stories </h5>');
                }
                ?>
                </div>
                <div class="site-name">
                    <h1>Newest Stories</h1>
                </div>
                <div class="log-status">
            <?php
            if ($is_loggedin){
                echo('<a class="link" href="logout.php">Logout</a>');
            } else {
                echo('<a class="link" href="login.php">Login Here</a>');
            }
            ?>
            </div>
        </div>
        
        <!-- Display the newest stories uploaded -->
        <div class="reads-container">
            <?php
                $stmt = $mysqli->prepare("SELECT title, body, link, users.username, user_id, stories.id FROM stories 
                                        JOIN users ON (stories.user_id=users.id) ORDER BY stories.id DESC;");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
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
        ?>
    </div>

    <!-- Toolbar that allows for story posting/deletion -->
    <?php
        // check if user is logged in for toolbar access 
        if ($is_loggedin){
        echo('<div class="user-tool">
                    <div class="create">
                        <a class="a-button" href="createStory.php">Create a story</a>
                    </div>
                    <div class="search">
                        <a class="a-button" href="searchStory.php">Search for a story</a>
                    </div>
              </div>');
        }
    ?>
    
    </body>
</html>