<?php
# основные настройки бота
#$bot_api_key  = '747356094:AAEJtIQ7QKLfUEai74AR9ELml4Ap5QqxIas';
#$bot_username = 'OsuRasp_bot';
#$hook_url     = 'https://sip4.hvcloud.ru/t/hook.php';

define('BOT_TOKEN', '123123:123:123ELml4Ap5QqxIas'); // токен бота
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/'); // адрес API. Без нужды не трогать!
define('WEBHOOK', 'https://your_domain.ru/process.php'); // адрес вебхука, поменять на свой (строго https!!!)
define('ADMIN', 'ivanivanov123'); // ник админа, используется проверка "админ ли?"
define('R', '/var/www/htmlt'); // рутовая директория, т.е. где лежит этот файл, например

# доп. сервисы
define('SPEECHKIT_TOKEN', '123'); // token Yandex.SpeechKit для распознавания голоса
define('LASTFM', '1234567890'); // API key Last.FM для просмотра NowPlaying

# настройки подключения к БД
$mysql=mysqli_connect('localhost','root','123123123') or die("AAAAB".mysqli_connect_error()); // укажите тут данные для коннекта к серверу БД
mysqli_select_db($mysql,'tg_bot') or die("AAAA".mysqli_error(1)); // укажите имя базы
mysqli_set_charset($mysql,'utf8'); // по умолчанию все на utf-8