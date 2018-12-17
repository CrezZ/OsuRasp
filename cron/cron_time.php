<?php

$debug=0;

if (!$debug) error_reporting(E_ERROR | E_PARSE);
require_once __DIR__.'/../api.php';
require_once __DIR__.'/../lib.osu.php';
require_once __DIR__.'/../lib.tg.php';
require_once __DIR__.'/../lib.vb.php';

//$debug=1;

mysqli_query($mysql,'set codepage "utf-8"');
#minimal time before reminder in minutes
$mintime=5;

$add_query="";
if ($debug) $mintime=195;
if (!$debug) $add_query="and curtime()<timer";

//select time reminder
$query="select type,reminder.messenger,save.viber_id,timer,save.user_id,who_id,prep_id,facult_id,potok_id,group_id from reminder ".
		" left join save on (reminder.viber_id=save.viber_id)".
	    "where enabled=1 and type=1 and ((TIME_TO_SEC(TIMEDIFF(curtime(),timer))/60) between 0 and $mintime) $add_query";
$r=mysqli_query($mysql,$query);
if ($debug) print "\n $query \n count - ".mysqli_num_rows($r)."\n";

if (mysqli_num_rows($r)>0){
    while($ar=mysqli_fetch_assoc($r)){
//var_dump($ar);
//	if ($ar['who_id']==2){ //  prepod
#print 'go';

			$data="Пары:\n";
            //$dat= time();
            $dd=strtoupper(date("d-M-y",time()));
  if ($debug)         $dd=strtoupper(date("d-M-y",strtotime('0 day')));
			$rasp = lessonsList($ar['who_id'], $dd,$ar['prep_id'],$ar['group_id'],
		                   $ar['potok_id'],$ar['facult_id']); //universal

            if (count($rasp)==0){ //no lessons
                $data='Нет пар';
            } else {
                sort($rasp);
                $data.=implode("\n",$rasp);
            }

if(!$debug && $ar['messenger']=='telegram'){
	sendMenu4($ar['viber_id'],$data,'telegram');
// print 'send';
}
if(!$debug && $ar['messenger']=='viber'){
	viberSendMenu4($ar['viber_id'],$data,'viber');
}
if ($debug) print $ar['viber_id'].$data."\n";

    }
}
?>