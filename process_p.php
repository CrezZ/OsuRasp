<?php

//////////////////////////////////////////////////////////////////////
////  This file used for WebPush notification install and save data for client WebPush
////  with index.html

require_once "settings.php";
require_once "lib.push.php";


$debug=1;


function mres($value)
{
    $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

    return str_replace($search, $replace, "$value");
}


if ($_SERVER['REQUEST_METHOD']=='POST' ){
$input=file_get_contents('php://input');
$array=json_decode($input,true);



if (!empty($array['endpoint'])){
	
if ($debug)error_log(var_export($array,true));

//$id = explode('/', $array['endpoint']);
//$id = $id[count($id)-1];
$timer='';
if ($_GET['type']=='1'){
$timer=strtotime('H:i',$_GET['time'] ?? '09:00');
}
$who=mres($array['who']) ?? '0';
$s1=mres($array['s1'] )?? '0';
$s2=mres($array['s2'] ) ?? '0';
$s3=mres($array['s3'])  ?? '0';

$data=mysqli_real_escape_string($mysql,$input);
$q="insert ignore into pushdata (data) values ('".$data."')";
$r=mysqli_query($mysql,$q);
$id=mysqli_insert_id($mysql);
if ($id==0){
	$r=mysqli_query($mysql,"select id from pushdata where data='$data'");
	$ar=mysqli_fetch_array($r);
	$id=$ar[0];
}
	if ($debug) error_log('id='.$id);
	//if ($debug) error_log($q);
	//if ($debug) error_log(mysqli_error($mysql));
	
	if ($debug) error_log(mysqli_error($mysql));

	mysqli_query($mysql,"update pushdata as t1 left join pushdata as t2 on (t1.id=t2.id) set t1.viber_id=t2.id  ".
						"where t2.id='$id'");
	
	if($who!='0'){
	mysqli_query($mysql,"insert ignore into save (viber_id,who_id,facult_id,potok_id,group_id,kafedra_id,prep_id) values ".
					" ('$id','$who','$s1','$s2','$s3','$s2','$s3')");
	if ($debug) error_log(mysqli_error($mysql));
	mysqli_query($mysql,"insert into reminder (user_id,timer, type,enabled, viber_id, messenger) values ".
	" ('$id','$timer','1','1','$id','push')");
	if ($debug) error_log(mysqli_error($mysql));
	}
	$result=sendPush($array ,"Вы подписаны");
 if ($debug) error_log($result);
}

}



?>