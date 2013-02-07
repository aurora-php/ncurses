#!/usr/bin/env php
<?php

// (c) 2013 by Harald Lapp <harald@octris.org>

/* 
 * convenient command to list all available ncurses constants
 * - not all constants are defined, if ncurses is not initialized.
 * - get_defined_constants(true) does not seem to work here, ACS 
 *   constants aren't included in that case.
 */
ncurses_init(); 

$def = array(); $max = 0;
foreach (get_defined_constants() as $k => $v) {
	if (substr($k, 0, 8) != 'NCURSES_') continue;

	$def[$k] = $v;
	$max     = max($max, strlen($k));
}

ncurses_end(); 

array_walk($def, function(&$v, $k) use ($max) {
	printf("    %-" . $max . "s = %s\n", $k, $v);
});
