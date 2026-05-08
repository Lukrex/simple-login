<?php
session_start(); //needed at start of php

//XAMPP default name is root and no password
$conn = new PDO('mysql:host=localhost;dbname=passcheck', 'root', '');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['login'])) { //if login submitted
    $username = trim($_POST['username']);
    $pass = trim($_POST['passwd']);

    $statement = $conn->prepare("SELECT * FROM userinfo WHERE username = :username");
    $statement->execute(array('username' => $username));
    $user = $statement->fetch();  //false if non-existent

    // echo "<pre>"; 
    // print_r($user); // this shows what the DB found
    // echo "</pre>";

    if ($user !== false && password_verify($pass, $user['password'])) { //password_verify() for password_hash() using BCRYPT
        $message = "Login success<br>";
    } else {
        $message = "Username or password is incorrect<br>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Credentials</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <form method="POST" action="?login=1">
        <label for="username">Username: </label>
        <input name="username" id="username" placeholder="Username">

        <label for="passwd">Password: </label>
        <input type="password" name="passwd" id="passwd" placeholder="Password">

        <input type="submit" id="submitBtn" value="Submit"><br>
        <?php 
            if(isset($message)) { echo $message; }
        ?>
    </form>
</body>
</html>