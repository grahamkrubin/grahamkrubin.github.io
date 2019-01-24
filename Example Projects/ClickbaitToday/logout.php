<?php
session_start();
$_SESSION['loggedin']=0;
$_SESSION['user_id']=0;
header("Location: http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/main_news_page.php");
session_destroy();
?>