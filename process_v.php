<?php
require_once("vendor/autoload.php");
require_once 'settings.php';
require_once 'lib.osu.php';
require_once 'lib.tg.php';


use Viber\Client;
use Viber\Bot;
use Viber\Api\Sender;

		define('MESSENGER', 'viber');
		define('INLINE','0');

if(php_sapi_name() == 'cli' OR isset($_GET['webhook']))
        {

	$webhookUrl = 'https://viber.hcbogdan.com/bot.php'; // <- PLACE-YOU-HTTPS-URL
	try {	
	   $client = new Client([ 'token' => VIBER_BOT_TOKEN ]);
	    $result = $client->setWebhook(VIBER_WEBKOOK);
	    echo "Success!\n";
	} catch (Exception $e) {
	    echo "Error: ". $e->getError() ."\n";
	}
}

$botSender = new Sender([
    'name' => 'OsuRasp',
    'avatar' => 'https://osu.ru/favicon.ico',
]);

try {
    $bot = new Bot(['token' => VIBER_BOT_TOKEN]);
    $bot
    ->onConversation(function ($event) use ($bot, $botSender) {
        // ��� ������� ����� �������, ��� ������ ������������ �������� � ���
        // �� ������ ��������� "����������", �� �� ������ �������� ����� ���������
			sendHelp([$bot,$botSender,$event]);

        return (new \Viber\Api\Message\Text())
            ->setSender($botSender)
            ->setText("������������, ��� ��� ���������� ���. ��� ��� ��������?");
    })
    ->onText('|.*|s', function ($event) use ($bot, $botSender, $mysql) {
        // ��� ������� ����� ������� ���� ������������ ������ ��������� 
        // ������� �������� � ���������� ����������
        //$bot->getClient()->sendMessage(
         //   (new \Viber\Api\Message\Text())
         //   ->setSender($botSender)
         //   ->setReceiver($event->getSender()->getId())
         //   ->setText("I do not know )")
		
	
	//		# ������	���������������� ����������
		
		$_TEXT = mb_strtolower( $event->getMessage()->getText(), 'utf-8'); // ��� ��������������������� ����� ����� � ������ �������������
		$_USER['id'] = $event->getSender()->getId(); // ���������� � �����-�����������
		$_USER['username']=$event->getSender()->getName();
		$_CHAT['id'] = [$bot,$botSender,$event->getSender()->getId()];
		//$_USER['username'] = empty($_USER['username']) ? $_USER['first_name'].' '.$_USER['last_name'] : $_USER['username'];
	//	error_log(var_export($event, true));
	//	error_log(var_export($_USER['id'].'--', true));
		 //$mysql=$GLOBALS['mysql'];
		// ����� � ����
		mysqli_query($mysql,"INSERT INTO `messages`(`id_chat`, `id_message`, `id_user`, `time`, `message`, `user_nick`, `chat_name`) VALUES ('".$_USER['id']."', '', '".$_USER['id']."', '".time()."', '".$_TEXT."', '".$_USER['username']."', 'viber')");

		// �������� ���� ������
/*		$q_u = mysqli_query($mysql,"SELECT * FROM `tg_users` WHERE `id_user` = '".$_USER['id']."'");
		if(mysqli_num_rows($q_u) < 1)
			{				
				mysqli_query($mysql,"INSERT INTO `tg_users`(`id_user`, `nick`) VALUES ('".$_USER['id']."', '".$_USER['username']."')");
			}
*/
				
				require_once 'scripts/menu2.php';
						
        //);
    })
    ->run();
} catch (Exception $e) {
    // todo - log exceptions
	error_log ($e->getMessage());
}




?>