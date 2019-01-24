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
			$current_user = (int)$_SESSION['user_id'];
			require 'userdatabase.php';
			//finds the correct key for the comment
			foreach($_POST['del'] as $key => $value);
			//deletes the correct comment, then redirects to main page
			$story = $mysqli->prepare("DELETE from comments where comment_id =?");
					if(!$story){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                    $story->bind_param('s', $key);
					$story->execute();
            $story->close();
			?>
			<h1>COMMENT DELETED!</h1>
            <?php
            header('Location: http://ec2-13-59-77-129.us-east-2.compute.amazonaws.com/~robertlandlord/module3/main_news_page.php')
			?>
		</body>
	</html>


