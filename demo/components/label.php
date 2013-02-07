#!/usr/bin/env php
<?php

require_once(__DIR__ . '/../../libs/autoloader.class.php');

use \org\octris\ncurses as ncurses;

class test extends ncurses\app {
    protected static $logging = '/tmp/test.log';

    protected function setup() {
        $this->addChild(
            new ncurses\component\label(1, 1, 'Label #1')
        );
        $this->addChild(
            new ncurses\component\label(1, 2, 'Label #2')
        );
    }

    protected function main() {
        sleep(2);
    }
}

test::getInstance()->run();
