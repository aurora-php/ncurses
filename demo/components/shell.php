#!/usr/bin/env php
<?php

require_once(__DIR__ . '/../../libs/autoloader.php');

use \octris\ncurses as ncurses;

class test extends ncurses\app {
    protected $win;

    protected function setup() {
        $this->win = $this->addChild(new ncurses\component\shell(10, 5, '$ '));
    }

    protected function main() {
        $this->win->show();
    }
}

test::enableLog('/tmp/test.log');
test::getInstance()->run();
