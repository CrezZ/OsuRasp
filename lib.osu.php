<?php

// API for osu.ru schedule. (c) Ushakov Yuri 2018
//**************************************
// Type of requests - facult (list of facult), kafedra (list of kafedra on facult), prep (list of prepods on kafedra), 
//  rasp (list of lessons for prepod)
// All request must have POST method and field who and what. 
// who: 1- student, 2 - prepod
// what: 1 - shcedule , 2 - zachet, 3 - exams
// filial: 1- main, 2,3... - others
// mode: full - full semester, 2w - 2 weeks
// Specification for requests: 
//  1. facult. Required - who={1,2}, request=facult. Response json {list:[{id,title,name,params},...]}
//  2. kafedra. Required - who={1,2}, request=kafedra, facult={facult_id}.  Response json {list:[{id,title,name,params},...]}
//  3. prep. Required - who={1,2}, request=prep, kafedra={kadedra_id}.  Response json {list:[{id,title,name,params},...]}
//  4. rasp. Required - who={1,2}, request=rasp, prep={prep_id}.  Response json {content:'html <div> with schedule table and scripts']}
// wharning - content not usable outside osu.ru site, it need be parsed.
// curl -X POST -H 'Content-Type: application/x-www-form-urlencoded' -H "X-Requested-With: XMLHttpRequest" -d "who=2&what=1&request=facult&filial=1&mode=full" http://osu.ru/pages/schedule/index.php 
// curl -X POST -H 'Content-Type: application/x-www-form-urlencoded' -H "X-Requested-With: XMLHttpRequest" -d "who=2&what=1&request=kafedra&filial=1&mode=full&facult=5220" http://osu.ru/pages/schedule/index.php 
// curl -X POST -H 'Content-Type: application/x-www-form-urlencoded' -H "X-Requested-With: XMLHttpRequest" -d "who=2&what=1&request=prep&filial=1&mode=full&kafedra=123123" http://osu.ru/pages/schedule/index.php 
// curl -X POST -H 'Content-Type: application/x-www-form-urlencoded' -H "X-Requested-With: XMLHttpRequest" -d "who=2&what=1&request=rasp&filial=1&mode=full&prep=123123" http://osu.ru/pages/schedule/index.php 

function getOSUData($request,$id,$who=1,$filial=1){
$data="what=1&mode=full&bot=1&filial=$filial&who=$who&request=$request&";
if ($request=='facult')
{
}
if ($request=='group')
{
if ($who==1)$data.="potok=$id";
}
if ($request=='potok')
{
if ($who==1)$data.="facult=$id";
}
if ($request=='kafedra')
{
if ($who==2)$data.="facult=$id";
}
if ($request=='prep')
{
if ($who==2)$data.="kafedra=$id";
}
if ($request=='rasp')
{
if ($who==2)$data.="prep=$id";
if ($who==1)$data.="group=$id";
}
//print $data;
                $ch = curl_init('http://osu.ru/pages/schedule/index.php');
		curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded",
							    "X-Requested-With: XMLHttpRequest"));
//                $a = utf8_encode(curl_exec($ch)); //site osu ru use koi8-r
                $a = iconv('KOI8-R','UTF-8',curl_exec($ch)); //site osu ru use koi8-r
if ($request !='rasp')		$a=str_replace('"','^',$a);
		$a=str_replace("'",'"',$a);
		$a=str_replace('^',"'",$a);
                 $r=json_decode($a, true,512, JSON_UNESCAPED_UNICODE);
	//	print ':';
	//	print json_last_error() ;
                return $r;
}

//test
//$r= getOSUData('group',"2018&facult=700",1);
//$r= getOSUData('rasp',"32375",2);
//var_dump( $r);

?>