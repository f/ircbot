<?php
namespace IRC\Bot;

use IRC\Message,
	IRC\Client,
	IRC\Bot;

class Rambo extends Bot {

	private $personal;
	private $settings = array(
		'suspend' => false
	);

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

	/**
	 * @param  $key
	 * @param  $value
	 * @return void
	 *
	 * Settings.
	 *
	 */
	private function set($key, $value)
	{
		$this->settings[$key] = $value;
	}

	public function onLogin()
	{
		$this->talk('Yönetilmeye hazırım patron.', 0, $this->getOwner());
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
		if ($this->info('mention') == 2)
			$this->talk('efendim patron?', 3);
	}

	public function onOwnerPrivateMessage()
	{
		switch (true)
		{
			case preg_match('/^stop/ui', $this->info('message')):

				$this->set('suspend', true);
				$this->replyPrivate('Suspended.', 0);

				break;
			case preg_match('/^start/ui', $this->info('message')):

				$this->set('suspend', false);
				$this->replyPrivate('Activated again.', 0);

				break;
			case preg_match('/^quit/ui', $this->info('message')):

				$this->replyPrivate('Quit request queued.', 0);
				$this->talk('Beyler ben kaçıyorum...', 2);
				sleep(2);
				$this->quitRequest = true;

				break;
		}
	}

	public function onOwnerPublicMessage()
	{

	}

	public function onSomeoneExit()
	{

	}

	public function onSomeoneLogin()
	{
		if ($this->info('nickname') == $this->getOwner())
		{
			$this->replyPrivate('Yönetilmeye hazırım patron.', 0);
			$this->replyPublic('Selam naber bebek :).', 2);
		}
	}

	public function onSomeoneKicked()
	{
		//$this->bot()->talk('Bye');
	}

}