<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="allstyles.css">
        <title>story submitting page</title>
    </head>
    <body>
        <!--new user button-->
        <form name="story_submit" id="story_submit" action="story_submissionrealphp.php" method="post">
            <p>
                <?php session_start();
                ?>
               <label>
                    Title:
                </label>
                <input type="text" name="story_title">
               
                <br>
                <label>
                    Story:
                </label>
                
                <div>
                <textarea name="storybox" id="storybox" form="story_submit" rows = "5" cols = "60" placeholder="Write story here..."></textarea>
                </div>
                
                <br>
                <label>
                    Optional Link (include https://):
                </label>
                <input type="text" name="link">
                <br>
                <br>
                <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                <input type="submit" name="newusersubmit" value="Submit"> 
            </p>
        </form>
 
    </body>
</html>
    
