<!doctype html>
	<html lang="en">
		<head>
			<meta charset="utf-8">
			<title>Story Viewer</title>
			<link rel="stylesheet" href="allstyles.css">
		</head>
		<body>
			<?php
			session_start();
			$current_user;
            if(isset($_SESSION['loggedin'])&&$_SESSION['loggedin']==1){
                $current_user = (int)$_SESSION['user_id'];
            }
			require 'userdatabase.php';
			
			foreach($_POST['id'] as $key => $value);
			//showing the story, and all components
			$story = $mysqli->prepare("select story_title, story_link, story_content, author_id, date_sub, story_id, userinfo.username from stories join userinfo on userinfo.userid=author_id where story_id = ?");
                    if(!$story){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                    $story->bind_param('s',$key);
					$story->execute();
					$story->bind_result($title, $link, $content, $author_id, $time_written, $story_id, $username);
			$story->fetch();
            $_SESSION['story_id']=$story_id;
            $_SESSION['link']=$link;
            $story->close();
            if($link==null){
                $link = "http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/emptylink.php";
            }

			?>
            
			<h1><?=$title?></h1>
			<p>
				<?=$content?>
                <br>
                <!-- <form action='http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/openlink.php'>
                    <input type="submit" value='<?=$link?>'/>
                </form> -->
                <a href='<?=$link?>'><?=$link?></a>
                <h4>By: <?=$username?></h4>
			</p>
            <!-- ALL THE COMMENTS STUFF UNDER HERE-->
            
			<?php  
                if(isset($current_user)&&$current_user==$author_id){ //deleting/editing story with correct buttons
                    echo "<form action = 'delete_story.php' method='post'><input type='submit' name='delstory'value='DELETE STORY'><input type='hidden' name='token' value='".$_SESSION['token']."' /></form>";
                    echo "<form action = 'edit_story.php' method='post'><input type='submit' name='editstory'value='EDIT STORY'><input type='hidden' name='token' value='".$_SESSION['token']."' /></form>";
                    echo "<br><br>";
                }
                echo "<h3>COMMENTS:</h3>";
                $stmt = $mysqli->prepare("SELECT content, story_id, user_id, comment_id, userinfo.username FROM comments JOIN userinfo on user_id=userinfo.userid WHERE story_id = ?;");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                $stmt->bind_param('s',$story_id);
                $stmt->execute();
                $stmt->bind_result($comment_content, $story_id, $user_id, $comment_id, $commenter);
                while($stmt->fetch()){
                    echo $comment_content;
                    echo "<br>";
                    echo "By: ".$commenter;
                    echo "<br>";
                    if(isset($current_user)&&$user_id == $current_user){//deleting/editing comments with correct buttons
                        echo "<form action='edit_comment.php' method='post'> <input type='submit' name='edit[$comment_id]' value='edit comment'><input type='hidden' name='token' value='".$_SESSION['token']."' /></form>";
                        echo "<form action='delete_comment.php' method='post'> <input type='submit' name='del[$comment_id]' value='delete comment'><input type='hidden' name='token' value='".$_SESSION['token']."' /></form>";
                    }
                }
			$stmt->close();
			?>
        <form name='add_comment' id='add_comment'action='add_comment.php' method='post'>
            <textarea name='commentbox' id='commentbox' form='add_comment' rows='4'columns='40' placeholder="Write comment here...">
            </textarea>
            <br>
            <input type="submit" name="commentsubmit" value="Submit comment">
        </form>
		<form action='http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/main_news_page.php'>
                <input type='submit' value='Go Home!'>
        </form>
		</body>
	</html>


