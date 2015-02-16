#Startistics#

Startistics is a social networks monitoring system. This system is able to collect growing informations of following social networks:

* Facebook - Page Likes
* Twitter - Followers
* Instagram - Followes
* Youtube - Followers
* Youtube - Views

##What is Startistics?##

Since you configured you cronjob to hourly collect social networks informations, you are able, before some time, to user the following resources:

* Daily Mails - you will recive every day a table of contents of growingup of all social networks inspected;
* Weekly Mails - you will recive every week a table of contents of growingup of all social networks inspected;
* Analytics - you can consult and compare growingup informations;
* Plot - you will able to plot a lot of options of growingup graphs;

##Technologies##

The system was developed based at:

* PHP 5.x

##Usage##

You will need a MySQL database with following structure (also avaliable at _Extras/Sql/create_database.sql_):

```
CREATE TABLE startistics (
	id INTEGER NOT NULL AUTO_INCREMENT,
	artist VARCHAR(500) NOT NULL,
	facebook DOUBLE NOT NULL,
	twitter DOUBLE NOT NULL,
	instagram DOUBLE NOT NULL,
	youtube_sc DOUBLE NOT NULL,
	youtube_tuv DOUBLE NOT NULL DEFAULT 0,
	date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id)
)DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
```

You will need configure _Config/config.php_ with your social networks tokens, MySQL connection and Mandrill Key.

You also need to insert social networks that you would like do inspect at the _Data/data.php_ file following the structure:

```
<?php
	$artists = array(
		'name' => array('facebook'  => 'facebook_page', 
			            'twitter'   => 'twitter_page',
			            'instagram' => 'instagram_id',
			            'youtube'   => 'youtube_page'),
				  ...
	)
?>
```

*IMPORTANT*

If you want to run this correctly, please include a cron job that hourly invokes the script like:

```
# m h dom mon dow user command
00 15	* * 5 	root	cd /var/www/dctb-startistics/ && php weekly.php
00 14	* * *	root	cd /var/www/dctb-startistics/ && php mail.php
0 *	* * *	    root	cd /var/www/dctb-startistics/ && php index.php
```

##Dependences##

We use composer with the following packages:

```
{
    "require": {
        "mandrill/mandrill": "1.0.*",
        "facebook/php-sdk": "dev-master",
        "abraham/twitteroauth": "dev-master",
        "davefx/phplot": "dev-master"
    }
}
```