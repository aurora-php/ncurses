#!/usr/bin/env php
<?php

require_once(__DIR__ . '/../../libs/autoloader.class.php');

use \org\octris\ncurses as ncurses;

class win extends ncurses\container\window {
	protected function setup() {
		 $this->addChild(new ncurses\widget\prompt('$ '));
	}
}

class test extends ncurses\app {
	protected $win;

	protected function setup() {
		$this->win = $this->addChild(new win(10, 5));
	}

	protected function main() {
		$this->win->show();
	}
}

test::enableLog('/tmp/test.log');
test::getInstance()->run();
