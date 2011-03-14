<?php
namespace IRC;

class Channel {

	private $name;

	private $title;

	private $users = array();

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function setUsers($users)
	{
		$users = explode(' ', $users);
		$this->users = $users;
	}

	public function getUsers()
	{
		return $this->users;
	}
}