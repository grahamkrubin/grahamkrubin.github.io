<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="allstyles.css">
	<title>Change your password</title>
</head>
<body>

	<?php
	session_start();
	require 'userdatabase.php';
	//getting information from password setting
	$user_id = (int)$_SESSION['user_id'];
	$new_pass_0 = password_hash($_POST['new_password_0'], PASSWORD_BCRYPT);
	$new_pass_1 = password_hash($_POST['new_password_1'], PASSWORD_BCRYPT);
	$database = $mysqli->prepare("SELECT COUNT(*), passhash FROM userinfo WHERE userid=?");
	if (!$database) {
		printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
	}
	$database->bind_param('s', $user_id);
	$database->execute();
	$database->bind_result($count, $passhash);
	$database->fetch();
	$temp_hash = $passhash;
	$temp_count = $count;
	$database->close();
	//check if old password and entered old password match up
	if (!($temp_count == 1 && password_verify($_POST['old_password'], $temp_hash))) {
		echo "Your old password does not match<br>";
	}
	
	//else, reset password to new thing
	else if (password_verify($_POST['new_password_0'],$new_pass_1) && password_verify($_POST['new_password_1'],$new_pass_0)) {
		echo "You changed your password successfully!<br>";
		$stmt = $mysqli->prepare("UPDATE userinfo SET passhash = ? WHERE userid = ?");
		if(!$stmt){
        	printf("Query Prep Failed: %s\n", $mysqli->error);
        	exit;
    	}
    	$stmt->bind_param('ss', $new_pass_0, $user_id);
		$stmt->execute();
		$stmt->close();
	}
	else if (!(password_verify($_POST['new_password_0'],$new_pass_1) && password_verify($_POST['new_password_1'],$new_pass_0))) {
		echo "The new password instances do not match.<br>";
	}
	echo "<form name='back_to_user_page' action='http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/user_page.php'><input type='submit' value='Back to User Page.'";
	?>

</body>
</html>