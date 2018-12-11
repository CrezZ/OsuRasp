<?php
///////////////////////////////////////////////
//// This is library for viber send

require_once("vendor/autoload.php");
require_once 'api.php';
require_once 'lib.osu.php';
require_once 'lib.tg.php';

use Viber\Client;
use Viber\Bot;
use Viber\Api\Sender;


function viberSend($id,$message){

		define('MESSENGER', 'viber');
		define('INLINE','0');

$botSender = new Sender([
    'name' => 'OsuRasp',
    'avatar' => 'https://osu.ru/favicon.ico',
]);

try {
    $bot = new Bot(['token' => VIBER_BOT_TOKEN]);
	$bot->getClient()->sendMessage(
                (new \Viber\Api\Message\Text())
                    ->setSender($botSender)
                    ->setReceiver($id)
                    ->setText($message)
			);

}
catch (Exception $e) {
    // todo - log exceptions
	error_log ($e->getMessage());
}

}
function viberSendMenu4($id,$message){
		define('MESSENGER', 'viber');
		define('INLINE','0');

$botSender = new Sender([
    'name' => 'OsuRasp',
    'avatar' => 'https://osu.ru/favicon.ico',
]);

try {
    $bot = new Bot(['token' => VIBER_BOT_TOKEN]);

    sendMenu4([$bot,$botSender,$id],$message,'viber');


}
catch (Exception $e) {
    // todo - log exceptions
	error_log ($e->getMessage());
}

}

//viberSendMenu4('nWbBvP/AFYl1yD0t66C8xQ==','Hi');

?>