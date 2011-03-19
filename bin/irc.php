<?php
 //Eğer CLI dışında bir ortamdan girildiyse hata verilir.

$license = <<<LICENSE

IRCBot Framework Copyleft (c) 2010 Fatih Kadir AKIN
This program comes with ABSOLUTELY NO WARRANTY.
For more information, please refer to <http://unlicense.org/>

LICENSE;
$usage = <<<USAGE

Usage: ircbot.php [options]

	-s or --server <server>		Server to connect
	-p or --port <port>		Port to connect
	-c or --channel <channel>	IRC Channel name to connect, must be started with "#"
	-a or --attach <bot>		A bot name to attach, stored in IRC/Bot folder.
	-h or --help			Show this information.


USAGE;

echo $license;


error_reporting(0);
ini_set('display_errors', 0);

if (php_sapi_name() != 'cli') {
	exit('This program can only be run on CLI');
}

//Değerler CLI'den alınır.
$options = getopt('s:p:c:a:h', array('server:', 'port:', 'channel:', 'attach:', 'help'));

$options['server'] 	= isset($options['s']) ? $options['s'] : $options['server'];
$options['port'] 	= isset($options['p']) ? $options['p'] : $options['port'];
$options['channel'] = isset($options['c']) ? $options['c'] : $options['channel'];
$options['attach'] 	= isset($options['a']) ? $options['a'] : $options['attach'];
$options['help'] 	= isset($options['h']) ? $options['h'] : $options['help'];

if (isset($options['help']))
{
	echo $usage;
	exit;
}

include __DIR__ . '/../IRC/Autoloader.php';

try {
	IRC\Autoloader::register();

	$irc = IRC\Client::getInstance();

	if (!file_exists(__DIR__ . '/../IRC/Bot/' . $options['attach'] . '.php'))
		exit("\nBot {$options['attach']} doesn't exists! \n".$usage);

	$irc->server($options['host'], $options['port']);
	$irc->channel($options['channel']);
	$irc->attach($options['attach']);

	$irc->connect();
	$irc->listen();
} catch (Exception $e) {
	echo $usage;
	exit;
}