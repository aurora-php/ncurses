#!/usr/bin/env php
<?php

require_once(__DIR__ . '/../../libs/autoloader.php');

use \org\octris\ncurses as ncurses;

class test extends ncurses\app {
    protected static $logging = '/tmp/test.log';

    protected $menubar;

    protected function setup() {
        $this->menubar = $this->addChild(new ncurses\component\menubar());

        $this->menubar->addMenu('File', new ncurses\component\menu(array(array('label' => 'Open'), array('label' => 'Save'))));
        $this->menubar->addMenu('Help', new ncurses\component\menu(array(array('label' => 'About'))));
    }
}

test::getInstance()->run();
