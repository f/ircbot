<?php
 //Eğer CLI dışında bir ortamdan girildiyse hata verilir.
if (php_sapi_name() != 'cli') {
	exit('Bu bot yalnızca CLI üzerinde çalışmalıdır.');
}

//Değerler CLI'den alınır.
$options = getopt('h:p:c:', array('host:', 'port:', 'channel:'));

include __DIR__ . '/IRC/Autoloader.php';

IRC\Autoloader::register();

$irc = IRC\Client::getInstance();

$irc->server($options['host'], $options['port']);
$irc->channel($options['channel']);
$irc->attach('Rambo');

$irc->connect();

$irc->listen();