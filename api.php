<?php
#error_reporting(~E_NOTICE);
	


require_once 'settings.php';


# простая отправка сообщения
function sendMessage($id_chat, $text, $mark = '', $id_message = '')
	{
		// sendChatAction($id_chat, 'typing');
		// sleep(1);
		// $text = empty($text) ? 'undef or empty var' : $text;
		$toSend = array('method' => 'sendMessage', 'chat_id' => $id_chat, 'text' => $text);
		!empty($id_message) ? $toSend['reply_to_message_id'] = $id_message : '';
		!empty($mark) ? $toSend['parse_mode'] = $mark : '';
		
		// $toSend['reply_to_id_message'] = $id_message;
		$ch = curl_init(API_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($toSend));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$a = curl_exec($ch);
		return json_decode($a, true);
	}

# редактирование
function editMessage($id_message, $id_chat, $text, $mark = '')
	{
		$toSend = array('method' => 'editMessageText', 'message_id' => $id_message, 'chat_id' => $id_chat, 'text' => $text);
		!empty($mark) ? $toSend['parse_mode'] = $mark : '';
		
		$ch = curl_init(API_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($toSend));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$a = curl_exec($ch);
		return json_decode($a, true);
	}

# отправка клавы с выборкой
function sendKeyboard($id_chat, $text, $mark = '', $id_message = '', $keyboard = array(),$max_in_row=0)
	{
		sendChatAction($id_chat, 'typing');
		//sleep(1);
		$inline=array();
		$add="";	
		$i=0;$j=0;
		//$max_in_row=3;
		if ($max_in_row>0){
		  foreach ($keyboard as $k1){
			
		  if (count($k1)>1){
		    foreach ($k1 as $k2){
			 //$k2n=substr($k2,strpos($k2,'-')+1,strlen($k2));
			 $inline[$j][]=['text'=> "$k2", 'callback_data' => "$k2" ];
			 $i++;
		  if ($i%$max_in_row==0) $j++;
		   }} else
		   {   
	         // $k1n=substr($k1,strpos($k1,'-')+1,strlen($k1));
		     $inline[$j][]=((array('text'=> "$k1", 'callback_data' => "$k1" )));
	     	}
//			if ($i%$max_in_row==0) $j++;		
		     $i++;
		 //		 error_log($i.$j);
		  }
		} else
		{$inline=$keyboard;}
		$toSend = array('method' => 'sendMessage', 'chat_id' => $id_chat, 'text' => $text);
		isset($id_message) ? $toSend['reply_to_message_id'] = $id_message : '';
		isset($mark) ? $toSend['parse_mode'] = $mark : '';
		
		!empty($keyboard) ? $toSend['reply_markup'] = array('keyboard' => $inline, 'resize_keyboard' => true, 'selective' => true) : '';
		
		$ch = curl_init(API_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($toSend));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$a = curl_exec($ch);
		return json_decode($a, true);
	}

function sendInlineKeyboard($id_chat, $text, $mark = '', $id_message = '', $keyboard = array())
	{
		sendChatAction($id_chat, 'typing');
		//sleep(1);
		$inline=array();
		$add="";	
		$i=0;$j=0;
		$max_in_row=3;
		foreach ($keyboard as $k1){
			
		  if (count($k1)>1){
		 foreach ($k1 as $k2){
			 $k2n=substr($k2,strpos($k2,'-')+1,strlen($k2));
			 $inline[$j][]=['text'=> "$k2n", 'callback_data' => "$k2" ];
			 $i++;
		 if ($i%$max_in_row==0) $j++;
		 }} else
		 {   
	          $k1n=substr($k1,strpos($k1,'-')+1,strlen($k1));
		 $inline[$j][]=((array('text'=> "$k1n", 'callback_data' => "$k1" )));
			  }
		 $i++;
		 if ($i%$max_in_row==0) $j++;
//		 error_log($i.$j);
		}
		
		//error_log(var_export($inline, true));
		//$text.=var_export($inline, true);
		
		
		$toSend = array('method' => 'sendMessage', 'chat_id' => $id_chat, 'text' => "$text");
		isset($id_message) ? $toSend['reply_to_message_id'] = $id_message : '';
		isset($mark) ? $toSend['parse_mode'] = $mark : '';
		!empty($keyboard) ? $toSend['reply_markup'] = array('inline_keyboard' => $inline) : '';
		//error_log(json_encode($toSend));
		$ch = curl_init(API_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($toSend));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$a = curl_exec($ch);
		//error_log($a);
		return json_decode($a, true);
	}

function deleteKeyboard($id_chat, $id_message)
	{
		$toSend = array('method' => 'sendMessage', 'chat_id' => $id_chat);
		isset($id_message) ? $toSend['reply_to_message_id'] = $id_message : '';
		$toSend['reply_markup'] = array('remove_keyboard' => true, 'selective' => true);
		
		$ch = curl_init(API_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($toSend));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$a = curl_exec($ch);
		return json_decode($a, true);
	}

# Отправка картинки
function sendImage($id_chat, $path, $local = true, $caption = '', $message_id = '')
	{
		sendChatAction($id_chat, 'typing');
		//sleep(1);
		
		if(!$local)
			{
				$ch2 = curl_init($path);
				curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch2, CURLOPT_USERAGENT, 'Mozilla/2.28 for Android 1488 Yoba edition');
				$a = curl_exec($ch2);
				
				$__tmp = explode('.', $path);
				$ext = end($__tmp);
				
				$fop = fopen('data/tmp.'.$ext, 'w');
				flock($fop, LOCK_EX);
				fwrite($fop, $a);
				flock($fop, LOCK_UN);
				fclose($fop);
				$path = 'data/tmp.'.$ext;
			}
				
		
		$cfile = new CURLFile(realpath($path));
		$toSend = array('method' => 'sendPhoto', 'chat_id' => $id_chat, 'photo' => $cfile);
		!empty($caption) ? $toSend['caption'] = $caption : '';
		!empty($message_id) ? $toSend['reply_to_message_id'] = $message_id : '';
		
		$ch = curl_init(API_URL);
		
		
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $toSend);
		$a = curl_exec($ch);
	}

# отправка файла
function sendFile($id_chat, $path, $local = true, $caption = '', $message_id = '')
	{
		sendChatAction($id_chat, 'typing');
		//sleep(1);
		
		if(!$local)
			{
				$ch2 = curl_init($path);
				curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch2, CURLOPT_USERAGENT, 'Mozilla/2.28 for Android 1488 Yoba edition');
				$a = curl_exec($ch2);
				
				$ext = end(explode('.', $path));
				
				$fop = fopen('data/tmp.'.$ext, 'w');
				flock($fop, LOCK_EX);
				fwrite($fop, $a);
				flock($fop, LOCK_UN);
				fclose($fop);
				$path = 'data/tmp.'.$ext;
			}
				

		$cfile = new CURLFile(realpath($path));
		$toSend = array('method' => 'sendDocument', 'document' => $cfile, 'chat_id' => $id_chat);
		!empty($caption) ? $toSend['caption'] = $caption : '';
		!empty($message_id) ? $toSend['reply_to_message_id'] = $message_id : '';
		
		$ch = curl_init(API_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $toSend);
		$a = curl_exec($ch);
	}

function sendVideo($id_chat, $path, $local = true, $caption = '', $message_id = '')
	{
		if(!$local)
			{
				$ch2 = curl_init($path);
				curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch2, CURLOPT_USERAGENT, 'Mozilla/2.28 for Android 1488 Yoba edition');
				$a = curl_exec($ch2);
				
				$ext = end(explode('.', $path));
				
				$fop = fopen('data/tmp.'.$ext, 'w');
				flock($fop, LOCK_EX);
				fwrite($fop, $a);
				flock($fop, LOCK_UN);
				fclose($fop);
				$path = 'data/tmp.'.$ext;
			}
		$ppath = realpath($path);
		$cfile = new CURLFile($path);
		$toSend = array('method' => 'sendVideo', 'video' => $cfile, 'chat_id' => $id_chat);
		!empty($caption) ? $toSend['caption'] = $caption : '';
		!empty($message_id) ? $toSend['reply_to_message_id'] = $message_id : '';
		
		$ch = curl_init(API_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $toSend);
		$a = curl_exec($ch);
		var_dump($a);
	}

# форвард
function forwardMessage($id_chat, $from_id, $message_id)
	{
		sendChatAction($id_chat, 'typing');
		//eep(1);
		
		$toSend = array('method' => 'forwardMessage', 'chat_id' => $id_chat, 'from_chat_id' => $from_id, 'message_id' => $message_id);
		
		$ch = curl_init(API_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($toSend));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$a = curl_exec($ch);
	}

# удаление 
function deleteMessage($id_chat, $id_message)
	{
		$toSend = array('method' => 'deleteMessage', 'chat_id' => $id_chat, 'message_id' => $id_message);
		$ch = curl_init(API_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($toSend));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$a = curl_exec($ch);
	}

# sendAction
function sendChatAction($id_chat, $action)
	{
		#typing for text messages, upload_photo for photos, record_video or upload_video for videos, record_audio or upload_audio for audio files, upload_document for general files, find_location for location data, record_video_note or upload_video_note for video notes.
		
		$toSend = array('method' => 'sendChatAction', 'chat_id' => $id_chat, 'action' => $action);
		$ch = curl_init(API_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($toSend));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$a = curl_exec($ch);
	}

# покинуть чат
function leaveChat($chat)
	{
		$toSend = array('method' => 'leaveChat', 'chat_id' => $chat);
		$ch = curl_init(API_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($toSend));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$a = curl_exec($ch);
	}

# webhook
function setWebhook($url, $delete = false)
	{
		$webhook = $delete == true ? 'delete' : $url;
		$toSend = array('url' => $webhook, 'method' => 'setWebhook');
		$ch = curl_init(API_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($toSend));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$a = curl_exec($ch);
		echo $a;
	}

# webhook
function setChatTitle($id_chat, $title)
	{
		$toSend = array('chat_id' => $id_chat, 'method' => 'setChatTitle', 'title' => $title);
		$ch = curl_init(API_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($toSend));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$a = curl_exec($ch);
		echo $a;
	}

function setChatDescription($id_chat, $title)
	{
		$toSend = array('chat_id' => $id_chat, 'method' => 'setChatDescription', 'description' => $title);
		$ch = curl_init(API_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($toSend));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$a = curl_exec($ch);
		echo $a;
	}

function recognizeVoice($_MESS = array())
	{
		
		if(isset($_MESS['voice']) && $_MESS['voice']['duration'] <= 15)
			{
				$_VOICE = $_MESS['voice'];				
				$ch = curl_init(API_URL.'getFile');
				$toSend = array('file_id' => $_VOICE['file_id']);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
				curl_setopt($ch, CURLOPT_TIMEOUT, 10);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($toSend));
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
				$_tmp = curl_exec($ch);
				$_tmp = json_decode($_tmp, true);
				$file_path = $_tmp['result']['file_path'];
				
				$url = "https://api.telegram.org/file/bot".BOT_TOKEN."/".$file_path;
				$f = file_get_contents($url);
				# раскомментировать код ниже, если хочется, чтобы все складывалось в voice локально
				#$fop = fopen('/var/www/4nmv.ru/bot/data/voice/'.$_VOICE['file_id'].'.ogg', 'w');
				#flock($fop, LOCK_EX);
				#fputs($fop, $f);
				#flock($fop, LOCK_UN);
				#fclose($fop);
								
				$ch = curl_init('http://asr.yandex.net/asr_xml?uuid='.md5(rand(1, 9)).'&key='.SPEECHKIT_TOKEN.'&topic=queries&lang=ru-RU');
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $f);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: audio/ogg;codecs=opus'));
				curl_setopt($ch, CURLOPT_VERBOSE, true);
				$a = curl_exec($ch);
				
				$rr = simplexml_load_string($a);
				$success = $rr->attributes()->success;

				if($success == 1)
					{
						$speech = $rr->variant[0]->__toString();
					}
				else
					{
						$speech = false;
					}
			}
		else
			{
				$speech = false;
			}
		
		return $speech;
		
		// return 'напомни мне';
	}

function getSong($mess)
	{
		$song = $mess['audio'];	
		if($song['mime_type'] != 'audio/mpeg')
			{
				sendMessage($mess['chat']['id'], 'MP3 Only', '', $mess['message_id']);
			}
		else
			{
				$filename = (!empty($song['performer']) ? trim($song['performer']) : 'Unknown Artist'.rand(100,999)).' - '.(!empty($song['title']) ? trim($song['title']) : 'Unknown Track'.rand(100,999)).'.mp3';
				$filename = str_replace(' ', '_', $filename);
				
				if(!file_exists('/var/www/MyMusic/'.$filename))
					{
						$ch = curl_init(API_URL.'getFile');
						$toSend = array('file_id' => $song['file_id']);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
						curl_setopt($ch, CURLOPT_TIMEOUT, 10);
						curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($toSend));
						curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
						$_tmp = curl_exec($ch);
						$_tmp = json_decode($_tmp, true);
						$file_path = $_tmp['result']['file_path'];
						
						$url = "https://api.telegram.org/file/bot".BOT_TOKEN."/".$file_path;
						
						$f = file_get_contents($url);
						$fop = fopen('/var/www/MyMusic/'.$filename, 'w');
						flock($fop, LOCK_EX);
						fputs($fop, $f);
						flock($fop, LOCK_UN);
						fclose($fop);
						if(file_exists('/var/www/MyMusic/'.$filename))
							{
								sendMessage($mess['chat']['id'], 'Спасибо, схоронил', '', $mess['message_id']);
							}
						else
							{
								sendMessage($mess['chat']['id'], 'Что-то пошло не так...', '', $mess['message_id']);
							}
					}
				else
					{
						sendMessage($mess['chat']['id'], 'Уже есть', '', $mess['message_id']);
					}	
			}
	}

function getFile($mess)
	{
		$file = $mess['document'];	
		$ch = curl_init(API_URL.'getFile');
		$toSend = array('file_id' => $file['file_id']);
		
		$tmpp = explode('.', $file['file_name']);
		$ext = end($tmpp);
		$filename = translit(str_replace('.'.$ext, '', $file['file_name'])).'_'.date('d.m.Y-H.i.s').'.'.$ext;
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($toSend));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$_tmp = curl_exec($ch);
		$_tmp = json_decode($_tmp, true);
		$file_path = $_tmp['result']['file_path'];
		// file_put_contents('/var/www/4nmv.ru/bot/data/debug.log', json_encode($_tmp));
		
		$url = "https://api.telegram.org/file/bot".BOT_TOKEN."/".$file_path;
		
		$f = file_get_contents($url);
		$fop = fopen('/var/www/4nmv.ru/bot/data/files/'.$filename, 'w');
		flock($fop, LOCK_EX);
		fputs($fop, $f);
		flock($fop, LOCK_UN);
		fclose($fop);
		
		$url = 'https://4nmv.ru/bot/data/files/'.$filename;
		sendMessage($mess['chat']['id'], $url);
	}


function mp3info($file = '')
	{
		if(file_exists($file) && end(explode('.', $file)) == 'mp3')
			{
				$a = shell_exec('mp3info "'.$file.'" -p "%a||%t||%S"');
				
				if(preg_match('#does not have#iu', $a))
					{
						$output['artist'] = 'Unknown Artist';
						$output['track'] = 'Unknown Track';
						$output['length'] = '0';
					}
				else
					{
						list($artist, $track, $length) = explode('||', $a);
						
						if(!empty($artist))
							{
								$output['artist'] = trim($artist);
							}
						else
							{
								$output['artist'] = 'Unknown Artist';
							}
						
						if(!empty($track))
							{
								$output['track'] = trim($track);
							}
						else
							{
								$tmp = explode('/', $file);
								
								$ff = end($tmp);
								
								$output['track'] = str_replace('.mp3', '', $ff);;
							}
						
						if(!empty($length))
							{
								$output['length'] = trim($length);
							}
						else
							{
								$output['length'] = '0';
							}
					}
			}
		else
			{
				$output['error'] = 'notrack';
			}
		
		return $output;
	}

function translit($s) 
	{
		$s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
		$s = trim($s); // убираем пробелы в начале и конце строки
		$s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
		$s = strtr($s, array('А'=>'A','Б'=>'B','В'=>'V','Г'=>'G','Д'=>'D','Е'=>'E','Ё'=>'E','Ж'=>'J','З'=>'Z','И'=>'I','Й'=>'Y','К'=>'K','Л'=>'L','М'=>'M','Н'=>'N','О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T','У'=>'U','Ф'=>'F','Х'=>'H','Ц'=>'C','Ч'=>'CH','Ш'=>'SH','Щ'=>'SHCH','Ы'=>'Y','Э'=>'E','Ю'=>'YU','Я'=>'YA','Ъ'=>'','Ь'=>''));
		$s = preg_replace("/[^0-9a-z\-_\. ]/i", "", $s); // очищаем строку от недопустимых символов
		$s = str_replace(" ", "_", $s); // заменяем пробелы 
		return $s; // возвращаем результат
	}