#!/usr/bin/env php
<?php

define('NCURSES_LOG', '/tmp/test.log');

require_once('org.octris.core/app/autoloader.class.php');

use \org\octris\ncurses as ncurses;

$app = ncurses\app::getInstance();
$win = $app->addChild(new ncurses\component\window(
	14, 4, 5, 5
));

$menu = $win->addChild(
    new ncurses\component\checkbox(array(
        array('label' => 'Apple', 'selected' => true),
        array('label' => 'Orange', 'selected' => false)
    ))
);

$app->build();
$app->refresh();

$menu->run();
