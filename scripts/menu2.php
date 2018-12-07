<?php

/*
create table save(id int primary key, user_id int, last_state int, 
who_id int,facult_id int, facult_name varchar(10), kafedra_id int, 
kafedra_name varchar(10), prep_id int, prep_name varchar(20), group_id int, 
group_name varchar(10), potok_id int, porok_name varchar(10));

*/

function hideMenu($chat){
//hide all menus
    deleteKeyboard($chat,'Справка' );
}

function sendHelp($chat){
//global help
    hideMenu($chat);
    $keyboard = [	['/w1 Я студент', '/w2 Я преподаватель'],
			        ['/h Помощь', '/start Сброс']];
    sendKeyboard($chat, 
	"Это бот для расписания Оренбургского госдарственного университета\n".
	"Сначала нужно выбрать студент Вы /w1 или преподаватель /w2, затем следовать подсказкам.\n".
	"/start - очистить все настройки\n".
//	"/pause - временно не напоминать о парах\n".
	"/rs - управление напоминаниями о парах\n".
	"/rasp - работа с распиcанием через меню (если доступно))\n".
	"/show - показать настройки" , 'Markdown',0 , $keyboard);
    return true;
}

function sendMenu1($chat,$text){
// menu for select all 
    hideMenu($chat);
    $keyboard = [	['/w1 Я студент', '/w2 Я преподаватель'],
		        ['/h Помощь', '/start Сброс']];
    sendKeyboard($chat, "$text" , 'Markdown',0 , $keyboard);
}

function sendMenu2($chat,$text){
// menu for sceduler
    hideMenu($chat);
    $keyboard =[
	        ['/h Помощь', '/start Сброс']];
    sendKeyboard($chat, "$text" , 'Markdown',0 , $keyboard);
}

function sendMenu4($chat,$text){
    $keyboard = [
	['/day Что сегодня','/z Что завтра'],
			['/rs1 Оповещать утром', '/rs2 Оповещать за N-минут'],
		        ['/h Помощь', '/start Сбросить настройки']];

    sendKeyboard($chat, "$text" , 'html', 0 , $keyboard);

}

//////////////////////////////////////////////////////////////////////////////
/// Begin main cycle
/////////////////////////////////////////////////////////////////////////////

if($_USER['id'] == $_CHAT['id'])
  //Private chat
{

    // main query for user chat 
    $r=mysqli_query($mysql,"select * from save where user_id='".$_USER['id']."'");
    $ar=null;
    if (mysqli_num_rows($r)>0){
	$ar=mysqli_fetch_assoc($r);
    }
    //if(preg_match('@^(?:/w)?([^\s]+)@i', $_TEXT,$who)){ //select student or prepod
    
    $num=''; //current id in the command
    if (strlen($_TEXT)>2){
	$end=strpos($_TEXT,' ');
	$end=($end===false)?strlen($_TEXT)-1:$end;
	$num=mysqli_real_escape_string($mysql,trim(substr($_TEXT,2,$end)));
    }


    if (strpos($_TEXT,'/h')===0){
	//Get help
	sendHelp($_CHAT['id']);
	exit();
    } //get help

    if (strpos($_TEXT,'/start')===0){
	//clear all and create new record
	sendHelp($_CHAT['id']);
	// clear state for user
	mysqli_query($mysql,"update save set last_state=0  where user_id='".$_USER['id']."'");
	mysqli_query($mysql,"delete from reminder  where user_id='".$_USER['id']."'");
	mysqli_query($mysql,"delete from cache where user_id='".$_USER['id']."'");
	$ar['last_state']=0;
	//exit();
    }// /start

///////////////////////////////////
/// state machine help:
///  0 - after /start
///  1 - after select /w - student or prepod
///  2 - after select /f - select facult
///  3 - after select /p or /k - select potok or kafedra
///  4 - after select /g or /p - select group or prepod name - FINAL state
///  5 - after select /rs1 - reminder by time
///  6 - after select /rs2 - reminder before time
///////////////////////////////////

    if (strpos($_TEXT,'/w')===0){
    // student or prepod? /w1 /w2
	//$who=substr($_TEXT,2,3);
        $title=($num==1)?'студент':'преподаватель';

	if (mysqli_num_rows($r)==0){ //new
	    mysqli_query($mysql,"insert into save (last_state,who_id,user_id) values ('1','$num','".$_USER['id']."')");
	    //re-query new data array
	    $r=mysqli_query($mysql,"select * from save where user_id='".$_USER['id']."'");
	    $ar=mysqli_fetch_assoc($r);
	    sendMessage($_CHAT['id'], 'Вы добавлены как новый пользователь '.$title,''); 
	}else { 
	    mysqli_query($mysql,"update save set last_state=1,who_id='$num' where user_id='".$_USER['id']."'");

	    $ar['who_id']=$num;
        sendMessage($_CHAT['id'], 'Вы обновлены как '.$title, '');

	} // 
    // state machine pos 1
    $ar['last_state']=1;
    } // /w


    if ((mysqli_num_rows($r)>0) || ($ar!=null)){ // if user not null

	if (strpos($_TEXT,'/f')===0){
	     if ($ar['last_state']!=1){
		//sendMessage($_CHAT['id'], 'Для изменения сначала выберите преподаватель или студент', '');
		sendMenu1($_CHAT['id'],'Для изменения сначала выберите преподаватель или студент');
//		exit();
	    } // error state
	    $fn=substr($_TEXT,strpos($_TEXT,'-'),strlen($_TEXT));
	    mysqli_query($mysql,"update save set last_state=2,facult_id='$num',facult_name='$fn'  where user_id='".$_USER['id']."'");
	    $ar['last_state']=2; // next state
	    $ar['facult_id']=$num;
	} // /f
    
    	if (strpos($_TEXT,'/p')===0){	//select potok
	     if ($ar['last_state']>2){
		//sendMessage($_CHAT['id'], 'Для изменения сначала выберите факультет /w'.$ar['who_id'], '');
		sendMenu2($_CHAT['id'],'Для изменения сначала выберите факультет /w'.$ar['who_id']);
//		exit();
	    } //error state
	    $fn=substr($_TEXT,strpos($_TEXT,'-'),strlen($_TEXT));
	    mysqli_query($mysql,"update save set last_state=3,potok_id='$num',porok_name='$fn'  where user_id='".$_USER['id']."'");
	    //correcting local copy of user data
	    $ar['last_state']=3;
	    $ar['potok_id']=$num;
	} // /p

	if (strpos($_TEXT,'/g')===0){ //select group
	    if ($ar['last_state']>3){
		//sendMessage($_CHAT['id'], 'Сначала выберите поток /f'.$ar['facult_id'], '');
		sendMenu2($_CHAT['id'],'Сначала выберите поток /f'.$ar['facult_id']);
//		exit();
	    } //error state
	    $fn=substr($_TEXT,strpos($_TEXT,'-'),strlen($_TEXT));
	    mysqli_query($mysql,"update save set last_state=4,group_id='$num',group_name='$fn'  where user_id='".$_USER['id']."'");

	    //correcting local copy of user data
	    $ar['last_state']=4;
	    $ar['group_id']=$num;
	} // /g

	if (strpos($_TEXT,'/k')===0){ //select kafedra
	     if ($ar['last_state']!=2){
		//sendMessage($_CHAT['id'], 'Сначала выберите факультет /w'.$ar['who_id'], '');
		sendMenu2($_CHAT['id'],'Сначала выберите факультет /w'.$ar['who_id'] );
//		exit();
	    }
	    $fn=substr($_TEXT,strpos($_TEXT,'-'),strlen($_TEXT));
	    mysqli_query($mysql,"update save set last_state=3,kafedra_id='$num',kafedra_name='$fn'  where user_id='".$_USER['id']."'");

	    //correcting local copy of user data
	    $ar['last_state']=3;
	    $ar['kafedra_id']=$num;
	} // /k

	if (strpos($_TEXT,'/t')===0){
	     if ($ar['last_state']<3){
    		//sendMessage($_CHAT['id'], 'Сначала выберите кафедру /f'.$ar['facult_id'], '');
		sendMenu2($_CHAT['id'],'Сначала выберите кафедру /f'.$ar['facult_id'].' и факультет /w'.$ar['who_id']);
//		exit();
	    } // error state
	    $fn=substr($_TEXT,strpos($_TEXT,'-'),strlen($_TEXT));
	    mysqli_query($mysql,"update save set last_state=4,prep_id='$num',prep_name='$fn'  where user_id='".$_USER['id']."'");

	    //correcting local copy of user data
	    $ar['last_state']=4;
	    $ar['prep_id']=$num;
	} // /t


	if (strpos($_TEXT,'/show')===0){
	    sendMessage($_CHAT['id'],'Настройки - '.$ar['facult_name'].', '.$ar['porok_name'].
			', '.$ar['group_name'].', '.$ar['kafedra_name'].', '.$ar['prep_name'], '');

	} // /show

	//$r=mysqli_query($mysql,"select * from save where user_id='".$_USER['id']."'");
	//$ar=mysqli_fetch_assoc($r);
    
	$state=$ar['last_state']; // cache
	$data=''; // Text for user will be here

	if ($ar['who_id']==1) //student

	{
	if ($state==1){ // select fac
		$data="Выберите факультет:\n";
		$fac=getOSUData('facult',0,1);
		foreach ($fac['list'] as $f){
		    $data.="/f".$f['id'].' - '.$f['name']."\n";
		}
	    } // if 1
	if ($state==2){ // select cource
	    $data="Выберите курс:\n";
	    $fac=getOSUData('potok',$num,1);
	    foreach ($fac['list'] as $f){
		$data.="/p".$f['id'].' - '.$f['name']."\n";
	    }
	}// if 2

	if ($state==3){ // select group
	    
	    $data="Выберите группу:\n";
	    $fac=getOSUData('group',"$num&facult=".$ar['facult_id'],1);
	    foreach ($fac['list'] as $f){
		$data.="/g".$f['id'].' - '.$f['name']."\n";
	    }
	}// if 2

    } // if who=1

    if ($ar['who_id']==2) //prepod
    {
	if ($state==1) // select fac
	{

	    $data="Выберите факультет:\n";
	    $fac=getOSUData('facult',0,2);
	    foreach ($fac['list'] as $f){
		$data.="/f".$f['id'].' - '.$f['name']."\n";
	    } // 
	} // fac

	if ($state==2) // select kaf
	{
    
	    $data="Выберите кафедру:\n";
	    $fac=getOSUData('kafedra',$ar['facult_id'],2);
	    foreach ($fac['list'] as $f){
		$data.="/k".$f['id'].' - '.$f['name']."\n";
	    }
	} // kaf
	if ($state==3) // select prep
	{

	    $data="Выберите преподавателя:\n";
	    $fac=getOSUData('prep',$ar['kafedra_id'],2);
	    foreach ($fac['list'] as $f){
		$data.="/t".$f['id'].' - '.$f['name']."\n";
	    }
	}
    }// who=2 prepod

    if (($state>4)&&(strpos($_TEXT,'/')===false)) // parse reminder
    {
        if ($state==5){ // time
	    $time=date("H:i:00",strtotime(mysqli_real_escape_string($mysql,trim($_TEXT))));
	    if($time){
		mysqli_query($mysql,"insert into reminder (user_id, timer, type, enabled) values ".
			"('".$_USER['id']."','$time',1,1)");
		$state=4;
    		$_TEXT='/rs';
		mysqli_query($mysql,"update save set last_state=4 where user_id='".$_USER['id']."'");
	    } else
	    {
    	        sendMessage($_CHAT['id'],"Неправильный формат, введите заново или отмените /undo", '');
	    }
    
	}
        if ($state==6){ // before
	    $time=date("H:i:00",mktime(0,mysqli_real_escape_string($mysql,trim($_TEXT))));
	    if($time){
		mysqli_query($mysql,"insert into reminder (user_id, timer, type, enabled) values ".
			"('".$_USER['id']."','$time',2,1)");
		$state=4;
    		$_TEXT='/rs';
		mysqli_query($mysql,"update save set last_state=4 where user_id='".$_USER['id']."'");

////////////////////////////////////
//// ADD TO CACHE current schedule
///////////////////////////////////
$rr=mysqli_query($mysql,"select type,timer,save.user_id,who_id,prep_id,facult_id,potok_id,group_id from reminder".
                " left join save on reminder.user_id=save.user_id ".
                " where enabled=1 and type=2 and save.user_id='".$_USER['id']."'");
if (mysqli_num_rows($r)>0){
    while($ar2=mysqli_fetch_assoc($rr)){
        if ($ar2['who_id']==2){ //  prepod
            //print 'go';

        //    $rasp=getOSUData('rasp',$ar2['prep_id'],2);
            $data="Пары:\n";
            $ras=getOSUData('rasp',$ar2['prep_id'],2);
            $dat= time();
            $dd=strtoupper(date("d-M-y",$dat));
            $rasp=array();
//          if ($ar2)
            foreach ($ras as $f){
                if ($f['DAY']==$dd)
                {
                    $rasp[]=$f['DESCRIPTION'].' - '.$f['AUD_ALL_LPAD'].'-'.$f['NAMEGROUP'].' '.$f['SHORT_NAME_SUB'].'('.$f['TYPEZAN_SHORT_NAME'].")";
                }
            }//foreach
            if (count($rasp)!=0){ //no lessons
                sort($rasp);
                $time=substr($rasp[0],0,5);
                $data.=mysqli_real_escape_string($mysql,implode("\n",$rasp));
                mysqli_query($mysql,"insert into cache (user_id,timer,dat,message) values ".
			    "('".$ar['user_id']."',TIMEDIFF('$time','".$ar2['timer']."'),now(),'$data')".
			    "ON DUPLICATE KEY UPDATE timer=TIMEDIFF('$time','".$ar2['timer']."'), dat=now(), message='$data'");
            }
//      sendMenu4($ar['user_id'],$data);
//          mysqli_query($mysql,"");
//print $data;
        }
        if ($ar['who_id']==1){ //  student
//            $rasp=getOSUData('rasp',$ar['group_id'],1);
        }

    } // while
    } // not 0






	    } else
	    {
    	        sendMessage($_CHAT['id'],"Неправильный формат, введите заново или отмените /undo", '');
	    }

	}
    }
    if (($state>=4)) // select reminder
    {
	if (((strpos($_TEXT,'/day')===0)||(strpos($_TEXT,'/z')===0)) && ($ar['who_id']==1)){
	// if today or tomorrow
	    $data="Пары:\n";
	    $ras=getOSUData('rasp',$ar['group_id'].'&potok='.$ar['potok_id'].'&facult='.$ar['facult_id'],1);
	    $dat= (strpos($_TEXT,'/z')===0)? strtotime("+1 day") : time();
	    $dd=strtoupper(date("d-M-y",$dat));
	    $rasp=array();
//	    if ($ar2)
	    foreach ($ras as $f){
		if ($f['DAY']==$dd)
		{
		    $rasp[]=$f['DESCRIPTION'].' - '.$f['AUD_ALL_LPAD'].'-'.$f['FIO_SOKR'].' '.$f['SHORT_NAME_SUB'].'('.$f['TYPEZAN_SHORT_NAME'].")";
		}
	    }//foreach
	    if (count($rasp)==0){ //no lessons
		$data='Нет пар';
	    } else {
		sort($rasp);
		$data.=implode("\n",$rasp);
	    }
		mysqli_query($mysql,"update save set last_state='4' where user_id='".$_USER['id']."'");

	}
	if (((strpos($_TEXT,'/day')===0)||(strpos($_TEXT,'/z')===0)) && ($ar['who_id']==2)){
	// if today or tomorrow
	    $data="Пары:\n";
	    $ras=getOSUData('rasp',$ar['prep_id'],2);
	    $dat= (strpos($_TEXT,'/z')===0)? strtotime("+1 day") : time();
	    $dd=strtoupper(date("d-M-y",$dat));
	    $rasp=array();
//	    if ($ar2)
	    foreach ($ras as $f){
		if ($f['DAY']==$dd)
		{
		    $rasp[]=$f['DESCRIPTION'].' - '.$f['AUD_ALL_LPAD'].'-'.$f['NAMEGROUP'].' '.$f['SHORT_NAME_SUB'].'('.$f['TYPEZAN_SHORT_NAME'].")";
		}
	    }//foreach
	    if (count($rasp)==0){ //no lessons
		$data='Нет пар';
	    } else {
		sort($rasp);
		$data.=implode("\n",$rasp);
	    }
		mysqli_query($mysql,"update save set last_state='4' where user_id='".$_USER['id']."'");

	} // /day
///create table reminder (id int primary key auto_increment, 
//user_id int, type int, timer TIME, enabled int default '0' );	
	if (strpos($_TEXT,'/rd')===0){ //delete
	    $id=substr($_TEXT,3,strlen($_TEXT));
	    mysqli_query($mysql,"delete from reminder where id='$id' and user_id='".$_USER['id']."'");
	    $_TEXT='/rs';
	    }
	if (strpos($_TEXT,'/rp')===0){ //pause
	    $id=substr($_TEXT,3,strlen($_TEXT));
	    mysqli_query($mysql,"update reminder set enabled='0' where id='$id' and user_id='".$_USER['id']."'");
	    $_TEXT='/rs';
	    }

	if (strpos($_TEXT,'/rs')===0){
	    $rr2=mysqli_query($mysql,"select * from reminder where user_id='".$_USER['id']."'");
	    

		$cnt=mysqli_num_rows($rr2);
	     if ($cnt>0) {
	      $data="Текущие напоминания ($cnt):\n";
		$i=1;
	      while($ar2=mysqli_fetch_assoc($rr2)){

		$lim0=($ar2['enabled']!=1)?'(выкл) ':''; //italic if disabled
		$lim1=($ar2['enabled']!=1)?'<i>':'<b>'; //italic if disabled
		$lim2=($ar2['enabled']!=1)?'</i>':'</b>';
		$endis=($ar2['enabled']!=1)?'включить /re':'выключить /rp';
		if ($ar2['type']==1) { // for time
			$data.="$i.$lim1  В ".substr($ar2['timer'],0,5).", если есть пары $lim2".
				"(удалить /rd".$ar2['id'].", $endis".$ar2['id'].")\n";
		} else
		if ($ar2['type']==2) { // before N minutes
			$data.="$lim1 $i. За ".$ar2['timer']." перед парой $lim2(удалить /rd".$ar2['id'].
					", $endis".$ar2['id'].")\n";
		}
		$i++;
	      } //foreach
	    } //if

		if (strpos($_TEXT,'/rs1')===0){
    	        $data.="Введите время оповещения (чч:мм или чч.мм):";
		mysqli_query($mysql,"update save set last_state='5' where user_id='".$_USER['id']."'");
		}
		if (strpos($_TEXT,'/rs2')===0){
    	        $data.="Введите за сколько минут оповестить перед ПЕРВОЙ парой:";
		mysqli_query($mysql,"update save set last_state='6' where user_id='".$_USER['id']."'");
		}
	}

	if ($data==''){$data="Выберите действие в меню";}
	sendMenu4($_CHAT['id'],$data);
	//    exit;
    } else // state=4
{ // other states
sendMessage($_CHAT['id'], $data, '' );
}
  } // if user not null

}// main if private chat

?>
