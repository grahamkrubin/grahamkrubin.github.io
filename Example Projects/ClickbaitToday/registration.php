<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Search Result</title>
        <link rel="stylesheet" href="allstyles.css">
        <!-- -->
    </head>
    <body>
    <?php
    require 'userdatabase.php';
    
    $username = (string)$_POST['newuser'];
    $userfind = $mysqli->prepare("SELECT COUNT(*) FROM userinfo WHERE username = ?");
    if(!$userfind){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $userfind->bind_param('s', $username);
    $userfind->execute();
    $userfind->bind_result($usercount);
    $userfind->fetch();
    $userfind->close();
    //checking if user already exists, need to have distinct usernames
    if($usercount>0){
        echo "USER ALREADY EXISTS!";
    }
    else{ //otherwise make new user, and new password with given parameters
        $unhashed = (string)$_POST['password'];
        $fullhash = password_hash($unhashed, PASSWORD_BCRYPT);
        $passarray = explode("$", $fullhash);
        $length = $passarray[2];
        $passhash = $passarray[3];
        
        $stmt = $mysqli->prepare(
            "insert into userinfo (username, passhash, length) values (?, ?, ?)"
        );
       
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        
        $stmt->bind_param('sss', $username, $fullhash, $length);
    
        $stmt->execute();
    
        $stmt->close();
    }
    echo "<form action='http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/main_news_page.php'>
                <input type='submit' value='Go Home!'>
        </form>";
?>
 </body> 
        
</html>