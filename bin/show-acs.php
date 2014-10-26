#!/usr/bin/env php
<?php

// (c) 2013-2014 by Harald Lapp <harald@octris.org>

/*
 * convenient command to show output of ACS constants
 */

ncurses_init();

register_shutdown_function (function () {
	$err = error_get_last();

    ncurses_end();

    print_r($err);
});

$def = array(); $max = 0;
foreach (get_defined_constants() as $k => $v) {
	if (substr($k, 0, 12) != 'NCURSES_ACS_') continue;

	$def[$k] = $v;
	$max     = max($max, strlen($k));
}

$y = 0;
array_walk($def, function (&$v, $k) use (&$y, $max) {
	ncurses_mvaddstr($y++, 4, sprintf('%-' . $max . 's = ', $k));
	ncurses_hline($v, 1);
});

ncurses_mvaddstr($y + 2, 0, 'press CTRL+C to exit');
ncurses_refresh();

do {
	sleep(2);
} while (true);
