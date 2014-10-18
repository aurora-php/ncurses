#!/usr/bin/env php
<?php

require_once(__DIR__ . '/../../libs/autoloader.php');

use \org\octris\ncurses as ncurses;

class test extends ncurses\app {
    protected static $logging = '/tmp/test.log';

    protected $pane;

    protected function setup() {
        $this->pane = $this->addChild(new ncurses\container\scrollpane(5, 5, 20, 20, 1000));
    }

    protected function main() {
        for ($i = 1; $i < 30; ++$i) {
            $this->pane->addRow(sprintf("Row #%d", $i));
            sleep(1);
        }

        sleep(2);
    }
}

test::getInstance()->run();
