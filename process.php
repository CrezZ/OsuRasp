<?php
// exit;
#error_log("Begin");
require_once 'settings.php';
require_once 'api.php'; 
require_once 'lib.osu.php';
require_once 'lib.tg.php';

		define('MESSENGER', 'telegram');

if(php_sapi_name() == 'cli' OR isset($_GET['webhook']))
	{
		setWebhook(WEBHOOK);	
	}

$content = file_get_contents("php://input"); // всё, что пришло на вебхук ПОСТом - идет в $content
$update = @json_decode($content, true); // декодим из джсона в ассоциативный массив
/*
ob_start();
//print_r($update);
$dat = ob_get_contents();
file_put_contents(R.'/data/debug.log', $dat.var_export($update,true));
ob_end_clean();
*/
// die;

if(!$update)
	{
		// кривой JSON, значит левый запрос или что-то такое
		die;
	}
else
	{
		define ('INLINE',!empty($update['callback_query']));
//		# делаем псевдоглобальные переменные
		$_MESS = (!empty($update['message']))? $update['message']:$update['callback_query']['message']; // массив с содержанием самого сообщения (полезная информация то есть)
		$_TEXT = (!INLINE)?mb_strtolower($_MESS['text'], 'utf-8'):
					mb_strtolower($update['callback_query']['data'], 'utf-8'); // для нерегистрозависимости сразу текст в нижнее подчеркивание
		$_CHAT = (!empty($_MESS['chat']))?$_MESS['chat']:''; // информация о том, какой это чат (если это личка, части переменных не будет)
		$_USER = $_MESS['from']; // информация о юзере-отправителе
		$_USER['username'] = empty($_USER['username']) ? $_USER['first_name'].' '.$_USER['last_name'] : $_USER['username'];
		

		$_CHAT['title'] = empty($_CHAT['title']) ? 'ЛС' : $_CHAT['title'];

		// пишем в базу
		mysqli_query($mysql,"INSERT INTO `messages`(`id_chat`, `id_message`, `id_user`, `time`, `message`, `user_nick`, `chat_name`) VALUES ('".$_CHAT['id']."', '".$_MESS['message_id']."', '".$_USER['id']."', '".time()."', '".$_MESS['text']."', '".$_USER['username']."', '".$_CHAT['title']."')");

		// собираем базу юзеров
		$q_u = mysqli_query($mysql,"SELECT * FROM `tg_users` WHERE `id_user` = '".$_USER['id']."'");
		if(mysqli_num_rows($q_u) < 1)
			{				
				mysqli_query($mysql,"INSERT INTO `tg_users`(`id_user`, `nick`) VALUES ('".$_USER['id']."', '".$_USER['username']."')");
			}
		
/*		// if not private
		if($_USER['id'] != $_CHAT['id'])
			{
				$q_c = mysqli_query($mysql,"SELECT * FROM `tg_chats` WHERE `id_chat` = '".$_CHAT['id']."'");
				if(mysqli_num_rows($q_c) < 1)
					{
						mysqli_query($mysql,"INSERT INTO `tg_chats`(`id_chat`, `title`) VALUES ('".$_CHAT['id']."', '".$_CHAT['title']."')");
					}
			}


		if($_USER['username'] == ADMIN)
			{
				if($_USER['id'] == $_CHAT['id'])
					{
						if(!empty($_MESS['document']['file_name']))
							{
								getFile($_MESS);
							}
					}
			}
*/

			
//		$qt = mysqli_query($mysql,"SELECT * FROM `blacklist_chats` WHERE `id_chat` = ".$_CHAT['id']);
//		if(mysqli_num_rows($qt) == 0 OR $_USER['username'] == ADMIN)
//			{
				$h = opendir('scripts');
				while(false !== ($file = readdir($h)))
					{
						$___tmp = explode('.', $file);
						$ext = end($___tmp);
						if($ext == 'php')
							{
								require_once 'scripts/'.$file;
							}
					}
				closedir($h);
//			}
	}