#!/usr/bin/env php
<?php

define('NCURSES_LOG', '/tmp/test.log');

require_once(__DIR__ . '/../libs/autoloader.class.php');

use \org\octris\ncurses as ncurses;

class test extends ncurses\app {
    protected $button;

    protected function setup() {
        $this->button = $this->addChild(
            new ncurses\component\button( 1, 1, 'Button #1')
        );
        $this->addChild(
            new ncurses\component\button(20, 1, 'Button #2')
        );
    }

    protected function main() {
        $this->button->focus();

        sleep(2);
    }
}

test::getInstance()->run();
