#!/usr/bin/env php
<?php

require_once(__DIR__ . '/../../libs/autoloader.class.php');

use \org\octris\ncurses as ncurses;

class win extends ncurses\container\window {
	protected function setup() {
		$this->addChild(
		    new ncurses\widget\text(
		    	'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris vel lacus nunc. Aliquam quam erat, semper ac pellentesque quis, aliquam a est. Integer rutrum malesuada libero vel aliquam. Praesent pharetra vestibulum fringilla. Vestibulum vitae ligula erat. Nunc scelerisque sapien eget dolor facilisis vitae imperdiet nunc tristique. In risus elit, vulputate id pulvinar et, sagittis at dui. Quisque nec risus sapien, eget pellentesque orci. Mauris porttitor semper lacus, ac egestas magna semper sit amet.',
		    	ncurses\widget\text::T_ALIGN_LEFT,
		    	1, 2
		    )
		);
	}

    protected function run() {
    }
}

class test extends ncurses\app {
    protected static $logging = '/tmp/test.log';

	protected $win;

	protected function setup() {
		$this->win = $this->addChild(new win(75, 20, 5, 5));
	}
}

test::getInstance()->run();
