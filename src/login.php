<!DOCTYPE html>

<html lang="en">
  
  <head>
    <title>Login Page</title>
    <link rel="stylesheet" type="text/css" href="stylesheet/login.css">
  </head>
  
  <body>
      <div class="login-box">
        <div class="login-header">
          <h2>Sign In</h2>
        </div>
        <div class="login-avatar">
          <img src="pics/rounded.webp" alt="User Avatar">
        </div>
        <form action="login.php" method="POST">
          <div class="input-group">
            <label for="username"></label>
            <input type="text" id="username" name="username" placeholder="Username" required>
            <label for="password"></label>
            <input type="Password" id="password" name="password" placeholder="Password" required>
          </div>
          <button type="submit" formaction="login.php">Log In</button>
          <button type="submit" formaction="register.php">Register</button>
        </form>
        <form action="main.php">
          <button type="submit" formaction="main.php">Continue as Guest</button>
        </form>
      </div>

  <?php
    session_start();
    require 'database.php';

    $error = '';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $username = $_POST['username'];
      $password = $_POST['password'];
      
      $stmt = $mysqli->prepare("SELECT id, password_hash FROM users WHERE username = ?");
      if (!$stmt) {
        echo "Database error: " . $mysqli->error;
        exit;
      }
      
      $stmt->bind_param('s', $username);
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($id, $password_hash);
      $stmt->fetch();

      // Verify the password
      // using password_verify() function to check a password securely
      if ($stmt->num_rows > 0 && password_verify($password, $password_hash)) {
        
        // Successful login
        
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        $_SESSION['loggedin'] = true;

        // clear confirm session variable if logging in
        unset($_SESSION['confirm']);
        
        header("Location: main.php");
        exit();
      } else {
        $error = "ERROR: Invalid username";
        errorHandle($error);
      }

      // Close the statement
      $stmt->close();
    }
      
    function errorHandle($err){
      if (isset($err)) {
        echo "<p style='color:red;'>$err</p>"; 
      }
    }

    if (isset($_SESSION['confirm'])) {
      echo "<h3 style='color:#03fc2c;'>".$_SESSION['confirm']."</h3>";
    }
    ?>
  </body>
  
</html>