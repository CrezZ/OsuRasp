# OsuRasp
Telegram bot for Orenburg State University  osu.ru schedules
Support Students  and teachers

Telegram based on https://github.com/ICQFan4ever/PHP-Telegram-Bot/
Rewrited for php-7 and mysqli

Viber based on https://github.com/Bogdaan/viber-bot-php
Whatsapp nased on https://github.com/mgp25/Chat-API




##0 Libraries and DB

for viber
1 apt-get install php7.1-gd

for whatsapp
Requires: PHP Protobuf and Curve25519 to enable end to end encryption


0.1 Install composer
0.2 RUN php composer.phar update or composer update
It create floder "vendor/" with libraries
VIBER WILL NOT WORK without this step!

0.3 Create database and tables

`mysql -p -e 'create database my_bot'
`mysql -p my_bot <schema.sql


#1 Copy settings-dist.php to settings.php

#2.1 Create Telegram bot 

1) Search BotFather in telegram
2) enter /newbot
3) enter name for bot. 
4) enter login_name for bot. ONLY english and it must end with "bot", for example MySuper_bot
5) Done. Store API token under this line "Use this token to access the HTTP API:"

#2.2 Create Viber bot 

1) Go to https:// partners.viber.com  → Login there  → Create Bot Account
2) enter all fields
3) Done. Store API token and URI

#3 Edit setting.php

Common 
define('ADMIN', 'ivanivanov123'); // ник админа, используется проверка "админ ли?"
define('MYSQL_HOST','localhost');
define('MYSQL_USER','root');
define('MYSQL_PASS','123123');
define('MYSQL_DB','my_bot');


Telegram
define('BOT_TOKEN', '123123:123:123ELml4Ap5QqxIas'); // токен бота
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/'); // адрес API. Без нужды не трогать!
define('WEBHOOK', 'https://your_domain.ru/process.php'); // адрес вебхука, поменять на свой (строго https!!!)

Viber
define('VIBER_WEBHOOK', 'https://your_domain.ru/process_v.php'); // адрес вебхука, поменять на свой (строго https!!!)
define('VIBER_BOT_TOKEN', '123123:123:123ELml4Ap5QqxIas'); // токен бота



#4 Webhook set 

Telegram
 Open browser and Set webhook http://youbotaddress/address/process.php?webhook=1

Viber
 Open browser and Set webhook http://youbotaddress/address/process_v.php?webhook=1

5. Edit cron

start editor 

`crontab -e

and add this lines (Corrected path!):

`*/5 * * * * /usr/bin/php /var/www/cron/cron_time.php
`*/5 * * * * /usr/bin/php /var/www/cron/cron_before.php



5 Search you bot in Telegram or Viber and press Start

 
