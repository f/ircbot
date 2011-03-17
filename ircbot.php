<?php
 //Eğer CLI dışında bir ortamdan girildiyse hata verilir.
if (php_sapi_name() != 'cli') {
	exit('Bu bot yalnızca CLI üzerinde çalışmalıdır.');
}

//Değerler CLI'den alınır.
$options = getopt('h:p:c:a:', array('host:', 'port:', 'channel:', 'attach:'));

include __DIR__ . '/IRC/Autoloader.php';

IRC\Autoloader::register();

$irc = IRC\Client::getInstance();

if (!file_exists(__DIR__.'/IRC/Bot/'. $options['channel'] . '.php'))
	exit("\n Bot not found! \n");

$irc->server($options['host'], $options['port']);
$irc->channel($options['channel']);
$irc->attach($options['attach']);

$irc->connect();

$irc->listen();