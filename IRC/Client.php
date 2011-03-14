<?php
namespace IRC;

class Client {

	const TIMEOUT = 30;

	private $host;

	private $port = 6667;

	/**
	 * @var Channel
	 */
	protected $channel;

	private $nickname;

	private $error = array(
		'number' => 0,
		'string' => ''
	);

	/**
	 * @var resource|bool
	 */
	private $connection = false;

	/**
	 * @var Commander
	 */
	private $commander = null;

	private $bot = false;

	private static $_instance;

	private function __clone() {}

	/**
	 * @static
	 * @return Client
	 */
	public static function getInstance()
	{
		if (!self::$_instance)
			self::$_instance = new self();

		return self::$_instance;
	}

	private function __construct()
	{
		$this->commander = new Commander();
	}

	public function server($host, $port = 6667)
	{
		$this->host = $host;
		$this->port = $port;
	}

	public function channel($channel)
	{
		$this->channel = new Channel();
		$this->channel->setName($channel);
	}

	/**
	 * @param  $nickname string|array
	 * @return void
	 */
	public function nickname($nickname)
	{
		$this->nickname = $nickname;
	}

	public function connect()
	{
		set_time_limit(0);
		$this->connection = fsockopen($this->host, $this->port, $error_number, $error_string, self::TIMEOUT);
		$this->error = array(
			'number' => $error_number,
			'string' => $error_string
		);
		if (!$this->connection)
			return false;

		$this->send($this->commander->nick($this->nickname));
		$this->send($this->commander->user($this->nickname));
		$this->send($this->commander->join($this->channel->getName()));


	}

	public function send($command)
	{
		if ($this->connection)
			fputs($this->connection, $command . ' ' . PHP_EOL);
		else
			return false;
	}

	public function receive()
	{
		if ($this->connection)
		{
			$data = fgets($this->connection, 4096);
			if (preg_match('{^PING}uis', $data))
			{
				$response = new Message(Message::TYPE_PING);
				return $response;

			} elseif (preg_match('{^:(?<server>[A-Za-z0-9\.]+) (?<code>\d+)[^:]*:(?<message>.*)$}uis', $data, $message)) {

				$message = array_map('trim', $message);

				$response = new Message(Message::TYPE_IRC);
				$response->setMessage(array(
					'code' => $message['code'],
					'message' => $message['message']
				));
				return $response;

			} elseif (preg_match('{^:(?<nickname>[^!]+)![^@]*@[A-Za-z\/]*(?<ip>[0-9\.]*)? (?<command>[A-Z]+) (?<channel>#?[^:]+)?:(?<message>.*)$}uis', $data, $message)) {

				$message = array_map('trim', $message);

				$mention_level = 0;

				if (preg_match('#' . preg_quote($this->nickname) . '#ui', $message['message']))
					$mention_level++;

				if (preg_match('#^' . preg_quote($this->nickname) . '\s*[\,:]?\s*$#ui', $message['message']))
					$mention_level++;

				$response = new Message(Message::TYPE_MSG);
				$response->setMessage(array(
					'ip' => trim($message['ip'],'.'),
					'nickname' => $message['nickname'],
					'channel' => ($message['channel'] != '' ? $message['channel'] : $this->channel->getName()),
					'message' => $message['message'],
					'command' => $message['command'],
					'private' => ($message['channel'] == $this->nickname ? true : false),
					'mention' => $mention_level
				));
				return $response;
			} else {
				$response = new Message(Message::TYPE_NULL);
				$response->setMessage(array(
					'message' => $data
				));
				return $response;
			}
		}
		else
			return false;
	}

	/**
	 * @param string $message
	 * @param bool|string $to
	 * @return void
	 */
	public function talk($message, $to = false)
	{
		if (!$to)
			$to = $this->channel->getName();

		$this->send($this->commander->talk($message, $to));
	}

	public function attach(Bot $bot)
	{
		$this->bot = $bot;
	}

	public function listen()
	{
		while(false !== ($message = $this->receive()))
		{
			$msg = $message->getMessage();
			switch ($message->getType())
			{
				case Message::TYPE_IRC:
					switch ($msg['code'])
					{
						case 332:
							$this->channel->setTitle($msg['message']);
							break;
						case 353:
							$this->channel->setUsers($msg['message']);
							break;
					}
					var_dump($msg);
					$this->bot->getTalk();
					break;
				case Message::TYPE_MSG:
					var_dump($msg);
					break;
				case Message::TYPE_PING:
					$this->send($this->commander->pong());
					break;
				case Message::TYPE_NULL:
					echo $msg['message'];
					break;
			}
		}
	}

}