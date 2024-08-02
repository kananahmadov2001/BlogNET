<html lang="en">
  
  <head>
    <title>Dual Degree Daily- All</title>
    <link rel="stylesheet" type="text/css" href="stylesheet/stories.css">
  </head>
  
  <body>
    <h1>All Stories</h1>
    <div class="all-reads-container">
        <?php 
            session_start();
            require 'database.php'; 

            $stmt = $mysqli->prepare("SELECT `title`,`body`,`link`,`users`.`username` FROM `stories` 
                                        JOIN `users` ON (stories.user_id=users.id) ORDER BY `stories`.`id` 
                                        DESC");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt->execute();
            $stmt->bind_result($title, $body, $link, $author);
            
            while($stmt->fetch()){
                echo('<div class="all-reads">');
                    echo('<div>
                        <a class="title" href="'.$link.'">'.$title.'</a>
                        <span class="author">posted by '.$author.'</span>
                    </div>');
                    // echo('<div class="body">'.$body.'</div>');
                    // echo('<div class="link"></div>'); change to view comments
                echo('</div>');
            }
        ?>
    </div>
    