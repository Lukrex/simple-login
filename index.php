<?php
session_start(); //needed at start of php
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    //XAMPP default name is root and no password
    $conn = new PDO('mysql:host=localhost;dbname=passcheck', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $max_attempts = 6;
    $lockout_time = 15; //minutes
    $user_ip = $_SERVER['REMOTE_ADDR'];

    if (isset($_GET['login']) && $_GET['login']) { //if login submitted
        //cross-site request forgery token validation
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die('CSRF token validation failed');
        }

        //check lockout status for rate limiting
        $stmt = $conn->prepare("SELECT attempts, last_attempt FROM login_attempts WHERE ip_address = ?");
        $stmt->execute([$user_ip]);
        $attempt_data = $stmt->fetch();

        if ($attempt_data) {
            $last_try = new DateTime($attempt_data['last_attempt']);
            $now = new DateTime();
            $diff = $now->diff($last_try);
            $minutes_passed = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;

            if ($attempt_data['attempts'] >= $max_attempts && $minutes_passed < $lockout_time) {
                $wait = $lockout_time - $minutes_passed;
                die("Too many failed attempts. Please wait $wait minutes.");
            }
        }

        $username = trim($_POST['username']);
        $pass = trim($_POST['passwd']);

        $statement = $conn->prepare("SELECT * FROM userinfo WHERE username = :username");
        $statement->execute(array('username' => $username));
        $user = $statement->fetch();  //false if non-existent

        // echo "<pre>"; 
        // print_r($user); //this shows what the DB found
        // echo "</pre>";

        if ($user !== false && password_verify($pass, $user['password'])) { //password_verify() for password_hash() using BCRYPT
            $stmt = $conn->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
            $stmt->execute([$user_ip]);

            session_regenerate_id(true); //generates a new random ID
            $_SESSION['user_id'] = $user['id'];
            $message = "Login success<br>";
        } else {
            //insert ip into table if not there, increment attempts if ip there
            $stmt = $conn->prepare("INSERT INTO login_attempts (ip_address, attempts, last_attempt) 
                                    VALUES (?, 1, NOW()) 
                                    ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt = NOW()");
            $stmt->execute([$user_ip]);

            $message = "Username or password is incorrect<br>";
        }
    } 
} catch (PDOException $e) {
    echo "Connection failed.";
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
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?>">
        <label for="username">Username: </label>
        <input name="username" id="username" placeholder="Username" maxlength=64>

        <label for="passwd">Password: </label>
        <input type="password" name="passwd" id="passwd" placeholder="Password" maxlength=64>

        <input type="submit" id="submitBtn" value="Submit"><br>
        <?php 
            if(isset($message)) { echo $message; }
        ?>
    </form>
</body>
</html>