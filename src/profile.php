<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $bio = $_POST['bio'];
  $social_links = $_POST['social_links'];
  
  // Update profile in the database
  $stmt = $mysqli->prepare("UPDATE users SET bio=?, social_links=? WHERE id=?");
  $stmt->bind_param('ssi', $bio, $social_links, $user_id);
  $stmt->execute();
  $stmt->close();
  
  header("Location: profile.php");
  exit();
}

// Fetch user information
$stmt = $mysqli->prepare("SELECT username, bio, social_links FROM users WHERE id=?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($username, $bio, $social_links);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>User Profile</title>
  <link rel="stylesheet" href="stylesheet/profile.css">
</head>

<body>
  <div class="profile-container">
    <form class="profile-form" action="profile.php" method="POST">
      <h2><?php echo htmlspecialchars($username); ?>'s Profile</h2>
      <div class="input-group">
        <label for="bio">Bio:</label>
        <textarea id="bio" name="bio"><?php echo htmlspecialchars($bio); ?></textarea>
      </div>
      <div class="input-group">
        <label for="social_links">Social Links:</label>
        <input type="text" id="social_links" name="social_links" value="<?php echo htmlspecialchars($social_links); ?>">
      </div>
      <button type="submit">Update Profile</button>
    </form>
  </div>
</body>

</html>