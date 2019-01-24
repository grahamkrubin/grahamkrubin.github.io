<!DOCTYPE html>
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
	//getting the right key
	foreach($_POST['edit'] as $key => $value);
	//set story_id session to key, so we don't have to do this foreach nonsense again.
	(int)$_SESSION['story_id'] = $key;
	$story = $mysqli->prepare("SELECT story_content FROM stories WHERE story_id = ?");
	if(!$story){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $story->bind_param('s', $key);
    $story->execute();
    $story->bind_result($content);
    $story->fetch();
    $story->close();
	?>
	<form action="true_edit_story.php" method="post" name='edit_story' id='edit_story'>
		<textarea name='editbox' id='editbox' form='edit_story' rows='4' columns='50'><?=$content?></textarea>
		<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
		<input type="submit" name="editsubmit" value="Submit Story">
	</form>
</body>
</html>