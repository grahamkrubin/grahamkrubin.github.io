<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="allstyles.css">
        <title>Reddit but Better</title>
        
    </head>
    <body>
        <h1>Clickbait Today</h1>
        <?php
        session_start();
        //if not logged, in show the log in button and search bar only
        if(!isset($_SESSION['loggedin'])||$_SESSION['loggedin']==0){
            echo "<form action='http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/login_main.html'><input type='submit' value='Log in'></form>";
            echo "<form action='search_result.php' method='post'>
                        <input type='text' name='searchquery'>
                        <input type='submit' value='Search!'/>
                    </form>";
            }
        else{ //otherwise, show only search bar, user page, submit, logout buttons
            echo "<form action='search_result.php' method='post'>
                        <input type='text' name='searchquery'>
                        <input type='submit' value='Search!'/>
                    </form>";
            echo "<br><form name='user_page' action='http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/user_page.php'><input type='submit' value='User Page'/></form>";
            echo "<br><form name='new_user_form' action='http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/story_submission.php'><input type='submit' value='Submit a story'/></form>";
            echo "<form name='logout' action='logout.php'><input type='submit' value='Logout'/></form>";
        }
        
        require 'userdatabase.php';
        echo "<br>";
        //showing all the stories on main page
        $story = $mysqli->prepare("select story_title, story_link, story_content, author_id, date_sub, story_id from stories");
        $story->execute();
        $story->bind_result($title, $link, $content, $author_id, $time_written, $story_id);
        
        while($story->fetch()) {
            echo $title . "<form action='view_story.php' method='post'> <input type='submit' name='id[$story_id]' value='$title'> </form>";
            echo "<br>";
        }
        $story->close();
        
        
        ?>
       </body> 
        
</html>
    
