<!doctype html>
	<html lang="en">
		<head>
			<meta charset="utf-8">
			<title>Story Editing</title>
			<link rel="stylesheet" href="allstyles.css">
		</head>
		<body>
        <?php
            session_start();
			if(!hash_equals($_SESSION['token'], $_POST['token'])){
			die("Request forgery detected");
			}
            $story_id = (int)$_SESSION['story_id'];
            require 'userdatabase.php';
			$editedcomment = (string)$_POST['editbox'];
			//update the story in the correct location to the new story
			$story = $mysqli->prepare("UPDATE stories SET story_content = ? WHERE story_id = ?");
                    if(!$story){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
					$story->bind_param('ss',$editedcomment, $story_id);
					$story->execute();
            $story->close();
        ?>
        <h1>STORY EDITED!</h1>
            <form action='http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/main_news_page.php'>
                <input type='submit' value='Go Home!'>
            </form>
        </body>
	</html>