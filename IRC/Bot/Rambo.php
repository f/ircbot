<?php
namespace IRC\Bot;

use IRC\Message,
	IRC\Client,
	IRC\Bot;

class Rambo extends Bot {

	private $personal;

	public function setup()
	{
		$this->owner = 'fka';
		$this->nickname = 'ramboberk';
		$this->personal = array(
			'age' => 23,
			'sex' => 'm',
			'land'=> 'istanbul'
		);
	}

	public function onLogin()
	{
		$this->bot()->talk('test');
		$this->bot()->talk('naber', 2);

		$users = implode(',', $this->bot()->getChannel()->getUsers());

		$this->bot()->talk('Selam ' . $users, 3);
	}

	public function onIRCNotice(Message $message)
	{
		var_dump($message->getMessage());
	}

	public function onAnyMessage(Message $message)
	{

	}

	public function onPrivateMessage(Message $message)
	{

	}

	public function onOwnerMessage(Message $message)
	{

	}

	public function onPublicMessage(Message $message)
	{

	}

	public function onSomeoneExit(Message $message)
	{

	}

	public function onSomeoneLogin(Message $message)
	{
		$this->bot()->talk('Hoşgeldin');
	}

	public function onSomeoneKicked(Message $message)
	{
		$this->bot()->talk('Bye');
	}

}