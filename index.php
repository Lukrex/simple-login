<?php
session_start(); //needed at start of php

//XAMPP default name is root and no password
$conn = new PDO ('mysql:host=localhost,dbname=passcheck', 'root', '')

if (isset($_GET['login'])) //if login submitted
{
    $name = $_POST['username'];
    $pass = $_POST['passwd'];

    $statement = $pdo->prepare("SELECT * FROM userinfo WHERE name = ".$name);
    $result = $statement->execute(array('name' => $name));
    $user = $statement->fetch();

     if ($user !== false && password_verify($pass, $user['password'])) {
        die('Login successful!');
        $message = "Login success"
    } else {
        $message = "Username or password is incorrect<br>";
    }
}

if (isset())

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

        <input type="submit" id="submitBtn" value="Submit">
    </form>
    <?php 
        if(isset($message)) { echo $message; }
    ?>
</body>
</html>