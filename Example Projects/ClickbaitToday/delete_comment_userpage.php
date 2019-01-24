<!doctype html>
	<html lang="en">
		<head>
			<meta charset="utf-8">
			<link rel="stylesheet" href="allstyles.css">
			<title>Comment Deleting</title>
		</head>
		<body>
			<?php
			session_start();
			if(!hash_equals($_SESSION['token'], $_POST['token'])){
			die("Request forgery detected");
			}
			//needs to find correct key (passed as del variable) for the button pressed
            foreach($_POST['del'] as $key => $value);
			$current_user = (int)$_SESSION['user_id'];
            $comment_id = $key;
			require 'userdatabase.php';
			//deletes the correct one, then gives you choice to go back through home button
			$story1 = $mysqli->prepare("DELETE from comments where comment_id =?");
                    if(!$story1){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                    $story1->bind_param('s', $comment_id);
					$story1->execute();
                       
            $story1->close();
			?>
			<h1>COMMENT DELETED!</h1>
            <form action='http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/main_news_page.php'>
                <input type='submit' value='Go Home!'>
            </form>
		</body>
	</html>


