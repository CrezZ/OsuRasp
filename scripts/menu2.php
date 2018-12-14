<?php

error_log(MESSENGER."-$_TEXT\n");


//////////////////////////////////////////////////////////////////////////////
/// Begin main cycle
/////////////////////////////////////////////////////////////////////////////

//if($_USER['id'] == $_CHAT['id'])
  //Private chat
{
 $num=''; //current id in the command
 $fn='';  // Text part of command (if exists)
    if (strlen($_TEXT)>2){
	$beg=strcspn($_TEXT,"1234567890");
	$end=strspn($_TEXT,"1234567890",$beg);
//	preg_match('/^\D*(?=\d)/', $_TEXT, $m);
//	$beg=isset($m[0]) ? strlen($m[0]) :2;
	$num=mysqli_real_escape_string($mysql,trim(substr($_TEXT,$beg,$end)));
	//error_log($end.$end2.$end0.$num);
	$fn=mb_strtoupper(trim(substr($_TEXT,strcspn($_TEXT,'- :')+1,strlen($_TEXT))," \t\n\r\0\x0B-"));

    }
    // main query for user chat 

	

  if (strpos($_TEXT,'/fb')===0){  // feedback
	sendMenu2($_CHAT['id'],'Введите свое замечание или пожелание, можете оставить свой контакт или попросить связаться через этот мессенджер');
	mysqli_query($mysql,"update save set last_state='10' where  last_user_id=user_id and viber_id='".$_USER['id']."' ");
	exit;
	}

  if (strpos($_TEXT,'/undo')===0){  // feedback
	sendMenu4($_CHAT['id'],'Отменено');
	mysqli_query($mysql,"update save set last_state='4' where  last_user_id=user_id and viber_id='".$_USER['id']."' ");
	exit;
	}
  
  
	
   if (strpos($_TEXT,'/dl')===0){  // dl<$id>
	// delete virtual user
	
		  //$num=substr($_TEXT,3,strlen($_TEXT));
	 if (is_numeric($num)) { //not error
    
		mysqli_query($mysql,"delete from save  where user_id='$num' and viber_id='".$_USER['id']."' ");
		mysqli_query($mysql,"delete from reminder  where  user_id='$num' and viber_id='".$_USER['id']."' ");
		mysqli_query($mysql,"delete from cache where user_id='$num' and viber_id='".$_USER['id']."' ");
		//set minimal user_id as main user (zero);
		mysqli_query($mysql," update save as t1 inner join (select min(user_id) as minid from save where viber_id='".
		$_USER['id']."') as t2  set user_id=0  where t1.user_id=t2.minid and viber_id='".
		$_USER['id']."' ");
		
	
		sendMsg($_CHAT['id'],"Удален $num");
		//sendMsg($_CHAT['id'],"Переключен на основного пользователя");
		  }
		 
	//sendHelp($_CHAT['id']);
	//exit;
	$_TEXT="/w"; //redirect to  list of users
		  
    }// /dl

	    if (strpos($_TEXT,'/ch')===0){   // /ch<$id>s<$who>
	// change virtual user
		  //$num=substr($_TEXT,3,strlen($_TEXT));
		  if (is_numeric($num)) { //not error
    //TODO Check last_id exists

	mysqli_query($mysql,"update save set last_user_id='$num' where  viber_id='".$_USER['id']."' ");
	
	 $b1=strcspn($_TEXT,"1234567890");
	 $e1=strspn($_TEXT,"1234567890",$b1);
	 $b2=strcspn($_TEXT,"1234567890",$b1+$e1);
	 $e2=strspn($_TEXT,"1234567890",$b1+$e1+$b2);
	 $who=substr($_TEXT,$b1+$e1+$b2,$e2);
	sendMsg($_CHAT['id'],"Переключен на $num ");	
	if (is_numeric($who)) { //not error
				   // update who 
					mysqli_query($mysql,"update save set last_state='1', who_id='$who' where user_id='$num' and viber_id='".$_USER['id']."' ");
				  }
	 
		  }
	//sendHelp($_CHAT['id']);
	//exit;
	//$_TEXT="/"; //redirect to reminder list
		  
    }// /ch
	
	
	
    $firstr=mysqli_query($mysql,"select * from save where  viber_id='".$_USER['id']."' order by user_id");
	
    $firstar=mysqli_fetch_assoc($firstr);
	$last_id=0;
	$r=null;
	$ar=null;
    if (mysqli_num_rows($firstr)>1){
		
		$last_id=(!empty($firstar['last_user_id']))?$firstar['last_user_id']:0;
		
    //TODO Check last_id exists
	$r=mysqli_query($mysql,"select * from save where (user_id='$last_id') and viber_id='".$_USER['id']."' ");
	$ar=mysqli_fetch_assoc($r);
//	$ar=mysqli_fetch_assoc($r);
    } else
	{
		$ar=$firstar;
		$r=$firstr;
	}


  if ($ar['last_state']==10){  // send feedback
	sendMsg($_CHAT['id'],'Ваш отзыв был отправлен ');
	//send msg to admin
	sendMsg(ADMIN_ID,'Отзыв от @'.$_USER['username']."\n $_TEXT",'',ADMIN_MESSENGER);
	mysqli_query($mysql,"update save set last_state='4' where  last_user_id=user_id and viber_id='".$_USER['id']."' ");
	//exit;
	$ar['last_state']=4;
	$_TEXT="/rasp";
	}



	// sendMsg($_CHAT['id'],var_export($ar,true));
    
    if (strpos($_TEXT,'/h')===0){
	//Get help
	sendHelp($_CHAT['id']);
	exit();
    } //get help

    if (strpos($_TEXT,'/start')===0){
		// TODO integrate with virtual users
	//clear all and create new record
	sendHelp($_CHAT['id']);
	// clear state for user
	mysqli_query($mysql,"update save set last_state=0, last_user_id=0  where viber_id='".$_USER['id']."' ");
	mysqli_query($mysql,"delete from save  where  user_id<>0 and viber_id='".$_USER['id']."' ");
	mysqli_query($mysql,"delete from reminder  where  viber_id='".$_USER['id']."' ");
	mysqli_query($mysql,"delete from cache where  viber_id='".$_USER['id']."' ");
	$ar['last_state']=0;
	exit();
    }// /start
	
	if (($ar['last_state']==0)&&(strpos($_TEXT,'/')===false)) {  //all inputs
	sendHelp($_CHAT['id']);
	exit;
	}
	
    if (strpos($_TEXT,'/norem')===0){
	// clear state for user
	mysqli_query($mysql,"delete from reminder  where  viber_id='".$_USER['id']."' ");
	mysqli_query($mysql,"delete from cache where  viber_id='".$_USER['id']."' ");
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

    if ((strpos($_TEXT,'/w')===0) || strpos($_TEXT,'/new')===0){
    // student or prepod? /w1 /w2 /new1 /new2
	//$who=substr($_TEXT,2,3);
        $title=($num==1)?'студент':'преподаватель';
     
	if ((mysqli_num_rows($r)==0)|| strpos($_TEXT,'/new')===0){ //new
	  //if (strpos($_TEXT,'/new')===0)
	  {
		  
		  // TODO escape the string
		  //$num=mysqli_real_escape_string($mysql,substr(trim($_TEXT),4));
		  if (!is_numeric($num) || $num == 0 || $num == null) { //error
			//sendMsg($_CHAT['id'],$num.'--');
			sendHelp($_CHAT['id']);
			exit;
		  }
	  } 
	  $cnt=[0];
	 if (mysqli_num_rows($r)!=0){ // new virtual user
	  $cntr=mysqli_query($mysql,"select max(id) from save where  viber_id='".$_USER['id']."'");
		$cnt=mysqli_fetch_array($cntr);
		$cnt[0]++;
	 }   
	 mysqli_query($mysql,"insert into save (last_state,who_id,user_id,viber_id, last_user_id) values ('1','$num','".($cnt[0])."','".$_USER['id']."','".$cnt[0]."')");
		mysqli_query($mysql,"update save set last_user_id='".$cnt[0]."' where viber_id='".$_USER['id']."' ");	
    //re-query new data array
	    $rr=mysqli_query($mysql,"select * from save where user_id='".$cnt[0]."' and viber_id='".$_USER['id']."' ");
	    $ar=mysqli_fetch_assoc($rr);
	    sendMsg($_CHAT['id'], 'Вы добавлены как новый пользователь '.$title,''); 
	}else { 
		$data="Вы хотите добавить нового виртуального юзера или изменить текущего?\n";
		$menu=array();
		$array=array();
		if (mysqli_num_rows($firstr)>=1){ // there is virtual users
		$data.="Ваши  пользователи (".mysqli_num_rows($firstr)."):\n";
		$i=1;
		
		
		//if ($num>0){  $array[]=['/new'.($num).' Добавить'];}
		//else
		$array[]=['/new1 - Добавить студента', '/new2 - Добавить преподавателя'];
		$ar=$firstar;
			 do{
				 $who=($ar['who_id']==1)?"Студент":"Преподаватель";
				 $gr_id=($ar['who_id']==1)?$ar['group_id']:
									 $ar['prep_id'];
				 $gr_name=($ar['who_id']==1)?$ar['group_name']:
									 $ar['prep_name'];
				 $gr_name=(empty($gr_name))?'(не настроен)':$gr_name;
				 $array[]=[ "/dl".$ar['user_id']." - Удалить $i ".$gr_name,
							 "/ch".$ar['user_id']."s".$ar['who_id']." - Поменять $i ".$gr_name]
							;
				$data.="$i. $who $gr_name \n"; 
				$i++;
			 }	while ($ar=mysqli_fetch_assoc($firstr)); 
		}
		$array[]=$BASE_KEYBOARD; // help and reset
		sendMenu0($_CHAT['id'],$data,'',$array);
		$ar['last_state']=1;
	    exit;

		//mysqli_query($mysql,"update save set last_state=1,who_id='$num' where  viber_id='".$_USER['id']."' ");
	    //$ar['who_id']=$num;
        //sendMenu1($_CHAT['id'], 'Вы обновлены как '.$title);

	} // 
    // state machine pos 1
    $ar['last_state']=1;
    } // /w
	


    if ((mysqli_num_rows($r)>0) || ($ar!=null)){ // if user not null

	if (strpos($_TEXT,'/f')===0){
	     if ($ar['last_state']>2){
		//sendMsg($_CHAT['id'], 'Для изменения сначала выберите преподаватель или студент', '');
		sendMenu1($_CHAT['id'],'Для изменения сначала выберите преподаватель или студент');
//		exit();
	    } // error state
	    //$fn=substr($_TEXT,strpos($_TEXT,'-')+1,strlen($_TEXT));
	    mysqli_query($mysql,"update save set last_state=2,facult_id='$num',facult_name='$fn'  where user_id='$last_id' and viber_id='".$_USER['id']."' ");
	    $ar['last_state']=2; // next state
	    $ar['facult_id']=$num;
		//error_log(var_export($ar, true));
	} // /f
    
    	if (strpos($_TEXT,'/p')===0){	//select potok
	     if ($ar['last_state']>3){
		//sendMsg($_CHAT['id'], 'Для изменения сначала выберите факультет /w'.$ar['who_id'], '');
		sendMenu2($_CHAT['id'],'Для изменения сначала выберите факультет /w'.$ar['who_id']);
//		exit();
	    } //error state
	    //$fn=substr($_TEXT,strpos($_TEXT,'-'),strlen($_TEXT));
	    mysqli_query($mysql,"update save set last_state=3,potok_id='$num',potok_name='$fn'  where  user_id='$last_id' and viber_id='".$_USER['id']."' ");
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
	    
	    mysqli_query($mysql,"update save set last_state=4,group_id='$num',group_name='$fn'  where user_id='$last_id' and viber_id='".$_USER['id']."' ");

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
	    //$fn=substr($_TEXT,strpos($_TEXT,'-'),strlen($_TEXT));
	    mysqli_query($mysql,"update save set last_state=3,kafedra_id='$num',kafedra_name='$fn'  where user_id='$last_id' and viber_id='".$_USER['id']."' ");

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
	    //$fn=substr($_TEXT,strpos($_TEXT,'-'),strlen($_TEXT));
	    mysqli_query($mysql,"update save set last_state=4,prep_id='$num',prep_name='$fn'  where user_id='$last_id' and viber_id='".$_USER['id']."' ");

	    //correcting local copy of user data
	    $ar['last_state']=4;
	    $ar['prep_id']=$num;
	} // /t


	if (strpos($_TEXT,'/show')===0){
		$data="Настройки:\n";
		$i=1;
				$ar=$firstar;
			 do{
				 $who=($ar['who_id']==1)?"Студент":"Преподаватель";
				 $gr_id=($ar['who_id']==1)?$ar['group_id']:
									 $ar['prep_id'];
				 $gr_name=($ar['who_id']==1)?$ar['group_name']:
									 $ar['prep_name'];
				 $kf_name=($ar['who_id']==1)?$ar['potok_name']:
									 $ar['kafedra_name'];
				 
				$data.="$i. $who $gr_name \n"; 
				$data.=mb_strtoupper(" - ".$ar['facult_name'].", ".$kf_name.", ".$gr_name);
				$data.="\n";
				$i++;
			 }	while ($ar=mysqli_fetch_assoc($firstr)); 
	    sendMenu4($_CHAT['id'],$data);
		exit;
	} // /show

	//$r=mysqli_query($mysql,"select * from save where  viber_id='".$_USER['id']."' OR  user_id='".$_USER['id']."'");
	//$ar=mysqli_fetch_assoc($r);
    
	$state=$ar['last_state']; // cache
	$data=''; // Text for user will be here
    $lines=[];
	$who=$ar['who_id'];
	$title=($who==1)?'студент':'преподаватель';
	
		if ($state==1){ // select fac
		$data="Выберите факультет:\n";
		$fac=getOSUData('facult',0,$who);
		foreach ($fac['list'] as $f){
		 //   $data.="/f".$f['id'].' - '.$f['name']."\n";
			$lines[]="/f".$f['id'].' - '.$f['name']."\n";
		}
	    } // if 1
	
	if (($ar['who_id']==1)&&(strpos($_TEXT,'/')===0)) //student

	{

	if ($state==2){ // select course
	    $data="Выберите курс:\n";
	    $fac=getOSUData('potok',$num,1);
	    foreach ($fac['list'] as $f){
		//$data.="/p".$f['id'].' - '.$f['name']."\n";
		$lines[]="/p".$f['id'].' - '.$f['name']."\n";
	    }
	}// if 2

	if ($state==3){ // select group
	    
	    $data="Выберите группу:\n";
	    $fac=getOSUData('group',"$num&facult=".$ar['facult_id'],1);
	    foreach ($fac['list'] as $f){
		//$data.="/g".$f['id'].' - '.$f['name']."\n";
		$lines[]="/g".$f['id'].' - '.$f['name']."\n";
	    }
	}// if 2

    } // if who=1

    if (($ar['who_id']==2)&&(strpos($_TEXT,'/')===0)) //prepod
    {


	if ($state==2) // select kaf
	{
    
	    $data="Выберите кафедру:\n";
	    $fac=getOSUData('kafedra',$ar['facult_id'],2);
	    foreach ($fac['list'] as $f){
		//$data.="/k".$f['id'].' - '.$f['name']."\n";
		$lines[]="/k".$f['id'].' - '.$f['name']."\n";
	    }
	} // kaf
	if ($state==3) // select prep
	{

	    $data="Выберите преподавателя:\n";
	    $fac=getOSUData('prep',$ar['kafedra_id'],2);
	    foreach ($fac['list'] as $f){
		//$data.="/t".$f['id'].' - '.$f['name']."\n";
		$lines[]="/t".$f['id'].' - '.$f['name']."\n";
	    }
	}
    }// who=2 prepod

    if (($state>4)&&(strpos($_TEXT,'/')===false)) // parse reminder
    {
        if ($state==5){ // time reminder
	    //TODO  make input check
		$_TEXT=trim($_TEXT);
		if (preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $_TEXT)){
		$time=date("H:i:00",strtotime(mysqli_real_escape_string($mysql,trim($_TEXT))));
	    if($time){
		mysqli_query($mysql,"insert into reminder (user_id, timer, type, enabled, messenger, viber_id) values ".
			"('".$last_id."','$time',1,1,'".MESSENGER."','".$_USER['id']."')");
		$state=4;  			// resore state
    		$_TEXT='/rs'; // redirect to list of remiders
		mysqli_query($mysql,"update save set last_state=4 where  viber_id='".$_USER['id']."' ");
		
	    }} else //if $time
	    {
    	        sendMsg($_CHAT['id'],"Неправильный формат, введите заново или отмените /undo");
				exit;
	    }
    
	}
        if ($state==6){ // before
		//TODO  make input check
		$_TEXT=trim($_TEXT);
		if (is_numeric($_TEXT)){
			
	    $time=date("H:i:00",mktime(0,mysqli_real_escape_string($mysql,trim($_TEXT))));
	    if($time){
		mysqli_query($mysql,"insert into reminder (user_id, timer, type, enabled,messenger,viber_id) values ".
			"('".$last_id."','$time',2,1,'".MESSENGER."','".$_USER['id']."' )");
		$state=4;
    		$_TEXT='/rs';
		mysqli_query($mysql,"update save set last_state=4 where  viber_id='".$_USER['id']."' ");

		////////////////////////////////////
		//// ADD TO CACHE current schedule
		///////////////////////////////////
		$rr=mysqli_query($mysql,"select type,timer,reminder.viber_id,reminder.messenger,save.user_id,who_id,prep_id,facult_id,potok_id,group_id from reminder".
                " left join save on (reminder.user_id=save.user_id ot reminder.viber_id=save.viber_id)".
                " where enabled=1 and type=2 and  (viber_id='".$_USER['id']."' )");
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

		}} else   //if $time error format
	    {
    	        sendMenu4($_CHAT['id'],"Неправильный формат, введите заново или отмените /undo");
	    }
	}
    }
    if (($state>=4)) // select reminder
    {
	if (((strpos($_TEXT,'/day')===0)||(strpos($_TEXT,'/z')===0))){
		sendChatAction($_CHAT['id'], 'typing');
	// if today or tomorrow
	$ar=$firstar;
	do {
		
	$title=($ar['who_id']==1)?'студент':'преподаватель';
		$gr_name=($ar['who_id']==1)? $ar['group_name']:
									 $ar['prep_name'];
	    $data0="Пары для $title $gr_name:\n";
	    $dat= (strpos($_TEXT,'/z')===0)? strtotime("+1 day") : time();
	    $dd=strtoupper(date("d-M-y",$dat));
		$rasp= lessonsList($ar['who_id'], $dd,$ar['prep_id'],$ar['group_id'],
		                   $ar['potok_id'],$ar['facult_id']); //universal

	    if (count($rasp)==0){ //no lessons
		$data0.='- Нет пар';
	    } else {
		sort($rasp);
		$data0.=implode("\n",$rasp);
	    }
		$data.=$data0."\n";
	} while ($ar=mysqli_fetch_assoc($firstr));
		//mysqli_query($mysql,"update save set last_state='4' where user_id='$last_id' and viber_id='".$_USER['id']."' ");
	} // /day /z
///create table reminder (id int primary key auto_increment, 
//user_id int, type int, timer TIME, enabled int default '0' );	
	if (strpos($_TEXT,'/rd')===0){ //delete
	    $id=substr($_TEXT,3,strlen($_TEXT));
	    mysqli_query($mysql,"delete from reminder where id='$id' and ( viber_id='".$_USER['id']."' )");
	    $_TEXT='/rs';
		$data='Готово';
	    }
	if (strpos($_TEXT,'/rp')===0){ //pause
	    $id=substr($_TEXT,3,strlen($_TEXT));
	    mysqli_query($mysql,"update reminder set enabled='0' where id='$id' and ( viber_id='".$_USER['id']."' )");
	    $_TEXT='/rs';
		$data='Готово';
	    }
	if (strpos($_TEXT,'/re')===0){ //un-pause
	    $id=substr($_TEXT,3,strlen($_TEXT));
	    mysqli_query($mysql,"update reminder set enabled='1' where id='$id' and ( viber_id='".$_USER['id']."' )");
	    $_TEXT='/rs';
		$data='Готово';
	    }

	if (strpos($_TEXT,'/rs')===0){
	    $rr2=mysqli_query($mysql,"select * from reminder where  viber_id='".$_USER['id']."' ");
	    

		$cnt=mysqli_num_rows($rr2);
	     if ($cnt>0) {
	      $data="Текущие напоминания ($cnt):\n";
		$i=1;
	      while($ar2=mysqli_fetch_assoc($rr2)){

		$lim0=($ar2['enabled']!=1)?'(выкл) ':''; //italic if disabled
		if (MESSENGER=='telegram'){
		$lim1=($ar2['enabled']!=1)?'<i>':'<b>'; //italic if disabled
		$lim2=($ar2['enabled']!=1)?'</i>':'</b>';
		$endis=($ar2['enabled']!=1)?'включить /re':'выключить /rp';
		$enddel="$lim2(удалить /rd".$ar2['id'].
					", $endis".$ar2['id'].")";
		}
		if (MESSENGER=='viber')
		{ $adden=($ar2['enabled']!=1)?"/re".$ar2['id']." - Включить $i":"/rp".$ar2['id']." - Выключить $i";
			$lines[]=["/rd".$ar2['id']." - Удалить $i", "$adden"];
		}
		if ($ar2['type']==1) { // for time
			$data.="$i.$lim1  В ".substr($ar2['timer'],0,5).", если есть пары $lim2".
				"(удалить /rd".$ar2['id'].", $endis".$ar2['id'].")\n";
		} else
		if ($ar2['type']==2) { // before N minutes
			$data.="$lim1 $i. За ".$ar2['timer']." перед парой $enddel\n";
		}
		$i++;
	      } //foreach
	    } //if 
            else
			{$data="Нет напоминаний\n";}
		if (strpos($_TEXT,'/rs1')===0){
    	        $data.="Введите время оповещения (чч:мм или чч.мм):";
		mysqli_query($mysql,"update save set last_state='5' where  viber_id='".$_USER['id']."' ");
		}
		if (strpos($_TEXT,'/rs2')===0){
    	        $data.="Введите за сколько минут оповестить перед ПЕРВОЙ парой:";
		mysqli_query($mysql,"update save set last_state='6' where  viber_id='".$_USER['id']."' ");
		}
	}

	if (strpos($_TEXT,'/rasp')===0 || strpos($_TEXT,'/t')===0 || strpos($_TEXT,'/g')===0 ){ // /after settings
		$data='Режим расписания. Выберите действие в меню.';
	    }

	if ($data==''){$data="Выберите действие в меню"; sendMsg($_CHAT['id'],$data);
	} 
	else {
		sendMsg($_CHAT['id'],var_export($lines,true));
	sendMenu4($_CHAT['id'],$data,'',$lines);}
	//    exit;
    } else // state=4
{ // other states
	if (($state<=4)&&(strpos($_TEXT,'/')!==false)){
	   if (!INLINE){
	     sendInlineMenu2($_CHAT['id'],"".$data,$lines);	
	      } else
	     { sendMenu2($_CHAT['id'],"".$data,'',$lines);
	      }
		 exit;
	  }   else{
		//if (($data=='')&&(strpos($_TEXT,'/')!==false)) sendHelp($_CHAT['id']);
		sendMenu2($_CHAT['id'],"".$data);
		
		exit;
	}
//sendMsg($_CHAT['id'], $data, '' );

}
//sendHelp($_CHAT['id']);
  } // if user not null

}// main if private chat

?>
