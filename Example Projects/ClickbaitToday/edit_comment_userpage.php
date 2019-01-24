<!doctype html>
	<html lang="en">
		<head>
			<meta charset="utf-8">
			<link rel="stylesheet" href="allstyles.css">
			<title>Comment Editing</title>
		</head>
		<body>
            
			<?php
			session_start();
			if(!hash_equals($_SESSION['token'], $_POST['token'])){
			die("Request forgery detected");
			}
            require 'userdatabase.php';
			$current_user = (int)$_SESSION['user_id'];
			//get correct key for which button was pressed
            foreach($_POST['edit'] as $key => $value);
            $comment_id = $key;
            $stmt = $mysqli->prepare("SELECT content FROM comments WHERE comment_id = ?");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                $stmt->bind_param('s', $comment_id);
                $stmt->execute();
                $stmt->bind_result($content);
                $stmt->fetch();
				$stmt->close();
            (int)$_SESSION['comment_key'] = $key;
            ?>
            <form action = "true_edit.php" method='post' name='edit_comment' id='edit_comment'>
                <textarea name='editbox' id='editbox' form='edit_comment' rows='4'columns='50'><?=$content?></textarea>
				<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                <input type="submit" name="editsubmit" value="Submit comment">
            </form>
		</body>
	</html>


