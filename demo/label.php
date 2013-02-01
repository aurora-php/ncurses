#!/usr/bin/env php
<?php

define('NCURSES_LOG', '/tmp/test.log');

require_once('org.octris.core/app/autoloader.class.php');

use \org\octris\ncurses as ncurses;

$app = ncurses\app::getInstance();

$app->addChild(
	new ncurses\component\label(1, 1, 'Label #1')
);
$app->addChild(
	new ncurses\component\label(1, 2, 'Label #2')
);


$app->build();
$app->refresh();

sleep(2);

