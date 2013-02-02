#!/usr/bin/env php
<?php

define('NCURSES_LOG', '/tmp/test.log');

require_once('org.octris.core/app/autoloader.class.php');

use \org\octris\ncurses as ncurses;

$app = ncurses\app::getInstance();

$button1 = $app->addChild(
    new ncurses\component\button( 1, 1, 'Button #1')
);
$button2 = $app->addChild(
    new ncurses\component\button(20, 1, 'Button #2')
);

$app->build();
$app->refresh();

$button1->focus();

sleep(2);

