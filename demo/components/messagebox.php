#!/usr/bin/env php
<?php

define('NCURSES_LOG', '/tmp/test.log');

require_once(__DIR__ . '/../../libs/autoloader.class.php');

use \org\octris\ncurses as ncurses;

class test extends ncurses\app {
	protected $msg;

	protected function setup() {
		$this->msg = $this->addChild(new ncurses\container\messagebox(
			ncurses\container\messagebox::T_OK,
			'Hinweis',
			40, 10, 5, 5
		));
	}

	protected function main() {
		$this->msg->show();
	}
}

test::getInstance()->run();
