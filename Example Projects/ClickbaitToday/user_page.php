<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>User Page</title>
		<link rel="stylesheet" href="allstyles.css">
	</head>

<!-- stories, comments, user info-->

<body>
	<?php
    session_start();
	require 'userdatabase.php';
	$user_id = (int)$_SESSION['user_id'];
    $username = (string)$_SESSION['username'];
	echo "hello ".$username."! Welcome to your user page.";
    echo "<br>";
	echo "<br><h3>STORIES:</h3>";
    $story_titles = $mysqli->prepare("select story_title, story_id from stories where author_id = ?");
    if(!$story_titles){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
    $story_titles->bind_param('s', $user_id);
    $story_titles->execute();
    $story_titles->bind_result($title, $story_id);
    while ($story_titles->fetch()) {
		//show all the stories, with correct buttons for each
        echo $title;
        echo "<form action='view_story.php' method='post'><input type='submit' name='id[$story_id]' value='VIEW'></form>";
        echo "<form action = 'delete_story_userpage.php' method='post'><input type='submit' name='del[$story_id]'value='DELETE STORY'><input type='hidden' name='token' value='".$_SESSION['token']."' /></form>";
        echo "<form action = 'edit_story_userpage.php' method='post'><input type='submit' name='edit[$story_id]'value='EDIT STORY'><input type='hidden' name='token' value='".$_SESSION['token']."' /></form>";
    }
	
	//COMMENTS SECTION~~~~~~~~~~~~~~
	echo "<br><h3>COMMENTS:</h3>";
	$comments_all = $mysqli->prepare("select content, comment_id from comments where user_id = ?");
	if(!$comments_all){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
	$comments_all->bind_param('s', $user_id);
    $comments_all->execute();
    $comments_all->bind_result($comment, $comment_id);
	while ($comments_all->fetch()) {
		//show all the comments with correct buttons for each
        echo $comment;
        echo "<form action = 'delete_comment_userpage.php' method='post'><input type='submit' name='del[$comment_id]'value='DELETE COMMENT'><input type='hidden' name='token' value='".$_SESSION['token']."' /></form>";
        echo "<form action = 'edit_comment_userpage.php' method='post'><input type='submit' name='edit[$comment_id]'value='EDIT COMMENT'><input type='hidden' name='token' value='".$_SESSION['token']."' /></form>";
    }
	
    echo "<br><form name='change_password' action=http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/password_change.html><input type='submit' value='Change Password'/></form>";
	echo "<form action='http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/main_news_page.php'>
                <input type='submit' value='Go Home!'>
            </form>";


	?>
</body>
</html>
