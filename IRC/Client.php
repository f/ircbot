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

	/**
	 * @var \IRC\Bot
	 */
	private $bot;

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

	public function getChannel()
	{
		return $this->channel;
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
		$this->send($this->commander->pong());

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

			} elseif (preg_match('{^:(?<nickname>[^!]+)![^@]*@(?<address>[0-9A-Za-z\/\.\:]*)? (?<command>[A-Z]+) (?<channel>#?[^:\s]+)?[^:]*:(?<message>.*)$}uis', $data, $message)) {

				$message = array_map('trim', $message);

				$mention_level = 0;

				if (preg_match('#' . preg_quote($this->nickname) . '#ui', $message['message']))
					$mention_level++;

				if (preg_match('#^' . preg_quote($this->nickname) . '\s*[\,:]?\s*$#ui', $message['message']))
					$mention_level++;

				$response = new Message(Message::TYPE_MSG);
				$response->setMessage(array(
					'address' => $message['address'],
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
	public function talk($message, $delay = 1, $to = false)
	{
		sleep($delay);

		if (!$to)
			$to = $this->channel->getName();

		$time = date('Y-m-d H:i:s');
		echo "[{$time}] <{$this->nickname}>\t[>>]\t: {$message} \n";

		$this->send($this->commander->talk($message, $to));
	}

	public function attach($bot)
	{
		$this->bot = Bot::factory($bot);
		$this->bot->setIRC($this);
	}

	public function listen()
	{
		while(false !== ($message = $this->receive()))
		{
			if ($this->bot)
				$this->bot->setMessage($message);

			$msg = $message->getMessage();

			switch ($message->getType())
			{
				case Message::TYPE_IRC:

					echo "[{$message->getTime()->format('Y-m-d H:i:s')}] <@IRC>\t[**]\t: {$msg['message']} \n";

					switch ($msg['code'])
					{
						case 332:
							$this->channel->setTitle($msg['message']);
							break;
						case 353:
							$this->channel->setUsers($msg['message']);
							break;
					}

					if ($this->bot)
					{
						if (!$this->bot->logged && preg_match('{NAMES}u', $msg['message'])) {
							$this->bot->logged = true;
							$this->bot->onLogin();
						}

						if ($this->bot->logged)
							$this->bot->onIRCNotice();
					}

					break;
				case Message::TYPE_MSG:

					if ($msg['command'] == 'PRIVMSG')
						echo "[{$message->getTime()->format('Y-m-d H:i:s')}] <{$msg['nickname']}>\t[<<]\t: {$msg['message']} \n";

					if ($this->bot && $msg['command'] == 'PRIVMSG')
					{
						if ($this->bot->logged) {
							$this->bot->onAnyMessage();
							if ($msg['nickname'] == $this->bot->getOwner())
							{
								if ($msg['private'])
									$this->bot->onOwnerPrivateMessage();
								else
									$this->bot->onOwnerPublicMessage();
							} else {
								if ($msg['private'])
									$this->bot->onPrivateMessage();
								else
									$this->bot->onPublicMessage();
							}

						}
					}

					if (in_array($msg['command'], array('JOIN', 'QUIT', 'KICK')))
					{
						$this->send($this->commander->names($this->channel->getName()));

						if ($this->bot && $this->bot->logged)
						{
							switch ($msg['command'])
							{
								case 'JOIN':
									$this->bot->onSomeoneLogin();
									break;
								case 'QUIT':
									$this->bot->onSomeoneExit();
									break;
								case 'KICK':
									$this->bot->onSomeoneKicked();
									break;
							}
						}
					}

					break;
				case Message::TYPE_PING:
					$this->send($this->commander->pong());
					break;
				case Message::TYPE_NULL:
					echo "[{$message->getTime()->format('Y-m-d H:i:s')}] <@IRC>\t[**]\t: {$msg['message']}";
					break;
			}
		}
	}

}