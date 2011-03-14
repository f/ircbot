<?php
namespace IRC;

abstract class Bot {

	protected $nickname = 'fkaBot';
	protected $owner = 'fka';

	public $logged = false;

	/**
	 * @var $irc Client
	 */
	private $irc;

	public function setIRC(Client $client)
	{
		$this->irc = $client;
	}

	/**
	 * @return Client
	 */
	public function bot()
	{
		return $this->irc;
	}

	public function getTalk()
	{
		return include __DIR__ . '/../' . implode(DIRECTORY_SEPARATOR, explode('\\',get_class($this))).'Talk.php';
	}

	/**
	 * @static
	 * @param  $bot
	 * @return IRC\Bot
	 */
	public static function factory($bot)
	{
		$bot = __NAMESPACE__."\\Bot\\$bot";
		return new $bot;
	}

	abstract function onLogin();
	abstract function onSomeoneLogin(Message $message);
	abstract function onSomeoneExit(Message $message);
	abstract function onAnyMessage(Message $message);
	abstract function onPrivateMessage(Message $message);
	abstract function onPublicMessage(Message $message);
	abstract function onOwnerMessage(Message $message);
	abstract function onIRCNotice(Message $message);

}