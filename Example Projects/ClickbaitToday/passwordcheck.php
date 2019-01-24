<html lang="en">
    <head>
         <meta charset="utf-8">
         <link rel="stylesheet" href="allstyles.css">
        <title>Login</title>
    </head>
    <body>
        <?php
            require 'userdatabase.php';
            
            $stmt = $mysqli->prepare(
                "SELECT COUNT(*), username, userid, passhash FROM userinfo WHERE username=?"
            );
            
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            
            $stmt->bind_param('s', $username);
            $username = (string)$_POST['username'];
            $stmt->execute();
            
            $stmt->bind_result($cnt, $user, $user_id, $pwd_hash);
            $stmt->fetch();
            
            $pwd_guess = (string)$_POST['password'];
            
            
            if($cnt == 1 && password_verify($pwd_guess, $pwd_hash)){
            // Login succeeded!
            echo "logged in";
            session_start();
            $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32)); // generate a 32-byte random string
            $_SESSION['user_id'] = (int)$user_id;
            $_SESSION['loggedin'] = 1;
            $_SESSION['username']= (string)$user;
            // Redirect to home page
            header('Location: http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/main_news_page.php');
            }
            else if($cnt == 1){
                echo "Invalid Password";
                echo "<form action='http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/main_news_page.php'>
                <input type='submit' value='Go Home!'>
                 </form>";
            // Login failed; redirect back to the login screen if invalid password
            }
            else {
                //uer not found, if cnt is 0
                echo "User not found.";
                echo "<form action='http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/main_news_page.php'>
                <input type='submit' value='Go Home!'>
                 </form>";
            }
            $stmt->close();
        ?>
    </body>
    
</html>
