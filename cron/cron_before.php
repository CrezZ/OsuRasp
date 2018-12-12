<?php
$debug=0;

if (!$debug)error_reporting(E_ERROR | E_PARSE);

//define ('MESSENGER',);

require_once __DIR__.'/../api.php';
require_once __DIR__.'/../api.php';
require_once __DIR__.'/../lib.osu.php';
require_once __DIR__.'/../lib.tg.php';
require_once __DIR__.'/../lib.vb.php';

//$debug=1;


#minimal time before reminder in minutes

$mintime=5;
if ($debug) $mintime=55555;

// one per day start


if ((date('H') == 6) && (date('i') < 5) || $debug) 
{	 //work only at 06:00

//select todays lessons
$query="select type,reminder.viber_id,reminder.messenger,timer,save.user_id,who_id,prep_id,facult_id,potok_id,group_id from reminder".
		" left join save on ( reminder.viber_id=save.viber_id)".
		" where enabled=1 and type=2 ";
$r=mysqli_query($mysql,$query);

if ($debug) print "$query \n count = ".mysqli_num_rows($r)."\n";
//if there is lessons
if (mysqli_num_rows($r)>0){
    while($ar=mysqli_fetch_assoc($r)){
//	if ($ar['who_id']==2){ //  prepod
	    //print 'go';

//	    $rasp=getOSUData('rasp',$ar['prep_id'],2);
	    $data="Пары:\n";
            $dd=strtoupper(date("d-M-y",time()));
if ($debug)   $dd=strtoupper(date("d-M-y",strtotime('+0 day')));

			$rasp = lessonsList($ar['who_id'], $dd,$ar['prep_id'],$ar['group_id'],
		                   $ar['potok_id'],$ar['facult_id']); //universal
	
		//if (count($rasp)!=0)
		{ //no lessons
                sort($rasp);
		$time=substr($rasp[0],0,5);
                $data.=mysqli_real_escape_string($mysql,implode("\n",$rasp));
		$query="insert into cache (user_id,timer,dat,message,viber_id,messenger) values ".
		"('".$ar['user_id']."',TIMEDIFF('$time','".$ar['timer']."'),now(),'$data','".$ar['viber_id']."','".$ar['messenger']."')".
		"ON DUPLICATE KEY UPDATE timer=TIMEDIFF('$time','".$ar['timer']."'), dat=now(), message='$data', ".
		"viber_id='".$ar['viber_id']."',messenger='".$ar['messenger']."'";		
		mysqli_query($mysql,$query);
	    if ($debug) print "$query \n";

            }
    }
}
}//if date

//select time reminder
if (!$debug) $add="curdate()=dat and";
$q="select *  from cache ".
	    "where $add  ABS(TIME_TO_SEC(TIMEDIFF(curtime(),timer))/60) <= $mintime";
$r=mysqli_query($mysql,$q);
if ($debug) print $q;

if (mysqli_num_rows($r)>0){
    while($ar=mysqli_fetch_assoc($r)){


 if ($debug && $ar['messenger']=='viber'){
	viberSendMenu4($ar['viber_id'],$ar['message']);

}
 if (!$debug && $ar['messenger']=='telegram'){
	sendMenu4($ar['viber_id'],$ar['message'],'telegram');
}

	if ($debug)	print var_export($ar)."\n";
    }
}

//viberSendMenu4('nWbBvP/AFYl1yD0t66C8xQ==','Hi2');

?>