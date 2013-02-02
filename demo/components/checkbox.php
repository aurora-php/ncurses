#!/usr/bin/env php
<?php

define('NCURSES_LOG', '/tmp/test.log');

require_once(__DIR__ . '/../libs/autoloader.class.php');

use \org\octris\ncurses as ncurses;

class win extends ncurses\container\window {
	protected $checkbox;

	protected function setup() {
		$this->checkbox = $this->addChild(
		    new ncurses\component\checkbox(array(
		        array('label' => 'Apple',  'selected' => true),
		        array('label' => 'Orange', 'selected' => false),
		        array('label' => 'Banana', 'selected' => false)
		    ))
		);
	}

    protected function run() {
    	$this->checkbox->run();
    }
}

class test extends ncurses\app {
	protected $win;

	protected function setup() {
		$this->win = $this->addChild(new win(14, 5, 5, 5));

	}

	protected function main() {
		$this->win->show();
	}
}

test::getInstance()->run();
