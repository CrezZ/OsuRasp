<?php

function get_rasp($URL, $filter='')
// fulter == date filter on 1st row;
{

$out=array(); // result
error_reporting(E_ERROR | E_PARSE);
$dom = new DOMDocument;
#$dom->loadHTML(file_get_contents("http://osu.ru/pages/schedule/?who=2"));

$html2='<?xml encoding="utf-8" ?> '.urldecode(file_get_contents($URL));
$html=iconv('KOI8-R','UTF-8',$html2);

#var_dump($html);

$dom->loadHTML($html);
#var_dump($dom);
#$fac = $dom->getElementById('facult');
#$opt= $fac->getElementsByTagName('option');
#foreach ($opt as $o ) {
#$caption.= $o->getAttribute('title');
#$caption.= "'/setfac ".$o->getAttribute('value') ."'".PHP_EOL;
#}

$Table = $dom->getElementsByTagName('table')->item(0);
$tr_i=0;
$header=array();
$th=$Table->getElementsByTagName('th');
 foreach($th as $td){
  $headers[] = $td->nodeValue;
 }

foreach($Table->getElementsByTagName('tr') as $tr)
{
    $tds = $tr->getElementsByTagName('td'); // get the columns in this row
#print $tds->length;
    if($tds->length > 0)
    {
 // check filter
if ((strpos($tds->item(0)->nodeValue,$filter)===false) && ($filter!='')) continue;

        // check if B and D are found in column 2 and 4
//        if(trim($tds->item(0)->nodeValue) != '' )
//        {
            // found B and D in the second and fourth columns
            // echo out each column value
$notempty=0;
$i=0;

 //chech if row not empty
 foreach($tds as $td){
 if ($i>0)
  if (trim($td->nodeValue) !='')  $notempty++;
 $i++;  
 }//foreach $tr
 if ($notempty>0){
 // print rasp
 foreach($tds as $td){
 if ($i>0)
  if (trim($td->nodeValue) !='')  {
             $out[] = array($tds->item(0)->nodeValue, $headers[$i], $td->nodeValue); //data, time, para name
    }
  $i++;  
 } //foreach $tr 
#echo "\n";
 } //if notempty

    } //if length >0
$tr_i++;

} //foreach $tr

return $out;
}


$r1 = get_rasp('http://www.osu.ru/pages/schedule/?who=2&what=1&filial=1&facult=6543&kafedra=6349&prep=32375&request=rasp');

foreach ($r1 as $a){
print $a[0].$a[1].$a[2];
}

?>