# OsuRasp
Telegram bot for Orenburg State University  osu.ru schedules
Support Students  and teachers

Based on https://github.com/ICQFan4ever/PHP-Telegram-Bot/

Rewrited for php-7 and mysqli


#1 Copy settings-dist.php to settings.php

#2 Create bot 

1) Search BotFather in telegram
2) enter /newbot
3) enter name for bot. 
4) enter login_name for bot. ONLY english and it must end with "bot", for example MySuper_bot
5) Done. Store API token under this line "Use this token to access the HTTP API:"

#3 Edit setting.php

define('BOT_TOKEN', '123123:123:123ELml4Ap5QqxIas'); // токен бота
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/'); // адрес API. Без нужды не трогать!
define('WEBHOOK', 'https://your_domain.ru/process.php'); // адрес вебхука, поменять на свой (строго https!!!)
define('ADMIN', 'ivanivanov123'); // ник админа, используется проверка "админ ли?"
define('R', '/var/www/htmlt'); // рутовая директория, т.е. где лежит этот файл, например

#4 Webhook set 

 Open browser and Set webhook http://bot.ru/address/process.php?webhook=1

5. Edit cron

start editor 

`crontab -e

and add this lines:

`*/5 * * * * /usr/bin/php /var/www/cron_time.php
`*/5 * * * * /usr/bin/php /var/www/cron_before.php



5 Search you bot in Telegram and press Start

 
