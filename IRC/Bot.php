<?php
namespace IRC;

abstract class Bot {

	private $nickname = 'fkaBot';
	private $owner = 'fka';

	public $logged = false;
	public $quitRequest = false;

	/**
	 * @var $irc Client
	 */
	private $irc;

	/**
	 * @var $message Message
	 */
	private $message;

	public function setIRC(Client $client)
	{
		$this->irc = $client;
		$this->setup();
	}

	public function setMessage(Message $message) {
		$this->message = $message;
	}

	public function getMessage() {
		return $this->message;
	}

	public function getOwner()
	{
		return $this->owner;
	}

	protected function setOwner($owner)
	{
		$this->owner = $owner;
	}

	public function getNickname() {
		return $this->nickname;
	}

	protected function setNickname($nickname) {
		$this->nickname = $nickname;
		$this->irc->nickname($nickname);
	}

	/**
	 * @return Client
	 */
	protected function bot()
	{
		return $this->irc;
	}

	protected function talk($message, $delay = 1, $to = false)
	{
		$this->bot()->talk($message, $delay, $to);
	}

	protected function replyPrivate($message, $delay = 1)
	{
		$this->bot()->talk($message, $delay, $this->message->get('nickname'));
	}

	protected function replyPublic($message, $delay = 1)
	{
		$this->bot()->talk($this->message->get('nickname').": ".$message, $delay);
	}

	protected function info($key)
	{
		return $this->getMessage()->get($key);
	}

	public function getTalk()
	{
		return include __DIR__ . '/../' . implode(DIRECTORY_SEPARATOR, explode('\\',get_class($this))).'Talk.php';
	}

	private function compileSentence($sentence)
	{
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

	abstract function setup();
	abstract function onLogin();
	abstract function onSomeoneLogin();
	abstract function onSomeoneExit();
	abstract function onSomeoneKicked();
	abstract function onAnyMessage();
	abstract function onPublicMessage();
	abstract function onPrivateMessage();
	abstract function onOwnerPublicMessage();
	abstract function onOwnerPrivateMessage();
	abstract function onIRCNotice();

}