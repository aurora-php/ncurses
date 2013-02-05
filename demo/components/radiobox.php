#!/usr/bin/env php
<?php

define('NCURSES_LOG', '/tmp/test.log');

require_once(__DIR__ . '/../../libs/autoloader.class.php');

use \org\octris\ncurses as ncurses;

class win extends ncurses\container\window {
	protected $radiobox;

	protected function setup() {
		$this->radiobox = $this->addChild(
		    new ncurses\component\radiobox(0, 0, array(
		        array('label' => 'blue',  'selected' => true),
		        array('label' => 'green', 'selected' => false),
		        array('label' => 'black', 'selected' => true)
		    ))
		);
	}
}

class test extends ncurses\app {
	protected $win;

	protected function setup() {
		$this->win = $this->addChild(new win(13, 5, 5, 5));

	}

	protected function main() {
		$this->win->show();
	}
}

test::getInstance()->run();

