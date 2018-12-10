<?php
# основные настройки бота
#$bot_api_key  = '747356094:AAEJtIQ7QKLfUEai74AR9ELml4Ap5QqxIas';
#$bot_username = 'OsuRasp_bot';
#$hook_url     = 'https://sip4.hvcloud.ru/t/hook.php';

//Telegrm

define('BOT_TOKEN', '74735123123123123'); // токен бота
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/'); // адрес API. Без нужды не трогать!
define('WEBHOOK', 'https://sip4.hvcloud.ru/t/process.php'); // адрес вебхука, поменять на свой (строго https!!!)
define('ADMIN', 'ivanivanov'); // ник админа, используется проверка "админ ли?"
define('R', '/var/www/fusionpbx/t'); // рутовая директория, т.е. где лежит этот файл, например

//Viber
define('VIBER_BOT_TOKEN', '123123123'); // токен бота
define('VIBER_WEBHOOK', 'https://sip4.hvcloud.ru/t/process_v.php'); // адрес вебхука, поменять на свой (строго https!!!)

//MYsql
define('MYSQL_HOST','localhost');
define('MYSQL_USER','root');
define('MYSQL_PASS','123123');
define('MYSQL_DB','my_bot');



# доп. сервисы
define('SPEECHKIT_TOKEN', '123'); // token Yandex.SpeechKit для распознавания голоса
define('LASTFM', '1234567890'); // API key Last.FM для просмотра NowPlaying

# настройки подключения к БД


$mysql=mysqli_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASS) or die("AAAAB".mysqli_connect_error()); // укажите тут данные для коннект
а к серверу БД
mysqli_select_db($mysql,MYSQL_DB) or die("DB NOT FOUND".mysqli_error(1)); // укажите имя базы
mysqli_set_charset($mysql,'utf8'); // по умолчанию все на utf-8
