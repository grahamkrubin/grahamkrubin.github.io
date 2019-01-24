<!doctype html> 
<html>
<!-- php load sql-->
<body>
<?php
require 'userdatabase.php';
$story = $mysqli->prepare("select story_title, story_link, story_content, author_id, date_sub from stories");
$story->execute();
$story->bind_result($title, $link, $content, $author_id, $time_written);

while($story->fetch()) {
	printf("\t<p>%s\n%s\n%s\n%s</p>\n",
	htmlspecialchars($title),
	htmlspecialchars($time_written),
	htmlspecialchars($link),
	htmlspecialchars($author_id)
	);
	echo "\n".$content;
}

$story->close();
?>
</body>
</html>
