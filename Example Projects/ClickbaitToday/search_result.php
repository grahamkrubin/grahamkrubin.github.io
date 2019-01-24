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
        session_start();
        require 'userdatabase.php';
        $searchstring = (string)$_POST['searchquery'];
        echo "<h1>Searching for: ".$searchstring."</h1>";
        echo "<br>";
        echo "<h2>Found:</h2>";
        $titlearr = array();
        $story = $mysqli->prepare("select story_title, story_id from stories");
        if(!$story){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
        $story->execute();
        $story->bind_result($titlestring, $story_id);
        while($story->fetch()) {
            array_push($titlearr,array($titlestring,$story_id));
        }
        $story->close();
        //put all titles into a 2d array of story titles and story id's
        foreach($titlearr as $miniarr){
            if(strpos($miniarr[0],$searchstring) !== false){ //source: https://stackoverflow.com/questions/4366730/how-do-i-check-if-a-string-contains-a-specific-word
                echo $miniarr[0] . "<form action='view_story.php' method='post'> <input type='submit' name='id[$miniarr[1]]' value='$miniarr[0]'> </form>";
                echo "<br>";
            }
        }
    
        ?>
        <form action='http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/main_news_page.php'>
                <input type='submit' value='Go Home!'>
        </form>
       </body> 
        
</html>
    
