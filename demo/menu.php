#!/usr/bin/env php
<?php

define('NCURSES_LOG', '/tmp/test.log');

require_once('org.octris.core/app/autoloader.class.php');

use \org\octris\ncurses as ncurses;

$app = ncurses\app::getInstance();

$menu = $app->addChild(
	new ncurses\component\menu(array(
		array('label' => 'Network'),
		array('label' => 'Administration')
	))
);

$app->build();
$app->refresh();

$menu->run();
