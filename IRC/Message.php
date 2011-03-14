<?php
namespace IRC;

class Message {

	const TYPE_NULL = -1;
	const TYPE_PING = 0;
	const TYPE_IRC = 1;
	const TYPE_MSG = 2;

	private $type;
	private $time;
	private $message = array();

	public function __construct($type) {
		$this->time = time();
		$this->type = $type;
	}

	public function setMessage(array $message) {
		$this->message = $message;
	}

	public function getMessage() {
		return $this->message;
	}

	public function getTime() {
		return $this->time;
	}

	public function getType() {
		return $this->type;
	}

}