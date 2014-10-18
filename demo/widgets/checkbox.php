#!/usr/bin/env php
<?php

require_once(__DIR__ . '/../../libs/autoloader.php');

use \org\octris\ncurses as ncurses;

class win extends ncurses\container\window {
    protected $checkbox;

    protected function setup() {
        $this->checkbox = $this->addChild(
            new ncurses\widget\checkbox(0, 0, array(
                array('label' => 'Apple',  'selected' => true),
                array('label' => 'Orange', 'selected' => false),
                array('label' => 'Banana', 'selected' => false)
            ))
        );
    }
}

class test extends ncurses\app {
    protected static $logging = '/tmp/test.log';

    protected $win;

    protected function setup() {
        $this->win = $this->addChild(new win(14, 5, 5, 5));

    }

    protected function main() {
        $this->win->show();
    }
}

test::getInstance()->run();
