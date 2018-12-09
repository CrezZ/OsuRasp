<?php
error_reporting(E_ERROR | E_PARSE);

require_once '../api.php';
require_once '../lib.osu.php';
require_once '../lib.tg.php';




#minimal time before reminder in minutes

$mintime=5;

// one per day start


if ((date('H') == 6) && (date('i') < 5)) 
{	 //work only at 06:00

//select todays lessons
$r=mysqli_query($mysql,"select type,timer,save.user_id,who_id,prep_id,facult_id,potok_id,group_id from reminder".
		" left join save on reminder.user_id=save.user_id ".
		" where enabled=1 and type=2 ");
//if there is lessons
if (mysqli_num_rows($r)>0){
    while($ar=mysqli_fetch_assoc($r)){
//	if ($ar['who_id']==2){ //  prepod
	    //print 'go';

//	    $rasp=getOSUData('rasp',$ar['prep_id'],2);
	    $data="Пары:\n";
	if ($ar['who_id']==2) //  prepod
            $ras=getOSUData('rasp',$ar['prep_id'],2);
	if ($ar['who_id']==1) //  student
            $ras=getOSUData('rasp',$ar['group_id'].'&potok='.$ar['potok_id'].'&facult='.$ar['facult_id'],1);
            $dat= time();
            $dd=strtoupper(date("d-M-y",$dat));
            $rasp=array();
//          if ($ar2)
	if ($ar['who_id']==2) //  prepod
            foreach ($ras as $f){
                if ($f['DAY']==$dd)
                $rasp[]=$f['DESCRIPTION'].' - '.$f['AUD_ALL_LPAD'].'-'.$f['NAMEGROUP'].' '.$f['SHORT_NAME_SUB'].'('.$f['TYPEZAN_SHORT_NAME'].")";
             }//foreach
	if ($ar['who_id']==1) //  student
            foreach ($ras as $f){
                if ($f['DAY']==$dd)
                $rasp[]=$f['DESCRIPTION'].' - '.$f['AUD_ALL_LPAD'].'-'.$f['FIO_SOKR'].' '.$f['SHORT_NAME_SUB'].'('.$f['TYPEZAN_SHORT_NAME'].")";
             }//foreach
            if (count($rasp)!=0){ //no lessons
                sort($rasp);
		$time=substr($rasp[0],0,5);
                $data.=mysqli_real_escape_string($mysql,implode("\n",$rasp));
		mysqli_query($mysql,"insert into cache (user_id,timer,dat,message) values ".
		"('".$ar['user_id']."',TIMEDIFF('$time','".$ar['timer']."'),now(),'$data')".
		"ON DUPLICATE KEY UPDATE timer=TIMEDIFF('$time','".$ar['timer']."'), dat=now(), message='$data'");
            }
    }
}
}//if date

//select time reminder
$r=mysqli_query($mysql,"select timer,user_id,message  from cache ".
	    "where curdate()=dat and ABS(TIME_TO_SEC(TIMEDIFF(curtime(),timer))/60) <= $mintime ");

if (mysqli_num_rows($r)>0){
    while($ar=mysqli_fetch_assoc($r)){
	sendMenu4($ar['user_id'],$ar['message']);
    }
}
?>