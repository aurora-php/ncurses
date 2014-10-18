#!/usr/bin/env php
<?php

define('NCURSES_LOG', '/tmp/test.log');

require_once(__DIR__ . '/../../libs/autoloader.php');

use \org\octris\ncurses as ncurses;

$app = ncurses\app::getInstance();

$win1 = $app->addChild(new ncurses\component\window(
    14, 4, 5, 5
));
$win2 = $app->addChild(new ncurses\component\window(
    14, 4, 5, 5
));
$win3 = $app->addChild(new ncurses\component\window(
    14, 4, 5, 5
));

$app->build();
$app->refresh();


