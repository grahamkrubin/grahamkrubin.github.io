<!doctype html>
	<html lang="en">
		<head>
			<meta charset="utf-8">
			<link rel="stylesheet" href="allstyles.css">
			<title>Story Editing</title>
		</head>
		<body>
			<?php
			session_start();
			if(!hash_equals($_SESSION['token'], $_POST['token'])){
			die("Request forgery detected");
			}
            require 'userdatabase.php';
			$current_user = (int)$_SESSION['user_id'];
            $story_id = (int)$_SESSION['story_id'];
            $stmt = $mysqli->prepare("SELECT story_content FROM stories WHERE story_id = ?");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                $stmt->bind_param('s', $story_id);
                $stmt->execute();
                $stmt->bind_result($content);
                $stmt->fetch();
            echo "Current Story:".$content;
            ?>
            <form action = "true_edit_story.php" method='post' name='edit_story' id='edit_story'>
                <textarea name='editbox' id='editbox' form='edit_story' rows='4'columns='50'><?=$content?></textarea>
				<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                <input type="submit" name="editsubmit" value="Submit story">
            </form>
		</body>
	</html>


