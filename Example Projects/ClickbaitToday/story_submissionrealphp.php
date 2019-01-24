<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="allstyles.css">
        <title>story submitting page</title>
    </head>
    <body>
    <?php
    
    require 'userdatabase.php';
    session_start();
    if(!hash_equals($_SESSION['token'], $_POST['token'])){
	die("Request forgery detected");
    }
    $story = (string)$_POST['storybox'];
    $title = (string)$_POST['story_title'];
    $link = (string)$_POST['link'];
    $user_id = (int)$_SESSION['user_id'];
    
    //inserting the actual stories into correct place.
    $upload = $mysqli->prepare("insert into stories (story_title, story_link, story_content, author_id) values(?, ?, ?, ?)");
    if (!$upload) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $upload->bind_param('ssss', $title, $link, $story, $user_id);
    $upload->execute();
    $upload->close();
    echo "<form action='http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/main_news_page.php'>
                <input type='submit' value='Go Home!'>
            </form>";
    
    ?>
    </body>
</html>
    
