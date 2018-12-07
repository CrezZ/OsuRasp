<?php
#include_once 'simple_html_dom.php';
#$tmp=tmpfile();
#write($tmp,);
#$html=str_get_html(file_get_contents("http://osu.ru/pages/schedule/?who=2"));

#$html=file_get_html("osu.html");


$caption="Facults";
$dom = new DOMDocument;
$dom->loadHTML(file_get_contents("http://osu.ru/pages/schedule/?who=2"));
$fac = $dom->getElementById('facult');
$opt= $fac->getElementsByTagName('option');
foreach ($opt as $o ) {
$caption.= $o->getAttribute('title');
$caption.= "'/setfac ".$o->getAttribute('value') ."'".PHP_EOL;
}

print $caption;



?>