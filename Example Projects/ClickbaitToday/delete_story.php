<!doctype html>
	<html lang="en">
		<head>
			<meta charset="utf-8">
			<link rel="stylesheet" href="allstyles.css">
			<title>Story Deleting</title>
		</head>
		<body>
			<?php
			session_start();
			if(!hash_equals($_SESSION['token'], $_POST['token'])){
			die("Request forgery detected");
			}
			$current_user = (int)$_SESSION['user_id'];
            $story_id = (int)$_SESSION['story_id'];
			require 'userdatabase.php';
			
			//need to delete comments first, then story
			$story1 = $mysqli->prepare("DELETE from comments where story_id =?");
                    if(!$story1){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                    $story1->bind_param('s', $story_id);
					$story1->execute();
					$story1->close();
            //deleting story after    
			$story = $mysqli->prepare("DELETE from stories where story_id =?");
                    if(!$story){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                    $story->bind_param('s', $story_id);
					$story->execute();
                    
                    
            $story->close();
			?>
			<h1>STORY DELETED!</h1>
            <form action='http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/main_news_page.php'>
                <input type='submit' value='Go Home!'>
            </form>
		</body>
	</html>


