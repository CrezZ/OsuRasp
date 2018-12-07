<?php
error_reporting(E_ERROR | E_PARSE);

require_once 'api.php';
require_once 'lib.osu.php';

function sendMenu4($chat,$text){
    $keyboard = [
        ['/day Что сегодня','/z Что завтра'],
                        ['/rs1 Оповещать утром', '/rs2 Оповещать за N-минут'],
                        ['/h Помощь', '/start Сбросить настройки']];

    sendKeyboard($chat, "$text" , 'html', 0 , $keyboard);

}



#minimal time before reminder in minutes

$mintime=5;

// one per day start
if ((date('H') == 6) && (date('i') < 5)) {	 //work only at 06:00
$r=mysqli_query($mysql,"select type,timer,save.user_id,who_id,prep_id,facult_id,potok_id,group_id from reminder".
		" left join save on reminder.user_id=save.user_id ".
		" where enabled=1 and type=2 ");
if (mysqli_num_rows($r)>0){
    while($ar=mysqli_fetch_assoc($r)){
	if ($ar['who_id']==2){ //  prepod
	    //print 'go';

	    $rasp=getOSUData('rasp',$ar['prep_id'],2);
	    $data="Пары:\n";
            $ras=getOSUData('rasp',$ar['prep_id'],2);
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
"('".$ar['user_id']."',TIMEDIFF('$time','".$ar['timer']."'),now(),'$data')".
"ON DUPLICATE KEY UPDATE timer=TIMEDIFF('$time','".$ar['timer']."'), dat=now(), message='$data'");
            }
//	sendMenu4($ar['user_id'],$data);
//	    mysqli_query($mysql,"");	
//print $data;
	}
	if ($ar['who_id']==1){ //  student
	    $rasp=getOSUData('rasp',$ar['group_id'],1);
	}
	
    }
}
}//if date

//select time reminder
$r=mysqli_query($mysql,"select timer,save.user_id,who_id,prep_id,facult_id,potok_id,group_id from cache left join save on cache.user_id=save.user_id ".
	    "where curdate()=dat and ABS(TIME_TO_SEC(TIMEDIFF(curtime(),timer))/60) <= $mintime ");

if (mysqli_num_rows($r)>0){
    while($ar=mysqli_fetch_assoc($r)){
#var_dump($ar);
	if ($ar['who_id']==2){ //  prepod
//print 'go';

//	    $rasp=getOSUData('rasp',$ar['prep_id'],2);
	    $data="Пары:\n";
            $ras=getOSUData('rasp',$ar['prep_id'],2);
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
            if (count($rasp)==0){ //no lessons
                $data='Нет пар';
            } else {
                sort($rasp);
                $data.=implode("\n",$rasp);
            }
	sendMenu4($ar['user_id'],$data);
//print $data;
	}
	if ($ar['who_id']==1){ //  student
            $data="Пары:\n";
            $ras=getOSUData('rasp',$ar['group_id'].'&potok='.$ar['potok_id'].'&facult='.$ar['facult_id'],1)
;
            $dat= (strpos($_TEXT,'/z')===0)? strtotime("+1 day") : time();
            $dd=strtoupper(date("d-M-y",$dat));
            $rasp=array();
//          if ($ar2)
            foreach ($ras as $f){
                if ($f['DAY']==$dd)
                {
                    $rasp[]=$f['DESCRIPTION'].' - '.$f['AUD_ALL_LPAD'].'-'.$f['FIO_SOKR'].' '.$f['SHORT_NAM
E_SUB'].'('.$f['TYPEZAN_SHORT_NAME'].")";
                }
            }//foreach
            if (count($rasp)!=0){ //no lessons
                sort($rasp);
                $data.=implode("\n",$rasp);
		sendMenu4($ar['user_id'],$data);
            }
	}

    }
}
?>