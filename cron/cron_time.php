<?php
error_reporting(E_ERROR | E_PARSE);
require_once '../api.php';
require_once '../lib.osu.php';
require_once '../lib.tg.php';

$debug=1;

mysqli_query('set codepage "utf-8"');
#minimal time before reminder in minutes
$mintime=5;

if ($debug) $mintime=5555;

//select time reminder
$query="select type,reminder.messenger,save.viber_id,timer,save.user_id,who_id,prep_id,facult_id,potok_id,group_id from reminder ".
		" left join save on (reminder.user_id=save.user_id or reminder.viber_id=save.viber_id)".
	    "where enabled=1 and type=1 and ABS(TIME_TO_SEC(TIMEDIFF(curtime(),timer))/60) <= $mintime ";
$r=mysqli_query($mysql,$query);
if ($debug) print " \n count - ".mysqli_num_rows($r)."\n";

if (mysqli_num_rows($r)>0){
    while($ar=mysqli_fetch_assoc($r)){
//var_dump($ar);
//	if ($ar['who_id']==2){ //  prepod
#print 'go';

			$data="Пары:\n";
            //$dat= time();
            $dd=strtoupper(date("d-M-y",strtotime('-1 day')));
			$rasp = lessonsList($ar['who_id'], $dd,$ar['prep_id'],$ar['group_id'],
		                   $ar['potok_id'],$ar['facult_id']); //universal

            if (count($rasp)==0){ //no lessons
                $data='Нет пар';
            } else {
                sort($rasp);
                $data.=implode("\n",$rasp);
            }

if(!$debug && $ar['messenger']=='telegram'){
	sendMenu4($ar['user_id'],$data);
// print 'send';
}
if ($debug) print $ar['user_id'].$data."\n";

    }
}
?>