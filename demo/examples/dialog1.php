#!/usr/bin/env php
<?php

define('NCURSES_LOG', '/tmp/test.log');

require_once(__DIR__ . '/../../libs/autoloader.class.php');

use \org\octris\ncurses as ncurses;

class win extends ncurses\container\window {
	protected $title = 'Network Settings';

	protected $checkbox;

	protected function setup() {
		$this->addChild(new ncurses\component\label(2, 1, 'Hostname'));
		$this->addChild(new ncurses\component\textline(15, 1, 20));
		$this->addChild(new ncurses\component\label(2, 2, 'Domain Name'));
		$this->addChild(new ncurses\component\textline(15, 2, 20));

		$this->addChild(new ncurses\component\label(2, 4, 'Netmask'));
		$this->addChild(new ncurses\component\textline(15, 4, 20));
		$this->addChild(new ncurses\component\label(2, 5, 'Gateway'));
		$this->addChild(new ncurses\component\textline(15, 5, 20));
		$this->addChild(new ncurses\component\label(2, 6, 'Primary DNS'));
		$this->addChild(new ncurses\component\textline(15, 6, 20));

		$this->addChild(new ncurses\component\checkbox(2, 8, array(array('label' => 'Use HTTP Proxy',  'selected' => false))));
		$this->addChild(new ncurses\component\label(2, 9, 'Proxy URL'));
		$this->addChild(new ncurses\component\textline(15, 9, 20));

        $this->addChild(new ncurses\component\button( 2, 12, 'Save'))->addEvent('action', function() {
            $this->doExit('Save');
        });;
        $this->addChild(new ncurses\component\button(10, 12, 'Cancel'))->addEvent('action', function() {
            $this->doExit('Cancel');
        });;
	}
}

class test extends ncurses\app {
	protected $win;

	protected function setup() {
		$this->win = $this->addChild(new win(40, 16));

	}

	protected function main() {
		$this->win->show();
	}
}

test::getInstance()->run();
