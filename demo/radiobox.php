#!/usr/bin/env php
<?php

define('NCURSES_LOG', '/tmp/test.log');

require_once('org.octris.core/app/autoloader.class.php');

use \org\octris\ncurses as ncurses;

$app = ncurses\app::getInstance();
$win = $app->addChild(new ncurses\component\window(
	13, 5, 5, 5
));

$menu = $win->addChild(
    new ncurses\component\radiobox(array(
        array('label' => 'blue',  'selected' => true),
        array('label' => 'green', 'selected' => false),
        array('label' => 'black', 'selected' => true)
    ))
);

$app->build();
$app->refresh();

$menu->run();
