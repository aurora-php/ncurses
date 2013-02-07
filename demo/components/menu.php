#!/usr/bin/env php
<?php

require_once(__DIR__ . '/../../libs/autoloader.class.php');

use \org\octris\ncurses as ncurses;

class test extends ncurses\app {
    protected static $logging = '/tmp/test.log';

	protected $menu;

	protected function setup() {
		$this->menu = $this->addChild(new ncurses\container\menu(array(
		    array('label' => 'Network'),
		    array('label' => 'Administration')
		)));
	}

	protected function main() {
		$this->menu->show();
	}
}

test::getInstance()->run();
