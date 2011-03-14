<?php
namespace IRC;

class Bot {

	public $nickname = 'fkaBot';
	public $personal = array();

	public function getTalk()
	{
		return include __DIR__ . '/Bot/' . get_class($this).'Talk.php';
	}

}