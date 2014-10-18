#!/usr/bin/env php
<?php

require_once(__DIR__ . '/../../libs/autoloader.php');

use \octris\ncurses as ncurses;

class win extends ncurses\container\window {
    protected $radiobox;

    protected function setup() {
        $this->radiobox = $this->addChild(
            new ncurses\widget\radiobox(0, 0, array(
                array('label' => 'blue',  'selected' => true),
                array('label' => 'green', 'selected' => false),
                array('label' => 'black', 'selected' => true)
            ))
        );
    }
}

class test extends ncurses\app {
    protected static $logging = '/tmp/test.log';

    protected $win;

    protected function setup() {
        $this->win = $this->addChild(new win(13, 5, 5, 5));

    }

    protected function main() {
        $this->win->show();
    }
}

test::getInstance()->run();

