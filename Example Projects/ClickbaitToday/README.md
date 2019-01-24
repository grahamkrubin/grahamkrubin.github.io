#ClickbaitToday: a story sharing webapp
###Students involved:
- Robert Landlord - robertlandlord@wustl.edu
- Graham Rubin - grahamkrubin@wustl.edu

### Features Implemented
- Basics: A user can create an account and sign in.
- User Page: When a user is logged in, they will see a button towards the top of the main page that reads "User Page." This displays all of the users stories and comments, and lets them delete or edit any of them (they may also view their stories from this page). The User Page also features another creative addition, Password Change.
- Password Change: This feature, located in a button on a user's User Page, allows them to change their password if they so desire. The user is prompted to enter their old password and then enter their new password twice, to avoid typos.
- Search Bar: This final feature is available at the very top of the main page for account-having users and lurkers alike. The user may enter a search string, and they will be redirected to a page featuring all articles with that string in the title.
- Security: detects request forgery

###Operating Info:
You can visit the site [here](http://ec2-18-191-25-109.us-east-2.compute.amazonaws.com/~grahamrubin/ClickbaitToday/main_news_page.php). There is a new account with username: hey, password: hi. However please feel free to create your own!
Otherwise:
- Clone this repo
- Upload it to a server instance
- Edit the file userdatabase.php
- Enter the following lines to a MySQL shell:
create database DBNAME
use DBNAME
CREATE TABLE userinfo (userid mediumint not null auto_increment, username varchar(100) not null, passhash varchar(200), length mediumint not null, primary key (userid));
CREATE TABLE stories (story_title varchar(200), story_link varchar(500), story_content text, author_id mediumint not null, date_sub datetime, story_id mediumint not null auto_increment, primary key (story_id), foreign key (author_id) references userinfo(userid));
CREATE TABLE comments (comment_id mediumint not null auto_increment, content text, story_id mediumint not null, user_id mediumint not null, primary key(comment_id), foreign key (story_id) references stories(story_id));





