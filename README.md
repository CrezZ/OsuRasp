# OsuRasp
Telegram bot for Orenburg State University  osu.ru schedules
Support Students  and teachers

Based on https://github.com/ICQFan4ever/PHP-Telegram-Bot/

Rewrited for php-7 and mysqli

## Webhook set 
1 Copy settings-dist.php to settings.php
2 Create bot 
3 Edit setting.php
4 Set webhook http://bot.ru/address/process.php?webhook=1

## Edit cron

*/5 * * * * /usr/bin/php /var/www/cron_time.php
*/5 * * * * /usr/bin/php /var/www/cron_before.php

5 Enjoy
