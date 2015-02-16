#Starttistics#

##Technologies##

The system was developed based at:

* PHP 5.x

##Usage##

*IMPORTANT*

If you want to run this correctly, please include a cron job that hourly invokes the script like:

```
# m h dom mon dow user command
00 15	* * 5 	root	cd /var/www/dctb-startistics/ && php weekly.php
00 14	* * *	root	cd /var/www/dctb-startistics/ && php mail.php
0 *	* * *	    root	cd /var/www/dctb-startistics/ && php index.php
```

##Dependences##

* Mandrill
* PHPlot