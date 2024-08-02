<?php
session_start();
require 'database.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
// checking if the form was submitted using the POST method. If not, the rest of the code inside this block will not execute
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the username already exists
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
    if (!$stmt) {
        echo "Database error: " . $mysqli->error;
        exit;
    }

    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = 'User already exists!';
        errorHandle($error);
    } else {
        // Username doesn't exist, proceed with registration
        // making sure we store salted, hashed passwords in the database
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $mysqli->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
        if (!$stmt) {
            echo "Database error: " . $mysqli->error;
            exit;
        }
        $stmt->bind_param('ss', $username, $password_hash);

        if ($stmt->execute()) {
            // Successful registration
            $_SESSION["confirm"] = 'User has been added';

            // Redirect to login page after successful registration
            header('Location: login.php');
            exit();
        } else {
            $error = 'Error: Cannot add user';
            errorHandle($error);
        }
    }

    // Close the statement
    $stmt->close();
}

// Error handling function
function errorHandle($err) {
    if (isset($err)) {
        echo "<h1 style='color:red;'>$err</h1>";
        header('Refresh: 2; url=login.php');
        exit();
    }
}
?>