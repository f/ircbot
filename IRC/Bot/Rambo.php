<?php
namespace IRC\Bot;

use IRC\Bot;

class Rambo extends Bot {

	public function identify()
	{
		$this->nickname = 'ramboberk';
		$this->personal = array(
			'age' => 23,
			'sex' => 'm',
			'land'=> 'istanbul'
		);
	}

}