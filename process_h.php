<?php
////////////////////////////////////////////////////////
//  THis file using for dynamic DropDown lists with index.html
////////////


//require_once("vendor/autoload.php");
require_once 'settings.php';
require_once 'lib.osu.php';
require_once 'lib.tg.php';

function mres($value)
{
    $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

    return str_replace($search, $replace, "$value");
}
	

if (!empty($_GET['who'])){

$who=mres($_GET['who']) ?? '0' ;

$s1=mres($_GET['s1'] )?? '0';
$s2=mres($_GET['s2'] ) ?? '0';
$s3=mres($_GET['s3'])  ?? '0';
$date=($_GET['date']!='')?$_GET['date']:time();
$step0=$_GET['step'];
$step=$step0+1;

if (($step=='4')){ // rasp
	$data="<table>";
	$rasp= lessonsList($who, strtoupper(date("d-M-y",$date)),$s3,$s3,$s2,$s1); //universal
	if (count($rasp)!=0){ //no lessons
                sort($rasp);
				foreach($rasp as $rr){
					$data.="<tr><td>$rr</td></tr>";
				}
			$data.="</table>";	
	} else 
	{$data="Нет пар";}
	
	print $data;
	exit;
}
$id=0;
$select='';


if ($step0='0')$id=0; // facult

$res=array();
if (($step=='3')&&($who==1)){ // student group
    
	$res=getOSUData('group',"$s2&facult=".$s1,$who);
}   
else
{	
	$request='';
	 if ($who==1)
		switch ($step){
		 case '1':{$request='facult';$id=0; break;}
		 case '2':{$request='potok'; $id=$s1;break;}
		 case '3':{$request='group'; $id=$s2;break;}
	 }
	if ($who==2)
		switch ($step){
		 case '1':{$request='facult';$id=0; break;}
		 case '2':{$request='kafedra';$id=$s1; break;}
		 case '3':{$request='prep';$id=$s2; break;}
	 }
	$res=getOSUData($request,"$id",$who);
}
	$data="<select name='s$step' id='s$step' default=0>";
	foreach($res['list'] as $r){
		$data.="<option value='".$r['id']."'>".$r['name'];
	}
	$data.="</select>";
print $data;
exit;
}


?>