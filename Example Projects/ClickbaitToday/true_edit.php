<!doctype html>
	<html lang="en">
		<head>
			<meta charset="utf-8">
			<title>Comment Editing</title>
			<link rel="stylesheet" href="allstyles.css">
		</head>
		<body>
        <?php
            session_start();
			if(!hash_equals($_SESSION['token'], $_POST['token'])){
			die("Request forgery detected");
			}
			//note comment_key is not casted, because it depends
            $key = $_SESSION['comment_key'];
            require 'userdatabase.php';
			$editedcomment = (string)$_POST['editbox'];
			$story = $mysqli->prepare("UPDATE comments SET content = ? WHERE comment_id = ?");
                    if(!$story){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
					$story->bind_param('ss',$editedcomment, $key);
					$story->execute();
            $story->close();
        ?>
        <h1>COMMENT EDITED!</h1>
            <form action='http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/main_news_page.php'>
                <input type='submit' value='Go Home!'>
            </form>
        </body>
	</html>