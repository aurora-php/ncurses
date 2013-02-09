#!/usr/bin/env php
<?php

require_once(__DIR__ . '/../../libs/autoloader.class.php');

use \org\octris\ncurses as ncurses;

class win extends ncurses\container\window {
	protected function setup() {
		 $this->addChild(new ncurses\component\shell(0, -1, '$ '));
	}
}

class test extends ncurses\app {
    protected static $logging = '/tmp/test.log';

	protected $win;

	protected function setup() {
		$this->win = $this->addChild(new win(40, 15));

	}

	protected function main() {
		$this->win->show();
	}
}

test::getInstance()->run();
