<?php
namespace IRC\Bot;

use IRC\Message,
	IRC\Client,
	IRC\Bot;

class Rambo extends Bot {

	private $personal;

	public function setup()
	{
		$this->setOwner('fka');
		$this->setNickname('ramboberk');
		$this->personal = array(
			'age' => 23,
			'sex' => 'm',
			'land'=> 'istanbul'
		);
	}

	public function onLogin()
	{
		//$this->bot()->talk('test');
		//$this->bot()->talk('naber', 2);

		//$users = implode(',', $this->bot()->getChannel()->getUsers());

		//$this->bot()->talk('Selam ' . $users, 3);
	}

	public function onIRCNotice()
	{}

	public function onAnyMessage()
	{

	}

	public function onPrivateMessage()
	{

	}

	public function onPublicMessage()
	{
		if ($this->getMessage()->get('mention') == 2)
			$this->bot()->talk('efendim patron?', 3);
	}

	public function onOwnerPrivateMessage()
	{
		$this->bot()->talk('ADMINISTRATION', 0, $this->getMessage()->get('nickname'));
	}

	public function onOwnerPublicMessage()
	{

	}

	public function onSomeoneExit()
	{

	}

	public function onSomeoneLogin()
	{
		//$this->bot()->talk('Hoşgeldin');
	}

	public function onSomeoneKicked()
	{
		//$this->bot()->talk('Bye');
	}

}