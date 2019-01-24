<!doctype html>
	<html lang="en">
		<head>
			<meta charset="utf-8">
			<link rel="stylesheet" href="allstyles.css">
			<title>Adding comment</title>
		</head>
		<body>
		<?php
		require 'userdatabase.php';
		session_start();
		
		if(isset($_SESSION['loggedin'])&&$_SESSION['loggedin']==1){
        $content = (string)$_POST['commentbox'];
        $user_id = (int)$_SESSION['user_id'];
        $story_id = (int)$_SESSION['story_id'];
        
        //putting the commentbox values into the corrent location in mysql comments table
        $upload = $mysqli->prepare("insert into comments (content, story_id, user_id) values(?, ?, ?)");
        if (!$upload) {
            echo "comment upload failed!";
        }
        $upload->bind_param('sss', $content, $story_id, $user_id);
        $upload->execute();
        $upload->close();
		//going home buttons
        echo "<form action='http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/main_news_page.php'><input type='submit' value='Go Home!'></form>";
    }
    else{
		//if not logged in, fails to add comment
        echo "You must log in to post a comment!";
        echo "<form action='http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/login_main.html'><input type='submit' value='Log in here!'></form>";
    }  
	?>
        </body>
	</html>

