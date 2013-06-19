#!/usr/bin/env php
<?php

require_once(__DIR__ . '/../../libs/autoloader.class.php');

use \org\octris\ncurses as ncurses;

class test extends ncurses\app {
    protected static $logging = '/tmp/test.log';

	protected $menubar;

	protected function setup() {
		$this->menubar = $this->addChild(new ncurses\component\menubar());

		$this->menubar->addMenu('File'); //, new ncurses\component\submenu());
		$this->menubar->addMenu('Help'); //, new ncurses\component\submenu());
	}
}

test::getInstance()->run();
