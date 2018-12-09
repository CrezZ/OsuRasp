<?php

///////////////////
////  Lib for Telegram send menu and ..

function array_flatten($array) { 
  if (!is_array($array)) { 
    return FALSE; 
  } 
  $result = array(); 
  foreach ($array as $key => $value) { 
    if (is_array($value)) { 
      $result = array_merge($result, array_flatten($value)); 
    } 
    else { 
      $result[$key] = $value; 
    } 
  } 
  return $result; 
} 

function viberMenu($chat,$text,$caption,$keyboard){
//    $keyboard = [	['/w1 Я студент', '/w2 Я преподаватель'],
//			        ['/h Помощь', '/start Сброс']];
				$keyboard2=array_flatten($keyboard);
	            $buttons = [];
            foreach ($keyboard2 as $k) {
				$pos=max(strpos($k,'-'), strpos($k,' '));
				$k2=substr($k,$pos+1,strlen($k));
                $buttons[] =
                    (new \Viber\Api\Keyboard\Button())
                        ->setBgColor('#8074d6')
                        ->setColumns(3)
//						->setSilent(true)
						
						->setActionType('reply')
                        ->setActionBody($k)
                        ->setText($k2);
            }
			//error_log(var_export($buttons,true));
        $chat[0]->getClient()->sendMessage(
           (new \Viber\Api\Message\Text())
           ->setSender($chat[1])
           ->setReceiver($chat[2])
           ->setText("$text") 
		   ->setKeyboard(
                    (new \Viber\Api\Keyboard())
                        ->setButtons($buttons))
		   );

	
}

function sendMsg($chat,$text,$caption=''){
	if (MESSENGER=='telegram'){
	  sendMessage($chat,$text,$caption);
	}
	if (MESSENGER=='viber'){
        $chat[0]->getClient()->sendMessage(
           (new \Viber\Api\Message\Text())
           ->setSender($chat[1])
           ->setReceiver($chat[2])
           ->setText("$text") );

	}
}

function hideMenu($chat){
//hide all menus
	if (MESSENGER=='telegram'){
    deleteKeyboard($chat,0 );
	}

}

function sendHelp($chat){
//global help
$tg_add="";
$viber_add="VIBER";
$add="";
if (MESSENGER=='viber') $add=$viber_add;

$help="Это бот для расписания Оренбургского госдарственного университета.".
	"Сначала нужно выбрать студент Вы /w1 или преподаватель /w2, затем следовать подсказкам.\n".
	"/start - очистить все настройки и начать заново. Если что то не работает, рекомендуется начать с этого места.\n".
	"/noreminder - удалить все напоминания\n".
	"/rs - управление напоминаниями о парах\n".
	"/rasp - работа с распиcанием через меню (если доступно))\n".
	"/day - пары сеодня (если доступно))\n".
	"/z - пары завтра (если доступно))\n".
	"/show - показать настройки" ;
    $keyboard = [	
					['/w1 Я студент', '/w2 Я преподаватель'],
					['/noreminder Не напоминать','/rs Напоминания'],
					['/rasp Основной режим','/show Посмотреть настройки'],
					['/day Пары сегодня','/z Пары завтра'],
			        ['/h Помощь', '/start Сброс']];

	if (MESSENGER=='viber'){
		//sendMsg($chat,$help);
		viberMenu($chat,$help,'',$keyboard);
	}
	if (MESSENGER=='telegram'){
	 hideMenu($chat);
	 //$keyb2=explode("\n",$keyboard);
     sendKeyboard($chat, $help, 'html',0 , $keyboard,0);
    }
    return true;
}

function sendMenu1($chat,$text,$messenger=''){
// menu for select all 
    $keyboard = [	['/w1 Я студент', '/w2 Я преподаватель'],
		        ['/h Помощь', '/start Сброс']];

	if ((MESSENGER=='viber')||($messenger=='viber')){
		//sendMsg($chat,$help);
		viberMenu($chat,$text,'',$keyboard);
	}

	if ((MESSENGER=='telegram')||($messenger=='telegram')){
     //hideMenu($chat);
    sendKeyboard($chat, "$text" , 'Markdown',0 , $keyboard,0);
	}
}

function sendMenu2($chat,$text){
// menu for sceduler
    $keyboard =[
	        ['/h Помощь', '/start Сброс']];

	if (MESSENGER=='viber'){
		//sendMsg($chat,$help);
		viberMenu($chat,$text,'',$keyboard);
	}

 	if (MESSENGER=='telegram'){
//   hideMenu($chat);
    sendKeyboard($chat, "$text" , 'Markdown',0 , $keyboard,0);
	}
}

function sendMenu4($chat,$text,$messenger=''){
    $keyboard = [
	['/day Что сегодня','/z Что завтра'],
			['/rs1 Оповещать утром', '/rs2 Оповещать перед парой'],
		        ['/h Помощь', '/start Сбросить настройки']];

	if ((MESSENGER=='viber')||($messenger=='viber')){
		//sendMsg($chat,$help);
		viberMenu($chat,$text,'',$keyboard,0);
	}

	if ((MESSENGER=='telegram')||($messenger=='telegram')){
    sendKeyboard($chat, "$text" , 'html', 0 , $keyboard,0);
	}
}
function sendInlineMenu2($chat,$text,$array){
    $keyboard = [[],['/h Помощь', '/start Сброс']];
   foreach($array as $a){
    $keyboard[0][]=$a;
   }
    //error_log(var_export($keyboard, true));
    //error_log(var_export($array, true));
	
   //$text.="DEBUG\n".export_var($keyboard,true);
	if (MESSENGER=='viber'){
		//sendMsg($chat,$help);
		viberMenu($chat,$text,'',$keyboard);
	}
	if (MESSENGER=='telegram'){
// TODO inline answer 
//    sendInlineKeyboard($chat, "$text" , 'html', 0 , $keyboard);
    sendKeyboard($chat, "$text" , 'html', 0 , $keyboard,2);

	}
}


function lessonsList($who_id,$date,$prep_id,$group_id=0,$potok_id=0,$facult_id=0){
	//USage:
	// require 'lib.osu.php';
	// $date=strtoupper(date("d-M-y",time()));
	// lessonsList(2,$date,$ar['prep_id']); //prepod
	// lessonsList(2,$date,0,$ar['group_id'],$ar['potok_id'],$ar['facult_id']); //student
	// lessonsList($ar['who_id'],$date,$ar['prep_id'],$ar['group_id'],$ar['potok_id'],$ar['facult_id']); //universal
			$rasp=array();
            
			
			if ($who_id==2) //prepod
				$ras=getOSUData('rasp',$prep_id,$who_id);
			if ($who_id==1) //  student
				$ras=getOSUData('rasp',"$group_id&potok=$potok_id&facult=$facult_id",1);
            
            foreach ($ras as $f){
              if ($f['DAY']==$date){
			  if ($who_id==2) //prepod	
                 $rasp[]=$f['DESCRIPTION'].' - '.$f['AUD_ALL_LPAD'].'-'.$f['NAMEGROUP'].' '.$f['SHORT_NAME_SUB'].'('.$f['TYPEZAN_SHORT_NAME'].")";
			  if ($who_id==1) //  student
			     $rasp[]=$f['DESCRIPTION'].' - '.$f['AUD_ALL_LPAD'].'-'.$f['FIO_SOKR'].' '.$f['SHORT_NAME_SUB'].'('.$f['TYPEZAN_SHORT_NAME'].")";
			   }  //filter
             }//foreach

	return $rasp;
} //lessonsList


?>