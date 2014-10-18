#!/usr/bin/env php
<?php

require_once(__DIR__ . '/../../libs/autoloader.php');

use \octris\ncurses as ncurses;

class test extends ncurses\app {
    protected static $logging = '/tmp/test.log';

    protected $menu;

    protected function setup() {
        $this->menu = $this->addChild(new ncurses\component\menu(array(
            array('label' => 'Network'),
            array('label' => 'Administration')
        )));
    }

    protected function main() {
        $this->menu->show();
    }
}

test::getInstance()->run();
