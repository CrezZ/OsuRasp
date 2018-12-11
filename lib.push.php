<?php
//////////////////////////////////////////////////////////////////////
/////  THis is library for WebPush send method

require_once("vendor/autoload.php");
require_once 'settings.php';
 
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

function sendPush($array,$text){


// array of notifications
$notification = [
        'subscription' => Subscription::create([
	'endpoint' => $array['endpoint'],
    "keys" => [
	    'p256dh' => $array['keys']['p256dh'],
	    'auth' => $array['keys']['auth'],
	  ]    
        ]),
	    'payload' => "$text",
      
];

$auth = [
    'VAPID' => [
        'subject' => 'www.osu.ru', // can be a mailto: or your website address
	'publicKey' => PUBLIC_KEY,
	'privateKey' => PRIVATE_KEY,
    ],
];


$webPush = new WebPush($auth);

    $webPush->sendNotification(
        $notification['subscription'],
        $notification['payload'] // optional (defaults null)
    );

    if ($webPush->flush()->current()->isSuccess()) {
    return true;
    }
return false;
}


/*
if(php_sapi_name() == 'cli'){


$ar[0]=[
 'endpoint' => 'https://fcm.googleapis.com/fcm/send/eUS9zTQ31q4:APA91bFzLqSmjZLwg3ywgWjZHqgujehk1A3NTc09fEkmE3n90cHonC1z2rdsKYcJCPBbtTT5Aza2u_uw8rqMbT6976nqXnfIZ-BoBTWZkGj6jgHhPTQ3OAR0EGmuzcGYJBGlt7SjaDpg', // Chrome
'keys' =>[ 
'p256dh' => 'BEKWVB43EiYfnt3lMPhDwRJ49VtfsipBa5UlsIYJ1qIPe1xWR8lmK0fSIE01GacBU-3BEQqdjq-usYzxNayI2Ss',
 'auth' => '4FFrE01Myivk2oZOsjCYYQ',
    ],
// 'publicKey' => 'BIqr_uQNyljwmAGj9W1YJd-yv84hAwLY2LFLeuBGPT6ZNpoKMBldEEwInZ_0Q5A-St6qY6ZowXTFfcWGISuRcfg', // (recommended) uncompressed public key P-256 encoded in Base64-URL
// 'privateKey' => 'BeiWT86OKo2tp1ZRprnv5__T2FFQi2yw7wAkOc21yCQ', // (recommended) in fact the secret multiplier of the private key encoded in Base64-URL

];

$ar[1]=  ["endpoint"=>"https://fcm.googleapis.com/fcm/send/fQWTfR-_NX4:APA91bEned7VNxG34cFNH_FhCY6nUQxkhKDZU0nmVaHzlH-WbuCr_0pXe075caYJgWuJwiQPav_t-y82YAuOyctQsYJ95kApLy5MnY_JYrwdEP4xsk_-RkaBKxi5W17-ql3sOzIJZT_Y",
"expirationTime"=>'null',"keys"=>["p256dh"=>"BMxXmLORE6OHpwl27cZU1upqswy3w_DSQM02mKSB-c3mocJF_Fzu1dDmWaxRSyXv5epvNxx1Wf48CDRMYwZ0xpw","auth"=>"rcWlW-fpxxiyrJicHPZrqw"]];

var_dump( sendPush($ar[1],'test'));
}

*/


?>