<?php
namespace IRC;

class Commander {

	const NICK = 'NICK %s';
	const USER = 'USER %s 0 * : %1$s';
	const JOIN = 'JOIN %s';
	const TALK = 'PRIVMSG %s : %s';
	const PONG = 'PONG 127.0.0.1';

	public function nick($nickname) {
		return sprintf(self::NICK, $nickname);
	}

	public function user($nickname) {
		return sprintf(self::USER, $nickname);
	}

	public function join($channel) {
		return sprintf(self::JOIN, $channel);
	}

	public function talk($message, $to) {
		return sprintf(self::TALK, $to, $message);
	}

	public function pong() {
		return self::PONG;
	}
}