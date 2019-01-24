<?php
session_start();
$link=(string)$_SESSION['link'];
//echo file_get_contents($link);
echo '<iframe src='.$link.'></iframe>';
?>