<?php

error_log(MESSENGER."$_TEXT");
/*
create table save(id int primary key, user_id int, last_state int, 
who_id int,facult_id int, facult_name varchar(10), kafedra_id int, 
kafedra_name varchar(10), prep_id int, prep_name varchar(20), group_id int, 
group_name varchar(10), potok_id int, potok_name varchar(10));

*/


//////////////////////////////////////////////////////////////////////////////
/// Begin main cycle
/////////////////////////////////////////////////////////////////////////////

//if($_USER['id'] == $_CHAT['id'])
  //Private chat
{

    // main query for user chat 
    $r=mysqli_query($mysql,"select * from save where viber_id='".$_USER['id']."' OR user_id='".
	$_USER['id']."'");
    $ar=null;
    if (mysqli_num_rows($r)>0){
	$ar=mysqli_fetch_assoc($r);
    }
    //if(preg_match('@^(?:/w)?([^\s]+)@i', $_TEXT,$who)){ //select student or prepod
    
    $num=''; //current id in the command
    if (strlen($_TEXT)>2){
	$end=strpos($_TEXT,' ');
	$end2=strpos($_TEXT,'-');
	$end=($end===false)?strlen($_TEXT):$end;
	$end2=($end2===false)?strlen($_TEXT):$end2;
	$end0=($end2>$end)?$end:$end2;
	$num=mysqli_real_escape_string($mysql,trim(substr($_TEXT,2,$end0-1)));
	error_log($end.$end2.$end0.$num);
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
	mysqli_query($mysql,"update save set last_state=0  where viber_id='".$_USER['id']."' OR user_id='".$_USER['id']."'");
	mysqli_query($mysql,"delete from reminder  where  viber_id='".$_USER['id']."' OR user_id='".$_USER['id']."'");
	mysqli_query($mysql,"delete from cache where  viber_id='".$_USER['id']."' OR user_id='".$_USER['id']."'");
	$ar['last_state']=0;
	exit();
    }// /start
	
    if (strpos($_TEXT,'/noreminder')===0){
	// clear state for user
	mysqli_query($mysql,"delete from reminder  where  viber_id='".$_USER['id']."' OR user_id='".$_USER['id']."'");
	mysqli_query($mysql,"delete from cache where  viber_id='".$_USER['id']."' OR user_id='".$_USER['id']."'");
	sendMenu4($_CHAT['id'],"Очищено");
	$_TEXT="/rs"; //redirect to reminder list
    }// /noreminder

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
	    mysqli_query($mysql,"insert into save (last_state,who_id,user_id,viber_id) values ('1','$num','".$_USER['id']."','".$_USER['id']."')");
	    //re-query new data array
	    $r=mysqli_query($mysql,"select * from save where  viber_id='".$_USER['id']."' OR user_id='".$_USER['id']."'");
	    $ar=mysqli_fetch_assoc($r);
	    sendMenu1($_CHAT['id'], 'Вы добавлены как новый пользователь '.$title,''); 
	}else { 
	    mysqli_query($mysql,"update save set last_state=1,who_id='$num' where  viber_id='".$_USER['id']."' OR  user_id='".$_USER['id']."'");

	    $ar['who_id']=$num;
        sendMenu1($_CHAT['id'], 'Вы обновлены как '.$title);

	} // 
    // state machine pos 1
    $ar['last_state']=1;
    } // /w


    if ((mysqli_num_rows($r)>0) || ($ar!=null)){ // if user not null

	if (strpos($_TEXT,'/f')===0){
	     if ($ar['last_state']!=1){
		//sendMsg($_CHAT['id'], 'Для изменения сначала выберите преподаватель или студент', '');
		sendMenu1($_CHAT['id'],'Для изменения сначала выберите преподаватель или студент');
//		exit();
	    } // error state
	    $fn=substr($_TEXT,strpos($_TEXT,'-')+1,strlen($_TEXT));
	    mysqli_query($mysql,"update save set last_state=2,facult_id='$num',facult_name='$fn'  where  viber_id='".$_USER['id']."' OR user_id='".$_USER['id']."'");
	    $ar['last_state']=2; // next state
	    $ar['facult_id']=$num;
		error_log(var_export($ar, true));
	} // /f
    
    	if (strpos($_TEXT,'/p')===0){	//select potok
	     if ($ar['last_state']>2){
		//sendMsg($_CHAT['id'], 'Для изменения сначала выберите факультет /w'.$ar['who_id'], '');
		sendMenu2($_CHAT['id'],'Для изменения сначала выберите факультет /w'.$ar['who_id']);
//		exit();
	    } //error state
	    $fn=substr($_TEXT,strpos($_TEXT,'-'),strlen($_TEXT));
	    mysqli_query($mysql,"update save set last_state=3,potok_id='$num',potok_name='$fn'  where  viber_id='".$_USER['id']."' OR  user_id='".$_USER['id']."'");
	    //correcting local copy of user data
	    $ar['last_state']=3;
	    $ar['potok_id']=$num;
	} // /p

	if (strpos($_TEXT,'/g')===0){ //select group
	    if ($ar['last_state']>3){
		//sendMsg($_CHAT['id'], 'Сначала выберите поток /f'.$ar['facult_id'], '');
		sendMenu2($_CHAT['id'],'Сначала выберите поток /f'.$ar['facult_id']);
//		exit();
	    } //error state
	    $fn=substr($_TEXT,strpos($_TEXT,'-'),strlen($_TEXT));
	    mysqli_query($mysql,"update save set last_state=4,group_id='$num',group_name='$fn'  where  viber_id='".$_USER['id']."' OR  user_id='".$_USER['id']."'");

	    //correcting local copy of user data
	    $ar['last_state']=4;
	    $ar['group_id']=$num;
	} // /g

	if (strpos($_TEXT,'/k')===0){ //select kafedra
	     if ($ar['last_state']!=2){
		//sendMsg($_CHAT['id'], 'Сначала выберите факультет /w'.$ar['who_id'], '');
		sendMenu2($_CHAT['id'],'Сначала выберите факультет /w'.$ar['who_id'] );
//		exit();
	    }
	    $fn=substr($_TEXT,strpos($_TEXT,'-'),strlen($_TEXT));
	    mysqli_query($mysql,"update save set last_state=3,kafedra_id='$num',kafedra_name='$fn'  where  viber_id='".$_USER['id']."' OR  user_id='".$_USER['id']."'");

	    //correcting local copy of user data
	    $ar['last_state']=3;
	    $ar['kafedra_id']=$num;
	} // /k

	if (strpos($_TEXT,'/t')===0){
	     if ($ar['last_state']<3){
    		//sendMsg($_CHAT['id'], 'Сначала выберите кафедру /f'.$ar['facult_id'], '');
		sendMenu2($_CHAT['id'],'Сначала выберите кафедру /f'.$ar['facult_id'].' и факультет /w'.$ar['who_id']);
//		exit();
	    } // error state
	    $fn=substr($_TEXT,strpos($_TEXT,'-'),strlen($_TEXT));
	    mysqli_query($mysql,"update save set last_state=4,prep_id='$num',prep_name='$fn'  where  viber_id='".$_USER['id']."' OR  user_id='".$_USER['id']."'");

	    //correcting local copy of user data
	    $ar['last_state']=4;
	    $ar['prep_id']=$num;
	} // /t


	if (strpos($_TEXT,'/show')===0){
	    sendMenu2($_CHAT['id'],'Настройки - '.$ar['facult_name'].', '.$ar['potok_name'].
			', '.$ar['group_name'].', '.$ar['kafedra_name'].', '.$ar['prep_name']);

	} // /show

	//$r=mysqli_query($mysql,"select * from save where  viber_id='".$_USER['id']."' OR  user_id='".$_USER['id']."'");
	//$ar=mysqli_fetch_assoc($r);
    
	$state=$ar['last_state']; // cache
	$data=''; // Text for user will be here
    $lines=[];
	if ($ar['who_id']==1) //student

	{
	if ($state==1){ // select fac
		$data="Выберите факультет:\n";
		$fac=getOSUData('facult',0,1);
		foreach ($fac['list'] as $f){
		    $data.="/f".$f['id'].' - '.$f['name']."\n";
			$lines[]="/f".$f['id'].' - '.$f['name']."\n";
		}
	    } // if 1
	if ($state==2){ // select cource
	    $data="Выберите курс:\n";
	    $fac=getOSUData('potok',$num,1);
	    foreach ($fac['list'] as $f){
		$data.="/p".$f['id'].' - '.$f['name']."\n";
		$lines[]="/p".$f['id'].' - '.$f['name']."\n";
	    }
	}// if 2

	if ($state==3){ // select group
	    
	    $data="Выберите группу:\n";
	    $fac=getOSUData('group',"$num&facult=".$ar['facult_id'],1);
	    foreach ($fac['list'] as $f){
		$data.="/g".$f['id'].' - '.$f['name']."\n";
		$lines[]="/g".$f['id'].' - '.$f['name']."\n";
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
		$lines[]="/f".$f['id'].' - '.$f['name']."\n";
	    } // 
	} // fac

	if ($state==2) // select kaf
	{
    
	    $data="Выберите кафедру:\n";
	    $fac=getOSUData('kafedra',$ar['facult_id'],2);
	    foreach ($fac['list'] as $f){
		$data.="/k".$f['id'].' - '.$f['name']."\n";
		$lines[]="/k".$f['id'].' - '.$f['name']."\n";
	    }
	} // kaf
	if ($state==3) // select prep
	{

	    $data="Выберите преподавателя:\n";
	    $fac=getOSUData('prep',$ar['kafedra_id'],2);
	    foreach ($fac['list'] as $f){
		$data.="/t".$f['id'].' - '.$f['name']."\n";
		$lines[]="/t".$f['id'].' - '.$f['name']."\n";
	    }
	}
    }// who=2 prepod

    if (($state>4)&&(strpos($_TEXT,'/')===false)) // parse reminder
    {
        if ($state==5){ // time reminder
	    //TODO  make input check
		
		$time=date("H:i:00",strtotime(mysqli_real_escape_string($mysql,trim($_TEXT))));
	    if($time){
		mysqli_query($mysql,"insert into reminder (user_id, timer, type, enabled, messenger, viber_id) values ".
			"('".$_USER['id']."','$time',1,1,'".MESSENGER."','".$_USER['id']."')");
		$state=4;  			// resore state
    		$_TEXT='/rs'; // redirect to list of remiders
		mysqli_query($mysql,"update save set last_state=4 where  viber_id='".$_USER['id']."' OR user_id='".$_USER['id']."'");
	    } else //if $time
	    {
    	        sendMenu2($_CHAT['id'],"Неправильный формат, введите заново или отмените /undo");
	    }
    
	}
        if ($state==6){ // before
		//TODO  make input check
		
	    $time=date("H:i:00",mktime(0,mysqli_real_escape_string($mysql,trim($_TEXT))));
	    if($time){
		mysqli_query($mysql,"insert into reminder (user_id, timer, type, enabled,messenger,viber_id) values ".
			"('".$_USER['id']."','$time',2,1,'".MESSENGER."','".$_USER['id']."' )");
		$state=4;
    		$_TEXT='/rs';
		mysqli_query($mysql,"update save set last_state=4 where  viber_id='".$_USER['id']."' OR  user_id='".$_USER['id']."'");

		////////////////////////////////////
		//// ADD TO CACHE current schedule
		///////////////////////////////////
		$rr=mysqli_query($mysql,"select type,timer,reminder.viber_id,reminder.messenger,save.user_id,who_id,prep_id,facult_id,potok_id,group_id from reminder".
                " left join save on (reminder.user_id=save.user_id ot reminder.viber_id=save.viber_id)".
                " where enabled=1 and type=2 and  (viber_id='".$_USER['id']."' OR save.user_id='".$_USER['id']."')");
		  if (mysqli_num_rows($r)>0){
			while($ar2=mysqli_fetch_assoc($rr)){
				$date=strtoupper(date("d-M-y",time()));
		
				$rasp= lessonsList($ar2['who_id'], $date,$ar2['prep_id'],$ar2['group_id'],
		                   $ar2['potok_id'],$ar2['facult_id']); //universal
        
			if (count($rasp)!=0){ //no lessons
                sort($rasp);
                $time=substr($rasp[0],0,5);
                $data.=mysqli_real_escape_string($mysql,implode("\n",$rasp)); // full list
                mysqli_query($mysql,"insert into cache (user_id,timer,dat,message,messenger,viber_id) values ".
			    "('".$ar['user_id']."',TIMEDIFF('$time','".$ar2['timer']."'),now(),'$data','".MESSENGER."','".$_USER['id']."')".
			    "ON DUPLICATE KEY UPDATE timer=TIMEDIFF('$time','".$ar2['timer']."'), dat=now(), message='$data',".
				"messenger='".MESSENGER."', viber_id='".$_USER['id']."' "	);
				}
			} // while
		  } // not 0

	    } else   //if $time error format
	    {
    	        sendMenu4($_CHAT['id'],"Неправильный формат, введите заново или отмените /undo");
	    }
	}
    }
    if (($state>=4)) // select reminder
    {
	if (((strpos($_TEXT,'/day')===0)||(strpos($_TEXT,'/z')===0))){
	// if today or tomorrow
	    $data="Пары:\n";
	    $dat= (strpos($_TEXT,'/z')===0)? strtotime("+1 day") : time();
	    $dd=strtoupper(date("d-M-y",$dat));
		$rasp= lessonsList($ar['who_id'], $dd,$ar['prep_id'],$ar['group_id'],
		                   $ar['potok_id'],$ar['facult_id']); //universal

	    if (count($rasp)==0){ //no lessons
		$data='Нет пар';
	    } else {
		sort($rasp);
		$data.=implode("\n",$rasp);
	    }
		mysqli_query($mysql,"update save set last_state='4' where  viber_id='".$_USER['id']."' OR  user_id='".$_USER['id']."'");
	}
///create table reminder (id int primary key auto_increment, 
//user_id int, type int, timer TIME, enabled int default '0' );	
	if (strpos($_TEXT,'/rd')===0){ //delete
	    $id=substr($_TEXT,3,strlen($_TEXT));
	    mysqli_query($mysql,"delete from reminder where id='$id' and ( viber_id='".$_USER['id']."' OR user_id='".$_USER['id']."')");
	    $_TEXT='/rs';
	    }
	if (strpos($_TEXT,'/rp')===0){ //pause
	    $id=substr($_TEXT,3,strlen($_TEXT));
	    mysqli_query($mysql,"update reminder set enabled='0' where id='$id' and ( viber_id='".$_USER['id']."' OR user_id='".$_USER['id']."')");
	    $_TEXT='/rs';
	    }

	if (strpos($_TEXT,'/rs')===0){
	    $rr2=mysqli_query($mysql,"select * from reminder where  viber_id='".$_USER['id']."' OR user_id='".$_USER['id']."'");
	    

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
		mysqli_query($mysql,"update save set last_state='5' where  viber_id='".$_USER['id']."' OR user_id='".$_USER['id']."'");
		}
		if (strpos($_TEXT,'/rs2')===0){
    	        $data.="Введите за сколько минут оповестить перед ПЕРВОЙ парой:";
		mysqli_query($mysql,"update save set last_state='6' where  viber_id='".$_USER['id']."' OR user_id='".$_USER['id']."'");
		}
	}

	if ($data==''){$data="Выберите действие в меню";}
	   sendMenu4($_CHAT['id'],$data);
	//    exit;
    } else // state=4
{ // other states
	if ($state<=4){
	   if (!INLINE){
	   sendInlineMenu2($_CHAT['id'],"".$data,$lines);	} else
	   {sendMsg($_CHAT['id'],"".$data);}
	} else{
		sendMenu2($_CHAT['id'],"".$data);
	}
//sendMsg($_CHAT['id'], $data, '' );
}
  } // if user not null

}// main if private chat

?>
